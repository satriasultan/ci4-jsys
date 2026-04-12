/* SIMULASI INPUT LEGER
INSERT stkblc
   ↓
avg cost auto update (trigger)
   ↓
v_stk_to_gl hitung nilai
   ↓
sp_post_stk_to_gl
   ↓
jurnal terbentuk
   ↓
status stk jadi posted

 */






INSERT INTO sc_trx.stkblc (
    idlocation, idarea, batch, idbarang,
    trxdate, doctype, docno, docref,
    qty_in, pricelst_in,
    hist, ctype,
    currcode, currvalue
)
VALUES

-- =====================================
-- GR001 (IDR)
-- =====================================
('GDG01','AREA01','BATCH01','BRG001',NOW(),'GR','GR001','PO001',10,100000,'PEMBELIAN','IN','IDR',1),
('GDG01','AREA01','BATCH01','BRG002',NOW(),'GR','GR001','PO001',5,200000,'PEMBELIAN','IN','IDR',1),

-- =====================================
-- GR002 (USD)
-- =====================================
('GDG01','AREA01','BATCH02','BRG003',NOW(),'GR','GR002','PO002',10,10,'PEMBELIAN','IN','USD',15000),

-- =====================================
-- GR003 (IDR)
-- =====================================
('GDG01','AREA01','BATCH03','BRG004',NOW(),'GR','GR003','PO003',8,150000,'PEMBELIAN','IN','IDR',1),

-- =====================================
-- GR004 (USD)
-- =====================================
('GDG01','AREA01','BATCH04','BRG005',NOW(),'GR','GR004','PO004',20,5,'PEMBELIAN','IN','USD',15500);


SELECT 
    idbarang,
    SUM(qty_in - qty_out) AS qty,
    SUM(
        CASE 
            WHEN qty_in > 0 THEN qty_in * pricelst_in
            ELSE 0
        END
    ) AS total_in
FROM sc_trx.stkblc
GROUP BY idbarang;

SELECT 
    idbarang,
    idlocation,
    batch,
    qty,
    total_value,
    avg_cost
FROM sc_trx.stkblc_avgcost
ORDER BY idbarang;

SELECT 
    idcoa,
    SUM(debet - kredit) AS saldo
FROM sc_trx.jurnal_dt
WHERE idcoa = '1.2.1' -- Persediaan
GROUP BY idcoa;

SELECT 
    a.idbarang,
    a.qty,
    a.avg_cost,
    (a.qty * a.avg_cost) AS nilai_avg,

    b.nilai_jurnal,

    (a.qty * a.avg_cost) - b.nilai_jurnal AS selisih

FROM sc_trx.stkblc_avgcost a
LEFT JOIN (
    SELECT SUM(debet - kredit) AS nilai_jurnal
    FROM sc_trx.jurnal_dt
    WHERE idcoa = '1.2.1'
) b ON TRUE;


-- =========================================
-- 7. TEST RUN
-- =========================================
-- SELECT sc_trx.sp_post_gl('SYSTEM');
-- SELECT sc_trx.sp_unpost_stk_to_gl('GR001','GR');
-- SELECT sc_trx.sp_rebuild_stk_to_gl('GR001','GR','SYSTEM');
-- SELECT sc_trx.sp_rebuild_periode('2026-01-01','2026-01-31','SYSTEM');




/*
REPOST REVISI

DELETE stkblc lama
↓
UNPOST jurnal lama 🔥
↓
INSERT stkblc baru
↓
POST ulang GL 🔥
*/

CREATE OR REPLACE FUNCTION sc_trx.sp_unpost_by_doc(
    p_docno VARCHAR,
    p_doctype VARCHAR
)
RETURNS VOID
LANGUAGE plpgsql
AS $$
BEGIN

    -- DELETE DETAIL
    DELETE FROM sc_trx.jurnal_dt
    WHERE jurnal_id IN (
        SELECT id FROM sc_trx.jurnal_hd
        WHERE TRIM(docno)=TRIM(p_docno)
          AND TRIM(doctype)=TRIM(p_doctype)
    );

    -- DELETE HEADER
    DELETE FROM sc_trx.jurnal_hd
    WHERE TRIM(docno)=TRIM(p_docno)
      AND TRIM(doctype)=TRIM(p_doctype);

    -- RESET STKBLC
    UPDATE sc_trx.stkblc
    SET is_posted = FALSE,
        posted_at = NULL
    WHERE TRIM(docno)=TRIM(p_docno)
      AND TRIM(doctype)=TRIM(p_doctype);

END;
$$;