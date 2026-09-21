/* ============================================================
   RECOMPILE NOTE - BASED ON USER REFERENCE TAHAP 05
   ============================================================

   CHECK RESULT:
   - journal_type sudah ada sebagai bagian identitas header.
   - grouping tetap:
       docno, doctype, journal_type, type_in_out,
       idbranch, ref_docno, ref_doctype
   - transaction_hd tetap SUMMARY.
   - transaction_dt tetap SOURCE OF TRUTH.
   - manual accounting tetap tidak menyalin idcoa /
     counter_idcoa ke header.
   - routing snapshot tetap tersedia.
   - TAHAP 05 tidak mengubah stock / stkblc.
   - DROP TABLE tidak digunakan agar aman untuk RECOMPILE
     terhadap data existing.

   Tidak ada perubahan functional yang wajib pada TAHAP 05
   setelah dibandingkan dengan reference yang diberikan.
   Perbedaan DROP+CREATE pada reference dipertahankan menjadi
   CREATE/ALTER yang rerun-safe pada versi recompile.

   ============================================================ */

/* ============================================================
   JSYS ERP
   TAHAP 05 - TRANSACTION_HD JSYS FINAL / RECOMPILE V3
   ============================================================

   FUNGSI:
   - transaction_hd = SUMMARY / HEADER
   - transaction_dt = SOURCE OF TRUTH
   - transaction_hd TIDAK menjadi sumber transaksi utama
   - header dapat di-recalculate dari transaction_dt
   - tidak membuat trigger ke transaction_dt pada tahap ini
     (trigger posting/recalculate tetap pada tahap berikutnya)

   GROUPING HEADER:
       docno
       doctype
       journal_type
       type_in_out
       idbranch
       ref_docno
       ref_doctype

   KEUNTUNGAN:
   - IN dan OUT tidak tercampur
   - transfer multi-branch tidak menggunakan MAX(idbranch)
   - satu source document dapat mempunyai banyak transaction row
   - satu branch dapat diringkas sendiri
   - accounting / stock / asset routing tetap dapat ditelusuri
     dari journal_type

   ============================================================ */


/* ============================================================
   0. CREATE TABLE TANPA NOTICE "ALREADY EXISTS"
   ============================================================ */

