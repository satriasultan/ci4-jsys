/* ============================================================
   TAHAP 10
   ACCOUNTING JOURNAL DETAIL : sc_trx.jurnal_dt

   FOKUS:
   - detail COA jurnal
   - debit / credit
   - status jurnal detail
   - source tracing
   - currency snapshot
   - integrity terhadap jurnal_hd
   - sinkronisasi total jurnal_hd

   CATATAN:
   - Tidak DROP TABLE.
   - Tidak membuat generator accounting.
   - Generator dari transaction_dt -> journal_type_coa
     -> jurnal_hd -> jurnal_dt dibuat pada tahap posting accounting.
   - Tidak membuat GL balance / posting / reverse di tahap ini.
   ============================================================ */


CREATE SCHEMA IF NOT EXISTS sc_trx;


/* ============================================================
   1. CREATE TABLE / REPAIR EXISTING TABLE
   ============================================================ */

CREATE TABLE IF NOT EXISTS sc_trx.jurnal_dt
(
    id                  BIGSERIAL NOT NULL,
    jurnal_id           BIGINT NOT NULL,
    journal_type        CHAR(6) NOT NULL DEFAULT '',
    source_uniqueid     TEXT NOT NULL DEFAULT '',
    source_line_no      INT,
    seq                 INT NOT NULL DEFAULT 1,

    account_role        VARCHAR(50) NOT NULL DEFAULT '',
    value_source        VARCHAR(50) NOT NULL DEFAULT '',

    idcoa               VARCHAR(20) NOT NULL,

    debet               NUMERIC(18,2) NOT NULL DEFAULT 0,
    kredit              NUMERIC(18,2) NOT NULL DEFAULT 0,

    ref_docno           VARCHAR(50) NOT NULL DEFAULT '',
    ref_doctype         VARCHAR(20) NOT NULL DEFAULT '',
    status              VARCHAR(20) NOT NULL DEFAULT 'DRAFT',

    keterangan          TEXT NOT NULL DEFAULT '',

    currcode            CHAR(3) NOT NULL DEFAULT '',
    kurs                NUMERIC(18,6) NOT NULL DEFAULT 1,

    createdby           VARCHAR(50),
    createddate         TIMESTAMP WITHOUT TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updatedby           VARCHAR(50),
    updateddate         TIMESTAMP WITHOUT TIME ZONE,

    CONSTRAINT pk_jurnal_dt PRIMARY KEY (id),
    CONSTRAINT chk_jurnal_dt_seq CHECK (seq > 0),
    CONSTRAINT chk_jurnal_dt_debet CHECK (debet >= 0),
    CONSTRAINT chk_jurnal_dt_kredit CHECK (kredit >= 0),
    CONSTRAINT chk_jurnal_dt_debet_kredit CHECK
    (
        (debet > 0 AND kredit = 0)
        OR
        (debet = 0 AND kredit > 0)
    ),
    CONSTRAINT chk_jurnal_dt_kurs CHECK (kurs > 0)
);


/* ============================================================
   Tambahkan kolom yang belum ada pada table lama.
   ============================================================ */

