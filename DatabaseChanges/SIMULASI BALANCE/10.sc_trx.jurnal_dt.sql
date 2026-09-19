/* ============================================================
   TAHAP 10
   GENERAL LEDGER DETAIL : sc_trx.jurnal_dt

   FOKUS:
   - detail COA jurnal
   - debit / credit
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
    OR keterangan IS NULL
    OR currcode IS NULL
    OR kurs IS NULL
    OR createddate IS NULL;


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
BEGIN
    IF TG_OP = 'INSERT' THEN

        SELECT status
          INTO v_status_new
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

        RETURN NEW;
    END IF;


    IF TG_OP = 'UPDATE' THEN

        SELECT status
          INTO v_status_old
        FROM sc_trx.jurnal_hd
        WHERE id = OLD.jurnal_id;

        IF v_status_old IS NULL THEN
            RAISE EXCEPTION
                'Jurnal header OLD ID % tidak ditemukan',
                OLD.jurnal_id;
        END IF;

        IF v_status_old <> 'DRAFT' THEN
            RAISE EXCEPTION
                'Jurnal detail % tidak dapat diubah. Status header OLD = %',
                OLD.jurnal_id,
                v_status_old;
        END IF;


        IF NEW.jurnal_id <> OLD.jurnal_id THEN

            SELECT status
              INTO v_status_new
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

        SELECT status
          INTO v_status_old
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


    RETURN NEW;
END;
$$;


/* ============================================================
   9. SYNC TOTAL jurnal_hd

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
   10. TRIGGER VALIDASI COA
   ============================================================ */

DROP TRIGGER IF EXISTS trg_validate_jurnal_coa
ON sc_trx.jurnal_dt;

CREATE TRIGGER trg_validate_jurnal_coa
BEFORE INSERT OR UPDATE
ON sc_trx.jurnal_dt
FOR EACH ROW
EXECUTE FUNCTION sc_trx.fn_validate_jurnal_coa();


/* ============================================================
   11. TRIGGER VALIDASI STATUS HEADER
   ============================================================ */

DROP TRIGGER IF EXISTS trg_validate_jurnal_detail_status
ON sc_trx.jurnal_dt;

CREATE TRIGGER trg_validate_jurnal_detail_status
BEFORE INSERT OR UPDATE OR DELETE
ON sc_trx.jurnal_dt
FOR EACH ROW
EXECUTE FUNCTION sc_trx.fn_validate_jurnal_detail_status();


/* ============================================================
   12. TRIGGER SYNC HEADER
   ============================================================ */

DROP TRIGGER IF EXISTS trg_sync_jurnal_hd
ON sc_trx.jurnal_dt;

CREATE TRIGGER trg_sync_jurnal_hd
AFTER INSERT OR UPDATE OR DELETE
ON sc_trx.jurnal_dt
FOR EACH ROW
EXECUTE FUNCTION sc_trx.fn_sync_jurnal_hd();


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
        +-- source_uniqueid
        |
        v
   accounting posting (tahap berikutnya)

   TAHAP 10 hanya menangani detail jurnal dan integritasnya.
   ============================================================ */