DO $$
BEGIN

    IF to_regclass('sc_trx.transaction_hd') IS NULL
    THEN

        CREATE TABLE sc_trx.transaction_hd
        (
            id BIGSERIAL NOT NULL,

            /* DOCUMENT */
            docno VARCHAR(20) NOT NULL,
            doctype VARCHAR(20) NOT NULL,
            journal_type CHAR(6) NOT NULL,

            /* ACTUAL MOVEMENT */
            type_in_out CHAR(3) NOT NULL,

            docdate DATE NOT NULL,

            /* BRANCH */
            idbranch CHAR(20) NOT NULL DEFAULT '',
            cabang CHAR(30) NOT NULL DEFAULT '',

            /* REFERENCE */
            ref_docno VARCHAR(50) NOT NULL DEFAULT '',
            ref_doctype VARCHAR(20) NOT NULL DEFAULT '',

            /* ROUTING SNAPSHOT */
            module VARCHAR(30) NOT NULL DEFAULT '',
            direction CHAR(5) NOT NULL DEFAULT '',
            stock_effect CHAR(5) NOT NULL DEFAULT 'NONE',
            accounting_effect CHAR(3) NOT NULL DEFAULT 'NO',
            asset_effect CHAR(5) NOT NULL DEFAULT 'NONE',

            /* SUMMARY QTY */
            total_qty NUMERIC(18,6) NOT NULL DEFAULT 0,
            total_qty_in NUMERIC(18,6) NOT NULL DEFAULT 0,
            total_qty_out NUMERIC(18,6) NOT NULL DEFAULT 0,

            /* SUMMARY VALUE */
            total_bruto NUMERIC(18,2) NOT NULL DEFAULT 0,
            total_discount NUMERIC(18,2) NOT NULL DEFAULT 0,
            total_nilai NUMERIC(18,2) NOT NULL DEFAULT 0,
            total_dpp NUMERIC(18,2) NOT NULL DEFAULT 0,
            total_pajak NUMERIC(18,2) NOT NULL DEFAULT 0,
            total_total NUMERIC(18,2) NOT NULL DEFAULT 0,

            /* SUMMARY ACCOUNTING */
            total_debet NUMERIC(18,2) NOT NULL DEFAULT 0,
            total_kredit NUMERIC(18,2) NOT NULL DEFAULT 0,
            balance NUMERIC(18,2) NOT NULL DEFAULT 0,

            /* INFORMATION */
            total_line BIGINT NOT NULL DEFAULT 0,

            /* AUDIT */
            createdby VARCHAR(50),
            createddate TIMESTAMP WITHOUT TIME ZONE
                DEFAULT CURRENT_TIMESTAMP,
            updatedby VARCHAR(50),
            updateddate TIMESTAMP WITHOUT TIME ZONE
                DEFAULT CURRENT_TIMESTAMP,

            CONSTRAINT pk_transaction_hd
                PRIMARY KEY (id),

            CONSTRAINT chk_transaction_hd_type_in_out
                CHECK (type_in_out IN ('IN','OUT')),

            CONSTRAINT chk_transaction_hd_total_qty
                CHECK (total_qty >= 0),

            CONSTRAINT chk_transaction_hd_total_qty_in
                CHECK (total_qty_in >= 0),

            CONSTRAINT chk_transaction_hd_total_qty_out
                CHECK (total_qty_out >= 0),

            CONSTRAINT chk_transaction_hd_total_bruto
                CHECK (total_bruto >= 0),

            CONSTRAINT chk_transaction_hd_total_discount
                CHECK (total_discount >= 0),

            CONSTRAINT chk_transaction_hd_total_nilai
                CHECK (total_nilai >= 0),

            CONSTRAINT chk_transaction_hd_total_dpp
                CHECK (total_dpp >= 0),

            CONSTRAINT chk_transaction_hd_total_pajak
                CHECK (total_pajak >= 0),

            CONSTRAINT chk_transaction_hd_total_total
                CHECK (total_total >= 0),

            CONSTRAINT chk_transaction_hd_total_debet
                CHECK (total_debet >= 0),

            CONSTRAINT chk_transaction_hd_total_kredit
                CHECK (total_kredit >= 0),

            CONSTRAINT chk_transaction_hd_total_line
                CHECK (total_line >= 0),

            CONSTRAINT chk_transaction_hd_balance
                CHECK (balance >= 0),

            CONSTRAINT chk_transaction_hd_stock_effect
                CHECK (stock_effect IN ('IN','OUT','INOUT','NONE')),

            CONSTRAINT chk_transaction_hd_accounting_effect
                CHECK (accounting_effect IN ('YES','NO')),

            CONSTRAINT chk_transaction_hd_asset_effect
                CHECK (asset_effect IN ('IN','OUT','INOUT','NONE')),

            CONSTRAINT chk_transaction_hd_direction
                CHECK (direction IN ('IN','OUT','INOUT','')),

            CONSTRAINT chk_transaction_hd_docno_not_blank
                CHECK (BTRIM(docno) <> ''),

            CONSTRAINT chk_transaction_hd_doctype_not_blank
                CHECK (BTRIM(doctype) <> '')
        );

    END IF;

END;
$$;


/* ============================================================
   0.1 NORMALIZE DOCNO EXISTING
   ============================================================

   Global rule JSYS:
       docno maksimum 20 karakter.

   Tidak melakukan truncation.
   Bila data existing > 20 karakter, script dihentikan agar
   dokumen tidak berubah diam-diam.
   ============================================================ */

DO $$
DECLARE
    v_long_docno BIGINT;
BEGIN

    SELECT COUNT(*)
    INTO v_long_docno
    FROM sc_trx.transaction_hd
    WHERE LENGTH(COALESCE(docno::TEXT,'')) > 20;

    IF v_long_docno > 0
    THEN
        RAISE EXCEPTION
            'transaction_hd.docno memiliki % baris > 20 karakter. Rapikan data terlebih dahulu.',
            v_long_docno;
    END IF;

    IF EXISTS
    (
        SELECT 1
        FROM information_schema.columns
        WHERE table_schema = 'sc_trx'
          AND table_name = 'transaction_hd'
          AND column_name = 'docno'
          AND character_maximum_length IS DISTINCT FROM 20
    )
    THEN
        ALTER TABLE sc_trx.transaction_hd
            ALTER COLUMN docno TYPE VARCHAR(20)
            USING docno::VARCHAR(20);
    END IF;