DO $$
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns
        WHERE table_schema='sc_trx' AND table_name='jurnal_dt'
          AND column_name='jurnal_id'
    ) THEN
        ALTER TABLE sc_trx.jurnal_dt ADD COLUMN jurnal_id BIGINT;
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns
        WHERE table_schema='sc_trx' AND table_name='jurnal_dt'
          AND column_name='journal_type'
    ) THEN
        ALTER TABLE sc_trx.jurnal_dt
            ADD COLUMN journal_type CHAR(6) NOT NULL DEFAULT '';
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns
        WHERE table_schema='sc_trx' AND table_name='jurnal_dt'
          AND column_name='source_uniqueid'
    ) THEN
        ALTER TABLE sc_trx.jurnal_dt ADD COLUMN source_uniqueid TEXT DEFAULT '';
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns
        WHERE table_schema='sc_trx' AND table_name='jurnal_dt'
          AND column_name='source_line_no'
    ) THEN
        ALTER TABLE sc_trx.jurnal_dt ADD COLUMN source_line_no INT;
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns
        WHERE table_schema='sc_trx' AND table_name='jurnal_dt'
          AND column_name='seq'
    ) THEN
        ALTER TABLE sc_trx.jurnal_dt ADD COLUMN seq INT DEFAULT 1;
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns
        WHERE table_schema='sc_trx' AND table_name='jurnal_dt'
          AND column_name='account_role'
    ) THEN
        ALTER TABLE sc_trx.jurnal_dt ADD COLUMN account_role VARCHAR(50) DEFAULT '';
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns
        WHERE table_schema='sc_trx' AND table_name='jurnal_dt'
          AND column_name='value_source'
    ) THEN
        ALTER TABLE sc_trx.jurnal_dt ADD COLUMN value_source VARCHAR(50) DEFAULT '';
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns
        WHERE table_schema='sc_trx' AND table_name='jurnal_dt'
          AND column_name='idcoa'
    ) THEN
        ALTER TABLE sc_trx.jurnal_dt ADD COLUMN idcoa VARCHAR(20) DEFAULT '';
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns
        WHERE table_schema='sc_trx' AND table_name='jurnal_dt'
          AND column_name='debet'
    ) THEN
        ALTER TABLE sc_trx.jurnal_dt ADD COLUMN debet NUMERIC(18,2) DEFAULT 0;
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns
        WHERE table_schema='sc_trx' AND table_name='jurnal_dt'
          AND column_name='kredit'
    ) THEN
        ALTER TABLE sc_trx.jurnal_dt ADD COLUMN kredit NUMERIC(18,2) DEFAULT 0;
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns
        WHERE table_schema='sc_trx' AND table_name='jurnal_dt'
          AND column_name='ref_docno'
    ) THEN
        ALTER TABLE sc_trx.jurnal_dt ADD COLUMN ref_docno VARCHAR(50) DEFAULT '';
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns
        WHERE table_schema='sc_trx' AND table_name='jurnal_dt'
          AND column_name='ref_doctype'
    ) THEN
        ALTER TABLE sc_trx.jurnal_dt ADD COLUMN ref_doctype VARCHAR(20) DEFAULT '';
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns
        WHERE table_schema='sc_trx' AND table_name='jurnal_dt'
          AND column_name='status'
    ) THEN
        ALTER TABLE sc_trx.jurnal_dt ADD COLUMN status VARCHAR(20) DEFAULT 'DRAFT';
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns
        WHERE table_schema='sc_trx' AND table_name='jurnal_dt'
          AND column_name='keterangan'
    ) THEN
        ALTER TABLE sc_trx.jurnal_dt ADD COLUMN keterangan TEXT DEFAULT '';
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns
        WHERE table_schema='sc_trx' AND table_name='jurnal_dt'
          AND column_name='currcode'
    ) THEN
        ALTER TABLE sc_trx.jurnal_dt ADD COLUMN currcode CHAR(3) DEFAULT '';
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns
        WHERE table_schema='sc_trx' AND table_name='jurnal_dt'
          AND column_name='kurs'
    ) THEN
        ALTER TABLE sc_trx.jurnal_dt ADD COLUMN kurs NUMERIC(18,6) DEFAULT 1;
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns
        WHERE table_schema='sc_trx' AND table_name='jurnal_dt'
          AND column_name='createdby'
    ) THEN
        ALTER TABLE sc_trx.jurnal_dt ADD COLUMN createdby VARCHAR(50);
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns
        WHERE table_schema='sc_trx' AND table_name='jurnal_dt'
          AND column_name='createddate'
    ) THEN
        ALTER TABLE sc_trx.jurnal_dt
            ADD COLUMN createddate TIMESTAMP WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP;
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns
        WHERE table_schema='sc_trx' AND table_name='jurnal_dt'
          AND column_name='updatedby'
    ) THEN
        ALTER TABLE sc_trx.jurnal_dt ADD COLUMN updatedby VARCHAR(50);
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns
        WHERE table_schema='sc_trx' AND table_name='jurnal_dt'
          AND column_name='updateddate'
    ) THEN
        ALTER TABLE sc_trx.jurnal_dt
            ADD COLUMN updateddate TIMESTAMP WITHOUT TIME ZONE;
    END IF;
END
$$;


/* ============================================================
   2. NORMALISASI NILAI DEFAULT UNTUK DATA LAMA
   Hanya mengisi NULL pada kolom yang memang punya default logis.
   ============================================================ */

/* =========================================================================
   BACKFILL journal_type DARI jurnal_hd
   ========================================================================= */
UPDATE sc_trx.jurnal_dt jd
SET journal_type = jh.journal_type
FROM sc_trx.jurnal_hd jh
WHERE jh.id = jd.jurnal_id
  AND (jd.journal_type IS NULL OR BTRIM(jd.journal_type::TEXT) = '');

