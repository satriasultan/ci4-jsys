/* ============================================================================
   JSYS ACCOUNTING / ERP
   TAHAP 21 - 27 : FINAL CLEAN READ-ONLY AUDIT
   PLUS ADDITIONAL TESTS UNTUK ARSITEKTUR TAHAP 06 - 19
   ============================================================================

   SIFAT SCRIPT:
       READ-ONLY.

   TIDAK MELAKUKAN:
       - INSERT transaksi test
       - UPDATE data existing
       - DELETE data existing
       - REBUILD massal
       - CREATE master baru
       - POST / REVERSE transaksi
       - BEGIN / COMMIT transaksi simulasi

   SOURCE OF TRUTH:
       transaction_dt

   FLOW:
       transaction_dt
            |
            +--> stkblc --> stkblc_avgcost
            |
            +--> assetblc
            |
            +--> jurnal_hd --> jurnal_dt
            |
            +--> transaction_hd

   TEST TAMBAHAN:
       TAHAP 28 : Manual accounting mapping
       TAHAP 29 : Tax mapping / tax journal detail
       TAHAP 30 : Accounting COST source
       TAHAP 31 : JSA stock exclusion
       TAHAP 32 : Accounting trigger order
       TAHAP 33 : Reversal idempotency / status integrity

   CATATAN:
       Hasil query yang menemukan anomaly TIDAK menghentikan script.
       Script audit hanya membaca dan menampilkan data yang perlu ditinjau.
   ============================================================================ */


/* ============================================================================
   TAHAP 20
   OBJECT DAN TRIGGER INTI
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

    IF to_regclass('sc_trx.stkblc_avgcost') IS NULL THEN
        v_missing := v_missing || E'\n- sc_trx.stkblc_avgcost';
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
    IF to_regprocedure(
        'sc_trx.fn_post_accounting_transaction(text)'
    ) IS NULL THEN
        v_missing := v_missing
            || E'\n- fn_post_accounting_transaction(text)';
    END IF;

    IF to_regprocedure(
        'sc_trx.fn_reverse_accounting_transaction(text)'
    ) IS NULL THEN
        v_missing := v_missing
            || E'\n- fn_reverse_accounting_transaction(text)';
    END IF;

    IF to_regprocedure(
        'sc_trx.fn_recalculate_transaction_hd(text)'
    ) IS NULL THEN
        v_missing := v_missing
            || E'\n- fn_recalculate_transaction_hd(text)';
    END IF;

    IF to_regprocedure(
        'sc_trx.fn_transaction_accounting()'
    ) IS NULL THEN
        v_missing := v_missing
            || E'\n- fn_transaction_accounting()';
    END IF;


    IF v_missing <> '' THEN
        RAISE EXCEPTION
            'TAHAP 20 GAGAL. Object inti belum tersedia:%',
            v_missing;
    END IF;


    RAISE NOTICE
        'TAHAP 20 OK : object inti tersedia';


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
        RAISE NOTICE
            'TAHAP 20 OK : trigger transaction_dt lengkap';
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
        RAISE NOTICE
            'TAHAP 20 OK : trigger jurnal_dt lengkap';
    END IF;

END;
$$;


/* ============================================================================
   TAHAP 21
   STOCK INTEGRITY
   ============================================================================ */

SELECT
    COUNT(*) AS invalid_stock_identity
FROM sc_trx.stkblc
WHERE stock_uniqueid IS NULL
   OR BTRIM(stock_uniqueid) = ''
   OR stock_key IS NULL
   OR LENGTH(BTRIM(stock_key::TEXT)) <> 32
   OR BTRIM(stock_key::TEXT) <> md5(stock_uniqueid);


SELECT
    COUNT(*) AS invalid_stock_direction
FROM sc_trx.stkblc
WHERE (type_in_out = 'IN'
       AND (
            COALESCE(qty_in,0) <= 0
            OR COALESCE(qty_out,0) <> 0
       ))
   OR (type_in_out = 'OUT'
       AND (
            COALESCE(qty_out,0) <= 0
            OR COALESCE(qty_in,0) <> 0
       ));


SELECT
    COUNT(*) AS orphan_stkblc
FROM sc_trx.stkblc sb
LEFT JOIN sc_trx.transaction_dt td
       ON td.uniqueid = sb.uniqueid
