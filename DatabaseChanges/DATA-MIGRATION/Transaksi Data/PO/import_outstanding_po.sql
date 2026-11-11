/*
===============================================================================
ONE SHOT INSTALL - OUTSTANDING PO IMPORT
Sumber upload UI : sheet template_import
Staging          : sc_im.lap_po_win
Target           : sc_trx.po dan sc_trx.po_dtl
Cara kerja       :
  1. Jalankan script ini satu kali untuk recreate staging + install procedure.
  2. Aplikasi import sheet template_import ke sc_im.lap_po_win.
  3. Setelah import selesai, panggil:
       CALL sc_im.sp_import_template_po();
Catatan:
  - Script ini tidak DROP / TRUNCATE tabel transaksi.
  - Recreate hanya tabel staging sc_im.lap_po_win.
  - Qty diambil langsung dari kolom "Qty PO".
  - qtylpb dan qtyvoid yang sudah ada tidak ditimpa saat update detail.
===============================================================================
*/
BEGIN;

CREATE SCHEMA IF NOT EXISTS sc_im;

DROP PROCEDURE IF EXISTS sc_im.sp_import_template_po();
DROP TABLE IF EXISTS sc_im.lap_po_win CASCADE;

CREATE TABLE sc_im.lap_po_win
(
    id BIGSERIAL PRIMARY KEY,
    no_po varchar(30) NOT NULL,
    keterangan_po text,
    kode_supplier varchar(30),
    nama_supplier varchar(250),
    kode_barang varchar(20) NOT NULL,
    nama_barang varchar(150),
    keterangan_detail text,
    qty_po numeric(18,2) NOT NULL DEFAULT 0,
    satuan varchar(20),
    harga_po numeric(18,2) DEFAULT 0,
    total_price numeric(18,2) DEFAULT 0,
    real_price numeric(18,2) DEFAULT 0,
    ppn_nonppn varchar(20),
    mata_uang varchar(3),
    kurs numeric(18,6) DEFAULT 1,
    category varchar(100),
    tahun_po integer,
    etd date,
    docdate date,
    senddate date,
    cabang varchar(30),
    user_pemohon varchar(100),
    exclude_include varchar(20),
    nilai_dpp numeric(18,2) DEFAULT 0,
    nilai_pajak numeric(18,2) DEFAULT 0,
    nilai_konversi numeric(18,2) DEFAULT 0,
    batch varchar(100),
    etdnotes varchar(100),
    imported_at timestamp without time zone DEFAULT now()
);

CREATE INDEX idx_lap_po_win_docno ON sc_im.lap_po_win (no_po);
CREATE INDEX idx_lap_po_win_item ON sc_im.lap_po_win (no_po, kode_barang);

-- Kolom kategori belum ada pada struktur PO awal; tambahkan tanpa menghapus data transaksi.
ALTER TABLE sc_trx.po_dtl ADD COLUMN IF NOT EXISTS category varchar(100);
ALTER TABLE sc_tmp.po_dtl ADD COLUMN IF NOT EXISTS category varchar(100);

CREATE OR REPLACE PROCEDURE sc_im.sp_import_template_po()
LANGUAGE plpgsql
AS $PROC$
DECLARE
    v_rows integer;