UPDATE sc_trx.jurnal_dt
SET journal_type = BTRIM(journal_type::TEXT)
WHERE journal_type IS NOT NULL;


UPDATE sc_trx.jurnal_dt
SET
    source_uniqueid = COALESCE(source_uniqueid, ''),
    seq             = COALESCE(seq, 1),
    account_role    = COALESCE(account_role, ''),
    value_source    = COALESCE(value_source, ''),
    idcoa           = COALESCE(idcoa, ''),
    debet           = COALESCE(debet, 0),
    kredit          = COALESCE(kredit, 0),
    ref_docno       = COALESCE(ref_docno, ''),
    ref_doctype     = COALESCE(ref_doctype, ''),
    status          = COALESCE(status, 'DRAFT'),
    keterangan      = COALESCE(keterangan, ''),
    currcode        = COALESCE(currcode, ''),
    kurs            = COALESCE(kurs, 1),
    createddate     = COALESCE(createddate, CURRENT_TIMESTAMP)
WHERE
    source_uniqueid IS NULL
    OR seq IS NULL
    OR account_role IS NULL
    OR value_source IS NULL
    OR idcoa IS NULL
    OR debet IS NULL
    OR kredit IS NULL
    OR ref_docno IS NULL
    OR ref_doctype IS NULL
    OR status IS NULL
    OR keterangan IS NULL
    OR currcode IS NULL
    OR kurs IS NULL
    OR createddate IS NULL;


/* ============================================================
   2A. SINKRONISASI STATUS DETAIL DENGAN HEADER
   ============================================================ */

DO $$
BEGIN
    IF to_regclass('sc_trx.jurnal_hd') IS NOT NULL THEN
        UPDATE sc_trx.jurnal_dt jd
        SET status = COALESCE(jh.status, 'DRAFT')
        FROM sc_trx.jurnal_hd jh
        WHERE jh.id = jd.jurnal_id
          AND jd.status IS DISTINCT FROM COALESCE(jh.status, 'DRAFT');
    END IF;

    ALTER TABLE sc_trx.jurnal_dt
        ALTER COLUMN status SET DEFAULT 'DRAFT';

    ALTER TABLE sc_trx.jurnal_dt
        ALTER COLUMN status SET NOT NULL;
END
$$;


/* ============================================================
   2B. STATUS CONSTRAINT
   ============================================================ */

DO $$
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM pg_constraint
        WHERE conrelid='sc_trx.jurnal_dt'::regclass
          AND conname='chk_jurnal_dt_status'
    ) THEN
        IF NOT EXISTS (
            SELECT 1 FROM sc_trx.jurnal_dt
            WHERE status NOT IN ('DRAFT','POSTED','REVERSED','CANCELLED')
        ) THEN
            ALTER TABLE sc_trx.jurnal_dt
                ADD CONSTRAINT chk_jurnal_dt_status
                CHECK (status IN ('DRAFT','POSTED','REVERSED','CANCELLED'));
        ELSE
            RAISE NOTICE 'CHECK status jurnal_dt dilewati karena ada data legacy tidak valid.';
        END IF;
    END IF;
END
$$;


/* ============================================================
   3. PRIMARY KEY
   ============================================================ */

DO $$
BEGIN
    IF NOT EXISTS (
        SELECT 1
        FROM pg_constraint
        WHERE conrelid = 'sc_trx.jurnal_dt'::regclass
          AND contype = 'p'
    ) THEN
        IF NOT EXISTS (
            SELECT 1
            FROM sc_trx.jurnal_dt
            WHERE id IS NULL
        )
        AND NOT EXISTS (
            SELECT id
            FROM sc_trx.jurnal_dt
            GROUP BY id
            HAVING COUNT(*) > 1
        ) THEN
            ALTER TABLE sc_trx.jurnal_dt
                ADD CONSTRAINT pk_jurnal_dt PRIMARY KEY (id);
        ELSE
            RAISE NOTICE 'PK jurnal_dt tidak dipasang karena data legacy tidak valid.';
        END IF;
    END IF;
END
$$;


/* ============================================================
   4. FK jurnal_id -> jurnal_hd
   Tidak menggunakan ON DELETE CASCADE agar jurnal accounting
   tidak ikut terhapus tanpa sengaja.
   ============================================================ */

