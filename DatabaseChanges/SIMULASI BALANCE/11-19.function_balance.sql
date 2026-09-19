/* ============================================================================
   JSYS ACCOUNTING / ERP
   TAHAP 11 - 19 : ACCOUNTING FLOW FINAL

   FOKUS:
   - Accounting journal only.
   - Stock posting tetap milik TAHAP 06.
   - Average cost tetap milik TAHAP 07.
   - Asset posting tetap milik TAHAP 08.
   - Journal detail tetap milik TAHAP 10.

   TAHAP 11 - 14 DITIADAKAN DARI SCRIPT INI KARENA SUDAH DITANGANI:
       transaction_dt -> TAHAP 06 -> stkblc -> TAHAP 07
       transaction_dt -> TAHAP 08 -> assetblc

   YANG DIPERTAHANKAN:
       TAHAP 15  reverse accounting
       TAHAP 16  post/generate accounting
       TAHAP 17  recalculate transaction_hd
       TAHAP 18  trigger accounting + summary

   TAHAP 19 validation source/type/coa TIDAK dibuat ulang karena:
       - validation transaction sudah ada di TAHAP 04
       - validation COA jurnal sudah ada di TAHAP 10

   ATURAN PENTING:
   - Tidak DROP table.
   - Tidak mengubah transaksi legacy.
   - Accounting membaca transaction_dt sebagai source of truth.
   - COA accounting berasal dari sc_mst.journal_type_coa.
   - COA role dapat resolve ke mbarang/currency/konfigurasi_umum.
   - Tax COA berasal dari sc_mst.tax_dtl.
   - COST mengambil biaya dari stkblc hasil TAHAP 07.
   - Jurnal harus balance sebelum status POSTED.
   ============================================================================ */


/* ============================================================================
   0. DEPENDENCY
   ============================================================================ */
DO $$
BEGIN
    IF to_regclass('sc_trx.transaction_dt') IS NULL THEN
        RAISE EXCEPTION 'sc_trx.transaction_dt tidak ditemukan.';
    END IF;

    IF to_regclass('sc_trx.jurnal_hd') IS NULL THEN
        RAISE EXCEPTION 'sc_trx.jurnal_hd tidak ditemukan. Jalankan TAHAP 09.';
    END IF;

    IF to_regclass('sc_trx.jurnal_dt') IS NULL THEN
        RAISE EXCEPTION 'sc_trx.jurnal_dt tidak ditemukan. Jalankan TAHAP 10.';
    END IF;

    IF to_regclass('sc_mst.journal_type') IS NULL THEN
        RAISE EXCEPTION 'sc_mst.journal_type tidak ditemukan.';
    END IF;

    IF to_regclass('sc_mst.journal_type_coa') IS NULL THEN
        RAISE EXCEPTION 'sc_mst.journal_type_coa tidak ditemukan. Jalankan TAHAP 03.';
    END IF;

    IF to_regclass('sc_mst.coa') IS NULL THEN
        RAISE EXCEPTION 'sc_mst.coa tidak ditemukan.';
    END IF;

    IF to_regclass('sc_mst.tax_dtl') IS NULL THEN
        RAISE EXCEPTION 'sc_mst.tax_dtl tidak ditemukan.';
    END IF;
END;
$$;


/* ============================================================================
   TAHAP 15
   REVERSE ACCOUNTING

   POSTED  -> buat jurnal reversal POSTED, lalu original = REVERSED
   DRAFT   -> hapus detail, lalu original = CANCELLED

   Reversal menggunakan detail jurnal original dengan DEBET/KREDIT dibalik.
   Tidak menghapus jurnal POSTED secara fisik.
   ============================================================================ */

CREATE OR REPLACE FUNCTION sc_trx.fn_reverse_accounting_transaction(
    p_transaction_uniqueid TEXT
)
RETURNS VOID
LANGUAGE plpgsql
AS $$
DECLARE
    r           RECORD;
    d           RECORD;
    v_rev_id    BIGINT;
    v_rev_uid   TEXT;
    v_rev_type  CHAR(6);
    v_total_d   NUMERIC(18,2);
    v_total_k   NUMERIC(18,2);
BEGIN
    IF NULLIF(BTRIM(p_transaction_uniqueid), '') IS NULL THEN
        RETURN;
    END IF;

    FOR r IN
        SELECT jh.*
        FROM sc_trx.jurnal_hd jh
        WHERE jh.source_uniqueid = p_transaction_uniqueid
          AND jh.uniqueid NOT LIKE 'JRNL-REV-%'
          AND jh.status IN ('DRAFT', 'POSTED')
        ORDER BY jh.id
    LOOP
        /* --------------------------------------------------------
           DRAFT cukup dibatalkan.
           -------------------------------------------------------- */
        IF r.status = 'DRAFT' THEN
            DELETE FROM sc_trx.jurnal_dt
            WHERE jurnal_id = r.id;

            UPDATE sc_trx.jurnal_hd
            SET status = 'CANCELLED',
                updateddate = CURRENT_TIMESTAMP
            WHERE id = r.id;

            CONTINUE;
        END IF;

        /* --------------------------------------------------------
           POSTED wajib mempunyai detail.
           -------------------------------------------------------- */
        IF NOT EXISTS (
            SELECT 1
            FROM sc_trx.jurnal_dt jd
            WHERE jd.jurnal_id = r.id
        ) THEN
            RAISE EXCEPTION
                'Jurnal POSTED % tidak mempunyai detail jurnal.',
                r.id;
        END IF;

        /* --------------------------------------------------------
           POSTED dibuatkan jurnal reversal.
           ID reversal deterministic agar idempotent.
           -------------------------------------------------------- */
        v_rev_uid := 'JRNL-REV-' || md5(r.uniqueid || '|REV');

        SELECT id
        INTO v_rev_id
        FROM sc_trx.jurnal_hd
        WHERE uniqueid = v_rev_uid
        LIMIT 1;

        IF v_rev_id IS NULL THEN
            SELECT
                CASE
                    WHEN EXISTS (
                        SELECT 1
                        FROM sc_mst.journal_type
                        WHERE journal_type = 'JVREVS'
                    )
                    THEN 'JVREVS'::CHAR(6)
                    ELSE r.journal_type
                END
            INTO v_rev_type;

            INSERT INTO sc_trx.jurnal_hd
            (
                uniqueid,
                source_uniqueid,
                docno,
                doctype,
                journal_type,
                trxdate,
                type_in_out,
                ref_docno,
                ref_doctype,
                idbranch,
                cabang,
                module,
                accounting_effect,
                currcode,
                kurs,
                total_debet,
                total_kredit,
                balance,
                status,
                keterangan,
                createdby,
                createddate
            )
            VALUES
            (
                v_rev_uid,
                r.source_uniqueid,
                r.docno,
                r.doctype,
                v_rev_type,
                r.trxdate,
                r.type_in_out,
                r.docno,
                r.doctype,
                r.idbranch,
                r.cabang,
                'ACCOUNTING',
                'YES',
                r.currcode,
                COALESCE(r.kurs, 1),
                0,
                0,
                0,
                'DRAFT',
                'REVERSAL JURNAL ' || r.uniqueid,
                'SYSTEM',
                CURRENT_TIMESTAMP
            )
            RETURNING id
            INTO v_rev_id;
        ELSE
            /* Reversal sudah pernah dibuat. Pastikan detail tidak ganda. */
            SELECT status
            INTO r.status
            FROM sc_trx.jurnal_hd
            WHERE id = v_rev_id;

            IF r.status = 'POSTED' THEN
                UPDATE sc_trx.jurnal_hd
                SET status = 'REVERSED',
                    updateddate = CURRENT_TIMESTAMP
                WHERE id = r.id;

                CONTINUE;
            END IF;

            DELETE FROM sc_trx.jurnal_dt
            WHERE jurnal_id = v_rev_id;
        END IF;

        /* --------------------------------------------------------
           Balik detail journal original.
           -------------------------------------------------------- */
        FOR d IN
            SELECT jd.*
            FROM sc_trx.jurnal_dt jd
            WHERE jd.jurnal_id = r.id
            ORDER BY jd.seq, jd.id
        LOOP
            INSERT INTO sc_trx.jurnal_dt
            (
                jurnal_id,
                source_uniqueid,
                source_line_no,
                seq,
                account_role,
                value_source,
                idcoa,
                debet,
                kredit,
                ref_docno,
                ref_doctype,
                keterangan,
                currcode,
                kurs,
                createdby,
                createddate
            )
            VALUES
            (
                v_rev_id,
                p_transaction_uniqueid,
                d.source_line_no,
                d.seq,
                d.account_role,
                d.value_source,
                d.idcoa,
                COALESCE(d.kredit, 0),
                COALESCE(d.debet, 0),
                d.ref_docno,
                d.ref_doctype,
                'REVERSAL: ' || COALESCE(d.keterangan, ''),
                d.currcode,
                COALESCE(d.kurs, 1),
                'SYSTEM',
                CURRENT_TIMESTAMP
            );
        END LOOP;

        SELECT
            COALESCE(SUM(debet), 0),
            COALESCE(SUM(kredit), 0)
        INTO v_total_d, v_total_k
        FROM sc_trx.jurnal_dt
        WHERE jurnal_id = v_rev_id;

        IF ABS(v_total_d - v_total_k) > 0.01 THEN
            RAISE EXCEPTION
                'Jurnal reversal % tidak balance. Debet=%, Kredit=%',
                v_rev_uid,
                v_total_d,
                v_total_k;
        END IF;

        UPDATE sc_trx.jurnal_hd
        SET total_debet = v_total_d,
            total_kredit = v_total_k,
            balance = ROUND(v_total_d - v_total_k, 2),
            status = 'POSTED',
            updateddate = CURRENT_TIMESTAMP
        WHERE id = v_rev_id;

        UPDATE sc_trx.jurnal_hd
        SET status = 'REVERSED',
            updateddate = CURRENT_TIMESTAMP
        WHERE id = r.id;
    END LOOP;
END;
$$;


/* ============================================================================
   HELPER TAHAP 16
   RESOLVE COA BERDASARKAN ACCOUNT ROLE

   Priority:
       mbarang / currency
       konfigurasi_umum
       journal_type_coa.idcoa sebagai fallback
   ============================================================================ */

CREATE OR REPLACE FUNCTION sc_trx.fn_resolve_accounting_coa(
    p_account_role   TEXT,
    p_idbarang       CHAR(20),
    p_currcode       CHAR(3),
    p_fallback_idcoa VARCHAR(20)
)
RETURNS VARCHAR(20)
LANGUAGE plpgsql
AS $$
DECLARE
    v_role TEXT := UPPER(TRIM(COALESCE(p_account_role, '')));
    v_idgroup TEXT;

    v_ppersediaan TEXT;
    v_psj TEXT;
    v_pcogs TEXT;
    v_phppproduksi TEXT;
    v_pjasa TEXT;
    v_pwaste TEXT;

    v_phutang TEXT;
    v_pum TEXT;
    v_pbonus TEXT;
    v_hutangac TEXT;
    v_hutangbiaya1 TEXT;
    v_hutangbiaya2 TEXT;

    v_ppiutang TEXT;
    v_pumjual TEXT;
    v_ppendapatan TEXT;
    v_pretur TEXT;
    v_pdisc TEXT;
    v_pbonusjual TEXT;
    v_ptunai TEXT;
    v_piutangac TEXT;
    v_pendapatanac TEXT;
    v_pps TEXT;

    v_cfg JSONB;
    v_cfg_hpp TEXT;
    v_cfg_pproduksi TEXT;
    v_cfg_ppersediaan TEXT;
    v_cfg_psj TEXT;
    v_cfg_pjasa TEXT;
    v_cfg_pwaste TEXT;
    v_cfg_phutang TEXT;
    v_cfg_ppiutang TEXT;
    v_cfg_ppendapatan TEXT;
    v_cfg_ptunai TEXT;
    v_cfg_pkas TEXT;
    v_coa TEXT;
