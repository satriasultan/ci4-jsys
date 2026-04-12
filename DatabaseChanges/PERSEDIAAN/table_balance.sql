-- =========================================
-- MASTER BATCH
-- =========================================
DROP TABLE IF EXISTS sc_mst.batch CASCADE;

CREATE TABLE sc_mst.batch (
    idurut BIGSERIAL PRIMARY KEY,
    idbarang VARCHAR(50) NOT NULL,
    batch VARCHAR(100) DEFAULT '',
    produksi_date DATE,
    expired_date DATE,
    status VARCHAR(10) DEFAULT 'A',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by VARCHAR(20)
);

-- =========================================
-- STOCK BALANCE (FIXED)
-- =========================================
DROP TABLE IF EXISTS sc_trx.stkblc CASCADE;

CREATE TABLE sc_trx.stkblc (
    idlocation VARCHAR(12) NOT NULL,
    idarea VARCHAR(30) NOT NULL,
    batch VARCHAR(100) DEFAULT '',
    idbarang VARCHAR(20) NOT NULL,
    trxdate TIMESTAMP NOT NULL,
    doctype VARCHAR(20) NOT NULL,
    docno VARCHAR(20) NOT NULL,
    docref VARCHAR(20) NOT NULL,

    qty_in NUMERIC(18,2) DEFAULT 0,
    qty_out NUMERIC(18,2) DEFAULT 0,
    qty_sld NUMERIC(18,2) DEFAULT 0,

    hist VARCHAR(50) NOT NULL,
    ctype VARCHAR(50) NOT NULL,

    pricelst_in NUMERIC(18,2) DEFAULT 0,
    pricelst_out NUMERIC(18,2) DEFAULT 0,
    pricelst_sld NUMERIC(18,2) DEFAULT 0,

    currcode CHAR(3) DEFAULT 'IDR',
    currvalue NUMERIC(18,4) DEFAULT 1,

    is_posted BOOLEAN DEFAULT FALSE,
    posted_at TIMESTAMP,

    picby VARCHAR(20),
    unit VARCHAR(12),
    subunit VARCHAR(12),
    description TEXT,

    idsort BIGSERIAL,

    CONSTRAINT pk_sc_trx_stkblc 
    PRIMARY KEY (idlocation, idarea, batch, idbarang, trxdate, doctype, docno, docref, hist, ctype)
);
ALTER TABLE sc_trx.stkblc
ADD COLUMN tax NUMERIC(18,2) DEFAULT 0,
ADD COLUMN disc NUMERIC(18,2) DEFAULT 0,
ADD COLUMN biaya NUMERIC(18,2) DEFAULT 0;

ALTER TABLE sc_trx.stkblc
ADD COLUMN created_at timestamp without time ZONE,
ADD COLUMN created_by character(20);

ALTER TABLE sc_trx.stkblc
ADD COLUMN idgroup character(6);
ALTER TABLE sc_trx.stkblc
ADD COLUMN grouptype character(10) 'STOCK & NON STOCK';

-- INDEX (PERFORMANCE)
CREATE INDEX idx_stk_doc ON sc_trx.stkblc(docno, doctype);
CREATE INDEX idx_stk_posted ON sc_trx.stkblc(is_posted);
CREATE UNIQUE INDEX IF NOT EXISTS idx_stkblc_unique
ON sc_trx.stkblc (docno,idbarang,idlocation,batch);
-- =========================================
-- AVG COST TABLE
-- =========================================
DROP TABLE IF EXISTS sc_trx.stkblc_avgcost CASCADE;

CREATE TABLE sc_trx.stkblc_avgcost (
    idbarang VARCHAR(20) NOT NULL,
    idlocation VARCHAR(12) NOT NULL,
    batch VARCHAR(100) DEFAULT '',

    qty NUMERIC(18,4) DEFAULT 0,
    total_value NUMERIC(18,4) DEFAULT 0,
    avg_cost NUMERIC(18,4) DEFAULT 0,

    unit VARCHAR(12),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT pk_sc_trx_stkblc_avgcost 
    PRIMARY KEY (idbarang, idlocation, batch)
);