DO $$
BEGIN
    IF NOT EXISTS (
        SELECT 1
        FROM pg_constraint
        WHERE conrelid = 'sc_trx.jurnal_dt'::regclass
          AND contype = 'f'
          AND conname = 'fk_jurnal_dt_jurnal_hd'
    ) THEN
        IF NOT EXISTS (
            SELECT 1
            FROM sc_trx.jurnal_dt jd
            LEFT JOIN sc_trx.jurnal_hd jh
              ON jh.id = jd.jurnal_id
            WHERE jd.jurnal_id IS NOT NULL
              AND jh.id IS NULL
        ) THEN
            ALTER TABLE sc_trx.jurnal_dt
                ADD CONSTRAINT fk_jurnal_dt_jurnal_hd
                FOREIGN KEY (jurnal_id)
                REFERENCES sc_trx.jurnal_hd(id);
        ELSE
            RAISE NOTICE 'FK jurnal_dt.jurnal_id tidak dipasang karena ada orphan jurnal_id.';
        END IF;
    END IF;
END
$$;


/* ============================================================
   4A. FK journal_type -> sc_mst.journal_type
   ============================================================ */

DO $$
DECLARE
    v_invalid BIGINT;
BEGIN
    SELECT COUNT(*)
    INTO v_invalid
    FROM sc_trx.jurnal_dt jd
    LEFT JOIN sc_mst.journal_type jt
      ON jt.journal_type = jd.journal_type
    WHERE NULLIF(BTRIM(jd.journal_type::TEXT), '') IS NULL
       OR jt.journal_type IS NULL;

    IF v_invalid > 0 THEN
        RAISE NOTICE
            'FK jurnal_dt.journal_type dilewati karena % baris kosong/tidak terdaftar.',
            v_invalid;
    ELSE
        IF NOT EXISTS (
            SELECT 1
            FROM pg_constraint
            WHERE conrelid = 'sc_trx.jurnal_dt'::regclass
              AND conname = 'fk_jurnal_dt_journal_type'
        ) THEN
            ALTER TABLE sc_trx.jurnal_dt
                ADD CONSTRAINT fk_jurnal_dt_journal_type
                FOREIGN KEY (journal_type)
                REFERENCES sc_mst.journal_type(journal_type);
        END IF;
    END IF;
END
$$;


CREATE INDEX IF NOT EXISTS idx_jurnal_dt_journal_type
    ON sc_trx.jurnal_dt(journal_type);


/* ============================================================
   5. CHECK CONSTRAINTS
   Dipasang hanya jika data existing memenuhi aturan.
   ============================================================ */

DO $$
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM pg_constraint
        WHERE conrelid='sc_trx.jurnal_dt'::regclass
          AND conname='chk_jurnal_dt_seq'
    ) THEN
        IF NOT EXISTS (
            SELECT 1 FROM sc_trx.jurnal_dt WHERE seq IS NULL OR seq <= 0
        ) THEN
            ALTER TABLE sc_trx.jurnal_dt
                ADD CONSTRAINT chk_jurnal_dt_seq CHECK (seq > 0);
        ELSE
            RAISE NOTICE 'CHECK seq dilewati karena ada data legacy seq <= 0 / NULL.';
        END IF;
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM pg_constraint
        WHERE conrelid='sc_trx.jurnal_dt'::regclass
          AND conname='chk_jurnal_dt_debet'
    ) THEN
        IF NOT EXISTS (
            SELECT 1 FROM sc_trx.jurnal_dt WHERE debet IS NULL OR debet < 0
        ) THEN
            ALTER TABLE sc_trx.jurnal_dt
                ADD CONSTRAINT chk_jurnal_dt_debet CHECK (debet >= 0);
        ELSE
            RAISE NOTICE 'CHECK debet dilewati karena ada nilai negatif / NULL.';
        END IF;
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM pg_constraint
        WHERE conrelid='sc_trx.jurnal_dt'::regclass
          AND conname='chk_jurnal_dt_kredit'
    ) THEN
        IF NOT EXISTS (
            SELECT 1 FROM sc_trx.jurnal_dt WHERE kredit IS NULL OR kredit < 0
        ) THEN
            ALTER TABLE sc_trx.jurnal_dt
                ADD CONSTRAINT chk_jurnal_dt_kredit CHECK (kredit >= 0);
        ELSE
            RAISE NOTICE 'CHECK kredit dilewati karena ada nilai negatif / NULL.';
        END IF;
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM pg_constraint
        WHERE conrelid='sc_trx.jurnal_dt'::regclass
          AND conname='chk_jurnal_dt_debet_kredit'
    ) THEN
        IF NOT EXISTS (
            SELECT 1
            FROM sc_trx.jurnal_dt
            WHERE NOT (
                (debet > 0 AND kredit = 0)
                OR
                (debet = 0 AND kredit > 0)
            )
        ) THEN
            ALTER TABLE sc_trx.jurnal_dt
                ADD CONSTRAINT chk_jurnal_dt_debet_kredit
                CHECK (
                    (debet > 0 AND kredit = 0)
                    OR
                    (debet = 0 AND kredit > 0)
                );
        ELSE
            RAISE NOTICE 'CHECK debit/kredit dilewati karena ada data legacy tidak valid.';
        END IF;
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM pg_constraint
        WHERE conrelid='sc_trx.jurnal_dt'::regclass
          AND conname='chk_jurnal_dt_kurs'
    ) THEN
        IF NOT EXISTS (
            SELECT 1 FROM sc_trx.jurnal_dt WHERE kurs IS NULL OR kurs <= 0
        ) THEN
            ALTER TABLE sc_trx.jurnal_dt
                ADD CONSTRAINT chk_jurnal_dt_kurs CHECK (kurs > 0);
        ELSE
            RAISE NOTICE 'CHECK kurs dilewati karena ada data legacy kurs <= 0 / NULL.';
        END IF;
    END IF;
