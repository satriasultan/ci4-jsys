<?php

namespace App\Models\Report;

use CodeIgniter\Model;

class M_Report extends Model
{

    /* =========================================================
    LAPORAN WO — Teoritis + Material Release + BPNM + Penerimaan BP
    ========================================================= */
    function buildQueryLaporanWO($withLimit = false, $perpage = 25, $offset = 0)
    {
        $sql = "
        WITH parameter AS (
            SELECT
                ?::date AS tanggal_awal,
                ?::date AS tanggal_akhir,
                ?       AS cabang,
                ?       AS wono,
                ?       AS status_wo
        ),

        /* =====================================================
        BLOK 1: TEORITIS WO — MATERIAL (dari workingorder_bom_dtl)
        ===================================================== */
        teoritis_material AS (
            SELECT
                1 AS urut_jenis,
                '1. Teoritis'::varchar AS jns,
                'Out'::varchar AS jenis_transaksi,

                wo.docno            AS wono,
                wo.docdate          AS docdate_wo,
                wo.docdatefinish    AS finish_date,
                wo.status           AS status_wo,
                wo.cabang           AS cabang,
                wo.pemohon          AS pemohon,

                wb.docno            AS bomno,
                wb.desc_bom         AS keterangan_bom,

                wd.doctype_detail   AS jenis_detail,
                wd.idbarang         AS idbarang,
                wd.nmbarang         AS nmbarang,
                wd.description      AS description,
                wd.qty              AS qty,
                'D'::varchar        AS dk,
                wd.unit             AS unit,
                wd.standartcost     AS hrg_stdcost,
                wd.totalcost        AS total_cost,
                wd.currcode         AS currcode,
                wd.uniqueid         AS uniqueid,

                wo.docno       AS docno_realisasi,
                NULL::varchar       AS batchno,
                NULL::varchar       AS spec,
                NULL::bigint        AS idurut,

                wd.inputby          AS inputby,
                wd.inputdate        AS inputdate

            FROM sc_trx.workingorder_mst wo
            JOIN sc_trx.workingorder_bom_mst wb
                ON TRIM(wb.docref) = TRIM(wo.docno)
            JOIN sc_trx.workingorder_bom_dtl wd
                ON TRIM(wd.docref) = TRIM(wo.docno)
            AND TRIM(wd.docno)  = TRIM(wb.docno)
            CROSS JOIN parameter p
            WHERE wo.status NOT IN ('C', 'D')
            AND UPPER(TRIM(wd.doctype_detail)) = 'MATERIAL'
            AND (p.cabang  IS NULL OR TRIM(p.cabang)  = '' OR TRIM(wo.cabang) = TRIM(p.cabang))
            AND (p.wono    IS NULL OR TRIM(p.wono)    = '' OR TRIM(wo.docno)  = TRIM(p.wono))
            AND (p.status_wo IS NULL OR TRIM(p.status_wo) = ''
                OR (p.status_wo = 'A' AND UPPER(TRIM(wo.status)) = 'A')
                OR (p.status_wo = 'NC' AND UPPER(TRIM(wo.status)) <> 'A'))
            AND wo.docdate >= p.tanggal_awal
            AND wo.docdate <= p.tanggal_akhir
        ),

        /* =====================================================
        BLOK 2: TEORITIS WO — COST (dari workingorder_bom_dtl)
        ===================================================== */
        teoritis_cost AS (
            SELECT
                2 AS urut_jenis,
                '1. Teoritis'::varchar AS jns,
                'Out'::varchar AS jenis_transaksi,

                wo.docno            AS wono,
                wo.docdate          AS docdate_wo,
                wo.docdatefinish    AS finish_date,
                wo.status           AS status_wo,
                wo.cabang           AS cabang,
                wo.pemohon          AS pemohon,

                wb.docno            AS bomno,
                wb.desc_bom         AS keterangan_bom,

                wd.doctype_detail   AS jenis_detail,
                wd.idbarang         AS idbarang,
                wd.nmbarang         AS nmbarang,
                wd.description      AS description,
                wd.qty              AS qty,
                'D'::varchar        AS dk,
                wd.unit             AS unit,
                wd.standartcost     AS hrg_stdcost,
                wd.totalcost        AS total_cost,
                wd.currcode         AS currcode,
                wd.uniqueid         AS uniqueid,

                wo.docno       AS docno_realisasi,
                NULL::varchar       AS batchno,
                NULL::varchar       AS spec,
                NULL::bigint        AS idurut,

                wd.inputby          AS inputby,
                wd.inputdate        AS inputdate

            FROM sc_trx.workingorder_mst wo
            JOIN sc_trx.workingorder_bom_mst wb
                ON TRIM(wb.docref) = TRIM(wo.docno)
            JOIN sc_trx.workingorder_bom_dtl wd
                ON TRIM(wd.docref) = TRIM(wo.docno)
            AND TRIM(wd.docno)  = TRIM(wb.docno)
            CROSS JOIN parameter p
            WHERE wo.status NOT IN ('C', 'D')
            AND UPPER(TRIM(wd.doctype_detail)) = 'COST'
            AND (p.cabang  IS NULL OR TRIM(p.cabang)  = '' OR TRIM(wo.cabang) = TRIM(p.cabang))
            AND (p.wono    IS NULL OR TRIM(p.wono)    = '' OR TRIM(wo.docno)  = TRIM(p.wono))
            AND (p.status_wo IS NULL OR TRIM(p.status_wo) = ''
                OR (p.status_wo = 'A' AND UPPER(TRIM(wo.status)) = 'A')
                OR (p.status_wo = 'NC' AND UPPER(TRIM(wo.status)) <> 'A'))
            AND wo.docdate >= p.tanggal_awal
            AND wo.docdate <= p.tanggal_akhir
        ),

        /* =====================================================
        BLOK 3: TEORITIS MR — BARANG JADI
        FG dari bom_mst, desc_bom dari workingorder_bom_mst
        hrg_stdcost = SUM(row WO Teoritis) ÷ qty MR
        ===================================================== */
        teoritis_mr AS (
            SELECT
                3 AS urut_jenis,
                '1. Teoritis'::varchar AS jns,
                'In'::varchar AS jenis_transaksi,

                wo.docno            AS wono,
                wo.docdate          AS docdate_wo,
                wo.docdatefinish    AS finish_date,
                wo.status           AS status_wo,
                wo.cabang           AS cabang,
                wo.pemohon          AS pemohon,

                mr.bomno            AS bomno,
                COALESCE(wbm.desc_bom, mr.desc_bom, '') AS keterangan_bom,

                'MATERIAL'::varchar AS jenis_detail,

                bm.idbarang_jadi    AS idbarang,
                COALESCE(mb.nmbarang, bm.nmbarang_jadi, '') AS nmbarang,

                mr.desc_bom         AS description,
                -mr.buildfor        AS qty,
                'K'::varchar        AS dk,
                mr.buildunit        AS unit,

                COALESCE(
                    (
                        SELECT SUM(wd_inner.totalcost) / NULLIF(mr.buildfor, 0)
                        FROM sc_trx.workingorder_bom_dtl wd_inner
                        WHERE TRIM(wd_inner.docref) = TRIM(wo.docno)
                    ),
                    0
                ) AS hrg_stdcost,

                COALESCE(
                    (
                        SELECT SUM(wd_inner.totalcost) / NULLIF(mr.buildfor, 0)
                        FROM sc_trx.workingorder_bom_dtl wd_inner
                        WHERE TRIM(wd_inner.docref) = TRIM(wo.docno)
                    ),
                    0
                ) * (-mr.buildfor) AS total_cost,

                NULL::varchar       AS currcode,
                NULL::varchar       AS uniqueid,

                mr.docno            AS docno_realisasi,
                mr.batchno          AS batchno,
                NULL::varchar       AS spec,
                NULL::bigint        AS idurut,

                mr.inputby          AS inputby,
                mr.inputdate        AS inputdate

            FROM sc_trx.workingorder_mst wo
            JOIN sc_trx.materialrelease_mst mr
                ON TRIM(mr.wono) = TRIM(wo.docno)
            JOIN sc_trx.bom_mst bm
                ON TRIM(bm.docno) = TRIM(mr.bomno)
            LEFT JOIN sc_trx.workingorder_bom_mst wbm
                ON TRIM(wbm.docno) = TRIM(mr.bomno)
            LEFT JOIN sc_mst.mbarang mb
                ON TRIM(mb.idbarang) = TRIM(bm.idbarang_jadi)
            CROSS JOIN parameter p
            WHERE wo.status NOT IN ('C', 'D')
            AND (p.cabang  IS NULL OR TRIM(p.cabang)  = '' OR TRIM(wo.cabang) = TRIM(p.cabang))
            AND (p.wono    IS NULL OR TRIM(p.wono)    = '' OR TRIM(wo.docno)  = TRIM(p.wono))
            AND (p.status_wo IS NULL OR TRIM(p.status_wo) = ''
                OR (p.status_wo = 'A' AND UPPER(TRIM(wo.status)) = 'A')
                OR (p.status_wo = 'NC' AND UPPER(TRIM(wo.status)) <> 'A'))
            AND wo.docdate >= p.tanggal_awal
            AND wo.docdate <= p.tanggal_akhir
        ),

        /* =====================================================
        BLOK 4: REALISASI MR — MATERIAL OUT
        ===================================================== */
        realisasi_mr AS (
            SELECT
                4 AS urut_jenis,
                '2. Realisasi'::varchar AS jns,
                'Out'::varchar AS jenis_transaksi,

                wo.docno            AS wono,
                wo.docdate          AS docdate_wo,
                wo.docdatefinish    AS finish_date,
                wo.status           AS status_wo,
                wo.cabang           AS cabang,
                wo.pemohon          AS pemohon,

                mr.bomno            AS bomno,
                COALESCE(wbm.desc_bom, mr.desc_bom, '') AS keterangan_bom,

                mrd.doctype_detail  AS jenis_detail,
                mrd.idbarang        AS idbarang,
                mrd.nmbarang        AS nmbarang,
                mrd.description     AS description,
                -mrd.qty            AS qty,
                'K'::varchar        AS dk,
                mrd.unit            AS unit,
                COALESCE(sc.newcost, 0) AS hrg_stdcost,
                (-mrd.qty * COALESCE(sc.newcost, 0)) AS total_cost,
                NULL::varchar       AS currcode,
                mrd.uniqueid        AS uniqueid,

                mr.docno            AS docno_realisasi,
                mr.batchno          AS batchno,
                mrd.spec            AS spec,
                mrd.idurut          AS idurut,

                mrd.inputby         AS inputby,
                mrd.inputdate       AS inputdate

            FROM sc_trx.workingorder_mst wo
            JOIN sc_trx.materialrelease_mst mr
                ON TRIM(mr.wono) = TRIM(wo.docno)
            JOIN sc_trx.materialrelease_dtl mrd
                ON TRIM(mrd.docref) = TRIM(mr.woeno)
            LEFT JOIN sc_trx.workingorder_bom_mst wbm
                ON TRIM(wbm.docno) = TRIM(mr.bomno)
            LEFT JOIN LATERAL (
                SELECT s.newcost
                FROM sc_mst.standart_cost_dtl s
                WHERE TRIM(s.idbarang) = TRIM(mrd.idbarang)
                AND s.status = 'F'
                AND s.activedate <= wo.docdate
                ORDER BY s.activedate DESC, s.inputdate DESC, s.uniqueid DESC
                LIMIT 1
            ) sc ON TRUE
            CROSS JOIN parameter p
            WHERE wo.status NOT IN ('C', 'D')
            AND (p.cabang  IS NULL OR TRIM(p.cabang)  = '' OR TRIM(wo.cabang) = TRIM(p.cabang))
            AND (p.wono    IS NULL OR TRIM(p.wono)    = '' OR TRIM(wo.docno)  = TRIM(p.wono))
            AND (p.status_wo IS NULL OR TRIM(p.status_wo) = ''
                OR (p.status_wo = 'A' AND UPPER(TRIM(wo.status)) = 'A')
                OR (p.status_wo = 'NC' AND UPPER(TRIM(wo.status)) <> 'A'))
            AND wo.docdate >= p.tanggal_awal
            AND wo.docdate <= p.tanggal_akhir
        ),

        /* =====================================================
        BLOK 5: REALISASI BPNM — COST OUT
        ===================================================== */
        realisasi_bpnm AS (
            SELECT
                5 AS urut_jenis,
                '2. Realisasi'::varchar AS jns,
                'Out'::varchar AS jenis_transaksi,

                wo.docno            AS wono,
                wo.docdate          AS docdate_wo,
                wo.docdatefinish    AS finish_date,
                wo.status           AS status_wo,
                wo.cabang           AS cabang,
                wo.pemohon          AS pemohon,

                bp.bomno            AS bomno,
                COALESCE(wbm.desc_bom, bp.desc_bom, '') AS keterangan_bom,

                bpd.doctype_detail  AS jenis_detail,
                bpd.idbarang        AS idbarang,
                bpd.nmbarang        AS nmbarang,
                bpd.description     AS description,
                -bpd.qty            AS qty,
                'K'::varchar        AS dk,
                bpd.unit            AS unit,
                bpd.harga           AS hrg_stdcost,
                -bpd.nilai          AS total_cost,
                NULL::varchar       AS currcode,
                bpd.uniqueid        AS uniqueid,

                bp.docno            AS docno_realisasi,
                bp.batchno          AS batchno,
                bpd.spec            AS spec,
                bpd.idurut          AS idurut,

                bpd.inputby         AS inputby,
                bpd.inputdate       AS inputdate

            FROM sc_trx.workingorder_mst wo
            JOIN sc_trx.bpnm_mst bp
                ON TRIM(bp.wono) = TRIM(wo.docno)
            JOIN sc_trx.bpnm_dtl bpd
                ON TRIM(bpd.docref) = TRIM(bp.woeno)
            LEFT JOIN sc_trx.workingorder_bom_mst wbm
                ON TRIM(wbm.docno) = TRIM(bp.bomno)
            CROSS JOIN parameter p
            WHERE wo.status NOT IN ('C', 'D')
            AND (p.cabang  IS NULL OR TRIM(p.cabang)  = '' OR TRIM(wo.cabang) = TRIM(p.cabang))
            AND (p.wono    IS NULL OR TRIM(p.wono)    = '' OR TRIM(wo.docno)  = TRIM(p.wono))
            AND (p.status_wo IS NULL OR TRIM(p.status_wo) = ''
                OR (p.status_wo = 'A' AND UPPER(TRIM(wo.status)) = 'A')
                OR (p.status_wo = 'NC' AND UPPER(TRIM(wo.status)) <> 'A'))
            AND wo.docdate >= p.tanggal_awal
            AND wo.docdate <= p.tanggal_akhir
        ),

        /* =====================================================
        BLOK 6: REALISASI PENERIMAAN BP — BARANG JADI IN
        ===================================================== */
        realisasi_penerimaan AS (
            SELECT
                6 AS urut_jenis,
                '2. Realisasi'::varchar AS jns,
                'In'::varchar AS jenis_transaksi,

                wo.docno            AS wono,
                wo.docdate          AS docdate_wo,
                wo.docdatefinish    AS finish_date,
                wo.status           AS status_wo,
                wo.cabang           AS cabang,
                wo.pemohon          AS pemohon,

                pb.bomno            AS bomno,
                COALESCE(wbm.desc_bom, pb.desc_bom, '') AS keterangan_bom,

                pbd.doctype_detail  AS jenis_detail,
                pbd.idbarang        AS idbarang,
                pbd.nmbarang        AS nmbarang,
                pbd.description     AS description,
                pbd.qty             AS qty,
                'D'::varchar        AS dk,
                pbd.unit            AS unit,
                pbd.harga           AS hrg_stdcost,
                pbd.nilai           AS total_cost,
                NULL::varchar       AS currcode,
                pbd.uniqueid        AS uniqueid,

                pb.docno            AS docno_realisasi,
                pb.batchno          AS batchno,
                pbd.spec            AS spec,
                pbd.idurut          AS idurut,

                pbd.inputby         AS inputby,
                pbd.inputdate       AS inputdate

            FROM sc_trx.workingorder_mst wo
            JOIN sc_trx.penerimaanbp_mst pb
                ON TRIM(pb.wono) = TRIM(wo.docno)
            JOIN sc_trx.penerimaanbp_dtl pbd
                ON TRIM(pbd.docno) = TRIM(pb.docno)
            LEFT JOIN sc_trx.workingorder_bom_mst wbm
                ON TRIM(wbm.docno) = TRIM(pb.bomno)
            CROSS JOIN parameter p
            WHERE wo.status NOT IN ('C', 'D')
            AND (p.cabang  IS NULL OR TRIM(p.cabang)  = '' OR TRIM(wo.cabang) = TRIM(p.cabang))
            AND (p.wono    IS NULL OR TRIM(p.wono)    = '' OR TRIM(wo.docno)  = TRIM(p.wono))
            AND (p.status_wo IS NULL OR TRIM(p.status_wo) = ''
                OR (p.status_wo = 'A' AND UPPER(TRIM(wo.status)) = 'A')
                OR (p.status_wo = 'NC' AND UPPER(TRIM(wo.status)) <> 'A'))
            AND wo.docdate >= p.tanggal_awal
            AND wo.docdate <= p.tanggal_akhir
        ),

        /* =====================================================
        GABUNGKAN
        ===================================================== */
        gabung AS (
            SELECT * FROM teoritis_material
            UNION ALL
            SELECT * FROM teoritis_cost
            UNION ALL
            SELECT * FROM teoritis_mr
            UNION ALL
            SELECT * FROM realisasi_mr
            UNION ALL
            SELECT * FROM realisasi_bpnm
            UNION ALL
            SELECT * FROM realisasi_penerimaan
        )

        SELECT
            g.urut_jenis,
            g.jns,
            g.jenis_transaksi,
            TRIM(g.wono)                AS wono,
            TO_CHAR(g.docdate_wo, 'DD/MM/YYYY') AS docdate_wo,

            CASE
                WHEN UPPER(TRIM(g.status_wo)) = 'A' THEN TO_CHAR(g.finish_date, 'DD/MM/YYYY')
                ELSE ''
            END AS finish_date,

            CASE
                WHEN UPPER(TRIM(g.status_wo)) = 'A' THEN 'Complete'
                WHEN UPPER(TRIM(g.status_wo)) = 'C'  THEN 'Cancel'
                ELSE 'In Progress'
            END AS status_wo,

            TRIM(g.cabang)              AS cabang,
            TRIM(g.pemohon)             AS pemohon,
            TRIM(g.bomno)               AS bomno,
            TRIM(g.keterangan_bom)      AS keterangan_bom,
            TRIM(g.jenis_detail)        AS jenis_detail,
            TRIM(g.idbarang)            AS idbarang,
            TRIM(g.nmbarang)            AS nmbarang,
            TRIM(g.description)         AS description,
            g.qty                       AS qty,
            g.dk                        AS dk,
            TRIM(g.unit)                AS unit,
            g.hrg_stdcost               AS hrg_stdcost,
            g.total_cost                AS total_cost,
            TRIM(g.currcode)            AS currcode,
            g.uniqueid                  AS uniqueid,
            TRIM(g.docno_realisasi)     AS docno_realisasi,
            TRIM(g.batchno)             AS batchno,
            TRIM(g.spec)                AS spec,
            g.idurut                    AS idurut,
            TRIM(g.inputby)             AS inputby,
            TO_CHAR(g.inputdate, 'DD/MM/YYYY HH24:MI:SS') AS inputdate
        FROM gabung g
        ORDER BY
            g.wono,
            g.urut_jenis,
            g.docno_realisasi NULLS FIRST,
            g.idurut NULLS FIRST,
            g.uniqueid
        ";

        if ($withLimit) {
            $sql .= " LIMIT ? OFFSET ?";
        }
        return $sql;
    }

}