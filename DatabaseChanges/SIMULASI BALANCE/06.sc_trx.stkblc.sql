/* ============================================================
   JSYS ERP
   TAHAP 06 - STOCK LEDGER : sc_trx.stkblc
   FINAL REPAIR / RERUN-SAFE
   ============================================================

   FIX UTAMA:
   1. Tidak DROP stkblc.
   2. Tidak DROP / CREATE ulang function milik TAHAP 04.
   3. Tidak membuat ulang function TAHAP 01.
   4. Kolom yang hilang pada stkblc (termasuk journal_type)
      akan ditambahkan hanya bila memang belum ada.
   5. Trigger memakai CREATE OR REPLACE TRIGGER.
   6. Stock posting membaca transaction_dt.stock_effect,
      bukan journal_type.stock_effect.
   7. JSA yang sudah diputuskan TAHAP 04 sebagai stock_effect=NONE
      tidak masuk stkblc.
   8. Cost OUT tidak ditentukan dari harga jual; TAHAP 07
      menangani costing OUT / average cost.
   ============================================================ */


/* ============================================================
   0. DEPENDENCY CHECK
   ============================================================ */

DO $$
BEGIN

    IF to_regclass('sc_trx.transaction_dt') IS NULL THEN
        RAISE EXCEPTION
            'TAHAP 04 belum tersedia: sc_trx.transaction_dt tidak ditemukan';
    END IF;

    IF to_regclass('sc_mst.journal_type') IS NULL THEN
        RAISE EXCEPTION
            'TAHAP 02 belum tersedia: sc_mst.journal_type tidak ditemukan';
    END IF;

    IF to_regprocedure(
        'sc_trx.fn_stock_uniqueid(text,text,text,text,text,text,text)'
    ) IS NULL THEN
        RAISE EXCEPTION
            'TAHAP 01 belum tersedia: sc_trx.fn_stock_uniqueid(TEXT x 7)';
    END IF;

    IF to_regprocedure(
        'sc_trx.fn_stock_key(text,text,text,text,text,text,text)'
    ) IS NULL THEN
        RAISE EXCEPTION
            'TAHAP 01 belum tersedia: sc_trx.fn_stock_key(TEXT x 7)';
    END IF;

END;
$$;


/* ============================================================
   1. CREATE STKBLc BILA BELUM ADA
   ============================================================ */

DO $$
BEGIN

    IF to_regclass('sc_trx.stkblc') IS NULL THEN

        CREATE TABLE sc_trx.stkblc
        (
            id BIGSERIAL NOT NULL,

            uniqueid TEXT NOT NULL,
            source_uniqueid TEXT NOT NULL DEFAULT '',

            docno VARCHAR(50) NOT NULL,
            doctype VARCHAR(20) NOT NULL,
            journal_type CHAR(6) NOT NULL,
            line_no INTEGER NOT NULL DEFAULT 1,
            docdate DATE NOT NULL,

            ref_docno VARCHAR(50) NOT NULL DEFAULT '',
            ref_doctype VARCHAR(20) NOT NULL DEFAULT '',

            type_in_out CHAR(3) NOT NULL,

            idbranch CHAR(20) NOT NULL DEFAULT '',
            cabang CHAR(30) NOT NULL DEFAULT '',

            idarea CHAR(20) NOT NULL DEFAULT '',
            warehouse VARCHAR(50) NOT NULL DEFAULT '',
            bin VARCHAR(50) NOT NULL DEFAULT '',

            idbarang CHAR(20) NOT NULL,
            idunit VARCHAR(10) NOT NULL DEFAULT '',

            batch CHAR(100) NOT NULL DEFAULT '',
            lotno VARCHAR(100) NOT NULL DEFAULT '',

            stock_uniqueid TEXT NOT NULL,
            stock_key CHAR(32) NOT NULL,

            qty_in NUMERIC(18,6) NOT NULL DEFAULT 0,
            qty_out NUMERIC(18,6) NOT NULL DEFAULT 0,

            unitcost NUMERIC(18,6) NOT NULL DEFAULT 0,
            totalcost NUMERIC(18,2) NOT NULL DEFAULT 0,

            currcode CHAR(3) NOT NULL DEFAULT 'IDR',
            kurs NUMERIC(18,6) NOT NULL DEFAULT 1,

            source_table VARCHAR(100) NOT NULL DEFAULT '',
            source_id BIGINT,
            source_line_id BIGINT,

            idcoa VARCHAR(20) NOT NULL DEFAULT '',

            keterangan TEXT NOT NULL DEFAULT '',

            createdby VARCHAR(50),
            createddate TIMESTAMP WITHOUT TIME ZONE
                NOT NULL DEFAULT CURRENT_TIMESTAMP,

            CONSTRAINT pk_stkblc
                PRIMARY KEY (id),

            CONSTRAINT uq_stkblc_uniqueid
                UNIQUE (uniqueid),

            CONSTRAINT chk_stkblc_type_in_out
                CHECK (type_in_out IN ('IN','OUT')),

            CONSTRAINT chk_stkblc_qty_in
                CHECK (qty_in >= 0),

            CONSTRAINT chk_stkblc_qty_out
                CHECK (qty_out >= 0),

            CONSTRAINT chk_stkblc_qty_direction
                CHECK
                (
                    (
                        type_in_out = 'IN'
                        AND qty_in > 0
                        AND qty_out = 0
                    )
                    OR
                    (
                        type_in_out = 'OUT'
                        AND qty_in = 0
                        AND qty_out > 0
                    )
                ),

            CONSTRAINT chk_stkblc_unitcost
                CHECK (unitcost >= 0),

            CONSTRAINT chk_stkblc_totalcost
                CHECK (totalcost >= 0),

            CONSTRAINT chk_stkblc_kurs
                CHECK (kurs > 0),

            CONSTRAINT chk_stkblc_line_no
                CHECK (line_no > 0)
        );

    END IF;