BEGIN
    PERFORM pg_advisory_xact_lock(hashtext('sc_im.sp_import_template_po'));

    SELECT COUNT(*) INTO v_rows
    FROM sc_im.lap_po_win
    WHERE NULLIF(BTRIM(no_po), '') IS NOT NULL
      AND NULLIF(BTRIM(kode_barang), '') IS NOT NULL;

    IF v_rows = 0 THEN
        RAISE EXCEPTION 'Staging sc_im.lap_po_win kosong. Import sheet template_import terlebih dahulu.';
    END IF;

    IF EXISTS (
        SELECT 1
        FROM sc_im.lap_po_win
        WHERE NULLIF(BTRIM(no_po), '') IS NULL
           OR NULLIF(BTRIM(kode_barang), '') IS NULL
           OR COALESCE(qty_po,0) < 0
    ) THEN
        RAISE EXCEPTION 'Validasi gagal: No. PO / Kode Barang kosong atau Qty PO negatif.';
    END IF;

    DROP TABLE IF EXISTS pg_temp.tmp_po_import_detail;
    CREATE TEMP TABLE tmp_po_import_detail ON COMMIT DROP AS
    SELECT
        BTRIM(no_po)::varchar(30) AS docno,
        COALESCE(MAX(NULLIF(BTRIM(keterangan_po),'')), '') AS keterangan_po,
        COALESCE(MAX(NULLIF(BTRIM(kode_supplier),'')), '') AS kdsupplier,
        COALESCE(MAX(NULLIF(BTRIM(nama_supplier),'')), '') AS nmsupplier,
        COALESCE(MAX(NULLIF(BTRIM(cabang),'')), '') AS cabang,
        COALESCE(MAX(NULLIF(BTRIM(user_pemohon),'')), '') AS pemohon,
        COALESCE(MAX(docdate), CURRENT_DATE) AS docdate,
        MAX(senddate) AS senddate,
        COALESCE(MAX(NULLIF(BTRIM(ppn_nonppn),'')), 'NON PPN') AS ppn_nonppn,
        COALESCE(MAX(NULLIF(BTRIM(mata_uang),'')), 'IDR') AS currcode,
        COALESCE(MAX(NULLIF(kurs,0)),1) AS kurs,
        SUM(COALESCE(nilai_dpp,total_price,0)) AS dpp,
        SUM(COALESCE(nilai_pajak,0)) AS jumlahpajak,
        SUM(COALESCE(nilai_dpp,total_price,0) + COALESCE(nilai_pajak,0)) AS total
    FROM sc_im.lap_po_win
    GROUP BY BTRIM(no_po);

    -- Header: update bila No. PO sudah ada, insert bila belum ada.
    UPDATE sc_trx.po p
    SET docdate = h.docdate,
        senddate = h.senddate,
        cabang = h.cabang,
        pemohon = h.pemohon,
        kdsupplier = h.kdsupplier,
        nmsupplier = h.nmsupplier,
        currcode = h.currcode,
        kurs = h.kurs,
        dpp = h.dpp,
        jumlahpajak = h.jumlahpajak,
        total = h.total,
        keterangan = h.keterangan_po,
        status = 'F',
        updateby = 'IMPORT_TEMPLATE_PO',
        updatedate = now()
    FROM tmp_po_import_detail h
    WHERE BTRIM(p.docno) = h.docno;

    INSERT INTO sc_trx.po
    (
        docno, docdate, senddate, cabang, pemohon,
        kdsupplier, nmsupplier, currcode, kurs,
        dpp, jumlahpajak, total, keterangan, status,
        inputby, inputdate
    )
    SELECT
        h.docno, h.docdate, h.senddate, h.cabang, h.pemohon,
        h.kdsupplier, h.nmsupplier, h.currcode, h.kurs,
        h.dpp, h.jumlahpajak, h.total, h.keterangan_po, 'F',
        'IMPORT_TEMPLATE_PO', now()
    FROM tmp_po_import_detail h
    WHERE NOT EXISTS (
        SELECT 1 FROM sc_trx.po p WHERE BTRIM(p.docno) = h.docno
    );

    -- Bentuk detail impor. uniqueidpo menjadi kunci idempotensi untuk baris template.
    DROP TABLE IF EXISTS pg_temp.tmp_po_import_rows;
    CREATE TEMP TABLE tmp_po_import_rows ON COMMIT DROP AS
    SELECT
        BTRIM(no_po)::varchar(30) AS docno,
        ''::varchar(30) AS docnopp,
        BTRIM(kode_barang)::varchar(20) AS idbarang,
        NULLIF(BTRIM(nama_barang),'')::varchar(150) AS nmbarang,
        NULLIF(BTRIM(satuan),'')::varchar(20) AS unit,
        COALESCE(qty_po,0)::numeric(18,2) AS qty,
        COALESCE(harga_po,0)::numeric(18,2) AS harga,
        COALESCE(total_price,0)::numeric(18,2) AS nilai,
        NULLIF(BTRIM(keterangan_detail),'') AS descriptionpo,
        NULLIF(BTRIM(keterangan_po),'') AS descriptionpp,
        NULLIF(BTRIM(ppn_nonppn),'')::varchar(20) AS idtax,
        NULLIF(BTRIM(mata_uang),'')::varchar(3) AS currcode,
        COALESCE(NULLIF(kurs,0),1)::numeric(18,2) AS kurs,
        COALESCE(nilai_konversi,0)::numeric(18,2) AS nilaikonversi,
        COALESCE(nilai_pajak,0)::numeric(18,2) AS nilaipajak,
        NULLIF(BTRIM(category),'')::varchar(100) AS category,
        NULLIF(BTRIM(batch),'')::varchar(100) AS batch,
        etd,
        NULLIF(BTRIM(etdnotes),'')::varchar(100) AS etdnotes,
        MD5(
            BTRIM(no_po) || '|' ||
            BTRIM(kode_barang) || '|' ||
            COALESCE(BTRIM(keterangan_detail),'') || '|' ||
            COALESCE(BTRIM(batch),'') || '|' ||
            COALESCE(etd::text,'')
        ) AS uniqueidpo
    FROM sc_im.lap_po_win;

    -- Update detail yang sudah ada. Qty LPB/void tidak di-reset.
    UPDATE sc_trx.po_dtl d
    SET nmbarang = r.nmbarang,
        unit = r.unit,
        qty = r.qty,
        harga = r.harga,
        nilai = r.nilai,
        descriptionpo = r.descriptionpo,
        descriptionpp = r.descriptionpp,
        idtax = r.idtax,
        currcode = r.currcode,
        kurs = r.kurs,
        nilaikonversi = r.nilaikonversi,
        nilaipajak = r.nilaipajak,
        category = r.category,
        batch = r.batch,
        etd = r.etd,
        etdnotes = r.etdnotes,
        status = 'F',
        updateby = 'IMPORT_TEMPLATE_PO',
        updatedate = now()
    FROM tmp_po_import_rows r
    WHERE d.uniqueidpo = r.uniqueidpo
       OR (
            BTRIM(d.docno) = r.docno
        AND BTRIM(d.idbarang) = r.idbarang
        AND COALESCE(BTRIM(d.descriptionpo),'') = COALESCE(r.descriptionpo,'')
        AND COALESCE(BTRIM(d.batch),'') = COALESCE(r.batch,'')
        AND COALESCE(d.etd, DATE '1900-01-01') = COALESCE(r.etd, DATE '1900-01-01')
       );

    -- Insert hanya detail yang belum ada.
    INSERT INTO sc_trx.po_dtl
    (
        docno, docnopp, idbarang, nmbarang, unit, qty, qtybonus,
        harga, multidisc, nilai, descriptionpo, descriptionpp, status,
        inputby, inputdate, uniqueidpo, idtax, currcode, kurs,
        nilaikonversi, nilaipajak, qtylpb, qtyvoid, batch, etd, etdnotes, category
    )
    SELECT
        r.docno, r.docnopp, r.idbarang, r.nmbarang, r.unit, r.qty, 0,
        r.harga, 0, r.nilai, r.descriptionpo, r.descriptionpp, 'F',
        'IMPORT_TEMPLATE_PO', now(), r.uniqueidpo, r.idtax,
        r.currcode, r.kurs, r.nilaikonversi, r.nilaipajak, 0, 0,
        r.batch, r.etd, r.etdnotes, r.category
    FROM tmp_po_import_rows r
    WHERE NOT EXISTS (
        SELECT 1
        FROM sc_trx.po_dtl d
        WHERE d.uniqueidpo = r.uniqueidpo
           OR (
                BTRIM(d.docno) = r.docno
            AND BTRIM(d.idbarang) = r.idbarang
            AND COALESCE(BTRIM(d.descriptionpo),'') = COALESCE(r.descriptionpo,'')
            AND COALESCE(BTRIM(d.batch),'') = COALESCE(r.batch,'')
            AND COALESCE(d.etd, DATE '1900-01-01') = COALESCE(r.etd, DATE '1900-01-01')
           )
    );

    RAISE NOTICE 'Import template PO selesai. Baris staging: %', v_rows;
