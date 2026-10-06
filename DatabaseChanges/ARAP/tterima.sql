BEGIN;

-- =========================================================
-- 1. SCHEMA
-- =========================================================

CREATE SCHEMA IF NOT EXISTS sc_tmp;
CREATE SCHEMA IF NOT EXISTS sc_trx;


-- =========================================================
-- 2. DROP TRIGGER LAMA
--    Aman untuk first run dan execute ulang.
-- =========================================================

DO $$
BEGIN
    IF to_regclass('sc_tmp.tterima_dt') IS NOT NULL THEN
        DROP TRIGGER IF EXISTS tr_tterima_dt_balance
        ON sc_tmp.tterima_dt;
    END IF;

    IF to_regclass('sc_tmp.tterima_hd') IS NOT NULL THEN
        DROP TRIGGER IF EXISTS tr_tterima_finalize
        ON sc_tmp.tterima_hd;
    END IF;

    IF to_regclass('sc_trx.tterima_hd') IS NOT NULL THEN
        DROP TRIGGER IF EXISTS tr_tterima_reopen
        ON sc_trx.tterima_hd;
    END IF;
END;
$$;


-- =========================================================
-- 3. DROP TABLE LAMA
--    Script ini boleh dieksekusi ulang dari awal.
-- =========================================================

DROP TABLE IF EXISTS sc_trx.tterima_dt CASCADE;
DROP TABLE IF EXISTS sc_trx.tterima_hd CASCADE;

DROP TABLE IF EXISTS sc_tmp.tterima_dt CASCADE;
DROP TABLE IF EXISTS sc_tmp.tterima_hd CASCADE;


-- =========================================================
-- 4. TABLE HEADER TEMPORARY
-- =========================================================

CREATE TABLE sc_tmp.tterima_hd
(
    idurut SERIAL NOT NULL,
    docno CHARACTER(30) NOT NULL,
    docdate CHARACTER(20),
    cabang CHARACTER(30),
    pemohon CHARACTER(100),

    kdsupplier CHARACTER(30),
    nmsupplier CHARACTER(250),
    kotasupplier CHARACTER(100),
    alamatsupplier TEXT,
    alamatkirim TEXT,

    -- Semua nomor dokumen referensi bersifat optional.
    noinvoice CHARACTER(50),
    tglinvoice DATE,
    nosj CHARACTER(50),
    tglsj DATE,

    noaju CHARACTER(50),
    nobl CHARACTER(50),
    noawb CHARACTER(50),
    noinvoicebea CHARACTER(50),
    tglinvoicebea DATE,
    nobkrev CHARACTER(30),
    nofakturpajak CHARACTER(100),
    senddate DATE,

    currcode CHARACTER(3) DEFAULT 'IDR',
    idtax CHARACTER(20),
    kurs NUMERIC(18,6) DEFAULT 1,
    jthtempo NUMERIC(18,2),
    tgljthtempo DATE,
    isinclusive CHARACTER(3) DEFAULT 'NO',

    dpp NUMERIC(18,2) DEFAULT 0,
    jumlahpajak NUMERIC(18,2) DEFAULT 0,
    total NUMERIC(18,2) DEFAULT 0,

    beaimport NUMERIC(18,2) DEFAULT 0,
    ppnimport NUMERIC(18,2) DEFAULT 0,
    pphimport NUMERIC(18,2) DEFAULT 0,
    biayaangkut NUMERIC(18,2) DEFAULT 0,
    biayaasuransi NUMERIC(18,2) DEFAULT 0,
    biayalain NUMERIC(18,2) DEFAULT 0,

    totalestimasi NUMERIC(18,2) DEFAULT 0,

    cekinvoice BOOLEAN DEFAULT FALSE,
    ceksj BOOLEAN DEFAULT FALSE,
    cekpenerimaan BOOLEAN DEFAULT FALSE,
    cekfakturpajak BOOLEAN DEFAULT FALSE,
    cekbeaimport BOOLEAN DEFAULT FALSE,
    cekdokumen BOOLEAN DEFAULT FALSE,

    status CHARACTER(6) DEFAULT 'E',
    keterangan TEXT,
    balance NUMERIC(18,2) DEFAULT 0,

    inputby VARCHAR(50),
    inputdate TIMESTAMP WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updateby VARCHAR(50),
    updatedate TIMESTAMP WITHOUT TIME ZONE,
    printby VARCHAR(50),
    printdate TIMESTAMP WITHOUT TIME ZONE,
    docnotmp CHARACTER(30),

    -- COA Bank
    coabank CHARACTER(20),
    nmcoabank CHARACTER(250),

    CONSTRAINT pk_tmp_tterima_hd
        PRIMARY KEY (idurut, docno)
);