END;
$$;


/* ============================================================
   2. TAMBAH KOLOM YANG BELUM ADA
   ============================================================

   Dynamic check digunakan supaya TIDAK muncul:
       column "... " already exists, skipping

   Khusus kasus user saat ini:
       journal_type memang pernah hilang dari table lama.
   ============================================================ */

DO $$
DECLARE
    v_col TEXT;
    v_def TEXT;

    v_columns JSONB := '{
        "id": "BIGSERIAL",
        "uniqueid": "TEXT",
        "source_uniqueid": "TEXT DEFAULT ''''",
        "docno": "VARCHAR(50)",
        "doctype": "VARCHAR(20)",
        "journal_type": "CHAR(6)",
        "line_no": "INTEGER DEFAULT 1",
        "docdate": "DATE",
        "ref_docno": "VARCHAR(50) DEFAULT ''''",
        "ref_doctype": "VARCHAR(20) DEFAULT ''''",
        "type_in_out": "CHAR(3)",
        "idbranch": "CHAR(20) DEFAULT ''''",
        "cabang": "CHAR(30) DEFAULT ''''",
        "idarea": "CHAR(20) DEFAULT ''''",
        "warehouse": "VARCHAR(50) DEFAULT ''''",
        "bin": "VARCHAR(50) DEFAULT ''''",
        "idbarang": "CHAR(20)",
        "idunit": "VARCHAR(10) DEFAULT ''''",
        "batch": "CHAR(100) DEFAULT ''''",
        "lotno": "VARCHAR(100) DEFAULT ''''",
        "stock_uniqueid": "TEXT",
        "stock_key": "CHAR(32)",
        "qty_in": "NUMERIC(18,6) DEFAULT 0",
        "qty_out": "NUMERIC(18,6) DEFAULT 0",
        "unitcost": "NUMERIC(18,6) DEFAULT 0",
        "totalcost": "NUMERIC(18,2) DEFAULT 0",
        "currcode": "CHAR(3) DEFAULT ''IDR''",
        "kurs": "NUMERIC(18,6) DEFAULT 1",
        "source_table": "VARCHAR(100) DEFAULT ''''",
        "source_id": "BIGINT",
        "source_line_id": "BIGINT",
        "idcoa": "VARCHAR(20) DEFAULT ''''",
        "keterangan": "TEXT DEFAULT ''''",
        "createdby": "VARCHAR(50)",
        "createddate": "TIMESTAMP WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP"
    }'::JSONB;

BEGIN

    FOR v_col, v_def IN
        SELECT key, value
        FROM jsonb_each_text(v_columns)
    LOOP

        IF NOT EXISTS
        (
            SELECT 1
            FROM information_schema.columns
            WHERE table_schema = 'sc_trx'
              AND table_name = 'stkblc'
              AND column_name = v_col
        )
        THEN
            EXECUTE format(
                'ALTER TABLE sc_trx.stkblc ADD COLUMN %I %s',
                v_col,
                v_def
            );
        END IF;

    END LOOP;

END;
$$;


/* ============================================================
   3. NORMALIZE NULL DATA
   ============================================================ */

UPDATE sc_trx.stkblc
SET
    source_uniqueid = COALESCE(source_uniqueid, ''),
    docno           = COALESCE(docno, ''),
    doctype         = COALESCE(doctype, ''),
    journal_type    = COALESCE(journal_type, ''),
    line_no         = COALESCE(line_no, 1),
    ref_docno       = COALESCE(ref_docno, ''),
    ref_doctype     = COALESCE(ref_doctype, ''),
    type_in_out     = COALESCE(type_in_out, 'IN'),
    idbranch        = COALESCE(idbranch, ''),
    cabang          = COALESCE(cabang, ''),
    idarea          = COALESCE(idarea, ''),
    warehouse       = COALESCE(warehouse, ''),
    bin             = COALESCE(bin, ''),
    idunit          = COALESCE(idunit, ''),
    batch           = COALESCE(batch, ''),
    lotno           = COALESCE(lotno, ''),
    qty_in          = COALESCE(qty_in, 0),
    qty_out         = COALESCE(qty_out, 0),
    unitcost        = COALESCE(unitcost, 0),
    totalcost       = COALESCE(totalcost, 0),
    currcode        = COALESCE(NULLIF(BTRIM(currcode), ''), 'IDR'),
    kurs            = COALESCE(kurs, 1),
    source_table    = COALESCE(source_table, ''),
    idcoa           = COALESCE(idcoa, ''),
    keterangan      = COALESCE(keterangan, ''),
    createddate     = COALESCE(createddate, CURRENT_TIMESTAMP);