END
$$;


/* ============================================================
   6. INDEX YANG DIPERLUKAN

   seq bukan UNIQUE karena data legacy dapat mempunyai seq yang sama.
   Hal tersebut tidak mengubah nilai transaksi maupun debit/kredit.
   seq hanya digunakan sebagai urutan tampilan/detail jurnal.
   ============================================================ */

CREATE INDEX IF NOT EXISTS idx_jurnal_dt_jurnal_seq
    ON sc_trx.jurnal_dt(jurnal_id, seq);

CREATE INDEX IF NOT EXISTS idx_jurnal_dt_source_uniqueid
    ON sc_trx.jurnal_dt(source_uniqueid);

CREATE INDEX IF NOT EXISTS idx_jurnal_dt_idcoa
    ON sc_trx.jurnal_dt(idcoa);


/* ============================================================
   DUPLICATE BUSINESS KEY
   ============================================================

   Hard rule:
       tidak boleh ada jurnal_dt lain dengan kombinasi identik:

       idcoa
       debet
       kredit
       ref_docno
       ref_doctype
       source_uniqueid
       keterangan

   journal_id sengaja tidak termasuk.
   ============================================================ */

/*
   DUPLICATE PROTECTION FINAL

   Hanya detail ACTIVE yang dicegah duplicate:
       DRAFT / POSTED

   Detail CANCELLED / REVERSED tetap menjadi histori dan
   boleh mempunyai kombinasi yang sama jika transaksi baru
   memang dibuat lagi setelah transaksi lama dibatalkan.
*/
DROP INDEX IF EXISTS sc_trx.uq_jurnal_dt_business_duplicate;

CREATE UNIQUE INDEX uq_jurnal_dt_business_duplicate
ON sc_trx.jurnal_dt
(
    idcoa,
    debet,
    kredit,
    ref_docno,
    ref_doctype,
    source_uniqueid,
    keterangan
)
NULLS NOT DISTINCT
WHERE status IN ('DRAFT','POSTED');


/* ============================================================
   DUPLICATE INSERT PROTECTION
   ============================================================ */

CREATE OR REPLACE FUNCTION sc_trx.fn_prevent_duplicate_jurnal_dt()
RETURNS TRIGGER
LANGUAGE plpgsql
AS $$
DECLARE
    v_existing_id     BIGINT;
    v_existing_jurnal BIGINT;
