/* ============================================================
   TAHAP 09
   ACCOUNTING JOURNAL HEADER : sc_trx.jurnal_hd

   Fokus:
   - Header jurnal accounting
   - Identitas jurnal
   - Trace ke transaction_dt
   - Status jurnal
   - Summary debit / credit untuk tahap berikutnya

   TAHAP 09 TIDAK menangani:
   - jurnal detail / COA
   - generate jurnal otomatis
   - posting / unposting / reverse
   - cancel

   Tidak DROP table agar aman untuk deploy ulang.
   ============================================================ */


-- ============================================================
-- 1. VALIDASI DEPENDENCY
-- ============================================================

DO $$
BEGIN
    IF to_regclass('sc_mst.journal_type') IS NULL THEN
        RAISE EXCEPTION
            'Dependency tidak ditemukan: sc_mst.journal_type';
    END IF;

    IF to_regclass('sc_trx.jurnal_hd') IS NULL
       AND to_regclass('sc_trx.transaction_dt') IS NULL THEN
        RAISE NOTICE
            'sc_trx.transaction_dt belum ada. jurnal_hd tetap dapat dibuat.';
    END IF;
END;
$$;


-- ============================================================
-- 2. CREATE TABLE BARU
-- ============================================================

CREATE TABLE IF NOT EXISTS sc_trx.jurnal_hd
(
    id BIGSERIAL NOT NULL,
    uniqueid TEXT NOT NULL,
    source_uniqueid TEXT NOT NULL DEFAULT '',

    docno VARCHAR(50) NOT NULL DEFAULT '',
    doctype VARCHAR(20) NOT NULL DEFAULT '',
    journal_type CHAR(6) NOT NULL DEFAULT '',
    trxdate DATE NOT NULL DEFAULT CURRENT_DATE,
    type_in_out CHAR(3) NOT NULL DEFAULT '',

    ref_docno VARCHAR(50) NOT NULL DEFAULT '',
    ref_doctype VARCHAR(20) NOT NULL DEFAULT '',

    idbranch CHAR(20) NOT NULL DEFAULT '',
    cabang CHAR(30) NOT NULL DEFAULT '',

    module VARCHAR(20) NOT NULL DEFAULT '',
    accounting_effect CHAR(3) NOT NULL DEFAULT 'YES',

    currcode CHAR(3) NOT NULL DEFAULT '',
    kurs NUMERIC(18,6) NOT NULL DEFAULT 1,

    total_debet NUMERIC(18,2) NOT NULL DEFAULT 0,
    total_kredit NUMERIC(18,2) NOT NULL DEFAULT 0,
    balance NUMERIC(18,2) NOT NULL DEFAULT 0,

    status VARCHAR(20) NOT NULL DEFAULT 'DRAFT',
    keterangan TEXT NOT NULL DEFAULT '',

    createdby VARCHAR(50),
    createddate TIMESTAMP WITHOUT TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updatedby VARCHAR(50),
    updateddate TIMESTAMP WITHOUT TIME ZONE,

    CONSTRAINT pk_jurnal_hd PRIMARY KEY (id),
    CONSTRAINT uq_jurnal_hd_uniqueid UNIQUE (uniqueid),
    CONSTRAINT chk_jurnal_hd_type_in_out
        CHECK (type_in_out IN ('', 'IN', 'OUT')),
    CONSTRAINT chk_jurnal_hd_status
        CHECK (status IN ('DRAFT', 'POSTED', 'REVERSED', 'CANCELLED')),
    CONSTRAINT chk_jurnal_hd_total_debet
        CHECK (total_debet >= 0),
    CONSTRAINT chk_jurnal_hd_total_kredit
        CHECK (total_kredit >= 0),
    CONSTRAINT chk_jurnal_hd_kurs
        CHECK (kurs > 0)
);


-- ============================================================
-- 3. MIGRASI TABLE LAMA
--    Semua kolom dasar dipastikan ada lebih dulu.
--    Ini memperbaiki error FK ketika jurnal_hd lama belum
--    mempunyai kolom journal_type.
-- ============================================================