WHERE td.uniqueid IS NULL;


SELECT
    BTRIM(sb.idbarang::TEXT) AS idbarang,
    BTRIM(sb.idlocation::TEXT) AS idlocation,
    BTRIM(COALESCE(sb.batch::TEXT,'')) AS batch,
    SUM(COALESCE(sb.qty_in,0)) AS qty_in,
    SUM(COALESCE(sb.qty_out,0)) AS qty_out,
    ROUND(
        SUM(COALESCE(sb.qty_in,0))
        - SUM(COALESCE(sb.qty_out,0)),
        6
    ) AS movement_qty_net
FROM sc_trx.stkblc sb
GROUP BY
    BTRIM(sb.idbarang::TEXT),
    BTRIM(sb.idlocation::TEXT),
    BTRIM(COALESCE(sb.batch::TEXT,''))
HAVING
    SUM(COALESCE(sb.qty_in,0))
    - SUM(COALESCE(sb.qty_out,0)) < 0
ORDER BY idbarang, idlocation, batch;


DO $$
BEGIN
    RAISE NOTICE
        'TAHAP 21 selesai : stock identity, arah qty, orphan, dan negative movement diperiksa';
END;
$$;


/* ============================================================================
   TAHAP 22
   ACCOUNTING CONFIGURATION
   ============================================================================

   IMPORTANT:
       idcoa kosong pada journal_type_coa TIDAK otomatis salah.
       Operational mapping memang dapat menggunakan resolver:
           mbarang / currency / konfigurasi_umum

       Yang dianggap anomaly di bawah hanya:
           - idcoa terisi tetapi tidak ada di sc_mst.coa
           - mapping aktif tidak mempunyai journal detail role
             yang dibutuhkan untuk manual accounting
   ============================================================================ */


/* Non-empty static COA mapping yang tidak ditemukan di master */
SELECT
    BTRIM(j.journal_type::TEXT) AS journal_type,
    j.seq,
    j.account_role,
    BTRIM(j.idcoa::TEXT) AS idcoa
FROM sc_mst.journal_type_coa j
LEFT JOIN sc_mst.coa c
       ON BTRIM(c.idcoa::TEXT) = BTRIM(j.idcoa::TEXT)
WHERE j.active = 'YES'
  AND NULLIF(BTRIM(j.idcoa::TEXT), '') IS NOT NULL
  AND c.idcoa IS NULL
ORDER BY j.journal_type, j.seq;


/* Mapping invalid debit/credit/value_source */
SELECT
    BTRIM(j.journal_type::TEXT) AS journal_type,
    j.seq,
    j.account_role,
    j.debit_credit,
    j.value_source
FROM sc_mst.journal_type_coa j
WHERE j.active = 'YES'
  AND (
        j.debit_credit NOT IN ('D','K')
        OR j.value_source NOT IN
           ('NILAI','DPP','PAJAK','TOTAL','QTY','COST')
      )
ORDER BY j.journal_type, j.seq;


/* Journal type accounting YES tetapi mapping aktif tidak ada */
SELECT
    BTRIM(jt.journal_type::TEXT) AS journal_type,
    jt.module,
    jt.accounting_effect
FROM sc_mst.journal_type jt
LEFT JOIN sc_mst.journal_type_coa j
       ON j.journal_type = jt.journal_type
      AND j.active = 'YES'
WHERE jt.accounting_effect = 'YES'
GROUP BY
    jt.journal_type,
    jt.module,
    jt.accounting_effect
HAVING COUNT(j.id) = 0
ORDER BY jt.journal_type;


/* Accounting transaction tanpa journal normal */
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
       ON (
            jh.source_uniqueid = td.uniqueid
            OR jh.source_uniqueid = td.source_uniqueid
          )
      AND jh.uniqueid NOT LIKE 'JRNL-REV-%'
WHERE COALESCE(td.accounting_effect,'NO') = 'YES'
  AND td.uniqueid IS NOT NULL
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
      WHERE (
                jh.source_uniqueid = td.uniqueid
                OR jh.source_uniqueid = td.source_uniqueid
            )
        AND jh.uniqueid NOT LIKE 'JRNL-REV-%'
        AND UPPER(BTRIM(COALESCE(jd.account_role,''))) = 'TAX'
  )