-- =========================================================
-- 5. TABLE DETAIL TEMPORARY
--    DETAIL JURNAL PER NOMOR BUKTI LPB
-- =========================================================

CREATE TABLE sc_tmp.tterima_dt
(
    idurut SERIAL NOT NULL,
    docno CHARACTER(30) NOT NULL,

    -- ID unik detail yang dibuat oleh server
    idunique CHARACTER(32),

    -- Referensi LPB dan dokumen sumber
    nobukti CHARACTER(30),
    docref CHARACTER(100),

    -- Perkiraan / akun
    noperkiraan CHARACTER(20),
    namaperkiraan CHARACTER(250),

    -- Keterangan transaksi
    keterangan TEXT,

    -- D = Debit, K = Kredit
    dk CHARACTER(1),

    -- Cost / Profit Center
    costprofitcenter CHARACTER(30),

    -- Nilai jurnal
    nilai NUMERIC(18,2) DEFAULT 0,

    status CHARACTER(6) DEFAULT 'E',

    inputby VARCHAR(50),
    inputdate TIMESTAMP WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updateby VARCHAR(50),
    updatedate TIMESTAMP WITHOUT TIME ZONE,

    docnotmp CHARACTER(30),

    CONSTRAINT pk_tmp_tterima_dt
        PRIMARY KEY (idurut, docno),

    CONSTRAINT chk_tterima_dt_dk
        CHECK (dk IS NULL OR dk IN ('D', 'K')),

    CONSTRAINT chk_tterima_dt_nilai
        CHECK (nilai IS NULL OR nilai >= 0)
);


-- =========================================================
-- 6. TABLE TRANSAKSI PERMANEN
-- =========================================================
-- Struktur mengikuti temporary agar seluruh kolom terbaru ikut
-- tersedia pada tabel transaksi.

CREATE TABLE sc_trx.tterima_hd
    (LIKE sc_tmp.tterima_hd INCLUDING ALL);

CREATE TABLE sc_trx.tterima_dt
    (LIKE sc_tmp.tterima_dt INCLUDING ALL);

-- ID transaksi diisi dari tabel temporary saat finalisasi.
-- Jangan gunakan sequence temporary untuk tabel permanent.
ALTER TABLE sc_trx.tterima_hd
    ALTER COLUMN idurut DROP DEFAULT;

ALTER TABLE sc_trx.tterima_dt
    ALTER COLUMN idurut DROP DEFAULT;


-- =========================================================
-- 7. FUNCTION HITUNG SALDO HEADER
--    BALANCE = TOTAL DEBIT - TOTAL KREDIT
-- =========================================================

CREATE OR REPLACE FUNCTION sc_tmp.fn_tterima_hd_balance(
    p_docno CHARACTER(30)
)
RETURNS VOID
LANGUAGE plpgsql
AS $$
BEGIN
    UPDATE sc_tmp.tterima_hd h
    SET
        balance = COALESCE(x.debit, 0) - COALESCE(x.kredit, 0),
        updatedate = CURRENT_TIMESTAMP
    FROM (
        SELECT
            COALESCE(
                SUM(
                    CASE
                        WHEN TRIM(dk) = 'D' THEN COALESCE(nilai, 0)
                        ELSE 0
                    END
                ), 0
            ) AS debit,

            COALESCE(
                SUM(
                    CASE
                        WHEN TRIM(dk) = 'K' THEN COALESCE(nilai, 0)
                        ELSE 0
                    END
                ), 0
            ) AS kredit

        FROM sc_tmp.tterima_dt
        WHERE rtrim(docno) = rtrim(p_docno)
          AND COALESCE(TRIM(status), 'E') <> 'C'
    ) x
    WHERE rtrim(h.docno) = rtrim(p_docno);
