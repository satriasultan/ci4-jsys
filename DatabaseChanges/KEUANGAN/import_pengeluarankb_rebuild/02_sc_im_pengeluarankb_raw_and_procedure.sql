BEGIN;

CREATE SCHEMA IF NOT EXISTS sc_im;

-- RAW staging: 1 baris = 1 baris Excel, kolom mengikuti template BK (135 kolom).
-- Semua nilai selain nomor baris disimpan sebagai teks agar impor tidak gagal
-- hanya karena format tanggal/angka dari Excel berbeda-beda.
CREATE TABLE IF NOT EXISTS sc_im.pengeluarankb_import_raw (
    id                  BIGSERIAL PRIMARY KEY,
    batch_id            BIGINT NOT NULL
                        REFERENCES sc_im.pengeluarankb_import_batch(id) ON DELETE CASCADE,
    nomor_baris         INTEGER NOT NULL,
    nomor_bk                 VARCHAR(100),
    tanggal_bk               TEXT,
    kode_supplier            TEXT,
    supplier                 TEXT,
    dibayar_kepada           TEXT,
    alamat_supplier          TEXT,
    kode_bank                TEXT,
    keterangan               TEXT,
    total_pembayaran         TEXT,
    currency                 TEXT,
    coa_1                    TEXT,
    coa_2                    TEXT,
    coa_3                    TEXT,
    coa_4                    TEXT,
    coa_5                    TEXT,
    coa_6                    TEXT,
    coa_7                    TEXT,
    coa_8                    TEXT,
    coa_9                    TEXT,
    coa_10                   TEXT,
    coa_11                   TEXT,
    coa_12                   TEXT,
    coa_13                   TEXT,
    coa_14                   TEXT,
    coa_15                   TEXT,
    coa_16                   TEXT,
    coa_17                   TEXT,
    coa_18                   TEXT,
    coa_19                   TEXT,
    coa_20                   TEXT,
    coa_21                   TEXT,
    coa_22                   TEXT,
    coa_23                   TEXT,
    coa_24                   TEXT,
    coa_25                   TEXT,
    ket_1                    TEXT,
    ket_2                    TEXT,
    ket_3                    TEXT,
    ket_4                    TEXT,
    ket_5                    TEXT,
    ket_6                    TEXT,
    ket_7                    TEXT,
    ket_8                    TEXT,
    ket_9                    TEXT,
    ket_10                   TEXT,
    ket_11                   TEXT,
    ket_12                   TEXT,
    ket_13                   TEXT,
    ket_14                   TEXT,
    ket_15                   TEXT,
    ket_16                   TEXT,
    ket_17                   TEXT,
    ket_18                   TEXT,
    ket_19                   TEXT,
    ket_20                   TEXT,
    ket_21                   TEXT,
    ket_22                   TEXT,
    ket_23                   TEXT,
    ket_24                   TEXT,
    ket_25                   TEXT,
    reff_1                   TEXT,
    reff_2                   TEXT,
    reff_3                   TEXT,
    reff_4                   TEXT,
    reff_5                   TEXT,
    reff_6                   TEXT,
    reff_7                   TEXT,
    reff_8                   TEXT,
    reff_9                   TEXT,
    reff_10                  TEXT,
    reff_11                  TEXT,
    reff_12                  TEXT,
    reff_13                  TEXT,
    reff_14                  TEXT,
    reff_15                  TEXT,
    reff_16                  TEXT,
    reff_17                  TEXT,
    reff_18                  TEXT,
    reff_19                  TEXT,
    reff_20                  TEXT,
    reff_21                  TEXT,
    reff_22                  TEXT,
    reff_23                  TEXT,
    reff_24                  TEXT,
    reff_25                  TEXT,
    dk_1                     TEXT,
    dk_2                     TEXT,
    dk_3                     TEXT,
    dk_4                     TEXT,
    dk_5                     TEXT,
    dk_6                     TEXT,
    dk_7                     TEXT,
    dk_8                     TEXT,
    dk_9                     TEXT,
    dk_10                    TEXT,
    dk_11                    TEXT,
    dk_12                    TEXT,
    dk_13                    TEXT,
    dk_14                    TEXT,
    dk_15                    TEXT,
    dk_16                    TEXT,
    dk_17                    TEXT,
    dk_18                    TEXT,
    dk_19                    TEXT,
    dk_20                    TEXT,
    dk_21                    TEXT,
    dk_22                    TEXT,
    dk_23                    TEXT,
    dk_24                    TEXT,
    dk_25                    TEXT,
    nilai_1                  TEXT,
    nilai_2                  TEXT,
    nilai_3                  TEXT,
    nilai_4                  TEXT,
    nilai_5                  TEXT,
    nilai_6                  TEXT,
    nilai_7                  TEXT,
    nilai_8                  TEXT,
    nilai_9                  TEXT,
    nilai_10                 TEXT,
    nilai_11                 TEXT,
    nilai_12                 TEXT,
    nilai_13                 TEXT,
    nilai_14                 TEXT,
    nilai_15                 TEXT,
    nilai_16                 TEXT,
    nilai_17                 TEXT,
    nilai_18                 TEXT,
    nilai_19                 TEXT,
    nilai_20                 TEXT,
    nilai_21                 TEXT,
    nilai_22                 TEXT,
    nilai_23                 TEXT,
    nilai_24                 TEXT,
    nilai_25                 TEXT,
    imported_at         TIMESTAMP WITHOUT TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (batch_id, nomor_baris)
);