BEGIN
    /* --------------------------------------------------------
       BARANG
       -------------------------------------------------------- */
    IF NULLIF(BTRIM(COALESCE(p_idbarang::TEXT, '')), '') IS NOT NULL THEN
        SELECT
            NULLIF(BTRIM(to_jsonb(b)->>'idgroup'), ''),
            NULLIF(BTRIM(to_jsonb(b)->>'ppersediaan'), ''),
            NULLIF(BTRIM(to_jsonb(b)->>'psj'), ''),
            NULLIF(BTRIM(to_jsonb(b)->>'pcogs'), ''),
            NULLIF(BTRIM(to_jsonb(b)->>'phppproduksi'), ''),
            NULLIF(BTRIM(to_jsonb(b)->>'pjasa'), ''),
            NULLIF(BTRIM(to_jsonb(b)->>'pwaste'), '')
        INTO
            v_idgroup,
            v_ppersediaan,
            v_psj,
            v_pcogs,
            v_phppproduksi,
            v_pjasa,
            v_pwaste
        FROM sc_mst.mbarang b
        WHERE BTRIM(to_jsonb(b)->>'idbarang') = BTRIM(p_idbarang::TEXT)
        LIMIT 1;
    END IF;

    /* --------------------------------------------------------
       CURRENCY
       -------------------------------------------------------- */
    SELECT
        NULLIF(BTRIM(c.phutang::TEXT), ''),
        NULLIF(BTRIM(c.pum::TEXT), ''),
        NULLIF(BTRIM(c.pbonus::TEXT), ''),
        NULLIF(BTRIM(c.hutangac::TEXT), ''),
        NULLIF(BTRIM(c.hutangbiaya1::TEXT), ''),
        NULLIF(BTRIM(c.hutangbiaya2::TEXT), ''),
        NULLIF(BTRIM(c.ppiutang::TEXT), ''),
        NULLIF(BTRIM(c.pumjual::TEXT), ''),
        NULLIF(BTRIM(c.ppendapatan::TEXT), ''),
        NULLIF(BTRIM(c.pretur::TEXT), ''),
        NULLIF(BTRIM(c.pdisc::TEXT), ''),
        NULLIF(BTRIM(c.pbonusjual::TEXT), ''),
        NULLIF(BTRIM(c.ptunai::TEXT), ''),
        NULLIF(BTRIM(c.piutangac::TEXT), ''),
        NULLIF(BTRIM(c.pendapatanac::TEXT), ''),
        NULLIF(BTRIM(c.pps::TEXT), '')
    INTO
        v_phutang,
        v_pum,
        v_pbonus,
        v_hutangac,
        v_hutangbiaya1,
        v_hutangbiaya2,
        v_ppiutang,
        v_pumjual,
        v_ppendapatan,
        v_pretur,
        v_pdisc,
        v_pbonusjual,
        v_ptunai,
        v_piutangac,
        v_pendapatanac,
        v_pps
    FROM sc_mst.currency c
    WHERE BTRIM(c.currcode::TEXT)
        = BTRIM(COALESCE(NULLIF(p_currcode::TEXT, ''), 'IDR'))
    LIMIT 1;

    /* --------------------------------------------------------
       KONFIGURASI UMUM
       -------------------------------------------------------- */
    SELECT to_jsonb(k)
    INTO v_cfg
    FROM sc_mst.konfigurasi_umum k
    LIMIT 1;

    v_cfg_hpp           := NULLIF(BTRIM(COALESCE(v_cfg->>'hpp', '')), '');
    v_cfg_pproduksi     := NULLIF(BTRIM(COALESCE(v_cfg->>'pproduksi', '')), '');
    v_cfg_ppersediaan   := NULLIF(BTRIM(COALESCE(v_cfg->>'ppersediaan', '')), '');
    v_cfg_psj           := NULLIF(BTRIM(COALESCE(v_cfg->>'psj', '')), '');
    v_cfg_pjasa         := NULLIF(BTRIM(COALESCE(v_cfg->>'pjasa', '')), '');
    v_cfg_pwaste        := NULLIF(BTRIM(COALESCE(v_cfg->>'pwaste', '')), '');
    v_cfg_phutang       := NULLIF(BTRIM(COALESCE(v_cfg->>'phutang', '')), '');
    v_cfg_ppiutang      := NULLIF(BTRIM(COALESCE(v_cfg->>'ppiutang', '')), '');
    v_cfg_ppendapatan   := NULLIF(BTRIM(COALESCE(v_cfg->>'ppendapatan', '')), '');
    v_cfg_ptunai        := NULLIF(BTRIM(COALESCE(v_cfg->>'ptunai', '')), '');
    v_cfg_pkas          := NULLIF(BTRIM(COALESCE(v_cfg->>'pkas', '')), '');

    /* --------------------------------------------------------
       RESOLVE ROLE
       -------------------------------------------------------- */
    CASE v_role
        WHEN 'STOCK', 'INVENTORY' THEN
            IF UPPER(COALESCE(v_idgroup, '')) = 'JSA' THEN
                v_coa := COALESCE(v_pjasa, v_cfg_pjasa, p_fallback_idcoa);
            ELSE
                v_coa := COALESCE(v_ppersediaan, v_cfg_ppersediaan, p_fallback_idcoa);
            END IF;

        WHEN 'SERVICE', 'JASA', 'SERVICE_EXPENSE' THEN
            v_coa := COALESCE(v_pjasa, v_cfg_pjasa, p_fallback_idcoa);

        WHEN 'AP', 'HUTANG' THEN
            v_coa := COALESCE(v_phutang, v_cfg_phutang, p_fallback_idcoa);

        WHEN 'AR', 'PIUTANG' THEN
            v_coa := COALESCE(v_ppiutang, v_cfg_ppiutang, p_fallback_idcoa);

        WHEN 'SALES', 'INCOME', 'PENDAPATAN' THEN
            v_coa := COALESCE(v_ppendapatan, v_cfg_ppendapatan, p_fallback_idcoa);

        WHEN 'SALES_RETURN', 'RETURN_SALES', 'RETUR_PENJUALAN' THEN
            v_coa := COALESCE(v_pretur, p_fallback_idcoa);

        WHEN 'COGS', 'HPP' THEN
            v_coa := COALESCE(v_pcogs, v_cfg_hpp, p_fallback_idcoa);

        WHEN 'PRODUCTION_HPP', 'HPP_PRODUKSI', 'WIP' THEN
            v_coa := COALESCE(v_phppproduksi, v_cfg_pproduksi, p_fallback_idcoa);

        WHEN 'PSJ', 'DELIVERY', 'SURAT_JALAN' THEN
            v_coa := COALESCE(v_psj, v_cfg_psj, p_fallback_idcoa);

        WHEN 'WASTE', 'SCRAP' THEN
            v_coa := COALESCE(v_pwaste, v_cfg_pwaste, p_fallback_idcoa);

        WHEN 'CASH', 'KAS', 'TUNAI' THEN
            v_coa := COALESCE(v_ptunai, v_cfg_ptunai, v_cfg_pkas, p_fallback_idcoa);

        WHEN 'PUM', 'DOWN_PAYMENT_PURCHASE', 'UM_PEMBELIAN' THEN
            v_coa := COALESCE(v_pum, p_fallback_idcoa);

        WHEN 'BONUS', 'PURCHASE_BONUS' THEN
            v_coa := COALESCE(v_pbonus, p_fallback_idcoa);

        WHEN 'HUTANG_AC' THEN
            v_coa := COALESCE(v_hutangac, p_fallback_idcoa);

        WHEN 'HUTANG_BIAYA1', 'ACCRUED_EXPENSE' THEN
            v_coa := COALESCE(v_hutangbiaya1, p_fallback_idcoa);

        WHEN 'HUTANG_BIAYA2' THEN
            v_coa := COALESCE(v_hutangbiaya2, p_fallback_idcoa);

        WHEN 'PUMJUAL', 'DOWN_PAYMENT_SALES', 'UM_PENJUALAN' THEN
            v_coa := COALESCE(v_pumjual, p_fallback_idcoa);

        WHEN 'DISCOUNT', 'DISC', 'DISKON' THEN
            v_coa := COALESCE(v_pdisc, p_fallback_idcoa);

        WHEN 'BONUS_JUAL', 'SALES_BONUS' THEN
            v_coa := COALESCE(v_pbonusjual, p_fallback_idcoa);

        WHEN 'PIUTANG_AC' THEN
            v_coa := COALESCE(v_piutangac, p_fallback_idcoa);

        WHEN 'PENDAPATAN_AC', 'OTHER_INCOME' THEN
            v_coa := COALESCE(v_pendapatanac, p_fallback_idcoa);

        WHEN 'PPS' THEN
            v_coa := COALESCE(v_pps, p_fallback_idcoa);

        ELSE
            v_coa := p_fallback_idcoa;
    END CASE;

    v_coa := NULLIF(BTRIM(COALESCE(v_coa, '')), '');

    IF v_coa IS NULL THEN
        RETURN NULL;
    END IF;

    IF NOT EXISTS (
        SELECT 1
        FROM sc_mst.coa c
        WHERE BTRIM(c.idcoa::TEXT) = BTRIM(v_coa)
    ) THEN
        RAISE EXCEPTION
            'COA % tidak ditemukan. account_role=%, idbarang=%, currcode=%',
            v_coa,
            v_role,
            COALESCE(BTRIM(p_idbarang::TEXT), ''),
            COALESCE(BTRIM(p_currcode::TEXT), '');
    END IF;

    RETURN v_coa;
END;
$$;


/* ============================================================================
   TAHAP 16
   POST / GENERATE ACCOUNTING

   transaction_dt
       -> journal_type_coa
       -> jurnal_hd
       -> jurnal_dt

   value_source:
       NILAI / DPP / PAJAK / TOTAL / QTY / COST

   COST:
       - prioritas stkblc.totalcost berdasarkan uniqueid
       - fallback ref_docno/ref_doctype
       - fallback stkblc_avgcost berdasarkan idbarang + idlocation + batch
       - tidak bergantung pada transaction_dt.stock_uniqueid

   Tax:
       account berasal dari sc_mst.tax_dtl, bukan hard-code.
       Jika tax_dtl memiliki lebih dari satu komponen, setiap komponen
       dibuat sebagai baris jurnal tersendiri.
   ============================================================================ */

CREATE OR REPLACE FUNCTION sc_trx.fn_post_accounting_transaction(
    p_uniqueid TEXT
)
RETURNS BIGINT
LANGUAGE plpgsql
AS $$
DECLARE
    t              sc_trx.transaction_dt%ROWTYPE;
    m              RECORD;
    tx             RECORD;

    v_jurnal_id    BIGINT;
    v_existing_id      BIGINT;
    v_existing_status   VARCHAR(20);
    v_version           INTEGER;
    v_journal_uid  TEXT;

    v_value        NUMERIC(18,2);
    v_cost         NUMERIC(18,2);
    v_total_d      NUMERIC(18,2);
    v_total_k      NUMERIC(18,2);
    v_balance      NUMERIC(18,2);

    v_coa          VARCHAR(20);
    v_tax_count    INTEGER := 0;
    v_tax_handled  BOOLEAN := FALSE;
    v_source       TEXT;
