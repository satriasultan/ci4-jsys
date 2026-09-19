/* ============================================================
   TAHAP 5
   CREATE TABLE sc_trx.transaction_hd

   FUNGSI:
   - Header merupakan hasil summary dari sc_trx.transaction_dt
   - Tidak menjadi sumber transaksi utama
   - Detail transaction_dt tetap menjadi source of truth
   - transaction_hd dapat di-refresh/recalculate dari transaction_dt

   LOGIC GROUPING:
   1. docno
   2. doctype
   3. journal_type
   4. type_in_out
   5. idbranch
   6. ref_docno
   7. ref_doctype

   type_in_out DIMASUKKAN KE GROUPING karena:
   - IN dan OUT tidak boleh tercampur dalam summary
   - Journal type INOUT dapat mempunyai transaksi IN dan OUT
   - Contoh transfer:
       B01 = OUT
       B02 = IN

   idbranch DIMASUKKAN KE GROUPING karena:
   - Satu dokumen dapat melibatkan lebih dari satu branch
   - Tidak boleh menggunakan MAX(idbranch) untuk dokumen multi-branch
   ============================================================ */


/* ============================================================
   1. HAPUS TABLE LAMA JIKA MEMANG MASIH DALAM TAHAP DEVELOPMENT
   ============================================================ */

DROP TABLE IF EXISTS sc_trx.transaction_hd CASCADE;


/* ============================================================
   2. CREATE TABLE
   ============================================================ */

CREATE TABLE sc_trx.transaction_hd
(
    id BIGSERIAL NOT NULL,

    docno VARCHAR(50) NOT NULL,
    doctype VARCHAR(20) NOT NULL,

    journal_type CHAR(6) NOT NULL,
    type_in_out CHAR(3) NOT NULL,

    docdate DATE NOT NULL,

    idbranch CHAR(20) NOT NULL DEFAULT '',
    cabang CHAR(30) NOT NULL DEFAULT '',

    ref_docno VARCHAR(50) NOT NULL DEFAULT '',
    ref_doctype VARCHAR(20) NOT NULL DEFAULT '',

    /*
       SUMMARY QUANTITY
    */
    total_qty NUMERIC(18,6) NOT NULL DEFAULT 0,
    total_qty_in NUMERIC(18,6) NOT NULL DEFAULT 0,
    total_qty_out NUMERIC(18,6) NOT NULL DEFAULT 0,

    /*
       SUMMARY VALUE
    */
    total_bruto NUMERIC(18,2) NOT NULL DEFAULT 0,
    total_discount NUMERIC(18,2) NOT NULL DEFAULT 0,
    total_nilai NUMERIC(18,2) NOT NULL DEFAULT 0,
    total_dpp NUMERIC(18,2) NOT NULL DEFAULT 0,
    total_pajak NUMERIC(18,2) NOT NULL DEFAULT 0,
    total_total NUMERIC(18,2) NOT NULL DEFAULT 0,

    /*
       SUMMARY ACCOUNTING
    */
    total_debet NUMERIC(18,2) NOT NULL DEFAULT 0,
    total_kredit NUMERIC(18,2) NOT NULL DEFAULT 0,
    balance NUMERIC(18,2) NOT NULL DEFAULT 0,

    /*
       INFORMASI BARIS
    */
    total_line BIGINT NOT NULL DEFAULT 0,

    /*
       AUDIT
    */
    createdby VARCHAR(50),
    createddate TIMESTAMP WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updatedby VARCHAR(50),
    updateddate TIMESTAMP WITHOUT TIME ZONE,

    CONSTRAINT pk_transaction_hd
        PRIMARY KEY (id),

    /*
       IN / OUT
    */
    CONSTRAINT chk_transaction_hd_type_in_out
        CHECK (type_in_out IN ('IN','OUT')),

    /*
       NILAI TIDAK BOLEH NEGATIF
    */
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
        CHECK (total_kredit >= 0)
);


/* ============================================================
   3. UNIQUE HEADER

   Satu kombinasi berikut hanya mempunyai satu summary:

   docno
   doctype
   journal_type
   type_in_out
   idbranch
   ref_docno
   ref_doctype
   ============================================================ */

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


/* ============================================================
   4. FOREIGN KEY KE MASTER JOURNAL TYPE
   ============================================================ */

ALTER TABLE sc_trx.transaction_hd
ADD CONSTRAINT fk_transaction_hd_journal_type
FOREIGN KEY (journal_type)
REFERENCES sc_mst.journal_type(journal_type);


/* ============================================================
   5. INDEX DOCUMENT
   ============================================================ */

CREATE INDEX idx_transaction_hd_doc
ON sc_trx.transaction_hd
(
    docno,
    doctype
);


/* ============================================================
   6. INDEX JOURNAL + TYPE
   ============================================================ */

CREATE INDEX idx_transaction_hd_journal_type
ON sc_trx.transaction_hd
(
    journal_type,
    type_in_out
);


/* ============================================================
   7. INDEX BRANCH + DATE
   ============================================================ */

CREATE INDEX idx_transaction_hd_branch_date
ON sc_trx.transaction_hd
(
    idbranch,
    docdate
);


/* ============================================================
   8. INDEX REFERENCE DOCUMENT
   ============================================================ */

CREATE INDEX idx_transaction_hd_reference
ON sc_trx.transaction_hd
(
    ref_docno,
    ref_doctype
);


/* ============================================================
   9. CONTOH HASIL SUMMARY

   transaction_dt:

   DOC-001 | PURCHASE | GRNREC | IN  | B01 | QTY 100
   DOC-001 | PURCHASE | GRNREC | IN  | B01 | QTY 200
   DOC-001 | PURCHASE | GRNREC | IN  | B01 | QTY  50

   transaction_hd:

   DOC-001 | PURCHASE | GRNREC | IN | B01
   total_qty = 350


   TRANSFER:

   TRF-001 | TRANSFER | BRNTRF | OUT | B01 | QTY 100
   TRF-001 | TRANSFER | BRNTRF | IN  | B02 | QTY 100

   transaction_hd akan menjadi:

   TRF-001 | TRANSFER | BRNTRF | OUT | B01 | QTY 100
   TRF-001 | TRANSFER | BRNTRF | IN  | B02 | QTY 100

   Jadi branch B01 dan B02 tidak tercampur.
   ============================================================ */


/* ============================================================
   10. CEK STRUKTUR
   ============================================================ */

SELECT column_name, data_type, character_maximum_length, numeric_precision, numeric_scale
FROM information_schema.columns
WHERE table_schema = 'sc_trx'
  AND table_name = 'transaction_hd'
ORDER BY ordinal_position;