CREATE INDEX IF NOT EXISTS ix_pengkb_import_raw_batch
    ON sc_im.pengeluarankb_import_raw(batch_id, nomor_baris);

-- Prosedur mengubah data RAW Excel menjadi struktur header/detail ternormalisasi.
-- PHP cukup upload ke tabel RAW lalu memanggil:
-- CALL sc_im.sp_build_pengeluarankb_import(<batch_id>);
CREATE OR REPLACE PROCEDURE sc_im.sp_build_pengeluarankb_import(p_batch_id BIGINT)
LANGUAGE plpgsql
AS $$
DECLARE
    r               RECORD;
    v_header_id     BIGINT;
    v_docdate       DATE;
    v_total         NUMERIC(18,2);
    v_nilai         NUMERIC(18,2);
    v_docno         TEXT;
    v_currcode      TEXT;
    v_kdsupplier    TEXT;
    v_prkkas        TEXT;
    v_bank          TEXT;
    v_dk            TEXT;
    v_coa           TEXT;
    v_ket           TEXT;
    v_reff          TEXT;
    v_errors        TEXT;
    v_detail_errors TEXT;
    v_nilai_text    TEXT;
    i               INTEGER;
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM sc_im.pengeluarankb_import_batch b
        WHERE b.id = p_batch_id AND b.status = 'PREVIEW'
    ) THEN
        RAISE EXCEPTION 'Batch % tidak ditemukan atau status bukan PREVIEW', p_batch_id;
    END IF;

    -- Idempotent: hasil transform batch ini dibangun ulang dari RAW.
    DELETE FROM sc_im.pengeluarankb_import_dtl WHERE batch_id = p_batch_id;
    DELETE FROM sc_im.pengeluarankb_import_hd  WHERE batch_id = p_batch_id;

    FOR r IN
        SELECT * FROM sc_im.pengeluarankb_import_raw
        WHERE batch_id = p_batch_id
        ORDER BY nomor_baris
    LOOP
        v_errors := NULL;
        v_docno := BTRIM(COALESCE(r.nomor_bk, ''));
        v_currcode := UPPER(LEFT(BTRIM(COALESCE(r.currency, 'IDR')), 3));
        IF v_currcode = '' THEN v_currcode := 'IDR'; END IF;

        -- Parse tanggal: support YYYY-MM-DD, DD-MM-YYYY, DD/MM/YYYY, and ISO timestamp.
        v_docdate := NULL;
        IF BTRIM(COALESCE(r.tanggal_bk, '')) <> '' THEN
            BEGIN
                IF BTRIM(r.tanggal_bk) ~ '^\d{4}-\d{2}-\d{2}' THEN
                    v_docdate := LEFT(BTRIM(r.tanggal_bk), 10)::DATE;
                ELSIF BTRIM(r.tanggal_bk) ~ '^\d{2}[-/]\d{2}[-/]\d{4}$' THEN
                    v_docdate := TO_DATE(REPLACE(BTRIM(r.tanggal_bk), '/', '-'), 'DD-MM-YYYY');
                ELSE
                    v_errors := CONCAT_WS('; ', v_errors, 'Format tanggal tidak dikenali');
                END IF;
            EXCEPTION WHEN OTHERS THEN
                v_docdate := NULL;
                v_errors := CONCAT_WS('; ', v_errors, 'Tanggal tidak valid');
            END;
        ELSE
            v_errors := CONCAT_WS('; ', v_errors, 'Tanggal BK kosong');
        END IF;

        -- Total: remove thousands separators, support decimal comma or dot.
        v_total := 0;
        IF BTRIM(COALESCE(r.total_pembayaran, '')) <> '' THEN
            BEGIN
                v_total := REPLACE(REPLACE(REPLACE(BTRIM(r.total_pembayaran), ' ', ''), '.', ''), ',', '.')::NUMERIC(18,2);
                -- If value uses dot decimal and no comma, preserve decimal dot instead of stripping it.
                IF BTRIM(r.total_pembayaran) ~ '^[-+]?\d+\.\d{1,2}$' AND BTRIM(r.total_pembayaran) !~ ',' THEN
                    v_total := BTRIM(r.total_pembayaran)::NUMERIC(18,2);
                END IF;
            EXCEPTION WHEN OTHERS THEN
                v_total := 0;
                v_errors := CONCAT_WS('; ', v_errors, 'Total pembayaran bukan angka');
            END;
        ELSE
            v_errors := CONCAT_WS('; ', v_errors, 'Total pembayaran kosong');
        END IF;

        IF v_docno = '' THEN
            v_errors := CONCAT_WS('; ', v_errors, 'NOMOR BK kosong');
        ELSIF v_docno !~ '^[A-Za-z]{3}/[0-9]{4}/[A-Za-z]{2}[0-9]{4}$' THEN
            v_errors := CONCAT_WS('; ', v_errors, 'Format NOMOR BK tidak sesuai');
        END IF;

        IF EXISTS (
            SELECT 1 FROM sc_im.pengeluarankb_import_raw x
            WHERE x.batch_id = p_batch_id
              AND x.nomor_baris <> r.nomor_baris
              AND BTRIM(COALESCE(x.nomor_bk, '')) = v_docno
        ) THEN
            v_errors := CONCAT_WS('; ', v_errors, 'NOMOR BK duplikat dalam file');
        END IF;

        v_kdsupplier := NULLIF(BTRIM(COALESCE(r.kode_supplier, '')), '');
        v_bank := BTRIM(COALESCE(r.kode_bank, ''));
        -- KODE BANK may contain "111204 - BANK NAME - IDR"; retain code only.
        v_prkkas := NULLIF(BTRIM(SPLIT_PART(v_bank, ' - ', 1)), '');

        INSERT INTO sc_im.pengeluarankb_import_hd (
            batch_id, nomor_baris, docno, docdate, kdsupplier, nmsupplier,
            dibayarkepada, alamatsupplier, prkkas, keterangan,
            total_pembayaran, currcode, raw_data, status, error_message
        ) VALUES (
            p_batch_id, r.nomor_baris, COALESCE(v_docno, ''), v_docdate,
            v_kdsupplier, NULLIF(BTRIM(COALESCE(r.supplier, '')), ''),
            NULLIF(BTRIM(COALESCE(r.dibayar_kepada, '')), ''),
            NULLIF(BTRIM(COALESCE(r.alamat_supplier, '')), ''),
            v_prkkas, NULLIF(BTRIM(COALESCE(r.keterangan, '')), ''),
            COALESCE(v_total, 0), v_currcode,
            TO_JSONB(r) - 'id' - 'batch_id' - 'nomor_baris' - 'imported_at',
            CASE WHEN v_errors IS NULL THEN 'VALID' ELSE 'ERROR' END,
            v_errors
        ) RETURNING id INTO v_header_id;

        FOR i IN 1..25 LOOP
            EXECUTE FORMAT(
                'SELECT ($1).%I::TEXT, ($1).%I::TEXT, ($1).%I::TEXT, ($1).%I::TEXT, ($1).%I::TEXT',
                'coa_' || i, 'ket_' || i, 'reff_' || i, 'dk_' || i, 'nilai_' || i
            ) INTO v_coa, v_ket, v_reff, v_dk, v_nilai_text USING r;

            v_coa := BTRIM(COALESCE(v_coa, ''));
            v_ket := NULLIF(BTRIM(COALESCE(v_ket, '')), '');
            v_reff := NULLIF(BTRIM(COALESCE(v_reff, '')), '');
            v_dk := UPPER(BTRIM(COALESCE(v_dk, '')));
            v_detail_errors := NULL;

            -- Empty slots are skipped. A slot with any content becomes a detail row.
            IF v_coa <> '' OR v_ket IS NOT NULL OR v_reff IS NOT NULL OR v_dk <> '' OR
               BTRIM(COALESCE(v_nilai_text, '')) <> '' THEN
                v_nilai := 0;
                BEGIN
                    IF BTRIM(COALESCE(v_nilai_text, '')) <> '' THEN
                        v_nilai := REPLACE(REPLACE(REPLACE(BTRIM(v_nilai_text), ' ', ''), '.', ''), ',', '.')::NUMERIC(18,2);
                        IF BTRIM(v_nilai_text) ~ '^[-+]?\d+\.\d{1,2}$' AND BTRIM(v_nilai_text) !~ ',' THEN
                            v_nilai := BTRIM(v_nilai_text)::NUMERIC(18,2);
                        END IF;
                    ELSE
                        v_detail_errors := CONCAT_WS('; ', v_detail_errors, 'Nilai kosong');
                    END IF;
                EXCEPTION WHEN OTHERS THEN
                    v_nilai := 0;
                    v_detail_errors := CONCAT_WS('; ', v_detail_errors, 'Nilai bukan angka');
                END;

                IF v_coa = '' THEN
                    v_detail_errors := CONCAT_WS('; ', v_detail_errors, 'COA kosong');
                END IF;
                IF v_dk NOT IN ('D', 'K') THEN
                    v_detail_errors := CONCAT_WS('; ', v_detail_errors, 'DK harus D atau K');
                END IF;

                INSERT INTO sc_im.pengeluarankb_import_dtl (
                    batch_id, header_id, slot_no, idcoa, nmcoa, keterangan,
                    reff, dk, nilai, status, error_message, raw_data
                ) VALUES (
                    p_batch_id, v_header_id, i, COALESCE(v_coa, ''),
                    NULL, v_ket, v_reff, NULLIF(v_dk, ''),
                    COALESCE(v_nilai, 0),
                    CASE WHEN v_detail_errors IS NULL THEN 'VALID' ELSE 'ERROR' END,
                    v_detail_errors,
                    JSONB_BUILD_OBJECT('coa', v_coa, 'ket', v_ket, 'reff', v_reff, 'dk', v_dk, 'nilai', v_nilai_text)
                );

                IF v_detail_errors IS NOT NULL THEN
                    UPDATE sc_im.pengeluarankb_import_hd
                    SET status = 'ERROR',
                        error_message = CONCAT_WS('; ', error_message, 'Ada detail tidak valid')
                    WHERE id = v_header_id;
                END IF;
            END IF;
        END LOOP;
    END LOOP;

    UPDATE sc_im.pengeluarankb_import_batch b
    SET jumlah_baris = x.jumlah_baris,
        jumlah_valid = x.jumlah_valid,
        jumlah_gagal = x.jumlah_gagal,
        keterangan = CONCAT_WS(E'\n', NULLIF(b.keterangan, ''), 'Data RAW diproses ke header/detail.')
    FROM (
        SELECT
            COUNT(*)::INTEGER AS jumlah_baris,
            COUNT(*) FILTER (WHERE h.status = 'VALID'
                AND NOT EXISTS (
                    SELECT 1 FROM sc_im.pengeluarankb_import_dtl d
                    WHERE d.header_id = h.id AND d.status = 'ERROR'
                ))::INTEGER AS jumlah_valid,
            COUNT(*) FILTER (WHERE h.status = 'ERROR'
                OR EXISTS (
                    SELECT 1 FROM sc_im.pengeluarankb_import_dtl d
                    WHERE d.header_id = h.id AND d.status = 'ERROR'
                ))::INTEGER AS jumlah_gagal
        FROM sc_im.pengeluarankb_import_hd h
        WHERE h.batch_id = p_batch_id
    ) x
    WHERE b.id = p_batch_id;
