/*
stkblc (qty + currency)
   ↓
v_stk_to_gl
   ↓
currency (ambil COA)
   ↓
posting GL
   ↓
jurnal_hd / jurnal_dt 

stkblc → currency → view → posting → jurnal → laporan
*/

-- =========================================
-- 1. JURNAL TABLE
-- =========================================
CREATE TABLE IF NOT EXISTS sc_trx.jurnal_hd (
    id BIGSERIAL PRIMARY KEY,
    docno VARCHAR(50),
    doctype VARCHAR(20),
    trxdate DATE,
    total_debet NUMERIC(18,2),
    total_kredit NUMERIC(18,2),
    status VARCHAR(10) DEFAULT 'POSTED',
    createdby VARCHAR(20),
    createddate TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS sc_trx.jurnal_dt (
    id BIGSERIAL PRIMARY KEY,
    jurnal_id BIGINT,
    idcoa VARCHAR(20),
    debet NUMERIC(18,2) DEFAULT 0,
    kredit NUMERIC(18,2) DEFAULT 0,
    ref_docno VARCHAR(50),
    ref_doctype VARCHAR(20)
);


-- =========================================
-- 2. VIEW (CURRENCY + NILAI)
-- =========================================
drop view sc_trx.v_stk_to_gl;
CREATE OR REPLACE VIEW sc_trx.v_stk_to_gl AS
SELECT
    s.*,

    -- =========================
    -- NILAI
    -- =========================
    CASE 
        WHEN s.grouptype = 'NON STOCK' 
            THEN COALESCE(s.pricelst_in,0)

        WHEN s.qty_in > 0 
            THEN s.qty_in * s.pricelst_in

        ELSE s.qty_out * COALESCE(ac.avg_cost,0)
    END AS nilai,

    -- =========================
    -- NILAI IDR
    -- =========================
    CASE 
        WHEN s.grouptype = 'NON STOCK' 
            THEN COALESCE(s.pricelst_in,0) * COALESCE(s.currvalue,1)

        WHEN s.qty_in > 0 
            THEN s.qty_in * s.pricelst_in * COALESCE(s.currvalue,1)

        ELSE s.qty_out * COALESCE(ac.avg_cost,0) * COALESCE(s.currvalue,1)
    END AS nilai_idr,

    -- =========================
    -- HPP 🔥 (PINDAH KE VIEW)
    -- =========================
    CASE 
        WHEN s.qty_out > 0 AND s.grouptype = 'STOCK'
            THEN s.qty_out * COALESCE(ac.avg_cost,0)
        ELSE 0
    END AS hpp,

    -- =========================
    -- TYPE
    -- =========================
    CASE 
        WHEN s.grouptype = 'NON STOCK' THEN 'NON'
        WHEN s.qty_in > 0 THEN 'IN'
        ELSE 'OUT'
    END AS trx_type,

    -- =========================
    -- COA CURRENCY
    -- =========================
    c.phutang,
    c.ppiutang,
    c.ppendapatan,
    c.ptunai,

    -- =========================
    -- COA DEBET (STOCK vs JASA)
    -- =========================
    CASE 
        WHEN s.grouptype = 'NON STOCK' THEN 
            COALESCE(
                NULLIF(TRIM(b.pjasa), ''), 
                NULLIF(TRIM(c.ptunai), ''), 
                '5.1.1'
            )
        ELSE 
            COALESCE(
                NULLIF(TRIM(b.ppersediaan), ''), 
                NULLIF(TRIM(c.ptunai), ''), 
                '1.2.1'
            )
    END AS coa_debet,

    -- =========================
    -- COA HPP 🔥
    -- =========================
    COALESCE(
        NULLIF(TRIM(b.php), ''), 
        '5.2.1'
    ) AS coa_hpp

FROM sc_trx.stkblc s

LEFT JOIN sc_trx.stkblc_avgcost ac
    ON ac.idbarang = s.idbarang
   AND ac.idlocation = s.idlocation
   AND ac.batch = s.batch

LEFT JOIN sc_mst.currency c
    ON TRIM(c.currcode) = TRIM(s.currcode)

LEFT JOIN sc_mst.mbarang b
    ON TRIM(b.idbarang) = TRIM(s.idbarang);
	

-- =========================================
-- 3. FUNCTION POSTING GL
-- =========================================
CREATE OR REPLACE FUNCTION sc_trx.sp_post_gl(p_user VARCHAR)
RETURNS VOID
LANGUAGE plpgsql
AS $$
DECLARE
    rec RECORD;
    v_jurnal_id BIGINT;
    v_total NUMERIC;
BEGIN

FOR rec IN
    SELECT TRIM(docno) AS docno, TRIM(doctype) AS doctype, DATE(trxdate) trxdate
    FROM sc_trx.v_stk_to_gl
    WHERE is_posted = FALSE
    GROUP BY TRIM(docno), TRIM(doctype), DATE(trxdate)
LOOP

    -- =========================
    -- VALIDASI COA
    -- =========================
    IF EXISTS (
        SELECT 1
        FROM sc_trx.v_stk_to_gl
        WHERE TRIM(docno) = rec.docno
          AND phutang IS NULL
    ) THEN
        RAISE EXCEPTION 'COA currency belum diset untuk doc %', rec.docno;
    END IF;

    -- =========================
    -- TOTAL
    -- =========================
    SELECT COALESCE(SUM(nilai_idr),0)
    INTO v_total
    FROM sc_trx.v_stk_to_gl
    WHERE TRIM(docno) = rec.docno;

    -- =========================
    -- HEADER
    -- =========================
    INSERT INTO sc_trx.jurnal_hd
    (docno, doctype, trxdate, total_debet, total_kredit, createdby)
    VALUES
    (rec.docno, rec.doctype, rec.trxdate, v_total, v_total, p_user)
    RETURNING id INTO v_jurnal_id;

    -- =====================================
    -- PEMBELIAN (IN) → SUMMARY
    -- =====================================

    -- DEBET (coa_debet: persediaan / jasa)
    INSERT INTO sc_trx.jurnal_dt
    SELECT
        v_jurnal_id,
        MAX(v.coa_debet),
        SUM(v.nilai_idr),
        0,
        v.docno,
        v.doctype
    FROM sc_trx.v_stk_to_gl v
    WHERE TRIM(v.docno) = rec.docno
      AND v.trx_type = 'IN'
    GROUP BY v.docno, v.doctype;

    -- KREDIT (hutang)
    INSERT INTO sc_trx.jurnal_dt
    SELECT
        v_jurnal_id,
        MAX(v.phutang),
        0,
        SUM(v.nilai_idr),
        v.docno,
        v.doctype
    FROM sc_trx.v_stk_to_gl v
    WHERE TRIM(v.docno) = rec.docno
      AND v.trx_type = 'IN'
    GROUP BY v.docno, v.doctype;

    -- =====================================
    -- PENJUALAN (OUT)
    -- =====================================

    -- DEBET PIUTANG (DETAIL)
    INSERT INTO sc_trx.jurnal_dt
    SELECT
        v_jurnal_id,
        v.ppiutang,
        v.nilai_idr,
        0,
        v.docno,
        v.doctype
    FROM sc_trx.v_stk_to_gl v
    WHERE TRIM(v.docno) = rec.docno
      AND v.trx_type = 'OUT';

    -- KREDIT PENDAPATAN (SUMMARY)
    INSERT INTO sc_trx.jurnal_dt
    SELECT
        v_jurnal_id,
        MAX(v.ppendapatan),
        0,
        SUM(v.nilai_idr),
        v.docno,
        v.doctype
    FROM sc_trx.v_stk_to_gl v
    WHERE TRIM(v.docno) = rec.docno
      AND v.trx_type = 'OUT'
    GROUP BY v.docno, v.doctype;

    -- =====================================
    -- 🔥 HPP (AUTO)
    -- =====================================

    -- DEBET HPP
    INSERT INTO sc_trx.jurnal_dt
    SELECT
        v_jurnal_id,
        COALESCE(NULLIF(TRIM(b.php),''),'5.2.1'),
        SUM(v.qty_out * COALESCE(ac.avg_cost,0)),
        0,
        v.docno,
        v.doctype
    FROM sc_trx.stkblc v
    LEFT JOIN sc_trx.stkblc_avgcost ac
        ON ac.idbarang = v.idbarang
       AND ac.idlocation = v.idlocation
       AND ac.batch = v.batch
    LEFT JOIN sc_mst.mbarang b
        ON b.idbarang = v.idbarang
    WHERE TRIM(v.docno) = rec.docno
      AND v.qty_out > 0
      AND v.grouptype = 'STOCK'
    GROUP BY v.docno, v.doctype;

    -- KREDIT PERSEDIAAN
    INSERT INTO sc_trx.jurnal_dt
    SELECT
        v_jurnal_id,
        MAX(v.coa_debet),
        0,
        SUM(v.qty_out * COALESCE(ac.avg_cost,0)),
        v.docno,
        v.doctype
    FROM sc_trx.v_stk_to_gl v
    LEFT JOIN sc_trx.stkblc_avgcost ac
        ON ac.idbarang = v.idbarang
       AND ac.idlocation = v.idlocation
       AND ac.batch = v.batch
    WHERE TRIM(v.docno) = rec.docno
      AND v.trx_type = 'OUT'
      AND v.grouptype = 'STOCK'
    GROUP BY v.docno, v.doctype;

    -- =====================================
    -- VALIDASI BALANCE
    -- =====================================
    IF EXISTS (
        SELECT 1
        FROM (
            SELECT SUM(debet) d, SUM(kredit) k
            FROM sc_trx.jurnal_dt
            WHERE jurnal_id = v_jurnal_id
        ) x
        WHERE x.d <> x.k
    ) THEN
        RAISE EXCEPTION 'JURNAL TIDAK BALANCE %', rec.docno;
    END IF;

    -- =====================================
    -- UPDATE POSTED
    -- =====================================
    UPDATE sc_trx.stkblc
    SET is_posted = TRUE,
        posted_at = NOW()
    WHERE TRIM(docno) = rec.docno;

END LOOP;

END;
$$;

-- =========================================
-- 4. UNPOST
-- =========================================
CREATE OR REPLACE FUNCTION sc_trx.sp_unpost_stk_to_gl(
    p_docno VARCHAR,
    p_doctype VARCHAR
)
RETURNS VOID
LANGUAGE plpgsql
AS $$
BEGIN

    DELETE FROM sc_trx.link_stk_jurnal
    WHERE TRIM(docno) = TRIM(p_docno)
      AND TRIM(doctype) = TRIM(p_doctype);

    DELETE FROM sc_trx.jurnal_dt
    WHERE jurnal_id IN (
        SELECT id FROM sc_trx.jurnal_hd
        WHERE TRIM(docno) = TRIM(p_docno)
          AND TRIM(doctype) = TRIM(p_doctype)
    );

    DELETE FROM sc_trx.jurnal_hd
    WHERE TRIM(docno) = TRIM(p_docno)
      AND TRIM(doctype) = TRIM(p_doctype);

    UPDATE sc_trx.stkblc
    SET is_posted = FALSE,
        posted_at = NULL
    WHERE TRIM(docno) = TRIM(p_docno)
      AND TRIM(doctype) = TRIM(p_doctype);

END;
$$;

-- =========================================
-- 5. REBUILD DOC
-- =========================================
CREATE OR REPLACE FUNCTION sc_trx.sp_rebuild_stk_to_gl(
    p_docno VARCHAR,
    p_doctype VARCHAR,
    p_user VARCHAR
)
RETURNS VOID
LANGUAGE plpgsql
AS $$
BEGIN

    PERFORM sc_trx.sp_unpost_stk_to_gl(p_docno, p_doctype);

    PERFORM sc_trx.sp_post_gl(p_user);

END;
$$;

-- =========================================
-- 6. REBUILD PERIODE
-- =========================================
CREATE OR REPLACE FUNCTION sc_trx.sp_rebuild_periode(
    p_start DATE,
    p_end DATE,
    p_user VARCHAR
)
RETURNS VOID
LANGUAGE plpgsql
AS $$
BEGIN

    UPDATE sc_trx.stkblc
    SET is_posted = FALSE,
        posted_at = NULL
    WHERE DATE(trxdate) BETWEEN p_start AND p_end;

    DELETE FROM sc_trx.link_stk_jurnal
    WHERE DATE(trxdate) BETWEEN p_start AND p_end;

    DELETE FROM sc_trx.jurnal_dt
    WHERE jurnal_id IN (
        SELECT id FROM sc_trx.jurnal_hd
        WHERE trxdate BETWEEN p_start AND p_end
    );

    DELETE FROM sc_trx.jurnal_hd
    WHERE trxdate BETWEEN p_start AND p_end;

    PERFORM sc_trx.sp_post_gl(p_user);

END;
$$;

-- =========================================
-- 7. TEST RUN
-- =========================================
-- SELECT sc_trx.sp_post_gl('SYSTEM');
-- SELECT sc_trx.sp_unpost_stk_to_gl('GR001','GR');
-- SELECT sc_trx.sp_rebuild_stk_to_gl('GR001','GR','SYSTEM');
-- SELECT sc_trx.sp_rebuild_periode('2026-01-01','2026-01-31','SYSTEM');