END;
$$;


-- =========================================================
-- 8. TRIGGER HITUNG SALDO HEADER
-- =========================================================

CREATE OR REPLACE FUNCTION sc_tmp.fn_tterima_dt_balance()
RETURNS TRIGGER
LANGUAGE plpgsql
AS $$
BEGIN
    IF TG_OP = 'DELETE' THEN

        PERFORM sc_tmp.fn_tterima_hd_balance(OLD.docno);
        RETURN OLD;

    ELSIF TG_OP = 'UPDATE' THEN

        IF rtrim(OLD.docno) <> rtrim(NEW.docno) THEN
            PERFORM sc_tmp.fn_tterima_hd_balance(OLD.docno);
        END IF;

        PERFORM sc_tmp.fn_tterima_hd_balance(NEW.docno);
        RETURN NEW;

    ELSE

        PERFORM sc_tmp.fn_tterima_hd_balance(NEW.docno);
        RETURN NEW;

    END IF;
END;
$$;

DROP TRIGGER IF EXISTS tr_tterima_dt_balance
ON sc_tmp.tterima_dt;

CREATE TRIGGER tr_tterima_dt_balance
AFTER INSERT OR UPDATE OR DELETE
ON sc_tmp.tterima_dt
FOR EACH ROW
EXECUTE FUNCTION sc_tmp.fn_tterima_dt_balance();


-- =========================================================
-- 9. FUNCTION FINALISASI HEADER DAN DETAIL
--    STATUS E -> F
-- =========================================================

CREATE OR REPLACE FUNCTION sc_tmp.fn_tterima_finalize()
RETURNS TRIGGER
LANGUAGE plpgsql
AS $$
DECLARE
    v_docno         TEXT;
    v_debit         NUMERIC(18,2);
    v_kredit        NUMERIC(18,2);
    v_existing_status CHARACTER(6);