BEGIN

    SELECT
        jd.id,
        jd.jurnal_id
    INTO
        v_existing_id,
        v_existing_jurnal
    FROM sc_trx.jurnal_dt jd
    WHERE jd.id IS DISTINCT FROM NEW.id
      AND jd.status IN ('DRAFT','POSTED')
      AND COALESCE(BTRIM(COALESCE(NEW.status, 'DRAFT')), 'DRAFT') IN ('DRAFT','POSTED')
      AND COALESCE(BTRIM(jd.idcoa), '') =
          COALESCE(BTRIM(NEW.idcoa), '')
      AND COALESCE(jd.debet, 0) =
          COALESCE(NEW.debet, 0)
      AND COALESCE(jd.kredit, 0) =
          COALESCE(NEW.kredit, 0)
      AND COALESCE(jd.ref_docno, '') =
          COALESCE(NEW.ref_docno, '')
      AND COALESCE(jd.ref_doctype, '') =
          COALESCE(NEW.ref_doctype, '')
      AND COALESCE(jd.source_uniqueid, '') =
          COALESCE(NEW.source_uniqueid, '')
      AND COALESCE(jd.keterangan, '') =
          COALESCE(NEW.keterangan, '')
    ORDER BY jd.id
    LIMIT 1;

    IF v_existing_id IS NOT NULL THEN

        RAISE EXCEPTION
            'DUPLICATE JURNAL DETAIL DITOLAK. Existing jurnal_dt.id=%, jurnal_id=%. '
            'Kombinasi idcoa, debet, kredit, ref_docno, ref_doctype, '
            'source_uniqueid, keterangan sudah ada.',
            v_existing_id,
            v_existing_jurnal
            USING ERRCODE = '23505';

    END IF;

    RETURN NEW;

END;
$$;


DROP TRIGGER IF EXISTS trg_prevent_duplicate_jurnal_dt
ON sc_trx.jurnal_dt;

CREATE TRIGGER trg_prevent_duplicate_jurnal_dt
BEFORE INSERT
ON sc_trx.jurnal_dt
FOR EACH ROW
EXECUTE FUNCTION sc_trx.fn_prevent_duplicate_jurnal_dt();


/* ============================================================
   7. VALIDASI COA

   COA harus terdaftar di sc_mst.coa.
   ============================================================ */

CREATE OR REPLACE FUNCTION sc_trx.fn_validate_jurnal_coa()
RETURNS TRIGGER
LANGUAGE plpgsql
AS $$
BEGIN
    IF COALESCE(BTRIM(NEW.idcoa), '') = '' THEN
        RAISE EXCEPTION 'COA jurnal tidak boleh kosong.';
    END IF;

    IF NOT EXISTS (
        SELECT 1
        FROM sc_mst.coa c
        WHERE c.idcoa = NEW.idcoa
    ) THEN
        RAISE EXCEPTION
            'COA % tidak ditemukan pada sc_mst.coa',
            NEW.idcoa;
    END IF;

    RETURN NEW;
END;
$$;


/* ============================================================
   8. VALIDASI STATUS HEADER

   Detail hanya boleh berubah ketika jurnal_hd = DRAFT.
   UPDATE yang memindahkan detail ke jurnal lain akan memeriksa
   header OLD dan NEW.
   ============================================================ */

CREATE OR REPLACE FUNCTION sc_trx.fn_validate_jurnal_detail_status()
RETURNS TRIGGER
LANGUAGE plpgsql
AS $$
DECLARE
    v_status_old VARCHAR(20);
    v_status_new VARCHAR(20);
    v_sync BOOLEAN := COALESCE(
        current_setting('sc_trx.journal_status_sync', true),
        '0'
    ) = '1';