END;
$$;

COMMIT;

-- Upload PHP (high-level):
-- 1) create batch row in sc_im.pengeluarankb_import_batch (status PREVIEW)
-- 2) bulk insert each Excel row into sc_im.pengeluarankb_import_raw
-- 3) CALL sc_im.sp_build_pengeluarankb_import(<batch_id>);
-- 4) read import_hd/import_dtl for preview. PHP tidak perlu memecah 25 slot.




BEGIN;

ALTER TABLE sc_tmp.pengeluarankb_dtl
    ADD COLUMN IF NOT EXISTS currcode CHARACTER(3),
    ADD COLUMN IF NOT EXISTS kurs NUMERIC(18,2);

ALTER TABLE sc_trx.pengeluarankb_dtl
    ADD COLUMN IF NOT EXISTS currcode CHARACTER(3),
    ADD COLUMN IF NOT EXISTS kurs NUMERIC(18,2);

CREATE OR REPLACE PROCEDURE sc_im.sp_confirm_pengeluarankb_import(
    p_batch_id BIGINT,
    p_user VARCHAR(100)
)
LANGUAGE plpgsql
AS $$
DECLARE
    h RECORD;
    d RECORD;
    v_header_id INTEGER;
    v_detail_id INTEGER;
    v_uniqueid TEXT;
    v_detail_uniqueid TEXT;
    v_docno TEXT;
    v_user TEXT;
    v_count INTEGER := 0;