DO $$
BEGIN
    -- source
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns
        WHERE table_schema='sc_trx' AND table_name='jurnal_hd'
          AND column_name='source_uniqueid'
    ) THEN
        ALTER TABLE sc_trx.jurnal_hd
            ADD COLUMN source_uniqueid TEXT NOT NULL DEFAULT '';
    END IF;

    -- document
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns
        WHERE table_schema='sc_trx' AND table_name='jurnal_hd'
          AND column_name='docno'
    ) THEN
        ALTER TABLE sc_trx.jurnal_hd
            ADD COLUMN docno VARCHAR(50) NOT NULL DEFAULT '';
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns
        WHERE table_schema='sc_trx' AND table_name='jurnal_hd'
          AND column_name='doctype'
    ) THEN
        ALTER TABLE sc_trx.jurnal_hd
            ADD COLUMN doctype VARCHAR(20) NOT NULL DEFAULT '';
    END IF;

    -- WAJIB: journal_type sebelum FK
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns
        WHERE table_schema='sc_trx' AND table_name='jurnal_hd'
          AND column_name='journal_type'
    ) THEN
        ALTER TABLE sc_trx.jurnal_hd
            ADD COLUMN journal_type CHAR(6) NOT NULL DEFAULT '';
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns
        WHERE table_schema='sc_trx' AND table_name='jurnal_hd'
          AND column_name='trxdate'
    ) THEN
        ALTER TABLE sc_trx.jurnal_hd
            ADD COLUMN trxdate DATE NOT NULL DEFAULT CURRENT_DATE;
    END IF;

    -- transaction routing
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns
        WHERE table_schema='sc_trx' AND table_name='jurnal_hd'
          AND column_name='type_in_out'
    ) THEN
        ALTER TABLE sc_trx.jurnal_hd
            ADD COLUMN type_in_out CHAR(3) NOT NULL DEFAULT '';
    END IF;

    -- reference
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns
        WHERE table_schema='sc_trx' AND table_name='jurnal_hd'
          AND column_name='ref_docno'
    ) THEN
        ALTER TABLE sc_trx.jurnal_hd
            ADD COLUMN ref_docno VARCHAR(50) NOT NULL DEFAULT '';
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns
        WHERE table_schema='sc_trx' AND table_name='jurnal_hd'
          AND column_name='ref_doctype'
    ) THEN
        ALTER TABLE sc_trx.jurnal_hd
            ADD COLUMN ref_doctype VARCHAR(20) NOT NULL DEFAULT '';
    END IF;

    -- branch
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns
        WHERE table_schema='sc_trx' AND table_name='jurnal_hd'
          AND column_name='idbranch'
    ) THEN
        ALTER TABLE sc_trx.jurnal_hd
            ADD COLUMN idbranch CHAR(20) NOT NULL DEFAULT '';
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns
        WHERE table_schema='sc_trx' AND table_name='jurnal_hd'
          AND column_name='cabang'
    ) THEN
        ALTER TABLE sc_trx.jurnal_hd
            ADD COLUMN cabang CHAR(30) NOT NULL DEFAULT '';
    END IF;

    -- routing snapshot
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns
        WHERE table_schema='sc_trx' AND table_name='jurnal_hd'
          AND column_name='module'
    ) THEN
        ALTER TABLE sc_trx.jurnal_hd
            ADD COLUMN module VARCHAR(20) NOT NULL DEFAULT '';
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns
        WHERE table_schema='sc_trx' AND table_name='jurnal_hd'
          AND column_name='accounting_effect'
    ) THEN
        ALTER TABLE sc_trx.jurnal_hd
            ADD COLUMN accounting_effect CHAR(3) NOT NULL DEFAULT 'YES';
    END IF;

    -- currency
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns
        WHERE table_schema='sc_trx' AND table_name='jurnal_hd'
          AND column_name='currcode'
    ) THEN
        ALTER TABLE sc_trx.jurnal_hd
            ADD COLUMN currcode CHAR(3) NOT NULL DEFAULT '';
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns
        WHERE table_schema='sc_trx' AND table_name='jurnal_hd'
          AND column_name='kurs'
    ) THEN
        ALTER TABLE sc_trx.jurnal_hd
            ADD COLUMN kurs NUMERIC(18,6) NOT NULL DEFAULT 1;
    END IF;

    -- summary
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns
        WHERE table_schema='sc_trx' AND table_name='jurnal_hd'
          AND column_name='total_debet'
    ) THEN
        ALTER TABLE sc_trx.jurnal_hd
            ADD COLUMN total_debet NUMERIC(18,2) NOT NULL DEFAULT 0;
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns
        WHERE table_schema='sc_trx' AND table_name='jurnal_hd'
          AND column_name='total_kredit'
    ) THEN
        ALTER TABLE sc_trx.jurnal_hd
            ADD COLUMN total_kredit NUMERIC(18,2) NOT NULL DEFAULT 0;
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns
        WHERE table_schema='sc_trx' AND table_name='jurnal_hd'
          AND column_name='balance'
    ) THEN
        ALTER TABLE sc_trx.jurnal_hd
            ADD COLUMN balance NUMERIC(18,2) NOT NULL DEFAULT 0;
    END IF;

    -- status / description / audit
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns
        WHERE table_schema='sc_trx' AND table_name='jurnal_hd'
          AND column_name='status'
    ) THEN
        ALTER TABLE sc_trx.jurnal_hd
            ADD COLUMN status VARCHAR(20) NOT NULL DEFAULT 'DRAFT';
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns
        WHERE table_schema='sc_trx' AND table_name='jurnal_hd'
          AND column_name='keterangan'
    ) THEN
        ALTER TABLE sc_trx.jurnal_hd
            ADD COLUMN keterangan TEXT NOT NULL DEFAULT '';
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns
        WHERE table_schema='sc_trx' AND table_name='jurnal_hd'
          AND column_name='createdby'
    ) THEN
        ALTER TABLE sc_trx.jurnal_hd ADD COLUMN createdby VARCHAR(50);
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns
        WHERE table_schema='sc_trx' AND table_name='jurnal_hd'
          AND column_name='createddate'
    ) THEN
        ALTER TABLE sc_trx.jurnal_hd
            ADD COLUMN createddate TIMESTAMP WITHOUT TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP;
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns
        WHERE table_schema='sc_trx' AND table_name='jurnal_hd'
          AND column_name='updatedby'
    ) THEN
        ALTER TABLE sc_trx.jurnal_hd ADD COLUMN updatedby VARCHAR(50);
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns
        WHERE table_schema='sc_trx' AND table_name='jurnal_hd'
          AND column_name='updateddate'
    ) THEN
        ALTER TABLE sc_trx.jurnal_hd
            ADD COLUMN updateddate TIMESTAMP WITHOUT TIME ZONE;
    END IF;

    -- identity jurnal dipastikan terakhir karena dapat membutuhkan
    -- kolom document/source yang sudah dijamin ada di atas.
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns
        WHERE table_schema='sc_trx' AND table_name='jurnal_hd'
          AND column_name='uniqueid'
    ) THEN
        ALTER TABLE sc_trx.jurnal_hd
            ADD COLUMN uniqueid TEXT;

        UPDATE sc_trx.jurnal_hd
        SET uniqueid = 'JRNL-' || md5(
            concat_ws('|',
                COALESCE(TRIM(docno::text), ''),
                COALESCE(TRIM(doctype::text), ''),
                COALESCE(TRIM(journal_type::text), ''),
                COALESCE(TRIM(idbranch::text), ''),
                COALESCE(TRIM(source_uniqueid::text), ''),
                id::text
            )
        )
        WHERE uniqueid IS NULL OR BTRIM(uniqueid)='';

        ALTER TABLE sc_trx.jurnal_hd
            ALTER COLUMN uniqueid SET NOT NULL;
    END IF;