BEGIN
    /*
       System status sync digunakan saat jurnal_hd mengubah status
       lalu meneruskan status yang sama ke jurnal_dt.
    */
    IF v_sync THEN
        RETURN COALESCE(NEW, OLD);
    END IF;

    IF TG_OP = 'INSERT' THEN
        SELECT status INTO v_status_new
        FROM sc_trx.jurnal_hd
        WHERE id = NEW.jurnal_id;

        IF v_status_new IS NULL THEN
            RAISE EXCEPTION
                'Jurnal header ID % tidak ditemukan',
                NEW.jurnal_id;
        END IF;

        IF v_status_new <> 'DRAFT' THEN
            RAISE EXCEPTION
                'Jurnal detail % tidak dapat ditambah. Status header = %',
                NEW.jurnal_id,
                v_status_new;
        END IF;

        IF NEW.status IS DISTINCT FROM 'DRAFT' THEN
            RAISE EXCEPTION
                'Jurnal detail baru harus berstatus DRAFT. Status=%',
                NEW.status;
        END IF;

        RETURN NEW;
    END IF;

    IF TG_OP = 'UPDATE' THEN
        SELECT status INTO v_status_old
        FROM sc_trx.jurnal_hd
        WHERE id = OLD.jurnal_id;

        IF v_status_old IS NULL THEN
            RAISE EXCEPTION
                'Jurnal header OLD ID % tidak ditemukan',
                OLD.jurnal_id;
        END IF;

        /*
           Detail POSTED/REVERSED/CANCELLED tidak boleh diubah secara manual.
           Perubahan status dilakukan oleh sync header.
        */
        IF v_status_old <> 'DRAFT' THEN
            RAISE EXCEPTION
                'Jurnal detail % tidak dapat diubah. Status header OLD = %',
                OLD.jurnal_id,
                v_status_old;
        END IF;

        IF NEW.jurnal_id <> OLD.jurnal_id THEN
            SELECT status INTO v_status_new
            FROM sc_trx.jurnal_hd
            WHERE id = NEW.jurnal_id;

            IF v_status_new IS NULL THEN
                RAISE EXCEPTION
                    'Jurnal header NEW ID % tidak ditemukan',
                    NEW.jurnal_id;
            END IF;

            IF v_status_new <> 'DRAFT' THEN
                RAISE EXCEPTION
                    'Jurnal detail tidak dapat dipindahkan ke jurnal % karena status = %',
                    NEW.jurnal_id,
                    v_status_new;
            END IF;
        END IF;

        RETURN NEW;
    END IF;

    IF TG_OP = 'DELETE' THEN
        SELECT status INTO v_status_old
        FROM sc_trx.jurnal_hd
        WHERE id = OLD.jurnal_id;

        IF v_status_old IS NULL THEN
            RAISE EXCEPTION
                'Jurnal header ID % tidak ditemukan saat menghapus detail',
                OLD.jurnal_id;
        END IF;

        IF v_status_old <> 'DRAFT' THEN
            RAISE EXCEPTION
                'Jurnal detail % tidak dapat dihapus. Status header = %',
                OLD.jurnal_id,
                v_status_old;
        END IF;

        RETURN OLD;
    END IF;

    RETURN COALESCE(NEW, OLD);
END;
$$;


/* ============================================================
   9. HEADER STATUS -> DETAIL STATUS SYNC

   jurnal_hd = source of truth untuk status jurnal.
   Status detail mengikuti status header.
   ============================================================ */

CREATE OR REPLACE FUNCTION sc_trx.fn_sync_jurnal_dt_status()
RETURNS TRIGGER
LANGUAGE plpgsql
AS $$
BEGIN
    PERFORM set_config('sc_trx.journal_status_sync', '1', true);

    UPDATE sc_trx.jurnal_dt
    SET
        status = NEW.status,
        updateddate = CURRENT_TIMESTAMP
    WHERE jurnal_id = NEW.id
      AND status IS DISTINCT FROM NEW.status;

    PERFORM set_config('sc_trx.journal_status_sync', '0', true);

    RETURN NEW;
END;
$$;


DROP TRIGGER IF EXISTS trg_sync_jurnal_dt_status
ON sc_trx.jurnal_hd;

CREATE TRIGGER trg_sync_jurnal_dt_status
AFTER UPDATE OF status
ON sc_trx.jurnal_hd
FOR EACH ROW
WHEN (OLD.status IS DISTINCT FROM NEW.status)
EXECUTE FUNCTION sc_trx.fn_sync_jurnal_dt_status();


/* ============================================================
   10. SYNC TOTAL jurnal_hd

   Tidak bergantung lagi pada function TAHAP 09.
   Langsung hitung dari jurnal_dt.
   ============================================================ */

CREATE OR REPLACE FUNCTION sc_trx.fn_sync_jurnal_hd()
RETURNS TRIGGER
LANGUAGE plpgsql
AS $$
DECLARE
    v_jurnal_id_old BIGINT;
    v_jurnal_id_new BIGINT;