END;
$$;


/* ============================================================
   1. EXTEND TABLE LAMA
   ============================================================ */

ALTER TABLE sc_trx.transaction_hd
    ADD COLUMN IF NOT EXISTS module VARCHAR(30) NOT NULL DEFAULT '';

ALTER TABLE sc_trx.transaction_hd
    ADD COLUMN IF NOT EXISTS direction CHAR(5) NOT NULL DEFAULT '';

ALTER TABLE sc_trx.transaction_hd
    ADD COLUMN IF NOT EXISTS stock_effect CHAR(5) NOT NULL DEFAULT 'NONE';

ALTER TABLE sc_trx.transaction_hd
    ADD COLUMN IF NOT EXISTS accounting_effect CHAR(3) NOT NULL DEFAULT 'NO';

ALTER TABLE sc_trx.transaction_hd
    ADD COLUMN IF NOT EXISTS asset_effect CHAR(5) NOT NULL DEFAULT 'NONE';


/* ============================================================
   2. NORMALIZE ROUTING HEADER DARI JOURNAL TYPE
   ============================================================

   Bila header lama sudah ada tetapi routing kosong,
   isi kembali berdasarkan journal_type.

   Untuk existing transaction_hd yang berasal dari
   transaction_dt versi lama, routing dapat di-refresh
   kemudian oleh proses recalculate.
   ============================================================ */

UPDATE sc_trx.transaction_hd h
SET
    module = COALESCE(TRIM(jt.module), ''),
    direction = COALESCE(TRIM(jt.direction), ''),
    stock_effect = COALESCE(TRIM(jt.stock_effect), 'NONE'),
    accounting_effect = COALESCE(TRIM(jt.accounting_effect), 'NO'),
    asset_effect = COALESCE(TRIM(jt.asset_effect), 'NONE')
FROM sc_mst.journal_type jt
WHERE jt.journal_type = h.journal_type
  AND
  (
      COALESCE(BTRIM(h.module), '') = ''
      OR COALESCE(BTRIM(h.direction), '') = ''
      OR COALESCE(BTRIM(h.stock_effect), '') = ''
      OR COALESCE(BTRIM(h.accounting_effect), '') = ''
      OR COALESCE(BTRIM(h.asset_effect), '') = ''
  );


/* ============================================================
   2.1 MANUAL ACCOUNTING
   ============================================================

   TAHAP 04 telah menambahkan:
       idcoa
       counter_idcoa
       debet_kredit

   transaction_hd tidak perlu menyimpan counter_idcoa karena:
       transaction_dt = source of truth
       transaction_hd = summary

   Untuk manual accounting:
       debet_kredit = D -> type_in_out = IN
       debet_kredit = K -> type_in_out = OUT

   Header tetap diringkas berdasarkan:
       docno
       doctype
       journal_type
       type_in_out
       idbranch
       ref_docno
       ref_doctype

   Dengan demikian satu dokumen manual yang memiliki arah berbeda
   tidak tercampur pada summary.
   ============================================================ */


/* ============================================================
   3. ADD CONSTRAINT SECARA AMAN
   ============================================================ */