/* ============================================================
   4. STOCK IDENTITY LAMA YANG KOSONG
   ============================================================ */

UPDATE sc_trx.stkblc
SET
    stock_uniqueid =
        sc_trx.fn_stock_uniqueid
        (
            idbranch::TEXT,
            warehouse::TEXT,
            bin::TEXT,
            idbarang::TEXT,
            idunit::TEXT,
            batch::TEXT,
            lotno::TEXT
        ),
    stock_key =
        sc_trx.fn_stock_key
        (
            idbranch::TEXT,
            warehouse::TEXT,
            bin::TEXT,
            idbarang::TEXT,
            idunit::TEXT,
            batch::TEXT,
            lotno::TEXT
        )
WHERE
    NULLIF(BTRIM(COALESCE(stock_uniqueid, '')), '') IS NULL
    OR
    NULLIF(BTRIM(COALESCE(stock_key, '')), '') IS NULL;


/* ============================================================
   5. CONSTRAINTS SECARA AMAN
   ============================================================ */

DO $$
DECLARE
    v_invalid BIGINT;
BEGIN

    /* --------------------------------------------------------
       UNIQUE transaction identity
       -------------------------------------------------------- */

    IF NOT EXISTS
    (
        SELECT 1
        FROM pg_constraint
        WHERE conrelid = 'sc_trx.stkblc'::regclass
          AND conname = 'uq_stkblc_uniqueid'
    )
    THEN

        SELECT COUNT(*)
        INTO v_invalid
        FROM
        (
            SELECT uniqueid
            FROM sc_trx.stkblc
            GROUP BY uniqueid
            HAVING COUNT(*) > 1
        ) x;

        IF v_invalid = 0 THEN
            ALTER TABLE sc_trx.stkblc
                ADD CONSTRAINT uq_stkblc_uniqueid
                UNIQUE (uniqueid);
        ELSE
            RAISE NOTICE
                'uq_stkblc_uniqueid tidak ditambahkan karena ada % duplicate uniqueid.',
                v_invalid;
        END IF;

    END IF;


    /* --------------------------------------------------------
       JOURNAL TYPE FK
       -------------------------------------------------------- */

    IF NOT EXISTS
    (
        SELECT 1
        FROM pg_constraint
        WHERE conrelid = 'sc_trx.stkblc'::regclass
          AND conname = 'fk_stkblc_journal_type'
    )
    THEN

        SELECT COUNT(*)
        INTO v_invalid
        FROM sc_trx.stkblc sb
        LEFT JOIN sc_mst.journal_type jt
               ON jt.journal_type = sb.journal_type
        WHERE jt.journal_type IS NULL;

        IF v_invalid = 0 THEN
            ALTER TABLE sc_trx.stkblc
                ADD CONSTRAINT fk_stkblc_journal_type
                FOREIGN KEY (journal_type)
                REFERENCES sc_mst.journal_type(journal_type);
        ELSE
            RAISE NOTICE
                'fk_stkblc_journal_type tidak ditambahkan karena ada % journal_type yang tidak ada di master.',
                v_invalid;
        END IF;

    END IF;


    /* --------------------------------------------------------
       TYPE
       -------------------------------------------------------- */

    IF NOT EXISTS
    (
        SELECT 1 FROM pg_constraint
        WHERE conrelid = 'sc_trx.stkblc'::regclass
          AND conname = 'chk_stkblc_type_in_out'
    )
    THEN

        SELECT COUNT(*)
        INTO v_invalid
        FROM sc_trx.stkblc
        WHERE type_in_out NOT IN ('IN','OUT');

        IF v_invalid = 0 THEN
            ALTER TABLE sc_trx.stkblc
                ADD CONSTRAINT chk_stkblc_type_in_out
                CHECK (type_in_out IN ('IN','OUT'));
        ELSE
            RAISE NOTICE
                'chk_stkblc_type_in_out tidak ditambahkan karena ada % data invalid.',
                v_invalid;
        END IF;

    END IF;


    /* --------------------------------------------------------
       QTY IN
       -------------------------------------------------------- */

    IF NOT EXISTS
    (
        SELECT 1 FROM pg_constraint
        WHERE conrelid = 'sc_trx.stkblc'::regclass
          AND conname = 'chk_stkblc_qty_in'
    )
    THEN

        SELECT COUNT(*)
        INTO v_invalid
        FROM sc_trx.stkblc
        WHERE qty_in < 0;

        IF v_invalid = 0 THEN
            ALTER TABLE sc_trx.stkblc
                ADD CONSTRAINT chk_stkblc_qty_in
                CHECK (qty_in >= 0);
        END IF;

    END IF;


    /* --------------------------------------------------------
       QTY OUT
       -------------------------------------------------------- */

    IF NOT EXISTS
    (
        SELECT 1 FROM pg_constraint
        WHERE conrelid = 'sc_trx.stkblc'::regclass
          AND conname = 'chk_stkblc_qty_out'
    )
    THEN

        SELECT COUNT(*)
        INTO v_invalid
        FROM sc_trx.stkblc
        WHERE qty_out < 0;

        IF v_invalid = 0 THEN
            ALTER TABLE sc_trx.stkblc
                ADD CONSTRAINT chk_stkblc_qty_out
                CHECK (qty_out >= 0);
        END IF;

    END IF;


    /* --------------------------------------------------------
       QTY DIRECTION
       -------------------------------------------------------- */

    IF NOT EXISTS
    (
        SELECT 1 FROM pg_constraint
        WHERE conrelid = 'sc_trx.stkblc'::regclass
          AND conname = 'chk_stkblc_qty_direction'
    )
    THEN

        SELECT COUNT(*)
        INTO v_invalid
        FROM sc_trx.stkblc
        WHERE NOT
        (
            (
                type_in_out = 'IN'
                AND qty_in > 0
                AND qty_out = 0
            )
            OR
            (
                type_in_out = 'OUT'
                AND qty_in = 0
                AND qty_out > 0
            )
        );

        IF v_invalid = 0 THEN
            ALTER TABLE sc_trx.stkblc
                ADD CONSTRAINT chk_stkblc_qty_direction
                CHECK
                (
                    (
                        type_in_out = 'IN'
                        AND qty_in > 0
                        AND qty_out = 0
                    )
                    OR
                    (
                        type_in_out = 'OUT'
                        AND qty_in = 0
                        AND qty_out > 0
                    )
                );
        ELSE
            RAISE NOTICE
                'chk_stkblc_qty_direction tidak ditambahkan karena ada % data invalid.',
                v_invalid;
        END IF;

    END IF;


    /* --------------------------------------------------------
       UNIT COST / TOTAL COST / KURS / LINE
       -------------------------------------------------------- */

    IF NOT EXISTS
    (
        SELECT 1 FROM pg_constraint
        WHERE conrelid = 'sc_trx.stkblc'::regclass
          AND conname = 'chk_stkblc_unitcost'
    )
    THEN
        ALTER TABLE sc_trx.stkblc
            ADD CONSTRAINT chk_stkblc_unitcost
            CHECK (unitcost >= 0);
    END IF;


    IF NOT EXISTS
    (
        SELECT 1 FROM pg_constraint
        WHERE conrelid = 'sc_trx.stkblc'::regclass
          AND conname = 'chk_stkblc_totalcost'
    )
    THEN
        ALTER TABLE sc_trx.stkblc
            ADD CONSTRAINT chk_stkblc_totalcost
            CHECK (totalcost >= 0);
    END IF;


    IF NOT EXISTS
    (
        SELECT 1 FROM pg_constraint
        WHERE conrelid = 'sc_trx.stkblc'::regclass
          AND conname = 'chk_stkblc_kurs'
    )
    THEN
        ALTER TABLE sc_trx.stkblc
            ADD CONSTRAINT chk_stkblc_kurs
            CHECK (kurs > 0);
    END IF;


    IF NOT EXISTS
    (
        SELECT 1 FROM pg_constraint
        WHERE conrelid = 'sc_trx.stkblc'::regclass
          AND conname = 'chk_stkblc_line_no'
    )
    THEN
        ALTER TABLE sc_trx.stkblc
            ADD CONSTRAINT chk_stkblc_line_no
            CHECK (line_no > 0);
    END IF;

