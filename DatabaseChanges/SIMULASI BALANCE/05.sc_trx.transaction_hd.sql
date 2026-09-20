/* ============================================================
   JSYS ERP
   TAHAP 05 - TRANSACTION_HD
   FINAL V3
   ============================================================

   BASÉD ON:
   - TAHAP 01 FINAL  : stock identity
   - TAHAP 02 FINAL  : journal_type
   - TAHAP 03 FINAL  : journal_type_coa
   - TAHAP 04 FINAL  : transaction_dt

   FUNGSI:
   - Header merupakan SUMMARY dari sc_trx.transaction_dt.
   - transaction_dt tetap SOURCE OF TRUTH.
   - transaction_hd tidak membuat transaksi baru.
   - Recalculate akan dibangun dari transaction_dt pada TAHAP 17.

   GROUPING HEADER:
       docno
       doctype
       journal_type
       type_in_out
       idbranch
       ref_docno
       ref_doctype

   CATATAN:
   - type_in_out tetap untuk pemisahan IN / OUT.
   - idbranch tetap bagian dari grouping untuk multibranch.
   - Manual accounting dari TAHAP 04:
         idcoa
         counter_idcoa
         debet_kredit
     tidak perlu disalin ke header karena transaction_dt adalah source
     detail/source of truth.
   - Routing snapshot disimpan agar monitoring/query header lebih mudah.
   - docno mengikuti standard JSYS maksimum 20 karakter.
   ============================================================ */


/* ============================================================
   1. HAPUS TABLE LAMA
   ============================================================

   TAHAP INI masih menggunakan mode development:
       DROP + CREATE

   Jangan digunakan terhadap production yang sudah memiliki data
   transaction_hd penting.

   CASCADE dipertahankan karena ini adalah struktur tahap development
   seperti source version sebelumnya.
   ============================================================ */

DROP TABLE IF EXISTS sc_trx.transaction_hd CASCADE;


/* ============================================================
   2. CREATE TABLE
   ============================================================ */

