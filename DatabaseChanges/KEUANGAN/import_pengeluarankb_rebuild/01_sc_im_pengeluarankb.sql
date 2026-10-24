BEGIN;
CREATE SCHEMA IF NOT EXISTS sc_im;

-- Satu record batch untuk satu sesi preview/import.
CREATE TABLE IF NOT EXISTS sc_im.pengeluarankb_import_batch (
    id              BIGSERIAL PRIMARY KEY,
    batch_token     VARCHAR(64) NOT NULL UNIQUE,
    nama_file       VARCHAR(255) NOT NULL,
    status          VARCHAR(20) NOT NULL DEFAULT 'PREVIEW'
                    CHECK (status IN ('PREVIEW','CONFIRMED','CANCELLED','ERROR')),
    created_by      VARCHAR(100) NOT NULL,
    created_at      TIMESTAMP WITHOUT TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP,
    confirmed_by    VARCHAR(100),
    confirmed_at    TIMESTAMP WITHOUT TIME ZONE,
    cancelled_by    VARCHAR(100),
    cancelled_at    TIMESTAMP WITHOUT TIME ZONE,
    jumlah_baris    INTEGER NOT NULL DEFAULT 0,
    jumlah_valid    INTEGER NOT NULL DEFAULT 0,
    jumlah_gagal    INTEGER NOT NULL DEFAULT 0,
    keterangan      TEXT
);

-- Header import. Raw JSON dipertahankan untuk audit dan perbaikan validasi.
CREATE TABLE IF NOT EXISTS sc_im.pengeluarankb_import_hd (
    id              BIGSERIAL PRIMARY KEY,
    batch_id        BIGINT NOT NULL REFERENCES sc_im.pengeluarankb_import_batch(id) ON DELETE CASCADE,
    nomor_baris     INTEGER NOT NULL,
    docno           VARCHAR(30) NOT NULL DEFAULT '',
    docdate         DATE,
    kdsupplier      VARCHAR(30),
    nmsupplier      VARCHAR(250),
    dibayarkepada   VARCHAR(250),
    alamatsupplier  TEXT,
    prkkas          VARCHAR(30),
    keterangan      TEXT,
    total_pembayaran NUMERIC(18,2) NOT NULL DEFAULT 0,
    currcode        VARCHAR(3) NOT NULL DEFAULT 'IDR',
    raw_data        JSONB NOT NULL DEFAULT '{}'::jsonb,
    status          VARCHAR(10) NOT NULL DEFAULT 'ERROR'
                    CHECK (status IN ('VALID','ERROR')),
    error_message   TEXT,
    created_at      TIMESTAMP WITHOUT TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (batch_id, nomor_baris)
);
CREATE INDEX IF NOT EXISTS ix_pengeluarankb_import_hd_batch_docno
    ON sc_im.pengeluarankb_import_hd(batch_id, docno);

-- Detail dinormalisasi: satu slot COA = satu record detail, maksimal 25 per header.
CREATE TABLE IF NOT EXISTS sc_im.pengeluarankb_import_dtl (
    id              BIGSERIAL PRIMARY KEY,
    batch_id        BIGINT NOT NULL REFERENCES sc_im.pengeluarankb_import_batch(id) ON DELETE CASCADE,
    header_id       BIGINT NOT NULL REFERENCES sc_im.pengeluarankb_import_hd(id) ON DELETE CASCADE,
    slot_no         SMALLINT NOT NULL CHECK (slot_no BETWEEN 1 AND 25),
    idcoa           VARCHAR(30) NOT NULL DEFAULT '',
    nmcoa           VARCHAR(250),
    keterangan      TEXT,
    reff            VARCHAR(100),
    dk              VARCHAR(1),
    nilai           NUMERIC(18,2) NOT NULL DEFAULT 0,
    status          VARCHAR(10) NOT NULL DEFAULT 'ERROR'
                    CHECK (status IN ('VALID','ERROR')),
    error_message   TEXT,
    raw_data        JSONB NOT NULL DEFAULT '{}'::jsonb,
    created_at      TIMESTAMP WITHOUT TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (header_id, slot_no)
);
CREATE INDEX IF NOT EXISTS ix_pengeluarankb_import_dtl_batch ON sc_im.pengeluarankb_import_dtl(batch_id);

COMMIT;
