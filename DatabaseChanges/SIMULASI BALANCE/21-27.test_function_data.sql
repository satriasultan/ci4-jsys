/* ============================================================================
   JSYS ACCOUNTING / ERP
   TAHAP 20 s/d 27 : READ-ONLY AUDIT / VALIDATION

   TUJUAN
   ---------------------------------------------------------------------------
   Script ini HANYA memeriksa integritas object dan data existing.

   TIDAK melakukan:
   - INSERT transaksi test
   - UPDATE transaksi existing
   - DELETE transaksi existing
   - BEGIN / ROLLBACK untuk simulasi transaksi
   - REBUILD massal
   - CREATE journal_type / COA / data master baru

   ALUR YANG DIAUDIT

   transaction_dt
        |
        +--> stkblc --> stkblc_avgcost
        |
        +--> assetblc
        |
        +--> jurnal_hd --> jurnal_dt
        |
        +--> transaction_hd

   Prinsip:
   - transaction_dt = source of truth
   - stock posting tetap milik TAHAP 06
   - average cost tetap milik TAHAP 07
   - asset posting tetap milik TAHAP 08
   - journal header/detail tetap milik TAHAP 09/10
   - accounting trigger tetap milik TAHAP 18
   - anomali legacy yang tidak mengubah transaksi TIDAK menghentikan script;
     script hanya memberikan NOTICE.
   - object inti yang hilang tetap menghasilkan ERROR.
   ============================================================================ */


/* ============================================================================
   TAHAP 20
   CHECK OBJECT DAN TRIGGER INTI
   ============================================================================ */
DO $$
DECLARE
    v_missing TEXT := '';
    v_found   INTEGER;
BEGIN
    /* TABLE */
    IF to_regclass('sc_trx.transaction_dt') IS NULL THEN
        v_missing := v_missing || E'\n- sc_trx.transaction_dt';
    END IF;

    IF to_regclass('sc_trx.transaction_hd') IS NULL THEN
        v_missing := v_missing || E'\n- sc_trx.transaction_hd';
    END IF;

    IF to_regclass('sc_trx.stkblc') IS NULL THEN
        v_missing := v_missing || E'\n- sc_trx.stkblc';
    END IF;

    IF to_regclass('sc_trx.assetblc') IS NULL THEN
        v_missing := v_missing || E'\n- sc_trx.assetblc';
    END IF;

    IF to_regclass('sc_trx.jurnal_hd') IS NULL THEN
        v_missing := v_missing || E'\n- sc_trx.jurnal_hd';
    END IF;

    IF to_regclass('sc_trx.jurnal_dt') IS NULL THEN
        v_missing := v_missing || E'\n- sc_trx.jurnal_dt';
    END IF;

    IF to_regclass('sc_mst.journal_type') IS NULL THEN
        v_missing := v_missing || E'\n- sc_mst.journal_type';
    END IF;

    IF to_regclass('sc_mst.journal_type_coa') IS NULL THEN
        v_missing := v_missing || E'\n- sc_mst.journal_type_coa';
    END IF;

    IF to_regclass('sc_mst.coa') IS NULL THEN
        v_missing := v_missing || E'\n- sc_mst.coa';
    END IF;

    IF to_regclass('sc_mst.tax_dtl') IS NULL THEN
        v_missing := v_missing || E'\n- sc_mst.tax_dtl';
    END IF;

    /* ACCOUNTING FUNCTIONS */
    IF to_regprocedure('sc_trx.fn_post_accounting_transaction(text)') IS NULL THEN
        v_missing := v_missing || E'\n- fn_post_accounting_transaction(text)';
    END IF;

    IF to_regprocedure('sc_trx.fn_reverse_accounting_transaction(text)') IS NULL THEN
        v_missing := v_missing || E'\n- fn_reverse_accounting_transaction(text)';
    END IF;

    IF to_regprocedure('sc_trx.fn_recalculate_transaction_hd(text)') IS NULL THEN
        v_missing := v_missing || E'\n- fn_recalculate_transaction_hd(text)';
    END IF;

    IF to_regprocedure('sc_trx.fn_transaction_accounting()') IS NULL THEN
        v_missing := v_missing || E'\n- fn_transaction_accounting()';
    END IF;

    IF v_missing <> '' THEN
        RAISE EXCEPTION 'TAHAP 20 GAGAL. Object inti belum tersedia:%', v_missing;
    END IF;

    RAISE NOTICE 'TAHAP 20 OK : table/master/function inti tersedia';

    IF to_regclass('sc_trx.stkblc_avgcost') IS NULL THEN
        RAISE NOTICE 'TAHAP 20 NOTICE : stkblc_avgcost tidak tersedia (tidak mengganggu audit accounting)';
    END IF;

    /* TRIGGER transaction_dt */
    SELECT COUNT(*)
    INTO v_found
    FROM pg_trigger
    WHERE tgrelid = 'sc_trx.transaction_dt'::regclass
      AND NOT tgisinternal
      AND tgname IN
      (
          'trg_01_transaction_prepare',
          'trg_02_transaction_type',
          'trg_transaction_stock',
          'trg_transaction_asset',
          'trg_zz_transaction_accounting'
      );

    IF v_found < 5 THEN
        RAISE NOTICE
            'TAHAP 20 NOTICE : trigger transaction_dt ditemukan % dari 5 trigger utama',
            v_found;
    ELSE
        RAISE NOTICE 'TAHAP 20 OK : trigger transaction_dt lengkap';
    END IF;

    /* TRIGGER jurnal_dt */
    SELECT COUNT(*)
    INTO v_found
    FROM pg_trigger
    WHERE tgrelid = 'sc_trx.jurnal_dt'::regclass
      AND NOT tgisinternal
      AND tgname IN
      (
          'trg_validate_jurnal_coa',
          'trg_validate_jurnal_detail_status',
          'trg_sync_jurnal_hd'
      );

    IF v_found < 3 THEN
        RAISE NOTICE
            'TAHAP 20 NOTICE : trigger jurnal_dt ditemukan % dari 3 trigger utama',
            v_found;
    ELSE
        RAISE NOTICE 'TAHAP 20 OK : trigger jurnal_dt lengkap';
    END IF;