DO $$
BEGIN

    /* UNIQUE HEADER */
    IF NOT EXISTS
    (
        SELECT 1
        FROM pg_constraint
        WHERE conrelid = 'sc_trx.transaction_hd'::regclass
          AND conname = 'uq_transaction_hd'
    )
    THEN
        ALTER TABLE sc_trx.transaction_hd
        ADD CONSTRAINT uq_transaction_hd
        UNIQUE
        (
            docno,
            doctype,
            journal_type,
            type_in_out,
            idbranch,
            ref_docno,
            ref_doctype
        );
    END IF;


    /* FOREIGN KEY JOURNAL TYPE */
    IF NOT EXISTS
    (
        SELECT 1
        FROM pg_constraint
        WHERE conrelid = 'sc_trx.transaction_hd'::regclass
          AND conname = 'fk_transaction_hd_journal_type'
    )
    THEN
        ALTER TABLE sc_trx.transaction_hd
        ADD CONSTRAINT fk_transaction_hd_journal_type
        FOREIGN KEY (journal_type)
        REFERENCES sc_mst.journal_type(journal_type);
    END IF;


    /* TYPE IN / OUT */
    IF NOT EXISTS
    (
        SELECT 1
        FROM pg_constraint
        WHERE conrelid = 'sc_trx.transaction_hd'::regclass
          AND conname = 'chk_transaction_hd_type_in_out'
    )
    THEN
        ALTER TABLE sc_trx.transaction_hd
        ADD CONSTRAINT chk_transaction_hd_type_in_out
        CHECK (type_in_out IN ('IN','OUT'));
    END IF;


    /* STOCK EFFECT */
    IF NOT EXISTS
    (
        SELECT 1
        FROM pg_constraint
        WHERE conrelid = 'sc_trx.transaction_hd'::regclass
          AND conname = 'chk_transaction_hd_stock_effect'
    )
    THEN
        ALTER TABLE sc_trx.transaction_hd
        ADD CONSTRAINT chk_transaction_hd_stock_effect
        CHECK (stock_effect IN ('IN','OUT','INOUT','NONE'));
    END IF;


    /* ACCOUNTING EFFECT */
    IF NOT EXISTS
    (
        SELECT 1
        FROM pg_constraint
        WHERE conrelid = 'sc_trx.transaction_hd'::regclass
          AND conname = 'chk_transaction_hd_accounting_effect'
    )
    THEN
        ALTER TABLE sc_trx.transaction_hd
        ADD CONSTRAINT chk_transaction_hd_accounting_effect
        CHECK (accounting_effect IN ('YES','NO'));
    END IF;


    /* ASSET EFFECT */
    IF NOT EXISTS
    (
        SELECT 1
        FROM pg_constraint
        WHERE conrelid = 'sc_trx.transaction_hd'::regclass
          AND conname = 'chk_transaction_hd_asset_effect'
    )
    THEN
        ALTER TABLE sc_trx.transaction_hd
        ADD CONSTRAINT chk_transaction_hd_asset_effect
        CHECK (asset_effect IN ('IN','OUT','INOUT','NONE'));
    END IF;


    /* DIRECTION */
    IF NOT EXISTS
    (
        SELECT 1
        FROM pg_constraint
        WHERE conrelid = 'sc_trx.transaction_hd'::regclass
          AND conname = 'chk_transaction_hd_direction'
    )
    THEN
        ALTER TABLE sc_trx.transaction_hd
        ADD CONSTRAINT chk_transaction_hd_direction
        CHECK (direction IN ('IN','OUT','INOUT',''));
    END IF;

END;
$$;


/* ============================================================
   4. INDEX TANPA NOTICE
   ============================================================ */

DO $$
BEGIN

    IF to_regclass('sc_trx.idx_transaction_hd_doc') IS NULL THEN
        CREATE INDEX idx_transaction_hd_doc
        ON sc_trx.transaction_hd
        (
            docno,
            doctype
        );
    END IF;


    IF to_regclass('sc_trx.idx_transaction_hd_journal_type') IS NULL THEN
        CREATE INDEX idx_transaction_hd_journal_type
        ON sc_trx.transaction_hd
        (
            journal_type,
            type_in_out
        );
    END IF;


    IF to_regclass('sc_trx.idx_transaction_hd_branch_date') IS NULL THEN
        CREATE INDEX idx_transaction_hd_branch_date
        ON sc_trx.transaction_hd
        (
            idbranch,
            docdate
        );
    END IF;


    IF to_regclass('sc_trx.idx_transaction_hd_reference') IS NULL THEN
        CREATE INDEX idx_transaction_hd_reference
        ON sc_trx.transaction_hd
        (
            ref_docno,
            ref_doctype
        );
    END IF;


    IF to_regclass('sc_trx.idx_transaction_hd_effect') IS NULL THEN
        CREATE INDEX idx_transaction_hd_effect
        ON sc_trx.transaction_hd
        (
            accounting_effect,
            stock_effect,
            asset_effect
        );
    END IF;


    IF to_regclass('sc_trx.idx_transaction_hd_module_date') IS NULL THEN
        CREATE INDEX idx_transaction_hd_module_date
        ON sc_trx.transaction_hd
        (
            module,
            docdate
        );
    END IF;

END;
$$;