BEGIN
    SELECT *
    INTO t
    FROM sc_trx.transaction_dt
    WHERE uniqueid = p_uniqueid;

    IF NOT FOUND THEN
        RAISE EXCEPTION 'Transaction % tidak ditemukan.', p_uniqueid;
    END IF;

    IF COALESCE(t.accounting_effect, 'YES') <> 'YES' THEN
        RETURN NULL;
    END IF;

    /* --------------------------------------------------------
       Mapping wajib tersedia untuk accounting transaction.
       -------------------------------------------------------- */
    SELECT COUNT(*)
    INTO v_tax_count
    FROM sc_mst.journal_type_coa j
    WHERE j.journal_type = t.journal_type
      AND j.active = 'YES';

    IF v_tax_count = 0 THEN
        RAISE EXCEPTION
            'Mapping accounting belum tersedia untuk journal_type=%',
            t.journal_type;
    END IF;

    /* --------------------------------------------------------
       Cari journal normal terakhir untuk transaction ini.
       Reversal journal dikecualikan.
       -------------------------------------------------------- */
    SELECT jh.id, jh.status
    INTO v_existing_id, v_existing_status
    FROM sc_trx.jurnal_hd jh
    WHERE jh.source_uniqueid = t.uniqueid
      AND jh.uniqueid NOT LIKE 'JRNL-REV-%'
    ORDER BY jh.id DESC
    LIMIT 1;

    /* POSTED normal journal sudah selesai. */
    IF v_existing_id IS NOT NULL AND v_existing_status = 'POSTED' THEN
        RETURN v_existing_id;
    END IF;

    IF v_existing_id IS NOT NULL AND v_existing_status = 'DRAFT' THEN
        v_jurnal_id := v_existing_id;

        DELETE FROM sc_trx.jurnal_dt
        WHERE jurnal_id = v_jurnal_id;

        UPDATE sc_trx.jurnal_hd
        SET
            docno = t.docno,
            doctype = t.doctype,
            journal_type = t.journal_type,
            trxdate = t.docdate,
            type_in_out = t.type_in_out,
            ref_docno = COALESCE(t.ref_docno, ''),
            ref_doctype = COALESCE(t.ref_doctype, ''),
            idbranch = COALESCE(t.idbranch, ''),
            cabang = COALESCE(t.cabang, ''),
            module = COALESCE(t.module, ''),
            accounting_effect = COALESCE(t.accounting_effect, 'YES'),
            currcode = COALESCE(t.currcode, ''),
            kurs = COALESCE(t.kurs, 1),
            keterangan = COALESCE(t.keterangan, ''),
            updatedby = t.updatedby,
            updateddate = CURRENT_TIMESTAMP,
            status = 'DRAFT'
        WHERE id = v_jurnal_id;
    ELSE
        SELECT COUNT(*) + 1
        INTO v_version
        FROM sc_trx.jurnal_hd
        WHERE source_uniqueid = t.uniqueid
          AND uniqueid NOT LIKE 'JRNL-REV-%';

        v_journal_uid :=
            sc_trx.fn_journal_uniqueid(
                t.docno,
                t.doctype,
                t.journal_type,
                t.idbranch,
                t.uniqueid || '|V' || v_version
            );

        INSERT INTO sc_trx.jurnal_hd
        (
            uniqueid,
            source_uniqueid,
            docno,
            doctype,
            journal_type,
            trxdate,
            type_in_out,
            ref_docno,
            ref_doctype,
            idbranch,
            cabang,
            module,
            accounting_effect,
            currcode,
            kurs,
            total_debet,
            total_kredit,
            balance,
            status,
            keterangan,
            createdby,
            createddate
        )
        VALUES
        (
            v_journal_uid,
            t.uniqueid,
            t.docno,
            t.doctype,
            t.journal_type,
            t.docdate,
            t.type_in_out,
            COALESCE(t.ref_docno, ''),
            COALESCE(t.ref_doctype, ''),
            COALESCE(t.idbranch, ''),
            COALESCE(t.cabang, ''),
            COALESCE(t.module, ''),
            COALESCE(t.accounting_effect, 'YES'),
            COALESCE(t.currcode, ''),
            COALESCE(t.kurs, 1),
            0,
            0,
            0,
            'DRAFT',
            COALESCE(t.keterangan, ''),
            t.createdby,
            COALESCE(t.createddate, CURRENT_TIMESTAMP)
        )
        RETURNING id
        INTO v_jurnal_id;
    END IF;

    /* --------------------------------------------------------
       COST transaksi.

       Urutan:
       1. stkblc berdasarkan transaction uniqueid
       2. stkblc berdasarkan ref_docno/ref_doctype
       3. stkblc_avgcost berdasarkan idbarang + warehouse + batch

       Langkah 3 TIDAK bergantung pada stock_uniqueid karena
       transaksi accounting-only seperti SALESX dapat memang
       tidak mempunyai stock_uniqueid.
       -------------------------------------------------------- */
    SELECT COALESCE(SUM(sb.totalcost), 0)
    INTO v_cost
    FROM sc_trx.stkblc sb
    WHERE sb.uniqueid = t.uniqueid;

    IF COALESCE(v_cost, 0) = 0
       AND NULLIF(BTRIM(COALESCE(t.ref_docno, '')), '') IS NOT NULL
    THEN
        SELECT COALESCE(SUM(sb.totalcost), 0)
        INTO v_cost
        FROM sc_trx.stkblc sb
        WHERE sb.docno = t.ref_docno
          AND sb.doctype = COALESCE(t.ref_doctype, '');
    END IF;

    IF COALESCE(v_cost, 0) = 0
       AND NULLIF(BTRIM(COALESCE(t.idbarang::TEXT, '')), '') IS NOT NULL
       AND NULLIF(BTRIM(COALESCE(t.warehouse::TEXT, '')), '') IS NOT NULL
    THEN
        SELECT ROUND(COALESCE(ac.avg_cost, 0) * COALESCE(t.qty, 0), 2)
        INTO v_cost
        FROM sc_trx.stkblc_avgcost ac
        WHERE BTRIM(ac.idbarang::TEXT)
                = BTRIM(COALESCE(t.idbarang::TEXT, ''))
          AND BTRIM(ac.idlocation::TEXT)
                = BTRIM(COALESCE(t.warehouse::TEXT, ''))
          AND BTRIM(COALESCE(ac.batch, ''))
                = BTRIM(COALESCE(t.batch::TEXT, ''))
        LIMIT 1;
    END IF;

    /* --------------------------------------------------------
       Generate detail sesuai mapping.
       -------------------------------------------------------- */
    FOR m IN
        SELECT
            j.id,
            j.seq,
            j.account_role,
            j.idcoa,
            j.debit_credit,
            j.value_source,
            j.description
        FROM sc_mst.journal_type_coa j
        WHERE j.journal_type = t.journal_type
          AND j.active = 'YES'
        ORDER BY j.seq, j.id
    LOOP
        /* ----------------------------------------------------
           TAX: resolve dari tax_dtl.
           Hanya baris TAX pertama yang memicu expansion.
           ---------------------------------------------------- */
        IF UPPER(TRIM(COALESCE(m.account_role, ''))) = 'TAX'
           AND UPPER(TRIM(COALESCE(m.value_source, ''))) = 'PAJAK'
           AND COALESCE(t.pajak, 0) > 0
           AND NOT v_tax_handled
        THEN
            SELECT COUNT(*)
            INTO v_tax_count
            FROM sc_mst.tax_dtl d
            JOIN sc_mst.journal_type jt
              ON jt.journal_type = t.journal_type
            WHERE TRIM(d.idtax) = TRIM(COALESCE(t.idtax, 'NON'))
              AND d.status = 'P'
              AND
              (
                  (UPPER(TRIM(jt.module)) = 'PURCHASE'
                   AND NULLIF(TRIM(d.prk_masukan), '') IS NOT NULL)
                  OR
                  (UPPER(TRIM(jt.module)) = 'SALES'
                   AND NULLIF(TRIM(d.prk_keluaran), '') IS NOT NULL)
                  OR
                  (UPPER(TRIM(t.journal_type)) = 'TAXINX'
                   AND NULLIF(TRIM(d.prk_masukan), '') IS NOT NULL)
                  OR
                  (UPPER(TRIM(t.journal_type)) = 'TAXOUT'
                   AND NULLIF(TRIM(d.prk_keluaran), '') IS NOT NULL)
              );

            IF v_tax_count > 0 THEN
                FOR tx IN
                    SELECT
                        d.idgrouptax,
                        d.percentation,
                        CASE
                            WHEN UPPER(TRIM(jt.module)) = 'PURCHASE'
                                THEN NULLIF(TRIM(d.prk_masukan), '')
                            WHEN UPPER(TRIM(jt.module)) = 'SALES'
                                THEN NULLIF(TRIM(d.prk_keluaran), '')
                            WHEN UPPER(TRIM(t.journal_type)) = 'TAXINX'
                                THEN NULLIF(TRIM(d.prk_masukan), '')
                            WHEN UPPER(TRIM(t.journal_type)) = 'TAXOUT'
                                THEN NULLIF(TRIM(d.prk_keluaran), '')
                            ELSE NULL
                        END AS idcoa_tax
                    FROM sc_mst.tax_dtl d
                    JOIN sc_mst.journal_type jt
                      ON jt.journal_type = t.journal_type
                    WHERE TRIM(d.idtax) = TRIM(COALESCE(t.idtax, 'NON'))
                      AND d.status = 'P'
                      AND
                      (
                          (UPPER(TRIM(jt.module)) = 'PURCHASE'
                           AND NULLIF(TRIM(d.prk_masukan), '') IS NOT NULL)
                          OR
                          (UPPER(TRIM(jt.module)) = 'SALES'
                           AND NULLIF(TRIM(d.prk_keluaran), '') IS NOT NULL)
                          OR
                          (UPPER(TRIM(t.journal_type)) = 'TAXINX'
                           AND NULLIF(TRIM(d.prk_masukan), '') IS NOT NULL)
                          OR
                          (UPPER(TRIM(t.journal_type)) = 'TAXOUT'
                           AND NULLIF(TRIM(d.prk_keluaran), '') IS NOT NULL)
                      )
                    ORDER BY d.id
                LOOP
                    IF v_tax_count = 1 THEN
                        v_value := ROUND(COALESCE(t.pajak, 0), 2);
                    ELSE
                        v_value := ROUND(
                            COALESCE(t.dpp, 0)
                            * COALESCE(tx.percentation, 0)
                            / 100,
                            2
                        );
                    END IF;

                    IF v_value > 0 THEN
                        INSERT INTO sc_trx.jurnal_dt
                        (
                            jurnal_id,
                            source_uniqueid,
                            source_line_no,
                            seq,
                            account_role,
                            value_source,
                            idcoa,
                            debet,
                            kredit,
                            ref_docno,
                            ref_doctype,
                            keterangan,
                            currcode,
                            kurs,
                            createdby,
                            createddate
                        )
                        VALUES
                        (
                            v_jurnal_id,
                            t.uniqueid,
                            t.line_no,
                            m.seq,
                            m.account_role,
                            m.value_source,
                            tx.idcoa_tax,
                            CASE WHEN m.debit_credit = 'D' THEN v_value ELSE 0 END,
                            CASE WHEN m.debit_credit = 'K' THEN v_value ELSE 0 END,
                            COALESCE(t.ref_docno, ''),
                            COALESCE(t.ref_doctype, ''),
                            COALESCE(m.description, t.keterangan, ''),
                            COALESCE(t.currcode, ''),
                            COALESCE(t.kurs, 1),
                            t.createdby,
                            CURRENT_TIMESTAMP
                        );
                    END IF;
                END LOOP;

                v_tax_handled := TRUE;
                CONTINUE;
            END IF;
        END IF;

        /* ----------------------------------------------------
           Nilai detail jurnal normal.
           ---------------------------------------------------- */
        v_source := UPPER(TRIM(COALESCE(m.value_source, '')));

        v_value :=
            CASE v_source
                WHEN 'NILAI' THEN COALESCE(t.nilai, 0)
                WHEN 'DPP'   THEN COALESCE(t.dpp, 0)
                WHEN 'PAJAK' THEN COALESCE(t.pajak, 0)
                WHEN 'TOTAL' THEN COALESCE(t.total, 0)
                WHEN 'QTY'   THEN COALESCE(t.qty, 0)
                WHEN 'COST'  THEN COALESCE(v_cost, 0)
                ELSE 0
            END;

        IF v_value <= 0 THEN
            CONTINUE;
        END IF;

        v_coa := sc_trx.fn_resolve_accounting_coa(
            m.account_role,
            t.idbarang,
            t.currcode,
            m.idcoa
        );

        IF NULLIF(BTRIM(COALESCE(v_coa, '')), '') IS NULL THEN
            RAISE EXCEPTION
                'COA tidak ter-resolve. journal_type=%, seq=%, role=%',
                t.journal_type,
                m.seq,
                m.account_role;
        END IF;

        INSERT INTO sc_trx.jurnal_dt
        (
            jurnal_id,
            source_uniqueid,
            source_line_no,
            seq,
            account_role,
            value_source,
            idcoa,
            debet,
            kredit,
            ref_docno,
            ref_doctype,
            keterangan,
            currcode,
            kurs,
            createdby,
            createddate
        )
        VALUES
        (
            v_jurnal_id,
            t.uniqueid,
            t.line_no,
            m.seq,
            m.account_role,
            m.value_source,
            v_coa,
            CASE WHEN m.debit_credit = 'D' THEN v_value ELSE 0 END,
            CASE WHEN m.debit_credit = 'K' THEN v_value ELSE 0 END,
            COALESCE(t.ref_docno, ''),
            COALESCE(t.ref_doctype, ''),
            COALESCE(m.description, t.keterangan, ''),
            COALESCE(t.currcode, ''),
            COALESCE(t.kurs, 1),
            t.createdby,
            CURRENT_TIMESTAMP
        );
    END LOOP;

    /* --------------------------------------------------------
       Recheck journal.
       -------------------------------------------------------- */
    SELECT
        COALESCE(SUM(debet), 0),
        COALESCE(SUM(kredit), 0)
    INTO v_total_d, v_total_k
    FROM sc_trx.jurnal_dt
    WHERE jurnal_id = v_jurnal_id;

    v_balance := ROUND(v_total_d - v_total_k, 2);

    IF v_total_d = 0 AND v_total_k = 0 THEN
        DELETE FROM sc_trx.jurnal_dt
        WHERE jurnal_id = v_jurnal_id;

        UPDATE sc_trx.jurnal_hd
        SET total_debet = 0,
            total_kredit = 0,
            balance = 0,
            status = 'CANCELLED',
            updateddate = CURRENT_TIMESTAMP
        WHERE id = v_jurnal_id;

        RAISE EXCEPTION
            'Journal type % belum menghasilkan nilai accounting untuk transaction %.',
            t.journal_type,
            t.uniqueid;
    END IF;

    IF ABS(v_balance) > 0.01 THEN
        RAISE EXCEPTION
            'Jurnal % tidak balance. Debet=%, Kredit=%, Balance=%',
            v_jurnal_id,
            v_total_d,
            v_total_k,
            v_balance;
    END IF;

    UPDATE sc_trx.jurnal_hd
    SET total_debet = v_total_d,
        total_kredit = v_total_k,
        balance = v_balance,
        status = 'POSTED',
        updateddate = CURRENT_TIMESTAMP
    WHERE id = v_jurnal_id;

    RETURN v_jurnal_id;
END;
$$;


/* ============================================================
   JSYS ERP
   PATCH TAHAP 16
   FIX RESOLVER COA - VARIABLE SAFE + PATCH 2 WASTE/SCRAP
   ============================================================

   Error sebelumnya:
       "v_phpproduksi" is not a known variable

   Solusi:
       Tidak lagi mendeklarasikan puluhan variable COA barang.
       Master barang / currency / konfigurasi dibaca sebagai JSONB.

   Tambahan penting:
       ADJUSTMENT / SELISIH
           -> konfigurasi_umum.pselisih
           -> journal_type_coa.idcoa

   Tidak DROP table.
   ============================================================ */

BEGIN;


/* ============================================================
   REPLACE FUNCTION RESOLVER ACCOUNTING COA
   ============================================================ */

CREATE OR REPLACE FUNCTION sc_trx.fn_resolve_accounting_coa(
    p_account_role   TEXT,
    p_idbarang       CHAR(20),
    p_currcode       CHAR(3),
    p_fallback_idcoa VARCHAR(20)
)
RETURNS VARCHAR(20)
LANGUAGE plpgsql
AS $$
DECLARE
    v_role      TEXT := UPPER(TRIM(COALESCE(p_account_role, '')));
    v_barang    JSONB;
    v_currency  JSONB;
    v_cfg       JSONB;
    v_coa       TEXT;