END;
$$;


-- ============================================================
-- 4. NORMALISASI DATA LAMA
-- ============================================================

UPDATE sc_trx.jurnal_hd
SET
    source_uniqueid = COALESCE(source_uniqueid, ''),
    journal_type = COALESCE(journal_type, ''),
    docno = COALESCE(docno, ''),
    doctype = COALESCE(doctype, ''),
    type_in_out = COALESCE(type_in_out, ''),
    ref_docno = COALESCE(ref_docno, ''),
    ref_doctype = COALESCE(ref_doctype, ''),
    idbranch = COALESCE(idbranch, ''),
    cabang = COALESCE(cabang, ''),
    module = COALESCE(module, ''),
    accounting_effect = COALESCE(accounting_effect, 'YES'),
    currcode = COALESCE(currcode, ''),
    kurs = CASE WHEN kurs IS NULL OR kurs <= 0 THEN 1 ELSE kurs END,
    total_debet = COALESCE(total_debet, 0),
    total_kredit = COALESCE(total_kredit, 0),
    balance = COALESCE(balance, 0),
    status = COALESCE(status, 'DRAFT'),
    keterangan = COALESCE(keterangan, ''),
    createddate = COALESCE(createddate, CURRENT_TIMESTAMP);


-- ============================================================
-- 5. CONSTRAINT NON-FK
--    Untuk table legacy, constraint hanya dipasang jika seluruh
--    data lama sudah memenuhi rule. Tidak mengubah data lama
--    secara otomatis.
-- ============================================================