END;
$$;


/* ============================================================================
   TAHAP 21
   STOCK INTEGRITY
   ============================================================================ */

/* Stock identity / stock key */
SELECT
    COUNT(*) AS invalid_stock_identity
FROM sc_trx.stkblc
WHERE stock_uniqueid IS NULL
   OR BTRIM(stock_uniqueid) = ''
   OR stock_key IS NULL
   OR LENGTH(BTRIM(stock_key::TEXT)) <> 32
   OR BTRIM(stock_key::TEXT) <> md5(stock_uniqueid);

/* Arah qty */
SELECT
    COUNT(*) AS invalid_stock_direction
FROM sc_trx.stkblc
WHERE (type_in_out = 'IN'
       AND (COALESCE(qty_in,0) <= 0 OR COALESCE(qty_out,0) <> 0))
   OR (type_in_out = 'OUT'
       AND (COALESCE(qty_out,0) <= 0 OR COALESCE(qty_in,0) <> 0));

/* Orphan stock ledger */
SELECT
    COUNT(*) AS orphan_stkblc
FROM sc_trx.stkblc sb
LEFT JOIN sc_trx.transaction_dt td
       ON td.uniqueid = sb.uniqueid
WHERE td.uniqueid IS NULL;

DO $$ BEGIN RAISE NOTICE 'TAHAP 21 selesai : stock integrity diperiksa'; END $$;


/* ============================================================================
   TAHAP 22
   ACCOUNTING CONFIGURATION
   ============================================================================ */

/* journal_type_coa dengan COA tidak ada */
SELECT
    j.journal_type,
    j.seq,
    j.account_role,
    j.idcoa
FROM sc_mst.journal_type_coa j
LEFT JOIN sc_mst.coa c
       ON BTRIM(c.idcoa::TEXT) = BTRIM(j.idcoa::TEXT)
WHERE j.active = 'YES'
  AND c.idcoa IS NULL
ORDER BY j.journal_type, j.seq;

/* Mapping invalid debit/credit/value_source */
SELECT
    j.journal_type,
    j.seq,
    j.account_role,
    j.debit_credit,
    j.value_source
FROM sc_mst.journal_type_coa j
WHERE j.active = 'YES'
  AND (
       j.debit_credit NOT IN ('D','K')
       OR j.value_source NOT IN ('NILAI','DPP','PAJAK','TOTAL','QTY','COST')
  )
