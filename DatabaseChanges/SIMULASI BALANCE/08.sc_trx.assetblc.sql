/* ============================================================
   TAHAP 08
   ASSET LEDGER : sc_trx.assetblc

   FLOW:
       transaction_dt
            |
            v
        assetblc

   PRINSIP:
   - transaction_dt = source of truth
   - assetblc = ledger hasil posting otomatis
   - posting dikontrol oleh transaction_dt.asset_effect
   - asset_uniqueid boleh berulang karena satu asset memiliki
     banyak histori transaksi
   - uniqueid transaction_dt = unique transaksi di assetblc
   - tahap ini TIDAK menghitung depreciation / disposal gain-loss
   ============================================================ */

/* ============================================================
   1. DEPENDENCY CHECK
   ============================================================ */
DO $$
BEGIN
    IF to_regclass('sc_trx.transaction_dt') IS NULL THEN
        RAISE EXCEPTION 'TAHAP 08 gagal: sc_trx.transaction_dt tidak ditemukan.';
    END IF;

    IF to_regclass('sc_mst.journal_type') IS NULL THEN
        RAISE EXCEPTION 'TAHAP 08 gagal: sc_mst.journal_type tidak ditemukan.';
    END IF;
END;
$$;

/* ============================================================
   2. ASSET IDENTITY FUNCTION
   Digunakan untuk membentuk identitas asset berdasarkan:
   branch + warehouse + bin + asset_no
   ============================================================ */
CREATE OR REPLACE FUNCTION sc_trx.fn_asset_uniqueid
(
    p_idbranch  CHAR(20),
    p_warehouse VARCHAR(50),
    p_bin       VARCHAR(50),
    p_asset_no  TEXT
)
RETURNS TEXT
LANGUAGE SQL
IMMUTABLE
AS $$
    SELECT concat_ws(
        '|',
        COALESCE(TRIM(p_idbranch), ''),
        COALESCE(TRIM(p_warehouse), ''),
        COALESCE(TRIM(p_bin), ''),
        COALESCE(TRIM(p_asset_no), '')
    );
$$;

/* ============================================================
   3. CREATE TABLE
   Tidak DROP table agar aman untuk deployment ulang.
   ============================================================ */
CREATE TABLE IF NOT EXISTS sc_trx.assetblc
(
    id BIGSERIAL PRIMARY KEY,

    /* transaction */
    uniqueid TEXT NOT NULL,
    source_uniqueid TEXT NOT NULL DEFAULT '',

    /* document */
    docno VARCHAR(50) NOT NULL,
    doctype VARCHAR(20) NOT NULL,
    journal_type CHAR(6) NOT NULL,
    line_no INT NOT NULL DEFAULT 1,
    docdate DATE NOT NULL,
    ref_docno VARCHAR(50) NOT NULL DEFAULT '',
    ref_doctype VARCHAR(20) NOT NULL DEFAULT '',

    /* movement */
    type_in_out CHAR(3) NOT NULL,

    /* location */
    idbranch CHAR(20) NOT NULL DEFAULT '',
    cabang CHAR(30) NOT NULL DEFAULT '',
    idarea CHAR(20) NOT NULL DEFAULT '',
    warehouse VARCHAR(50) NOT NULL DEFAULT '',
    bin VARCHAR(50) NOT NULL DEFAULT '',

    /* asset identity */
    asset_no VARCHAR(100) NOT NULL DEFAULT '',
    asset_name VARCHAR(250) NOT NULL DEFAULT '',
    asset_uniqueid TEXT NOT NULL,
    asset_key CHAR(32) NOT NULL,

    /* movement value */
    qty NUMERIC(18,6) NOT NULL DEFAULT 1,
    value_in NUMERIC(18,2) NOT NULL DEFAULT 0,
    value_out NUMERIC(18,2) NOT NULL DEFAULT 0,

    /* currency */
    currcode CHAR(3) NOT NULL DEFAULT '',
    kurs NUMERIC(18,6) NOT NULL DEFAULT 1,

    /* source */
    source_table VARCHAR(100) NOT NULL DEFAULT '',
    source_id BIGINT,
    source_line_id BIGINT,

    /* accounting snapshot */
    idcoa VARCHAR(20) NOT NULL DEFAULT '',
    keterangan TEXT NOT NULL DEFAULT '',

    /* audit */
    createdby VARCHAR(50),
    createddate TIMESTAMP WITHOUT TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP
);