/* ============================================================
   4.1 VALIDASI JOURNAL TYPE
   ============================================================ */

DO $$
DECLARE
    v_invalid BIGINT;
BEGIN

    SELECT COUNT(*)
    INTO v_invalid
    FROM sc_trx.transaction_hd h
    LEFT JOIN sc_mst.journal_type jt
           ON jt.journal_type = h.journal_type
    WHERE jt.journal_type IS NULL;

    IF v_invalid > 0
    THEN
        RAISE NOTICE
            'WARNING: % transaction_hd tidak memiliki journal_type pada master.',
            v_invalid;
    END IF;

END;
$$;


/* ============================================================
   5. VALIDASI KONSISTENSI JOURNAL TYPE ↔ HEADER ROUTING
   ============================================================ */

DO $$
DECLARE
    v_count BIGINT;
BEGIN

    SELECT COUNT(*)
    INTO v_count
    FROM sc_trx.transaction_hd h
    JOIN sc_mst.journal_type jt
      ON jt.journal_type = h.journal_type
    WHERE
        COALESCE(TRIM(h.module),'') <>
        COALESCE(TRIM(jt.module),'')
        OR
        COALESCE(TRIM(h.direction),'') <>
        COALESCE(TRIM(jt.direction),'')
        OR
        COALESCE(TRIM(h.stock_effect),'') <>
        COALESCE(TRIM(jt.stock_effect),'')
        OR
        COALESCE(TRIM(h.accounting_effect),'') <>
        COALESCE(TRIM(jt.accounting_effect),'')
        OR
        COALESCE(TRIM(h.asset_effect),'') <>
        COALESCE(TRIM(jt.asset_effect),'');


    IF v_count > 0
    THEN

        RAISE NOTICE
            'Ada % transaction_hd dengan routing lama. Jalankan recalculate dari transaction_dt pada tahap berikutnya.',
            v_count;

    END IF;

END;
$$;


/* ============================================================
   6. CHECK RESULT

   Header harus selalu dapat dibandingkan dengan detail.

   QUERY DI BAWAH INI TIDAK MENGUBAH DATA.
   ============================================================ */


/* ------------------------------------------------------------
   6.1 STRUKTUR
   ------------------------------------------------------------ */

SELECT
    column_name,
    data_type,
    character_maximum_length,
    numeric_precision,
    numeric_scale
FROM information_schema.columns
WHERE table_schema = 'sc_trx'
  AND table_name = 'transaction_hd'
ORDER BY ordinal_position;


/* ------------------------------------------------------------
   6.2 HEADER TANPA JOURNAL TYPE
   ------------------------------------------------------------ */

SELECT
    h.id,
    h.docno,
    h.doctype,
    h.journal_type
FROM sc_trx.transaction_hd h
LEFT JOIN sc_mst.journal_type jt
       ON jt.journal_type = h.journal_type
WHERE jt.journal_type IS NULL;


/* ------------------------------------------------------------
   6.3 CEK HEADER DENGAN DETAIL TIDAK ADA
   ------------------------------------------------------------ */

SELECT
    h.docno,
    h.doctype,
    h.journal_type,
    h.type_in_out,
    h.idbranch
FROM sc_trx.transaction_hd h
LEFT JOIN sc_trx.transaction_dt d
       ON d.docno = h.docno
      AND d.doctype = h.doctype
      AND d.journal_type = h.journal_type
      AND d.type_in_out = h.type_in_out
      AND d.idbranch = h.idbranch
      AND COALESCE(d.ref_docno,'') = COALESCE(h.ref_docno,'')
      AND COALESCE(d.ref_doctype,'') = COALESCE(h.ref_doctype,'')
WHERE d.id IS NULL;


/* ------------------------------------------------------------
   6.4 CEK DETAIL TANPA HEADER
   ------------------------------------------------------------ */

SELECT
    d.docno,
    d.doctype,
    d.journal_type,
    d.type_in_out,
    d.idbranch,
    d.ref_docno,
    d.ref_doctype
FROM sc_trx.transaction_dt d
LEFT JOIN sc_trx.transaction_hd h
       ON h.docno = d.docno
      AND h.doctype = d.doctype
      AND h.journal_type = d.journal_type
      AND h.type_in_out = d.type_in_out
      AND h.idbranch = d.idbranch
      AND COALESCE(h.ref_docno,'') = COALESCE(d.ref_docno,'')
      AND COALESCE(h.ref_doctype,'') = COALESCE(d.ref_doctype,'')