BEGIN

    /* ========================================================
       MASTER BARANG
       ======================================================== */
    IF NULLIF(BTRIM(COALESCE(p_idbarang::TEXT, '')), '') IS NOT NULL THEN

        SELECT to_jsonb(b)
        INTO v_barang
        FROM sc_mst.mbarang b
        WHERE BTRIM(to_jsonb(b)->>'idbarang')
              = BTRIM(p_idbarang::TEXT)
        LIMIT 1;

    END IF;


    /* ========================================================
       MASTER CURRENCY
       ======================================================== */
    SELECT to_jsonb(c)
    INTO v_currency
    FROM sc_mst.currency c
    WHERE BTRIM(c.currcode::TEXT)
          = BTRIM(
              COALESCE(
                  NULLIF(p_currcode::TEXT, ''),
                  'IDR'
              )
          )
    LIMIT 1;


    /* ========================================================
       KONFIGURASI UMUM
       ======================================================== */
    SELECT to_jsonb(k)
    INTO v_cfg
    FROM sc_mst.konfigurasi_umum k
    LIMIT 1;


    /* ========================================================
       RESOLVE ROLE
       ======================================================== */
    CASE v_role

        /* ----------------------------------------------------
           INVENTORY
           BRG -> ppersediaan
           JSA -> pjasa
           ---------------------------------------------------- */
        WHEN 'STOCK', 'INVENTORY' THEN

            IF UPPER(
                COALESCE(v_barang->>'idgroup', '')
            ) = 'JSA'
            THEN

                v_coa :=
                    COALESCE(
                        NULLIF(BTRIM(v_barang->>'pjasa'), ''),
                        NULLIF(BTRIM(v_cfg->>'pjasa'), ''),
                        NULLIF(BTRIM(p_fallback_idcoa), '')
                    );

            ELSE

                v_coa :=
                    COALESCE(
                        NULLIF(BTRIM(v_barang->>'ppersediaan'), ''),
                        NULLIF(BTRIM(v_cfg->>'ppersediaan'), ''),
                        NULLIF(BTRIM(p_fallback_idcoa), '')
                    );

            END IF;


        /* ----------------------------------------------------
           SERVICE / JASA
           ---------------------------------------------------- */
        WHEN 'SERVICE', 'JASA', 'SERVICE_EXPENSE' THEN

            v_coa :=
                COALESCE(
                    NULLIF(BTRIM(v_barang->>'pjasa'), ''),
                    NULLIF(BTRIM(v_cfg->>'pjasa'), ''),
                    NULLIF(BTRIM(p_fallback_idcoa), '')
                );


        /* ----------------------------------------------------
           AP
           ---------------------------------------------------- */
        WHEN 'AP', 'HUTANG' THEN

            v_coa :=
                COALESCE(
                    NULLIF(BTRIM(v_currency->>'phutang'), ''),
                    NULLIF(BTRIM(v_cfg->>'phutang'), ''),
                    NULLIF(BTRIM(p_fallback_idcoa), '')
                );


        /* ----------------------------------------------------
           AR
           ---------------------------------------------------- */
        WHEN 'AR', 'PIUTANG' THEN

            v_coa :=
                COALESCE(
                    NULLIF(BTRIM(v_currency->>'ppiutang'), ''),
                    NULLIF(BTRIM(v_cfg->>'ppiutang'), ''),
                    NULLIF(BTRIM(p_fallback_idcoa), '')
                );


        /* ----------------------------------------------------
           SALES / INCOME
           ---------------------------------------------------- */
        WHEN 'SALES', 'INCOME', 'PENDAPATAN' THEN

            v_coa :=
                COALESCE(
                    NULLIF(BTRIM(v_currency->>'ppendapatan'), ''),
                    NULLIF(BTRIM(v_cfg->>'ppendapatan'), ''),
                    NULLIF(BTRIM(p_fallback_idcoa), '')
                );


        /* ----------------------------------------------------
           SALES RETURN
           ---------------------------------------------------- */
        WHEN 'SALES_RETURN',
             'RETURN_SALES',
             'RETUR_PENJUALAN' THEN

            v_coa :=
                COALESCE(
                    NULLIF(BTRIM(v_currency->>'pretur'), ''),
                    NULLIF(BTRIM(p_fallback_idcoa), '')
                );


        /* ----------------------------------------------------
           COGS / HPP
           ---------------------------------------------------- */
        WHEN 'COGS', 'HPP' THEN

            v_coa :=
                COALESCE(
                    NULLIF(BTRIM(v_barang->>'pcogs'), ''),
                    NULLIF(BTRIM(v_cfg->>'hpp'), ''),
                    NULLIF(BTRIM(p_fallback_idcoa), '')
                );


        /* ----------------------------------------------------
           WIP / HPP PRODUKSI
           ---------------------------------------------------- */
        WHEN 'PRODUCTION_HPP',
             'HPP_PRODUKSI',
             'WIP' THEN

            v_coa :=
                COALESCE(
                    NULLIF(BTRIM(v_barang->>'phpproduksi'), ''),
                    NULLIF(BTRIM(v_cfg->>'pproduksi'), ''),
                    NULLIF(BTRIM(p_fallback_idcoa), '')
                );


        /* ----------------------------------------------------
           DELIVERY
           ---------------------------------------------------- */
        WHEN 'PSJ',
             'DELIVERY',
             'SURAT_JALAN' THEN

            v_coa :=
                COALESCE(
                    NULLIF(BTRIM(v_barang->>'psj'), ''),
                    NULLIF(BTRIM(v_cfg->>'psj'), ''),
                    NULLIF(BTRIM(p_fallback_idcoa), '')
                );


        /* ----------------------------------------------------
           WASTE / SCRAP
           ---------------------------------------------------- */
        WHEN 'WASTE',
             'SCRAP' THEN

            v_coa :=
                COALESCE(
                    NULLIF(BTRIM(v_barang->>'pwaste'), ''),
                    NULLIF(BTRIM(v_cfg->>'pwaste'), ''),
                    NULLIF(BTRIM(p_fallback_idcoa), '')
                );


        /* ----------------------------------------------------
           CASH
           ---------------------------------------------------- */
        WHEN 'CASH',
             'KAS',
             'TUNAI' THEN

            v_coa :=
                COALESCE(
                    NULLIF(BTRIM(v_currency->>'ptunai'), ''),
                    NULLIF(BTRIM(v_cfg->>'ptunai'), ''),
                    NULLIF(BTRIM(v_cfg->>'pkas'), ''),
                    NULLIF(BTRIM(p_fallback_idcoa), '')
                );


        /* ----------------------------------------------------
           UANG MUKA PEMBELIAN
           ---------------------------------------------------- */
        WHEN 'PUM',
             'DOWN_PAYMENT_PURCHASE',
             'UM_PEMBELIAN' THEN

            v_coa :=
                COALESCE(
                    NULLIF(BTRIM(v_currency->>'pum'), ''),
                    NULLIF(BTRIM(p_fallback_idcoa), '')
                );


        /* ----------------------------------------------------
           BONUS PEMBELIAN
           ---------------------------------------------------- */
        WHEN 'BONUS',
             'PURCHASE_BONUS' THEN

            v_coa :=
                COALESCE(
                    NULLIF(BTRIM(v_currency->>'pbonus'), ''),
                    NULLIF(BTRIM(p_fallback_idcoa), '')
                );


        /* ----------------------------------------------------
           HUTANG AC
           ---------------------------------------------------- */
        WHEN 'HUTANG_AC' THEN

            v_coa :=
                COALESCE(
                    NULLIF(BTRIM(v_currency->>'hutangac'), ''),
                    NULLIF(BTRIM(p_fallback_idcoa), '')
                );


        WHEN 'HUTANG_BIAYA1',
             'ACCRUED_EXPENSE' THEN

            v_coa :=
                COALESCE(
                    NULLIF(BTRIM(v_currency->>'hutangbiaya1'), ''),
                    NULLIF(BTRIM(p_fallback_idcoa), '')
                );


        WHEN 'HUTANG_BIAYA2' THEN

            v_coa :=
                COALESCE(
                    NULLIF(BTRIM(v_currency->>'hutangbiaya2'), ''),
                    NULLIF(BTRIM(p_fallback_idcoa), '')
                );


        /* ----------------------------------------------------
           UANG MUKA PENJUALAN
           ---------------------------------------------------- */
        WHEN 'PUMJUAL',
             'DOWN_PAYMENT_SALES',
             'UM_PENJUALAN' THEN

            v_coa :=
                COALESCE(
                    NULLIF(BTRIM(v_currency->>'pumjual'), ''),
                    NULLIF(BTRIM(p_fallback_idcoa), '')
                );


        /* ----------------------------------------------------
           DISCOUNT
           ---------------------------------------------------- */
        WHEN 'DISCOUNT',
             'DISC',
             'DISKON' THEN

            v_coa :=
                COALESCE(
                    NULLIF(BTRIM(v_currency->>'pdisc'), ''),
                    NULLIF(BTRIM(p_fallback_idcoa), '')
                );


        /* ----------------------------------------------------
           BONUS PENJUALAN
           ---------------------------------------------------- */
        WHEN 'BONUS_JUAL',
             'SALES_BONUS' THEN

            v_coa :=
                COALESCE(
                    NULLIF(BTRIM(v_currency->>'pbonusjual'), ''),
                    NULLIF(BTRIM(p_fallback_idcoa), '')
                );


        /* ----------------------------------------------------
           PIUTANG AC
           ---------------------------------------------------- */
        WHEN 'PIUTANG_AC' THEN

            v_coa :=
                COALESCE(
                    NULLIF(BTRIM(v_currency->>'piutangac'), ''),
                    NULLIF(BTRIM(p_fallback_idcoa), '')
                );


        /* ----------------------------------------------------
           PENDAPATAN AC
           ---------------------------------------------------- */
        WHEN 'PENDAPATAN_AC',
             'OTHER_INCOME' THEN

            v_coa :=
                COALESCE(
                    NULLIF(BTRIM(v_currency->>'pendapatanac'), ''),
                    NULLIF(BTRIM(p_fallback_idcoa), '')
                );


        /* ----------------------------------------------------
           PPS
           ---------------------------------------------------- */
        WHEN 'PPS' THEN

            v_coa :=
                COALESCE(
                    NULLIF(BTRIM(v_currency->>'pps'), ''),
                    NULLIF(BTRIM(p_fallback_idcoa), '')
                );


        /* ----------------------------------------------------
           ADJUSTMENT / SELISIH
           ---------------------------------------------------- */
        WHEN 'ADJUSTMENT',
             'SELISIH' THEN

            v_coa :=
                COALESCE(
                    NULLIF(BTRIM(v_cfg->>'pselisih'), ''),
                    NULLIF(BTRIM(p_fallback_idcoa), '')
                );


        /* ----------------------------------------------------
           DEFAULT
           ---------------------------------------------------- */
        ELSE

            v_coa :=
                NULLIF(BTRIM(p_fallback_idcoa), '');

    END CASE;


    /* ========================================================
       NORMALISASI
       ======================================================== */
    v_coa :=
        NULLIF(
            BTRIM(
                COALESCE(v_coa, '')
            ),
            ''
        );


    IF v_coa IS NULL THEN
        RETURN NULL;
    END IF;


    /* ========================================================
       VALIDASI COA MASTER
       ======================================================== */
    IF NOT EXISTS
    (
        SELECT 1
        FROM sc_mst.coa c
        WHERE BTRIM(c.idcoa::TEXT)
              = BTRIM(v_coa)
    )
    THEN

        RAISE EXCEPTION
            'COA % tidak ditemukan. account_role=%, idbarang=%, currcode=%',
            v_coa,
            v_role,
            COALESCE(BTRIM(p_idbarang::TEXT), ''),
            COALESCE(BTRIM(p_currcode::TEXT), '');

    END IF;


    RETURN v_coa;

END;
$$;


/* ============================================================
   TEST STKINX
   ============================================================ */

SELECT
    j.seq,
    BTRIM(j.journal_type::TEXT) AS journal_type,
    j.account_role,
    j.debit_credit,
    j.value_source,
    BTRIM(j.idcoa::TEXT) AS mapping_idcoa,
    sc_trx.fn_resolve_accounting_coa(
        j.account_role,
        'BRG001',
        'IDR',
        j.idcoa
    ) AS resolved_idcoa
FROM sc_mst.journal_type_coa j
WHERE BTRIM(j.journal_type::TEXT) = 'STKINX'
  AND j.active = 'YES'
ORDER BY j.seq, j.id;


/* ============================================================
   TEST SALESX
   ============================================================ */

SELECT
    j.seq,
    BTRIM(j.journal_type::TEXT) AS journal_type,
    j.account_role,
    j.debit_credit,
    j.value_source,
    BTRIM(j.idcoa::TEXT) AS mapping_idcoa,
    sc_trx.fn_resolve_accounting_coa(
        j.account_role,
        'BRG001',
        'IDR',
        j.idcoa
    ) AS resolved_idcoa
FROM sc_mst.journal_type_coa j
WHERE BTRIM(j.journal_type::TEXT) = 'SALESX'
  AND j.active = 'YES'
ORDER BY j.seq, j.id;


/* ============================================================
   TEST ADJUSTMENT
   ============================================================ */

SELECT
    sc_trx.fn_resolve_accounting_coa(
        'ADJUSTMENT',
        'BRG001',
        'IDR',
        ''
    ) AS adjustment_coa;


/* ============================================================
   SELESAI
   ============================================================ */

COMMIT;

/* ============================================================
   PATCH TAHAP 16
   FIX AVG COST LOOKUP LEGACY STRUCTURE
   ============================================================

   Error:
       column ac.avg_cost does not exist

   Struktur aktual yang dipakai database:
       sc_trx.stkblc_avgcost
           idbarang
           idlocation
           batch
           avg_cost

   Perubahan:
       ac.avg_cost                 -> ac.avg_cost
       ac.stock_uniqueid          -> lookup berdasarkan
                                    idbarang + idlocation + batch

   Tidak mengubah logic posting accounting lainnya.
   ============================================================ */

BEGIN;

CREATE OR REPLACE FUNCTION sc_trx.fn_post_accounting_transaction(
    p_uniqueid TEXT
)
RETURNS BIGINT
LANGUAGE plpgsql
AS $$
DECLARE
    t              sc_trx.transaction_dt%ROWTYPE;
    m              RECORD;
    tx             RECORD;

    v_jurnal_id    BIGINT;
    v_existing_id      BIGINT;
    v_existing_status   VARCHAR(20);
    v_version           INTEGER;
    v_journal_uid  TEXT;

    v_value        NUMERIC(18,2);
    v_cost         NUMERIC(18,2);
    v_total_d      NUMERIC(18,2);
    v_total_k      NUMERIC(18,2);
    v_balance      NUMERIC(18,2);

    v_coa          VARCHAR(20);
    v_tax_count    INTEGER := 0;
    v_tax_handled  BOOLEAN := FALSE;
    v_source       TEXT;