END;
$$;


/* ============================================================
   6. INDEX TANPA NOTICE
   ============================================================ */

DO $$
BEGIN

    IF to_regclass('sc_trx.idx_stkblc_uniqueid') IS NULL THEN
        CREATE INDEX idx_stkblc_uniqueid
        ON sc_trx.stkblc(uniqueid);
    END IF;

    IF to_regclass('sc_trx.idx_stkblc_source_uniqueid') IS NULL THEN
        CREATE INDEX idx_stkblc_source_uniqueid
        ON sc_trx.stkblc(source_uniqueid);
    END IF;

    IF to_regclass('sc_trx.idx_stkblc_doc') IS NULL THEN
        CREATE INDEX idx_stkblc_doc
        ON sc_trx.stkblc(docno, doctype);
    END IF;

    IF to_regclass('sc_trx.idx_stkblc_journal_type') IS NULL THEN
        CREATE INDEX idx_stkblc_journal_type
        ON sc_trx.stkblc(journal_type, type_in_out);
    END IF;

    IF to_regclass('sc_trx.idx_stkblc_stock_key') IS NULL THEN
        CREATE INDEX idx_stkblc_stock_key
        ON sc_trx.stkblc(stock_key);
    END IF;

    IF to_regclass('sc_trx.idx_stkblc_stock_uniqueid') IS NULL THEN
        CREATE INDEX idx_stkblc_stock_uniqueid
        ON sc_trx.stkblc(stock_uniqueid);
    END IF;

    IF to_regclass('sc_trx.idx_stkblc_stock_dimension') IS NULL THEN
        CREATE INDEX idx_stkblc_stock_dimension
        ON sc_trx.stkblc
        (
            idbranch,
            warehouse,
            bin,
            idbarang,
            idunit,
            batch,
            lotno
        );
    END IF;

    IF to_regclass('sc_trx.idx_stkblc_item_batch_lot') IS NULL THEN
        CREATE INDEX idx_stkblc_item_batch_lot
        ON sc_trx.stkblc
        (
            idbarang,
            batch,
            lotno
        );
    END IF;

    IF to_regclass('sc_trx.idx_stkblc_branch_date') IS NULL THEN
        CREATE INDEX idx_stkblc_branch_date
        ON sc_trx.stkblc(idbranch, docdate);
    END IF;

    IF to_regclass('sc_trx.idx_stkblc_docdate') IS NULL THEN
        CREATE INDEX idx_stkblc_docdate
        ON sc_trx.stkblc(docdate);
    END IF;

    IF to_regclass('sc_trx.idx_stkblc_source_document') IS NULL THEN
        CREATE INDEX idx_stkblc_source_document
        ON sc_trx.stkblc
        (
            source_table,
            source_id,
            source_line_id
        );
    END IF;