ORDER BY td.docdate, td.id;


DO $$
BEGIN
    RAISE NOTICE
        'TAHAP 22 selesai : accounting configuration diperiksa';
END;
$$;


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
WHERE NULLIF(BTRIM(jd.idcoa::TEXT), '') IS NULL
   OR c.idcoa IS NULL
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
    jh.balance AS header_balance,
    ROUND(x.detail_debet - x.detail_kredit,2)
        AS expected_balance
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
WHERE ABS(
          COALESCE(jh.total_debet,0)
          - x.detail_debet
       ) > 0.01
   OR ABS(
          COALESCE(jh.total_kredit,0)
          - x.detail_kredit
       ) > 0.01
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


DO $$
BEGIN
    RAISE NOTICE
        'TAHAP 23 selesai : jurnal detail/header integrity diperiksa';
END;
$$;


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


/* Reversal tanpa journal original */
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


/* Detail source trace */
SELECT
    jd.jurnal_id,
    jd.source_uniqueid,
    COUNT(*) AS detail_count
FROM sc_trx.jurnal_dt jd
LEFT JOIN sc_trx.transaction_dt td
       ON td.uniqueid = jd.source_uniqueid
       OR td.source_uniqueid = jd.source_uniqueid
WHERE td.uniqueid IS NULL
GROUP BY jd.jurnal_id, jd.source_uniqueid
ORDER BY jd.jurnal_id;


DO $$
BEGIN
    RAISE NOTICE
        'TAHAP 24 selesai : source trace accounting diperiksa';
END;
$$;


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
WHERE (jt.direction = 'IN'
       AND td.type_in_out <> 'IN')
   OR (jt.direction = 'OUT'
       AND td.type_in_out <> 'OUT')
ORDER BY td.docdate, td.id;


/* transaction routing snapshot berbeda dari master saat ini */
SELECT
    td.uniqueid,
    td.docno,
    td.journal_type,
    td.module,
    jt.module AS master_module,
    td.direction,
    jt.direction AS master_direction,
    td.stock_effect,
    jt.stock_effect AS master_stock_effect,
    td.accounting_effect,
    jt.accounting_effect AS master_accounting_effect,
    td.asset_effect,
    jt.asset_effect AS master_asset_effect
FROM sc_trx.transaction_dt td
JOIN sc_mst.journal_type jt
  ON jt.journal_type = td.journal_type
WHERE COALESCE(td.module,'') <> COALESCE(jt.module,'')
   OR COALESCE(td.direction,'') <> COALESCE(jt.direction,'')
   OR COALESCE(td.stock_effect,'NONE')
        <> COALESCE(jt.stock_effect,'NONE')
   OR COALESCE(td.accounting_effect,'NO')
        <> COALESCE(jt.accounting_effect,'NO')
   OR COALESCE(td.asset_effect,'NONE')
        <> COALESCE(jt.asset_effect,'NONE')
ORDER BY td.docdate, td.id;


/* journal_type transaction accounting effect */
SELECT
    BTRIM(jt.journal_type::TEXT) AS journal_type,
    jt.accounting_effect,
    COUNT(td.uniqueid) AS total_transaction
FROM sc_mst.journal_type jt
LEFT JOIN sc_trx.transaction_dt td
       ON td.journal_type = jt.journal_type
GROUP BY
    jt.journal_type,
    jt.accounting_effect
ORDER BY jt.journal_type;


DO $$
BEGIN
    RAISE NOTICE
        'TAHAP 25 selesai : nilai transaksi, direction, dan routing snapshot diperiksa';
