/* ============================================================================
   JSYS ERP / ACCOUNTING
   TAHAP 11 - 19 : FINAL RECOMPILE REV11
   ============================================================================

   TAHAP 06  : transaction_dt -> stkblc
   TAHAP 07  : stkblc -> average cost
   TAHAP 08  : transaction_dt -> assetblc
   TAHAP 09  : jurnal_hd
   TAHAP 10  : jurnal_dt

   SCRIPT INI HANYA MENANGANI:
       TAHAP 15 : cancel accounting (status-based)
       TAHAP 16 : generate / post accounting
       TAHAP 17 : recalculate transaction_hd
       TAHAP 18 : trigger accounting
       TAHAP 19 : validation

   TAHAP 11 - 14 TIDAK DIBUAT ULANG.
   TAHAP 19 TIDAK MENGGANTI VALIDASI YANG SUDAH ADA DI TAHAP 04 / 10.

   ATURAN:
       - Tidak DROP TABLE.
       - Tidak update data transaksi legacy.
       - transaction_dt = source of truth.
       - stock tetap milik TAHAP 06.
       - average cost tetap milik TAHAP 07.
       - asset tetap milik TAHAP 08.
       - jurnal detail tetap milik TAHAP 10.
       - accounting COA operasional dari journal_type_coa + master resolver.
       - manual accounting memakai:
             transaction_dt.idcoa
             transaction_dt.counter_idcoa
             transaction_dt.debet_kredit
       - TAX COA dari sc_mst.tax_dtl.
       - COST memakai hasil TAHAP 07 / stkblc.
       - POSTED hanya bila jurnal balance.
       - tidak ada TEST INSERT / rebuild massal.

   ============================================================================ */

BEGIN;

/* ============================================================================
   0. DEPENDENCY CHECK
   ============================================================================ */

DO $$
BEGIN

    IF to_regclass('sc_trx.transaction_dt') IS NULL THEN
        RAISE EXCEPTION
            'TAHAP 04 belum tersedia: sc_trx.transaction_dt tidak ditemukan.';
    END IF;

    IF to_regclass('sc_trx.transaction_hd') IS NULL THEN
        RAISE EXCEPTION
            'TAHAP 05 belum tersedia: sc_trx.transaction_hd tidak ditemukan.';
    END IF;

    IF to_regclass('sc_trx.stkblc') IS NULL THEN
        RAISE EXCEPTION
            'TAHAP 06 belum tersedia: sc_trx.stkblc tidak ditemukan.';
    END IF;

    IF to_regclass('sc_trx.stkblc_avgcost') IS NULL THEN
        RAISE EXCEPTION
            'TAHAP 07 belum tersedia: sc_trx.stkblc_avgcost tidak ditemukan.';
    END IF;

    IF to_regclass('sc_trx.jurnal_hd') IS NULL THEN
        RAISE EXCEPTION
            'TAHAP 09 belum tersedia: sc_trx.jurnal_hd tidak ditemukan.';
    END IF;

    IF to_regclass('sc_trx.jurnal_dt') IS NULL THEN
        RAISE EXCEPTION
            'TAHAP 10 belum tersedia: sc_trx.jurnal_dt tidak ditemukan.';
    END IF;

    IF NOT EXISTS (
        SELECT 1
        FROM information_schema.columns
        WHERE table_schema = 'sc_trx'
          AND table_name = 'jurnal_dt'
          AND column_name = 'journal_type'
    ) THEN
        RAISE EXCEPTION
            'TAHAP 10 belum patched: sc_trx.jurnal_dt.journal_type tidak ditemukan.';
    END IF;

    IF NOT EXISTS (
        SELECT 1
        FROM information_schema.columns
        WHERE table_schema = 'sc_trx'
          AND table_name = 'jurnal_dt'
          AND column_name = 'status'
    ) THEN
        RAISE EXCEPTION
            'TAHAP 10 belum patched: sc_trx.jurnal_dt.status tidak ditemukan.';
    END IF;

    IF to_regclass('sc_mst.journal_type') IS NULL THEN
        RAISE EXCEPTION
            'sc_mst.journal_type tidak ditemukan.';
    END IF;

    IF to_regclass('sc_mst.journal_type_coa') IS NULL THEN
        RAISE EXCEPTION
            'sc_mst.journal_type_coa tidak ditemukan.';
    END IF;

    IF to_regclass('sc_mst.coa') IS NULL THEN
        RAISE EXCEPTION
            'sc_mst.coa tidak ditemukan.';
    END IF;

    IF to_regclass('sc_mst.tax_dtl') IS NULL THEN
        RAISE EXCEPTION
            'sc_mst.tax_dtl tidak ditemukan.';
    END IF;

    IF to_regprocedure(
        'sc_trx.fn_journal_uniqueid(text,text,text,text,text)'
    ) IS NULL THEN
        RAISE EXCEPTION
            'sc_trx.fn_journal_uniqueid(TEXT x 5) tidak ditemukan. Jalankan TAHAP 09.';
    END IF;

END;
$$;


/* ============================================================================
   TAHAP 15
   CANCEL ACCOUNTING SAAT TRANSACTION DIHAPUS / DIGANTI
   ============================================================================

   ATURAN FINAL:
       DRAFT  -> CANCELLED
       POSTED -> CANCELLED

   Tidak membuat jurnal reversal otomatis dan tidak membuat JVREVS.
   Tidak membalik debit / kredit.
   jurnal_hd dan jurnal_dt tetap disimpan sebagai audit trail.

   GL aktif hanya membaca status POSTED.

   STOCK dan ASSET TIDAK diubah di tahap ini.
   ============================================================================ */

CREATE OR REPLACE FUNCTION sc_trx.fn_cancel_accounting_transaction(
    p_transaction_uniqueid TEXT
)
RETURNS VOID
LANGUAGE plpgsql
AS $$
DECLARE
    r RECORD;
BEGIN
    IF NULLIF(BTRIM(p_transaction_uniqueid), '') IS NULL THEN
        RETURN;
    END IF;

    FOR r IN
        SELECT jh.id
        FROM sc_trx.jurnal_hd jh
        WHERE jh.source_uniqueid = p_transaction_uniqueid
          AND jh.uniqueid NOT LIKE 'JRNL-REV-%'
          AND jh.status IN ('DRAFT','POSTED')
        ORDER BY jh.id
    LOOP
        UPDATE sc_trx.jurnal_hd
        SET
            status = 'CANCELLED',
            updatedby = 'SYSTEM',
            updateddate = CURRENT_TIMESTAMP
        WHERE id = r.id;
    END LOOP;
END;
$$;


/* Compatibility wrapper. */
CREATE OR REPLACE FUNCTION sc_trx.fn_reverse_accounting_transaction(
    p_transaction_uniqueid TEXT
)
RETURNS VOID
LANGUAGE plpgsql
AS $$
BEGIN
    PERFORM sc_trx.fn_cancel_accounting_transaction(
        p_transaction_uniqueid
    );