END;
$$;


/* ============================================================
   7. POST TRANSACTION_DT -> STKBLc
   ============================================================

   IMPORTANT:
   effective stock route = transaction_dt.stock_effect

   Bukan langsung:
       journal_type.stock_effect

   Karena TAHAP 04 sudah menerapkan:
       JSA -> NONE
       BRG -> mengikuti stock route
   ============================================================ */

CREATE OR REPLACE FUNCTION sc_trx.fn_post_stkblc_from_transaction
(
    p_transaction sc_trx.transaction_dt
)
RETURNS VOID
LANGUAGE plpgsql
AS $$
DECLARE

    v_stock_uniqueid TEXT;
    v_stock_key CHAR(32);

    v_totalcost NUMERIC(18,2);
    v_unitcost NUMERIC(18,6);

BEGIN

    /* --------------------------------------------------------
       1. EFFECTIVE STOCK
       -------------------------------------------------------- */

    IF COALESCE(TRIM(p_transaction.stock_effect), 'NONE')
       NOT IN ('IN','OUT','INOUT')
    THEN
        RETURN;
    END IF;


    /* --------------------------------------------------------
       2. BARANG WAJIB ADA
       -------------------------------------------------------- */

    IF NULLIF(BTRIM(COALESCE(p_transaction.idbarang,'')), '') IS NULL
    THEN
        RAISE EXCEPTION
            'Stock transaction % tidak mempunyai idbarang',
            p_transaction.uniqueid;
    END IF;


    /* --------------------------------------------------------
       3. STOCK IDENTITY
       -------------------------------------------------------- */

    v_stock_uniqueid :=
        sc_trx.fn_stock_uniqueid
        (
            p_transaction.idbranch::TEXT,
            p_transaction.warehouse::TEXT,
            p_transaction.bin::TEXT,
            p_transaction.idbarang::TEXT,
            p_transaction.idunit::TEXT,
            p_transaction.batch::TEXT,
            p_transaction.lotno::TEXT
        );

    v_stock_key :=
        sc_trx.fn_stock_key
        (
            p_transaction.idbranch::TEXT,
            p_transaction.warehouse::TEXT,
            p_transaction.bin::TEXT,
            p_transaction.idbarang::TEXT,
            p_transaction.idunit::TEXT,
            p_transaction.batch::TEXT,
            p_transaction.lotno::TEXT
        );


    /* --------------------------------------------------------
       4. INITIAL COST

       IN:
          gunakan nilai transaksi sebagai nilai awal stock.

       OUT:
          0 pada TAHAP 06.
          TAHAP 07 akan mengisi costing OUT berdasarkan
          average cost / inventory valuation.
       -------------------------------------------------------- */

    IF p_transaction.type_in_out = 'IN'
    THEN

        v_totalcost :=
            COALESCE
            (
                NULLIF(p_transaction.nilai, 0),
                NULLIF(
                    p_transaction.bruto - p_transaction.discount,
                    0
                ),
                NULLIF(
                    p_transaction.harga * p_transaction.qty,
                    0
                ),
                0
            );

        v_totalcost := ROUND(v_totalcost, 2);

        IF p_transaction.qty > 0
        THEN
            v_unitcost :=
                ROUND(
                    v_totalcost / p_transaction.qty,
                    6
                );
        ELSE
            v_unitcost := 0;
        END IF;

    ELSE

        v_totalcost := 0;
        v_unitcost := 0;

    END IF;


    /* --------------------------------------------------------
       5. INSERT STOCK LEDGER
       -------------------------------------------------------- */

    INSERT INTO sc_trx.stkblc
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
        idbarang,
        idunit,
        batch,
        lotno,
        stock_uniqueid,
        stock_key,
        qty_in,
        qty_out,
        unitcost,
        totalcost,
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
        p_transaction.uniqueid,
        COALESCE(p_transaction.source_uniqueid, ''),
        p_transaction.docno,
        p_transaction.doctype,
        p_transaction.journal_type,
        p_transaction.line_no,
        p_transaction.docdate,
        COALESCE(p_transaction.ref_docno, ''),
        COALESCE(p_transaction.ref_doctype, ''),
        p_transaction.type_in_out,
        COALESCE(p_transaction.idbranch, ''),
        COALESCE(p_transaction.cabang, ''),
        COALESCE(p_transaction.idarea, ''),
        COALESCE(p_transaction.warehouse, ''),
        COALESCE(p_transaction.bin, ''),
        p_transaction.idbarang,
        COALESCE(p_transaction.idunit, ''),
        COALESCE(p_transaction.batch, ''),
        COALESCE(p_transaction.lotno, ''),
        v_stock_uniqueid,
        v_stock_key,
        CASE
            WHEN p_transaction.type_in_out = 'IN'
            THEN p_transaction.qty
            ELSE 0
        END,
        CASE
            WHEN p_transaction.type_in_out = 'OUT'
            THEN p_transaction.qty
            ELSE 0
        END,
        v_unitcost,
        v_totalcost,
        COALESCE(NULLIF(BTRIM(p_transaction.currcode), ''), 'IDR'),
        COALESCE(p_transaction.kurs, 1),
        COALESCE(p_transaction.source_table, ''),
        p_transaction.source_id,
        p_transaction.source_line_id,
        COALESCE(p_transaction.idcoa, ''),
        COALESCE(p_transaction.keterangan, ''),
        p_transaction.createdby,
        CURRENT_TIMESTAMP
    );