BEGIN
    IF OLD.status = 'E' AND NEW.status = 'F' THEN

        v_docno := rtrim(NEW.docno);

        -- ---------------------------------------------
        -- VALIDASI SUPPLIER
        -- ---------------------------------------------
        IF COALESCE(TRIM(NEW.kdsupplier), '') = '' THEN
            RAISE EXCEPTION 'Supplier wajib diisi.';
        END IF;

        -- ---------------------------------------------
        -- DOKUMEN SUPPLIER
        -- Field berikut BOLEH KOSONG dan TIDAK menjadi
        -- syarat finalisasi:
        -- noinvoice, nosj, noaju, nobl, noawb,
        -- nobkrev, nofakturpajak.
        -- ---------------------------------------------


      
        -- ---------------------------------------------
        -- JIKA TRANSAKSI PERMANEN SUDAH ADA DENGAN STATUS E
        -- (kasus reopen F -> E), hapus record lama beserta
        -- detail-nya sebelum dibuat kembali menjadi F.
        -- Jika status masih F, jangan overwrite transaksi final.
        -- ---------------------------------------------
        SELECT TRIM(status)
        INTO v_existing_status
        FROM sc_trx.tterima_hd
        WHERE idurut = NEW.idurut
          AND rtrim(docno) = v_docno
        FOR UPDATE;

        IF v_existing_status = 'F' THEN
            RAISE EXCEPTION
                'Tanda Terima % sudah berstatus final.',
                v_docno;
        END IF;

        IF v_existing_status = 'E' THEN
            -- Hapus SEMUA detail permanent untuk docno ini.
            -- Penting untuk kasus detail pernah dihapus saat reopen/edit.
            DELETE FROM sc_trx.tterima_dt
            WHERE rtrim(docno) = v_docno;

            DELETE FROM sc_trx.tterima_hd
            WHERE idurut = NEW.idurut
              AND rtrim(docno) = v_docno;
        END IF;

        -- ---------------------------------------------
        -- INSERT HEADER TMP -> TRX
        -- JANGAN PAKAI SELECT *
        -- ---------------------------------------------
        INSERT INTO sc_trx.tterima_hd
        (
            idurut,
            docno,
            docdate,
            cabang,
            pemohon,
            kdsupplier,
            nmsupplier,
            kotasupplier,
            alamatsupplier,
            alamatkirim,
            noinvoice,
            tglinvoice,
            nosj,
            tglsj,
            noaju,
            nobl,
            noawb,
            noinvoicebea,
            tglinvoicebea,
            nobkrev,
            nofakturpajak,
            senddate,
            currcode,
            idtax,
            kurs,
            jthtempo,
            tgljthtempo,
            isinclusive,
            dpp,
            jumlahpajak,
            total,
            beaimport,
            ppnimport,
            pphimport,
            biayaangkut,
            biayaasuransi,
            biayalain,
            totalestimasi,
            cekinvoice,
            ceksj,
            cekpenerimaan,
            cekfakturpajak,
            cekbeaimport,
            cekdokumen,
            status,
            keterangan,
            balance,
            inputby,
            inputdate,
            updateby,
            updatedate,
            printby,
            printdate,
            docnotmp,
            coabank,
            nmcoabank
        )
        SELECT
            idurut,
            docno,
            docdate,
            cabang,
            pemohon,
            kdsupplier,
            nmsupplier,
            kotasupplier,
            alamatsupplier,
            alamatkirim,
            noinvoice,
            tglinvoice,
            nosj,
            tglsj,
            noaju,
            nobl,
            noawb,
            noinvoicebea,
            tglinvoicebea,
            nobkrev,
            nofakturpajak,
            senddate,
            currcode,
            idtax,
            kurs,
            jthtempo,
            tgljthtempo,
            isinclusive,
            dpp,
            jumlahpajak,
            total,
            beaimport,
            ppnimport,
            pphimport,
            biayaangkut,
            biayaasuransi,
            biayalain,
            totalestimasi,
            cekinvoice,
            ceksj,
            cekpenerimaan,
            cekfakturpajak,
            cekbeaimport,
            cekdokumen,
            status,
            keterangan,
            balance,
            inputby,
            inputdate,
            updateby,
            updatedate,
            printby,
            printdate,
            docnotmp,
            coabank,
            nmcoabank
        FROM sc_tmp.tterima_hd
        WHERE rtrim(docno) = v_docno
          AND idurut = NEW.idurut;

        -- ---------------------------------------------
        -- INSERT DETAIL TMP -> TRX
        -- JANGAN PAKAI SELECT *
        -- ---------------------------------------------
        INSERT INTO sc_trx.tterima_dt
        (
            idurut,
            docno,
            idunique,
            nobukti,
            docref,
            noperkiraan,
            namaperkiraan,
            keterangan,
            dk,
            costprofitcenter,
            nilai,
            status,
            inputby,
            inputdate,
            updateby,
            updatedate,
            docnotmp
        )
        SELECT
            idurut,
            docno,
            idunique,
            nobukti,
            docref,
            noperkiraan,
            namaperkiraan,
            keterangan,
            dk,
            costprofitcenter,
            nilai,
            status,
            inputby,
            inputdate,
            updateby,
            updatedate,
            docnotmp
        FROM sc_tmp.tterima_dt
        WHERE rtrim(docno) = v_docno
          AND COALESCE(TRIM(status), 'E') <> 'C';

        -- ---------------------------------------------
        -- HAPUS DETAIL TEMPORARY
        -- ---------------------------------------------
        DELETE FROM sc_tmp.tterima_dt
        WHERE rtrim(docno) = v_docno;

        -- ---------------------------------------------
        -- HAPUS HEADER TEMPORARY
        -- ---------------------------------------------
        DELETE FROM sc_tmp.tterima_hd
        WHERE rtrim(docno) = v_docno
          AND idurut = NEW.idurut;

    END IF;

    RETURN NEW;
