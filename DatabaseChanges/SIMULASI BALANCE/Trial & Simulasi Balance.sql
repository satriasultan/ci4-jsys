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



/* MANUAL INSERT */

select * from sc_trx.stkblc;
select * from sc_trx.stkblc_avgcost; --persedian dan stock
select * from sc_trx.stkblc_snapshot; --jurnal posting saldo akhir per bulan
select * from sc_mst.stkgdw; --stock perarea

/*posting perkiraan jurnal */
select * from sc_trx.jurnal_hd;
select * from sc_trx.jurnal_dt;


--delete from sc_trx.stkblc where docno='LPB/2604/PA0002' and doctype='GR';

INSERT INTO sc_trx.stkblc (idlocation, idarea, batch, idbarang, trxdate, doctype, docno, docref, qty_in, qty_out, qty_sld, hist, ctype, pricelst_in, pricelst_out, pricelst_sld, currcode, currvalue, is_posted, posted_at, picby, unit, subunit, description, idsort, created_at, created_by, idgroup, grouptype, tax, disc, biaya) VALUES ('16401', '16401.0000', '', '010403A0000157', '2026-04-17 12:42:46.232572', 'GR', 'LPB/2604/PA0002', '05M/2604/PA0003', 3.00, 0.00, 0.00, 'LPB', 'IN', 3000000.00, 0.00, 0.00, 'IDR', 1.0000, true, '2026-04-17 12:42:46.232572', null, null, null, null, 44, null, null, 'BRG   ', 'STOCK     ', 0.00, 0.00, 0.00);
INSERT INTO sc_trx.stkblc (idlocation, idarea, batch, idbarang, trxdate, doctype, docno, docref, qty_in, qty_out, qty_sld, hist, ctype, pricelst_in, pricelst_out, pricelst_sld, currcode, currvalue, is_posted, posted_at, picby, unit, subunit, description, idsort, created_at, created_by, idgroup, grouptype, tax, disc, biaya) VALUES ('16401', '16401.0000', '', '010104A0000027', '2026-04-17 12:42:46.232572', 'GR', 'LPB/2604/PA0002', '05M/2604/PA0003', 5.00, 0.00, 0.00, 'LPB', 'IN', 150000.00, 0.00, 0.00, 'IDR', 1.0000, true, '2026-04-17 12:42:46.232572', null, null, null, null, 45, null, null, 'BRG   ', 'STOCK     ', 0.00, 0.00, 0.00);
INSERT INTO sc_trx.stkblc (idlocation, idarea, batch, idbarang, trxdate, doctype, docno, docref, qty_in, qty_out, qty_sld, hist, ctype, pricelst_in, pricelst_out, pricelst_sld, currcode, currvalue, is_posted, posted_at, picby, unit, subunit, description, idsort, created_at, created_by, idgroup, grouptype, tax, disc, biaya) VALUES ('16401', '16401.0000', '', '01050A00000104', '2026-04-17 12:42:46.232572', 'GR', 'LPB/2604/PA0002', '05M/2604/PA0003', 5.00, 0.00, 0.00, 'LPB', 'IN', 700000.00, 0.00, 0.00, 'IDR', 1.0000, true, '2026-04-17 12:42:46.232572', null, null, null, null, 46, null, null, 'BRG   ', 'STOCK     ', 0.00, 0.00, 0.00);
INSERT INTO sc_trx.stkblc (idlocation, idarea, batch, idbarang, trxdate, doctype, docno, docref, qty_in, qty_out, qty_sld, hist, ctype, pricelst_in, pricelst_out, pricelst_sld, currcode, currvalue, is_posted, posted_at, picby, unit, subunit, description, idsort, created_at, created_by, idgroup, grouptype, tax, disc, biaya) VALUES ('16401', '16401.0000', '', '01050A00000155', '2026-04-17 12:42:46.232572', 'GR', 'LPB/2604/PA0002', '05M/2604/PA0003', 5.00, 0.00, 0.00, 'LPB', 'IN', 1500000.00, 0.00, 0.00, 'IDR', 1.0000, true, '2026-04-17 12:42:46.232572', null, null, null, null, 47, null, null, 'BRG   ', 'STOCK     ', 0.00, 0.00, 0.00);





-- 7. TEST RUN
-- =========================================
-- SELECT sc_trx.sp_post_gl('SYSTEM');
-- SELECT sc_trx.sp_unpost_stk_to_gl('GR001','GR');
-- SELECT sc_trx.sp_rebuild_stk_to_gl('GR001','GR','SYSTEM');
-- SELECT sc_trx.sp_rebuild_periode('2026-01-01','2026-01-31','SYSTEM');



CREATE OR REPLACE FUNCTION sc_trx.sp_repost_universal(
    p_docno   VARCHAR,
    p_doctype VARCHAR,
    p_user    VARCHAR
)
RETURNS VOID
LANGUAGE plpgsql
AS $$
DECLARE
    v_doctype VARCHAR;