END;
$$;


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
        COALESCE(
            SUM(
                CASE
                    WHEN t.type_in_out='IN'
                    THEN COALESCE(t.qty,0)
                    ELSE 0
                END
            ),
            0
        ) AS total_qty_in,
        COALESCE(
            SUM(
                CASE
                    WHEN t.type_in_out='OUT'
                    THEN COALESCE(t.qty,0)
                    ELSE 0
                END
            ),
            0
        ) AS total_qty_out,
        COALESCE(SUM(t.bruto),0) AS total_bruto,
        COALESCE(SUM(t.discount),0) AS total_discount,
        COALESCE(SUM(t.nilai),0) AS total_nilai,
        COALESCE(SUM(t.dpp),0) AS total_dpp,
        COALESCE(SUM(t.pajak),0) AS total_pajak,
        COALESCE(SUM(t.total),0) AS total_total,
        COALESCE(SUM(t.debet),0) AS total_debet,
        COALESCE(SUM(t.kredit),0) AS total_kredit,
        ABS(
            ROUND(
                COALESCE(SUM(t.debet),0)
                -
                COALESCE(SUM(t.kredit),0),
                2
            )
        ) AS balance
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
ORDER BY
    e.docno,
    e.journal_type,
    e.type_in_out;


/* transaction_hd duplicate grouping */
SELECT
    docno,
    doctype,
    journal_type,
    type_in_out,
    idbranch,
    ref_docno,
    ref_doctype,
    COUNT(*) AS duplicate_header
FROM sc_trx.transaction_hd
GROUP BY
    docno,
    doctype,
    journal_type,
    type_in_out,
    idbranch,
    ref_docno,
    ref_doctype
HAVING COUNT(*) > 1
ORDER BY
    docno,
    doctype,
    journal_type;


DO $$
BEGIN
    RAISE NOTICE
        'TAHAP 26 selesai : transaction_hd vs transaction_dt diperiksa';
END;
$$;


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
       OR (
            COALESCE(td.debet,0) > 0
            AND COALESCE(td.kredit,0) > 0
          );


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
    FROM
    (
        SELECT DISTINCT
            td.docno,
            td.doctype,
            td.journal_type,
            td.type_in_out,
            td.idbranch,
            td.cabang,
            td.ref_docno,
            td.ref_doctype
        FROM sc_trx.transaction_dt td
    ) x
    LEFT JOIN sc_trx.transaction_hd th
      ON th.docno IS NOT DISTINCT FROM x.docno
     AND th.doctype IS NOT DISTINCT FROM x.doctype
     AND th.journal_type IS NOT DISTINCT FROM x.journal_type
     AND th.type_in_out IS NOT DISTINCT FROM x.type_in_out
     AND th.idbranch IS NOT DISTINCT FROM x.idbranch
     AND th.cabang IS NOT DISTINCT FROM x.cabang
     AND th.ref_docno IS NOT DISTINCT FROM x.ref_docno
     AND th.ref_doctype IS NOT DISTINCT FROM x.ref_doctype
    WHERE th.docno IS NULL;


    RAISE NOTICE
        '============================================================';

    RAISE NOTICE
        'TAHAP 20 s/d 27 : READ-ONLY AUDIT';

    RAISE NOTICE
        'transaction_dt invalid  : %',
        v_tx_invalid;

    RAISE NOTICE
        'stkblc invalid           : %',
        v_stock_invalid;

    RAISE NOTICE
        'POSTED journal invalid   : %',
        v_journal_invalid;

    RAISE NOTICE
        'transaction header miss  : %',
        v_header_invalid;

    RAISE NOTICE
        '============================================================';

    IF v_tx_invalid = 0
       AND v_stock_invalid = 0
       AND v_journal_invalid = 0
       AND v_header_invalid = 0
    THEN
        RAISE NOTICE
            'STATUS : INTEGRITY CHECK UTAMA OK';
    ELSE
        RAISE NOTICE
            'STATUS : ADA DATA YANG PERLU DITINJAU';
        RAISE NOTICE
            'Script audit TIDAK mengubah data legacy.';
    END IF;

END;
$$;


/* ============================================================================
   TAHAP 28
   MANUAL ACCOUNTING MAPPING
   ============================================================================

   Manual journal:
       JVGENL
       UMTITP
       NDKAPD
       NDKAPK
       NDKARD
       NDKARK
       GIROIN
       GIROUT
       FXREAL
       FXUNRL
       ARWOFF
       APWOFF
       BADPRV
       BADREV
       UNEARN
       UNEREL
       PAYROL

   Untuk manual accounting:
       transaction_dt.idcoa
       transaction_dt.counter_idcoa
       transaction_dt.debet_kredit

   Audit:
       role ACCOUNT dan COUNTER_ACCOUNT harus tersedia.
   ============================================================================ */