BEGIN
    SELECT *
    INTO t
    FROM sc_trx.transaction_dt
    WHERE uniqueid = p_uniqueid;

    IF NOT FOUND THEN
        RAISE EXCEPTION 'Transaction % tidak ditemukan.', p_uniqueid;
    END IF;

    IF COALESCE(t.accounting_effect, 'YES') <> 'YES' THEN
        RETURN NULL;
    END IF;

    /* --------------------------------------------------------
       Mapping wajib tersedia untuk accounting transaction.
       -------------------------------------------------------- */
    SELECT COUNT(*)
    INTO v_tax_count
    FROM sc_mst.journal_type_coa j
    WHERE j.journal_type = t.journal_type
      AND j.active = 'YES';

    IF v_tax_count = 0 THEN
        RAISE EXCEPTION
            'Mapping accounting belum tersedia untuk journal_type=%',
            t.journal_type;
    END IF;

    /* --------------------------------------------------------
       Cari journal normal terakhir untuk transaction ini.
       Reversal journal dikecualikan.
       -------------------------------------------------------- */
    SELECT jh.id, jh.status
    INTO v_existing_id, v_existing_status
    FROM sc_trx.jurnal_hd jh
    WHERE jh.source_uniqueid = t.uniqueid
      AND jh.uniqueid NOT LIKE 'JRNL-REV-%'
    ORDER BY jh.id DESC
    LIMIT 1;

    /* POSTED normal journal sudah selesai. */
    IF v_existing_id IS NOT NULL AND v_existing_status = 'POSTED' THEN
        RETURN v_existing_id;
    END IF;

    IF v_existing_id IS NOT NULL AND v_existing_status = 'DRAFT' THEN
        v_jurnal_id := v_existing_id;

        DELETE FROM sc_trx.jurnal_dt
        WHERE jurnal_id = v_jurnal_id;

        UPDATE sc_trx.jurnal_hd
        SET
            docno = t.docno,
            doctype = t.doctype,
            journal_type = t.journal_type,
            trxdate = t.docdate,
            type_in_out = t.type_in_out,
            ref_docno = COALESCE(t.ref_docno, ''),
            ref_doctype = COALESCE(t.ref_doctype, ''),
            idbranch = COALESCE(t.idbranch, ''),
            cabang = COALESCE(t.cabang, ''),
            module = COALESCE(t.module, ''),
            accounting_effect = COALESCE(t.accounting_effect, 'YES'),
            currcode = COALESCE(t.currcode, ''),
            kurs = COALESCE(t.kurs, 1),
            keterangan = COALESCE(t.keterangan, ''),
            updatedby = t.updatedby,
            updateddate = CURRENT_TIMESTAMP,
            status = 'DRAFT'
        WHERE id = v_jurnal_id;
    ELSE
        SELECT COUNT(*) + 1
        INTO v_version
        FROM sc_trx.jurnal_hd
        WHERE source_uniqueid = t.uniqueid
          AND uniqueid NOT LIKE 'JRNL-REV-%';

        v_journal_uid :=
            sc_trx.fn_journal_uniqueid(
                t.docno,
                t.doctype,
                t.journal_type,
                t.idbranch,
                t.uniqueid || '|V' || v_version
            );

        INSERT INTO sc_trx.jurnal_hd
        (
            uniqueid,
            source_uniqueid,
            docno,
            doctype,
            journal_type,
            trxdate,
            type_in_out,
            ref_docno,
            ref_doctype,
            idbranch,
            cabang,
            module,
            accounting_effect,
            currcode,
            kurs,
            total_debet,
            total_kredit,
            balance,
            status,
            keterangan,
            createdby,
            createddate
        )
        VALUES
        (
            v_journal_uid,
            t.uniqueid,
            t.docno,
            t.doctype,
            t.journal_type,
            t.docdate,
            t.type_in_out,
            COALESCE(t.ref_docno, ''),
            COALESCE(t.ref_doctype, ''),
            COALESCE(t.idbranch, ''),
            COALESCE(t.cabang, ''),
            COALESCE(t.module, ''),
            COALESCE(t.accounting_effect, 'YES'),
            COALESCE(t.currcode, ''),
            COALESCE(t.kurs, 1),
            0,
            0,
            0,
            'DRAFT',
            COALESCE(t.keterangan, ''),
            t.createdby,
            COALESCE(t.createddate, CURRENT_TIMESTAMP)
        )
        RETURNING id
        INTO v_jurnal_id;
    END IF;

    /* --------------------------------------------------------
       COST transaksi.
       -------------------------------------------------------- */
    SELECT COALESCE(SUM(sb.totalcost), 0)
    INTO v_cost
    FROM sc_trx.stkblc sb
    WHERE sb.uniqueid = t.uniqueid;

    IF COALESCE(v_cost, 0) = 0
       AND NULLIF(BTRIM(COALESCE(t.ref_docno, '')), '') IS NOT NULL
    THEN
        SELECT COALESCE(SUM(sb.totalcost), 0)
        INTO v_cost
        FROM sc_trx.stkblc sb
        WHERE sb.docno = t.ref_docno
          AND sb.doctype = COALESCE(t.ref_doctype, '');
    END IF;

    IF COALESCE(v_cost, 0) = 0
       AND NULLIF(BTRIM(COALESCE(t.idbarang::TEXT, '')), '') IS NOT NULL
       AND NULLIF(BTRIM(COALESCE(t.warehouse::TEXT, '')), '') IS NOT NULL
    THEN
        SELECT ROUND(COALESCE(ac.avg_cost, 0) * COALESCE(t.qty, 0), 2)
        INTO v_cost
        FROM sc_trx.stkblc_avgcost ac
        WHERE BTRIM(ac.idbarang::TEXT)
                = BTRIM(COALESCE(t.idbarang::TEXT, ''))
          AND BTRIM(ac.idlocation::TEXT)
                = BTRIM(COALESCE(t.warehouse::TEXT, ''))
          AND BTRIM(COALESCE(ac.batch, ''))
                = BTRIM(COALESCE(t.batch::TEXT, ''))
        LIMIT 1;
    END IF;

    /* --------------------------------------------------------
       Generate detail sesuai mapping.
       -------------------------------------------------------- */
    FOR m IN
        SELECT
            j.id,
            j.seq,
            j.account_role,
            j.idcoa,
            j.debit_credit,
            j.value_source,
            j.description
        FROM sc_mst.journal_type_coa j
        WHERE j.journal_type = t.journal_type
          AND j.active = 'YES'
        ORDER BY j.seq, j.id
    LOOP
        /* ----------------------------------------------------
           TAX: resolve dari tax_dtl.
           Hanya baris TAX pertama yang memicu expansion.
           ---------------------------------------------------- */
        IF UPPER(TRIM(COALESCE(m.account_role, ''))) = 'TAX'
           AND UPPER(TRIM(COALESCE(m.value_source, ''))) = 'PAJAK'
           AND COALESCE(t.pajak, 0) > 0
           AND NOT v_tax_handled
        THEN
            SELECT COUNT(*)
            INTO v_tax_count
            FROM sc_mst.tax_dtl d
            JOIN sc_mst.journal_type jt
              ON jt.journal_type = t.journal_type
            WHERE TRIM(d.idtax) = TRIM(COALESCE(t.idtax, 'NON'))
              AND d.status = 'P'
              AND
              (
                  (UPPER(TRIM(jt.module)) = 'PURCHASE'
                   AND NULLIF(TRIM(d.prk_masukan), '') IS NOT NULL)
                  OR
                  (UPPER(TRIM(jt.module)) = 'SALES'
                   AND NULLIF(TRIM(d.prk_keluaran), '') IS NOT NULL)
                  OR
                  (UPPER(TRIM(t.journal_type)) = 'TAXINX'
                   AND NULLIF(TRIM(d.prk_masukan), '') IS NOT NULL)
                  OR
                  (UPPER(TRIM(t.journal_type)) = 'TAXOUT'
                   AND NULLIF(TRIM(d.prk_keluaran), '') IS NOT NULL)
              );

            IF v_tax_count > 0 THEN
                FOR tx IN
                    SELECT
                        d.idgrouptax,
                        d.percentation,
                        CASE
                            WHEN UPPER(TRIM(jt.module)) = 'PURCHASE'
                                THEN NULLIF(TRIM(d.prk_masukan), '')
                            WHEN UPPER(TRIM(jt.module)) = 'SALES'
                                THEN NULLIF(TRIM(d.prk_keluaran), '')
                            WHEN UPPER(TRIM(t.journal_type)) = 'TAXINX'
                                THEN NULLIF(TRIM(d.prk_masukan), '')
                            WHEN UPPER(TRIM(t.journal_type)) = 'TAXOUT'
                                THEN NULLIF(TRIM(d.prk_keluaran), '')
                            ELSE NULL
                        END AS idcoa_tax
                    FROM sc_mst.tax_dtl d
                    JOIN sc_mst.journal_type jt
                      ON jt.journal_type = t.journal_type
                    WHERE TRIM(d.idtax) = TRIM(COALESCE(t.idtax, 'NON'))
                      AND d.status = 'P'
                      AND
                      (
                          (UPPER(TRIM(jt.module)) = 'PURCHASE'
                           AND NULLIF(TRIM(d.prk_masukan), '') IS NOT NULL)
                          OR
                          (UPPER(TRIM(jt.module)) = 'SALES'
                           AND NULLIF(TRIM(d.prk_keluaran), '') IS NOT NULL)
                          OR
                          (UPPER(TRIM(t.journal_type)) = 'TAXINX'
                           AND NULLIF(TRIM(d.prk_masukan), '') IS NOT NULL)
                          OR
                          (UPPER(TRIM(t.journal_type)) = 'TAXOUT'
                           AND NULLIF(TRIM(d.prk_keluaran), '') IS NOT NULL)
                      )
                    ORDER BY d.id
                LOOP
                    IF v_tax_count = 1 THEN
                        v_value := ROUND(COALESCE(t.pajak, 0), 2);
                    ELSE
                        v_value := ROUND(
                            COALESCE(t.dpp, 0)
                            * COALESCE(tx.percentation, 0)
                            / 100,
                            2
                        );
                    END IF;

                    IF v_value > 0 THEN
                        INSERT INTO sc_trx.jurnal_dt
                        (
                            jurnal_id,
                            source_uniqueid,
                            source_line_no,
                            seq,
                            account_role,
                            value_source,
                            idcoa,
                            debet,
                            kredit,
                            ref_docno,
                            ref_doctype,
                            keterangan,
                            currcode,
                            kurs,
                            createdby,
                            createddate
                        )
                        VALUES
                        (
                            v_jurnal_id,
                            t.uniqueid,
                            t.line_no,
                            m.seq,
                            m.account_role,
                            m.value_source,
                            tx.idcoa_tax,
                            CASE WHEN m.debit_credit = 'D' THEN v_value ELSE 0 END,
                            CASE WHEN m.debit_credit = 'K' THEN v_value ELSE 0 END,
                            COALESCE(t.ref_docno, ''),
                            COALESCE(t.ref_doctype, ''),
                            COALESCE(m.description, t.keterangan, ''),
                            COALESCE(t.currcode, ''),
                            COALESCE(t.kurs, 1),
                            t.createdby,
                            CURRENT_TIMESTAMP
                        );
                    END IF;
                END LOOP;

                v_tax_handled := TRUE;
                CONTINUE;
            END IF;
        END IF;

        /* ----------------------------------------------------
           Nilai detail jurnal normal.
           ---------------------------------------------------- */
        v_source := UPPER(TRIM(COALESCE(m.value_source, '')));

        v_value :=
            CASE v_source
                WHEN 'NILAI' THEN COALESCE(t.nilai, 0)
                WHEN 'DPP'   THEN COALESCE(t.dpp, 0)
                WHEN 'PAJAK' THEN COALESCE(t.pajak, 0)
                WHEN 'TOTAL' THEN COALESCE(t.total, 0)
                WHEN 'QTY'   THEN COALESCE(t.qty, 0)
                WHEN 'COST'  THEN COALESCE(v_cost, 0)
                ELSE 0
            END;

        IF v_value <= 0 THEN
            CONTINUE;
        END IF;

        v_coa := sc_trx.fn_resolve_accounting_coa(
            m.account_role,
            t.idbarang,
            t.currcode,
            m.idcoa
        );

        IF NULLIF(BTRIM(COALESCE(v_coa, '')), '') IS NULL THEN
            RAISE EXCEPTION
                'COA tidak ter-resolve. journal_type=%, seq=%, role=%',
                t.journal_type,
                m.seq,
                m.account_role;
        END IF;

        INSERT INTO sc_trx.jurnal_dt
        (
            jurnal_id,
            source_uniqueid,
            source_line_no,
            seq,
            account_role,
            value_source,
            idcoa,
            debet,
            kredit,
            ref_docno,
            ref_doctype,
            keterangan,
            currcode,
            kurs,
            createdby,
            createddate
        )
        VALUES
        (
            v_jurnal_id,
            t.uniqueid,
            t.line_no,
            m.seq,
            m.account_role,
            m.value_source,
            v_coa,
            CASE WHEN m.debit_credit = 'D' THEN v_value ELSE 0 END,
            CASE WHEN m.debit_credit = 'K' THEN v_value ELSE 0 END,
            COALESCE(t.ref_docno, ''),
            COALESCE(t.ref_doctype, ''),
            COALESCE(m.description, t.keterangan, ''),
            COALESCE(t.currcode, ''),
            COALESCE(t.kurs, 1),
            t.createdby,
            CURRENT_TIMESTAMP
        );
    END LOOP;

    /* --------------------------------------------------------
       Recheck journal.
       -------------------------------------------------------- */
    SELECT
        COALESCE(SUM(debet), 0),
        COALESCE(SUM(kredit), 0)
    INTO v_total_d, v_total_k
    FROM sc_trx.jurnal_dt
    WHERE jurnal_id = v_jurnal_id;

    v_balance := ROUND(v_total_d - v_total_k, 2);

    IF v_total_d = 0 AND v_total_k = 0 THEN
        DELETE FROM sc_trx.jurnal_dt
        WHERE jurnal_id = v_jurnal_id;

        UPDATE sc_trx.jurnal_hd
        SET total_debet = 0,
            total_kredit = 0,
            balance = 0,
            status = 'CANCELLED',
            updateddate = CURRENT_TIMESTAMP
        WHERE id = v_jurnal_id;

        RAISE EXCEPTION
            'Journal type % belum menghasilkan nilai accounting untuk transaction %.',
            t.journal_type,
            t.uniqueid;
    END IF;

    IF ABS(v_balance) > 0.01 THEN
        RAISE EXCEPTION
            'Jurnal % tidak balance. Debet=%, Kredit=%, Balance=%',
            v_jurnal_id,
            v_total_d,
            v_total_k,
            v_balance;
    END IF;

    UPDATE sc_trx.jurnal_hd
    SET total_debet = v_total_d,
        total_kredit = v_total_k,
        balance = v_balance,
        status = 'POSTED',
        updateddate = CURRENT_TIMESTAMP
    WHERE id = v_jurnal_id;

    RETURN v_jurnal_id;
END;
$$;

/* ============================================================
   VALIDASI STRUKTUR KOLOM
   ============================================================ */

SELECT
    column_name,
    data_type
FROM information_schema.columns
WHERE table_schema = 'sc_trx'
  AND table_name = 'stkblc_avgcost'
  AND column_name IN
      ('idbarang', 'idlocation', 'batch', 'qty', 'total_value', 'avg_cost')
ORDER BY ordinal_position;

COMMIT;

/* ============================================================
   PATCH TAHAP 16
   DIAGNOSTIC DATA TOO LONG VARCHAR(20)
   ============================================================

   Tujuan:
       Menangkap detail schema/table/column saat terjadi
       ERROR 22001: value too long for type character varying(20).

   Perubahan avg cost yang sudah dilakukan:
       ac.avg_cost        -> ac.avg_cost
       ac.stock_uniqueid -> idbarang + idlocation + batch

   Struktur aktual yang dipakai database:
       sc_trx.stkblc_avgcost
           idbarang
           idlocation
           batch
           avg_cost

   Perubahan:
       ac.avg_cost                 -> ac.avg_cost
       ac.stock_uniqueid          -> lookup berdasarkan
                                    idbarang + idlocation + batch

   Tidak mengubah logic posting accounting lainnya.
   ============================================================ */

BEGIN;

CREATE OR REPLACE FUNCTION sc_trx.fn_post_accounting_transaction(
    p_uniqueid TEXT
)
RETURNS BIGINT
LANGUAGE plpgsql
AS $$
DECLARE
    t              sc_trx.transaction_dt%ROWTYPE;
    m              RECORD;
    tx             RECORD;

    v_jurnal_id    BIGINT;
    v_existing_id      BIGINT;
    v_existing_status   VARCHAR(20);
    v_version           INTEGER;
    v_journal_uid  TEXT;

    v_value        NUMERIC(18,2);
    v_cost         NUMERIC(18,2);
    v_total_d      NUMERIC(18,2);
    v_total_k      NUMERIC(18,2);
    v_balance      NUMERIC(18,2);

    v_coa          VARCHAR(20);
    v_tax_count    INTEGER := 0;
    v_tax_handled  BOOLEAN := FALSE;
    v_source       TEXT;

    v_err_state    TEXT;
    v_err_message  TEXT;
    v_err_detail   TEXT;
    v_err_hint     TEXT;
    v_err_column   TEXT;
    v_err_table    TEXT;
    v_err_schema   TEXT;
