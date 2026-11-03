/*
 * ONE EXECUTE - PROSES OUTSTANDING PP
 *
 * Sumber data : sc_im.lap_pp_win (sudah diisi oleh UI).
 * Tidak menggunakan CSV, COPY, GitHub, atau pengambilan data eksternal.
 *
 * Output:
 *   sc_trx.pp      : header PP status 'P'
 *   sc_trx.pp_dtl  : detail outstanding status 'P'
 *
 * Aturan:
 *   - Satu nomor PP menjadi satu header.
 *   - Detail dikelompokkan berdasarkan PP + Kode Barang + Keterangan Detail.
 *   - Qty outstanding = SUM(qty) - SUM(qty_realisasi).
 *   - Hanya detail dengan Qty outstanding > 0 yang diproses.
 *   - docdate pada sc_trx.pp diperbarui dari tanggal detail (date_)
 *     untuk docno yang sama. Jika date_ kosong, gunakan docdate staging.
 *   - Aman dijalankan ulang: header/detail yang sudah ada akan di-update.
 *
 * Jalankan seluruh script ini sekali (ONE EXECUTE).
 */

BEGIN;

CREATE SCHEMA IF NOT EXISTS sc_im;

/*
 * Tabel staging dibuat hanya jika belum ada.
 * Tidak DROP/TRUNCATE, sehingga data yang sudah diinput UI tetap aman.
 */
CREATE TABLE IF NOT EXISTS sc_im.lap_pp_win
(
    id BIGSERIAL,
    pp TEXT NOT NULL,
    jurnal TEXT NOT NULL DEFAULT '',
    date_ TEXT,
    username TEXT,
    brg TEXT NOT NULL,
    qty TEXT,
    qty_realisasi TEXT,
    nama_user TEXT,
    nama_barang TEXT,
    unit TEXT,
    spec TEXT,
    rem_pp TEXT NOT NULL DEFAULT '',
    rem_pd TEXT NOT NULL DEFAULT '',
    date_pakai TEXT,
    job TEXT,
    docdate TEXT,
    estpakai TEXT,
    uniqueidpd TEXT NOT NULL,
    status CHARACTER(6),
    inputdate TIMESTAMP WITHOUT TIME ZONE,
    inputby CHARACTER(20),
    updatedate TIMESTAMP WITHOUT TIME ZONE,
    updateby VARCHAR(20),
    CONSTRAINT lap_pp_win_pkey
        PRIMARY KEY (pp, jurnal, brg, rem_pd, uniqueidpd)
);

/* Parser angka: 1,000.00 menjadi 1000.00. */
CREATE OR REPLACE FUNCTION sc_im.fn_parse_qty(p_value TEXT)
RETURNS NUMERIC(18,2)
LANGUAGE plpgsql
IMMUTABLE
AS $$
DECLARE
    v TEXT;
BEGIN
    v := regexp_replace(COALESCE(BTRIM(p_value), ''), '[[:space:]]', '', 'g');
    v := replace(v, chr(160), '');

    IF v = '' THEN
        RETURN 0;
    END IF;

    v := replace(v, ',', '');

    IF v !~ '^[+-]?[0-9]+([.][0-9]+)?$' THEN
        RAISE EXCEPTION 'Format Qty tidak valid: "%", hasil normalisasi "%"', p_value, v;
    END IF;

    RETURN v::NUMERIC(18,2);
EXCEPTION
    WHEN numeric_value_out_of_range THEN
        RAISE EXCEPTION 'Qty di luar kapasitas NUMERIC(18,2): %', p_value;
END;
$$;

/* Parser tanggal: DD/MM/YYYY atau YYYY-MM-DD. */
CREATE OR REPLACE FUNCTION sc_im.fn_parse_date(p_value TEXT)
RETURNS DATE
LANGUAGE plpgsql
IMMUTABLE
AS $$
DECLARE
    v TEXT := BTRIM(COALESCE(p_value, ''));