END;
$$;


/* ============================================================================
   TAHAP 16
   HELPER : RESOLVE ACCOUNTING COA
   ============================================================================

   Priority:
       1. mbarang / currency
       2. konfigurasi_umum
       3. journal_type_coa.idcoa

   Manual accounting:
       COA ACCOUNT          -> transaction_dt.idcoa
       COA COUNTER_ACCOUNT  -> transaction_dt.counter_idcoa

   WASTE / SCRAP:
       pwaste -> cfg.pwaste -> pcogs -> cfg.hpp -> mapping fallback
       tidak fallback ke ppersediaan.
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
    v_role      TEXT := UPPER(TRIM(COALESCE(p_account_role, '')));
    v_barang    JSONB;
    v_currency  JSONB;
    v_cfg       JSONB;
    v_coa       TEXT;
BEGIN

    /* MASTER BARANG */
    IF NULLIF(BTRIM(COALESCE(p_idbarang::TEXT, '')), '') IS NOT NULL THEN

        SELECT to_jsonb(b)
        INTO v_barang
        FROM sc_mst.mbarang b
        WHERE BTRIM(to_jsonb(b)->>'idbarang')
              = BTRIM(p_idbarang::TEXT)
        LIMIT 1;

    END IF;


    /* MASTER CURRENCY */
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


    /* KONFIGURASI UMUM */
    SELECT to_jsonb(k)
    INTO v_cfg
    FROM sc_mst.konfigurasi_umum k
    LIMIT 1;


    /* RESOLVE ROLE */
    CASE v_role

        WHEN 'STOCK', 'INVENTORY' THEN

            IF UPPER(COALESCE(v_barang->>'idgroup', '')) = 'JSA' THEN

                v_coa := COALESCE(
                    NULLIF(BTRIM(v_barang->>'pjasa'), ''),
                    NULLIF(BTRIM(v_cfg->>'pjasa'), ''),
                    NULLIF(BTRIM(p_fallback_idcoa), '')
                );

            ELSE

                v_coa := COALESCE(
                    NULLIF(BTRIM(v_barang->>'ppersediaan'), ''),
                    NULLIF(BTRIM(v_cfg->>'ppersediaan'), ''),
                    NULLIF(BTRIM(p_fallback_idcoa), '')
                );

            END IF;


        WHEN 'SERVICE', 'JASA', 'SERVICE_EXPENSE' THEN

            v_coa := COALESCE(
                NULLIF(BTRIM(v_barang->>'pjasa'), ''),
                NULLIF(BTRIM(v_cfg->>'pjasa'), ''),
                NULLIF(BTRIM(p_fallback_idcoa), '')
            );


        WHEN 'AP', 'HUTANG' THEN

            v_coa := COALESCE(
                NULLIF(BTRIM(v_currency->>'phutang'), ''),
                NULLIF(BTRIM(v_cfg->>'phutang'), ''),
                NULLIF(BTRIM(p_fallback_idcoa), '')
            );


        WHEN 'AR', 'PIUTANG' THEN

            v_coa := COALESCE(
                NULLIF(BTRIM(v_currency->>'ppiutang'), ''),
                NULLIF(BTRIM(v_cfg->>'ppiutang'), ''),
                NULLIF(BTRIM(p_fallback_idcoa), '')
            );


        WHEN 'SALES', 'INCOME', 'PENDAPATAN' THEN

            v_coa := COALESCE(
                NULLIF(BTRIM(v_currency->>'ppendapatan'), ''),
                NULLIF(BTRIM(v_cfg->>'ppendapatan'), ''),
                NULLIF(BTRIM(p_fallback_idcoa), '')
            );


        WHEN 'SALES_RETURN',
             'RETURN_SALES',
             'RETUR_PENJUALAN' THEN

            v_coa := COALESCE(
                NULLIF(BTRIM(v_currency->>'pretur'), ''),
                NULLIF(BTRIM(p_fallback_idcoa), '')
            );


        WHEN 'COGS', 'HPP' THEN

            v_coa := COALESCE(
                NULLIF(BTRIM(v_barang->>'pcogs'), ''),
                NULLIF(BTRIM(v_cfg->>'hpp'), ''),
                NULLIF(BTRIM(p_fallback_idcoa), '')
            );


        WHEN 'PRODUCTION_HPP',
             'HPP_PRODUKSI',
             'WIP' THEN

            v_coa := COALESCE(
                NULLIF(BTRIM(v_barang->>'phpproduksi'), ''),
                NULLIF(BTRIM(v_cfg->>'pproduksi'), ''),
                NULLIF(BTRIM(p_fallback_idcoa), '')
            );


        WHEN 'PSJ',
             'DELIVERY',
             'SURAT_JALAN' THEN

            v_coa := COALESCE(
                NULLIF(BTRIM(v_barang->>'psj'), ''),
                NULLIF(BTRIM(v_cfg->>'psj'), ''),
                NULLIF(BTRIM(p_fallback_idcoa), '')
            );


        WHEN 'WASTE',
             'SCRAP' THEN

            v_coa := COALESCE(
                NULLIF(BTRIM(v_barang->>'pwaste'), ''),
                NULLIF(BTRIM(v_cfg->>'pwaste'), ''),
                NULLIF(BTRIM(v_barang->>'pcogs'), ''),
                NULLIF(BTRIM(v_cfg->>'hpp'), ''),
                NULLIF(BTRIM(p_fallback_idcoa), '')
            );


        WHEN 'CASH',
             'KAS',
             'TUNAI' THEN

            v_coa := COALESCE(
                NULLIF(BTRIM(v_currency->>'ptunai'), ''),
                NULLIF(BTRIM(v_cfg->>'ptunai'), ''),
                NULLIF(BTRIM(v_cfg->>'pkas'), ''),
                NULLIF(BTRIM(p_fallback_idcoa), '')
            );


        WHEN 'PUM',
             'DOWN_PAYMENT_PURCHASE',
             'UM_PEMBELIAN' THEN

            v_coa := COALESCE(
                NULLIF(BTRIM(v_currency->>'pum'), ''),
                NULLIF(BTRIM(p_fallback_idcoa), '')
            );


        WHEN 'BONUS',
             'PURCHASE_BONUS' THEN

            v_coa := COALESCE(
                NULLIF(BTRIM(v_currency->>'pbonus'), ''),
                NULLIF(BTRIM(p_fallback_idcoa), '')
            );


        WHEN 'HUTANG_AC' THEN

            v_coa := COALESCE(
                NULLIF(BTRIM(v_currency->>'hutangac'), ''),
                NULLIF(BTRIM(p_fallback_idcoa), '')
            );


        WHEN 'HUTANG_BIAYA1',
             'ACCRUED_EXPENSE' THEN

            v_coa := COALESCE(
                NULLIF(BTRIM(v_currency->>'hutangbiaya1'), ''),
                NULLIF(BTRIM(p_fallback_idcoa), '')
            );


        WHEN 'HUTANG_BIAYA2' THEN

            v_coa := COALESCE(
                NULLIF(BTRIM(v_currency->>'hutangbiaya2'), ''),
                NULLIF(BTRIM(p_fallback_idcoa), '')
            );


        WHEN 'PUMJUAL',
             'DOWN_PAYMENT_SALES',
             'UM_PENJUALAN' THEN

            v_coa := COALESCE(
                NULLIF(BTRIM(v_currency->>'pumjual'), ''),
                NULLIF(BTRIM(p_fallback_idcoa), '')
            );


        WHEN 'DISCOUNT',
             'DISC',
             'DISKON' THEN

            v_coa := COALESCE(
                NULLIF(BTRIM(v_currency->>'pdisc'), ''),
                NULLIF(BTRIM(p_fallback_idcoa), '')
            );


        WHEN 'BONUS_JUAL',
             'SALES_BONUS' THEN

            v_coa := COALESCE(
                NULLIF(BTRIM(v_currency->>'pbonusjual'), ''),
                NULLIF(BTRIM(p_fallback_idcoa), '')
            );


        WHEN 'PIUTANG_AC' THEN

            v_coa := COALESCE(
                NULLIF(BTRIM(v_currency->>'piutangac'), ''),
                NULLIF(BTRIM(p_fallback_idcoa), '')
            );


        WHEN 'PENDAPATAN_AC',
             'OTHER_INCOME' THEN

            v_coa := COALESCE(
                NULLIF(BTRIM(v_currency->>'pendapatanac'), ''),
                NULLIF(BTRIM(p_fallback_idcoa), '')
            );


        WHEN 'PPS' THEN

            v_coa := COALESCE(
                NULLIF(BTRIM(v_currency->>'pps'), ''),
                NULLIF(BTRIM(p_fallback_idcoa), '')
            );


        WHEN 'ADJUSTMENT',
             'SELISIH' THEN

            v_coa := COALESCE(
                NULLIF(BTRIM(v_cfg->>'pselisih'), ''),
                NULLIF(BTRIM(p_fallback_idcoa), '')
            );


        ELSE

            v_coa := NULLIF(
                BTRIM(p_fallback_idcoa),
                ''
            );

    END CASE;


    v_coa := NULLIF(
        BTRIM(COALESCE(v_coa, '')),
        ''
    );


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
   ============================================================================

   Operational:
       transaction_dt
           -> journal_type_coa
           -> resolver COA
           -> jurnal_hd
           -> jurnal_dt

   Manual:
       ACCOUNT          -> transaction_dt.idcoa
       COUNTER_ACCOUNT  -> transaction_dt.counter_idcoa
       TAX              -> sc_mst.tax_dtl

   COST:
       1. stkblc.uniqueid
       2. stkblc.docno/ref_docno
       3. stkblc_avgcost.idbarang + idlocation + batch

   Tidak bergantung pada transaction_dt.stock_uniqueid.
   ============================================================================ */

