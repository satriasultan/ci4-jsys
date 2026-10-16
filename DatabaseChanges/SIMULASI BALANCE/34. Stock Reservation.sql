
   
   
   
   
   
   /* COBA MENGGUNAKAN INI !IMPORTAN */
   /* ============================================================================
   VIEW: sc_tmp.v_all_tmp_reservation
   Union semua TMP detail yang masih DRAFT (status 'E').
   Untuk sekarang hanya TRANSFER LOCATION.
   Nanti tinggal UNION ALL LPB / SALES / DO.
   ============================================================================ */

DROP VIEW IF EXISTS sc_tmp.v_all_tmp_reservation CASCADE;

CREATE OR REPLACE VIEW sc_tmp.v_all_tmp_reservation AS

/* ============================================================
   TRANSFER LOCATION (TRFWHS)
   Reservasi diambil dari idlocation_from (lokasi asal)
   ============================================================ */
SELECT
    'sc_tmp.transfer_location_dtl'::TEXT    AS source_table,
    TRIM(tld.docno)                         AS source_docno,
    'TRFWHS'::TEXT                          AS source_doctype,

    /* RESERVASI DARI LOKASI ASAL */
    TRIM(tlh.cabang)                        AS idbranch_raw,
    TRIM(tlh.idlocation_from)               AS idlocation,
    ''::TEXT                                AS idarea,

    TRIM(tld.idbarang)                      AS idbarang,
    TRIM(COALESCE(tld.unit_default, tld.unit)) AS idunit,
    TRIM(COALESCE(tld.batch, ''))           AS batch,
    TRIM(COALESCE(tld.lotno, ''))           AS lotno,

    COALESCE(tld.qty_base, 0)               AS qty_reserved,

    tld.idurut                              AS source_line_id,
    TRIM(tld.inputby)                       AS inputby,

    /* Untuk referensi tambahan */
    TRIM(COALESCE(tld.nmbarang, ''))        AS nmbarang

FROM sc_tmp.transfer_location_dtl tld
JOIN sc_tmp.transfer_location_mst tlh
  ON  TRIM(tlh.docno) = TRIM(tld.docno)
  AND tlh.inputby     = tld.inputby
WHERE TRIM(COALESCE(tlh.status, '')) = 'E'
  AND COALESCE(tld.qty_base, 0) > 0


UNION ALL

/* ============================================================
   PMK BRNG (STKOUT)
   Reservasi diambil dari idlocation di DETAIL
   karena PMK memilih gudang di level detail, bukan header.
   ============================================================ */
SELECT
    'sc_tmp.pmk_brng_dtl'::TEXT             AS source_table,
    TRIM(pd.docno)                          AS source_docno,
    'STKOUT'::TEXT                          AS source_doctype,

    /* RESERVASI DARI DETAIL (idlocation per baris) */
    TRIM(pm.cabang)                         AS idbranch_raw,
    TRIM(pd.idlocation)                     AS idlocation,
    ''::TEXT                                AS idarea,

    TRIM(pd.idbarang)                       AS idbarang,
    TRIM(COALESCE(pd.unit_default, pd.unit)) AS idunit,
    TRIM(COALESCE(pd.batch, ''))            AS batch,
    ''::TEXT                                AS lotno,

    COALESCE(pd.qty_base, 0)                AS qty_reserved,

    pd.idurut                               AS source_line_id,
    TRIM(pd.inputby)                        AS inputby,

    /* Untuk referensi tambahan */
    TRIM(COALESCE(pd.nmbarang, ''))         AS nmbarang

FROM sc_tmp.pmk_brng_dtl pd
JOIN sc_tmp.pmk_brng_mst pm
  ON  TRIM(pm.docno) = TRIM(pd.docno)
  AND pm.inputby     = pd.inputby
WHERE TRIM(COALESCE(pm.status, '')) = 'E'
  AND COALESCE(pd.qty_base, 0) > 0

/* NANTI TINGGAL UNION ALL DI SINI
   UNION ALL
   SELECT ... FROM sc_tmp.lpb_dtl ...
   UNION ALL
   SELECT ... FROM sc_tmp.sales_dtl ...
*/
;