END;
$$;


-- =========================================================
-- 10. TRIGGER FINALISASI
-- =========================================================

DROP TRIGGER IF EXISTS tr_tterima_finalize
ON sc_tmp.tterima_hd;

CREATE TRIGGER tr_tterima_finalize
AFTER UPDATE OF status
ON sc_tmp.tterima_hd
FOR EACH ROW
WHEN (OLD.status = 'E' AND NEW.status = 'F')
EXECUTE FUNCTION sc_tmp.fn_tterima_finalize();


-- =========================================================
-- 11. REOPEN / EDIT ULANG TRANSAKSI FINAL
--     STATUS F -> E pada sc_trx.tterima_hd
--
--     IMPORTANT:
--     PostgreSQL tidak dapat membaca session PHP/CodeIgniter
--     secara langsung. Aplikasi WAJIB mengisi session DB:
--         SET LOCAL app.nama = 'NAMA USER';
--     sebelum UPDATE status F -> E.
-- =========================================================

CREATE OR REPLACE FUNCTION sc_trx.fn_tterima_reopen()
RETURNS TRIGGER
LANGUAGE plpgsql
AS $$
DECLARE
    v_nama VARCHAR(50);
    v_now  TIMESTAMP WITHOUT TIME ZONE;
BEGIN
    IF OLD.status = 'F' AND NEW.status = 'E' THEN

        -- Ambil nama user dari session database yang di-set aplikasi.
        v_nama :=new.inputby;

        IF v_nama IS NULL THEN
            RAISE EXCEPTION
                'Session aplikasi belum di-set. Set app.nama sebelum mengubah status F menjadi E.';
        END IF;

        v_now := CURRENT_TIMESTAMP;

        -- Jangan membuat draft ganda.
        IF EXISTS (
            SELECT 1
            FROM sc_tmp.tterima_hd t
            WHERE t.idurut = NEW.idurut
              AND rtrim(t.docno) = rtrim(NEW.docno)
        ) THEN
            RAISE EXCEPTION
                'Draft temporary Tanda Terima % sudah tersedia untuk diedit.',
                rtrim(NEW.docno);
        END IF;

        -- ---------------------------------------------
        -- COPY HEADER TRX -> TMP
        -- inputby/inputdate diganti user yang membuka kembali
        -- status dipaksa menjadi E
        -- ---------------------------------------------
        INSERT INTO sc_tmp.tterima_hd
        (
            idurut,
            docno,
            docdate,
            cabang,
            pemohon,
            kdsupplier,
            nmsupplier,
            kotasupplier,
            alamatsupplier,
            alamatkirim,
            noinvoice,
            tglinvoice,
            nosj,
            tglsj,
            noaju,
            nobl,
            noawb,
            noinvoicebea,
            tglinvoicebea,
            nobkrev,
            nofakturpajak,
            senddate,
            currcode,
            idtax,
            kurs,
            jthtempo,
            tgljthtempo,
            isinclusive,
            dpp,
            jumlahpajak,
            total,
            beaimport,
            ppnimport,
            pphimport,
            biayaangkut,
            biayaasuransi,
            biayalain,
            totalestimasi,
            cekinvoice,
            ceksj,
            cekpenerimaan,
            cekfakturpajak,
            cekbeaimport,
            cekdokumen,
            status,
            keterangan,
            balance,
            inputby,
            inputdate,
            updateby,
            updatedate,
            printby,
            printdate,
            docnotmp,
            coabank,
            nmcoabank
        )
        VALUES
        (
            NEW.idurut,
            NEW.docno,
            NEW.docdate,
            NEW.cabang,
            NEW.pemohon,
            NEW.kdsupplier,
            NEW.nmsupplier,
            NEW.kotasupplier,
            NEW.alamatsupplier,
            NEW.alamatkirim,
            NEW.noinvoice,
            NEW.tglinvoice,
            NEW.nosj,
            NEW.tglsj,
            NEW.noaju,
            NEW.nobl,
            NEW.noawb,
            NEW.noinvoicebea,
            NEW.tglinvoicebea,
            NEW.nobkrev,
            NEW.nofakturpajak,
            NEW.senddate,
            NEW.currcode,
            NEW.idtax,
            NEW.kurs,
            NEW.jthtempo,
            NEW.tgljthtempo,
            NEW.isinclusive,
            NEW.dpp,
            NEW.jumlahpajak,
            NEW.total,
            NEW.beaimport,
            NEW.ppnimport,
            NEW.pphimport,
            NEW.biayaangkut,
            NEW.biayaasuransi,
            NEW.biayalain,
            NEW.totalestimasi,
            NEW.cekinvoice,
            NEW.ceksj,
            NEW.cekpenerimaan,
            NEW.cekfakturpajak,
            NEW.cekbeaimport,
            NEW.cekdokumen,
            'E',
            NEW.keterangan,
            NEW.balance,
            v_nama,
            v_now,
            NEW.updateby,
            NEW.updatedate,
            NEW.printby,
            NEW.printdate,
            NEW.docnotmp,
            NEW.coabank,
            NEW.nmcoabank
        );

        -- ---------------------------------------------
        -- COPY DETAIL TRX -> TMP
        -- inputby/inputdate diganti user yang membuka kembali
        -- status dipaksa menjadi E
        -- ---------------------------------------------
        INSERT INTO sc_tmp.tterima_dt
        (
            idurut,
            docno,
            idunique,
            nobukti,
            docref,
            noperkiraan,
            namaperkiraan,
            keterangan,
            dk,
            costprofitcenter,
            nilai,
            status,
            inputby,
            inputdate,
            updateby,
            updatedate,
            docnotmp
        )
        SELECT
            d.idurut,
            d.docno,
            d.idunique,
            d.nobukti,
            d.docref,
            d.noperkiraan,
            d.namaperkiraan,
            d.keterangan,
            d.dk,
            d.costprofitcenter,
            d.nilai,
            'E',
            v_nama,
            v_now,
            d.updateby,
            d.updatedate,
            d.docnotmp
        FROM sc_trx.tterima_dt d
        WHERE rtrim(d.docno) = rtrim(NEW.docno)
          AND COALESCE(TRIM(d.status), 'F') <> 'C';

        -- Balance dihitung ulang oleh trigger detail temporary.
        PERFORM sc_tmp.fn_tterima_hd_balance(NEW.docno);

    END IF;

    RETURN NEW;