END;
$PROC$;






COMMIT;

/*update unique id klo null*/


update sc_trx.po_dtl set uniqueid=md5(idurut||trim(docno)||trim(idbarang))  where idurut=idurut and trim(coalesce(uniqueid,''))='';

/* tambahkan update manual data untuk ppn ikut master */
update sc_trx.po set idtax='PPN11' , isinclusive='NO' where docno in (select docno from sc_trx.po_dtl where idtax='PPN');
update sc_trx.po_dtl set idtax='PPN11' where idtax='PPN';


update sc_trx.po set idtax='NON' , isinclusive='NO' where docno in (select docno from sc_trx.po_dtl where idtax='NON PPN');
update sc_trx.po_dtl set idtax='NON' where idtax='NON PPN';

UPDATE sc_trx.po AS po
SET
    alamatsupplier = sup.alamat,
    --alamatkirim    = sup.alamat,
    jthtempo       = sup.jthtempo,
    keterangan     = sup.nmsupplier
FROM sc_mst.mstsupplier AS sup
WHERE TRIM(po.kdsupplier) = TRIM(sup.kdsupplier) and trim(keterangan)='';


-- Verifikasi struktur staging dan prosedur terpasang.
SELECT column_name, data_type
FROM information_schema.columns
WHERE table_schema = 'sc_im'
  AND table_name = 'lap_po_win'
ORDER BY ordinal_position;

SELECT routine_schema, routine_name, routine_type
FROM information_schema.routines
WHERE routine_schema = 'sc_im'
  AND routine_name = 'sp_import_template_po';