BEGIN
    v_jurnal_id_old := NULL;
    v_jurnal_id_new := NULL;

    IF TG_OP = 'INSERT' THEN
        v_jurnal_id_new := NEW.jurnal_id;

    ELSIF TG_OP = 'DELETE' THEN
        v_jurnal_id_old := OLD.jurnal_id;

    ELSIF TG_OP = 'UPDATE' THEN
        v_jurnal_id_old := OLD.jurnal_id;
        v_jurnal_id_new := NEW.jurnal_id;
    END IF;


    IF v_jurnal_id_old IS NOT NULL THEN
        UPDATE sc_trx.jurnal_hd jh
        SET
            total_debet  = x.total_debet,
            total_kredit = x.total_kredit,
            balance      = ROUND(x.total_debet - x.total_kredit, 2),
            updateddate  = CURRENT_TIMESTAMP
        FROM (
            SELECT
                COALESCE(SUM(jd.debet), 0)::NUMERIC(18,2)  AS total_debet,
                COALESCE(SUM(jd.kredit), 0)::NUMERIC(18,2) AS total_kredit
            FROM sc_trx.jurnal_dt jd
            WHERE jd.jurnal_id = v_jurnal_id_old
        ) x
        WHERE jh.id = v_jurnal_id_old;
    END IF;


    IF v_jurnal_id_new IS NOT NULL
       AND v_jurnal_id_new IS DISTINCT FROM v_jurnal_id_old THEN

        UPDATE sc_trx.jurnal_hd jh
        SET
            total_debet  = x.total_debet,
            total_kredit = x.total_kredit,
            balance      = ROUND(x.total_debet - x.total_kredit, 2),
            updateddate  = CURRENT_TIMESTAMP
        FROM (
            SELECT
                COALESCE(SUM(jd.debet), 0)::NUMERIC(18,2)  AS total_debet,
                COALESCE(SUM(jd.kredit), 0)::NUMERIC(18,2) AS total_kredit
            FROM sc_trx.jurnal_dt jd
            WHERE jd.jurnal_id = v_jurnal_id_new
        ) x
        WHERE jh.id = v_jurnal_id_new;
    END IF;

    RETURN COALESCE(NEW, OLD);
END;
$$;


/* ============================================================
   11. TRIGGER VALIDASI COA
   ============================================================ */

DROP TRIGGER IF EXISTS trg_validate_jurnal_coa
ON sc_trx.jurnal_dt;

CREATE TRIGGER trg_validate_jurnal_coa
BEFORE INSERT OR UPDATE
ON sc_trx.jurnal_dt
FOR EACH ROW
EXECUTE FUNCTION sc_trx.fn_validate_jurnal_coa();


/* ============================================================
   12. TRIGGER VALIDASI STATUS HEADER
   ============================================================ */

DROP TRIGGER IF EXISTS trg_validate_jurnal_detail_status
ON sc_trx.jurnal_dt;

CREATE TRIGGER trg_validate_jurnal_detail_status
BEFORE INSERT OR UPDATE OR DELETE
ON sc_trx.jurnal_dt
FOR EACH ROW
EXECUTE FUNCTION sc_trx.fn_validate_jurnal_detail_status();


/* ============================================================
   13. TRIGGER SYNC HEADER
   ============================================================ */

DROP TRIGGER IF EXISTS trg_sync_jurnal_hd
ON sc_trx.jurnal_dt;

CREATE TRIGGER trg_sync_jurnal_hd
AFTER INSERT OR UPDATE OR DELETE
ON sc_trx.jurnal_dt
FOR EACH ROW
EXECUTE FUNCTION sc_trx.fn_sync_jurnal_hd();


/* ============================================================
   DUPLICATE AUDIT
   ============================================================ */

SELECT
    idcoa,
    debet,
    kredit,
    ref_docno,
    ref_doctype,
    source_uniqueid,
    keterangan,
    MIN(id) AS first_jurnal_dt_id,
    COUNT(*) AS duplicate_count
FROM sc_trx.jurnal_dt
GROUP BY
    idcoa,
    debet,
    kredit,
    ref_docno,
    ref_doctype,
    source_uniqueid,
    keterangan
HAVING COUNT(*) > 1
ORDER BY duplicate_count DESC, first_jurnal_dt_id;


/* ============================================================
   HASIL AKHIR


   transaction_dt
        |
        v
   journal_type_coa
        |
        v
   jurnal_hd
        |
        v
   jurnal_dt
        |
        +-- idcoa
        +-- account_role
        +-- value_source
        +-- debet
        +-- kredit
        +-- status
        +-- source_uniqueid
        |
        v
   accounting posting (tahap berikutnya)

   TAHAP 10 hanya menangani detail jurnal dan integritasnya.
   ============================================================ */


/* ============================================================
   FINAL VERIFICATION JOURNAL TYPE
   ============================================================ */

SELECT
    journal_type,
    COUNT(*) AS row_count
FROM sc_trx.jurnal_dt
GROUP BY journal_type
ORDER BY journal_type;

SELECT
    COUNT(*) AS invalid_journal_type
FROM sc_trx.jurnal_dt jd
LEFT JOIN sc_mst.journal_type jt
       ON jt.journal_type = jd.journal_type
WHERE NULLIF(BTRIM(jd.journal_type::TEXT), '') IS NULL
   OR jt.journal_type IS NULL;
