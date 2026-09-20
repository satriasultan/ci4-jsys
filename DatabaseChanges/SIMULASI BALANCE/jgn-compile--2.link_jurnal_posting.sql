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
-- =========================================
-- DROP VIEW
-- =========================================

DROP VIEW IF EXISTS sc_trx.v_stk_to_gl;


-- =========================================
-- VIEW STOCK TO GL
-- CURRENCY + NILAI + TAX
-- =========================================

CREATE OR REPLACE VIEW sc_trx.v_stk_to_gl AS

SELECT

    /* =====================================================
       DATA STOCK

       Semua kolom asli dari STKBLC tetap dibawa.
       Termasuk:
       idtax
       tax_percent
       isinclusive
       nilai_dpp
       nilai_ppn
       nilai_bruto
       coa_tax_masukan
       coa_tax_keluaran
    ===================================================== */

    s.*,


    /* =====================================================
       DATA BARANG
    ===================================================== */

    TRIM(b.idbarang) AS barang_idbarang,

    TRIM(b.nmbarang) AS nmbarang,

    TRIM(b.grouptype) AS barang_grouptype,


    /* =====================================================
       AVG COST
    ===================================================== */

    COALESCE(ac.avg_cost, 0) AS avg_cost,


    /* =====================================================
       NILAI DASAR TRANSAKSI

       NILAI DALAM CURRENCY TRANSAKSI
    ===================================================== */

    CASE

        /* -----------------------------------------------
           NON STOCK
        ----------------------------------------------- */

        WHEN TRIM(COALESCE(s.grouptype, '')) = 'NON STOCK'
        THEN
            COALESCE(s.qty_in, 0)
            * COALESCE(s.pricelst_in, 0)


        /* -----------------------------------------------
           STOCK MASUK
        ----------------------------------------------- */

        WHEN COALESCE(s.qty_in, 0) > 0
        THEN
            COALESCE(s.qty_in, 0)
            * COALESCE(s.pricelst_in, 0)


        /* -----------------------------------------------
           STOCK KELUAR
        ----------------------------------------------- */

        WHEN COALESCE(s.qty_out, 0) > 0
        THEN
            COALESCE(s.qty_out, 0)
            * COALESCE(ac.avg_cost, 0)


        ELSE 0

    END AS nilai,


    /* =====================================================
       NILAI IDR DASAR

       IN  = QTY × PRICE × KURS
       OUT = QTY × AVG COST
    ===================================================== */

    CASE

        WHEN COALESCE(s.qty_in, 0) > 0
        THEN

            COALESCE(s.qty_in, 0)
            * COALESCE(s.pricelst_in, 0)
            * COALESCE(s.currvalue, 1)


        WHEN COALESCE(s.qty_out, 0) > 0
        THEN

            COALESCE(s.qty_out, 0)
            * COALESCE(ac.avg_cost, 0)


        ELSE 0

    END AS nilai_idr,


    /* =====================================================
       GL NILAI DPP

       Tidak menggunakan nama nilai_dpp karena
       nilai_dpp sudah ada di s.*
    ===================================================== */

    CASE

        WHEN COALESCE(s.qty_in, 0) > 0
        THEN

            COALESCE(

                s.nilai_dpp,

                CASE

                    /* -------------------------------------
                       TAX INCLUDE
                    ------------------------------------- */

                    WHEN NULLIF(TRIM(COALESCE(s.isinclusive, '')), '') = 'YES'
                     AND COALESCE(s.tax_percent, 0) > 0

                    THEN

                        (
                            COALESCE(s.qty_in, 0)
                            * COALESCE(s.pricelst_in, 0)
                            * COALESCE(s.currvalue, 1)
                        )
                        /
                        (
                            1
                            + (
                                COALESCE(s.tax_percent, 0) / 100
                              )
                        )


                    /* -------------------------------------
                       TAX EXCLUDE / NO TAX
                    ------------------------------------- */

                    ELSE

                        COALESCE(s.qty_in, 0)
                        * COALESCE(s.pricelst_in, 0)
                        * COALESCE(s.currvalue, 1)

                END

            )


        WHEN COALESCE(s.qty_out, 0) > 0
        THEN

            COALESCE(s.qty_out, 0)
            * COALESCE(ac.avg_cost, 0)


        ELSE 0

    END AS gl_nilai_dpp,


    /* =====================================================
       GL NILAI PPN

       PRIORITAS:
       1. Snapshot nilai_ppn dari STKBLC
       2. Jika belum ada, hitung dari TAX %
    ===================================================== */

    CASE

        WHEN COALESCE(s.qty_in, 0) > 0
        THEN

            COALESCE(

                NULLIF(s.nilai_ppn, 0),

                CASE

                    /* -------------------------------------
                       TIDAK ADA TAX
                    ------------------------------------- */

                    WHEN NULLIF(TRIM(COALESCE(s.idtax, '')), '') IS NULL
                    THEN 0


                    /* -------------------------------------
                       TAX INCLUDE

                       PPN = NILAI BRUTO - DPP
                    ------------------------------------- */

                    WHEN UPPER(TRIM(COALESCE(s.isinclusive, 'NO'))) = 'YES'
                     AND COALESCE(s.tax_percent, 0) > 0

                    THEN

                        (
                            COALESCE(s.qty_in, 0)
                            * COALESCE(s.pricelst_in, 0)
                            * COALESCE(s.currvalue, 1)
                        )

                        -

                        (
                            (
                                COALESCE(s.qty_in, 0)
                                * COALESCE(s.pricelst_in, 0)
                                * COALESCE(s.currvalue, 1)
                            )

                            /

                            (
                                1
                                + (
                                    COALESCE(s.tax_percent, 0) / 100
                                  )
                            )
                        )


                    /* -------------------------------------
                       TAX EXCLUDE

                       PPN = DPP × %
                    ------------------------------------- */

                    WHEN UPPER(TRIM(COALESCE(s.isinclusive, 'NO'))) = 'NO'
                     AND COALESCE(s.tax_percent, 0) > 0

                    THEN

                        (
                            COALESCE(s.qty_in, 0)
                            * COALESCE(s.pricelst_in, 0)
                            * COALESCE(s.currvalue, 1)
                        )

                        *
                        (
                            COALESCE(s.tax_percent, 0) / 100
                        )


                    ELSE 0

                END

            )


        ELSE 0

    END AS gl_nilai_ppn,


    /* =====================================================
       GL NILAI BRUTO

       INCLUDE:
           Harga sudah termasuk PPN

       EXCLUDE:
           DPP + PPN
    ===================================================== */

    CASE

        WHEN COALESCE(s.qty_in, 0) > 0
        THEN

            COALESCE(

                s.nilai_bruto,

                CASE

                    /* -------------------------------------
                       INCLUDE
                    ------------------------------------- */

                    WHEN UPPER(TRIM(COALESCE(s.isinclusive, 'NO'))) = 'YES'

                    THEN

                        COALESCE(s.qty_in, 0)
                        * COALESCE(s.pricelst_in, 0)
                        * COALESCE(s.currvalue, 1)


                    /* -------------------------------------
                       EXCLUDE
                    ------------------------------------- */

                    WHEN UPPER(TRIM(COALESCE(s.isinclusive, 'NO'))) = 'NO'
                     AND COALESCE(s.tax_percent, 0) > 0

                    THEN

                        (
                            COALESCE(s.qty_in, 0)
                            * COALESCE(s.pricelst_in, 0)
                            * COALESCE(s.currvalue, 1)
                        )

                        +

                        (
                            (
                                COALESCE(s.qty_in, 0)
                                * COALESCE(s.pricelst_in, 0)
                                * COALESCE(s.currvalue, 1)
                            )

                            *
                            (
                                COALESCE(s.tax_percent, 0) / 100
                            )
                        )


                    ELSE

                        COALESCE(s.qty_in, 0)
                        * COALESCE(s.pricelst_in, 0)
                        * COALESCE(s.currvalue, 1)

                END

            )


        WHEN COALESCE(s.qty_out, 0) > 0
        THEN

            COALESCE(s.qty_out, 0)
            * COALESCE(ac.avg_cost, 0)


        ELSE 0

    END AS gl_nilai_bruto,


    /* =====================================================
       HPP
    ===================================================== */

    CASE

        WHEN COALESCE(s.qty_out, 0) > 0

         AND TRIM(COALESCE(s.grouptype, '')) = 'STOCK'

        THEN

            COALESCE(s.qty_out, 0)
            * COALESCE(ac.avg_cost, 0)

        ELSE 0

    END AS hpp,


    /* =====================================================
       TYPE TRANSAKSI
    ===================================================== */

    CASE

        WHEN COALESCE(s.qty_in, 0) > 0
        THEN 'IN'


        WHEN COALESCE(s.qty_out, 0) > 0
        THEN 'OUT'


        ELSE 'UNKNOWN'

    END AS trx_type,


    /* =====================================================
       COA DARI MBARANG
    ===================================================== */

    NULLIF(TRIM(b.ppersediaan), '') AS ppersediaan,

    NULLIF(TRIM(b.pjasa), '') AS pjasa,

    NULLIF(TRIM(b.salesakun), '') AS salesakun,

    NULLIF(TRIM(b.pcogs), '') AS pcogs,

    NULLIF(TRIM(b.phpproduksi), '') AS phpproduksi,

    NULLIF(TRIM(b.pwaste), '') AS pwaste,


    /* =====================================================
       COA DEBET UTAMA

       NON STOCK → PJASA
       STOCK     → PERSEDIAAN
    ===================================================== */

    CASE

        WHEN TRIM(COALESCE(s.grouptype, '')) = 'NON STOCK'

        THEN NULLIF(TRIM(b.pjasa), '')


        ELSE NULLIF(TRIM(b.ppersediaan), '')

    END AS coa_debet,


    /* =====================================================
       COA HPP
    ===================================================== */

    NULLIF(TRIM(b.pcogs), '') AS coa_hpp,


    /* =====================================================
       COA CURRENCY
    ===================================================== */

    NULLIF(TRIM(c.phutang), '') AS phutang,

    NULLIF(TRIM(c.ppiutang), '') AS ppiutang,

    NULLIF(TRIM(c.ppendapatan), '') AS ppendapatan,

    NULLIF(TRIM(c.ptunai), '') AS ptunai