BEGIN
    SELECT *
    INTO t
    FROM sc_trx.transaction_dt
    WHERE uniqueid = p_uniqueid;

    IF NOT FOUND THEN
        RAISE EXCEPTION 'Transaction % tidak ditemukan.', p_uniqueid;
    END IF;

    IF COALESCE(t.accounting_effect, 'YES') <> 'YES' THEN
        RETURN NULL;
    END IF;

    /* --------------------------------------------------------
       Mapping wajib tersedia untuk accounting transaction.
       -------------------------------------------------------- */
    SELECT COUNT(*)
    INTO v_tax_count
    FROM sc_mst.journal_type_coa j
    WHERE j.journal_type = t.journal_type
      AND j.active = 'YES';

    IF v_tax_count = 0 THEN
        RAISE EXCEPTION
            'Mapping accounting belum tersedia untuk journal_type=%',
            t.journal_type;
    END IF;

    /* --------------------------------------------------------
       Cari journal normal terakhir untuk transaction ini.
       Reversal journal dikecualikan.
       -------------------------------------------------------- */
    SELECT jh.id, jh.status
    INTO v_existing_id, v_existing_status
    FROM sc_trx.jurnal_hd jh
    WHERE jh.source_uniqueid = t.uniqueid
      AND jh.uniqueid NOT LIKE 'JRNL-REV-%'
    ORDER BY jh.id DESC
    LIMIT 1;

    /* POSTED normal journal sudah selesai. */
    IF v_existing_id IS NOT NULL AND v_existing_status = 'POSTED' THEN
        RETURN v_existing_id;
    END IF;

    IF v_existing_id IS NOT NULL AND v_existing_status = 'DRAFT' THEN
        v_jurnal_id := v_existing_id;

        DELETE FROM sc_trx.jurnal_dt
        WHERE jurnal_id = v_jurnal_id;

        UPDATE sc_trx.jurnal_hd
        SET
            docno = t.docno,
            doctype = t.doctype,
            journal_type = t.journal_type,
            trxdate = t.docdate,
            type_in_out = t.type_in_out,
            ref_docno = COALESCE(t.ref_docno, ''),
            ref_doctype = COALESCE(t.ref_doctype, ''),
            idbranch = COALESCE(t.idbranch, ''),
            cabang = COALESCE(t.cabang, ''),
            module = COALESCE(t.module, ''),
            accounting_effect = COALESCE(t.accounting_effect, 'YES'),
            currcode = COALESCE(t.currcode, ''),
            kurs = COALESCE(t.kurs, 1),
            keterangan = COALESCE(t.keterangan, ''),
            updatedby = t.updatedby,
            updateddate = CURRENT_TIMESTAMP,
            status = 'DRAFT'
        WHERE id = v_jurnal_id;
    ELSE
        SELECT COUNT(*) + 1
        INTO v_version
        FROM sc_trx.jurnal_hd
        WHERE source_uniqueid = t.uniqueid
          AND uniqueid NOT LIKE 'JRNL-REV-%';

        v_journal_uid :=
            sc_trx.fn_journal_uniqueid(
                t.docno,
                t.doctype,
                t.journal_type,
                t.idbranch,
                t.uniqueid || '|V' || v_version
            );

        INSERT INTO sc_trx.jurnal_hd
        (
            uniqueid,
            source_uniqueid,
            docno,
            doctype,
            journal_type,
            trxdate,
            type_in_out,
            ref_docno,
            ref_doctype,
            idbranch,
            cabang,
            module,
            accounting_effect,
            currcode,
            kurs,
            total_debet,
            total_kredit,
            balance,
            status,
            keterangan,
            createdby,
            createddate
        )
        VALUES
        (
            v_journal_uid,
            t.uniqueid,
            t.docno,
            t.doctype,
            t.journal_type,
            t.docdate,
            t.type_in_out,
            COALESCE(t.ref_docno, ''),
            COALESCE(t.ref_doctype, ''),
            COALESCE(t.idbranch, ''),
            COALESCE(t.cabang, ''),
            COALESCE(t.module, ''),
            COALESCE(t.accounting_effect, 'YES'),
            COALESCE(t.currcode, ''),
            COALESCE(t.kurs, 1),
            0,
            0,
            0,
            'DRAFT',
            COALESCE(t.keterangan, ''),
            t.createdby,
            COALESCE(t.createddate, CURRENT_TIMESTAMP)
        )
        RETURNING id
        INTO v_jurnal_id;
    END IF;

    /* --------------------------------------------------------
       COST transaksi.
       -------------------------------------------------------- */
    SELECT COALESCE(SUM(sb.totalcost), 0)
    INTO v_cost
    FROM sc_trx.stkblc sb
    WHERE sb.uniqueid = t.uniqueid;

    IF COALESCE(v_cost, 0) = 0
       AND NULLIF(BTRIM(COALESCE(t.ref_docno, '')), '') IS NOT NULL
    THEN
        SELECT COALESCE(SUM(sb.totalcost), 0)
        INTO v_cost
        FROM sc_trx.stkblc sb
        WHERE sb.docno = t.ref_docno
          AND sb.doctype = COALESCE(t.ref_doctype, '');
    END IF;

    IF COALESCE(v_cost, 0) = 0
       AND NULLIF(BTRIM(COALESCE(t.idbarang::TEXT, '')), '') IS NOT NULL
       AND NULLIF(BTRIM(COALESCE(t.warehouse::TEXT, '')), '') IS NOT NULL
    THEN
        SELECT ROUND(COALESCE(ac.avg_cost, 0) * COALESCE(t.qty, 0), 2)
        INTO v_cost
        FROM sc_trx.stkblc_avgcost ac
        WHERE BTRIM(ac.idbarang::TEXT)
                = BTRIM(COALESCE(t.idbarang::TEXT, ''))
          AND BTRIM(ac.idlocation::TEXT)
                = BTRIM(COALESCE(t.warehouse::TEXT, ''))
          AND BTRIM(COALESCE(ac.batch, ''))
                = BTRIM(COALESCE(t.batch::TEXT, ''))
        LIMIT 1;
    END IF;

    /* --------------------------------------------------------
       Generate detail sesuai mapping.
       -------------------------------------------------------- */
    FOR m IN
        SELECT
            j.id,
            j.seq,
            j.account_role,
            j.idcoa,
            j.debit_credit,
            j.value_source,
            j.description
        FROM sc_mst.journal_type_coa j
        WHERE j.journal_type = t.journal_type
          AND j.active = 'YES'
        ORDER BY j.seq, j.id
    LOOP
        /* ----------------------------------------------------
           TAX: resolve dari tax_dtl.
           Hanya baris TAX pertama yang memicu expansion.
           ---------------------------------------------------- */
        IF UPPER(TRIM(COALESCE(m.account_role, ''))) = 'TAX'
           AND UPPER(TRIM(COALESCE(m.value_source, ''))) = 'PAJAK'
           AND COALESCE(t.pajak, 0) > 0
           AND NOT v_tax_handled
        THEN
            SELECT COUNT(*)
            INTO v_tax_count
            FROM sc_mst.tax_dtl d
            JOIN sc_mst.journal_type jt
              ON jt.journal_type = t.journal_type
            WHERE TRIM(d.idtax) = TRIM(COALESCE(t.idtax, 'NON'))
              AND d.status = 'P'
              AND
              (
                  (UPPER(TRIM(jt.module)) = 'PURCHASE'
                   AND NULLIF(TRIM(d.prk_masukan), '') IS NOT NULL)
                  OR
                  (UPPER(TRIM(jt.module)) = 'SALES'
                   AND NULLIF(TRIM(d.prk_keluaran), '') IS NOT NULL)
                  OR
                  (UPPER(TRIM(t.journal_type)) = 'TAXINX'
                   AND NULLIF(TRIM(d.prk_masukan), '') IS NOT NULL)
                  OR
                  (UPPER(TRIM(t.journal_type)) = 'TAXOUT'
                   AND NULLIF(TRIM(d.prk_keluaran), '') IS NOT NULL)
              );

            IF v_tax_count > 0 THEN
                FOR tx IN
                    SELECT
                        d.idgrouptax,
                        d.percentation,
                        CASE
                            WHEN UPPER(TRIM(jt.module)) = 'PURCHASE'
                                THEN NULLIF(TRIM(d.prk_masukan), '')
                            WHEN UPPER(TRIM(jt.module)) = 'SALES'
                                THEN NULLIF(TRIM(d.prk_keluaran), '')
                            WHEN UPPER(TRIM(t.journal_type)) = 'TAXINX'
                                THEN NULLIF(TRIM(d.prk_masukan), '')
                            WHEN UPPER(TRIM(t.journal_type)) = 'TAXOUT'
                                THEN NULLIF(TRIM(d.prk_keluaran), '')
                            ELSE NULL
                        END AS idcoa_tax
                    FROM sc_mst.tax_dtl d
                    JOIN sc_mst.journal_type jt
                      ON jt.journal_type = t.journal_type
                    WHERE TRIM(d.idtax) = TRIM(COALESCE(t.idtax, 'NON'))
                      AND d.status = 'P'
                      AND
                      (
                          (UPPER(TRIM(jt.module)) = 'PURCHASE'
                           AND NULLIF(TRIM(d.prk_masukan), '') IS NOT NULL)
                          OR
                          (UPPER(TRIM(jt.module)) = 'SALES'
                           AND NULLIF(TRIM(d.prk_keluaran), '') IS NOT NULL)
                          OR
                          (UPPER(TRIM(t.journal_type)) = 'TAXINX'
                           AND NULLIF(TRIM(d.prk_masukan), '') IS NOT NULL)
                          OR
                          (UPPER(TRIM(t.journal_type)) = 'TAXOUT'
                           AND NULLIF(TRIM(d.prk_keluaran), '') IS NOT NULL)
                      )
                    ORDER BY d.id
                LOOP
                    IF v_tax_count = 1 THEN
                        v_value := ROUND(COALESCE(t.pajak, 0), 2);
                    ELSE
                        v_value := ROUND(
                            COALESCE(t.dpp, 0)
                            * COALESCE(tx.percentation, 0)
                            / 100,
                            2
                        );
                    END IF;

                    IF v_value > 0 THEN
                        INSERT INTO sc_trx.jurnal_dt
                        (
                            jurnal_id,
                            source_uniqueid,
                            source_line_no,
                            seq,
                            account_role,
                            value_source,
                            idcoa,
                            debet,
                            kredit,
                            ref_docno,
                            ref_doctype,
                            keterangan,
                            currcode,
                            kurs,
                            createdby,
                            createddate
                        )
                        VALUES
                        (
                            v_jurnal_id,
                            t.uniqueid,
                            t.line_no,
                            m.seq,
                            m.account_role,
                            m.value_source,
                            tx.idcoa_tax,
                            CASE WHEN m.debit_credit = 'D' THEN v_value ELSE 0 END,
                            CASE WHEN m.debit_credit = 'K' THEN v_value ELSE 0 END,
                            COALESCE(t.ref_docno, ''),
                            COALESCE(t.ref_doctype, ''),
                            COALESCE(m.description, t.keterangan, ''),
                            COALESCE(t.currcode, ''),
                            COALESCE(t.kurs, 1),
                            t.createdby,
                            CURRENT_TIMESTAMP
                        );
                    END IF;
                END LOOP;

                v_tax_handled := TRUE;
                CONTINUE;
            END IF;
        END IF;

        /* ----------------------------------------------------
           Nilai detail jurnal normal.
           ---------------------------------------------------- */
        v_source := UPPER(TRIM(COALESCE(m.value_source, '')));

        v_value :=
            CASE v_source
                WHEN 'NILAI' THEN COALESCE(t.nilai, 0)
                WHEN 'DPP'   THEN COALESCE(t.dpp, 0)
                WHEN 'PAJAK' THEN COALESCE(t.pajak, 0)
                WHEN 'TOTAL' THEN COALESCE(t.total, 0)
                WHEN 'QTY'   THEN COALESCE(t.qty, 0)
                WHEN 'COST'  THEN COALESCE(v_cost, 0)
                ELSE 0
            END;

        IF v_value <= 0 THEN
            CONTINUE;
        END IF;

        v_coa := sc_trx.fn_resolve_accounting_coa(
            m.account_role,
            t.idbarang,
            t.currcode,
            m.idcoa
        );

        IF NULLIF(BTRIM(COALESCE(v_coa, '')), '') IS NULL THEN
            RAISE EXCEPTION
                'COA tidak ter-resolve. journal_type=%, seq=%, role=%',
                t.journal_type,
                m.seq,
                m.account_role;
        END IF;

        INSERT INTO sc_trx.jurnal_dt
        (
            jurnal_id,
            source_uniqueid,
            source_line_no,
            seq,
            account_role,
            value_source,
            idcoa,
            debet,
            kredit,
            ref_docno,
            ref_doctype,
            keterangan,
            currcode,
            kurs,
            createdby,
            createddate
        )
        VALUES
        (
            v_jurnal_id,
            t.uniqueid,
            t.line_no,
            m.seq,
            m.account_role,
            m.value_source,
            v_coa,
            CASE WHEN m.debit_credit = 'D' THEN v_value ELSE 0 END,
            CASE WHEN m.debit_credit = 'K' THEN v_value ELSE 0 END,
            COALESCE(t.ref_docno, ''),
            COALESCE(t.ref_doctype, ''),
            COALESCE(m.description, t.keterangan, ''),
            COALESCE(t.currcode, ''),
            COALESCE(t.kurs, 1),
            t.createdby,
            CURRENT_TIMESTAMP
        );
    END LOOP;

    /* --------------------------------------------------------
       Recheck journal.
       -------------------------------------------------------- */
    SELECT
        COALESCE(SUM(debet), 0),
        COALESCE(SUM(kredit), 0)
    INTO v_total_d, v_total_k
    FROM sc_trx.jurnal_dt
    WHERE jurnal_id = v_jurnal_id;

    v_balance := ROUND(v_total_d - v_total_k, 2);

    IF v_total_d = 0 AND v_total_k = 0 THEN
        DELETE FROM sc_trx.jurnal_dt
        WHERE jurnal_id = v_jurnal_id;

        UPDATE sc_trx.jurnal_hd
        SET total_debet = 0,
            total_kredit = 0,
            balance = 0,
            status = 'CANCELLED',
            updateddate = CURRENT_TIMESTAMP
        WHERE id = v_jurnal_id;

        RAISE EXCEPTION
            'Journal type % belum menghasilkan nilai accounting untuk transaction %.',
            t.journal_type,
            t.uniqueid;
    END IF;

    IF ABS(v_balance) > 0.01 THEN
        RAISE EXCEPTION
            'Jurnal % tidak balance. Debet=%, Kredit=%, Balance=%',
            v_jurnal_id,
            v_total_d,
            v_total_k,
            v_balance;
    END IF;

    UPDATE sc_trx.jurnal_hd
    SET total_debet = v_total_d,
        total_kredit = v_total_k,
        balance = v_balance,
        status = 'POSTED',
        updateddate = CURRENT_TIMESTAMP
    WHERE id = v_jurnal_id;

    RETURN v_jurnal_id;

EXCEPTION
    WHEN SQLSTATE '22001' THEN
        GET STACKED DIAGNOSTICS
            v_err_state   = RETURNED_SQLSTATE,
            v_err_message = MESSAGE_TEXT,
            v_err_detail  = PG_EXCEPTION_DETAIL,
            v_err_hint    = PG_EXCEPTION_HINT,
            v_err_column  = COLUMN_NAME,
            v_err_table   = TABLE_NAME,
            v_err_schema  = SCHEMA_NAME;

        RAISE EXCEPTION
            'DATA TERLALU PANJANG [22001]. schema=%, table=%, column=%, message=%, detail=%, hint=%',
            COALESCE(v_err_schema, ''),
            COALESCE(v_err_table, ''),
            COALESCE(v_err_column, ''),
            COALESCE(v_err_message, ''),
            COALESCE(v_err_detail, ''),
            COALESCE(v_err_hint, '');

    WHEN OTHERS THEN
        RAISE;