WITH manual_types AS
(
    SELECT UNNEST(
        ARRAY[
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
        ]
    )::CHAR(6) AS journal_type
)
SELECT
    BTRIM(mt.journal_type::TEXT) AS journal_type,
    jt.module,
    jt.accounting_effect,
    COUNT(j.account_role) FILTER (
        WHERE UPPER(BTRIM(j.account_role)) = 'ACCOUNT'
    ) AS account_role_count,
    COUNT(j.account_role) FILTER (
        WHERE UPPER(BTRIM(j.account_role)) = 'COUNTER_ACCOUNT'
    ) AS counter_account_role_count
FROM manual_types mt
LEFT JOIN sc_mst.journal_type jt
       ON jt.journal_type = mt.journal_type
LEFT JOIN sc_mst.journal_type_coa j
       ON j.journal_type = mt.journal_type
      AND j.active = 'YES'
GROUP BY
    mt.journal_type,
    jt.module,
    jt.accounting_effect
ORDER BY mt.journal_type;


/* Manual transaction yang COA asal/lawan kosong */
SELECT
    td.uniqueid,
    td.docno,
    td.journal_type,
    td.idcoa AS account_coa,
    td.counter_idcoa AS counter_account_coa,
    td.debet_kredit
FROM sc_trx.transaction_dt td
WHERE TRIM(td.journal_type::TEXT) IN
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
      )
  AND (
        NULLIF(BTRIM(COALESCE(td.idcoa,'')), '') IS NULL
        OR
        NULLIF(BTRIM(COALESCE(td.counter_idcoa,'')), '') IS NULL
      )
ORDER BY td.docdate, td.id;


DO $$
BEGIN
    RAISE NOTICE
        'TAHAP 28 selesai : manual accounting mapping diperiksa';
END;
$$;


/* ============================================================================
   TAHAP 29
   TAX MAPPING / TAX JOURNAL DETAIL
   ============================================================================ */


/* Tax master component yang belum mempunyai COA */
SELECT
    BTRIM(d.idtax::TEXT) AS idtax,
    d.idgrouptax,
    d.percentation,
    BTRIM(COALESCE(d.prk_masukan::TEXT,'')) AS prk_masukan,
    BTRIM(COALESCE(d.prk_keluaran::TEXT,'')) AS prk_keluaran
FROM sc_mst.tax_dtl d
LEFT JOIN sc_mst.coa ci
       ON NULLIF(BTRIM(d.prk_masukan::TEXT),'') IS NOT NULL
      AND BTRIM(ci.idcoa::TEXT) = BTRIM(d.prk_masukan::TEXT)
LEFT JOIN sc_mst.coa co
       ON NULLIF(BTRIM(d.prk_keluaran::TEXT),'') IS NOT NULL
      AND BTRIM(co.idcoa::TEXT) = BTRIM(d.prk_keluaran::TEXT)
WHERE d.status = 'P'
  AND (
        (
            NULLIF(BTRIM(d.prk_masukan::TEXT),'') IS NOT NULL
            AND ci.idcoa IS NULL
        )
        OR
        (
            NULLIF(BTRIM(d.prk_keluaran::TEXT),'') IS NOT NULL
            AND co.idcoa IS NULL
        )
      )
ORDER BY d.idtax, d.id;


/* Tax detail aktual vs tax master */
SELECT
    td.uniqueid,
    td.docno,
    td.journal_type,
    td.idtax,
    td.dpp,
    td.pajak,
    COUNT(jd.id) AS tax_journal_lines,
    STRING_AGG(
        DISTINCT BTRIM(jd.idcoa::TEXT),
        ', ' ORDER BY BTRIM(jd.idcoa::TEXT)
    ) AS tax_journal_coa
FROM sc_trx.transaction_dt td
LEFT JOIN sc_trx.jurnal_hd jh
       ON (
            jh.source_uniqueid = td.uniqueid
            OR jh.source_uniqueid = td.source_uniqueid
          )
      AND jh.uniqueid NOT LIKE 'JRNL-REV-%'
LEFT JOIN sc_trx.jurnal_dt jd
       ON jd.jurnal_id = jh.id
      AND UPPER(BTRIM(COALESCE(jd.account_role,''))) = 'TAX'