BEGIN
    IF v = '' THEN
        RETURN NULL;
    ELSIF v ~ '^[0-9]{2}/[0-9]{2}/[0-9]{4}' THEN
        RETURN TO_DATE(SUBSTRING(v FROM 1 FOR 10), 'DD/MM/YYYY');
    ELSIF v ~ '^[0-9]{4}-[0-9]{2}-[0-9]{2}' THEN
        RETURN TO_DATE(SUBSTRING(v FROM 1 FOR 10), 'YYYY-MM-DD');
    END IF;

    RAISE EXCEPTION 'Format tanggal tidak valid: %', p_value;
END;
$$;

CREATE OR REPLACE PROCEDURE sc_im.sp_import_lap_pp_to_trx()
LANGUAGE plpgsql
AS $$
DECLARE
    v_source_count    INTEGER := 0;
    v_header_inserted INTEGER := 0;
    v_header_updated  INTEGER := 0;
    v_detail_inserted INTEGER := 0;
    v_detail_updated  INTEGER := 0;
BEGIN
    PERFORM pg_advisory_xact_lock(hashtext('sc_im.sp_import_lap_pp_to_trx'));

    IF TO_REGCLASS('sc_im.lap_pp_win') IS NULL THEN
        RAISE EXCEPTION 'Tabel sumber sc_im.lap_pp_win tidak ditemukan';
    END IF;

    SELECT COUNT(*)
      INTO v_source_count
      FROM sc_im.lap_pp_win
     WHERE NULLIF(BTRIM(pp), '') IS NOT NULL
       AND NULLIF(BTRIM(brg), '') IS NOT NULL;

    IF v_source_count = 0 THEN
        RAISE EXCEPTION 'Tidak ada data valid pada sc_im.lap_pp_win. Proses dibatalkan.';
    END IF;

    /*
     * Satu nomor PP menjadi satu header.
     * docdate diprioritaskan dari date_ (tanggal pada detail).
     * Jika date_ tidak tersedia, gunakan kolom docdate staging.
     */
    CREATE TEMP TABLE tmp_pp_header ON COMMIT DROP AS
    SELECT
        BTRIM(pp) AS docno,
        COALESCE(
            MIN(sc_im.fn_parse_date(date_)),
            MIN(sc_im.fn_parse_date(docdate))
        ) AS docdate,
        MIN(sc_im.fn_parse_date(estpakai)) AS estpakai,
        MAX(NULLIF(BTRIM(job), '')) AS cabang,
        MAX(NULLIF(BTRIM(nama_user), '')) AS nama_user,
        MAX(NULLIF(BTRIM(username), '')) AS username,
        MAX(NULLIF(BTRIM(rem_pp), '')) AS keterangan
    FROM sc_im.lap_pp_win
    WHERE NULLIF(BTRIM(pp), '') IS NOT NULL
    GROUP BY BTRIM(pp);

    /* Insert header PP yang belum tersedia. */
    INSERT INTO sc_trx.pp
        (docno, docdate, cabang, pemohon, estpakai, status,
         keterangan, inputby, inputdate)
    SELECT
        h.docno,
        h.docdate,
        h.cabang,
        COALESCE(h.nama_user, h.username),
        h.estpakai,
        'P',
        h.keterangan,
        h.username,
        CURRENT_TIMESTAMP
    FROM tmp_pp_header h
    WHERE NOT EXISTS
    (
        SELECT 1
        FROM sc_trx.pp p
        WHERE BTRIM(p.docno) = h.docno
    );

    GET DIAGNOSTICS v_header_inserted = ROW_COUNT;

    /*
     * Update header yang sudah ada.
     * docdate ikut diperbarui dari date_ detail dengan docno yang sama.
     */
    UPDATE sc_trx.pp p
       SET docdate    = COALESCE(h.docdate, p.docdate),
           cabang     = COALESCE(h.cabang, p.cabang),
           pemohon    = COALESCE(h.nama_user, h.username, p.pemohon),
           estpakai   = COALESCE(h.estpakai, p.estpakai),
           status     = 'P',
           keterangan = COALESCE(h.keterangan, p.keterangan)
      FROM tmp_pp_header h
     WHERE BTRIM(p.docno) = h.docno;

    GET DIAGNOSTICS v_header_updated = ROW_COUNT;

    /*
     * Agregasi detail:
     * PP + Kode Barang + Keterangan Detail.
     * Qty target = total Qty - total Qty Realisasi.
     */
    CREATE TEMP TABLE tmp_pp_detail ON COMMIT DROP AS
    WITH item_agg AS
    (
        SELECT
            BTRIM(pp) AS docno,
            BTRIM(brg) AS idbarang,
            BTRIM(COALESCE(rem_pd, '')) AS description,
            MAX(NULLIF(BTRIM(nama_barang), '')) AS nmbarang,
            MAX(NULLIF(BTRIM(unit), '')) AS unit,
            SUM(sc_im.fn_parse_qty(qty))::NUMERIC(18,2) AS qty_pp,
            SUM(sc_im.fn_parse_qty(qty_realisasi))::NUMERIC(18,2)
                AS qty_realisasi,
            MAX(NULLIF(BTRIM(username), '')) AS inputby
        FROM sc_im.lap_pp_win
        WHERE NULLIF(BTRIM(pp), '') IS NOT NULL
          AND NULLIF(BTRIM(brg), '') IS NOT NULL
        GROUP BY
            BTRIM(pp),
            BTRIM(brg),
            BTRIM(COALESCE(rem_pd, ''))
    )
    SELECT
        docno,
        idbarang,
        nmbarang,
        unit,
        (qty_pp - qty_realisasi)::NUMERIC(18,2) AS qty,
        description,
        inputby,
        MD5(docno || '|' || idbarang || '|' || description) AS source_uniqueid,
        'P'::CHARACTER(1) AS status
    FROM item_agg
    WHERE qty_pp - qty_realisasi > 0;

    /* Update detail yang sudah ada. */
    UPDATE sc_trx.pp_dtl t
       SET nmbarang        = COALESCE(d.nmbarang, t.nmbarang),
           unit            = COALESCE(d.unit, t.unit),
           qty             = d.qty,
           description     = d.description,
           status          = 'P',
           source_uniqueid = COALESCE(t.source_uniqueid, d.source_uniqueid)
      FROM tmp_pp_detail d
     WHERE BTRIM(t.docno) = d.docno
       AND BTRIM(t.idbarang) = d.idbarang
       AND BTRIM(COALESCE(t.description, '')) = d.description;

    GET DIAGNOSTICS v_detail_updated = ROW_COUNT;

    /* Insert detail baru yang belum ada. */
    INSERT INTO sc_trx.pp_dtl
        (docno, idbarang, nmbarang, unit, qty, description,
         status, inputby, inputdate, uniqueid, source_uniqueid)
    SELECT
        d.docno,
        d.idbarang,
        d.nmbarang,
        d.unit,
        d.qty,
        d.description,
        'P',
        d.inputby,
        CURRENT_TIMESTAMP,
        d.source_uniqueid,
        d.source_uniqueid
    FROM tmp_pp_detail d
    WHERE EXISTS
    (
        SELECT 1
        FROM sc_trx.pp p
        WHERE BTRIM(p.docno) = d.docno
    )
    AND NOT EXISTS
    (
        SELECT 1
        FROM sc_trx.pp_dtl t
        WHERE BTRIM(t.docno) = d.docno
          AND BTRIM(t.idbarang) = d.idbarang
          AND BTRIM(COALESCE(t.description, '')) = d.description
    );

    GET DIAGNOSTICS v_detail_inserted = ROW_COUNT;

    RAISE NOTICE
        'Outstanding PP selesai. Sumber: %, Header insert: %, Header update: %, Detail insert: %, Detail update: %',
        v_source_count,
        v_header_inserted,
        v_header_updated,
        v_detail_inserted,
        v_detail_updated;
END;
$$;

/* Jalankan prosedur. */
CALL sc_im.sp_import_lap_pp_to_trx();

/* Verifikasi header dan detail PP berstatus P. */
SELECT
    'HEADER' AS tipe,
    status,
    COUNT(*) AS jumlah
FROM sc_trx.pp
WHERE status = 'P'
GROUP BY status

UNION ALL

SELECT
    'DETAIL' AS tipe,
    status,
    COUNT(*) AS jumlah
FROM sc_trx.pp_dtl
WHERE status = 'P'
GROUP BY status
ORDER BY tipe;

COMMIT;