END;
$$;

/* ============================================================
   VALIDASI STRUKTUR KOLOM
   ============================================================ */

SELECT
    table_name,
    column_name,
    data_type,
    character_maximum_length
FROM information_schema.columns
WHERE table_schema = 'sc_trx'
  AND table_name IN ('jurnal_hd', 'jurnal_dt', 'stkblc', 'stkblc_avgcost')
  AND data_type = 'character varying'
  AND character_maximum_length = 20
ORDER BY table_name, ordinal_position;

COMMIT;


/* ============================================================
   JSYS ERP
   PATCH IDENTITY + DOCUMENT LENGTH
   ============================================================

   MASALAH:
       ERROR: value too long for type character varying(20)

   PENYEBAB YANG SANGAT MUNGKIN:
       fn_journal_uniqueid() menghasilkan:
           JRNL- + md5(...)
       panjang = 37 karakter.

       Jika jurnal_hd.uniqueid masih VARCHAR(20),
       INSERT jurnal akan gagal.

   RULE FINAL:
       1. Semua kolom identity/uniqueid di sc_trx
          menggunakan TEXT.
       2. Semua kolom docno di sc_trx
          menggunakan VARCHAR(20).
       3. Data docno existing > 20 TIDAK dipotong otomatis.
          Script akan berhenti dan menampilkan sumber data
          yang melanggar agar tidak ada data yang hilang.
       4. ref_docno TIDAK diubah pada patch ini.
   ============================================================ */

BEGIN;


/* ============================================================
   1. CEK DOCNO EXISTING > 20 KARAKTER
   ============================================================ */

DO $$
DECLARE
    r RECORD;
    v_sql TEXT;
    v_bad TEXT;
BEGIN

    FOR r IN
        SELECT
            c.table_schema,
            c.table_name
        FROM information_schema.columns c
        JOIN information_schema.tables t
          ON t.table_schema = c.table_schema
         AND t.table_name   = c.table_name
        WHERE c.table_schema = 'sc_trx'
          AND c.column_name = 'docno'
          AND t.table_type = 'BASE TABLE'
    LOOP

        v_sql :=
            format(
                'SELECT docno::TEXT
                   FROM %I.%I
                  WHERE char_length(docno::TEXT) > 20
                  LIMIT 1',
                r.table_schema,
                r.table_name
            );

        EXECUTE v_sql INTO v_bad;

        IF v_bad IS NOT NULL THEN
            RAISE EXCEPTION
                'DOCNO > 20 ditemukan pada %.%. Nilai contoh = "%" (panjang %). Tidak ada data yang dipotong otomatis.',
                r.table_schema,
                r.table_name,
                v_bad,
                char_length(v_bad);
        END IF;

    END LOOP;

END;
$$;


/* ============================================================
   2. SEMUA KOLOM IDENTITY -> TEXT
   ============================================================

   Kolom yang dinormalisasi:
       uniqueid
       source_uniqueid
       stock_uniqueid
       asset_uniqueid

   Hanya kolom yang memang sudah ada.
   ============================================================ */

DO $$
DECLARE
    r RECORD;
BEGIN

    FOR r IN
        SELECT
            c.table_schema,
            c.table_name,
            c.column_name,
            c.data_type
        FROM information_schema.columns c
        JOIN information_schema.tables t
          ON t.table_schema = c.table_schema
         AND t.table_name   = c.table_name
        WHERE c.table_schema = 'sc_trx'
          AND t.table_type = 'BASE TABLE'
          AND c.column_name IN
              (
                  'uniqueid',
                  'source_uniqueid',
                  'stock_uniqueid',
                  'asset_uniqueid'
              )
          AND c.data_type <> 'text'
        ORDER BY c.table_name, c.ordinal_position
    LOOP

        EXECUTE format(
            'ALTER TABLE %I.%I
                 ALTER COLUMN %I TYPE TEXT
                 USING %I::TEXT',
            r.table_schema,
            r.table_name,
            r.column_name,
            r.column_name
        );

    END LOOP;

END;
$$;


/* ============================================================
   3. SEMUA DOCNO -> VARCHAR(20)
   ============================================================ */

DO $$
DECLARE
    r RECORD;
BEGIN

    FOR r IN
        SELECT
            c.table_schema,
            c.table_name,
            c.column_name,
            c.data_type,
            c.character_maximum_length
        FROM information_schema.columns c
        JOIN information_schema.tables t
          ON t.table_schema = c.table_schema
         AND t.table_name   = c.table_name
        WHERE c.table_schema = 'sc_trx'
          AND t.table_type = 'BASE TABLE'
          AND c.column_name = 'docno'
          AND NOT (
              c.data_type = 'character varying'
              AND c.character_maximum_length = 20
          )
        ORDER BY c.table_name
    LOOP

        EXECUTE format(
            'ALTER TABLE %I.%I
                 ALTER COLUMN %I TYPE VARCHAR(20)
                 USING LEFT(%I::TEXT, 20)',
            r.table_schema,
            r.table_name,
            r.column_name,
            r.column_name
        );

    END LOOP;

END;
$$;


/* ============================================================
   4. VALIDASI FINAL IDENTITY
   ============================================================ */

SELECT
    table_schema,
    table_name,
    column_name,
    data_type,
    character_maximum_length
FROM information_schema.columns
WHERE table_schema = 'sc_trx'
  AND column_name IN
      (
          'uniqueid',
          'source_uniqueid',
          'stock_uniqueid',
          'asset_uniqueid'
      )
ORDER BY table_name, column_name;


/* ============================================================
   5. VALIDASI FINAL DOCNO
   ============================================================ */

SELECT
    table_schema,
    table_name,
    column_name,
    data_type,
    character_maximum_length
FROM information_schema.columns
WHERE table_schema = 'sc_trx'
  AND column_name = 'docno'
ORDER BY table_name;


/* ============================================================
   6. VALIDASI JOURNAL UNIQUEID
   ============================================================ */

SELECT
    sc_trx.fn_journal_uniqueid(
        'DOC001',
        'GRN',
        'GRNREC',
        'B01',
        'TX-000001'
    ) AS journal_uniqueid,
    char_length(
        sc_trx.fn_journal_uniqueid(
            'DOC001',
            'GRN',
            'GRNREC',
            'B01',
            'TX-000001'
        )
    ) AS panjang;


/* ============================================================
   HASIL YANG DIHARAPKAN:
       journal_uniqueid = JRNL-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
       panjang         = 37

   Karena uniqueid sekarang TEXT,
   nilai 37 karakter tersebut valid.
   ============================================================ */

COMMIT;

/* ============================================================
   JSYS ERP
   PATCH TAHAP 16
   FIX RESOLVER COA - VARIABLE SAFE
   ============================================================

   Error sebelumnya:
       "v_phpproduksi" is not a known variable

   Solusi:
       Tidak lagi mendeklarasikan puluhan variable COA barang.
       Master barang / currency / konfigurasi dibaca sebagai JSONB.

   Tambahan penting:
       ADJUSTMENT / SELISIH
           -> konfigurasi_umum.pselisih
           -> journal_type_coa.idcoa

   Tidak DROP table.
   ============================================================ */

BEGIN;


/* ============================================================
   REPLACE FUNCTION RESOLVER ACCOUNTING COA
   ============================================================ */

CREATE OR REPLACE FUNCTION sc_trx.fn_resolve_accounting_coa(
    p_account_role   TEXT,
    p_idbarang       CHAR(20),
    p_currcode       CHAR(3),
    p_fallback_idcoa VARCHAR(20)
)
RETURNS VARCHAR(20)
LANGUAGE plpgsql
AS $$
DECLARE
    v_role      TEXT := UPPER(TRIM(COALESCE(p_account_role, '')));
    v_barang    JSONB;
    v_currency  JSONB;
    v_cfg       JSONB;
    v_coa       TEXT;
BEGIN

    /* ========================================================
       MASTER BARANG
       ======================================================== */
    IF NULLIF(BTRIM(COALESCE(p_idbarang::TEXT, '')), '') IS NOT NULL THEN

        SELECT to_jsonb(b)
        INTO v_barang
        FROM sc_mst.mbarang b
        WHERE BTRIM(to_jsonb(b)->>'idbarang')
              = BTRIM(p_idbarang::TEXT)
        LIMIT 1;

    END IF;


    /* ========================================================
       MASTER CURRENCY
       ======================================================== */
    SELECT to_jsonb(c)
    INTO v_currency
    FROM sc_mst.currency c
    WHERE BTRIM(c.currcode::TEXT)
          = BTRIM(
              COALESCE(
                  NULLIF(p_currcode::TEXT, ''),
                  'IDR'
              )
          )
    LIMIT 1;


    /* ========================================================
       KONFIGURASI UMUM
       ======================================================== */
    SELECT to_jsonb(k)
    INTO v_cfg
    FROM sc_mst.konfigurasi_umum k
    LIMIT 1;


    /* ========================================================
       RESOLVE ROLE
       ======================================================== */
    CASE v_role

        /* ----------------------------------------------------
           INVENTORY
           BRG -> ppersediaan
           JSA -> pjasa
           ---------------------------------------------------- */
        WHEN 'STOCK', 'INVENTORY' THEN

            IF UPPER(
                COALESCE(v_barang->>'idgroup', '')
            ) = 'JSA'
            THEN

                v_coa :=
                    COALESCE(
                        NULLIF(BTRIM(v_barang->>'pjasa'), ''),
                        NULLIF(BTRIM(v_cfg->>'pjasa'), ''),
                        NULLIF(BTRIM(p_fallback_idcoa), '')
                    );

            ELSE

                v_coa :=
                    COALESCE(
                        NULLIF(BTRIM(v_barang->>'ppersediaan'), ''),
                        NULLIF(BTRIM(v_cfg->>'ppersediaan'), ''),
                        NULLIF(BTRIM(p_fallback_idcoa), '')
                    );

            END IF;


        /* ----------------------------------------------------
           SERVICE / JASA
           ---------------------------------------------------- */
        WHEN 'SERVICE', 'JASA', 'SERVICE_EXPENSE' THEN

            v_coa :=
                COALESCE(
                    NULLIF(BTRIM(v_barang->>'pjasa'), ''),
                    NULLIF(BTRIM(v_cfg->>'pjasa'), ''),
                    NULLIF(BTRIM(p_fallback_idcoa), '')
                );


        /* ----------------------------------------------------
           AP
           ---------------------------------------------------- */
        WHEN 'AP', 'HUTANG' THEN

            v_coa :=
                COALESCE(
                    NULLIF(BTRIM(v_currency->>'phutang'), ''),
                    NULLIF(BTRIM(v_cfg->>'phutang'), ''),
                    NULLIF(BTRIM(p_fallback_idcoa), '')
                );


        /* ----------------------------------------------------
           AR
           ---------------------------------------------------- */
        WHEN 'AR', 'PIUTANG' THEN

            v_coa :=
                COALESCE(
                    NULLIF(BTRIM(v_currency->>'ppiutang'), ''),
                    NULLIF(BTRIM(v_cfg->>'ppiutang'), ''),
                    NULLIF(BTRIM(p_fallback_idcoa), '')
                );


        /* ----------------------------------------------------
           SALES / INCOME
           ---------------------------------------------------- */
        WHEN 'SALES', 'INCOME', 'PENDAPATAN' THEN

            v_coa :=
                COALESCE(
                    NULLIF(BTRIM(v_currency->>'ppendapatan'), ''),
                    NULLIF(BTRIM(v_cfg->>'ppendapatan'), ''),
                    NULLIF(BTRIM(p_fallback_idcoa), '')
                );


        /* ----------------------------------------------------
           SALES RETURN
           ---------------------------------------------------- */
        WHEN 'SALES_RETURN',
             'RETURN_SALES',
             'RETUR_PENJUALAN' THEN

            v_coa :=
                COALESCE(
                    NULLIF(BTRIM(v_currency->>'pretur'), ''),
                    NULLIF(BTRIM(p_fallback_idcoa), '')
                );


        /* ----------------------------------------------------
           COGS / HPP
           ---------------------------------------------------- */
        WHEN 'COGS', 'HPP' THEN

            v_coa :=
                COALESCE(
                    NULLIF(BTRIM(v_barang->>'pcogs'), ''),
                    NULLIF(BTRIM(v_cfg->>'hpp'), ''),
                    NULLIF(BTRIM(p_fallback_idcoa), '')
                );


        /* ----------------------------------------------------
           WIP / HPP PRODUKSI
           ---------------------------------------------------- */
        WHEN 'PRODUCTION_HPP',
             'HPP_PRODUKSI',
             'WIP' THEN

            v_coa :=
                COALESCE(
                    NULLIF(BTRIM(v_barang->>'phpproduksi'), ''),
                    NULLIF(BTRIM(v_cfg->>'pproduksi'), ''),
                    NULLIF(BTRIM(p_fallback_idcoa), '')
                );


        /* ----------------------------------------------------
           DELIVERY
           ---------------------------------------------------- */
        WHEN 'PSJ',
             'DELIVERY',
             'SURAT_JALAN' THEN

            v_coa :=
                COALESCE(
                    NULLIF(BTRIM(v_barang->>'psj'), ''),
                    NULLIF(BTRIM(v_cfg->>'psj'), ''),
                    NULLIF(BTRIM(p_fallback_idcoa), '')
                );


        /* ----------------------------------------------------
           WASTE / SCRAP
           ---------------------------------------------------- */
        WHEN 'WASTE',
             'SCRAP' THEN

            /*
               Prioritas akun WASTE:
               1. mbarang.pwaste
               2. konfigurasi_umum.pwaste
               3. mbarang.pcogs
               4. konfigurasi_umum.hpp
               5. journal_type_coa.idcoa

               Jangan fallback ke ppersediaan.
               SCRAPP / WASTEX membutuhkan akun biaya/loss
               sebagai lawan kredit persediaan.
            */
            v_coa :=
                COALESCE(
                    NULLIF(BTRIM(v_barang->>'pwaste'), ''),
                    NULLIF(BTRIM(v_cfg->>'pwaste'), ''),
                    NULLIF(BTRIM(v_barang->>'pcogs'), ''),
                    NULLIF(BTRIM(v_cfg->>'hpp'), ''),
                    NULLIF(BTRIM(p_fallback_idcoa), '')
                );


        /* ----------------------------------------------------
           CASH
           ---------------------------------------------------- */
        WHEN 'CASH',
             'KAS',
             'TUNAI' THEN

            v_coa :=
                COALESCE(
                    NULLIF(BTRIM(v_currency->>'ptunai'), ''),
                    NULLIF(BTRIM(v_cfg->>'ptunai'), ''),
                    NULLIF(BTRIM(v_cfg->>'pkas'), ''),
                    NULLIF(BTRIM(p_fallback_idcoa), '')
                );


        /* ----------------------------------------------------
           UANG MUKA PEMBELIAN
           ---------------------------------------------------- */
        WHEN 'PUM',
             'DOWN_PAYMENT_PURCHASE',
             'UM_PEMBELIAN' THEN

            v_coa :=
                COALESCE(
                    NULLIF(BTRIM(v_currency->>'pum'), ''),
                    NULLIF(BTRIM(p_fallback_idcoa), '')
                );


        /* ----------------------------------------------------
           BONUS PEMBELIAN
           ---------------------------------------------------- */
        WHEN 'BONUS',
             'PURCHASE_BONUS' THEN

            v_coa :=
                COALESCE(
                    NULLIF(BTRIM(v_currency->>'pbonus'), ''),
                    NULLIF(BTRIM(p_fallback_idcoa), '')
                );


        /* ----------------------------------------------------
           HUTANG AC
           ---------------------------------------------------- */
        WHEN 'HUTANG_AC' THEN

            v_coa :=
                COALESCE(
                    NULLIF(BTRIM(v_currency->>'hutangac'), ''),
                    NULLIF(BTRIM(p_fallback_idcoa), '')
                );


        WHEN 'HUTANG_BIAYA1',
             'ACCRUED_EXPENSE' THEN

            v_coa :=
                COALESCE(
                    NULLIF(BTRIM(v_currency->>'hutangbiaya1'), ''),
                    NULLIF(BTRIM(p_fallback_idcoa), '')
                );


        WHEN 'HUTANG_BIAYA2' THEN

            v_coa :=
                COALESCE(
                    NULLIF(BTRIM(v_currency->>'hutangbiaya2'), ''),
                    NULLIF(BTRIM(p_fallback_idcoa), '')
                );


        /* ----------------------------------------------------
           UANG MUKA PENJUALAN
           ---------------------------------------------------- */
        WHEN 'PUMJUAL',
             'DOWN_PAYMENT_SALES',
             'UM_PENJUALAN' THEN

            v_coa :=
                COALESCE(
                    NULLIF(BTRIM(v_currency->>'pumjual'), ''),
                    NULLIF(BTRIM(p_fallback_idcoa), '')
                );


        /* ----------------------------------------------------
           DISCOUNT
           ---------------------------------------------------- */
        WHEN 'DISCOUNT',
             'DISC',
             'DISKON' THEN

            v_coa :=
                COALESCE(
                    NULLIF(BTRIM(v_currency->>'pdisc'), ''),
                    NULLIF(BTRIM(p_fallback_idcoa), '')
                );


        /* ----------------------------------------------------
           BONUS PENJUALAN
           ---------------------------------------------------- */
        WHEN 'BONUS_JUAL',
             'SALES_BONUS' THEN

            v_coa :=
                COALESCE(
                    NULLIF(BTRIM(v_currency->>'pbonusjual'), ''),
                    NULLIF(BTRIM(p_fallback_idcoa), '')
                );


        /* ----------------------------------------------------
           PIUTANG AC
           ---------------------------------------------------- */
        WHEN 'PIUTANG_AC' THEN

            v_coa :=
                COALESCE(
                    NULLIF(BTRIM(v_currency->>'piutangac'), ''),
                    NULLIF(BTRIM(p_fallback_idcoa), '')
                );


        /* ----------------------------------------------------
           PENDAPATAN AC
           ---------------------------------------------------- */
        WHEN 'PENDAPATAN_AC',
             'OTHER_INCOME' THEN

            v_coa :=
                COALESCE(
                    NULLIF(BTRIM(v_currency->>'pendapatanac'), ''),
                    NULLIF(BTRIM(p_fallback_idcoa), '')
                );


        /* ----------------------------------------------------
           PPS
           ---------------------------------------------------- */
        WHEN 'PPS' THEN

            v_coa :=
                COALESCE(
                    NULLIF(BTRIM(v_currency->>'pps'), ''),
                    NULLIF(BTRIM(p_fallback_idcoa), '')
                );


        /* ----------------------------------------------------
           ADJUSTMENT / SELISIH
           ---------------------------------------------------- */
        WHEN 'ADJUSTMENT',
             'SELISIH' THEN

            v_coa :=
                COALESCE(
                    NULLIF(BTRIM(v_cfg->>'pselisih'), ''),
                    NULLIF(BTRIM(p_fallback_idcoa), '')
                );


        /* ----------------------------------------------------
           DEFAULT
           ---------------------------------------------------- */
        ELSE

            v_coa :=
                NULLIF(BTRIM(p_fallback_idcoa), '');

    END CASE;


    /* ========================================================
       NORMALISASI
       ======================================================== */
    v_coa :=
        NULLIF(
            BTRIM(
                COALESCE(v_coa, '')
            ),
            ''
        );


    IF v_coa IS NULL THEN
        RETURN NULL;
    END IF;


    /* ========================================================
       VALIDASI COA MASTER
       ======================================================== */
    IF NOT EXISTS
    (
        SELECT 1
        FROM sc_mst.coa c
        WHERE BTRIM(c.idcoa::TEXT)
              = BTRIM(v_coa)
    )
    THEN

        RAISE EXCEPTION
            'COA % tidak ditemukan. account_role=%, idbarang=%, currcode=%',
            v_coa,
            v_role,
            COALESCE(BTRIM(p_idbarang::TEXT), ''),
            COALESCE(BTRIM(p_currcode::TEXT), '');

    END IF;


    RETURN v_coa;