/* ============================================================================
   TAHAP 16
   JVGENL DOCUMENT-LEVEL ACCOUNTING
   ============================================================================

   JVGENL / JURNAL UMUM:
       - satu docno dapat memiliki banyak line perkiraan
       - setiap line transaction_dt = satu jurnal_dt
       - semua line dalam docno yang sama = satu jurnal_hd
       - idcoa + debet_kredit mengikuti input setiap line
       - counter_idcoa tidak diwajibkan per line JVGENL

   REF:
       jurnal_dt.ref_docno   = transaction_dt.docno
       jurnal_dt.ref_doctype = transaction_dt.doctype
   ============================================================================ */


/* ============================================================================
   CANCEL JVGENL DOCUMENT
   ============================================================================

   DELETE / UPDATE source JVGENL:
       jurnal_hd = CANCELLED
       jurnal_dt = mengikuti status header

   Tidak membuat journal JVREVS.
   ============================================================================ */

CREATE OR REPLACE FUNCTION sc_trx.fn_cancel_jvgenl_document(
    p_docno     TEXT,
    p_doctype   TEXT,
    p_idbranch  TEXT
)
RETURNS VOID
LANGUAGE plpgsql
AS $$
DECLARE
    r RECORD;
BEGIN
    IF NULLIF(BTRIM(p_docno), '') IS NULL THEN
        RETURN;
    END IF;

    FOR r IN
        SELECT jh.id
        FROM sc_trx.jurnal_hd jh
        WHERE BTRIM(jh.docno::TEXT) = BTRIM(p_docno)
          AND BTRIM(jh.doctype::TEXT) = BTRIM(COALESCE(p_doctype, ''))
          AND BTRIM(jh.journal_type::TEXT) = 'JVGENL'
          AND BTRIM(jh.idbranch::TEXT) = BTRIM(COALESCE(p_idbranch, ''))
          AND jh.uniqueid NOT LIKE 'JRNL-REV-%'
          AND jh.status IN ('DRAFT','POSTED')
        ORDER BY jh.id
    LOOP
        UPDATE sc_trx.jurnal_hd
        SET
            status = 'CANCELLED',
            updatedby = 'SYSTEM',
            updateddate = CURRENT_TIMESTAMP
        WHERE id = r.id;
    END LOOP;
END;
$$;


/* Compatibility wrapper. */
CREATE OR REPLACE FUNCTION sc_trx.fn_reverse_jvgenl_document(
    p_docno     TEXT,
    p_doctype   TEXT,
    p_idbranch  TEXT
)
RETURNS VOID
LANGUAGE plpgsql
AS $$
BEGIN
    PERFORM sc_trx.fn_cancel_jvgenl_document(
        p_docno,
        p_doctype,
        p_idbranch
    );
END;
$$;


/* ============================================================================
   POST / REBUILD JVGENL DOCUMENT
   ============================================================================ */

CREATE OR REPLACE FUNCTION sc_trx.fn_post_jvgenl_document(
    p_docno     TEXT,
    p_doctype   TEXT,
    p_idbranch  TEXT
)
RETURNS BIGINT
LANGUAGE plpgsql
AS $$
DECLARE
    r               RECORD;
    v_jurnal_id     BIGINT;
    v_journal_uid   TEXT;
    v_source_uid    TEXT;
    v_version       INTEGER;
    v_seq           INTEGER := 0;

    v_total_d       NUMERIC(18,2) := 0;
    v_total_k       NUMERIC(18,2) := 0;
    v_balance       NUMERIC(18,2) := 0;

    v_value         NUMERIC(18,2);
    v_coa           TEXT;