FROM sc_trx.stkblc s


/* =====================================================
   RELASI BARANG
===================================================== */

LEFT JOIN sc_mst.mbarang b

    ON TRIM(b.idbarang)
       =
       TRIM(s.idbarang)


/* =====================================================
   RELASI AVG COST
===================================================== */

LEFT JOIN sc_trx.stkblc_avgcost ac

    ON TRIM(ac.idbarang)
       =
       TRIM(s.idbarang)

   AND TRIM(ac.idlocation)
       =
       TRIM(s.idlocation)

   AND TRIM(COALESCE(ac.batch, ''))
       =
       TRIM(COALESCE(s.batch, ''))


/* =====================================================
   RELASI CURRENCY
===================================================== */

LEFT JOIN sc_mst.currency c

    ON TRIM(c.currcode)
       =
       TRIM(s.currcode);

-- =========================================
-- 3. FUNCTION POSTING GL
-- =========================================
-- =========================================
-- FUNCTION POSTING GL - FIX
-- =========================================

-- =========================================
-- FUNCTION POSTING GL - FIX COA PER GROUP
-- COA CURRENCY + COA BARANG/HPP
-- =========================================

-- =========================================
-- FUNCTION POSTING GL
-- FIX DENGAN PPN / TAX
-- =========================================
-- =========================================
-- FUNCTION POSTING GL
-- STOCK IN / STOCK OUT
-- DENGAN COA CURRENCY + PPN
-- =========================================