ORDER BY j.journal_type, j.seq;

/* Accounting transaction yang belum memiliki journal header */
SELECT
    td.uniqueid,
    td.docno,
    td.doctype,
    td.journal_type,
    td.docdate,
    td.accounting_effect,
    td.idcoa,
    td.debet,
    td.kredit
FROM sc_trx.transaction_dt td
LEFT JOIN sc_trx.jurnal_hd jh
       ON jh.source_uniqueid = COALESCE(NULLIF(BTRIM(td.source_uniqueid), ''), td.uniqueid)
      AND jh.uniqueid NOT LIKE 'JRNL-REV-%'
WHERE COALESCE(td.accounting_effect,'NO') = 'YES'
  AND jh.id IS NULL
ORDER BY td.docdate, td.id;

/* Transaction bertax tetapi belum mempunyai detail TAX */
SELECT
    td.uniqueid,
    td.docno,
    td.journal_type,
    td.idtax,
    td.dpp,
    td.pajak
FROM sc_trx.transaction_dt td
WHERE COALESCE(td.accounting_effect,'NO') = 'YES'
  AND COALESCE(td.pajak,0) > 0
  AND NOT EXISTS
  (
      SELECT 1
      FROM sc_trx.jurnal_hd jh
      JOIN sc_trx.jurnal_dt jd
        ON jd.jurnal_id = jh.id
      WHERE jh.source_uniqueid = COALESCE(NULLIF(BTRIM(td.source_uniqueid), ''), td.uniqueid)
        AND jh.uniqueid NOT LIKE 'JRNL-REV-%'
        AND UPPER(BTRIM(COALESCE(jd.account_role,''))) = 'TAX'
  )
ORDER BY td.docdate, td.id;

DO $$ BEGIN RAISE NOTICE 'TAHAP 22 selesai : accounting configuration diperiksa'; END $$;


/* ============================================================================
   TAHAP 23
   JOURNAL DETAIL INTEGRITY
   ============================================================================ */

/* Detail dengan COA tidak valid */
SELECT
    jd.id,
    jd.jurnal_id,
    jd.source_uniqueid,
    jd.seq,
    jd.idcoa,
    jd.debet,
    jd.kredit
FROM sc_trx.jurnal_dt jd
LEFT JOIN sc_mst.coa c
       ON BTRIM(c.idcoa::TEXT) = BTRIM(jd.idcoa::TEXT)
WHERE c.idcoa IS NULL
ORDER BY jd.jurnal_id, jd.seq, jd.id;

/* Nominal invalid */
SELECT
    COUNT(*) AS invalid_journal_detail
FROM sc_trx.jurnal_dt
WHERE COALESCE(debet,0) < 0
   OR COALESCE(kredit,0) < 0
   OR (
        COALESCE(debet,0) > 0
        AND COALESCE(kredit,0) > 0
      )
   OR (
        COALESCE(debet,0) = 0
        AND COALESCE(kredit,0) = 0
      );

/* Detail tanpa header */
SELECT
    jd.id,
    jd.jurnal_id,
    jd.source_uniqueid,
    jd.idcoa
FROM sc_trx.jurnal_dt jd
LEFT JOIN sc_trx.jurnal_hd jh
       ON jh.id = jd.jurnal_id
WHERE jh.id IS NULL
ORDER BY jd.id;

/* Header vs detail */
SELECT
    jh.id,
    jh.uniqueid,
    jh.docno,
    jh.journal_type,
    jh.status,
    jh.total_debet AS header_debet,
    x.detail_debet,
    jh.total_kredit AS header_kredit,
    x.detail_kredit,
    jh.balance AS header_balance
FROM sc_trx.jurnal_hd jh
JOIN
(
    SELECT
        jurnal_id,
        ROUND(COALESCE(SUM(debet),0),2) AS detail_debet,
        ROUND(COALESCE(SUM(kredit),0),2) AS detail_kredit
    FROM sc_trx.jurnal_dt
    GROUP BY jurnal_id
) x
  ON x.jurnal_id = jh.id