-- =========================================
-- FUNCTION AVG COST (FIXED)
-- =========================================
CREATE OR REPLACE FUNCTION sc_trx.fn_update_avgcost()
RETURNS TRIGGER AS $$
DECLARE
    v_old_qty NUMERIC := 0;
    v_old_value NUMERIC := 0;
    v_new_qty NUMERIC;
    v_new_value NUMERIC;
    v_avg NUMERIC;
BEGIN

    -- 🔥 SKIP NON STOCK TOTAL
    IF NEW.grouptype = 'NON STOCK' THEN
        RETURN NEW;
    END IF;

    SELECT qty, total_value 
    INTO v_old_qty, v_old_value
    FROM sc_trx.stkblc_avgcost
    WHERE idbarang = NEW.idbarang
      AND idlocation = NEW.idlocation
      AND batch = NEW.batch
    FOR UPDATE;

    IF NOT FOUND THEN
        v_old_qty := 0;
        v_old_value := 0;
    END IF;

    -- IN
    IF NEW.qty_in > 0 THEN
        v_new_qty := v_old_qty + NEW.qty_in;
        v_new_value := v_old_value + (NEW.qty_in * NEW.pricelst_in);

    -- OUT
    ELSIF NEW.qty_out > 0 THEN
        v_avg := CASE WHEN v_old_qty = 0 THEN 0 ELSE v_old_value / v_old_qty END;
        v_new_qty := v_old_qty - NEW.qty_out;
        v_new_value := v_old_value - (NEW.qty_out * v_avg);

    ELSE
        RETURN NEW;
    END IF;

    v_avg := CASE WHEN v_new_qty = 0 THEN 0 ELSE v_new_value / v_new_qty END;

    INSERT INTO sc_trx.stkblc_avgcost
    VALUES (NEW.idbarang, NEW.idlocation, NEW.batch, v_new_qty, v_new_value, v_avg, NEW.unit, NOW())
    ON CONFLICT (idbarang, idlocation, batch)
    DO UPDATE SET
        qty = EXCLUDED.qty,
        total_value = EXCLUDED.total_value,
        avg_cost = EXCLUDED.avg_cost,
        updated_at = NOW();

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- TRIGGER
DROP TRIGGER IF EXISTS trg_avgcost_stkblc ON sc_trx.stkblc;

CREATE TRIGGER trg_avgcost_stkblc
AFTER INSERT ON sc_trx.stkblc
FOR EACH ROW
EXECUTE FUNCTION sc_trx.fn_update_avgcost();

-- =========================================
-- STOCK GUDANG
-- =========================================
DROP TABLE IF EXISTS sc_mst.stkgdw CASCADE;

CREATE TABLE sc_mst.stkgdw (
    idlocation VARCHAR(12) NOT NULL,
    idarea VARCHAR(30) NOT NULL,
    batch VARCHAR(100) NOT NULL,
    idbarang VARCHAR(50) NOT NULL,

    onhand NUMERIC(18,2),
    allocated NUMERIC(18,2),
    tmpalloca NUMERIC(18,2),

    docno VARCHAR(50),
    docref VARCHAR(50),

    prc_onhand NUMERIC(18,2),
    prc_allocated NUMERIC(18,2),
    prc_tmpalloca NUMERIC(18,2),

    ctype VARCHAR(50),
    unit VARCHAR(10),
    subunit VARCHAR(10),

    lasttrxdate TIMESTAMP,
    id BIGSERIAL,

    defaultcurrency CHAR(3) DEFAULT 'IDR',

    CONSTRAINT pk_stkgdw 
    PRIMARY KEY (idlocation, idarea, batch, idbarang)
);

-- =========================================
-- FUNCTION CLEAN STK (FIXED)
-- =========================================
CREATE OR REPLACE FUNCTION sc_mst.tr_mst_stkgdw()
RETURNS trigger
LANGUAGE plpgsql
AS $$
BEGIN

    IF TG_OP = 'DELETE' THEN

        DELETE FROM sc_trx.stkblc 
        WHERE idlocation = OLD.idlocation 
          AND batch = OLD.batch 
          AND idbarang = OLD.idbarang;

        DELETE FROM sc_trx.stkblc_avgcost
        WHERE idlocation = OLD.idlocation 
          AND batch = OLD.batch 
          AND idbarang = OLD.idbarang;

    END IF;

    RETURN COALESCE(NEW, OLD);