BEGIN
    v_user := COALESCE(NULLIF(BTRIM(p_user), ''), 'SYSTEM');

    PERFORM 1
    FROM sc_im.pengeluarankb_import_batch
    WHERE id = p_batch_id AND status = 'PREVIEW'
    FOR UPDATE;

    IF NOT FOUND THEN
        RAISE EXCEPTION 'Batch % tidak ditemukan atau status bukan PREVIEW.', p_batch_id;
    END IF;

    IF to_regclass('sc_trx.transaction_dt') IS NULL THEN
        RAISE EXCEPTION 'Tabel sc_trx.transaction_dt belum tersedia.';
    END IF;

    IF to_regclass('sc_trx.pengeluarankb') IS NULL
       OR to_regclass('sc_trx.pengeluarankb_dtl') IS NULL THEN
        RAISE EXCEPTION 'Tabel transaksi sc_trx.pengeluarankb / pengeluarankb_dtl belum tersedia.';
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM sc_mst.journal_type jt
        WHERE BTRIM(jt.journal_type::TEXT) = 'BANKOUT'
    ) THEN
        RAISE EXCEPTION 'journal_type BANKOUT belum terdaftar di sc_mst.journal_type.';
    END IF;

    IF EXISTS (
        SELECT 1
        FROM sc_im.pengeluarankb_import_hd h
        WHERE h.batch_id = p_batch_id
          AND (
              h.status <> 'VALID'
              OR h.docdate IS NULL
              OR BTRIM(COALESCE(h.docno, '')) = ''
              OR BTRIM(COALESCE(h.prkkas, '')) = ''
              OR EXISTS (
                  SELECT 1 FROM sc_im.pengeluarankb_import_dtl d
                  WHERE d.header_id = h.id AND d.status <> 'VALID'
              )
              OR NOT EXISTS (
                  SELECT 1 FROM sc_im.pengeluarankb_import_dtl d
                  WHERE d.header_id = h.id
              )
          )
    ) THEN
        RAISE EXCEPTION 'Batch % masih memiliki header/detail tidak valid atau tanpa detail.', p_batch_id;
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM sc_im.pengeluarankb_import_hd WHERE batch_id = p_batch_id
    ) THEN
        RAISE EXCEPTION 'Batch % tidak memiliki data untuk diimpor.', p_batch_id;
    END IF;

    IF EXISTS (
        SELECT 1
        FROM sc_im.pengeluarankb_import_hd ih
        JOIN sc_trx.pengeluarankb th ON BTRIM(th.docno) = BTRIM(ih.docno)
        WHERE ih.batch_id = p_batch_id
    ) THEN
        RAISE EXCEPTION 'Ada NOMOR BK yang sudah ada di sc_trx.pengeluarankb. Tidak ada data batch yang disimpan.';
    END IF;

    IF EXISTS (
        SELECT 1
        FROM sc_im.pengeluarankb_import_hd ih
        LEFT JOIN sc_mst.coa c ON BTRIM(c.idcoa::TEXT) = BTRIM(ih.prkkas)
        WHERE ih.batch_id = p_batch_id AND c.idcoa IS NULL
    ) THEN
        RAISE EXCEPTION 'Ada COA bank/prkkas yang tidak ditemukan di sc_mst.coa.';
    END IF;

    IF EXISTS (
        SELECT 1
        FROM sc_im.pengeluarankb_import_dtl id
        JOIN sc_im.pengeluarankb_import_hd ih ON ih.id = id.header_id
        LEFT JOIN sc_mst.coa c ON BTRIM(c.idcoa::TEXT) = BTRIM(id.idcoa)
        WHERE ih.batch_id = p_batch_id AND c.idcoa IS NULL
    ) THEN
        RAISE EXCEPTION 'Ada COA detail yang tidak ditemukan di sc_mst.coa.';
    END IF;

    FOR h IN
        SELECT * FROM sc_im.pengeluarankb_import_hd
        WHERE batch_id = p_batch_id ORDER BY nomor_baris
    LOOP
        v_docno := BTRIM(h.docno);

        INSERT INTO sc_trx.pengeluarankb (
            docno, docdate, cabang, pemohon, kdsupplier, nmsupplier,
            alamatsupplier, prkkas, np, jthtempo, isinclusive, currcode,
            kurs, dpp, jumlahpajak, total, status, keterangan, balance,
            inputby, inputdate, docnotmp
        ) VALUES (
            v_docno, TO_CHAR(h.docdate, 'DD-MM-YYYY'), NULL, h.dibayarkepada,
            h.kdsupplier, h.nmsupplier, h.alamatsupplier, h.prkkas, NULL,
            0, 'NO', COALESCE(NULLIF(BTRIM(h.currcode), ''), 'IDR'), 1,
            h.total_pembayaran, 0, h.total_pembayaran, 'F', h.keterangan, 0,
            v_user, CURRENT_TIMESTAMP, NULL
        )
        RETURNING idurut INTO v_header_id;

        FOR d IN
            SELECT * FROM sc_im.pengeluarankb_import_dtl
            WHERE header_id = h.id ORDER BY slot_no
        LOOP
            v_detail_uniqueid := MD5(
                'IMPORT-PENGELUARANKB|' || p_batch_id::TEXT || '|' ||
                h.nomor_baris::TEXT || '|' || d.slot_no::TEXT || '|' || v_docno
            );

            INSERT INTO sc_trx.pengeluarankb_dtl (
                docno, nobukti, idcoa, nmcoa, remarks, dk, cabang,
                nilai, nilaiakhir, uniqueid, status, inputby, inputdate,
                docnotmp, jurnalref, nilai_ref, currcode, kurs
            ) VALUES (
                v_docno, d.reff, d.idcoa,
                COALESCE(
                    NULLIF(BTRIM(d.nmcoa), ''),
                    (SELECT c.nmcoa FROM sc_mst.coa c
                     WHERE BTRIM(c.idcoa::TEXT) = BTRIM(d.idcoa) LIMIT 1)
                ),
                d.keterangan, d.dk, NULL, d.nilai, d.nilai,
                v_detail_uniqueid, 'F', v_user, CURRENT_TIMESTAMP,
                NULL, 'YES', 0, COALESCE(NULLIF(BTRIM(h.currcode), ''), 'IDR'), 1
            )
            RETURNING idurut INTO v_detail_id;

            v_uniqueid := MD5(v_docno || '|' || v_detail_uniqueid || '|pengeluarankb');

            INSERT INTO sc_trx.transaction_dt (
                uniqueid, source_uniqueid, docno, doctype, journal_type,
                line_no, docdate, idbranch, cabang, type_in_out,
                ref_docno, ref_doctype, source_table, source_id, source_line_id,
                kdsupplier, nsupplier, nilai, dpp, pajak, total, idtax,
                isinclusive, currcode, kurs, idcoa, counter_idcoa, debet_kredit,
                module, direction, stock_effect, accounting_effect, asset_effect,
                keterangan, createdby, createddate, updatedby, updateddate
            ) VALUES (
                v_uniqueid, v_detail_uniqueid, v_docno, 'PENGELUARKANKB', 'BANKOUT',
                d.slot_no, h.docdate, '', '', 'OUT', COALESCE(d.reff, ''), '',
                'sc_trx.pengeluarankb_dtl', v_header_id, v_detail_id,
                COALESCE(h.kdsupplier, ''), COALESCE(h.nmsupplier, ''),
                COALESCE(d.nilai, 0), COALESCE(d.nilai, 0), 0, COALESCE(d.nilai, 0),
                'NON', 'NO', COALESCE(NULLIF(BTRIM(h.currcode), ''), 'IDR'), 1,
                BTRIM(d.idcoa), BTRIM(h.prkkas), d.dk,
                'FINANCE', 'OUT', 'NONE', 'YES', 'NONE',
                CONCAT_WS(' | ', NULLIF(BTRIM(COALESCE(d.keterangan, '')), ''),
                    NULLIF(BTRIM(COALESCE(d.reff, '')), ''),
                    NULLIF(BTRIM(COALESCE(h.keterangan, '')), '')),
                v_user, CURRENT_TIMESTAMP, v_user, CURRENT_TIMESTAMP
            )
            ON CONFLICT (uniqueid) DO UPDATE SET
                source_uniqueid = EXCLUDED.source_uniqueid,
                docno = EXCLUDED.docno,
                doctype = EXCLUDED.doctype,
                journal_type = EXCLUDED.journal_type,
                line_no = EXCLUDED.line_no,
                docdate = EXCLUDED.docdate,
                source_table = EXCLUDED.source_table,
                source_id = EXCLUDED.source_id,
                source_line_id = EXCLUDED.source_line_id,
                nilai = EXCLUDED.nilai,
                dpp = EXCLUDED.dpp,
                total = EXCLUDED.total,
                currcode = EXCLUDED.currcode,
                kurs = EXCLUDED.kurs,
                idcoa = EXCLUDED.idcoa,
                counter_idcoa = EXCLUDED.counter_idcoa,
                debet_kredit = EXCLUDED.debet_kredit,
                keterangan = EXCLUDED.keterangan,
                updatedby = EXCLUDED.updatedby,
                updateddate = CURRENT_TIMESTAMP;
        END LOOP;

        v_count := v_count + 1;
    END LOOP;

    UPDATE sc_im.pengeluarankb_import_batch
    SET status = 'CONFIRMED',
        confirmed_by = v_user,
        confirmed_at = CURRENT_TIMESTAMP,
        keterangan = CONCAT_WS(E'\n', NULLIF(keterangan, ''),
            FORMAT('Confirmed %s dokumen ke sc_trx dan transaction_dt BANKOUT.', v_count))
    WHERE id = p_batch_id;
END;
$$;

COMMIT;

-- Jalankan setelah preview lolos validasi:
-- CALL sc_im.sp_confirm_pengeluarankb_import(<BATCH_ID>, '<USER_LOGIN>');