DO $$
DECLARE
    v_bad BIGINT;
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM pg_constraint
        WHERE conname='uq_jurnal_hd_uniqueid'
          AND conrelid='sc_trx.jurnal_hd'::regclass
    ) THEN
        IF EXISTS (
            SELECT uniqueid
            FROM sc_trx.jurnal_hd
            GROUP BY uniqueid
            HAVING COUNT(*) > 1
        ) THEN
            RAISE NOTICE 'UNIQUE uq_jurnal_hd_uniqueid dilewati karena ada duplicate uniqueid pada data lama.';
        ELSE
            ALTER TABLE sc_trx.jurnal_hd
                ADD CONSTRAINT uq_jurnal_hd_uniqueid UNIQUE (uniqueid);
        END IF;
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM pg_constraint
        WHERE conname='chk_jurnal_hd_type_in_out'
          AND conrelid='sc_trx.jurnal_hd'::regclass
    ) THEN
        SELECT COUNT(*) INTO v_bad
        FROM sc_trx.jurnal_hd
        WHERE type_in_out IS NULL
           OR type_in_out NOT IN ('', 'IN', 'OUT');

        IF v_bad = 0 THEN
            ALTER TABLE sc_trx.jurnal_hd
                ADD CONSTRAINT chk_jurnal_hd_type_in_out
                CHECK (type_in_out IN ('', 'IN', 'OUT'));
        ELSE
            RAISE NOTICE 'CHECK chk_jurnal_hd_type_in_out dilewati: % row legacy tidak valid.', v_bad;
        END IF;
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM pg_constraint
        WHERE conname='chk_jurnal_hd_status'
          AND conrelid='sc_trx.jurnal_hd'::regclass
    ) THEN
        SELECT COUNT(*) INTO v_bad
        FROM sc_trx.jurnal_hd
        WHERE status IS NULL
           OR UPPER(BTRIM(status)) NOT IN ('DRAFT', 'POSTED', 'REVERSED', 'CANCELLED');

        IF v_bad = 0 THEN
            ALTER TABLE sc_trx.jurnal_hd
                ADD CONSTRAINT chk_jurnal_hd_status
                CHECK (status IN ('DRAFT', 'POSTED', 'REVERSED', 'CANCELLED'));
        ELSE
            RAISE NOTICE 'CHECK chk_jurnal_hd_status dilewati: % row legacy memiliki status di luar DRAFT/POSTED/REVERSED/CANCELLED.', v_bad;
        END IF;
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM pg_constraint
        WHERE conname='chk_jurnal_hd_total_debet'
          AND conrelid='sc_trx.jurnal_hd'::regclass
    ) THEN
        SELECT COUNT(*) INTO v_bad
        FROM sc_trx.jurnal_hd
        WHERE total_debet IS NULL OR total_debet < 0;

        IF v_bad = 0 THEN
            ALTER TABLE sc_trx.jurnal_hd
                ADD CONSTRAINT chk_jurnal_hd_total_debet CHECK (total_debet >= 0);
        ELSE
            RAISE NOTICE 'CHECK chk_jurnal_hd_total_debet dilewati: % row legacy tidak valid.', v_bad;
        END IF;
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM pg_constraint
        WHERE conname='chk_jurnal_hd_total_kredit'
          AND conrelid='sc_trx.jurnal_hd'::regclass
    ) THEN
        SELECT COUNT(*) INTO v_bad
        FROM sc_trx.jurnal_hd
        WHERE total_kredit IS NULL OR total_kredit < 0;

        IF v_bad = 0 THEN
            ALTER TABLE sc_trx.jurnal_hd
                ADD CONSTRAINT chk_jurnal_hd_total_kredit CHECK (total_kredit >= 0);
        ELSE
            RAISE NOTICE 'CHECK chk_jurnal_hd_total_kredit dilewati: % row legacy tidak valid.', v_bad;
        END IF;
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM pg_constraint
        WHERE conname='chk_jurnal_hd_kurs'
          AND conrelid='sc_trx.jurnal_hd'::regclass
    ) THEN
        SELECT COUNT(*) INTO v_bad
        FROM sc_trx.jurnal_hd
        WHERE kurs IS NULL OR kurs <= 0;

        IF v_bad = 0 THEN
            ALTER TABLE sc_trx.jurnal_hd
                ADD CONSTRAINT chk_jurnal_hd_kurs CHECK (kurs > 0);
        ELSE
            RAISE NOTICE 'CHECK chk_jurnal_hd_kurs dilewati: % row legacy tidak valid.', v_bad;
        END IF;
    END IF;
END;
$$;