END;
$$;

DROP TRIGGER IF EXISTS tr_tterima_reopen
ON sc_trx.tterima_hd;

CREATE TRIGGER tr_tterima_reopen
AFTER UPDATE OF status
ON sc_trx.tterima_hd
FOR EACH ROW
WHEN (OLD.status = 'F' AND NEW.status = 'E')
EXECUTE FUNCTION sc_trx.fn_tterima_reopen();


-- =========================================================
-- 12. INDEX
-- =========================================================

CREATE INDEX idx_tterima_hd_supplier_invoice
    ON sc_trx.tterima_hd (kdsupplier, noinvoice);

CREATE INDEX idx_tterima_dt_docno
    ON sc_tmp.tterima_dt (docno);

CREATE INDEX idx_tterima_trx_dt_docno
    ON sc_trx.tterima_dt (docno);

CREATE INDEX idx_tterima_dt_nobukti
    ON sc_tmp.tterima_dt (nobukti);

CREATE INDEX idx_tterima_trx_dt_nobukti
    ON sc_trx.tterima_dt (nobukti);

CREATE INDEX idx_tterima_dt_idunique
    ON sc_tmp.tterima_dt (idunique);

CREATE INDEX idx_tterima_trx_dt_idunique
    ON sc_trx.tterima_dt (idunique);


-- =========================================================
-- 13. OWNER
-- =========================================================

ALTER TABLE sc_tmp.tterima_hd OWNER TO postgres;
ALTER TABLE sc_tmp.tterima_dt OWNER TO postgres;
ALTER TABLE sc_trx.tterima_hd OWNER TO postgres;
ALTER TABLE sc_trx.tterima_dt OWNER TO postgres;


COMMIT;
