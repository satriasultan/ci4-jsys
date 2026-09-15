
-- JALANKAN INI DULU

DROP TABLE IF EXISTS sc_tmp.ndk;
DROP TABLE IF EXISTS sc_trx.ndk;




CREATE TABLE IF NOT EXISTS sc_tmp.ndk
(
    idurut serial NOT NULL,
    docno character(30) COLLATE pg_catalog."default" NOT NULL,
    docdate date,
    -- senddate character(20) COLLATE pg_catalog."default",
    cabang character (30 ) COLLATE pg_catalog."default",    
    pemohon character(100) COLLATE pg_catalog."default",
    kdsupplier character(30) COLLATE pg_catalog."default",
    nmsupplier character(250) COLLATE pg_catalog."default",
    alamatsupplier TEXT,
    -- alamatkirim TEXT,
    jthtempo numeric(18,2),
    dk character(20),
    kdsalesman character(10),
    idtax character(20),
    isinclusive character(6),
    currcode character(3),
    kurs numeric(18,2),
    perkiraanarap character(20),
    perkiraanlawan character(20),
    
    nilai numeric(18,2),
    dpp numeric(18,2),
    jumlahpajak numeric(18,2),
    total numeric(18,2),
    -- syarat TEXT,
    status character(6) COLLATE pg_catalog."default",
    keterangan TEXT,
    inputby character varying(50) COLLATE pg_catalog."default",
    inputdate timestamp without time zone,
    updateby character varying(50) COLLATE pg_catalog."default",
    updatedate timestamp without time zone,
    printby character varying(50) COLLATE pg_catalog."default",
    printdate timestamp without time zone,
    docnotmp character(30) COLLATE pg_catalog."default",
    CONSTRAINT pk_tmp_ndk PRIMARY KEY (docno)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_tmp.ndk
    OWNER to postgres;





CREATE TABLE IF NOT EXISTS sc_trx.ndk
(
    idurut serial NOT NULL,
    docno character(30) COLLATE pg_catalog."default" NOT NULL,
    docdate date,
    -- senddate character(20) COLLATE pg_catalog."default",
    cabang character (30 ) COLLATE pg_catalog."default",    
    pemohon character(100) COLLATE pg_catalog."default",
    kdsupplier character(30) COLLATE pg_catalog."default",
    nmsupplier character(250) COLLATE pg_catalog."default",
    alamatsupplier TEXT,
    -- alamatkirim TEXT,
    jthtempo numeric(18,2),
    dk character(20),
    kdsalesman character(10),
    idtax character(20),
    isinclusive character(6),
    currcode character(3),
    kurs numeric(18,2),
    perkiraanarap character(20),
    perkiraanlawan character(20),
    
    nilai numeric(18,2),
    dpp numeric(18,2),
    jumlahpajak numeric(18,2),
    total numeric(18,2),
    -- syarat TEXT,
    status character(6) COLLATE pg_catalog."default",
    keterangan TEXT,
    inputby character varying(50) COLLATE pg_catalog."default",
    inputdate timestamp without time zone,
    updateby character varying(50) COLLATE pg_catalog."default",
    updatedate timestamp without time zone,
    printby character varying(50) COLLATE pg_catalog."default",
    printdate timestamp without time zone,
    docnotmp character(30) COLLATE pg_catalog."default",
    CONSTRAINT pk_trx_ndk PRIMARY KEY (docno)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_trx.ndk
    OWNER to postgres;






-- FUNCTION: sc_tmp.tr_ndk_finalize()

-- DROP FUNCTION IF EXISTS sc_tmp.tr_ndk_finalize();
CREATE OR REPLACE FUNCTION sc_tmp.tr_ndk_finalize()
RETURNS trigger
LANGUAGE plpgsql
AS $BODY$
DECLARE
    v_docno       TEXT;
    v_inputby     TEXT;
    v_idurut      INTEGER;
    v_base_docno  TEXT;
    v_new_docno   TEXT;
    v_num         TEXT;
    v_num_int     INTEGER;
    v_lock_key    BIGINT;
BEGIN

    /* ============================================================
       NORMAL FINALIZE
       E -> F
       ============================================================ */

    IF OLD.status = 'E'
       AND NEW.status = 'F'
       AND COALESCE(NEW.docnotmp, '') = '' THEN

        /* --------------------------------------------------------
           NORMALISASI
           -------------------------------------------------------- */

        v_docno   := RTRIM(NEW.docno);
        v_inputby := NEW.inputby;
        v_idurut  := NEW.idurut;


        /* --------------------------------------------------------
           CEK APAKAH TRANSAKSI INI SUDAH PERNAH DIFINALKAN

           Menggunakan idurut + inputby.
           Jika sudah ada di trx, jangan membuat NDK kedua.
           -------------------------------------------------------- */

        IF EXISTS (
            SELECT 1
            FROM sc_trx.ndk
            WHERE idurut = v_idurut
              AND TRIM(COALESCE(inputby, '')) =
                  TRIM(COALESCE(v_inputby, ''))
        ) THEN

            /* TMP tetap dibersihkan */
            DELETE FROM sc_tmp.ndk
            WHERE TRIM(docno) = TRIM(OLD.docno)
              AND idurut = v_idurut;

            RETURN NEW;
        END IF;


        /* --------------------------------------------------------
           AMBIL BASE DOCNO

           Contoh:
           05M/2601/PA0001 -> 05M/2601/PA
           PPB/2601/PT0025 -> PPB/2601/PT
           -------------------------------------------------------- */

        v_base_docno := regexp_replace(v_docno, '[0-9]+$', '');


        /* --------------------------------------------------------
           ADVISORY LOCK

           Mencegah dua finalize bersamaan mendapatkan nomor sama.
           -------------------------------------------------------- */

        v_lock_key := hashtext(v_base_docno);

        PERFORM pg_advisory_xact_lock(v_lock_key);


        /* --------------------------------------------------------
           GENERATE DOCNO
           -------------------------------------------------------- */

        v_new_docno := v_docno;

        LOOP

            EXIT WHEN NOT EXISTS (
                SELECT 1
                FROM sc_trx.ndk
                WHERE TRIM(docno) = TRIM(v_new_docno)
            );


            v_num := regexp_replace(
                v_new_docno,
                '.*?([0-9]+)$',
                '\1'
            );


            IF COALESCE(v_num, '') = '' THEN
                RAISE EXCEPTION
                    'Format DOCNO NDK tidak valid: %',
                    v_new_docno;
            END IF;


            v_num_int := v_num::INTEGER + 1;


            v_new_docno :=
                v_base_docno ||
                lpad(
                    v_num_int::TEXT,
                    length(v_num),
                    '0'
                );

        END LOOP;


        /* --------------------------------------------------------
           DOCNO FINAL
           -------------------------------------------------------- */

        v_docno := v_new_docno;


        /* --------------------------------------------------------
           INSERT HEADER KE TRANSAKSI
           -------------------------------------------------------- */

        INSERT INTO sc_trx.ndk
        (
            idurut,
            docno,
            cabang,
            docdate,
            pemohon,
            kdsupplier,
            nmsupplier,
            alamatsupplier,
            kdsalesman,
            jthtempo,
            isinclusive,
            dk,
            perkiraanarap,
            perkiraanlawan,
            nilai,
            idtax,
            currcode,
            kurs,
            dpp,
            jumlahpajak,
            total,
            keterangan,
            status,
            inputby,
            inputdate,
            updateby,
            updatedate,
            printby,
            printdate
        )
        SELECT
            idurut,
            v_docno,
            cabang,
            docdate,
            pemohon,
            kdsupplier,
            nmsupplier,
            alamatsupplier,
            kdsalesman,
            jthtempo,
            isinclusive,
            dk,
            perkiraanarap,
            perkiraanlawan,
            nilai,
            idtax,
            currcode,
            kurs,
            dpp,
            jumlahpajak,
            total,
            keterangan,
            'F',
            inputby,
            inputdate,
            updateby,
            updatedate,
            printby,
            printdate
        FROM sc_tmp.ndk
        WHERE TRIM(docno) = TRIM(OLD.docno)
          AND TRIM(COALESCE(inputby, '')) =
              TRIM(COALESCE(v_inputby, ''))
          AND idurut = v_idurut;


        /* --------------------------------------------------------
           CLEANUP TMP
           -------------------------------------------------------- */

        DELETE FROM sc_tmp.ndk
        WHERE TRIM(docno) = TRIM(OLD.docno)
          AND TRIM(COALESCE(inputby, '')) =
              TRIM(COALESCE(v_inputby, ''))
          AND idurut = v_idurut;


    /* ============================================================
       DOCNOTMP FLOW
       ============================================================ */

    ELSIF OLD.status = 'E'
       AND NEW.status = 'F'
       AND COALESCE(NEW.docnotmp, '') <> '' THEN

        /* Hapus transaksi lama */
        DELETE FROM sc_trx.ndk
        WHERE TRIM(docno) = TRIM(NEW.docnotmp);


        /* Insert transaksi baru */
        INSERT INTO sc_trx.ndk
        (
            idurut,
            docno,
            cabang,
            docdate,
            pemohon,
            kdsupplier,
            nmsupplier,
            alamatsupplier,
            kdsalesman,
            jthtempo,
            isinclusive,
            dk,
            perkiraanarap,
            perkiraanlawan,
            nilai,
            idtax,
            currcode,
            kurs,
            dpp,
            jumlahpajak,
            total,
            keterangan,
            status,
            inputby,
            inputdate,
            updateby,
            updatedate,
            printby,
            printdate,
            docnotmp
        )
        SELECT
            idurut,
            NEW.docnotmp,
            cabang,
            docdate,
            pemohon,
            kdsupplier,
            nmsupplier,
            alamatsupplier,
            kdsalesman,
            jthtempo,
            isinclusive,
            dk,
            perkiraanarap,
            perkiraanlawan,
            nilai,
            idtax,
            currcode,
            kurs,
            dpp,
            jumlahpajak,
            total,
            keterangan,
            'F',
            inputby,
            inputdate,
            updateby,
            updatedate,
            printby,
            printdate,
            docnotmp
        FROM sc_tmp.ndk
        WHERE TRIM(docno) = TRIM(NEW.docno);


        /* Cleanup */
        DELETE FROM sc_tmp.ndk
        WHERE TRIM(docno) = TRIM(NEW.docno);


    /* ============================================================
       CANCEL
       ============================================================ */

    ELSIF OLD.status = 'E'
       AND NEW.status = 'C' THEN

        IF NEW.printby IS NOT NULL
           AND NEW.printby <> ''
           AND NEW.printdate IS NOT NULL THEN

            UPDATE sc_trx.ndk
            SET status = 'P'
            WHERE TRIM(docno) = TRIM(NEW.docnotmp);

        ELSE

            UPDATE sc_trx.ndk
            SET status = 'F'
            WHERE TRIM(docno) = TRIM(NEW.docnotmp);

        END IF;


        DELETE FROM sc_tmp.ndk
        WHERE TRIM(docno) = TRIM(NEW.docno);

    END IF;


    RETURN NEW;

END;
$BODY$;

DROP TRIGGER IF EXISTS tr_ndk_finalize
ON sc_tmp.ndk;

CREATE OR REPLACE TRIGGER tr_ndk_finalize
    AFTER UPDATE ON sc_tmp.ndk
    FOR EACH ROW
    EXECUTE FUNCTION sc_tmp.tr_ndk_finalize();







-- DROP FUNCTION IF EXISTS sc_trx.tr_ndk();

CREATE OR REPLACE FUNCTION sc_trx.tr_ndk()
RETURNS trigger
LANGUAGE plpgsql
AS $BODY$
DECLARE
    v_docno VARCHAR(50);
    v_user VARCHAR(50);
BEGIN

    /* ============================================================
       INSERT
       ============================================================ */

    IF TG_OP = 'INSERT' THEN

        v_docno := TRIM(NEW.docno);

        v_user := COALESCE(
            NULLIF(TRIM(NEW.updateby), ''),
            NULLIF(TRIM(NEW.inputby), ''),
            CURRENT_USER
        );

        PERFORM sc_trx.sp_sync_ndk_journal(
            v_docno,
            v_user
        );

        RETURN NEW;

    END IF;


    /* ============================================================
       UPDATE
       ============================================================ */

    IF TG_OP = 'UPDATE' THEN

        v_docno := TRIM(NEW.docno);

        v_user := COALESCE(
            NULLIF(TRIM(NEW.updateby), ''),
            NULLIF(TRIM(NEW.inputby), ''),
            CURRENT_USER
        );


        /* ========================================================
           F → E
           COPY KE TEMPORARY
           ======================================================== */

        IF TRIM(COALESCE(OLD.status, '')) = 'F'
           AND TRIM(COALESCE(NEW.status, '')) = 'E' THEN

            INSERT INTO sc_tmp.ndk
            (
                idurut, docno, cabang, docdate, pemohon, kdsupplier,
                nmsupplier, alamatsupplier, kdsalesman, jthtempo,
                isinclusive, dk, perkiraanarap, perkiraanlawan, nilai,
                idtax, currcode, kurs, dpp,
                jumlahpajak, total,
                keterangan, status, inputby, inputdate, updateby, updatedate,
                printby, printdate, docnotmp
            )
            SELECT
                idurut, NEW.docno, cabang, docdate, pemohon, kdsupplier,
                nmsupplier, alamatsupplier, kdsalesman, jthtempo,
                isinclusive, dk, perkiraanarap, perkiraanlawan, nilai,
                idtax, currcode, kurs, dpp,
                jumlahpajak, total,
                keterangan, status, inputby, inputdate, updateby, updatedate,
                printby, printdate, NEW.docno
            FROM sc_trx.ndk
            WHERE TRIM(docno) = TRIM(NEW.docno);

        END IF;


        /* ========================================================
           SEMUA UPDATE DISINKRONKAN KE JURNAL
           ======================================================== */

        PERFORM sc_trx.sp_sync_ndk_journal(
            v_docno,
            v_user
        );

        RETURN NEW;

    END IF;


    /* ============================================================
       DELETE
       ============================================================ */

    IF TG_OP = 'DELETE' THEN

        v_docno := TRIM(OLD.docno);

        v_user := COALESCE(
            NULLIF(TRIM(OLD.updateby), ''),
            NULLIF(TRIM(OLD.inputby), ''),
            CURRENT_USER
        );

        PERFORM sc_trx.sp_sync_ndk_journal(
            v_docno,
            v_user
        );

        RETURN OLD;

    END IF;


    RETURN NULL;

END;
$BODY$;

ALTER FUNCTION sc_trx.tr_ndk()
OWNER TO postgres;

DROP TRIGGER IF EXISTS tr_ndk
ON sc_trx.ndk;

CREATE or REPLACE TRIGGER  tr_ndk
AFTER INSERT OR UPDATE OR DELETE
ON sc_trx.ndk
FOR EACH ROW
EXECUTE FUNCTION sc_trx.tr_ndk();





/* REPOSTING NDK */

/* ============================================================
   REPOSTING NDK
   DK = K : Supplier
           PPN Masukan  -> tax_dtl.prk_masukan

   DK = D : Customer
           PPN Keluaran -> tax_dtl.prk_keluaran
   ============================================================ */

CREATE OR REPLACE FUNCTION sc_trx.sp_sync_ndk_journal(
    p_docno VARCHAR,
    p_user VARCHAR DEFAULT CURRENT_USER
)
RETURNS VOID
LANGUAGE plpgsql
AS $BODY$
DECLARE
    v_ndk RECORD;
    v_tax RECORD;
    v_jurnal_id BIGINT;
    v_total_debet NUMERIC(18,2) := 0;
    v_total_kredit NUMERIC(18,2) := 0;
    v_nilai_netto NUMERIC(18,2) := 0;
BEGIN

    /* ============================================================
       VALIDASI DOCNO
       ============================================================ */

    IF COALESCE(TRIM(p_docno), '') = '' THEN
        RETURN;
    END IF;


    /* ============================================================
       1. HAPUS JURNAL LAMA

       Digunakan untuk:
       - UPDATE nilai
       - UPDATE DPP
       - UPDATE PPN
       - UPDATE idtax
       - UPDATE perkiraan
       - F -> E
       - F -> C
       - DELETE NDK
       ============================================================ */

    DELETE FROM sc_trx.jurnal_dt
    WHERE TRIM(ref_docno) = TRIM(p_docno)
      AND TRIM(ref_doctype) = 'NDK';


    DELETE FROM sc_trx.jurnal_hd
    WHERE TRIM(docno) = TRIM(p_docno)
      AND TRIM(doctype) = 'NDK';


    /* ============================================================
       2. AMBIL NDK TERBARU
       ============================================================ */

    SELECT TRIM(n.docno) AS docno,
           n.docdate AS docdate,
           TRIM(n.dk) AS dk,
           TRIM(n.idtax) AS idtax,
           TRIM(n.perkiraanarap) AS perkiraanarap,
           TRIM(n.perkiraanlawan) AS perkiraanlawan,
           COALESCE(n.nilai, 0) AS nilai,
           COALESCE(n.dpp, 0) AS dpp,
           COALESCE(n.jumlahpajak, 0) AS jumlahpajak,
           COALESCE(n.total, 0) AS total,
           TRIM(n.currcode) AS currcode,
           COALESCE(n.kurs, 1) AS kurs,
           TRIM(n.kdsupplier) AS kdsupplier,
           TRIM(n.nmsupplier) AS nmsupplier,
           n.keterangan,
           TRIM(n.status) AS status
    INTO v_ndk
    FROM sc_trx.ndk n
    WHERE TRIM(n.docno) = TRIM(p_docno)
    LIMIT 1;


    /* ============================================================
       3. KALAU NDK SUDAH TIDAK ADA

       Kemungkinan DELETE.
       Jurnal lama sudah dihapus pada STEP 1.
       ============================================================ */

    IF NOT FOUND THEN
        RETURN;
    END IF;


    /* ============================================================
       4. HANYA STATUS F YANG MASUK JURNAL

       E = Editing
       F = Final
       C = Cancel
       P = Printed / flow lainnya
       ============================================================ */

    IF COALESCE(TRIM(v_ndk.status), '') <> 'F' THEN
        RETURN;
    END IF;


    /* ============================================================
       5. VALIDASI PERKIRAAN
       ============================================================ */

    IF COALESCE(TRIM(v_ndk.perkiraanarap), '') = '' THEN
        RAISE EXCEPTION
            'Perkiraan AR/AP belum diisi untuk NDK %',
            v_ndk.docno;
    END IF;


    IF COALESCE(TRIM(v_ndk.perkiraanlawan), '') = '' THEN
        RAISE EXCEPTION
            'Perkiraan lawan belum diisi untuk NDK %',
            v_ndk.docno;
    END IF;


    IF COALESCE(v_ndk.total, 0) <= 0 THEN
        RAISE EXCEPTION
            'Total NDK % harus lebih besar dari 0',
            v_ndk.docno;
    END IF;


    /* ============================================================
       6. AMBIL SETTING TAX

       PPN:
       - prk_masukan  -> Supplier
       - prk_keluaran -> Customer

       Berdasarkan:
       - idtax
       - idgrouptax = PPN
       ============================================================ */

    SELECT TRIM(td.idtax) AS idtax,
           TRIM(td.idgrouptax) AS idgrouptax,
           TRIM(td.prk_masukan) AS prk_masukan,
           TRIM(td.prk_keluaran) AS prk_keluaran,
           COALESCE(td.percentation, 0) AS percentation
    INTO v_tax
    FROM sc_mst.tax_dtl td
    WHERE TRIM(td.idtax) = COALESCE(TRIM(v_ndk.idtax), '')
      AND TRIM(td.idgrouptax) = 'PPN'
      AND TRIM(COALESCE(td.status, '')) = 'P'
      AND TRIM(COALESCE(td.chold, 'NO')) = 'NO'
    ORDER BY td.id
    LIMIT 1;


    /* ============================================================
       7. HITUNG NILAI NETTO

       Contoh:

       TOTAL          3.300.000
       JUMLAHPAJAK      330.000
       -------------------------
       NILAI NETTO    3.000.000
       ============================================================ */

    v_nilai_netto :=
        ROUND(
            GREATEST(
                COALESCE(v_ndk.total, 0)
                - COALESCE(v_ndk.jumlahpajak, 0),
                0
            ),
            2
        );


    /* ============================================================
       8. VALIDASI TAX JIKA ADA PAJAK
       ============================================================ */

    IF COALESCE(v_ndk.jumlahpajak, 0) > 0 THEN

        IF v_tax.idtax IS NULL THEN
            RAISE EXCEPTION
                'Setting PPN untuk idtax % belum ditemukan pada NDK %',
                COALESCE(v_ndk.idtax, ''),
                v_ndk.docno;
        END IF;

    END IF;


    /* ============================================================
       9. BUAT HEADER JURNAL
       ============================================================ */

    INSERT INTO sc_trx.jurnal_hd
    (
        docno,
        doctype,
        trxdate,
        total_debet,
        total_kredit,
        status,
        createdby,
        createddate
    )
    VALUES
    (
        v_ndk.docno,
        'NDK',
        v_ndk.docdate,
        0,
        0,
        'P',
        COALESCE(NULLIF(TRIM(p_user), ''), CURRENT_USER),
        NOW()
    )
    RETURNING id INTO v_jurnal_id;


    /* ============================================================
       10. NOTA KREDIT / SUPPLIER

       DK = K

       TANPA PAJAK:
       Perkiraan Lawan     D
       AR/AP               K

       DENGAN PPN:
       Perkiraan Lawan     D = TOTAL - PPN
       PPN Masukan         D = PPN
       AR/AP               K = TOTAL

       Contoh:

       111311    D    3.000.000
       116106    D      330.000
       213102    K    3.300.000
       ============================================================ */

    IF UPPER(COALESCE(TRIM(v_ndk.dk), '')) = 'K' THEN

        /* --------------------------------------------------------
           10.1 PERKIRAAN LAWAN
           -------------------------------------------------------- */

        INSERT INTO sc_trx.jurnal_dt
        (
            jurnal_id,
            idcoa,
            debet,
            kredit,
            ref_docno,
            ref_doctype
        )
        VALUES
        (
            v_jurnal_id,
            v_ndk.perkiraanlawan,
            CASE
                WHEN COALESCE(v_ndk.jumlahpajak, 0) > 0
                THEN v_nilai_netto
                ELSE v_ndk.total
            END,
            0,
            v_ndk.docno,
            'NDK'
        );


        /* --------------------------------------------------------
           10.2 PPN MASUKAN

           Supplier menggunakan prk_masukan
           -------------------------------------------------------- */

        IF COALESCE(v_ndk.jumlahpajak, 0) > 0 THEN

            IF COALESCE(TRIM(v_tax.prk_masukan), '') = '' THEN
                RAISE EXCEPTION
                    'Perkiraan PPN Masukan belum disetting untuk tax % pada NDK %',
                    COALESCE(v_ndk.idtax, ''),
                    v_ndk.docno;
            END IF;


            INSERT INTO sc_trx.jurnal_dt
            (
                jurnal_id,
                idcoa,
                debet,
                kredit,
                ref_docno,
                ref_doctype
            )
            VALUES
            (
                v_jurnal_id,
                v_tax.prk_masukan,
                v_ndk.jumlahpajak,
                0,
                v_ndk.docno,
                'NDK'
            );

        END IF;


        /* --------------------------------------------------------
           10.3 AR/AP

           Supplier -> Hutang Dagang
           Nilainya TOTAL
           -------------------------------------------------------- */

        INSERT INTO sc_trx.jurnal_dt
        (
            jurnal_id,
            idcoa,
            debet,
            kredit,
            ref_docno,
            ref_doctype
        )
        VALUES
        (
            v_jurnal_id,
            v_ndk.perkiraanarap,
            0,
            v_ndk.total,
            v_ndk.docno,
            'NDK'
        );


    /* ============================================================
       11. NOTA DEBIT / CUSTOMER

       DK = D

       TANPA PAJAK:
       AR/AP               D
       Perkiraan Lawan     K

       DENGAN PPN:
       AR/AP               D = TOTAL
       PPN Keluaran        K = PPN
       Perkiraan Lawan     K = TOTAL - PPN

       Contoh:

       213102    D    3.300.000
       214116    K      330.000
       411xxx    K    3.000.000
       ============================================================ */

    ELSIF UPPER(COALESCE(TRIM(v_ndk.dk), '')) = 'D' THEN

        /* --------------------------------------------------------
           11.1 AR/AP

           Customer -> Piutang
           Nilainya TOTAL
           -------------------------------------------------------- */

        INSERT INTO sc_trx.jurnal_dt
        (
            jurnal_id,
            idcoa,
            debet,
            kredit,
            ref_docno,
            ref_doctype
        )
        VALUES
        (
            v_jurnal_id,
            v_ndk.perkiraanarap,
            v_ndk.total,
            0,
            v_ndk.docno,
            'NDK'
        );


        /* --------------------------------------------------------
           11.2 PPN KELUARAN

           Customer menggunakan prk_keluaran
           -------------------------------------------------------- */

        IF COALESCE(v_ndk.jumlahpajak, 0) > 0 THEN

            IF COALESCE(TRIM(v_tax.prk_keluaran), '') = '' THEN
                RAISE EXCEPTION
                    'Perkiraan PPN Keluaran belum disetting untuk tax % pada NDK %',
                    COALESCE(v_ndk.idtax, ''),
                    v_ndk.docno;
            END IF;


            INSERT INTO sc_trx.jurnal_dt
            (
                jurnal_id,
                idcoa,
                debet,
                kredit,
                ref_docno,
                ref_doctype
            )
            VALUES
            (
                v_jurnal_id,
                v_tax.prk_keluaran,
                0,
                v_ndk.jumlahpajak,
                v_ndk.docno,
                'NDK'
            );

        END IF;


        /* --------------------------------------------------------
           11.3 PERKIRAAN LAWAN

           Jika ada PPN:
           TOTAL - PPN

           Jika tidak ada PPN:
           TOTAL
           -------------------------------------------------------- */

        INSERT INTO sc_trx.jurnal_dt
        (
            jurnal_id,
            idcoa,
            debet,
            kredit,
            ref_docno,
            ref_doctype
        )
        VALUES
        (
            v_jurnal_id,
            v_ndk.perkiraanlawan,
            0,
            CASE
                WHEN COALESCE(v_ndk.jumlahpajak, 0) > 0
                THEN v_nilai_netto
                ELSE v_ndk.total
            END,
            v_ndk.docno,
            'NDK'
        );


    ELSE

        /* ========================================================
           DK HARUS D ATAU K
           ======================================================== */

        RAISE EXCEPTION
            'DK NDK % harus D atau K. Nilai saat ini: %',
            v_ndk.docno,
            v_ndk.dk;

    END IF;


    /* ============================================================
       12. VALIDASI BALANCE
       ============================================================ */

    SELECT COALESCE(SUM(debet), 0),
           COALESCE(SUM(kredit), 0)
    INTO v_total_debet, v_total_kredit
    FROM sc_trx.jurnal_dt
    WHERE jurnal_id = v_jurnal_id;


    IF ROUND(v_total_debet, 2) <> ROUND(v_total_kredit, 2) THEN

        RAISE EXCEPTION
            'JURNAL NDK TIDAK BALANCE. DOCNO: %, DEBET: %, KREDIT: %',
            v_ndk.docno,
            v_total_debet,
            v_total_kredit;

    END IF;


    /* ============================================================
       13. UPDATE TOTAL HEADER
       ============================================================ */

    UPDATE sc_trx.jurnal_hd
    SET total_debet = v_total_debet,
        total_kredit = v_total_kredit
    WHERE id = v_jurnal_id;


END;
$BODY$;