/* ============================================================
   4. ADD MISSING COLUMNS FOR EXISTING TABLE
   Aman untuk database yang sudah mempunyai assetblc lama.
   Kolom lama tidak dihapus agar tidak kehilangan data.
   ============================================================ */
DO $$
DECLARE
    r RECORD;
BEGIN
    FOR r IN
        SELECT *
        FROM (
            VALUES
                ($q$uniqueid$q$,        $q$TEXT NOT NULL DEFAULT ''$q$),
                ($q$source_uniqueid$q$, $q$TEXT NOT NULL DEFAULT ''$q$),
                ($q$docno$q$,           $q$VARCHAR(50) NOT NULL DEFAULT ''$q$),
                ($q$doctype$q$,         $q$VARCHAR(20) NOT NULL DEFAULT ''$q$),
                ($q$journal_type$q$,    $q$CHAR(6) NOT NULL DEFAULT ''$q$),
                ($q$line_no$q$,         $q$INT NOT NULL DEFAULT 1$q$),
                ($q$docdate$q$,         $q$DATE NOT NULL DEFAULT CURRENT_DATE$q$),
                ($q$ref_docno$q$,       $q$VARCHAR(50) NOT NULL DEFAULT ''$q$),
                ($q$ref_doctype$q$,     $q$VARCHAR(20) NOT NULL DEFAULT ''$q$),
                ($q$type_in_out$q$,     $q$CHAR(3) NOT NULL DEFAULT 'IN'$q$),
                ($q$idbranch$q$,        $q$CHAR(20) NOT NULL DEFAULT ''$q$),
                ($q$cabang$q$,          $q$CHAR(30) NOT NULL DEFAULT ''$q$),
                ($q$idarea$q$,          $q$CHAR(20) NOT NULL DEFAULT ''$q$),
                ($q$warehouse$q$,       $q$VARCHAR(50) NOT NULL DEFAULT ''$q$),
                ($q$bin$q$,             $q$VARCHAR(50) NOT NULL DEFAULT ''$q$),
                ($q$asset_no$q$,        $q$VARCHAR(100) NOT NULL DEFAULT ''$q$),
                ($q$asset_name$q$,      $q$VARCHAR(250) NOT NULL DEFAULT ''$q$),
                ($q$asset_uniqueid$q$,  $q$TEXT NOT NULL DEFAULT ''$q$),
                ($q$asset_key$q$,       $q$CHAR(32) NOT NULL DEFAULT ''$q$),
                ($q$qty$q$,              $q$NUMERIC(18,6) NOT NULL DEFAULT 1$q$),
                ($q$value_in$q$,         $q$NUMERIC(18,2) NOT NULL DEFAULT 0$q$),
                ($q$value_out$q$,        $q$NUMERIC(18,2) NOT NULL DEFAULT 0$q$),
                ($q$currcode$q$,         $q$CHAR(3) NOT NULL DEFAULT ''$q$),
                ($q$kurs$q$,              $q$NUMERIC(18,6) NOT NULL DEFAULT 1$q$),
                ($q$source_table$q$,     $q$VARCHAR(100) NOT NULL DEFAULT ''$q$),
                ($q$source_id$q$,        $q$BIGINT$q$),
                ($q$source_line_id$q$,   $q$BIGINT$q$),
                ($q$idcoa$q$,             $q$VARCHAR(20) NOT NULL DEFAULT ''$q$),
                ($q$keterangan$q$,       $q$TEXT NOT NULL DEFAULT ''$q$),
                ($q$createdby$q$,        $q$VARCHAR(50)$q$),
                ($q$createddate$q$,      $q$TIMESTAMP WITHOUT TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP$q$)
        ) AS x(column_name, column_definition)
        WHERE NOT EXISTS
        (
            SELECT 1
            FROM information_schema.columns c
            WHERE c.table_schema = 'sc_trx'
              AND c.table_name = 'assetblc'
              AND c.column_name = x.column_name
        )
    LOOP
        EXECUTE format(
            'ALTER TABLE sc_trx.assetblc ADD COLUMN %I %s',
            r.column_name,
            r.column_definition
        );
    END LOOP;
END;
$$;

/* ============================================================
   5. NORMALIZE / BUILD IDENTITY
   ============================================================ */