-- ============================================================
-- 6. FOREIGN KEY JOURNAL TYPE
--    Dipasang setelah kolom journal_type dipastikan ada.
--    Jika data lama mengandung journal_type yang belum ada di
--    master, FK tidak dipaksakan agar deployment tidak rusak.
-- ============================================================

DO $$
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM pg_constraint
        WHERE conname='fk_jurnal_hd_journal_type'
          AND conrelid='sc_trx.jurnal_hd'::regclass
    ) THEN
        IF NOT EXISTS (
            SELECT 1
            FROM sc_trx.jurnal_hd h
            LEFT JOIN sc_mst.journal_type jt
              ON jt.journal_type = h.journal_type
            WHERE jt.journal_type IS NULL
        ) THEN
            ALTER TABLE sc_trx.jurnal_hd
                ADD CONSTRAINT fk_jurnal_hd_journal_type
                FOREIGN KEY (journal_type)
                REFERENCES sc_mst.journal_type(journal_type);
        ELSE
            RAISE NOTICE
                'FK fk_jurnal_hd_journal_type dilewati karena data lama masih memiliki journal_type yang belum terdaftar di sc_mst.journal_type.';
        END IF;
    END IF;
END;
$$;


-- ============================================================
-- 7. FUNCTION IDENTITY JURNAL
-- ============================================================

CREATE OR REPLACE FUNCTION sc_trx.fn_journal_uniqueid
(
    p_docno TEXT,
    p_doctype TEXT,
    p_journal_type TEXT,
    p_idbranch TEXT,
    p_source_uniqueid TEXT
)
RETURNS TEXT
LANGUAGE SQL
IMMUTABLE
AS $$
    SELECT 'JRNL-' || md5(
        concat_ws(
            '|',
            COALESCE(TRIM(p_docno), ''),
            COALESCE(TRIM(p_doctype), ''),
            COALESCE(TRIM(p_journal_type), ''),
            COALESCE(TRIM(p_idbranch), ''),
            COALESCE(TRIM(p_source_uniqueid), '')
        )
    );
$$;


-- ============================================================
-- 8. INDEX ACCOUNTING YANG DIPERLUKAN
-- ============================================================

CREATE INDEX IF NOT EXISTS idx_jurnal_hd_source_uniqueid
    ON sc_trx.jurnal_hd(source_uniqueid);

CREATE INDEX IF NOT EXISTS idx_jurnal_hd_doc
    ON sc_trx.jurnal_hd(docno, doctype, journal_type);

CREATE INDEX IF NOT EXISTS idx_jurnal_hd_trxdate
    ON sc_trx.jurnal_hd(trxdate);

CREATE INDEX IF NOT EXISTS idx_jurnal_hd_branch_date
    ON sc_trx.jurnal_hd(idbranch, trxdate);

CREATE INDEX IF NOT EXISTS idx_jurnal_hd_status
    ON sc_trx.jurnal_hd(status);


-- ============================================================
-- 9. CEK STRUKTUR
-- ============================================================

SELECT
    column_name,
    data_type,
    character_maximum_length,
    numeric_precision,
    numeric_scale,
    is_nullable,
    column_default
FROM information_schema.columns
WHERE table_schema='sc_trx'
  AND table_name='jurnal_hd'
ORDER BY ordinal_position;

-- ============================================================
-- 9. CEK DATA LEGACY YANG MASIH BELUM MEMENUHI CONSTRAINT
-- ============================================================

SELECT 'STATUS' AS masalah, status::text AS nilai, COUNT(*) AS jumlah
FROM sc_trx.jurnal_hd
WHERE status IS NULL
   OR UPPER(BTRIM(status)) NOT IN ('DRAFT', 'POSTED', 'REVERSED', 'CANCELLED')
GROUP BY status
ORDER BY status;

SELECT 'TYPE_IN_OUT' AS masalah, type_in_out::text AS nilai, COUNT(*) AS jumlah
FROM sc_trx.jurnal_hd
WHERE type_in_out IS NULL
   OR type_in_out NOT IN ('', 'IN', 'OUT')
GROUP BY type_in_out
ORDER BY type_in_out;

SELECT 'JOURNAL_TYPE_ORPHAN' AS masalah, h.journal_type::text AS nilai, COUNT(*) AS jumlah
FROM sc_trx.jurnal_hd h
LEFT JOIN sc_mst.journal_type jt
  ON jt.journal_type = h.journal_type
WHERE jt.journal_type IS NULL
GROUP BY h.journal_type
ORDER BY h.journal_type;