BEGIN

    -- =========================================
    -- NORMALISASI
    -- =========================================
    v_doctype := TRIM(COALESCE(p_doctype,'GR'));
    p_docno   := TRIM(p_docno);

    RAISE NOTICE 'REPOST START: % - %', p_docno, v_doctype;

    -- =========================================
    -- 🔥 1. HARD DELETE (ANTI DUPLICATE)
    -- =========================================
    DELETE FROM sc_trx.stkblc
    WHERE docno = p_docno
      AND doctype = v_doctype;

    -- =========================================
    -- 🔥 2. INSERT ULANG PER DOCTYPE
    -- =========================================

    ----------------------------------------------------------------
    -- 🟢 GR (LPB)
    ----------------------------------------------------------------
    IF v_doctype = 'GR' THEN

        INSERT INTO sc_trx.stkblc (
            idlocation,idarea,batch,idbarang,
            trxdate,doctype,docno,docref,
            qty_in,pricelst_in,
            currcode,currvalue,
            hist,ctype,
            idgroup,grouptype,
            uniqueid,status,is_posted,
            created_at,created_by
        )
        SELECT DISTINCT
            d.idgudang,
            d.idgudang||'.0000',
            COALESCE(d.idspec,''),
            d.idbarang,

            h.docdate::date + CURRENT_TIME,
            v_doctype,
            p_docno,
            d.docnopo,

            CASE 
                WHEN COALESCE(mb.grouptype,'STOCK')='NON STOCK' THEN 0
                ELSE COALESCE(d.qty,0)+COALESCE(d.qtybonus,0)
            END,

            COALESCE(d.harga,0),

            h.currcode,
            COALESCE(h.kurs,1),

            'LPB',
            CASE 
                WHEN COALESCE(mb.grouptype,'STOCK')='NON STOCK' THEN 'NON'
                ELSE 'IN'
            END,

            mb.idgroup,
            COALESCE(mb.grouptype,'STOCK'),

            d.uniqueid,
            'A', FALSE,
            NOW(), p_user

        FROM sc_tmp.lpb h
        JOIN sc_tmp.lpb_dtl d ON TRIM(d.docno) = TRIM(h.docno)
        LEFT JOIN sc_mst.mbarang mb ON mb.idbarang = d.idbarang
        WHERE TRIM(h.docno) = p_docno;

    ----------------------------------------------------------------
    -- 🔵 PEMAKAIAN BARANG
    ----------------------------------------------------------------
    ELSIF v_doctype = 'PMKBRG' THEN

        INSERT INTO sc_trx.stkblc (
            idlocation,idarea,batch,idbarang,
            trxdate,doctype,docno,docref,
            qty_out,pricelst_out,
            hist,ctype,
            currcode,currvalue,
            idgroup,grouptype,
            uniqueid,status,is_posted,
            created_at,created_by
        )
        SELECT DISTINCT
            d.idlocation,
            h.cabang,
            COALESCE(d.batch,''),
            d.idbarang,

            h.docdate::date + CURRENT_TIME,
            v_doctype,
            p_docno,
            p_docno,

            CASE 
                WHEN COALESCE(mb.grouptype,'STOCK')='NON STOCK' THEN 0
                ELSE COALESCE(d.qty,0)
            END,

            COALESCE(d.val,0),

            'PEMAKAIAN',
            CASE 
                WHEN COALESCE(mb.grouptype,'STOCK')='NON STOCK' THEN 'NON'
                ELSE 'OUT'
            END,

            'IDR',1,

            mb.idgroup,
            COALESCE(mb.grouptype,'STOCK'),

            d.uniqueid,
            'A', FALSE,
            NOW(), p_user

        FROM sc_tmp.pmk_brng_mst h
        JOIN sc_tmp.pmk_brng_dtl d ON TRIM(d.docno) = TRIM(h.docno)
        LEFT JOIN sc_mst.mbarang mb ON mb.idbarang = d.idbarang
        WHERE TRIM(h.docno) = p_docno;

    ----------------------------------------------------------------
    -- 🔴 SALES
    ----------------------------------------------------------------
    ELSIF v_doctype = 'SALES' THEN

        INSERT INTO sc_trx.stkblc (
            idlocation,idarea,batch,idbarang,
            trxdate,doctype,docno,docref,
            qty_out,pricelst_out,
            hist,ctype,
            currcode,currvalue,
            idgroup,grouptype,
            uniqueid,status,is_posted,
            created_at,created_by
        )
        SELECT DISTINCT
            d.idlocation,
            h.cabang,
            COALESCE(d.batch,''),
            d.idbarang,

            h.docdate::date + CURRENT_TIME,
            v_doctype,
            p_docno,
            p_docno,

            COALESCE(d.qty,0),
            COALESCE(d.harga,0),

            'PENJUALAN','OUT',

            h.currcode,
            COALESCE(h.kurs,1),

            mb.idgroup,
            COALESCE(mb.grouptype,'STOCK'),

            d.uniqueid,
            'A', FALSE,
            NOW(), p_user

        FROM sc_tmp.sales_mst h
        JOIN sc_tmp.sales_dtl d ON TRIM(d.docno) = TRIM(h.docno)
        LEFT JOIN sc_mst.mbarang mb ON mb.idbarang = d.idbarang
        WHERE TRIM(h.docno) = p_docno;

    END IF;

    -- =========================================
    -- 🔥 3. REBUILD AVG COST
    -- =========================================
    PERFORM sc_trx.sp_rebuild_avgcost_item_all(p_docno);

    -- =========================================
    -- 🔥 4. DELETE GL (BY DOCTYPE)
    -- =========================================
    DELETE FROM sc_trx.jurnal_dt
    WHERE jurnal_id IN (
        SELECT id FROM sc_trx.jurnal_hd
        WHERE docno = p_docno
          AND doctype = v_doctype
    );

    DELETE FROM sc_trx.jurnal_hd
    WHERE docno = p_docno
      AND doctype = v_doctype;

    -- =========================================
    -- 🔥 5. POST ULANG GL
    -- =========================================
    PERFORM sc_trx.sp_post_gl(p_user);

    RAISE NOTICE 'REPOST DONE: % - %', p_docno, v_doctype;

END;
$$;