WHERE COALESCE(td.pajak,0) > 0
  AND COALESCE(td.accounting_effect,'NO') = 'YES'
GROUP BY
    td.uniqueid,
    td.docno,
    td.journal_type,
    td.idtax,
    td.dpp,
    td.pajak
HAVING COUNT(jd.id) = 0
ORDER BY MIN(td.docdate), td.uniqueid;


/* Multi-component tax candidates */
SELECT
    td.uniqueid,
    td.docno,
    td.journal_type,
    td.idtax,
    COUNT(d.id) AS active_tax_component_count
FROM sc_trx.transaction_dt td
JOIN sc_mst.tax_dtl d
  ON TRIM(d.idtax) = TRIM(COALESCE(td.idtax,'NON'))
 AND d.status = 'P'
WHERE COALESCE(td.pajak,0) > 0
GROUP BY
    td.uniqueid,
    td.docno,
    td.journal_type,
    td.idtax
HAVING COUNT(d.id) > 1
ORDER BY td.docno, td.uniqueid;


DO $$
BEGIN
    RAISE NOTICE
        'TAHAP 29 selesai : tax master dan tax journal detail diperiksa';
END;
$$;


/* ============================================================================
   TAHAP 30
   ACCOUNTING COST SOURCE
   ============================================================================

   Priority TAHAP 16:
       1. stkblc.uniqueid
       2. stkblc.docno + ref_doctype
       3. stkblc_avgcost.idbarang + idlocation + batch

   Test ini READ-ONLY dan hanya menampilkan transaksi COST yang perlu ditinjau.
   ============================================================================ */

WITH cost_mapping AS
(
    SELECT DISTINCT
        BTRIM(j.journal_type::TEXT) AS journal_type
    FROM sc_mst.journal_type_coa j
    WHERE j.active = 'YES'
      AND UPPER(BTRIM(j.account_role)) IN ('COGS','HPP','INVENTORY','STOCK')
      AND UPPER(BTRIM(j.value_source)) = 'COST'
),
tx AS
(
    SELECT td.*
    FROM sc_trx.transaction_dt td
    JOIN cost_mapping cm
      ON cm.journal_type = BTRIM(td.journal_type::TEXT)
    WHERE COALESCE(td.accounting_effect,'NO') = 'YES'
),
cost_source AS
(
    SELECT
        tx.uniqueid,
        tx.docno,
        tx.doctype,
        tx.journal_type,
        tx.idbarang,
        tx.warehouse,
        tx.batch,
        CASE
            WHEN COALESCE((
                SELECT SUM(sb.totalcost)
                FROM sc_trx.stkblc sb
                WHERE sb.uniqueid = tx.uniqueid
            ),0) <> 0
                THEN 'STKBLC_UNIQUEID'

            WHEN NULLIF(BTRIM(COALESCE(tx.ref_docno,'')),'') IS NOT NULL
             AND COALESCE((
                SELECT SUM(sb.totalcost)
                FROM sc_trx.stkblc sb
                WHERE sb.docno = tx.ref_docno
                  AND sb.doctype = COALESCE(tx.ref_doctype,'')
            ),0) <> 0
                THEN 'STKBLC_REFERENCE'

            WHEN NULLIF(BTRIM(COALESCE(tx.idbarang::TEXT,'')),'') IS NOT NULL
             AND NULLIF(BTRIM(COALESCE(tx.warehouse::TEXT,'')),'') IS NOT NULL
             AND EXISTS
             (
                SELECT 1
                FROM sc_trx.stkblc_avgcost ac
                WHERE BTRIM(ac.idbarang::TEXT)
                      = BTRIM(tx.idbarang::TEXT)
                  AND BTRIM(ac.idlocation::TEXT)
                      = BTRIM(tx.warehouse::TEXT)
                  AND BTRIM(COALESCE(ac.batch,''))
                      = BTRIM(COALESCE(tx.batch::TEXT,''))
             )
                THEN 'AVG_COST'

            ELSE 'NO_COST_SOURCE'
        END AS cost_source
    FROM tx
)
SELECT *
FROM cost_source
WHERE cost_source = 'NO_COST_SOURCE'
ORDER BY docno, uniqueid;