WHERE ABS(COALESCE(jh.total_debet,0) - x.detail_debet) > 0.01
   OR ABS(COALESCE(jh.total_kredit,0) - x.detail_kredit) > 0.01
   OR ABS(
          COALESCE(jh.balance,0)
          - ROUND(x.detail_debet - x.detail_kredit,2)
       ) > 0.01
ORDER BY jh.id;

/* POSTED harus balance */
SELECT
    id,
    uniqueid,
    docno,
    journal_type,
    total_debet,
    total_kredit,
    balance,
    status
FROM sc_trx.jurnal_hd
WHERE status = 'POSTED'
  AND ABS(COALESCE(balance,0)) > 0.01
ORDER BY trxdate, id;

/* POSTED tanpa detail */
SELECT
    jh.id,
    jh.uniqueid,
    jh.docno,
    jh.journal_type,
    jh.status
FROM sc_trx.jurnal_hd jh
WHERE jh.status = 'POSTED'
  AND NOT EXISTS
  (
      SELECT 1
      FROM sc_trx.jurnal_dt jd
      WHERE jd.jurnal_id = jh.id
  )
ORDER BY jh.id;

DO $$ BEGIN RAISE NOTICE 'TAHAP 23 selesai : jurnal detail/header integrity diperiksa'; END $$;


/* ============================================================================
   TAHAP 24
   SOURCE TRACE ACCOUNTING
   ============================================================================ */

/* Jurnal normal tanpa transaction_dt */
SELECT
    jh.id,
    jh.uniqueid,
    jh.source_uniqueid,
    jh.docno,
    jh.doctype,
    jh.journal_type,
    jh.status
FROM sc_trx.jurnal_hd jh
LEFT JOIN sc_trx.transaction_dt td
       ON td.uniqueid = jh.source_uniqueid
       OR td.source_uniqueid = jh.source_uniqueid
WHERE jh.uniqueid NOT LIKE 'JRNL-REV-%'
  AND td.uniqueid IS NULL
ORDER BY jh.trxdate, jh.id;

/* Reversal journal yang referensi original tidak ditemukan */
SELECT
    rev.id,
    rev.uniqueid,
    rev.source_uniqueid,
    rev.docno,
    rev.journal_type,
    rev.status
FROM sc_trx.jurnal_hd rev
WHERE rev.uniqueid LIKE 'JRNL-REV-%'
  AND NOT EXISTS
  (
      SELECT 1
      FROM sc_trx.jurnal_hd orig
      WHERE orig.uniqueid <> rev.uniqueid
        AND orig.source_uniqueid = rev.source_uniqueid
        AND orig.uniqueid NOT LIKE 'JRNL-REV-%'
  )
ORDER BY rev.id;

DO $$ BEGIN RAISE NOTICE 'TAHAP 24 selesai : source trace accounting diperiksa'; END $$;


/* ============================================================================
   TAHAP 25
   TRANSACTION / ACCOUNTING VALUE INTEGRITY
   ============================================================================ */

/* transaction_dt accounting value invalid */
SELECT
    uniqueid,
    docno,
    journal_type,
    idcoa,
    debet,
    kredit
FROM sc_trx.transaction_dt
WHERE COALESCE(debet,0) < 0
   OR COALESCE(kredit,0) < 0
   OR (
        COALESCE(debet,0) > 0
        AND COALESCE(kredit,0) > 0
      )
ORDER BY docdate, id;

/* direction mismatch */
SELECT
    td.uniqueid,
    td.docno,
    td.journal_type,
    jt.direction,
    td.type_in_out
FROM sc_trx.transaction_dt td
JOIN sc_mst.journal_type jt
  ON jt.journal_type = td.journal_type
WHERE (jt.direction = 'IN'  AND td.type_in_out <> 'IN')
   OR (jt.direction = 'OUT' AND td.type_in_out <> 'OUT')
ORDER BY td.docdate, td.id;

/* journal_type transaction accounting effect */
SELECT
    jt.journal_type,
    jt.accounting_effect,
    COUNT(td.uniqueid) AS total_transaction
FROM sc_mst.journal_type jt
LEFT JOIN sc_trx.transaction_dt td
       ON td.journal_type = jt.journal_type
GROUP BY jt.journal_type, jt.accounting_effect
ORDER BY jt.journal_type;