BEGIN

    IF NOT EXISTS
    (
        SELECT 1
        FROM sc_trx.transaction_dt td
        WHERE BTRIM(td.docno::TEXT) = BTRIM(p_docno)
          AND BTRIM(td.doctype::TEXT) = BTRIM(COALESCE(p_doctype, ''))
          AND BTRIM(td.journal_type::TEXT) = 'JVGENL'
          AND BTRIM(td.idbranch::TEXT) = BTRIM(COALESCE(p_idbranch, ''))
          AND COALESCE(td.accounting_effect, 'YES') = 'YES'
    )
    THEN
        RETURN NULL;
    END IF;


    /*
       JVGENL = document-level multi-line journal.
       Trigger masih AFTER EACH ROW, sehingga line pertama
       belum boleh diposting bila dokumen belum balance.

       POST hanya ketika:
           total debit = total credit
           dan keduanya > 0.
    */
    SELECT
        COALESCE(SUM(td.debet), 0),
        COALESCE(SUM(td.kredit), 0)
    INTO
        v_total_d,
        v_total_k
    FROM sc_trx.transaction_dt td
    WHERE BTRIM(td.docno::TEXT) = BTRIM(p_docno)
      AND BTRIM(td.doctype::TEXT) = BTRIM(COALESCE(p_doctype, ''))
      AND BTRIM(td.journal_type::TEXT) = 'JVGENL'
      AND BTRIM(td.idbranch::TEXT) = BTRIM(COALESCE(p_idbranch, ''))
      AND COALESCE(td.accounting_effect, 'YES') = 'YES';


    IF v_total_d <= 0
       OR v_total_k <= 0
       OR ABS(ROUND(v_total_d - v_total_k, 2)) > 0.01
    THEN
        RETURN NULL;
    END IF;


    PERFORM sc_trx.fn_reverse_jvgenl_document(
        p_docno,
        p_doctype,
        p_idbranch
    );


    v_source_uid :=
        md5(
            'JVGENL|'
            || BTRIM(COALESCE(p_docno, ''))
            || '|'
            || BTRIM(COALESCE(p_doctype, ''))
            || '|'
            || BTRIM(COALESCE(p_idbranch, ''))
        );


    SELECT COUNT(*) + 1
    INTO v_version
    FROM sc_trx.jurnal_hd jh
    WHERE jh.source_uniqueid = v_source_uid
      AND jh.uniqueid NOT LIKE 'JRNL-REV-%';


    v_journal_uid :=
        sc_trx.fn_journal_uniqueid(
            p_docno,
            p_doctype,
            'JVGENL',
            p_idbranch,
            v_source_uid || '|V' || v_version
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
    SELECT
        v_journal_uid,
        v_source_uid,
        MIN(td.docno),
        MIN(td.doctype),
        'JVGENL',
        MIN(td.docdate),
        'IN',
        COALESCE(
            MIN(NULLIF(BTRIM(COALESCE(td.ref_docno, '')), '')),
            ''
        ),
        COALESCE(
            MIN(NULLIF(BTRIM(COALESCE(td.ref_doctype, '')), '')),
            ''
        ),
        p_idbranch,
        MAX(COALESCE(td.cabang, '')),
        'ACCOUNTING',
        'YES',
        MAX(COALESCE(td.currcode, '')),
        MAX(COALESCE(td.kurs, 1)),
        0,
        0,
        0,
        'DRAFT',
        MAX(COALESCE(td.keterangan, '')),
        MAX(td.createdby),
        CURRENT_TIMESTAMP
    FROM sc_trx.transaction_dt td
    WHERE BTRIM(td.docno::TEXT) = BTRIM(p_docno)
      AND BTRIM(td.doctype::TEXT) = BTRIM(COALESCE(p_doctype, ''))
      AND BTRIM(td.journal_type::TEXT) = 'JVGENL'
      AND BTRIM(td.idbranch::TEXT) = BTRIM(COALESCE(p_idbranch, ''))
      AND COALESCE(td.accounting_effect, 'YES') = 'YES'
    RETURNING id INTO v_jurnal_id;


    FOR r IN
        SELECT td.*
        FROM sc_trx.transaction_dt td
        WHERE BTRIM(td.docno::TEXT) = BTRIM(p_docno)
          AND BTRIM(td.doctype::TEXT) = BTRIM(COALESCE(p_doctype, ''))
          AND BTRIM(td.journal_type::TEXT) = 'JVGENL'
          AND BTRIM(td.idbranch::TEXT) = BTRIM(COALESCE(p_idbranch, ''))
          AND COALESCE(td.accounting_effect, 'YES') = 'YES'
        ORDER BY td.line_no, td.id
    LOOP

        v_seq := v_seq + 1;

        v_coa :=
            NULLIF(
                BTRIM(COALESCE(r.idcoa::TEXT, '')),
                ''
            );

        IF v_coa IS NULL THEN
            RAISE EXCEPTION
                'JVGENL % line %: Perkiraan wajib diisi.',
                p_docno,
                r.line_no;
        END IF;


        IF NOT EXISTS
        (
            SELECT 1
            FROM sc_mst.coa c
            WHERE BTRIM(c.idcoa::TEXT) = v_coa
        )
        THEN
            RAISE EXCEPTION
                'JVGENL % line %: COA % tidak ditemukan.',
                p_docno,
                r.line_no,
                v_coa;
        END IF;


        IF UPPER(TRIM(COALESCE(r.debet_kredit, ''))) NOT IN ('D', 'K') THEN
            RAISE EXCEPTION
                'JVGENL % line %: debet_kredit harus D/K.',
                p_docno,
                r.line_no;
        END IF;


        v_value :=
            COALESCE(
                NULLIF(r.nilai, 0),
                NULLIF(r.total, 0),
                NULLIF(r.dpp, 0),
                0
            );


        IF v_value <= 0 THEN
            RAISE EXCEPTION
                'JVGENL % line %: nilai harus > 0.',
                p_docno,
                r.line_no;
        END IF;


                INSERT INTO sc_trx.jurnal_dt
        (
            jurnal_id,
            journal_type,
            status,
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
            'JVGENL'::CHAR(6),
            'DRAFT',
            r.uniqueid,
            r.line_no,
            v_seq,
            'ACCOUNT',
            'NILAI',
            v_coa,
            CASE
                WHEN UPPER(TRIM(r.debet_kredit)) = 'D'
                THEN v_value
                ELSE 0
            END,
            CASE
                WHEN UPPER(TRIM(r.debet_kredit)) = 'K'
                THEN v_value
                ELSE 0
            END,
            r.docno,
            r.doctype,
            COALESCE(r.keterangan, ''),
            COALESCE(r.currcode, ''),
            COALESCE(r.kurs, 1),
            r.createdby,
            CURRENT_TIMESTAMP
        );

    END LOOP;


    SELECT
        COALESCE(SUM(jd.debet), 0),
        COALESCE(SUM(jd.kredit), 0)
    INTO v_total_d, v_total_k
    FROM sc_trx.jurnal_dt jd
    WHERE jd.jurnal_id = v_jurnal_id;


    v_balance :=
        ROUND(v_total_d - v_total_k, 2);


    IF ABS(v_balance) > 0.01 THEN
        RAISE EXCEPTION
            'JVGENL % tidak balance. Debet=%, Kredit=%, Balance=%',
            p_docno,
            v_total_d,
            v_total_k,
            v_balance;
    END IF;


    UPDATE sc_trx.jurnal_hd
    SET
        total_debet = v_total_d,
        total_kredit = v_total_k,
        balance = v_balance,
        status = 'POSTED',
        updateddate = CURRENT_TIMESTAMP
    WHERE id = v_jurnal_id;


    RETURN v_jurnal_id;

END;
$$;

CREATE OR REPLACE FUNCTION sc_trx.fn_post_accounting_transaction(
    p_uniqueid TEXT
)
RETURNS BIGINT
LANGUAGE plpgsql
AS $$
DECLARE
    t                   sc_trx.transaction_dt%ROWTYPE;
    m                   RECORD;
    tx                  RECORD;

    v_jurnal_id         BIGINT;
    v_existing_id       BIGINT;
    v_existing_status   VARCHAR(20);
    v_version           INTEGER;
    v_journal_uid       TEXT;

    v_value             NUMERIC(18,2);
    v_cost              NUMERIC(18,2) := 0;
    v_total_d           NUMERIC(18,2);
    v_total_k           NUMERIC(18,2);
    v_balance           NUMERIC(18,2);

    v_coa               VARCHAR(20);
    v_tax_count         INTEGER := 0;
    v_tax_handled       BOOLEAN := FALSE;
    v_source            TEXT;

    v_is_manual         BOOLEAN := FALSE;
    v_effective_dc     CHAR(1);
    v_manual_toggle     BOOLEAN := FALSE;
BEGIN

    /* ------------------------------------------------------------
       SOURCE TRANSACTION
       ------------------------------------------------------------ */

    SELECT *
    INTO t
    FROM sc_trx.transaction_dt
    WHERE uniqueid = p_uniqueid;

    IF NOT FOUND THEN
        RAISE EXCEPTION
            'Transaction % tidak ditemukan.',
            p_uniqueid;
    END IF;


    /* ------------------------------------------------------------
       ROUTING
       ------------------------------------------------------------ */

    IF COALESCE(t.accounting_effect, 'YES') <> 'YES' THEN
        RETURN NULL;
    END IF;


    /*
       JVGENL diproses document-level.
       Guard ini juga membuat pemanggilan langsung terhadap
       fn_post_accounting_transaction(JVGENL line) aman.
    */
    IF TRIM(t.journal_type::TEXT) = 'JVGENL' THEN
        RETURN sc_trx.fn_post_jvgenl_document(
            t.docno::TEXT,
            t.doctype::TEXT,
            t.idbranch::TEXT
        );
    END IF;


    /* ------------------------------------------------------------
       MANUAL ACCOUNTING
       ------------------------------------------------------------ */

    v_is_manual :=
        TRIM(t.journal_type::TEXT) IN
        (
            'JVGENL',
            'UMTITP',
            'NDKAPD',
            'NDKAPK',
            'NDKARD',
            'NDKARK',
            'GIROIN',
            'GIROUT',
            'FXREAL',
            'FXUNRL',
            'ARWOFF',
            'APWOFF',
            'BADPRV',
            'BADREV',
            'UNEARN',
            'UNEREL',
            'PAYROL'
        );

    v_manual_toggle :=
        TRIM(t.journal_type::TEXT) IN
        (
            'JVGENL',
            'UMTITP',
            'FXREAL',
            'FXUNRL',
            'ARWOFF',
            'APWOFF',
            'BADPRV',
            'BADREV',
            'UNEARN',
            'UNEREL',
            'PAYROL'
        );


    /* ------------------------------------------------------------
       MAPPING WAJIB
       ------------------------------------------------------------ */

    SELECT COUNT(*)
    INTO v_tax_count
    FROM sc_mst.journal_type_coa j
    WHERE TRIM(j.journal_type::TEXT) = TRIM(t.journal_type::TEXT)
      AND j.active = 'YES';

    IF v_tax_count = 0 THEN
        RAISE EXCEPTION
            'Mapping accounting belum tersedia untuk journal_type=%',
            TRIM(t.journal_type::TEXT);
    END IF;


    /* ------------------------------------------------------------
       CARI JOURNAL NORMAL TERAKHIR
       ------------------------------------------------------------ */

    SELECT jh.id, jh.status
    INTO v_existing_id, v_existing_status
    FROM sc_trx.jurnal_hd jh
    WHERE jh.source_uniqueid = t.uniqueid
      AND jh.uniqueid NOT LIKE 'JRNL-REV-%'
    ORDER BY jh.id DESC
    LIMIT 1;


    IF v_existing_id IS NOT NULL
       AND v_existing_status = 'POSTED'
    THEN
        RETURN v_existing_id;
    END IF;


    /* ------------------------------------------------------------
       REUSE DRAFT
       ------------------------------------------------------------ */

    IF v_existing_id IS NOT NULL
       AND v_existing_status = 'DRAFT'
    THEN

        v_jurnal_id := v_existing_id;

        DELETE FROM sc_trx.jurnal_dt
        WHERE jurnal_id = v_jurnal_id;

        UPDATE sc_trx.jurnal_hd
        SET
            docno              = t.docno,
            doctype            = t.doctype,
            journal_type       = t.journal_type,
            trxdate            = t.docdate,
            type_in_out        = t.type_in_out,
            ref_docno          = COALESCE(t.ref_docno, ''),
            ref_doctype        = COALESCE(t.ref_doctype, ''),
            idbranch           = COALESCE(t.idbranch, ''),
            cabang             = COALESCE(t.cabang, ''),
            module             = COALESCE(t.module, ''),
            accounting_effect  = COALESCE(t.accounting_effect, 'YES'),
            currcode           = COALESCE(t.currcode, ''),
            kurs               = COALESCE(t.kurs, 1),
            keterangan         = COALESCE(t.keterangan, ''),
            updatedby          = t.updatedby,
            updateddate        = CURRENT_TIMESTAMP,
            status             = 'DRAFT'
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


    /* ------------------------------------------------------------
       COST
       ------------------------------------------------------------ */

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

        SELECT
            ROUND(
                COALESCE(ac.avg_cost, 0)
                * COALESCE(t.qty, 0),
                2
            )
        INTO v_cost
        FROM sc_trx.stkblc_avgcost ac
        WHERE BTRIM(ac.idbarang::TEXT)
                = BTRIM(COALESCE(t.idbarang::TEXT, ''))
          AND BTRIM(ac.idlocation::TEXT)
                = BTRIM(COALESCE(t.warehouse::TEXT, ''))
          AND BTRIM(COALESCE(ac.batch, ''))
                = BTRIM(COALESCE(t.batch::TEXT, ''))
        LIMIT 1;

        v_cost := COALESCE(v_cost, 0);

    END IF;


    /* ------------------------------------------------------------
       GENERATE DETAIL
       ------------------------------------------------------------ */

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
        WHERE TRIM(j.journal_type::TEXT)
              = TRIM(t.journal_type::TEXT)
          AND j.active = 'YES'
        ORDER BY j.seq, j.id
    LOOP

        /* --------------------------------------------------------
           TAX EXPANSION
           -------------------------------------------------------- */

        IF UPPER(TRIM(COALESCE(m.account_role, ''))) = 'TAX'
           AND UPPER(TRIM(COALESCE(m.value_source, ''))) = 'PAJAK'
           AND COALESCE(t.pajak, 0) > 0
           AND NOT v_tax_handled
        THEN

            /*
               TAX FLOW YANG DAPAT DITENTUKAN DARI CONTEXT:
                   PURCHASE -> INPUT
                   SALES    -> OUTPUT
                   TAXINX   -> INPUT
                   TAXOUT   -> OUTPUT
                   NDK supplier -> INPUT
                   NDK customer -> OUTPUT

               Journal type finance lain tidak ditebak ulang.
            */

            SELECT COUNT(*)
            INTO v_tax_count
            FROM sc_mst.tax_dtl d
            WHERE TRIM(d.idtax)
                    = TRIM(COALESCE(t.idtax, 'NON'))
              AND d.status = 'P'
              AND
              (
                  (
                      UPPER(TRIM(COALESCE(t.module, '')))
                      = 'PURCHASE'
                      AND NULLIF(TRIM(d.prk_masukan), '') IS NOT NULL
                  )
                  OR
                  (
                      UPPER(TRIM(COALESCE(t.module, '')))
                      = 'SALES'
                      AND NULLIF(TRIM(d.prk_keluaran), '') IS NOT NULL
                  )
                  OR
                  (
                      TRIM(t.journal_type::TEXT)
                      IN ('TAXINX','NDKAPD','NDKAPK')
                      AND NULLIF(TRIM(d.prk_masukan), '') IS NOT NULL
                  )
                  OR
                  (
                      TRIM(t.journal_type::TEXT)
                      IN ('TAXOUT','NDKARD','NDKARK')
                      AND NULLIF(TRIM(d.prk_keluaran), '') IS NOT NULL
                  )
              );


            IF v_tax_count > 0 THEN

                FOR tx IN
                    SELECT
                        d.percentation,

                        CASE
                            WHEN UPPER(TRIM(COALESCE(t.module, '')))
                                 = 'PURCHASE'
                                THEN NULLIF(TRIM(d.prk_masukan), '')

                            WHEN UPPER(TRIM(COALESCE(t.module, '')))
                                 = 'SALES'
                                THEN NULLIF(TRIM(d.prk_keluaran), '')

                            WHEN TRIM(t.journal_type::TEXT)
                                 IN ('TAXINX','NDKAPD','NDKAPK')
                                THEN NULLIF(TRIM(d.prk_masukan), '')

                            WHEN TRIM(t.journal_type::TEXT)
                                 IN ('TAXOUT','NDKARD','NDKARK')
                                THEN NULLIF(TRIM(d.prk_keluaran), '')

                            ELSE NULL
                        END AS idcoa_tax

                    FROM sc_mst.tax_dtl d
                    WHERE TRIM(d.idtax)
                            = TRIM(COALESCE(t.idtax, 'NON'))
                      AND d.status = 'P'
                      AND
                      (
                          (
                              UPPER(TRIM(COALESCE(t.module, '')))
                              = 'PURCHASE'
                              AND NULLIF(TRIM(d.prk_masukan), '') IS NOT NULL
                          )
                          OR
                          (
                              UPPER(TRIM(COALESCE(t.module, '')))
                              = 'SALES'
                              AND NULLIF(TRIM(d.prk_keluaran), '') IS NOT NULL
                          )
                          OR
                          (
                              TRIM(t.journal_type::TEXT)
                              IN ('TAXINX','NDKAPD','NDKAPK')
                              AND NULLIF(TRIM(d.prk_masukan), '') IS NOT NULL
                          )
                          OR
                          (
                              TRIM(t.journal_type::TEXT)
                              IN ('TAXOUT','NDKARD','NDKARK')
                              AND NULLIF(TRIM(d.prk_keluaran), '') IS NOT NULL
                          )
                      )
                    ORDER BY d.id
                LOOP

                    IF v_tax_count = 1 THEN

                        v_value :=
                            ROUND(
                                COALESCE(t.pajak, 0),
                                2
                            );

                    ELSE

                        v_value :=
                            ROUND(
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
            journal_type,
            status,
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
                            t.journal_type,
                            'DRAFT',
                            t.uniqueid,
                            t.line_no,
                            m.seq,
                            m.account_role,
                            m.value_source,
                            tx.idcoa_tax,
                            CASE
                                WHEN (
                                    v_manual_toggle
                                    AND UPPER(TRIM(COALESCE(t.debet_kredit, 'D'))) = 'K'
                                ) THEN
                                    CASE WHEN m.debit_credit = 'D' THEN 0 ELSE v_value END
                                ELSE
                                    CASE WHEN m.debit_credit = 'D' THEN v_value ELSE 0 END
                            END,
                            CASE
                                WHEN (
                                    v_manual_toggle
                                    AND UPPER(TRIM(COALESCE(t.debet_kredit, 'D'))) = 'K'
                                ) THEN
                                    CASE WHEN m.debit_credit = 'K' THEN 0 ELSE v_value END
                                ELSE
                                    CASE WHEN m.debit_credit = 'K' THEN v_value ELSE 0 END
                            END,
                            COALESCE(t.docno, ''),
                            COALESCE(t.doctype, ''),
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

            /*
               Tidak ada mapping tax yang bisa ditentukan.
               Jangan membuat COA fiktif.
            */
            CONTINUE;

        END IF;


        /* --------------------------------------------------------
           NILAI DETAIL
           -------------------------------------------------------- */

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


        /* --------------------------------------------------------
           RESOLVE COA
           -------------------------------------------------------- */

        IF v_is_manual
           AND UPPER(TRIM(COALESCE(m.account_role, '')))
               = 'ACCOUNT'
        THEN

            v_coa :=
                NULLIF(
                    BTRIM(COALESCE(t.idcoa, '')),
                    ''
                );

        ELSIF v_is_manual
              AND UPPER(TRIM(COALESCE(m.account_role, '')))
                  = 'COUNTER_ACCOUNT'
        THEN

            v_coa :=
                NULLIF(
                    BTRIM(COALESCE(t.counter_idcoa, '')),
                    ''
                );

        ELSE

            v_coa := sc_trx.fn_resolve_accounting_coa(
                m.account_role,
                t.idbarang,
                t.currcode,
                m.idcoa
            );

        END IF;


        IF NULLIF(BTRIM(COALESCE(v_coa, '')), '') IS NULL THEN

            RAISE EXCEPTION
                'COA tidak ter-resolve. journal_type=%, seq=%, role=%',
                TRIM(t.journal_type::TEXT),
                m.seq,
                m.account_role;

        END IF;


        /*
           Untuk manual journal yang memang memperbolehkan
           user menentukan arah account asal:
               D -> mapping asli
               K -> mapping dibalik

           NDK / GIRO mempunyai arah yang sudah ditentukan
           oleh TAHAP 04 sehingga tidak dibalik lagi.
        */

        v_effective_dc := m.debit_credit;

        IF v_is_manual
           AND v_manual_toggle
           AND UPPER(TRIM(COALESCE(t.debet_kredit, 'D'))) = 'K'
        THEN

            v_effective_dc :=
                CASE
                    WHEN m.debit_credit = 'D' THEN 'K'
                    ELSE 'D'
                END;

        END IF;


                INSERT INTO sc_trx.jurnal_dt
        (
            jurnal_id,
            journal_type,
            status,
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
            t.journal_type,
            'DRAFT',
            t.uniqueid,
            t.line_no,
            m.seq,
            m.account_role,
            m.value_source,
            v_coa,
            CASE
                WHEN v_effective_dc = 'D'
                THEN v_value
                ELSE 0
            END,
            CASE
                WHEN v_effective_dc = 'K'
                THEN v_value
                ELSE 0
            END,
            COALESCE(t.docno, ''),
            COALESCE(t.doctype, ''),
            COALESCE(m.description, t.keterangan, ''),
            COALESCE(t.currcode, ''),
            COALESCE(t.kurs, 1),
            t.createdby,
            CURRENT_TIMESTAMP
        );

    END LOOP;


    /* ------------------------------------------------------------
       RECHECK JOURNAL
       ------------------------------------------------------------ */

    SELECT
        COALESCE(SUM(debet), 0),
        COALESCE(SUM(kredit), 0)
    INTO v_total_d, v_total_k
    FROM sc_trx.jurnal_dt
    WHERE jurnal_id = v_jurnal_id;


    v_balance :=
        ROUND(
            v_total_d - v_total_k,
            2
        );


    IF v_total_d = 0
       AND v_total_k = 0
    THEN

        DELETE FROM sc_trx.jurnal_dt
        WHERE jurnal_id = v_jurnal_id;

        UPDATE sc_trx.jurnal_hd
        SET
            total_debet  = 0,
            total_kredit = 0,
            balance      = 0,
            status       = 'CANCELLED',
            updateddate  = CURRENT_TIMESTAMP
        WHERE id = v_jurnal_id;

        RAISE EXCEPTION
            'Journal type % belum menghasilkan nilai accounting untuk transaction %.',
            TRIM(t.journal_type::TEXT),
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
    SET
        total_debet  = v_total_d,
        total_kredit = v_total_k,
        balance      = v_balance,
        status       = 'POSTED',
        updateddate  = CURRENT_TIMESTAMP
    WHERE id = v_jurnal_id;


    RETURN v_jurnal_id;

END;
$$;


/* ============================================================================
   TAHAP 17
   RECALCULATE transaction_hd
   ============================================================================

   transaction_hd tetap summary dari transaction_dt.
   Tidak posting stock / asset / accounting.
   Grouping mengikuti key yang digunakan TAHAP 05.
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
        COALESCE(
            SUM(
                CASE
                    WHEN t.type_in_out = 'IN'
                    THEN COALESCE(t.qty, 0)
                    ELSE 0
                END
            ),
            0
        ),
        COALESCE(
            SUM(
                CASE
                    WHEN t.type_in_out = 'OUT'
                    THEN COALESCE(t.qty, 0)
                    ELSE 0
                END
            ),
            0
        ),
        COALESCE(SUM(t.bruto), 0),
        COALESCE(SUM(t.discount), 0),
        COALESCE(SUM(t.nilai), 0),
        COALESCE(SUM(t.dpp), 0),
        COALESCE(SUM(t.pajak), 0),
        COALESCE(SUM(t.total), 0),
        COALESCE(SUM(t.debet), 0),
        COALESCE(SUM(t.kredit), 0),
        ABS(
            ROUND(
                COALESCE(SUM(t.debet), 0)
                -
                COALESCE(SUM(t.kredit), 0),
                2
            )
        ),
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
   ACCOUNTING TRIGGER
   ============================================================================

   Trigger dibuat dengan prefix ZZ agar setelah:
       trg_transaction_asset
       trg_transaction_stock

   sehingga COST TAHAP 07 tersedia sebelum accounting.
   ============================================================================ */

CREATE OR REPLACE FUNCTION sc_trx.fn_transaction_accounting()
RETURNS TRIGGER
LANGUAGE plpgsql
AS $$
BEGIN

    IF TG_OP = 'INSERT' THEN

        IF TRIM(NEW.journal_type::TEXT) = 'JVGENL' THEN
            PERFORM sc_trx.fn_post_jvgenl_document(
                NEW.docno::TEXT,
                NEW.doctype::TEXT,
                NEW.idbranch::TEXT
            );
        ELSE
            PERFORM sc_trx.fn_post_accounting_transaction(
                NEW.uniqueid
            );
        END IF;

        PERFORM sc_trx.fn_recalculate_transaction_hd(
            NEW.docno
        );

        RETURN NEW;

    END IF;


    IF TG_OP = 'UPDATE' THEN

        IF TRIM(OLD.journal_type::TEXT) = 'JVGENL' THEN
            PERFORM sc_trx.fn_reverse_jvgenl_document(
                OLD.docno::TEXT,
                OLD.doctype::TEXT,
                OLD.idbranch::TEXT
            );
        ELSE
            PERFORM sc_trx.fn_reverse_accounting_transaction(
                OLD.uniqueid
            );
        END IF;


        IF TRIM(NEW.journal_type::TEXT) = 'JVGENL' THEN
            PERFORM sc_trx.fn_post_jvgenl_document(
                NEW.docno::TEXT,
                NEW.doctype::TEXT,
                NEW.idbranch::TEXT
            );
        ELSE
            PERFORM sc_trx.fn_post_accounting_transaction(
                NEW.uniqueid
            );
        END IF;


        PERFORM sc_trx.fn_recalculate_transaction_hd(
            OLD.docno
        );

        IF NEW.docno IS DISTINCT FROM OLD.docno THEN
            PERFORM sc_trx.fn_recalculate_transaction_hd(
                NEW.docno
            );
        END IF;

        RETURN NEW;

    END IF;


    IF TG_OP = 'DELETE' THEN

        IF TRIM(OLD.journal_type::TEXT) = 'JVGENL' THEN

            PERFORM sc_trx.fn_reverse_jvgenl_document(
                OLD.docno::TEXT,
                OLD.doctype::TEXT,
                OLD.idbranch::TEXT
            );

        ELSE

            PERFORM sc_trx.fn_reverse_accounting_transaction(
                OLD.uniqueid
            );

        END IF;


        PERFORM sc_trx.fn_recalculate_transaction_hd(
            OLD.docno
        );

        RETURN OLD;

    END IF;


    RETURN NULL;

END;
$$;

/*
   FINAL RUNTIME TRIGGER
   ====================
   Hapus trigger legacy yang memanggil function accounting yang sama,
   kemudian pasang hanya satu trigger final.
*/
DO $$
DECLARE
    r RECORD;
BEGIN

    FOR r IN
        SELECT tg.tgname
        FROM pg_trigger tg
        JOIN pg_proc p
          ON p.oid = tg.tgfoid
        JOIN pg_namespace n
          ON n.oid = p.pronamespace
        WHERE tg.tgrelid = 'sc_trx.transaction_dt'::regclass
          AND NOT tg.tgisinternal
          AND n.nspname = 'sc_trx'
          AND p.proname = 'fn_transaction_accounting'
          AND tg.tgname <> 'trg_zz_transaction_accounting'
    LOOP

        EXECUTE format(
            'DROP TRIGGER IF EXISTS %I ON sc_trx.transaction_dt',
            r.tgname
        );

    END LOOP;

END;
$$;


DROP TRIGGER IF EXISTS trg_zz_transaction_accounting
ON sc_trx.transaction_dt;


CREATE TRIGGER trg_zz_transaction_accounting
AFTER INSERT OR UPDATE OR DELETE
ON sc_trx.transaction_dt
FOR EACH ROW
EXECUTE FUNCTION sc_trx.fn_transaction_accounting();



/* ============================================================
   TAHAP 18 -ADD
   DELETE DOWNSTREAM STOCK PROTECTION
   ============================================================ */

CREATE OR REPLACE FUNCTION sc_trx.fn_block_delete_downstream_stock()
RETURNS TRIGGER
LANGUAGE plpgsql
AS $$
DECLARE
    v_source_stk_id    BIGINT;
    v_downstream_count INTEGER;
    v_downstream_doc   TEXT;
BEGIN

    /* Hanya transaksi yang mempunyai efek stock */
    IF COALESCE(BTRIM(OLD.stock_effect), 'NONE')
       NOT IN ('IN','OUT','INOUT')
    THEN
        RETURN OLD;
    END IF;


    /* Cari stock ledger transaksi yang akan dihapus */
    SELECT sb.id
    INTO v_source_stk_id
    FROM sc_trx.stkblc sb
    WHERE sb.uniqueid = OLD.uniqueid
    ORDER BY sb.id
    LIMIT 1;


    /* Tidak ada stkblc = tidak ada stock yang perlu dilindungi */
    IF v_source_stk_id IS NULL THEN
        RETURN OLD;
    END IF;


    /* Cari downstream stock */
    SELECT
        COUNT(*),
        MIN(x.docno)
    INTO
        v_downstream_count,
        v_downstream_doc
    FROM sc_trx.stkblc x
    WHERE x.stock_uniqueid = OLD.stock_uniqueid
      AND x.uniqueid <> OLD.uniqueid

      /* Dokumen yang sama bukan downstream */
      AND NOT (
          x.docno IS NOT DISTINCT FROM OLD.docno
          AND x.doctype IS NOT DISTINCT FROM OLD.doctype
      )

      AND (
          x.docdate > OLD.docdate
          OR (
              x.docdate = OLD.docdate
              AND x.id > v_source_stk_id
          )
      );


    /* BLOCK DELETE */
    IF v_downstream_count > 0 THEN

        RAISE EXCEPTION
            'DELETE BLOCKED: transaksi % mempunyai % downstream stock transaction. Downstream: %.',
            BTRIM(COALESCE(OLD.docno::TEXT, '')),
            v_downstream_count,
            BTRIM(COALESCE(v_downstream_doc, ''));

    END IF;


    RETURN OLD;

END;
$$;


/* ============================================================
   TRIGGER
   ============================================================ */

DROP TRIGGER IF EXISTS trg_block_delete_downstream_stock
ON sc_trx.transaction_dt;

CREATE TRIGGER trg_block_delete_downstream_stock
BEFORE DELETE
ON sc_trx.transaction_dt
FOR EACH ROW
EXECUTE FUNCTION sc_trx.fn_block_delete_downstream_stock();


/* ============================================================================
   TAHAP 19
   VALIDATION
   ============================================================================

   Tidak membuat validation baru.
   Validation source / type / primary COA tetap berasal dari TAHAP 04.
   Validation COA jurnal + status DRAFT tetap berasal dari TAHAP 10.

   Function utama yang menjadi dependency:
       fn_validate_source_uniqueid()
       fn_validate_transaction_type / versi aktif TAHAP 04
       fn_validate_jurnal_coa()
       fn_validate_jurnal_detail_status()
   ============================================================================ */


/* ============================================================================
   VERIFICATION
   ============================================================================ */

SELECT
    tg.tgname,
    tg.tgenabled,
    p.proname AS trigger_function,
    pg_get_triggerdef(tg.oid) AS trigger_definition
FROM pg_trigger tg
JOIN pg_proc p
  ON p.oid = tg.tgfoid
WHERE tg.tgrelid = 'sc_trx.transaction_dt'::regclass
  AND NOT tg.tgisinternal
ORDER BY tg.tgname;


SELECT
    proname,
    pg_get_function_identity_arguments(p.oid) AS arguments
FROM pg_proc p
JOIN pg_namespace n
  ON n.oid = p.pronamespace
WHERE n.nspname = 'sc_trx'
  AND proname IN
      (
          'fn_reverse_accounting_transaction',
          'fn_resolve_accounting_coa',
          'fn_post_accounting_transaction',
          'fn_recalculate_transaction_hd',
          'fn_transaction_accounting'
      )
ORDER BY proname;


SELECT
    jh.id,
    jh.uniqueid,
    jh.source_uniqueid,
    jh.docno,
    jh.doctype,
    jh.journal_type,
    jh.total_debet,
    jh.total_kredit,
    jh.balance,
    jh.status
FROM sc_trx.jurnal_hd jh
ORDER BY jh.id DESC
LIMIT 50;


SELECT
    column_name,
    data_type,
    character_maximum_length,
    is_nullable,
    column_default
FROM information_schema.columns
WHERE table_schema = 'sc_trx'
  AND table_name = 'jurnal_dt'
  AND column_name IN ('journal_type', 'status')
ORDER BY ordinal_position;


/* ============================================================================
   HASIL AKHIR
   ============================================================================

   INSERT:
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
       cancel accounting OLD
       -> post accounting NEW
       -> recalculate transaction_hd

   DELETE:
       cancel accounting
       -> recalculate transaction_hd

   TAHAP 11 - 14:
       tetap menggunakan object tahap sebelumnya.

   TAHAP 19:
       validation tetap dikelola TAHAP 04 + TAHAP 10.

   Tidak ada:
       - DROP TABLE
       - TEST INSERT
       - rebuild massal
       - update data legacy
   ============================================================================ */

COMMIT;
