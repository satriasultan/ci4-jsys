-- FUNCTION: fn_generate_docno(jobcode, codemenu, tahun, bulan)
-- Tujuan: Menghasilkan nomor docno otomatis berdasarkan prefix + suffix
--         Mengutamakan reuse suffix dari rolepo_log jika ada yang belum terpakai

CREATE OR REPLACE FUNCTION sc_mst.fn_generate_docno(
    p_jobcode VARCHAR,
    p_codemenu VARCHAR,
    p_tahun CHAR(2),
    p_bulan CHAR(2),
    p_inputby VARCHAR
)
RETURNS VARCHAR AS $$
DECLARE
    v_prefix VARCHAR;
    v_suffix CHAR(5);
    v_next_suffix CHAR(5);
    v_docno VARCHAR;
    v_log_id INT;
BEGIN
    -- Lock row di rolepo untuk hindari race condition
    PERFORM 1 FROM sc_mst.rolepo
    WHERE jobcode = p_jobcode AND codemenu = p_codemenu AND tahun = p_tahun AND bulan = p_bulan
    FOR UPDATE;

    -- Cek apakah ada suffix yang belum terpakai (reuse)
    SELECT suffix, id INTO v_suffix, v_log_id
    FROM sc_mst.rolepo_log
    WHERE jobcode = p_jobcode AND codemenu = p_codemenu
      AND tahun = p_tahun AND bulan = p_bulan
      AND is_used = FALSE
    ORDER BY suffix ASC
    LIMIT 1;

    IF FOUND THEN
        -- Gunakan suffix dari rolepo_log
        v_prefix := (
            SELECT prefix FROM sc_mst.rolepo
            WHERE jobcode = p_jobcode AND codemenu = p_codemenu
              AND tahun = p_tahun AND bulan = p_bulan
        );

        v_docno := v_prefix || '/' || v_suffix;

        -- Tandai suffix ini sebagai used
        UPDATE sc_mst.rolepo_log
        SET is_used = TRUE, used_by = p_inputby, used_at = now(), docno = v_docno
        WHERE id = v_log_id;

        RETURN v_docno;
    END IF;

    -- Jika tidak ada yang bisa di-reuse, generate suffix baru dari rolepo
    SELECT suffix INTO v_suffix
    FROM sc_mst.rolepo
    WHERE jobcode = p_jobcode AND codemenu = p_codemenu
      AND tahun = p_tahun AND bulan = p_bulan;

    -- Tambahkan 1 ke suffix
    v_next_suffix := LPAD((CAST(v_suffix AS INTEGER) + 1)::TEXT, 3, '0');

    -- Update rolepo dengan suffix terbaru
    UPDATE sc_mst.rolepo
    SET suffix = v_next_suffix, inputdate = now(), inputby = p_inputby
    WHERE jobcode = p_jobcode AND codemenu = p_codemenu AND tahun = p_tahun AND bulan = p_bulan;

    -- Ambil prefix
    SELECT prefix INTO v_prefix
    FROM sc_mst.rolepo
    WHERE jobcode = p_jobcode AND codemenu = p_codemenu AND tahun = p_tahun AND bulan = p_bulan;

    -- Buat docno akhir
    v_docno := v_prefix || '/' || v_next_suffix;

    -- Simpan ke log juga
    INSERT INTO sc_mst.rolepo_log (
        jobcode, codemenu, tahun, bulan, suffix,
        is_used, docno, used_by, used_at
    ) VALUES (
        p_jobcode, p_codemenu, p_tahun, p_bulan, v_next_suffix,
        TRUE, v_docno, p_inputby, now()
    );

    RETURN v_docno;
END;
$$ LANGUAGE plpgsql;