END;
$$;


/* ============================================================
   8. DELETE STOCK BY TRANSACTION UNIQUEID
   ============================================================ */

CREATE OR REPLACE FUNCTION sc_trx.fn_delete_stkblc_from_transaction
(
    p_uniqueid TEXT
)
RETURNS VOID
LANGUAGE plpgsql
AS $$
BEGIN

    DELETE FROM sc_trx.stkblc
    WHERE uniqueid = p_uniqueid;

END;
$$;


/* ============================================================
   9. MAIN STOCK TRIGGER FUNCTION
   ============================================================ */

CREATE OR REPLACE FUNCTION sc_trx.fn_transaction_stock()
RETURNS TRIGGER
LANGUAGE plpgsql
AS $$
BEGIN

    IF TG_OP = 'INSERT'
    THEN

        PERFORM
            sc_trx.fn_post_stkblc_from_transaction(NEW);

        RETURN NEW;

    ELSIF TG_OP = 'DELETE'
    THEN

        PERFORM
            sc_trx.fn_delete_stkblc_from_transaction(OLD.uniqueid);

        RETURN OLD;

    ELSIF TG_OP = 'UPDATE'
    THEN

        PERFORM
            sc_trx.fn_delete_stkblc_from_transaction(OLD.uniqueid);

        PERFORM
            sc_trx.fn_post_stkblc_from_transaction(NEW);

        RETURN NEW;

    END IF;

    RETURN NEW;

END;
$$;


/* ============================================================
   10. TRIGGER STOCK
   ============================================================ */

CREATE OR REPLACE TRIGGER trg_transaction_stock
AFTER INSERT OR UPDATE OR DELETE
ON sc_trx.transaction_dt
FOR EACH ROW
EXECUTE FUNCTION sc_trx.fn_transaction_stock();


/* ============================================================
   11. AUDIT ROUTING
   ============================================================ */

SELECT
    td.uniqueid,
    td.journal_type,
    td.type_in_out,
    td.stock_effect,
    td.idbranch,
    td.warehouse,
    td.bin,
    td.idbarang,
    td.batch,
    td.lotno,
    td.qty
FROM sc_trx.transaction_dt td
LEFT JOIN sc_trx.stkblc sb
       ON sb.uniqueid = td.uniqueid
WHERE td.stock_effect IN ('IN','OUT','INOUT')
  AND sb.uniqueid IS NULL
ORDER BY td.docdate, td.id;


/* ============================================================
   12. AUDIT ORPHAN STKBLc
   ============================================================ */

SELECT
    sb.uniqueid,
    sb.source_uniqueid,
    sb.docno,
    sb.doctype,
    sb.journal_type,
    sb.type_in_out,
    sb.idbranch,
    sb.idbarang,
    sb.qty_in,
    sb.qty_out