-- =========================================================
-- FUNCTION POSTING GL
-- STOCK / NON STOCK / SALES / HPP / TAX
-- =========================================================

CREATE OR REPLACE FUNCTION sc_trx.sp_post_gl(
    p_user VARCHAR
)
RETURNS VOID
LANGUAGE plpgsql
AS $$
DECLARE

    rec RECORD;

    v_jurnal_id BIGINT;

BEGIN

    -- =====================================================
    -- LOOP DOKUMEN YANG BELUM POSTING
    -- =====================================================

    FOR rec IN

        SELECT
            TRIM(v.docno) AS docno,
            TRIM(v.doctype) AS doctype,
            DATE(v.trxdate) AS trxdate

        FROM sc_trx.v_stk_to_gl v

        WHERE COALESCE(v.is_posted, FALSE) = FALSE

        GROUP BY
            TRIM(v.docno),
            TRIM(v.doctype),
            DATE(v.trxdate)

    LOOP


        -- =================================================
        -- CEK JURNAL SUDAH ADA
        -- =================================================

        IF EXISTS (

            SELECT 1
            FROM sc_trx.jurnal_hd jh
            WHERE TRIM(jh.docno) = rec.docno
              AND TRIM(jh.doctype) = rec.doctype

        ) THEN

            RAISE EXCEPTION
                'Jurnal sudah ada untuk dokumen % (%)',
                rec.docno,
                rec.doctype;

        END IF;


        -- =================================================
        -- VALIDASI COA UTAMA
        -- =================================================

        IF EXISTS (

            SELECT 1

            FROM sc_trx.v_stk_to_gl v

            WHERE TRIM(v.docno) = rec.docno
              AND TRIM(v.doctype) = rec.doctype

              AND (

                    -- STOCK / NON STOCK MASUK
                    (
                        v.trx_type = 'IN'
                        AND NULLIF(TRIM(v.coa_debet), '') IS NULL
                    )

                    OR

                    -- HUTANG
                    (
                        v.trx_type = 'IN'
                        AND NULLIF(TRIM(v.phutang), '') IS NULL
                    )

                    OR

                    -- PPN MASUKAN
                    (
                        COALESCE(v.nilai_ppn, 0) > 0
                        AND NULLIF(TRIM(v.coa_tax_masukan), '') IS NULL
                    )

                    OR

                    -- PIUTANG
                    (
                        v.trx_type = 'OUT'
                        AND NULLIF(TRIM(v.ppiutang), '') IS NULL
                    )

                    OR

                    -- PENDAPATAN
                    (
                        v.trx_type = 'OUT'
                        AND NULLIF(TRIM(v.ppendapatan), '') IS NULL
                    )

                    OR

                    -- HPP
                    (
                        v.trx_type = 'OUT'
                        AND TRIM(COALESCE(v.grouptype, '')) = 'STOCK'
                        AND NULLIF(TRIM(v.coa_hpp), '') IS NULL
                    )

              )

        ) THEN

            RAISE EXCEPTION
                'COA belum lengkap untuk dokumen %',
                rec.docno;

        END IF;


        -- =================================================
        -- INSERT JOURNAL HEADER
        -- TOTAL AKAN DIUPDATE SETELAH DETAIL SELESAI
        -- =================================================

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
            rec.docno,
            rec.doctype,
            rec.trxdate,
            0,
            0,
            'P',
            p_user,
            NOW()
        )

        RETURNING id INTO v_jurnal_id;


        -- =================================================
        -- =================================================
        -- PEMBELIAN / LPB / STOCK IN
        -- =================================================
        -- =================================================


        -- =================================================
        -- DEBET PERSEDIAAN / JASA
        --
        -- MENGGUNAKAN DPP
        -- =================================================

        INSERT INTO sc_trx.jurnal_dt
        (
            jurnal_id,
            idcoa,
            debet,
            kredit,
            ref_docno,
            ref_doctype
        )

        SELECT

            v_jurnal_id,

            TRIM(v.coa_debet),

            SUM(
                COALESCE(
                    NULLIF(v.nilai_dpp, 0),
                    v.nilai_idr
                )
            ),

            0,

            rec.docno,

            rec.doctype

        FROM sc_trx.v_stk_to_gl v

        WHERE TRIM(v.docno) = rec.docno
          AND TRIM(v.doctype) = rec.doctype
          AND v.trx_type = 'IN'

        GROUP BY
            TRIM(v.coa_debet)

        HAVING SUM(
            COALESCE(
                NULLIF(v.nilai_dpp, 0),
                v.nilai_idr
            )
        ) <> 0;


        -- =================================================
        -- DEBET PPN MASUKAN
        -- =================================================

        INSERT INTO sc_trx.jurnal_dt
        (
            jurnal_id,
            idcoa,
            debet,
            kredit,
            ref_docno,
            ref_doctype
        )

        SELECT

            v_jurnal_id,

            TRIM(v.coa_tax_masukan),

            SUM(COALESCE(v.nilai_ppn, 0)),

            0,

            rec.docno,

            rec.doctype

        FROM sc_trx.v_stk_to_gl v

        WHERE TRIM(v.docno) = rec.docno
          AND TRIM(v.doctype) = rec.doctype

          AND v.trx_type = 'IN'

          AND COALESCE(v.nilai_ppn, 0) <> 0

          AND NULLIF(TRIM(v.coa_tax_masukan), '') IS NOT NULL

        GROUP BY
            TRIM(v.coa_tax_masukan)

        HAVING SUM(COALESCE(v.nilai_ppn, 0)) <> 0;


        -- =================================================
        -- KREDIT HUTANG
        --
        -- DPP + PPN
        -- =================================================

        INSERT INTO sc_trx.jurnal_dt
        (
            jurnal_id,
            idcoa,
            debet,
            kredit,
            ref_docno,
            ref_doctype
        )

        SELECT

            v_jurnal_id,

            TRIM(v.phutang),

            0,

            SUM(

                COALESCE(
                    NULLIF(v.nilai_bruto, 0),

                    COALESCE(
                        NULLIF(v.nilai_dpp, 0),
                        v.nilai_idr
                    )

                    +

                    COALESCE(v.nilai_ppn, 0)

                )

            ),

            rec.docno,

            rec.doctype

        FROM sc_trx.v_stk_to_gl v

        WHERE TRIM(v.docno) = rec.docno
          AND TRIM(v.doctype) = rec.doctype
          AND v.trx_type = 'IN'

        GROUP BY
            TRIM(v.phutang)

        HAVING SUM(

            COALESCE(
                NULLIF(v.nilai_bruto, 0),

                COALESCE(
                    NULLIF(v.nilai_dpp, 0),
                    v.nilai_idr
                )
                +
                COALESCE(v.nilai_ppn, 0)

            )

        ) <> 0;


        -- =================================================
        -- =================================================
        -- PENJUALAN / STOCK OUT
        -- =================================================
        -- =================================================


        -- =================================================
        -- DEBET PIUTANG
        -- =================================================

        INSERT INTO sc_trx.jurnal_dt
        (
            jurnal_id,
            idcoa,
            debet,
            kredit,
            ref_docno,
            ref_doctype
        )

        SELECT

            v_jurnal_id,

            TRIM(v.ppiutang),

            SUM(v.nilai_idr),

            0,

            rec.docno,

            rec.doctype

        FROM sc_trx.v_stk_to_gl v

        WHERE TRIM(v.docno) = rec.docno
          AND TRIM(v.doctype) = rec.doctype
          AND v.trx_type = 'OUT'

        GROUP BY
            TRIM(v.ppiutang);


        -- =================================================
        -- KREDIT PENDAPATAN
        -- =================================================

        INSERT INTO sc_trx.jurnal_dt
        (
            jurnal_id,
            idcoa,
            debet,
            kredit,
            ref_docno,
            ref_doctype
        )

        SELECT

            v_jurnal_id,

            TRIM(v.ppendapatan),

            0,

            SUM(v.nilai_idr),

            rec.docno,

            rec.doctype

        FROM sc_trx.v_stk_to_gl v

        WHERE TRIM(v.docno) = rec.docno
          AND TRIM(v.doctype) = rec.doctype
          AND v.trx_type = 'OUT'

        GROUP BY
            TRIM(v.ppendapatan);


        -- =================================================
        -- DEBET HPP
        -- =================================================

        INSERT INTO sc_trx.jurnal_dt
        (
            jurnal_id,
            idcoa,
            debet,
            kredit,
            ref_docno,
            ref_doctype
        )

        SELECT

            v_jurnal_id,

            TRIM(v.coa_hpp),

            SUM(COALESCE(v.hpp, 0)),

            0,

            rec.docno,

            rec.doctype

        FROM sc_trx.v_stk_to_gl v

        WHERE TRIM(v.docno) = rec.docno
          AND TRIM(v.doctype) = rec.doctype

          AND v.trx_type = 'OUT'

          AND TRIM(COALESCE(v.grouptype, '')) = 'STOCK'

        GROUP BY
            TRIM(v.coa_hpp)

        HAVING SUM(COALESCE(v.hpp, 0)) <> 0;


        -- =================================================
        -- KREDIT PERSEDIAAN
        -- =================================================

        INSERT INTO sc_trx.jurnal_dt
        (
            jurnal_id,
            idcoa,
            debet,
            kredit,
            ref_docno,
            ref_doctype
        )

        SELECT

            v_jurnal_id,

            TRIM(v.coa_debet),

            0,

            SUM(COALESCE(v.hpp, 0)),

            rec.docno,

            rec.doctype

        FROM sc_trx.v_stk_to_gl v

        WHERE TRIM(v.docno) = rec.docno
          AND TRIM(v.doctype) = rec.doctype

          AND v.trx_type = 'OUT'

          AND TRIM(COALESCE(v.grouptype, '')) = 'STOCK'

        GROUP BY
            TRIM(v.coa_debet)

        HAVING SUM(COALESCE(v.hpp, 0)) <> 0;


        -- =================================================
        -- VALIDASI BALANCE
        -- =================================================

        IF EXISTS
        (
            SELECT 1

            FROM
            (
                SELECT

                    COALESCE(SUM(jd.debet), 0) AS total_debet,

                    COALESCE(SUM(jd.kredit), 0) AS total_kredit

                FROM sc_trx.jurnal_dt jd

                WHERE jd.jurnal_id = v_jurnal_id

            ) x

            WHERE ROUND(x.total_debet, 2)
               <> ROUND(x.total_kredit, 2)

        ) THEN

            RAISE EXCEPTION
                'JURNAL TIDAK BALANCE. DOCNO: %',
                rec.docno;

        END IF;


        -- =================================================
        -- UPDATE JOURNAL HEADER
        -- =================================================

        UPDATE sc_trx.jurnal_hd jh

        SET

            total_debet =
            (
                SELECT COALESCE(SUM(jd.debet), 0)
                FROM sc_trx.jurnal_dt jd
                WHERE jd.jurnal_id = v_jurnal_id
            ),

            total_kredit =
            (
                SELECT COALESCE(SUM(jd.kredit), 0)
                FROM sc_trx.jurnal_dt jd
                WHERE jd.jurnal_id = v_jurnal_id
            )

        WHERE jh.id = v_jurnal_id;


        -- =================================================
        -- UPDATE STOCK POSTED
        -- =================================================

        UPDATE sc_trx.stkblc s

        SET

            is_posted = TRUE,

            posted_at = NOW()

        WHERE TRIM(s.docno) = rec.docno
          AND TRIM(s.doctype) = rec.doctype;


    END LOOP;

