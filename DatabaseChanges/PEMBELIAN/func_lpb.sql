CREATE OR REPLACE FUNCTION sc_trx.fn_sync_lpb_dtl_to_transaction(
    p_docno TEXT,
    p_uniqueid TEXT
)
RETURNS VOID
LANGUAGE plpgsql
AS $$
DECLARE
    v_lpb          sc_trx.lpb%ROWTYPE;
    v_dtl          sc_trx.lpb_dtl%ROWTYPE;
    v_journal      CHAR(6);
    v_type         CHAR(3);
    v_tx_uid       TEXT;
    v_source_uid   TEXT;
    v_exists       BOOLEAN;
    v_idbranch     CHAR(20);

    v_dpp          NUMERIC(18,2);
    v_pajak        NUMERIC(18,2);
    v_nilai        NUMERIC(18,2);
    v_bruto        NUMERIC(18,2);
BEGIN
    -- 1. Ambil header
    SELECT * INTO v_lpb
    FROM sc_trx.lpb
    WHERE rtrim(docno) = rtrim(p_docno);

    IF NOT FOUND THEN
        RETURN;
    END IF;

    -- 2. Ambil detail
    SELECT * INTO v_dtl
    FROM sc_trx.lpb_dtl
    WHERE rtrim(docno) = rtrim(p_docno)
      AND uniqueid = p_uniqueid;

    IF NOT FOUND THEN
        RETURN;
    END IF;

    -- 3. Tentukan journal_type dari doctype
    v_journal := CASE rtrim(COALESCE(v_dtl.doctype, v_lpb.doctype))
        WHEN 'GR'    THEN 'GRNREC'::CHAR(6)
        WHEN 'GRRET' THEN 'GRNRET'::CHAR(6)
        ELSE 'GRNREC'::CHAR(6)
    END;

    v_type := CASE v_journal
        WHEN 'GRNREC' THEN 'IN'::CHAR(3)
        WHEN 'GRNRET' THEN 'OUT'::CHAR(3)
        ELSE 'IN'::CHAR(3)
    END;

    -- 4. idbranch
    v_idbranch := CASE rtrim(v_lpb.cabang)
        WHEN 'JTS1' THEN 'JTS'::CHAR(20)
        WHEN 'JTS2' THEN 'JTS'::CHAR(20)
        ELSE rtrim(v_lpb.cabang)::CHAR(20)
    END;

    -- 5. Hitung nilai
    v_dpp   := COALESCE(v_dtl.nilaikonversi, 0);
    v_pajak := COALESCE(v_dtl.nilaipajak, 0);
    v_nilai := v_dpp + v_pajak;
    v_bruto := v_nilai;

    -- 6. Generate uniqueid transaksi
    v_tx_uid := md5(rtrim(p_docno) || '|' || COALESCE(v_dtl.uniqueid, ''));

    v_source_uid := COALESCE(v_dtl.uniqueid, '');

    -- 7. Cek apakah sudah ada
    SELECT EXISTS (
        SELECT 1 FROM sc_trx.transaction_dt
        WHERE rtrim(docno) = rtrim(p_docno)
          AND uniqueid = v_tx_uid
    ) INTO v_exists;

    -- 8. Insert atau update
    IF v_exists THEN
        UPDATE sc_trx.transaction_dt
        SET
            qty             = v_dtl.qty,
            harga           = v_dtl.harga,
            bruto           = v_bruto,
            discount        = COALESCE(v_dtl.multidisc, 0),
            nilai           = v_nilai,
            dpp             = v_dpp,
            pajak           = v_pajak,
            total           = v_nilai,
            idtax           = rtrim(v_dtl.idtax),
            isinclusive     = rtrim(v_lpb.isinclusive),
            currcode        = rtrim(v_lpb.currcode),
            kurs            = v_lpb.kurs,
            idbarang        = rtrim(v_dtl.idbarang),
            namabarang      = rtrim(v_dtl.nmbarang),
            idunit          = rtrim(v_dtl.unit),
            warehouse       = rtrim(v_dtl.idgudang),
            idarea          = rtrim(v_dtl.idgudang),
            kdsupplier      = rtrim(v_lpb.kdsupplier),
            nsupplier       = rtrim(v_lpb.nmsupplier),
            updatedby       = v_lpb.updateby,
            updateddate     = CURRENT_TIMESTAMP
        WHERE rtrim(docno) = rtrim(p_docno)
          AND uniqueid = v_tx_uid;
    ELSE
        INSERT INTO sc_trx.transaction_dt (
            uniqueid, source_uniqueid, docno, doctype, journal_type,
            line_no, docdate, idbranch, cabang, type_in_out,
            ref_docno, ref_doctype,
            kdsupplier, nsupplier,
            idbarang, namabarang, idunit, idarea, warehouse,
            bin, batch, lotno,
            qty, harga, bruto, discount, nilai, dpp, pajak, total,
            idtax, isinclusive, currcode, kurs, createdby
        )
        VALUES (
            v_tx_uid,
            v_source_uid,
            rtrim(p_docno),
            rtrim(COALESCE(v_dtl.doctype, v_lpb.doctype)),
            v_journal,
            v_dtl.idurut,
            v_lpb.docdate,
            v_idbranch,
            rtrim(v_lpb.cabang),
            v_type,
            rtrim(v_dtl.docnopo),
            'PO',
            rtrim(v_lpb.kdsupplier),
            rtrim(v_lpb.nmsupplier),
            rtrim(v_dtl.idbarang),
            rtrim(v_dtl.nmbarang),
            rtrim(v_dtl.unit),
            rtrim(v_dtl.idgudang),
            rtrim(v_dtl.idgudang),
            '',                              -- bin
            rtrim(v_dtl.idspec),                              -- batch
            '',                              -- lotno
            v_dtl.qty,
            v_dtl.harga,
            v_bruto,
            COALESCE(v_dtl.multidisc, 0),
            v_nilai,
            v_dpp,
            v_pajak,
            v_nilai,
            rtrim(v_dtl.idtax),
            rtrim(v_lpb.isinclusive),
            rtrim(v_lpb.currcode),
            v_lpb.kurs,
            rtrim(v_lpb.inputby)
        );
    END IF;
END;
$$;





CREATE OR REPLACE FUNCTION sc_trx.fn_lpb_dtl_to_transaction()
RETURNS TRIGGER
LANGUAGE plpgsql
AS $$
BEGIN
    IF TG_OP IN ('INSERT') THEN
        PERFORM sc_trx.fn_sync_lpb_dtl_to_transaction(NEW.docno, NEW.uniqueid);
        RETURN NEW;
    END IF;
END;
$$;

CREATE TRIGGER tr_lpb_dtl_to_transaction
AFTER INSERT
ON sc_trx.lpb_dtl
FOR EACH ROW
EXECUTE FUNCTION sc_trx.fn_lpb_dtl_to_transaction();