CREATE TABLE sc_trx.transaction_hd
(
    id BIGSERIAL NOT NULL,

    /* DOCUMENT */
    docno VARCHAR(20) NOT NULL,
    doctype VARCHAR(20) NOT NULL,

    journal_type CHAR(6) NOT NULL,
    type_in_out CHAR(3) NOT NULL,

    docdate DATE NOT NULL,

    /* BRANCH */
    idbranch CHAR(20) NOT NULL DEFAULT '',
    cabang CHAR(30) NOT NULL DEFAULT '',

    /* REFERENCE */
    ref_docno VARCHAR(50) NOT NULL DEFAULT '',
    ref_doctype VARCHAR(20) NOT NULL DEFAULT '',

    /* ROUTING SNAPSHOT
       Diambil dari transaction_dt / journal_type.
       Tidak menjadi source of truth.
    */
    module VARCHAR(30) NOT NULL DEFAULT '',
    direction CHAR(5) NOT NULL DEFAULT '',
    stock_effect CHAR(5) NOT NULL DEFAULT 'NONE',
    accounting_effect CHAR(3) NOT NULL DEFAULT 'NO',
    asset_effect CHAR(5) NOT NULL DEFAULT 'NONE',

    /* ========================================================
       SUMMARY QUANTITY
       ======================================================== */

    total_qty NUMERIC(18,6) NOT NULL DEFAULT 0,
    total_qty_in NUMERIC(18,6) NOT NULL DEFAULT 0,
    total_qty_out NUMERIC(18,6) NOT NULL DEFAULT 0,

    /* ========================================================
       SUMMARY VALUE
       ======================================================== */

    total_bruto NUMERIC(18,2) NOT NULL DEFAULT 0,
    total_discount NUMERIC(18,2) NOT NULL DEFAULT 0,
    total_nilai NUMERIC(18,2) NOT NULL DEFAULT 0,
    total_dpp NUMERIC(18,2) NOT NULL DEFAULT 0,
    total_pajak NUMERIC(18,2) NOT NULL DEFAULT 0,
    total_total NUMERIC(18,2) NOT NULL DEFAULT 0,

    /* ========================================================
       SUMMARY ACCOUNTING

       Untuk manual accounting:
       transaction_dt.debet / kredit adalah summary dari
       Perkiraan Asal berdasarkan debet_kredit.

       Jurnal lengkap tetap dibentuk di jurnal_hd/jurnal_dt.
       ======================================================== */

    total_debet NUMERIC(18,2) NOT NULL DEFAULT 0,
    total_kredit NUMERIC(18,2) NOT NULL DEFAULT 0,
    balance NUMERIC(18,2) NOT NULL DEFAULT 0,

    /* INFORMATION */
    total_line BIGINT NOT NULL DEFAULT 0,

    /* AUDIT */
    createdby VARCHAR(50),
    createddate TIMESTAMP WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updatedby VARCHAR(50),
    updateddate TIMESTAMP WITHOUT TIME ZONE,

    CONSTRAINT pk_transaction_hd
        PRIMARY KEY (id),

    /* IN / OUT */
    CONSTRAINT chk_transaction_hd_type_in_out
        CHECK (type_in_out IN ('IN','OUT')),

    /* SUMMARY NON-NEGATIVE */
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

    /* ROUTING */
    CONSTRAINT chk_transaction_hd_stock_effect
        CHECK (
            stock_effect IN ('IN','OUT','INOUT','NONE')
        ),

    CONSTRAINT chk_transaction_hd_accounting_effect
        CHECK (
            accounting_effect IN ('YES','NO')
        ),

    CONSTRAINT chk_transaction_hd_asset_effect
        CHECK (
            asset_effect IN ('IN','OUT','INOUT','NONE')
        ),

    CONSTRAINT chk_transaction_hd_direction
        CHECK (
            direction IN ('IN','OUT','INOUT','')
        ),

    /* DOCUMENT */
    CONSTRAINT chk_transaction_hd_docno_not_blank
        CHECK (BTRIM(docno) <> ''),

    CONSTRAINT chk_transaction_hd_doctype_not_blank
        CHECK (BTRIM(doctype) <> '')
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
   4. FOREIGN KEY JOURNAL TYPE
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
   9. INDEX EFFECT
   ============================================================ */

CREATE INDEX idx_transaction_hd_effect
ON sc_trx.transaction_hd
(
    accounting_effect,
    stock_effect,
    asset_effect
);


/* ============================================================
   10. INDEX MODULE + DATE
   ============================================================ */

CREATE INDEX idx_transaction_hd_module_date
ON sc_trx.transaction_hd
(
    module,
    docdate
);


/* ============================================================
   11. REFERENSI GROUPING TAHAP 17

   HEADER dibentuk dari transaction_dt dengan GROUP BY:

       docno
       doctype
       journal_type
       type_in_out
       idbranch
       ref_docno
       ref_doctype

   Contoh PURCHASE:

       DOC-001 | PURCHASE | GRNREC | IN | B01 | QTY 100
       DOC-001 | PURCHASE | GRNREC | IN | B01 | QTY 200
       DOC-001 | PURCHASE | GRNREC | IN | B01 | QTY  50

   Header:

       DOC-001 | PURCHASE | GRNREC | IN | B01
       total_qty = 350


   Contoh TRANSFER MULTIBRANCH:

       TRF-001 | TRANSFER | BRNTRF | OUT | B01 | QTY 100
       TRF-001 | TRANSFER | BRNTRF | IN  | B02 | QTY 100

   Header:

       TRF-001 | TRANSFER | BRNTRF | OUT | B01
       TRF-001 | TRANSFER | BRNTRF | IN  | B02

   B01 dan B02 tidak tercampur.
   ============================================================ */


/* ============================================================
   12. REFERENSI MANUAL ACCOUNTING

   TAHAP 04 sudah menyimpan:

       idcoa
       counter_idcoa
       debet_kredit

   Contoh:

       NDKAPD
       idcoa          = 213102
       counter_idcoa  = 511101
       debet_kredit   = D

   transaction_hd tetap hanya menyimpan:

       total_debet
       total_kredit
       balance

   Detail pasangan COA berada di transaction_dt dan nantinya
   dibentuk menjadi jurnal_dt oleh TAHAP 16.
   ============================================================ */


/* ============================================================
   13. EXPECTED ROUTING

   GRNREC:
       stock_effect      = IN
       accounting_effect = YES

   GRNRET:
       stock_effect      = OUT
       accounting_effect = YES

   DELIVR:
       stock_effect      = OUT
       accounting_effect = NO

   DELRET:
       stock_effect      = IN
       accounting_effect = NO

   SALESX:
       stock_effect      = NONE
       accounting_effect = YES

   SALRET:
       stock_effect      = NONE
       accounting_effect = YES

   Manual accounting:
       JVGENL
       UMTITP
       NDKAPD
       NDKAPK
       NDKARD
       NDKARK
       GIROIN
       GIROUT
       FXREAL
       FXUNRL
       ARWOFF
       APWOFF
       BADPRV
       BADREV
       UNEARN
       UNEREL
       PAYROL

       stock_effect      = NONE
       accounting_effect = YES
       asset_effect      = NONE
   ============================================================ */


/* ============================================================
   14. CHECK STRUCTURE
   ============================================================ */

SELECT
    ordinal_position,
    column_name,
    data_type,
    character_maximum_length,
    numeric_precision,
    numeric_scale
FROM information_schema.columns
WHERE table_schema = 'sc_trx'
  AND table_name = 'transaction_hd'
ORDER BY ordinal_position;


/* ============================================================
   15. CHECK MASTER RELATION
   ============================================================ */

SELECT
    h.journal_type,
    COUNT(*) AS total_header,
    MAX(h.module) AS module,
    MAX(h.stock_effect) AS stock_effect,
    MAX(h.accounting_effect) AS accounting_effect,
    MAX(h.asset_effect) AS asset_effect
FROM sc_trx.transaction_hd h
LEFT JOIN sc_mst.journal_type jt
       ON jt.journal_type = h.journal_type
GROUP BY h.journal_type
ORDER BY h.journal_type;


/* ============================================================
   TAHAP 05 SELESAI

   NEXT:
       TAHAP 06 = stkblc
       TAHAP 07 = stkblc_avgcost
       TAHAP 08 = assetblc
       TAHAP 09 = jurnal_hd
       TAHAP 10 = jurnal_dt
       TAHAP 11-16 = reverse / post
       TAHAP 17 = recalculate transaction_hd
       TAHAP 18 = trigger transaction_dt
   ============================================================ */