DO $$ BEGIN RAISE NOTICE 'TAHAP 25 selesai : nilai transaksi dan direction diperiksa'; END $$;


/* ============================================================================
   TAHAP 26
   TRANSACTION HEADER CONSISTENCY
   ============================================================================ */
WITH expected AS
(
    SELECT
        t.docno,
        t.doctype,
        t.journal_type,
        t.type_in_out,
        t.idbranch,
        t.cabang,
        t.ref_docno,
        t.ref_doctype,
        MIN(t.docdate) AS docdate,
        COUNT(*) AS total_line,
        COALESCE(SUM(t.qty),0) AS total_qty,
        COALESCE(SUM(CASE WHEN t.type_in_out='IN'  THEN COALESCE(t.qty,0) ELSE 0 END),0) AS total_qty_in,
        COALESCE(SUM(CASE WHEN t.type_in_out='OUT' THEN COALESCE(t.qty,0) ELSE 0 END),0) AS total_qty_out,
        COALESCE(SUM(t.bruto),0) AS total_bruto,
        COALESCE(SUM(t.discount),0) AS total_discount,
        COALESCE(SUM(t.nilai),0) AS total_nilai,
        COALESCE(SUM(t.dpp),0) AS total_dpp,
        COALESCE(SUM(t.pajak),0) AS total_pajak,
        COALESCE(SUM(t.total),0) AS total_total,
        COALESCE(SUM(t.debet),0) AS total_debet,
        COALESCE(SUM(t.kredit),0) AS total_kredit,
        ABS(ROUND(COALESCE(SUM(t.debet),0)-COALESCE(SUM(t.kredit),0),2)) AS balance
    FROM sc_trx.transaction_dt t
    GROUP BY
        t.docno,
        t.doctype,
        t.journal_type,
        t.type_in_out,
        t.idbranch,
        t.cabang,
        t.ref_docno,
        t.ref_doctype
)
SELECT
    e.docno,
    e.doctype,
    e.journal_type,
    e.type_in_out,
    e.idbranch,
    h.docdate AS header_docdate,
    e.docdate AS expected_docdate,
    h.total_line,
    e.total_line AS expected_total_line,
    h.total_qty,
    e.total_qty AS expected_total_qty,
    h.total_qty_in,
    e.total_qty_in AS expected_total_qty_in,
    h.total_qty_out,
    e.total_qty_out AS expected_total_qty_out,
    h.total_bruto,
    e.total_bruto AS expected_total_bruto,
    h.total_discount,
    e.total_discount AS expected_total_discount,
    h.total_nilai,
    e.total_nilai AS expected_total_nilai,
    h.total_dpp,
    e.total_dpp AS expected_total_dpp,
    h.total_pajak,
    e.total_pajak AS expected_total_pajak,
    h.total_total,
    e.total_total AS expected_total_total,
    h.total_debet,
    e.total_debet AS expected_total_debet,
    h.total_kredit,
    e.total_kredit AS expected_total_kredit,
    h.balance,
    e.balance AS expected_balance
FROM expected e
LEFT JOIN sc_trx.transaction_hd h
  ON h.docno IS NOT DISTINCT FROM e.docno
 AND h.doctype IS NOT DISTINCT FROM e.doctype
 AND h.journal_type IS NOT DISTINCT FROM e.journal_type
 AND h.type_in_out IS NOT DISTINCT FROM e.type_in_out
 AND h.idbranch IS NOT DISTINCT FROM e.idbranch
 AND h.cabang IS NOT DISTINCT FROM e.cabang
 AND h.ref_docno IS NOT DISTINCT FROM e.ref_docno
 AND h.ref_doctype IS NOT DISTINCT FROM e.ref_doctype