UPDATE sc_trx.assetblc
SET source_uniqueid = COALESCE(source_uniqueid, ''),
    ref_docno       = COALESCE(ref_docno, ''),
    ref_doctype     = COALESCE(ref_doctype, ''),
    idbranch        = COALESCE(idbranch, ''),
    cabang          = COALESCE(cabang, ''),
    idarea          = COALESCE(idarea, ''),
    warehouse       = COALESCE(warehouse, ''),
    bin             = COALESCE(bin, ''),
    asset_no        = COALESCE(asset_no, ''),
    asset_name      = COALESCE(asset_name, ''),
    qty             = CASE WHEN qty IS NULL OR qty <= 0 THEN 1 ELSE qty END,
    value_in        = COALESCE(value_in, 0),
    value_out       = COALESCE(value_out, 0),
    currcode        = COALESCE(currcode, ''),
    kurs            = CASE WHEN kurs IS NULL OR kurs <= 0 THEN 1 ELSE kurs END,
    source_table    = COALESCE(source_table, ''),
    idcoa           = COALESCE(idcoa, ''),
    keterangan      = COALESCE(keterangan, ''),
    createddate     = COALESCE(createddate, CURRENT_TIMESTAMP);

UPDATE sc_trx.assetblc
SET asset_uniqueid = sc_trx.fn_asset_uniqueid(
        idbranch,
        warehouse,
        bin,
        asset_no
    )
WHERE asset_uniqueid IS NULL
   OR asset_uniqueid = '';

UPDATE sc_trx.assetblc
SET asset_key = md5(asset_uniqueid)
WHERE asset_key IS NULL
   OR asset_key = ''
   OR length(asset_key) <> 32;

/* ============================================================
   6. CONSTRAINTS
   Hanya ditambahkan bila belum ada.
   ============================================================ */
DO $$
BEGIN
    IF NOT EXISTS (
        SELECT 1
        FROM pg_constraint
        WHERE conrelid = 'sc_trx.assetblc'::regclass
          AND conname = 'uq_assetblc_uniqueid'
    ) THEN
        IF EXISTS (
            SELECT 1
            FROM sc_trx.assetblc
            GROUP BY uniqueid
            HAVING COUNT(*) > 1
        ) THEN
            RAISE NOTICE 'UNIQUE uq_assetblc_uniqueid dilewati karena masih ada duplicate uniqueid.';
        ELSE
            ALTER TABLE sc_trx.assetblc
                ADD CONSTRAINT uq_assetblc_uniqueid UNIQUE (uniqueid);
        END IF;
    END IF;

    IF NOT EXISTS (
        SELECT 1
        FROM pg_constraint
        WHERE conrelid = 'sc_trx.assetblc'::regclass
          AND conname = 'chk_assetblc_type_in_out'
    ) THEN
        ALTER TABLE sc_trx.assetblc
            ADD CONSTRAINT chk_assetblc_type_in_out
            CHECK (type_in_out IN ('IN','OUT'));
    END IF;

    IF NOT EXISTS (
        SELECT 1
        FROM pg_constraint
        WHERE conrelid = 'sc_trx.assetblc'::regclass
          AND conname = 'chk_assetblc_qty'
    ) THEN
        ALTER TABLE sc_trx.assetblc
            ADD CONSTRAINT chk_assetblc_qty
            CHECK (qty > 0);
    END IF;

    IF NOT EXISTS (
        SELECT 1
        FROM pg_constraint
        WHERE conrelid = 'sc_trx.assetblc'::regclass
          AND conname = 'chk_assetblc_value_in'
    ) THEN
        ALTER TABLE sc_trx.assetblc
            ADD CONSTRAINT chk_assetblc_value_in
            CHECK (value_in >= 0);
    END IF;

    IF NOT EXISTS (
        SELECT 1
        FROM pg_constraint
        WHERE conrelid = 'sc_trx.assetblc'::regclass
          AND conname = 'chk_assetblc_value_out'
    ) THEN
        ALTER TABLE sc_trx.assetblc
            ADD CONSTRAINT chk_assetblc_value_out
            CHECK (value_out >= 0);
    END IF;

    IF NOT EXISTS (
        SELECT 1
        FROM pg_constraint
        WHERE conrelid = 'sc_trx.assetblc'::regclass
          AND conname = 'chk_assetblc_kurs'
    ) THEN
        ALTER TABLE sc_trx.assetblc
            ADD CONSTRAINT chk_assetblc_kurs
            CHECK (kurs > 0);
    END IF;
END;
$$;