WHERE h.id IS NULL;


/* ============================================================
   7. CONTOH SUMMARY YANG BENAR
   ============================================================

   GRNREC:

       DOC001 | LPB | GRNREC | IN | B01

           detail 1  qty 100
           detail 2  qty 200
           detail 3  qty  50

       header:
           total_qty = 350


   BRNTRF:

       TRF001 | TRANSFER | BRNTRF | OUT | B01
       TRF001 | TRANSFER | BRNTRF | IN  | B02

       HEADER 1:
           TRF001 | BRNTRF | OUT | B01

       HEADER 2:
           TRF001 | BRNTRF | IN  | B02

   Tidak boleh menggunakan:
       MAX(idbranch)
       MAX(type_in_out)

   Karena keduanya adalah bagian dari identitas summary.
   ============================================================ */


/* ============================================================
   8. REFERENCE LOGIC UNTUK TAHAP 17
   ============================================================

   Saat recalculate nanti:

       transaction_dt
          |
          | GROUP BY
          v
       docno
       doctype
       journal_type
       type_in_out
       idbranch
       ref_docno
       ref_doctype
          |
          v
       transaction_hd


   Field summary:

       SUM(qty)        -> total_qty
       SUM(bruto)      -> total_bruto
       SUM(discount)   -> total_discount
       SUM(nilai)      -> total_nilai
       SUM(dpp)        -> total_dpp
       SUM(pajak)      -> total_pajak
       SUM(total)      -> total_total
       SUM(debet)      -> total_debet
       SUM(kredit)     -> total_kredit
       COUNT(*)        -> total_line

   balance:

       ABS(total_debet - total_kredit)

   IMPORTANT:
       Jangan menjumlahkan nilai antar currency berbeda.
       Header harus berasal dari detail dengan currency
       yang konsisten untuk satu dokumen/group.
       Validasi currency akan ditangani pada proses
       recalculate / posting berikutnya.


   ============================================================ */


/* ============================================================
   9. RELASI DENGAN TAHAP 04

   transaction_dt:
       source of truth

   transaction_hd:
       summary

   transaction_dt mempunyai:

       module
       direction
       stock_effect
       accounting_effect
       asset_effect

   transaction_hd menyimpan snapshot yang sama agar
   query header / posting / monitoring lebih cepat.

   ============================================================ */


/* ============================================================
   10. RELASI DENGAN POSTING ACCOUNTING

   Contoh GRNREC - BRG:

       transaction_dt
            |
            +--- stock_effect = IN
            +--- accounting_effect = YES
            +--- idcoa = ppersediaan
            +--- idtax = PPN11 / BBB11
            |
            +--------------------------+
                                       |
                                       v
                               journal generator
                                       |
                    +------------------+------------------+
                    |                  |                  |
                    v                  v                  v
                PERSEDIAAN         PPN MASUKAN          AP

   Contoh GRNREC - JSA:

       transaction_dt
            |
            +--- stock_effect = NONE
            +--- accounting_effect = YES
            +--- idcoa = pjasa
            +--- idtax = PPN11 / BBB11
            |
            v
        journal generator
            |
       JASA + TAX + AP


   Contoh SALESX:

       transaction_dt
            |
            +--- stock_effect = NONE
            +--- accounting_effect = YES
            +--- idcoa = ppendapatan
            +--- idtax = PPN11 / BBB11
            |
            v
        journal generator
            |
            +--- AR
            +--- SALES
            +--- TAX OUTPUT
            +--- COGS (COST)
            +--- INVENTORY (COST)

   ============================================================ */


/* ============================================================
   TAHAP 05 SELESAI
   ============================================================

   Tidak ada trigger posting pada tahap ini.

   Tahap berikut:
       TAHAP 06  stkblc
       TAHAP 07  stkblc_avgcost
       TAHAP 08  assetblc
       TAHAP 09  jurnal_hd
       TAHAP 10  jurnal_dt
       TAHAP 11-16 reverse/post
       TAHAP 17 recalculate transaction_hd
       TAHAP 18 trigger transaction_dt

   ============================================================ */