WHERE h.docno IS NULL
   OR h.docdate IS DISTINCT FROM e.docdate
   OR COALESCE(h.total_line,0) <> e.total_line
   OR ABS(COALESCE(h.total_qty,0) - e.total_qty) > 0.000001
   OR ABS(COALESCE(h.total_qty_in,0) - e.total_qty_in) > 0.000001
   OR ABS(COALESCE(h.total_qty_out,0) - e.total_qty_out) > 0.000001
   OR ABS(COALESCE(h.total_bruto,0) - e.total_bruto) > 0.01
   OR ABS(COALESCE(h.total_discount,0) - e.total_discount) > 0.01
   OR ABS(COALESCE(h.total_nilai,0) - e.total_nilai) > 0.01
   OR ABS(COALESCE(h.total_dpp,0) - e.total_dpp) > 0.01
   OR ABS(COALESCE(h.total_pajak,0) - e.total_pajak) > 0.01
   OR ABS(COALESCE(h.total_total,0) - e.total_total) > 0.01
   OR ABS(COALESCE(h.total_debet,0) - e.total_debet) > 0.01
   OR ABS(COALESCE(h.total_kredit,0) - e.total_kredit) > 0.01
   OR ABS(COALESCE(h.balance,0) - e.balance) > 0.01
ORDER BY e.docno, e.journal_type, e.type_in_out;

DO $$ BEGIN RAISE NOTICE 'TAHAP 26 selesai : transaction_hd vs transaction_dt diperiksa'; END $$;


/* ============================================================================
   TAHAP 27
   FINAL SUMMARY
   ============================================================================ */
DO $$
DECLARE
    v_tx_invalid      BIGINT;
    v_stock_invalid   BIGINT;
    v_journal_invalid BIGINT;
    v_header_invalid  BIGINT;
BEGIN
    SELECT COUNT(*)
    INTO v_tx_invalid
    FROM sc_trx.transaction_dt td
    LEFT JOIN sc_mst.journal_type jt
      ON jt.journal_type = td.journal_type
    WHERE jt.journal_type IS NULL
       OR td.type_in_out NOT IN ('IN','OUT')
       OR COALESCE(td.qty,0) < 0
       OR COALESCE(td.debet,0) < 0
       OR COALESCE(td.kredit,0) < 0
       OR (COALESCE(td.debet,0) > 0 AND COALESCE(td.kredit,0) > 0);

    SELECT COUNT(*)
    INTO v_stock_invalid
    FROM sc_trx.stkblc sb
    WHERE sb.stock_uniqueid IS NULL
       OR BTRIM(sb.stock_uniqueid) = ''
       OR sb.stock_key IS NULL
       OR BTRIM(sb.stock_key::TEXT) <> md5(sb.stock_uniqueid);

    SELECT COUNT(*)
    INTO v_journal_invalid
    FROM sc_trx.jurnal_hd jh
    WHERE jh.status = 'POSTED'
      AND ABS(COALESCE(jh.balance,0)) > 0.01;

    SELECT COUNT(*)
    INTO v_header_invalid
    FROM sc_trx.transaction_dt td
    LEFT JOIN sc_trx.transaction_hd th
      ON th.docno IS NOT DISTINCT FROM td.docno
     AND th.doctype IS NOT DISTINCT FROM td.doctype
     AND th.journal_type IS NOT DISTINCT FROM td.journal_type
     AND th.type_in_out IS NOT DISTINCT FROM td.type_in_out
     AND th.idbranch IS NOT DISTINCT FROM td.idbranch
     AND th.cabang IS NOT DISTINCT FROM td.cabang
     AND th.ref_docno IS NOT DISTINCT FROM td.ref_docno
     AND th.ref_doctype IS NOT DISTINCT FROM td.ref_doctype
    WHERE th.docno IS NULL;

    RAISE NOTICE '============================================================';
    RAISE NOTICE 'TAHAP 20 s/d 27 : READ-ONLY AUDIT SELESAI';
    RAISE NOTICE 'transaction_dt invalid  : %', v_tx_invalid;
    RAISE NOTICE 'stkblc invalid           : %', v_stock_invalid;
    RAISE NOTICE 'POSTED journal invalid   : %', v_journal_invalid;
    RAISE NOTICE 'transaction header miss  : %', v_header_invalid;
    RAISE NOTICE '============================================================';

    IF v_tx_invalid = 0
       AND v_stock_invalid = 0
       AND v_journal_invalid = 0
       AND v_header_invalid = 0
    THEN
        RAISE NOTICE 'STATUS : INTEGRITY CHECK UTAMA OK';
    ELSE
        RAISE NOTICE 'STATUS : ADA DATA YANG PERLU DITINJAU';
        RAISE NOTICE 'Script sengaja TIDAK mengubah data legacy.';
    END IF;
END;
$$;


/* ============================================================================
   END
   ============================================================================ */