/* COST journal line harus mempunyai nominal bila transaction COST mempunyai qty */
SELECT
    td.uniqueid,
    td.docno,
    td.journal_type,
    td.qty,
    SUM(
        CASE
            WHEN UPPER(BTRIM(jd.account_role)) IN
                 ('COGS','HPP','INVENTORY','STOCK')
             THEN COALESCE(jd.debet,0) + COALESCE(jd.kredit,0)
            ELSE 0
        END
    ) AS cost_journal_value
FROM sc_trx.transaction_dt td
JOIN sc_trx.jurnal_hd jh
  ON (
        jh.source_uniqueid = td.uniqueid
        OR jh.source_uniqueid = td.source_uniqueid
     )
 AND jh.uniqueid NOT LIKE 'JRNL-REV-%'
JOIN sc_trx.jurnal_dt jd
  ON jd.jurnal_id = jh.id
WHERE COALESCE(td.accounting_effect,'NO') = 'YES'
GROUP BY
    td.uniqueid,
    td.docno,
    td.journal_type,
    td.qty
HAVING
    COALESCE(td.qty,0) > 0
    AND COUNT(*) FILTER (
        WHERE UPPER(BTRIM(jd.value_source)) = 'COST'
    ) > 0
    AND SUM(
        CASE
            WHEN UPPER(BTRIM(jd.value_source)) = 'COST'
            THEN COALESCE(jd.debet,0) + COALESCE(jd.kredit,0)
            ELSE 0
        END
    ) = 0
ORDER BY MIN(td.docdate), td.uniqueid;


DO $$
BEGIN
    RAISE NOTICE
        'TAHAP 30 selesai : source COST accounting diperiksa';
END;
$$;


/* ============================================================================
   TAHAP 31
   JSA STOCK EXCLUSION
   ============================================================================

   Rule:
       JSA -> stock_effect NONE
       JSA tetap dapat accounting sebagai SERVICE/JASA.

   Query hanya menampilkan jika ternyata JSA masuk stkblc.
   ============================================================================ */

SELECT
    td.uniqueid,
    td.docno,
    td.doctype,
    td.journal_type,
    td.idbarang,
    mb.idgroup,
    td.stock_effect,
    sb.uniqueid AS stock_uniqueid
FROM sc_trx.transaction_dt td
JOIN sc_mst.mbarang mb
  ON BTRIM(mb.idbarang::TEXT)
     = BTRIM(td.idbarang::TEXT)
LEFT JOIN sc_trx.stkblc sb
  ON sb.uniqueid = td.uniqueid
WHERE UPPER(BTRIM(COALESCE(mb.idgroup::TEXT,''))) = 'JSA'
  AND sb.uniqueid IS NOT NULL
ORDER BY td.docdate, td.id;


/* JSA accounting yang tidak menjadi stock tetap diperbolehkan */
SELECT
    COUNT(*) AS jsa_accounting_transaction
FROM sc_trx.transaction_dt td
JOIN sc_mst.mbarang mb
  ON BTRIM(mb.idbarang::TEXT)
     = BTRIM(td.idbarang::TEXT)
WHERE UPPER(BTRIM(COALESCE(mb.idgroup::TEXT,''))) = 'JSA'
  AND COALESCE(td.accounting_effect,'NO') = 'YES'
  AND COALESCE(td.stock_effect,'NONE') = 'NONE';


DO $$
BEGIN
    RAISE NOTICE
        'TAHAP 31 selesai : JSA stock exclusion diperiksa';
END;
$$;


/* ============================================================================
   TAHAP 32
   ACCOUNTING TRIGGER ORDER
   ============================================================================ */

WITH trig AS
(
    SELECT
        tgname,
        ROW_NUMBER() OVER (ORDER BY tgname) AS trigger_position
    FROM pg_trigger
    WHERE tgrelid = 'sc_trx.transaction_dt'::regclass
      AND NOT tgisinternal
      AND tgname IN
      (
          'trg_transaction_stock',
          'trg_transaction_asset',
          'trg_zz_transaction_accounting'
      )
)
SELECT
    tgname,
    trigger_position
FROM trig
ORDER BY trigger_position;


/* Expected: zz accounting harus berada setelah stock dan asset secara nama */
DO $$
DECLARE
    v_stock_pos INTEGER;
    v_asset_pos INTEGER;
    v_accounting_pos INTEGER;