/* ============================================================
   7. FOREIGN KEY JOURNAL TYPE
   ============================================================ */
DO $$
BEGIN
    IF NOT EXISTS (
        SELECT 1
        FROM pg_constraint
        WHERE conrelid = 'sc_trx.assetblc'::regclass
          AND conname = 'fk_assetblc_journal_type'
    ) THEN
        IF EXISTS (
            SELECT 1
            FROM sc_trx.assetblc ab
            LEFT JOIN sc_mst.journal_type jt
              ON jt.journal_type = ab.journal_type
            WHERE jt.journal_type IS NULL
        ) THEN
            RAISE NOTICE 'FK fk_assetblc_journal_type dilewati karena ada journal_type orphan.';
        ELSE
            ALTER TABLE sc_trx.assetblc
                ADD CONSTRAINT fk_assetblc_journal_type
                FOREIGN KEY (journal_type)
                REFERENCES sc_mst.journal_type(journal_type);
        END IF;
    END IF;
END;
$$;

/* ============================================================
   8. INDEX - HANYA YANG DIGUNAKAN UNTUK TRACE / SEARCH
   ============================================================ */
CREATE INDEX IF NOT EXISTS idx_assetblc_source_uniqueid
    ON sc_trx.assetblc(source_uniqueid);

CREATE INDEX IF NOT EXISTS idx_assetblc_asset_uniqueid
    ON sc_trx.assetblc(asset_uniqueid);

CREATE INDEX IF NOT EXISTS idx_assetblc_asset_key
    ON sc_trx.assetblc(asset_key);

CREATE INDEX IF NOT EXISTS idx_assetblc_asset_no
    ON sc_trx.assetblc(asset_no);

CREATE INDEX IF NOT EXISTS idx_assetblc_doc
    ON sc_trx.assetblc(docno, doctype);

CREATE INDEX IF NOT EXISTS idx_assetblc_date
    ON sc_trx.assetblc(docdate);

/* ============================================================
   9. POST / DELETE TRIGGER FUNCTION

   asset_effect dibaca dari transaction_dt, bukan dari master
   journal_type saat transaksi lama diproses ulang.

   INSERT:
       jika asset_effect aktif -> INSERT assetblc

   DELETE:
       DELETE assetblc berdasarkan uniqueid

   UPDATE:
       DELETE hasil OLD -> INSERT hasil NEW bila asset_effect aktif
   ============================================================ */
CREATE OR REPLACE FUNCTION sc_trx.fn_transaction_asset()
RETURNS TRIGGER
LANGUAGE plpgsql
AS $$
DECLARE
    v_asset_effect CHAR(5);
    v_asset_no VARCHAR(100);
    v_asset_uniqueid TEXT;