FROM sc_trx.stkblc sb
LEFT JOIN sc_trx.transaction_dt td
       ON td.uniqueid = sb.uniqueid
WHERE td.uniqueid IS NULL
ORDER BY sb.docdate, sb.id;


/* ============================================================
   13. CEK JOURNAL TYPE
   ============================================================ */

SELECT
    journal_type,
    COUNT(*) AS row_count
FROM sc_trx.stkblc
GROUP BY journal_type
ORDER BY journal_type;


/* ============================================================
   14. CEK STRUKTUR
   ============================================================ */

SELECT
    column_name,
    data_type,
    character_maximum_length,
    numeric_precision,
    numeric_scale,
    is_nullable,
    column_default
FROM information_schema.columns
WHERE table_schema = 'sc_trx'
  AND table_name = 'stkblc'
ORDER BY ordinal_position;


/* ============================================================
   TAHAP 06 SELESAI
   ============================================================

   transaction_dt
          |
          | stock_effect
          v
       stkblc
          |
          v
   TAHAP 07
   stkblc_avgcost

   TAHAP 07 menangani:
       - average cost
       - valuation
       - cost OUT
       - return cost
       - transfer cost
       - production cost

   ============================================================ */
/* ============================================================
   PATCH TAHAP 06 - COMPATIBLE LEGACY STKBLC
   Fix:
     - idlocation NOT NULL
     - trxdate NOT NULL
     - docref NOT NULL
     - hist NOT NULL
     - ctype NOT NULL

   Mapping:
     idlocation = transaction_dt.warehouse
     idarea     = transaction_dt.idarea
     trxdate    = transaction_dt.docdate + current time
     docref     = transaction_dt.ref_docno
     hist       = transaction_dt.journal_type
     ctype      = transaction_dt.type_in_out

   JANGAN DROP TABLE.
   ============================================================ */

CREATE OR REPLACE FUNCTION sc_trx.fn_post_stkblc_from_transaction
(
    p_transaction sc_trx.transaction_dt
)
RETURNS VOID
LANGUAGE plpgsql
AS $$
DECLARE
    v_stock_uniqueid TEXT;
    v_stock_key      CHAR(32);
    v_totalcost      NUMERIC(18,2);
    v_unitcost       NUMERIC(18,6);
    v_idgroup        VARCHAR(20);
    v_grouptype      VARCHAR(20);
    v_idlocation     VARCHAR(50);
    v_trxdate        TIMESTAMP;
