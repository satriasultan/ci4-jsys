-- =========================================
-- FULL RESET SCRIPT (STOCK + GL)
-- =========================================
-- AUTHOR: SYSTEM
-- NOTE:
-- Pilih salah satu:
-- 1. TRUNCATE (RECOMMENDED 🔥)
-- 2. DELETE (kalau ada constraint khusus)
-- =========================================


-- =========================================
-- OPTION 1: TRUNCATE (FAST & CLEAN 🔥)
-- =========================================
TRUNCATE TABLE 
    sc_trx.jurnal_dt,
    sc_trx.jurnal_hd,
    sc_trx.stkblc_avgcost,
    sc_trx.stkblc
RESTART IDENTITY CASCADE;


-- =========================================
-- OPTION 2: DELETE (SAFE MODE)
-- =========================================
-- (Uncomment jika TRUNCATE tidak bisa)

-- DELETE FROM sc_trx.jurnal_dt;
-- DELETE FROM sc_trx.jurnal_hd;
-- DELETE FROM sc_trx.stkblc_avgcost;
-- DELETE FROM sc_trx.stkblc;


-- =========================================
-- RESET SEQUENCE (OPTIONAL)
-- =========================================
DO $$
BEGIN
    IF EXISTS (
        SELECT 1 FROM information_schema.sequences 
        WHERE sequence_name = 'jurnal_hd_id_seq'
    ) THEN
        EXECUTE 'ALTER SEQUENCE sc_trx.jurnal_hd_id_seq RESTART WITH 1';
    END IF;

    IF EXISTS (
        SELECT 1 FROM information_schema.sequences 
        WHERE sequence_name = 'jurnal_dt_id_seq'
    ) THEN
        EXECUTE 'ALTER SEQUENCE sc_trx.jurnal_dt_id_seq RESTART WITH 1';
    END IF;
END $$;


-- =========================================
-- VALIDATION (OPTIONAL CHECK)
-- =========================================
-- SELECT COUNT(*) FROM sc_trx.stkblc;
-- SELECT COUNT(*) FROM sc_trx.jurnal_hd;
-- SELECT COUNT(*) FROM sc_trx.jurnal_dt;


-- =========================================
-- DONE
-- =========================================
-- SYSTEM CLEAN SUCCESS