BEGIN

    SELECT
        MIN(trigger_position) FILTER (
            WHERE tgname = 'trg_transaction_stock'
        ),
        MIN(trigger_position) FILTER (
            WHERE tgname = 'trg_transaction_asset'
        ),
        MIN(trigger_position) FILTER (
            WHERE tgname = 'trg_zz_transaction_accounting'
        )
    INTO
        v_stock_pos,
        v_asset_pos,
        v_accounting_pos
    FROM
    (
        SELECT
            tgname,
            ROW_NUMBER() OVER (ORDER BY tgname) AS trigger_position
        FROM pg_trigger
        WHERE tgrelid = 'sc_trx.transaction_dt'::regclass
          AND NOT tgisinternal
          AND tgname IN
          (
              'trg_transaction_stock',
              'trg_transaction_asset',
              'trg_zz_transaction_accounting'
          )
    ) x;

    IF v_accounting_pos IS NULL THEN
        RAISE NOTICE
            'TAHAP 32 NOTICE : trigger accounting tidak ditemukan.';
    ELSIF v_stock_pos IS NOT NULL
       AND v_asset_pos IS NOT NULL
       AND v_accounting_pos > v_stock_pos
       AND v_accounting_pos > v_asset_pos
    THEN
        RAISE NOTICE
            'TAHAP 32 OK : accounting trigger berada setelah stock + asset.';
    ELSE
        RAISE NOTICE
            'TAHAP 32 NOTICE : urutan trigger perlu ditinjau. stock=%, asset=%, accounting=%',
            v_stock_pos,
            v_asset_pos,
            v_accounting_pos;
    END IF;

END;
$$;


/* ============================================================================
   TAHAP 33
   REVERSAL IDEMPOTENCY / STATUS INTEGRITY
   ============================================================================ */


/* Satu original journal seharusnya tidak mempunyai lebih dari satu reversal */
SELECT
    rev.source_uniqueid,
    COUNT(*) AS reversal_count,
    STRING_AGG(rev.uniqueid, ', ' ORDER BY rev.id) AS reversal_uniqueids
FROM sc_trx.jurnal_hd rev
WHERE rev.uniqueid LIKE 'JRNL-REV-%'
GROUP BY rev.source_uniqueid
HAVING COUNT(*) > 1
ORDER BY rev.source_uniqueid;


/* Reversal POSTED harus balance */
SELECT
    rev.id,
    rev.uniqueid,
    rev.source_uniqueid,
    rev.total_debet,
    rev.total_kredit,
    rev.balance,
    rev.status
FROM sc_trx.jurnal_hd rev
WHERE rev.uniqueid LIKE 'JRNL-REV-%'
  AND (
        rev.status <> 'POSTED'
        OR ABS(COALESCE(rev.balance,0)) > 0.01
      )
ORDER BY rev.id;


/* Original yang sudah REVERSED seharusnya mempunyai reversal */
SELECT
    orig.id,
    orig.uniqueid,
    orig.source_uniqueid,
    orig.docno,
    orig.journal_type,
    orig.status
FROM sc_trx.jurnal_hd orig
WHERE orig.status = 'REVERSED'
  AND orig.uniqueid NOT LIKE 'JRNL-REV-%'
  AND NOT EXISTS
  (
      SELECT 1
      FROM sc_trx.jurnal_hd rev
      WHERE rev.uniqueid LIKE 'JRNL-REV-%'
        AND rev.source_uniqueid = orig.source_uniqueid
  )
ORDER BY orig.id;


DO $$
BEGIN
    RAISE NOTICE
        'TAHAP 33 selesai : reversal idempotency dan status integrity diperiksa';
END;
$$;


/* ============================================================================
   END
   ============================================================================ */

DO $$
BEGIN
    RAISE NOTICE '============================================================';
    RAISE NOTICE 'TAHAP 21 - 27 FINAL CLEAN + TEST TAMBAHAN 28 - 33 SELESAI';
    RAISE NOTICE 'SCRIPT 100%% READ-ONLY';
    RAISE NOTICE 'Tidak ada INSERT / UPDATE / DELETE / REBUILD / POST';
    RAISE NOTICE '============================================================';
END;
$$;