END;
$$;


/* ============================================================
   VALIDASI SCRAPP WASTE
   ============================================================ */

SELECT
    TRIM(td.idbarang::TEXT) AS idbarang,
    TRIM(j.journal_type::TEXT) AS journal_type,
    j.seq,
    TRIM(j.account_role) AS account_role,
    TRIM(j.idcoa::TEXT) AS mapping_idcoa,
    sc_trx.fn_resolve_accounting_coa(
        j.account_role,
        td.idbarang,
        td.currcode,
        j.idcoa
    ) AS resolved_idcoa,
    TRIM(COALESCE(mb.pwaste::TEXT, '')) AS mbarang_pwaste,
    TRIM(COALESCE(mb.pcogs::TEXT, '')) AS mbarang_pcogs
FROM sc_trx.transaction_dt td
JOIN sc_mst.journal_type_coa j
  ON TRIM(j.journal_type::TEXT) = TRIM(td.journal_type::TEXT)
 AND j.active = 'YES'
LEFT JOIN sc_mst.mbarang mb
  ON BTRIM(mb.idbarang::TEXT) = BTRIM(td.idbarang::TEXT)
WHERE TRIM(td.journal_type::TEXT) IN ('SCRAPP', 'WASTEX', 'SCRREV')
ORDER BY td.createddate DESC, j.seq
LIMIT 30;


/* ============================================================
   TEST STKINX
   ============================================================ */

SELECT
    j.seq,
    BTRIM(j.journal_type::TEXT) AS journal_type,
    j.account_role,
    j.debit_credit,
    j.value_source,
    BTRIM(j.idcoa::TEXT) AS mapping_idcoa,
    sc_trx.fn_resolve_accounting_coa(
        j.account_role,
        'BRG001',
        'IDR',
        j.idcoa
    ) AS resolved_idcoa
FROM sc_mst.journal_type_coa j
WHERE BTRIM(j.journal_type::TEXT) = 'STKINX'
  AND j.active = 'YES'
ORDER BY j.seq, j.id;


/* ============================================================
   TEST SALESX
   ============================================================ */

SELECT
    j.seq,
    BTRIM(j.journal_type::TEXT) AS journal_type,
    j.account_role,
    j.debit_credit,
    j.value_source,
    BTRIM(j.idcoa::TEXT) AS mapping_idcoa,
    sc_trx.fn_resolve_accounting_coa(
        j.account_role,
        'BRG001',
        'IDR',
        j.idcoa
    ) AS resolved_idcoa
FROM sc_mst.journal_type_coa j
WHERE BTRIM(j.journal_type::TEXT) = 'SALESX'
  AND j.active = 'YES'
ORDER BY j.seq, j.id;


/* ============================================================
   TEST ADJUSTMENT
   ============================================================ */

SELECT
    sc_trx.fn_resolve_accounting_coa(
        'ADJUSTMENT',
        'BRG001',
        'IDR',
        ''
    ) AS adjustment_coa;


/* ============================================================
   SELESAI
   ============================================================ */

COMMIT;


/* ============================================================================
   TAHAP 17
   RECALCULATE transaction_hd

   transaction_hd tetap summary dari transaction_dt.
   Tidak ada posting stock/asset/accounting di function ini.
   ============================================================================ */

CREATE OR REPLACE FUNCTION sc_trx.fn_recalculate_transaction_hd(
    p_docno TEXT DEFAULT NULL
)
RETURNS VOID
LANGUAGE plpgsql
AS $$
BEGIN
    DELETE FROM sc_trx.transaction_hd h
    WHERE p_docno IS NULL
       OR h.docno = p_docno;

    INSERT INTO sc_trx.transaction_hd
    (
        docno,
        doctype,
        journal_type,
        type_in_out,
        docdate,
        idbranch,
        cabang,
        ref_docno,
        ref_doctype,
        module,
        direction,
        stock_effect,
        accounting_effect,
        asset_effect,
        total_qty,
        total_qty_in,
        total_qty_out,
        total_bruto,
        total_discount,
        total_nilai,
        total_dpp,
        total_pajak,
        total_total,
        total_debet,
        total_kredit,
        balance,
        total_line
    )
    SELECT
        t.docno,
        t.doctype,
        t.journal_type,
        t.type_in_out,
        MIN(t.docdate),
        t.idbranch,
        t.cabang,
        t.ref_docno,
        t.ref_doctype,
        MAX(COALESCE(t.module, '')),
        MAX(COALESCE(t.direction, '')),
        MAX(COALESCE(t.stock_effect, 'NONE')),
        MAX(COALESCE(t.accounting_effect, 'NO')),
        MAX(COALESCE(t.asset_effect, 'NONE')),
        COALESCE(SUM(t.qty), 0),
        COALESCE(SUM(CASE WHEN t.type_in_out = 'IN'  THEN COALESCE(t.qty, 0) ELSE 0 END), 0),
        COALESCE(SUM(CASE WHEN t.type_in_out = 'OUT' THEN COALESCE(t.qty, 0) ELSE 0 END), 0),
        COALESCE(SUM(t.bruto), 0),
        COALESCE(SUM(t.discount), 0),
        COALESCE(SUM(t.nilai), 0),
        COALESCE(SUM(t.dpp), 0),
        COALESCE(SUM(t.pajak), 0),
        COALESCE(SUM(t.total), 0),
        COALESCE(SUM(t.debet), 0),
        COALESCE(SUM(t.kredit), 0),
        ABS(ROUND(COALESCE(SUM(t.debet), 0) - COALESCE(SUM(t.kredit), 0), 2)),
        COUNT(*)
    FROM sc_trx.transaction_dt t
    WHERE p_docno IS NULL
       OR t.docno = p_docno
    GROUP BY
        t.docno,
        t.doctype,
        t.journal_type,
        t.type_in_out,
        t.idbranch,
        t.cabang,
        t.ref_docno,
        t.ref_doctype;
END;
$$;


/* ============================================================================
   TAHAP 18
   ACCOUNTING TRIGGER SAJA

   Penting:
   - Tidak memanggil posting stock.
   - Tidak memanggil posting asset.
   - Stock sudah diproses TAHAP 06.
   - Asset sudah diproses TAHAP 08.
   - Accounting diproses setelah trigger stock/asset agar COST dari TAHAP 07
     sudah tersedia.
   ============================================================================ */

CREATE OR REPLACE FUNCTION sc_trx.fn_transaction_accounting()
RETURNS TRIGGER
LANGUAGE plpgsql
AS $$
BEGIN
    IF TG_OP = 'INSERT' THEN
        PERFORM sc_trx.fn_post_accounting_transaction(NEW.uniqueid);
        PERFORM sc_trx.fn_recalculate_transaction_hd(NEW.docno);
        RETURN NEW;
    END IF;

    IF TG_OP = 'UPDATE' THEN
        PERFORM sc_trx.fn_reverse_accounting_transaction(OLD.uniqueid);

        PERFORM sc_trx.fn_post_accounting_transaction(NEW.uniqueid);

        PERFORM sc_trx.fn_recalculate_transaction_hd(OLD.docno);

        IF NEW.docno IS DISTINCT FROM OLD.docno THEN
            PERFORM sc_trx.fn_recalculate_transaction_hd(NEW.docno);
        END IF;

        RETURN NEW;
    END IF;

    IF TG_OP = 'DELETE' THEN
        PERFORM sc_trx.fn_reverse_accounting_transaction(OLD.uniqueid);
        PERFORM sc_trx.fn_recalculate_transaction_hd(OLD.docno);
        RETURN OLD;
    END IF;

    RETURN NULL;
END;
$$;


/*
   Nama trigger sengaja dibuat "zz" supaya alphabetically setelah:
       trg_transaction_stock
       trg_transaction_asset

   Dengan demikian COST dari TAHAP 07 sudah tersedia sebelum accounting.
*/

DROP TRIGGER IF EXISTS trg_zz_transaction_accounting
ON sc_trx.transaction_dt;

CREATE TRIGGER trg_zz_transaction_accounting
AFTER INSERT OR UPDATE OR DELETE
ON sc_trx.transaction_dt
FOR EACH ROW
EXECUTE FUNCTION sc_trx.fn_transaction_accounting();


/* ============================================================================
   CEK TRIGGER TRANSACTION_DT
   ============================================================================ */
SELECT
    tgname,
    tgenabled,
    pg_get_triggerdef(oid)
FROM pg_trigger
WHERE tgrelid = 'sc_trx.transaction_dt'::regclass
  AND NOT tgisinternal
ORDER BY tgname;


/* ============================================================================
   SELESAI

   Tidak ada TEST INSERT.
   Tidak ada REBUILD massal.
   Tidak ada UPDATE data legacy.
   Tidak ada DROP TABLE.

   Flow aktif:

       transaction_dt
            |
            +--> TAHAP 06 stock
            |       |
            |       +--> TAHAP 07 avgcost
            |
            +--> TAHAP 08 asset
            |
            +--> TAHAP 16 accounting
            |       |
            |       +--> jurnal_hd
            |       +--> jurnal_dt
            |
            +--> TAHAP 17 transaction_hd

   UPDATE:
       reverse accounting OLD
       -> post accounting NEW
       -> recalculate header

   DELETE:
       reverse accounting
       -> recalculate header
   ============================================================================ */