BEGIN
    /* --------------------------------------------------------
       DELETE OLD RESULT
       -------------------------------------------------------- */
    IF TG_OP IN ('DELETE', 'UPDATE') THEN
        DELETE FROM sc_trx.assetblc
        WHERE uniqueid = OLD.uniqueid;
    END IF;

    IF TG_OP = 'DELETE' THEN
        RETURN OLD;
    END IF;

    /* --------------------------------------------------------
       ASSET EFFECT SNAPSHOT DARI transaction_dt
       -------------------------------------------------------- */
    v_asset_effect := COALESCE(NULLIF(TRIM(NEW.asset_effect), ''), 'NONE');

    IF v_asset_effect NOT IN ('IN', 'OUT', 'INOUT') THEN
        RETURN NEW;
    END IF;

    /* --------------------------------------------------------
       ASSET IDENTITY
       transaction_dt belum mempunyai asset_no khusus.
       Gunakan source_uniqueid sebagai asset identity,
       fallback ke uniqueid.
       -------------------------------------------------------- */
    v_asset_no := COALESCE(
        NULLIF(TRIM(NEW.source_uniqueid), ''),
        NEW.uniqueid
    );

    v_asset_uniqueid := sc_trx.fn_asset_uniqueid(
        NEW.idbranch,
        NEW.warehouse,
        NEW.bin,
        v_asset_no
    );

    /* --------------------------------------------------------
       INSERT ASSET LEDGER
       value_in/value_out hanya mencatat nilai perpindahan.
       Perhitungan depreciation, book value dan gain/loss
       dilakukan pada tahap asset accounting.
       -------------------------------------------------------- */
    INSERT INTO sc_trx.assetblc
    (
        uniqueid,
        source_uniqueid,
        docno,
        doctype,
        journal_type,
        line_no,
        docdate,
        ref_docno,
        ref_doctype,
        type_in_out,
        idbranch,
        cabang,
        idarea,
        warehouse,
        bin,
        asset_no,
        asset_name,
        asset_uniqueid,
        asset_key,
        qty,
        value_in,
        value_out,
        currcode,
        kurs,
        source_table,
        source_id,
        source_line_id,
        idcoa,
        keterangan,
        createdby,
        createddate
    )
    VALUES
    (
        NEW.uniqueid,
        COALESCE(NEW.source_uniqueid, ''),
        NEW.docno,
        NEW.doctype,
        NEW.journal_type,
        COALESCE(NEW.line_no, 1),
        NEW.docdate,
        COALESCE(NEW.ref_docno, ''),
        COALESCE(NEW.ref_doctype, ''),
        NEW.type_in_out,
        COALESCE(NEW.idbranch, ''),
        COALESCE(NEW.cabang, ''),
        COALESCE(NEW.idarea, ''),
        COALESCE(NEW.warehouse, ''),
        COALESCE(NEW.bin, ''),
        v_asset_no,
        COALESCE(NEW.namabarang, ''),
        v_asset_uniqueid,
        md5(v_asset_uniqueid),
        CASE
            WHEN COALESCE(NEW.qty, 0) > 0 THEN NEW.qty
            ELSE 1
        END,
        CASE
            WHEN NEW.type_in_out = 'IN'
            THEN COALESCE(NEW.total, NEW.nilai, 0)
            ELSE 0
        END,
        CASE
            WHEN NEW.type_in_out = 'OUT'
            THEN COALESCE(NEW.total, NEW.nilai, 0)
            ELSE 0
        END,
        COALESCE(NEW.currcode, ''),
        CASE
            WHEN COALESCE(NEW.kurs, 0) > 0 THEN NEW.kurs
            ELSE 1
        END,
        COALESCE(NEW.source_table, ''),
        NEW.source_id,
        NEW.source_line_id,
        COALESCE(NEW.idcoa, ''),
        COALESCE(NEW.keterangan, ''),
        NEW.createdby,
        CURRENT_TIMESTAMP
    );

    RETURN NEW;
END;
$$;

/* ============================================================
   10. TRIGGER transaction_dt -> assetblc
   ============================================================ */
DROP TRIGGER IF EXISTS trg_transaction_asset
ON sc_trx.transaction_dt;

CREATE TRIGGER trg_transaction_asset
AFTER INSERT OR UPDATE OR DELETE
ON sc_trx.transaction_dt
FOR EACH ROW
EXECUTE FUNCTION sc_trx.fn_transaction_asset();

/* ============================================================
   11. VERIFICATION
   ============================================================ */
SELECT
    tgname,
    tgenabled,
    pg_get_triggerdef(oid) AS trigger_definition
FROM pg_trigger
WHERE tgrelid = 'sc_trx.transaction_dt'::regclass
  AND NOT tgisinternal
ORDER BY tgname;

SELECT
    column_name,
    data_type,
    character_maximum_length,
    numeric_precision,
    numeric_scale,
    is_nullable
FROM information_schema.columns
WHERE table_schema = 'sc_trx'
  AND table_name = 'assetblc'
ORDER BY ordinal_position;

/* audit source -> asset */
SELECT
    td.uniqueid,
    td.source_uniqueid,
    td.docno,
    td.doctype,
    td.journal_type,
    td.type_in_out
FROM sc_trx.transaction_dt td
WHERE td.asset_effect IN ('IN','OUT','INOUT')
  AND NOT EXISTS
  (
      SELECT 1
      FROM sc_trx.assetblc ab
      WHERE ab.uniqueid = td.uniqueid
  );

/* audit asset -> source */
SELECT
    ab.uniqueid,
    ab.source_uniqueid,
    ab.docno,
    ab.doctype,
    ab.journal_type,
    ab.type_in_out,
    ab.asset_no
FROM sc_trx.assetblc ab
WHERE NOT EXISTS
(
    SELECT 1
    FROM sc_trx.transaction_dt td
    WHERE td.uniqueid = ab.uniqueid
);

/* ============================================================
   TAHAP 08 SELESAI

   Belum termasuk:
   - asset master
   - depreciation engine
   - depreciation schedule
   - disposal gain/loss
   - revaluation
   - asset accounting journal
   ============================================================ */