END;
$$;

-- TRIGGER
DROP TRIGGER IF EXISTS tr_mst_stkgdw ON sc_mst.stkgdw;

CREATE TRIGGER tr_mst_stkgdw
AFTER INSERT OR UPDATE OR DELETE 
ON sc_mst.stkgdw
FOR EACH ROW
EXECUTE FUNCTION sc_mst.tr_mst_stkgdw();




CREATE OR REPLACE FUNCTION sc_trx.sp_repost_lpb_to_stk(
    p_docno VARCHAR
)
RETURNS VOID
LANGUAGE plpgsql
AS $$
BEGIN

    -- =========================
	--usage SELECT sc_trx.sp_repost_lpb_to_stk('LPB/2604/PA0001');
    -- 1. DELETE EXISTING STK (REPOST MODE)
    -- =========================
    DELETE FROM sc_trx.stkblc
    WHERE TRIM(docno) = TRIM(p_docno)
      AND doctype = 'GR';

    -- =========================
    -- 2. INSERT ULANG DARI LPB
    -- =========================
    INSERT INTO sc_trx.stkblc (
    idlocation,
    idarea,
    batch,
    idbarang,
    trxdate,
    doctype,
    docno,
    docref,
    qty_in,
    pricelst_in,
    hist,
    ctype,
    currcode,
    currvalue,
    created_at,
    created_by,
    idgroup,
    grouptype
)
SELECT
    d.idgudang,
    h.cabang,
    COALESCE(NULLIF(TRIM(d.idspec),''),''),
    d.idbarang,

    trim(h.docdate)::date + CURRENT_TIME,

    'GR',
    h.docno,
    h.docno,

    -- 🔥 CORE FIX
    CASE 
        WHEN d.grouptype = 'NON STOCK' THEN 0
        ELSE d.qty
    END,

    d.harga,

    'PEMBELIAN',

    CASE 
        WHEN d.grouptype = 'NON STOCK' THEN 'NON'
        ELSE 'IN'
    END,

    h.currcode,
    h.kurs,

    NOW(),
    'SYSTEM',

    d.idgroup,
    d.grouptype

FROM sc_trx.lpb_dtl d
JOIN sc_trx.lpb h ON h.docno = d.docno
WHERE TRIM(d.docno) = TRIM(p_docno);

END;
$$;


/* KARTU STOCK 
SELECT * FROM sc_trx.v_kartu_stock
WHERE idbarang = 'BRG001'; USAGE
*/
CREATE OR REPLACE VIEW sc_trx.v_kartu_stock AS
SELECT
    s.idbarang,
    s.idlocation,
    s.idarea,
    s.batch,

    s.trxdate,
    s.doctype,
    s.docno,
    s.docref,

    s.qty_in,
    s.qty_out,

    -- =========================
    -- SALDO BERJALAN
    -- =========================
    SUM(s.qty_in - s.qty_out) OVER (
        PARTITION BY s.idbarang, s.idlocation, s.idarea, s.batch
        ORDER BY s.trxdate, s.idsort
        ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW
    ) AS saldo_qty,

    -- =========================
    -- NILAI MASUK
    -- =========================
    (s.qty_in * s.pricelst_in) AS nilai_in,

    -- =========================
    -- NILAI KELUAR (pakai avg cost)
    -- =========================
    (s.qty_out * COALESCE(ac.avg_cost,0)) AS nilai_out,

    -- =========================
    -- SALDO NILAI
    -- =========================
    SUM(
        (s.qty_in * s.pricelst_in) - 
        (s.qty_out * COALESCE(ac.avg_cost,0))
    ) OVER (
        PARTITION BY s.idbarang, s.idlocation, s.idarea, s.batch
        ORDER BY s.trxdate, s.idsort
    ) AS saldo_nilai

FROM sc_trx.stkblc s

LEFT JOIN sc_trx.stkblc_avgcost ac
    ON ac.idbarang = s.idbarang
   AND ac.idlocation = s.idlocation
   AND ac.batch = s.batch
   WHERE s.grouptype = 'STOCK';