END;

$$;
-- =========================================
-- 7. TEST RUN
-- =========================================
-- SELECT sc_trx.sp_post_gl('SYSTEM');
-- SELECT sc_trx.sp_unpost_stk_to_gl('GR001','GR');
-- SELECT sc_trx.sp_rebuild_stk_to_gl('GR001','GR','SYSTEM');
-- SELECT sc_trx.sp_rebuild_periode('2026-01-01','2026-01-31','SYSTEM');





SELECT sc_trx.sp_preview_gl('LPB/2609/PA0001');
-- =========================================================
-- FUNCTION : sc_trx.sp_preview_gl
-- PURPOSE  : Preview jurnal tanpa INSERT / UPDATE
--            Tanpa mengubah jurnal_hd, jurnal_dt, stkblc
-- =========================================================
CREATE OR REPLACE FUNCTION sc_trx.sp_preview_gl(
    p_docno VARCHAR
)
RETURNS TABLE
(
    docno        VARCHAR,
    doctype      VARCHAR,
    jurnal_type  VARCHAR,
    idcoa        VARCHAR,
    debet        NUMERIC,
    kredit       NUMERIC
)
LANGUAGE plpgsql
AS $$
BEGIN

    -- =====================================================
    -- VALIDASI
    -- =====================================================

    IF COALESCE(TRIM(p_docno), '') = '' THEN
        RAISE EXCEPTION 'DOCNO tidak boleh kosong';
    END IF;


    -- =====================================================
    -- CEK DOKUMEN
    -- =====================================================

    IF NOT EXISTS (
        SELECT 1
        FROM sc_trx.v_stk_to_gl AS v
        WHERE TRIM(v.docno) = TRIM(p_docno)
    ) THEN

        RAISE EXCEPTION
            'Data DOCNO % tidak ditemukan',
            p_docno;

    END IF;


    -- =====================================================
    -- STOCK IN
    -- DEBET PERSEDIAAN / BIAYA
    -- =====================================================

    RETURN QUERY
    SELECT
        TRIM(v.docno)::VARCHAR,
        TRIM(v.doctype)::VARCHAR,
        'DEBET PERSEDIAAN / BIAYA'::VARCHAR,
        TRIM(v.coa_debet)::VARCHAR,
        SUM(COALESCE(v.nilai_idr, 0))::NUMERIC,
        0::NUMERIC

    FROM sc_trx.v_stk_to_gl AS v

    WHERE TRIM(v.docno) = TRIM(p_docno)
      AND TRIM(v.trx_type) = 'IN'
      AND COALESCE(TRIM(v.coa_debet), '') <> ''

    GROUP BY
        TRIM(v.docno),
        TRIM(v.doctype),
        TRIM(v.coa_debet);


    -- =====================================================
    -- STOCK IN
    -- KREDIT HUTANG
    -- =====================================================

    RETURN QUERY
    SELECT
        TRIM(v.docno)::VARCHAR,
        TRIM(v.doctype)::VARCHAR,
        'KREDIT HUTANG'::VARCHAR,
        TRIM(v.phutang)::VARCHAR,
        0::NUMERIC,
        SUM(COALESCE(v.nilai_idr, 0))::NUMERIC

    FROM sc_trx.v_stk_to_gl AS v

    WHERE TRIM(v.docno) = TRIM(p_docno)
      AND TRIM(v.trx_type) = 'IN'
      AND COALESCE(TRIM(v.phutang), '') <> ''

    GROUP BY
        TRIM(v.docno),
        TRIM(v.doctype),
        TRIM(v.phutang);


    -- =====================================================
    -- STOCK OUT
    -- DEBET PIUTANG
    -- =====================================================

    RETURN QUERY
    SELECT
        TRIM(v.docno)::VARCHAR,
        TRIM(v.doctype)::VARCHAR,
        'DEBET PIUTANG'::VARCHAR,
        TRIM(v.ppiutang)::VARCHAR,
        SUM(COALESCE(v.nilai_idr, 0))::NUMERIC,
        0::NUMERIC

    FROM sc_trx.v_stk_to_gl AS v

    WHERE TRIM(v.docno) = TRIM(p_docno)
      AND TRIM(v.trx_type) = 'OUT'
      AND COALESCE(TRIM(v.ppiutang), '') <> ''

    GROUP BY
        TRIM(v.docno),
        TRIM(v.doctype),
        TRIM(v.ppiutang);


    -- =====================================================
    -- STOCK OUT
    -- KREDIT PENDAPATAN
    -- =====================================================

    RETURN QUERY
    SELECT
        TRIM(v.docno)::VARCHAR,
        TRIM(v.doctype)::VARCHAR,
        'KREDIT PENDAPATAN'::VARCHAR,
        TRIM(v.ppendapatan)::VARCHAR,
        0::NUMERIC,
        SUM(COALESCE(v.nilai_idr, 0))::NUMERIC

    FROM sc_trx.v_stk_to_gl AS v

    WHERE TRIM(v.docno) = TRIM(p_docno)
      AND TRIM(v.trx_type) = 'OUT'
      AND COALESCE(TRIM(v.ppendapatan), '') <> ''

    GROUP BY
        TRIM(v.docno),
        TRIM(v.doctype),
        TRIM(v.ppendapatan);


    -- =====================================================
    -- STOCK OUT
    -- DEBET HPP
    -- =====================================================

    RETURN QUERY
    SELECT
        TRIM(v.docno)::VARCHAR,
        TRIM(v.doctype)::VARCHAR,
        'DEBET HPP'::VARCHAR,
        TRIM(v.coa_hpp)::VARCHAR,
        SUM(COALESCE(v.hpp, 0))::NUMERIC,
        0::NUMERIC

    FROM sc_trx.v_stk_to_gl AS v

    WHERE TRIM(v.docno) = TRIM(p_docno)
      AND TRIM(v.trx_type) = 'OUT'
      AND TRIM(COALESCE(v.grouptype, '')) = 'STOCK'
      AND COALESCE(TRIM(v.coa_hpp), '') <> ''

    GROUP BY
        TRIM(v.docno),
        TRIM(v.doctype),
        TRIM(v.coa_hpp);


    -- =====================================================
    -- STOCK OUT
    -- KREDIT PERSEDIAAN
    -- =====================================================

    RETURN QUERY
    SELECT
        TRIM(v.docno)::VARCHAR,
        TRIM(v.doctype)::VARCHAR,
        'KREDIT PERSEDIAAN'::VARCHAR,
        TRIM(v.coa_debet)::VARCHAR,
        0::NUMERIC,
        SUM(COALESCE(v.hpp, 0))::NUMERIC

    FROM sc_trx.v_stk_to_gl AS v

    WHERE TRIM(v.docno) = TRIM(p_docno)
      AND TRIM(v.trx_type) = 'OUT'
      AND TRIM(COALESCE(v.grouptype, '')) = 'STOCK'
      AND COALESCE(TRIM(v.coa_debet), '') <> ''

    GROUP BY
        TRIM(v.docno),
        TRIM(v.doctype),
        TRIM(v.coa_debet);

END;
$$;