BEGIN

    /* Hanya stock transaction */
    IF COALESCE(TRIM(p_transaction.stock_effect), 'NONE')
       NOT IN ('IN','OUT','INOUT')
    THEN
        RETURN;
    END IF;

    /* Barang wajib */
    IF NULLIF(BTRIM(COALESCE(p_transaction.idbarang,'')), '') IS NULL
    THEN
        RAISE EXCEPTION
            'Stock transaction % tidak mempunyai idbarang',
            p_transaction.uniqueid;
    END IF;

    /* Location wajib untuk stkblc legacy */
    v_idlocation := NULLIF(BTRIM(COALESCE(p_transaction.warehouse,'')), '');

    IF v_idlocation IS NULL
    THEN
        RAISE EXCEPTION
            'Stock transaction % tidak mempunyai warehouse/idlocation',
            p_transaction.uniqueid;
    END IF;

    /* Tanggal transaksi legacy */
    v_trxdate :=
        COALESCE(p_transaction.docdate, CURRENT_DATE)::DATE
        + CURRENT_TIME;

    /* Master barang */
    SELECT
        COALESCE(mb.idgroup, ''),
        COALESCE(mb.grouptype, 'STOCK')
    INTO
        v_idgroup,
        v_grouptype
    FROM sc_mst.mbarang mb
    WHERE mb.idbarang = p_transaction.idbarang
    LIMIT 1;

    v_idgroup   := COALESCE(v_idgroup, '');
    v_grouptype := COALESCE(NULLIF(BTRIM(v_grouptype), ''), 'STOCK');

    /* Stock identity */
    v_stock_uniqueid :=
        sc_trx.fn_stock_uniqueid
        (
            p_transaction.idbranch::TEXT,
            p_transaction.warehouse::TEXT,
            p_transaction.bin::TEXT,
            p_transaction.idbarang::TEXT,
            p_transaction.idunit::TEXT,
            p_transaction.batch::TEXT,
            p_transaction.lotno::TEXT
        );

    v_stock_key :=
        sc_trx.fn_stock_key
        (
            p_transaction.idbranch::TEXT,
            p_transaction.warehouse::TEXT,
            p_transaction.bin::TEXT,
            p_transaction.idbarang::TEXT,
            p_transaction.idunit::TEXT,
            p_transaction.batch::TEXT,
            p_transaction.lotno::TEXT
        );

    /* Cost IN = nilai transaksi. Cost OUT sementara 0; TAHAP 07 menghitung HPP. */
    IF p_transaction.type_in_out = 'IN'
    THEN
        v_totalcost :=
            ROUND(
                COALESCE
                (
                    NULLIF(p_transaction.nilai, 0),
                    NULLIF(p_transaction.bruto - p_transaction.discount, 0),
                    NULLIF(p_transaction.harga * p_transaction.qty, 0),
                    0
                ),
                2
            );

        IF COALESCE(p_transaction.qty,0) > 0
        THEN
            v_unitcost := ROUND(v_totalcost / p_transaction.qty, 6);
        ELSE
            v_unitcost := 0;
        END IF;
    ELSE
        v_totalcost := 0;
        v_unitcost  := 0;
    END IF;

    /*
       INSERT kompatibel dengan STKBLC legacy + kolom TAHAP 06 baru.
    */
    INSERT INTO sc_trx.stkblc
    (
        /* LEGACY REQUIRED */
        idlocation,
        idarea,
        batch,
        idbarang,
        trxdate,
        doctype,
        docno,
        docref,
        hist,
        ctype,

        /* LEGACY STOCK VALUE */
        qty_in,
        qty_out,
        pricelst_in,
        pricelst_out,
        pricelst_sld,
        currcode,
        currvalue,
        tax,
        disc,
        biaya,
        idgroup,
        grouptype,
        is_posted,
        posted_at,
        picby,
        unit,
        description,
        created_at,
        created_by,
        status,

        /* TAHAP 06 */
        uniqueid,
        source_uniqueid,
        journal_type,
        line_no,
        docdate,
        ref_docno,
        ref_doctype,
        type_in_out,
        idbranch,
        cabang,
        warehouse,
        bin,
        idunit,
        lotno,
        stock_uniqueid,
        stock_key,
        unitcost,
        totalcost,
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
        /* LEGACY REQUIRED */
        v_idlocation,
        COALESCE(p_transaction.idarea, ''),
        COALESCE(p_transaction.batch, ''),
        p_transaction.idbarang,
        v_trxdate,
        COALESCE(p_transaction.doctype, ''),
        p_transaction.docno,
        COALESCE(p_transaction.ref_docno, ''),
        COALESCE(NULLIF(BTRIM(p_transaction.journal_type), ''), p_transaction.doctype),
        COALESCE(p_transaction.type_in_out, 'IN'),

        /* LEGACY STOCK VALUE */
        CASE WHEN p_transaction.type_in_out = 'IN'  THEN COALESCE(p_transaction.qty,0) ELSE 0 END,
        CASE WHEN p_transaction.type_in_out = 'OUT' THEN COALESCE(p_transaction.qty,0) ELSE 0 END,
        CASE WHEN p_transaction.type_in_out = 'IN'  THEN v_unitcost ELSE 0 END,
        0,
        0,
        COALESCE(NULLIF(BTRIM(p_transaction.currcode), ''), 'IDR'),
        COALESCE(p_transaction.kurs, 1),
        COALESCE(p_transaction.pajak, 0),
        COALESCE(p_transaction.discount, 0),
        0,
        v_idgroup,
        v_grouptype,
        FALSE,
        NULL,
        p_transaction.createdby,
        p_transaction.idunit,
        COALESCE(p_transaction.keterangan, ''),
        COALESCE(p_transaction.createddate, CURRENT_TIMESTAMP),
        p_transaction.createdby,
        'F',

        /* TAHAP 06 */
        p_transaction.uniqueid,
        COALESCE(p_transaction.source_uniqueid, ''),
        p_transaction.journal_type,
        p_transaction.line_no,
        p_transaction.docdate,
        COALESCE(p_transaction.ref_docno, ''),
        COALESCE(p_transaction.ref_doctype, ''),
        p_transaction.type_in_out,
        COALESCE(p_transaction.idbranch, ''),
        COALESCE(p_transaction.cabang, ''),
        COALESCE(p_transaction.warehouse, ''),
        COALESCE(p_transaction.bin, ''),
        COALESCE(p_transaction.idunit, ''),
        COALESCE(p_transaction.lotno, ''),
        v_stock_uniqueid,
        v_stock_key,
        v_unitcost,
        v_totalcost,
        COALESCE(p_transaction.kurs, 1),
        COALESCE(p_transaction.source_table, ''),
        p_transaction.source_id,
        p_transaction.source_line_id,
        COALESCE(p_transaction.idcoa, ''),
        COALESCE(p_transaction.keterangan, ''),
        p_transaction.createdby,
        COALESCE(p_transaction.createddate, CURRENT_TIMESTAMP)
    );

END;
$$;

/* ============================================================
   CEK
   ============================================================ */
SELECT
    column_name,
    is_nullable,
    column_default
FROM information_schema.columns
WHERE table_schema = 'sc_trx'
  AND table_name = 'stkblc'
  AND column_name IN
  (
      'idlocation','idarea','trxdate','docref',
      'hist','ctype','uniqueid','stock_uniqueid','stock_key'
  )
ORDER BY ordinal_position;
