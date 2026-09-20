/* ============================================================
   JSYS ERP
   TAHAP 04 - TRANSACTION_DT FINAL / HARDENED
   ============================================================

   TUJUAN:
   1. transaction_dt = sumber transaksi utama.
   2. Routing transaksi ditentukan dari sc_mst.journal_type.
   3. Posting point diperbaiki agar tidak double posting:
        PURCHASE:
          PURCHS / PURRET = dokumen order/administratif
          GRNREC / GRNRET = titik stock + accounting

        SALES:
          DELIVR / DELRET = titik stock fisik
          SALESX / SALRET = titik accounting

   4. JSA:
        tidak masuk stock walaupun journal_type mempunyai
        stock_effect IN/OUT.
        JSA tetap masuk accounting sebagai JASA.

   5. BRG:
        menggunakan mbarang.ppersediaan untuk akun persediaan.

   6. Pajak:
        transaction_dt.idtax mengambil master barang terlebih dahulu,
        kemudian konfigurasi umum, lalu NON.
        COA pajak diambil dari sc_mst.tax_dtl.
        Tidak ada hard-code 116106 / 214116 / 214113 di trigger.

   7. transaction_dt menyimpan snapshot routing:
        module
        direction
        stock_effect
        accounting_effect
        asset_effect

      sehingga posting tahap berikutnya membaca routing transaksi,
      bukan menebak ulang dari dokumen.

   8. transaction_dt.idcoa = COA utama / primary COA.
      Untuk transaksi manual accounting:
         idcoa          = perkiraan asal
         counter_idcoa  = perkiraan lawan
         debet_kredit   = arah perkiraan asal (D/K)

      Satu baris transaction_dt tidak dipakai untuk menampung
      seluruh COA jurnal. Multi-account journal dibuat pada
      tahap posting berikutnya.

   9. TIDAK menghapus transaction_dt saat script dijalankan ulang.
      Data existing dipertahankan.

   10. SCRIPT RERUN-SAFE:
      - function menggunakan CREATE OR REPLACE
      - trigger menggunakan CREATE OR REPLACE
      - legacy trigger dibuang hanya jika memang ada
      - table/index existing tidak dibuang

   DEPENDENCY:
      TAHAP 01 : fn_stock_uniqueid / fn_stock_key
      TAHAP 02 : sc_mst.journal_type
      sc_mst.coa
      sc_mst.mbarang
      sc_mst.currency
      sc_mst.konfigurasi_umum
      sc_mst.tax_mst
      sc_mst.tax_dtl

   ============================================================ */


/* ============================================================
   0. NORMALISASI POSTING POINT DARI TAHAP 02
   ============================================================

   Tujuan utama:
      mencegah satu dokumen operasional melakukan posting
      stock/accounting dua kali.

   PURCHASE
      PURCHS / PURRET
          = tidak stock, tidak accounting

      GRNREC / GRNRET
          = titik penerimaan / retur
          = stock + accounting

   SALES
      DELIVR / DELRET
          = stock fisik
          = tidak accounting

      SALESX / SALRET
          = accounting
          = tidak stock ledger

   Catatan:
      HPP + Persediaan pada SALESX tetap dibuat pada
      accounting journal melalui COST, bukan movement stkblc.
   ============================================================ */

UPDATE sc_mst.journal_type
SET
    stock_effect     = 'NONE',
    accounting_effect = 'NO'
WHERE journal_type IN ('PURCHS', 'PURRET');

UPDATE sc_mst.journal_type
SET
    stock_effect     = 'IN',
    accounting_effect = 'YES'
WHERE journal_type = 'GRNREC';

UPDATE sc_mst.journal_type
SET
    stock_effect     = 'OUT',
    accounting_effect = 'YES'
WHERE journal_type = 'GRNRET';

UPDATE sc_mst.journal_type
SET
    stock_effect     = 'OUT',
    accounting_effect = 'NO'
WHERE journal_type = 'DELIVR';

UPDATE sc_mst.journal_type
SET
    stock_effect     = 'IN',
    accounting_effect = 'NO'
WHERE journal_type = 'DELRET';

UPDATE sc_mst.journal_type
SET
    stock_effect     = 'NONE',
    accounting_effect = 'YES'
WHERE journal_type = 'SALESX';

UPDATE sc_mst.journal_type
SET
    stock_effect     = 'NONE',
    accounting_effect = 'YES'
WHERE journal_type = 'SALRET';


/* ============================================================
   1. CREATE / EXTEND TRANSACTION_DT
   ============================================================ */

DO $$
BEGIN
    IF to_regclass('sc_trx.transaction_dt') IS NULL THEN
        EXECUTE $sql$
CREATE TABLE sc_trx.transaction_dt
(
    id BIGSERIAL NOT NULL,

    uniqueid TEXT NOT NULL,
    source_uniqueid TEXT DEFAULT '',

    docno VARCHAR(20) NOT NULL,
    doctype VARCHAR(20) NOT NULL,
    journal_type CHAR(6) NOT NULL,
    line_no INTEGER NOT NULL DEFAULT 1,
    docdate DATE NOT NULL,

    idbranch CHAR(20) DEFAULT '',
    cabang CHAR(30) DEFAULT '',

    type_in_out CHAR(3) NOT NULL,

    ref_docno VARCHAR(50) DEFAULT '',
    ref_doctype VARCHAR(20) DEFAULT '',

    source_table VARCHAR(100) DEFAULT '',
    source_id BIGINT,
    source_line_id BIGINT,

    kdcustomer CHAR(30) DEFAULT '',
    ncustomer VARCHAR(250) DEFAULT '',
    kdsupplier CHAR(30) DEFAULT '',
    nsupplier VARCHAR(250) DEFAULT '',

    idbarang CHAR(20) DEFAULT '',
    namabarang VARCHAR(250) DEFAULT '',
    idunit VARCHAR(10) DEFAULT '',

    idarea CHAR(20) DEFAULT '',
    warehouse VARCHAR(50) DEFAULT '',
    bin VARCHAR(50) DEFAULT '',

    batch CHAR(100) DEFAULT '',
    lotno VARCHAR(100) DEFAULT '',

    qty NUMERIC(18,6) DEFAULT 0,
    harga NUMERIC(18,6) DEFAULT 0,
    bruto NUMERIC(18,2) DEFAULT 0,
    discount NUMERIC(18,2) DEFAULT 0,
    nilai NUMERIC(18,2) DEFAULT 0,
    dpp NUMERIC(18,2) DEFAULT 0,
    pajak NUMERIC(18,2) DEFAULT 0,
    total NUMERIC(18,2) DEFAULT 0,

    idtax CHAR(20) DEFAULT 'NON',
    isinclusive CHAR(6) DEFAULT 'NO',

    currcode CHAR(3) DEFAULT 'IDR',
    kurs NUMERIC(18,6) DEFAULT 1,

    /* PRIMARY ACCOUNT / PRIMARY COA */
    idcoa VARCHAR(20) DEFAULT '',

    /* MANUAL ACCOUNTING */
    counter_idcoa VARCHAR(20) DEFAULT '',
    debet_kredit CHAR(1) DEFAULT 'D',

    debet NUMERIC(18,2) DEFAULT 0,
    kredit NUMERIC(18,2) DEFAULT 0,

    /* SNAPSHOT ROUTING */
    module VARCHAR(30) DEFAULT '',
    direction CHAR(5) DEFAULT '',
    stock_effect CHAR(5) DEFAULT 'NONE',
    accounting_effect CHAR(3) DEFAULT 'NO',
    asset_effect CHAR(5) DEFAULT 'NONE',

    /* STOCK IDENTITY */
    stock_uniqueid TEXT,
    stock_key CHAR(32),

    keterangan TEXT,

    createdby VARCHAR(50),
    createddate TIMESTAMP WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updatedby VARCHAR(50),
    updateddate TIMESTAMP WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT pk_transaction_dt
        PRIMARY KEY (id),

    CONSTRAINT uq_transaction_dt_uniqueid
        UNIQUE (uniqueid),

    CONSTRAINT fk_transaction_dt_journal_type
        FOREIGN KEY (journal_type)
        REFERENCES sc_mst.journal_type(journal_type),

    CONSTRAINT ck_transaction_dt_type_in_out
        CHECK (type_in_out IN ('IN','OUT')),

    CONSTRAINT ck_transaction_dt_uniqueid_not_blank
        CHECK (BTRIM(uniqueid) <> ''),

    CONSTRAINT ck_transaction_dt_docno_not_blank
        CHECK (BTRIM(docno) <> ''),

    CONSTRAINT ck_transaction_dt_qty
        CHECK (qty >= 0),

    CONSTRAINT ck_transaction_dt_kurs
        CHECK (kurs > 0),

    CONSTRAINT ck_transaction_dt_line_no
        CHECK (line_no > 0),

    CONSTRAINT ck_transaction_dt_debet_kredit
        CHECK
        (
            debet >= 0
            AND kredit >= 0
            AND NOT
            (
                debet > 0
                AND kredit > 0
            )
        ),

    CONSTRAINT ck_transaction_dt_manual_dc
        CHECK (debet_kredit IN ('D','K')),

    CONSTRAINT ck_transaction_dt_isinclusive
        CHECK (isinclusive IN ('YES','NO')),

    CONSTRAINT ck_transaction_dt_stock_effect
        CHECK (stock_effect IN ('IN','OUT','INOUT','NONE')),

    CONSTRAINT ck_transaction_dt_accounting_effect
        CHECK (accounting_effect IN ('YES','NO')),

    CONSTRAINT ck_transaction_dt_asset_effect
        CHECK (asset_effect IN ('IN','OUT','INOUT','NONE')),

    CONSTRAINT ck_transaction_dt_direction
        CHECK (direction IN ('IN','OUT','INOUT',''))
);
        $sql$;
    END IF;
END;
$$;



/* ============================================================
   1.1 EXTEND TABLE JIKA TAHAP 04 LAMA SUDAH ADA
   ============================================================ */

DO $$
BEGIN
    IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema='sc_trx' AND table_name='transaction_dt' AND column_name='module') THEN
        ALTER TABLE sc_trx.transaction_dt ADD COLUMN module VARCHAR(30) DEFAULT '';
    END IF;

    IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema='sc_trx' AND table_name='transaction_dt' AND column_name='direction') THEN
        ALTER TABLE sc_trx.transaction_dt ADD COLUMN direction CHAR(5) DEFAULT '';
    END IF;

    IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema='sc_trx' AND table_name='transaction_dt' AND column_name='stock_effect') THEN
        ALTER TABLE sc_trx.transaction_dt ADD COLUMN stock_effect CHAR(5) DEFAULT 'NONE';
    END IF;

    IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema='sc_trx' AND table_name='transaction_dt' AND column_name='accounting_effect') THEN
        ALTER TABLE sc_trx.transaction_dt ADD COLUMN accounting_effect CHAR(3) DEFAULT 'NO';
    END IF;

    IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema='sc_trx' AND table_name='transaction_dt' AND column_name='asset_effect') THEN
        ALTER TABLE sc_trx.transaction_dt ADD COLUMN asset_effect CHAR(5) DEFAULT 'NONE';
    END IF;

    IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema='sc_trx' AND table_name='transaction_dt' AND column_name='stock_uniqueid') THEN
        ALTER TABLE sc_trx.transaction_dt ADD COLUMN stock_uniqueid TEXT;
    END IF;

    IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema='sc_trx' AND table_name='transaction_dt' AND column_name='stock_key') THEN
        ALTER TABLE sc_trx.transaction_dt ADD COLUMN stock_key CHAR(32);
    END IF;

    IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema='sc_trx' AND table_name='transaction_dt' AND column_name='counter_idcoa') THEN
        ALTER TABLE sc_trx.transaction_dt ADD COLUMN counter_idcoa VARCHAR(20) DEFAULT '';
    END IF;

    IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema='sc_trx' AND table_name='transaction_dt' AND column_name='debet_kredit') THEN
        ALTER TABLE sc_trx.transaction_dt ADD COLUMN debet_kredit CHAR(1) DEFAULT 'D';
    END IF;
END;
$$;


/* ============================================================
   1.2 SAFE ADD CONSTRAINT UNTUK TABLE LAMA
   ============================================================ */

DO $$
BEGIN

    IF NOT EXISTS
    (
        SELECT 1
        FROM pg_constraint
        WHERE conrelid = 'sc_trx.transaction_dt'::regclass
          AND conname = 'uq_transaction_dt_uniqueid'
    )
    THEN
        ALTER TABLE sc_trx.transaction_dt
            ADD CONSTRAINT uq_transaction_dt_uniqueid
            UNIQUE (uniqueid);
    END IF;


    IF NOT EXISTS
    (
        SELECT 1
        FROM pg_constraint
        WHERE conrelid = 'sc_trx.transaction_dt'::regclass
          AND conname = 'fk_transaction_dt_journal_type'
    )
    THEN
        ALTER TABLE sc_trx.transaction_dt
            ADD CONSTRAINT fk_transaction_dt_journal_type
            FOREIGN KEY (journal_type)
            REFERENCES sc_mst.journal_type(journal_type);
    END IF;


    IF NOT EXISTS
    (
        SELECT 1
        FROM pg_constraint
        WHERE conrelid = 'sc_trx.transaction_dt'::regclass
          AND conname = 'ck_transaction_dt_stock_effect'
    )
    THEN
        ALTER TABLE sc_trx.transaction_dt
            ADD CONSTRAINT ck_transaction_dt_stock_effect
            CHECK (stock_effect IN ('IN','OUT','INOUT','NONE'));
    END IF;


    IF NOT EXISTS
    (
        SELECT 1
        FROM pg_constraint
        WHERE conrelid = 'sc_trx.transaction_dt'::regclass
          AND conname = 'ck_transaction_dt_accounting_effect'
    )
    THEN
        ALTER TABLE sc_trx.transaction_dt
            ADD CONSTRAINT ck_transaction_dt_accounting_effect
            CHECK (accounting_effect IN ('YES','NO'));
    END IF;


    IF NOT EXISTS
    (
        SELECT 1
        FROM pg_constraint
        WHERE conrelid = 'sc_trx.transaction_dt'::regclass
          AND conname = 'ck_transaction_dt_asset_effect'
    )
    THEN
        ALTER TABLE sc_trx.transaction_dt
            ADD CONSTRAINT ck_transaction_dt_asset_effect
            CHECK (asset_effect IN ('IN','OUT','INOUT','NONE'));
    END IF;

    IF NOT EXISTS
    (
        SELECT 1
        FROM pg_constraint
        WHERE conrelid = 'sc_trx.transaction_dt'::regclass
          AND conname = 'ck_transaction_dt_manual_dc'
    )
    THEN
        ALTER TABLE sc_trx.transaction_dt
            ADD CONSTRAINT ck_transaction_dt_manual_dc
            CHECK (debet_kredit IN ('D','K'));
    END IF;

END;
$$;


/* ============================================================
   2. INDEX
   ============================================================ */

DO $$
BEGIN
    IF to_regclass('sc_trx.idx_transaction_dt_source') IS NULL THEN
        CREATE INDEX idx_transaction_dt_source
        ON sc_trx.transaction_dt
        (
            source_table,
            source_id,
            source_line_id
        );
    END IF;
END;
$$;

DO $$
BEGIN
    IF to_regclass('sc_trx.idx_transaction_dt_source_uniqueid') IS NULL THEN
        CREATE INDEX idx_transaction_dt_source_uniqueid
        ON sc_trx.transaction_dt(source_uniqueid);
    END IF;
END;
$$;

DO $$
BEGIN
    IF to_regclass('sc_trx.idx_transaction_dt_doc') IS NULL THEN
        CREATE INDEX idx_transaction_dt_doc
        ON sc_trx.transaction_dt
        (
            docno,
            doctype,
            journal_type
        );
    END IF;
END;
$$;

DO $$
BEGIN
    IF to_regclass('sc_trx.idx_transaction_dt_type') IS NULL THEN
        CREATE INDEX idx_transaction_dt_type
        ON sc_trx.transaction_dt
        (
            journal_type,
            type_in_out
        );
    END IF;
END;
$$;

DO $$
BEGIN
    IF to_regclass('sc_trx.idx_transaction_dt_stock_dimension') IS NULL THEN
        CREATE INDEX idx_transaction_dt_stock_dimension
        ON sc_trx.transaction_dt
        (
            idbranch,
            idarea,
            warehouse,
            bin,
            idbarang,
            idunit,
            batch,
            lotno
        );
    END IF;
END;
$$;

DO $$
BEGIN
    IF to_regclass('sc_trx.idx_transaction_dt_barang_batch_lot') IS NULL THEN
        CREATE INDEX idx_transaction_dt_barang_batch_lot
        ON sc_trx.transaction_dt
        (
            idbarang,
            batch,
            lotno
        );
    END IF;
END;
$$;

DO $$
BEGIN
    IF to_regclass('sc_trx.idx_transaction_dt_docdate') IS NULL THEN
        CREATE INDEX idx_transaction_dt_docdate
        ON sc_trx.transaction_dt(docdate);
    END IF;
END;
$$;

DO $$
BEGIN
    IF to_regclass('sc_trx.idx_transaction_dt_branch_date') IS NULL THEN
        CREATE INDEX idx_transaction_dt_branch_date
        ON sc_trx.transaction_dt
        (
            idbranch,
            docdate
        );
    END IF;
END;
$$;

DO $$
BEGIN
    IF to_regclass('sc_trx.idx_transaction_dt_stock_posting') IS NULL THEN
        CREATE INDEX idx_transaction_dt_stock_posting
        ON sc_trx.transaction_dt
        (
            stock_effect,
            docdate,
            idbranch
        );
    END IF;
END;
$$;

DO $$
BEGIN
    IF to_regclass('sc_trx.idx_transaction_dt_accounting_posting') IS NULL THEN
        CREATE INDEX idx_transaction_dt_accounting_posting
        ON sc_trx.transaction_dt
        (
            accounting_effect,
            docdate,
            idbranch
        );
    END IF;
END;
$$;

DO $$
BEGIN
    IF to_regclass('sc_trx.idx_transaction_dt_asset_posting') IS NULL THEN
        CREATE INDEX idx_transaction_dt_asset_posting
        ON sc_trx.transaction_dt
        (
            asset_effect,
            docdate,
            idbranch
        );
    END IF;
END;
$$;

DO $$
BEGIN
    IF to_regclass('sc_trx.idx_transaction_dt_stock_uniqueid') IS NULL THEN
        CREATE INDEX idx_transaction_dt_stock_uniqueid
        ON sc_trx.transaction_dt(stock_uniqueid);
    END IF;
END;
$$;


/* ============================================================
   3. FUNCTION TAX FLOW
   ============================================================

   PURCHASE:
       GRNREC / GRNRET
           -> pajak input
           -> tax_dtl.prk_masukan

   SALES:
       SALESX / SALRET
           -> pajak output
           -> tax_dtl.prk_keluaran

   RETUR:
       account COA tetap menggunakan akun pajak asal,
       arah DEBET/KREDIT dibalik oleh generator jurnal.
   ============================================================ */

CREATE OR REPLACE FUNCTION sc_trx.fn_get_transaction_tax_accounts
(
    p_idtax       CHAR(20),
    p_journal_type CHAR(6)
)
RETURNS TABLE
(
    idtax             CHAR(20),
    idgrouptax        CHAR(50),
    percentation      NUMERIC(18,2),
    tax_flow          VARCHAR(10),
    idcoa_tax         CHAR(20),
    idcoa_faktur      CHAR(20)
)
LANGUAGE SQL
STABLE
AS $$
    WITH jt AS
    (
        SELECT
            UPPER(TRIM(module)) AS module
        FROM sc_mst.journal_type
        WHERE journal_type = p_journal_type
        LIMIT 1
    )
    SELECT
        d.idtax,
        d.idgrouptax,
        d.percentation,

        CASE
            WHEN jt.module = 'PURCHASE'
                THEN 'INPUT'
            WHEN jt.module = 'SALES'
                THEN 'OUTPUT'
            ELSE
                CASE
                    WHEN UPPER(TRIM(COALESCE(p_journal_type,''))) IN
                         ('TAXINX')
                        THEN 'INPUT'
                    WHEN UPPER(TRIM(COALESCE(p_journal_type,''))) IN
                         ('TAXOUT')
                        THEN 'OUTPUT'
                    ELSE 'NONE'
                END
        END AS tax_flow,

        CASE
            WHEN jt.module = 'PURCHASE'
                THEN NULLIF(TRIM(d.prk_masukan), '')
            WHEN jt.module = 'SALES'
                THEN NULLIF(TRIM(d.prk_keluaran), '')
            WHEN UPPER(TRIM(COALESCE(p_journal_type,''))) = 'TAXINX'
                THEN NULLIF(TRIM(d.prk_masukan), '')
            WHEN UPPER(TRIM(COALESCE(p_journal_type,''))) = 'TAXOUT'
                THEN NULLIF(TRIM(d.prk_keluaran), '')
            ELSE NULL
        END AS idcoa_tax,

        CASE
            WHEN jt.module = 'PURCHASE'
                THEN NULLIF(TRIM(d.prk_fpj_msk), '')
            WHEN jt.module = 'SALES'
                THEN NULLIF(TRIM(d.prk_fpj_klr), '')
            WHEN UPPER(TRIM(COALESCE(p_journal_type,''))) = 'TAXINX'
                THEN NULLIF(TRIM(d.prk_fpj_msk), '')
            WHEN UPPER(TRIM(COALESCE(p_journal_type,''))) = 'TAXOUT'
                THEN NULLIF(TRIM(d.prk_fpj_klr), '')
            ELSE NULL
        END AS idcoa_faktur

    FROM sc_mst.tax_dtl d
    CROSS JOIN jt
    WHERE TRIM(d.idtax) = TRIM(COALESCE(p_idtax, ''))
      AND d.status = 'P'
      AND
      (
          (
              jt.module = 'PURCHASE'
              AND NULLIF(TRIM(d.prk_masukan), '') IS NOT NULL
          )
          OR
          (
              jt.module = 'SALES'
              AND NULLIF(TRIM(d.prk_keluaran), '') IS NOT NULL
          )
          OR
          (
              UPPER(TRIM(COALESCE(p_journal_type,''))) = 'TAXINX'
              AND NULLIF(TRIM(d.prk_masukan), '') IS NOT NULL
          )
          OR
          (
              UPPER(TRIM(COALESCE(p_journal_type,''))) = 'TAXOUT'
              AND NULLIF(TRIM(d.prk_keluaran), '') IS NOT NULL
          )
      )
    ORDER BY d.id;
$$;


/* ============================================================
   4. FUNCTION PRIMARY COA
   ============================================================

   transaction_dt.idcoa = PRIMARY COA.

   IMPORTANT:
   Satu baris transaction_dt tidak cukup untuk seluruh jurnal.
   Contoh SALESX:
       AR
       SALES
       TAX
       COGS
       INVENTORY

   Oleh sebab itu:
       idcoa          = primary/operational COA
       tax_dtl        = COA pajak
       journal_type_coa = seluruh detail jurnal pada tahap accounting

   SUMBER PRIMARY COA:
       mbarang
       currency
       konfigurasi_umum
       tax_dtl untuk module TAX
       source idcoa sebagai fallback

   Tidak membaca sc_mst.journal_type_coa di TAHAP 04.
   ============================================================ */

CREATE OR REPLACE FUNCTION sc_trx.fn_resolve_transaction_coa
(
    p_journal_type  CHAR(6),
    p_idbarang      CHAR(20),
    p_currcode      CHAR(3),
    p_idtax         CHAR(20),
    p_type_in_out   CHAR(3),
    p_current_idcoa VARCHAR(20)
)
RETURNS VARCHAR(20)
LANGUAGE plpgsql
AS $$
DECLARE

    /* --------------------------------------------------------
       JOURNAL TYPE
       -------------------------------------------------------- */
    v_module          TEXT;
    v_direction       TEXT;
    v_stock_effect    TEXT;
    v_accounting      TEXT;
    v_asset_effect    TEXT;


    /* --------------------------------------------------------
       MASTER BARANG
       -------------------------------------------------------- */
    v_idgroup        TEXT;
    v_ppersediaan    TEXT;
    v_psj            TEXT;
    v_pcogs           TEXT;
    v_phpproduksi    TEXT;
    v_pjasa          TEXT;
    v_pwaste         TEXT;
    v_item_idtax     TEXT;


    /* --------------------------------------------------------
       CURRENCY - PURCHASE
       -------------------------------------------------------- */
    v_phutang        TEXT;
    v_pum            TEXT;
    v_pbonus         TEXT;
    v_hutangac       TEXT;
    v_hutangbiaya1   TEXT;
    v_hutangbiaya2   TEXT;


    /* --------------------------------------------------------
       CURRENCY - SALES
       -------------------------------------------------------- */
    v_ppiutang       TEXT;
    v_pumjual        TEXT;
    v_ppendapatan    TEXT;
    v_pretur         TEXT;
    v_pdisc          TEXT;
    v_pbonusjual     TEXT;
    v_ptunai         TEXT;
    v_piutangac      TEXT;
    v_pendapatanac   TEXT;
    v_pps            TEXT;


    /* --------------------------------------------------------
       KONFIGURASI UMUM
       -------------------------------------------------------- */
    v_cfg JSONB;

    v_cfg_hpp           TEXT;
    v_cfg_labakurs      TEXT;
    v_cfg_rugikurs      TEXT;
    v_cfg_ldtb          TEXT;
    v_cfg_ldtl          TEXT;
    v_cfg_pproduksi     TEXT;
    v_cfg_ppersediaan   TEXT;
    v_cfg_psj           TEXT;
    v_cfg_pselisih      TEXT;
    v_cfg_pkas          TEXT;
    v_cfg_kaskecil      TEXT;
    v_cfg_idtax         TEXT;
    v_cfg_ispajak       TEXT;
    v_cfg_currcode      TEXT;
    v_cfg_gudang        TEXT;
    v_cfg_pjasa         TEXT;
    v_cfg_pwaste        TEXT;
    v_cfg_ppendapatan   TEXT;
    v_cfg_pretur        TEXT;


    /* --------------------------------------------------------
       HASIL
       -------------------------------------------------------- */
    v_result_coa TEXT;
    v_tax_coa    TEXT;


BEGIN

    /* --------------------------------------------------------
       1. LOAD JOURNAL TYPE
       -------------------------------------------------------- */

    SELECT
        TRIM(jt.module),
        TRIM(jt.direction),
        TRIM(jt.stock_effect),
        TRIM(jt.accounting_effect),
        TRIM(jt.asset_effect)
    INTO
        v_module,
        v_direction,
        v_stock_effect,
        v_accounting,
        v_asset_effect
    FROM sc_mst.journal_type jt
    WHERE jt.journal_type = p_journal_type
    LIMIT 1;


    IF NOT FOUND THEN
        RAISE EXCEPTION
            'Journal type % tidak ditemukan',
            TRIM(p_journal_type);
    END IF;


    /*
    -----------------------------------------------------------------------
    MANUAL ACCOUNTING
    -----------------------------------------------------------------------
    COA utama berasal dari input transaction_dt.idcoa.
    Resolver tidak menggantinya dengan COA operational master.
    -----------------------------------------------------------------------
    */
    IF TRIM(p_journal_type) IN
    (
        'JVGENL',
        'UMTITP',
        'NDKAPD',
        'NDKAPK',
        'NDKARD',
        'NDKARK',
        'GIROIN',
        'GIROUT',
        'FXREAL',
        'FXUNRL',
        'ARWOFF',
        'APWOFF',
        'BADPRV',
        'BADREV',
        'UNEARN',
        'UNEREL',
        'PAYROL'
    )
    THEN

        v_result_coa :=
            NULLIF(BTRIM(COALESCE(p_current_idcoa,'')), '');

        IF v_result_coa IS NOT NULL
           AND NOT EXISTS
           (
               SELECT 1
               FROM sc_mst.coa c
               WHERE BTRIM(c.idcoa::TEXT)
                     = BTRIM(v_result_coa)
           )
        THEN
            RAISE EXCEPTION
                'COA input % untuk journal_type % tidak ditemukan di sc_mst.coa',
                v_result_coa,
                TRIM(p_journal_type);
        END IF;

        RETURN v_result_coa;

    END IF;


    /* --------------------------------------------------------
       2. LOAD MASTER BARANG
       -------------------------------------------------------- */

    IF NULLIF(BTRIM(COALESCE(p_idbarang, '')), '') IS NOT NULL
    THEN

        SELECT
            NULLIF(BTRIM(to_jsonb(b)->>'idgroup'), ''),
            NULLIF(BTRIM(to_jsonb(b)->>'ppersediaan'), ''),
            NULLIF(BTRIM(to_jsonb(b)->>'psj'), ''),
            NULLIF(BTRIM(to_jsonb(b)->>'pcogs'), ''),
            NULLIF(BTRIM(to_jsonb(b)->>'phpproduksi'), ''),
            NULLIF(BTRIM(to_jsonb(b)->>'pjasa'), ''),
            NULLIF(BTRIM(to_jsonb(b)->>'pwaste'), ''),
            NULLIF(BTRIM(to_jsonb(b)->>'idtax'), '')
        INTO
            v_idgroup,
            v_ppersediaan,
            v_psj,
            v_pcogs,
            v_phpproduksi,
            v_pjasa,
            v_pwaste,
            v_item_idtax
        FROM sc_mst.mbarang b
        WHERE BTRIM(to_jsonb(b)->>'idbarang')
            = BTRIM(COALESCE(p_idbarang, '')::TEXT)
        LIMIT 1;

    END IF;


    /* --------------------------------------------------------
       3. LOAD CURRENCY
       -------------------------------------------------------- */

    SELECT
        NULLIF(BTRIM(c.phutang::TEXT), ''),
        NULLIF(BTRIM(c.pum::TEXT), ''),
        NULLIF(BTRIM(c.pbonus::TEXT), ''),
        NULLIF(BTRIM(c.hutangac::TEXT), ''),
        NULLIF(BTRIM(c.hutangbiaya1::TEXT), ''),
        NULLIF(BTRIM(c.hutangbiaya2::TEXT), ''),

        NULLIF(BTRIM(c.ppiutang::TEXT), ''),
        NULLIF(BTRIM(c.pumjual::TEXT), ''),
        NULLIF(BTRIM(c.ppendapatan::TEXT), ''),
        NULLIF(BTRIM(c.pretur::TEXT), ''),
        NULLIF(BTRIM(c.pdisc::TEXT), ''),
        NULLIF(BTRIM(c.pbonusjual::TEXT), ''),
        NULLIF(BTRIM(c.ptunai::TEXT), ''),
        NULLIF(BTRIM(c.piutangac::TEXT), ''),
        NULLIF(BTRIM(c.pendapatanac::TEXT), ''),
        NULLIF(BTRIM(c.pps::TEXT), '')
    INTO
        v_phutang,
        v_pum,
        v_pbonus,
        v_hutangac,
        v_hutangbiaya1,
        v_hutangbiaya2,

        v_ppiutang,
        v_pumjual,
        v_ppendapatan,
        v_pretur,
        v_pdisc,
        v_pbonusjual,
        v_ptunai,
        v_piutangac,
        v_pendapatanac,
        v_pps
    FROM sc_mst.currency c
    WHERE BTRIM(c.currcode::TEXT)
        = BTRIM(COALESCE(p_currcode, 'IDR')::TEXT)
    LIMIT 1;


    /* --------------------------------------------------------
       4. LOAD KONFIGURASI UMUM
       -------------------------------------------------------- */

    SELECT to_jsonb(k)
    INTO v_cfg
    FROM sc_mst.konfigurasi_umum k
    LIMIT 1;


    v_cfg_hpp :=
        NULLIF(BTRIM(COALESCE(v_cfg->>'hpp', '')), '');

    v_cfg_labakurs :=
        NULLIF(BTRIM(COALESCE(v_cfg->>'labakurs', '')), '');

    v_cfg_rugikurs :=
        NULLIF(BTRIM(COALESCE(v_cfg->>'rugikurs', '')), '');

    v_cfg_ldtb :=
        NULLIF(BTRIM(COALESCE(v_cfg->>'ldtb', '')), '');

    v_cfg_ldtl :=
        NULLIF(BTRIM(COALESCE(v_cfg->>'ldtl', '')), '');

    v_cfg_pproduksi :=
        NULLIF(BTRIM(COALESCE(v_cfg->>'pproduksi', '')), '');

    v_cfg_ppersediaan :=
        NULLIF(BTRIM(COALESCE(v_cfg->>'ppersediaan', '')), '');

    v_cfg_psj :=
        NULLIF(BTRIM(COALESCE(v_cfg->>'psj', '')), '');

    v_cfg_pselisih :=
        NULLIF(BTRIM(COALESCE(v_cfg->>'pselisih', '')), '');

    v_cfg_pkas :=
        NULLIF(BTRIM(COALESCE(v_cfg->>'pkas', '')), '');

    v_cfg_kaskecil :=
        NULLIF(BTRIM(COALESCE(v_cfg->>'kaskecil', '')), '');

    v_cfg_idtax :=
        NULLIF(BTRIM(COALESCE(v_cfg->>'idtax', '')), '');

    v_cfg_ispajak :=
        NULLIF(BTRIM(COALESCE(v_cfg->>'ispajak', '')), '');

    v_cfg_currcode :=
        NULLIF(BTRIM(COALESCE(v_cfg->>'currcode', '')), '');

    v_cfg_gudang :=
        NULLIF(BTRIM(COALESCE(v_cfg->>'gudang', '')), '');

    v_cfg_pjasa :=
        NULLIF(BTRIM(COALESCE(v_cfg->>'pjasa', '')), '');

    v_cfg_pwaste :=
        NULLIF(BTRIM(COALESCE(v_cfg->>'pwaste', '')), '');

    v_cfg_ppendapatan :=
        NULLIF(BTRIM(COALESCE(v_cfg->>'ppendapatan', '')), '');

    v_cfg_pretur :=
        NULLIF(BTRIM(COALESCE(v_cfg->>'pretur', '')), '');


    /* --------------------------------------------------------
       5. PRIMARY COA BERDASARKAN MODULE / JOURNAL TYPE
       -------------------------------------------------------- */

    v_result_coa := NULL;


    /* PURCHASE */

    IF UPPER(COALESCE(v_module,'')) = 'PURCHASE'
    THEN

        IF UPPER(COALESCE(v_idgroup,'')) = 'JSA'
        THEN

            v_result_coa :=
                COALESCE(
                    v_pjasa,
                    v_cfg_pjasa
                );

        ELSE

            v_result_coa :=
                COALESCE(
                    v_ppersediaan,
                    v_cfg_ppersediaan
                );

        END IF;


    /* SALES */

    ELSIF UPPER(COALESCE(v_module,'')) = 'SALES'
    THEN

        IF UPPER(TRIM(p_journal_type)) = 'SALESX'
        THEN

            v_result_coa :=
                COALESCE(
                    v_ppendapatan,
                    v_cfg_ppendapatan
                );

        ELSIF UPPER(TRIM(p_journal_type)) = 'SALRET'
        THEN

            v_result_coa :=
                COALESCE(
                    v_pretur,
                    v_cfg_pretur
                );

        ELSIF UPPER(TRIM(p_journal_type)) IN
              ('DELIVR','DELRET')
        THEN

            v_result_coa :=
                COALESCE(
                    v_psj,
                    v_cfg_psj
                );

        END IF;


    /* INVENTORY / OPENING */

    ELSIF UPPER(COALESCE(v_module,'')) IN
          ('INVENTORY','OPENING')
    THEN

        v_result_coa :=
            COALESCE(
                v_ppersediaan,
                v_cfg_ppersediaan
            );


    /* MANUFACTURING */

    ELSIF UPPER(COALESCE(v_module,'')) = 'MANUFACTURING'
    THEN

        /*
           SCRAP + PRODUCTION:
           Primary COA mengikuti COA persediaan dari mbarang.idbarang.

           Jadi untuk:
             - SCRAPP
             - SCRREV
             - WASTEX
             - WIPINX
             - WIPOUT
             - WIPMOV
             - transaksi production/manufacturing lainnya

           tidak memakai COA waste / pproduksi sebagai primary COA.
           Jika COA persediaan barang tidak ada, fallback ke
           konfigurasi umum.
        */
        v_result_coa :=
            COALESCE(
                v_ppersediaan,
                v_cfg_ppersediaan
            );


    /* TAX */

    ELSIF UPPER(COALESCE(v_module,'')) = 'TAX'
    THEN

        SELECT x.idcoa_tax::TEXT
        INTO v_tax_coa
        FROM sc_trx.fn_get_transaction_tax_accounts
        (
            COALESCE(NULLIF(BTRIM(p_idtax),''), 'NON'),
            p_journal_type
        ) x
        WHERE NULLIF(BTRIM(x.idcoa_tax::TEXT),'') IS NOT NULL
        ORDER BY x.idgrouptax
        LIMIT 1;

        v_result_coa := v_tax_coa;


    /* FINANCE */

    ELSIF UPPER(COALESCE(v_module,'')) = 'FINANCE'
    THEN

        IF UPPER(TRIM(p_journal_type)) IN
           ('CASHIN','CASHOT','CASHAD','PAYMNT','PAYREV','RECVPT','RECREV')
        THEN

            v_result_coa :=
                COALESCE(
                    v_ptunai,
                    v_cfg_pkas,
                    v_cfg_kaskecil
                );

        ELSE

            v_result_coa :=
                NULL;

        END IF;

    END IF;


    /* --------------------------------------------------------
       6. FALLBACK
       -------------------------------------------------------- */

    v_result_coa :=
        COALESCE
        (
            v_result_coa,
            NULLIF(BTRIM(COALESCE(p_current_idcoa,'')), '')
        );


    /* --------------------------------------------------------
       7. VALIDATE AGAINST MASTER COA
       -------------------------------------------------------- */

    IF NULLIF(BTRIM(COALESCE(v_result_coa,'')), '') IS NOT NULL
    THEN

        IF NOT EXISTS
        (
            SELECT 1
            FROM sc_mst.coa c
            WHERE BTRIM(c.idcoa::TEXT)
                  = BTRIM(v_result_coa)
        )
        THEN

            RAISE EXCEPTION
                'COA % tidak ditemukan pada sc_mst.coa. journal_type=%, idbarang=%, idtax=%, currcode=%',
                v_result_coa,
                TRIM(p_journal_type),
                COALESCE(TRIM(p_idbarang),''),
                COALESCE(TRIM(p_idtax),''),
                COALESCE(TRIM(p_currcode),'');
        END IF;

    END IF;


    RETURN NULLIF(BTRIM(v_result_coa), '');

END;
$$;


/* ============================================================
   5. FUNCTION PREPARE TRANSACTION_DT
   ============================================================

   SATU trigger menyiapkan:
      - module
      - direction
      - stock_effect effective
      - accounting_effect effective
      - asset_effect
      - idtax
      - primary idcoa
      - stock_uniqueid
      - stock_key

   ============================================================ */

CREATE OR REPLACE FUNCTION sc_trx.fn_prepare_transaction_dt()
RETURNS TRIGGER
LANGUAGE plpgsql
AS $$
DECLARE

    /* JOURNAL */
    v_module              TEXT;
    v_direction           TEXT;
    v_master_stock        TEXT;
    v_master_accounting   TEXT;
    v_master_asset        TEXT;

    v_effective_stock     TEXT;

    /* BARANG */
    v_idgroup             TEXT;
    v_item_idtax          TEXT;

    /* CONFIG */
    v_cfg                 JSONB;
    v_cfg_idtax           TEXT;

    /* TAX */
    v_effective_tax       TEXT;
    v_tax_count           INTEGER;
    v_tax_missing_coa     INTEGER;
    v_tax_invalid_coa     INTEGER;

    /* COA */
    v_coa                 VARCHAR(20);

    /* MANUAL ACCOUNTING */
    v_is_manual           BOOLEAN := FALSE;
    v_tax_manual_flow     TEXT;


BEGIN

    /* ========================================================
       1. VALIDATE JOURNAL TYPE + LOAD ROUTE
       ======================================================== */

    SELECT
        TRIM(jt.module),
        TRIM(jt.direction),
        TRIM(jt.stock_effect),
        TRIM(jt.accounting_effect),
        TRIM(jt.asset_effect)
    INTO
        v_module,
        v_direction,
        v_master_stock,
        v_master_accounting,
        v_master_asset
    FROM sc_mst.journal_type jt
    WHERE jt.journal_type = NEW.journal_type
    LIMIT 1;


    IF NOT FOUND
    THEN
        RAISE EXCEPTION
            'Journal type % tidak ditemukan',
            TRIM(NEW.journal_type);
    END IF;


    /* ========================================================
       MANUAL ACCOUNTING
       ======================================================== */

    v_is_manual :=
        TRIM(NEW.journal_type) IN
        (
            'JVGENL',
            'UMTITP',
            'NDKAPD',
            'NDKAPK',
            'NDKARD',
            'NDKARK',
            'GIROIN',
            'GIROUT',
            'FXREAL',
            'FXUNRL',
            'ARWOFF',
            'APWOFF',
            'BADPRV',
            'BADREV',
            'UNEARN',
            'UNEREL',
            'PAYROL'
        );


    /* ========================================================
       2. VALIDATE TYPE IN / OUT
       ======================================================== */

    IF NOT v_is_manual
    THEN

        IF v_direction = 'IN'
           AND NEW.type_in_out <> 'IN'
        THEN

            RAISE EXCEPTION
                'Journal type % hanya boleh type_in_out IN',
                TRIM(NEW.journal_type);

        ELSIF v_direction = 'OUT'
              AND NEW.type_in_out <> 'OUT'
        THEN

            RAISE EXCEPTION
                'Journal type % hanya boleh type_in_out OUT',
                TRIM(NEW.journal_type);

        END IF;

    END IF;


    /* ========================================================
       3. LOAD MASTER BARANG
       ======================================================== */

    IF NULLIF(BTRIM(COALESCE(NEW.idbarang,'')), '') IS NOT NULL
    THEN

        SELECT
            NULLIF(BTRIM(to_jsonb(b)->>'idgroup'), ''),
            NULLIF(BTRIM(to_jsonb(b)->>'idtax'), '')
        INTO
            v_idgroup,
            v_item_idtax
        FROM sc_mst.mbarang b
        WHERE BTRIM(to_jsonb(b)->>'idbarang')
              = BTRIM(NEW.idbarang::TEXT)
        LIMIT 1;

    END IF;


    /* ========================================================
       4. LOAD KONFIGURASI
       ======================================================== */

    SELECT to_jsonb(k)
    INTO v_cfg
    FROM sc_mst.konfigurasi_umum k
    LIMIT 1;

    v_cfg_idtax :=
        NULLIF(BTRIM(COALESCE(v_cfg->>'idtax','')), '');


    /* ========================================================
       5. EFFECTIVE TAX

       PRIORITY:
          source transaction idtax
             ↓
          mbarang.idtax
             ↓
          konfigurasi_umum.idtax
             ↓
          NON
       ======================================================== */

    IF v_is_manual
    THEN

        v_effective_tax :=
            COALESCE(
                NULLIF(BTRIM(COALESCE(NEW.idtax,'')), ''),
                'NON'
            );

    ELSE

        v_effective_tax :=
            COALESCE
            (
                NULLIF(BTRIM(COALESCE(NEW.idtax,'')), ''),
                v_item_idtax,
                v_cfg_idtax,
                'NON'
            );

    END IF;

    NEW.idtax := BTRIM(v_effective_tax);


    /* ========================================================
       6. VALIDATE TAX MASTER
       ======================================================== */

    IF NOT EXISTS
    (
        SELECT 1
        FROM sc_mst.tax_mst t
        WHERE BTRIM(t.idtax)
              = BTRIM(NEW.idtax)
          AND t.status = 'P'
    )
    THEN

        RAISE EXCEPTION
            'Tax % tidak ditemukan / tidak aktif pada sc_mst.tax_mst',
            TRIM(NEW.idtax);

    END IF;


    /*
       Tax flow khusus manual NDK:
         NDKAPD / NDKAPK -> INPUT
         NDKARD / NDKARK -> OUTPUT

       Untuk JVGENL / UMTITP / GIRO / FX / adjustment generic,
       tax master hanya disimpan di transaction_dt. Pemilihan tax
       account detail dilakukan pada posting accounting setelah
       context jurnal tersedia.
    */
    v_tax_manual_flow :=
        CASE
            WHEN TRIM(NEW.journal_type) IN ('NDKAPD','NDKAPK')
                THEN 'INPUT'
            WHEN TRIM(NEW.journal_type) IN ('NDKARD','NDKARK')
                THEN 'OUTPUT'
            ELSE 'NONE'
        END;


    /* ========================================================
       7. MANUAL ACCOUNTING ROUTING
       ======================================================== */

    IF v_is_manual
    THEN

        NEW.module            := COALESCE(v_module, 'ACCOUNTING');
        NEW.direction         := COALESCE(v_direction, 'INOUT');
        NEW.stock_effect      := 'NONE';
        NEW.accounting_effect := 'YES';
        NEW.asset_effect      := 'NONE';


        /*
           Debit/Kredit pada manual accounting berasal dari input
           dan menentukan arah Perkiraan Asal.
        */

        IF TRIM(NEW.journal_type) IN
           ('NDKAPD','NDKARD','GIROIN')
        THEN
            NEW.debet_kredit := 'D';

        ELSIF TRIM(NEW.journal_type) IN
              ('NDKAPK','NDKARK','GIROUT')
        THEN
            NEW.debet_kredit := 'K';

        ELSIF NEW.debet_kredit NOT IN ('D','K')
        THEN
            NEW.debet_kredit := 'D';
        END IF;


        /*
           type_in_out hanya untuk kompatibilitas schema lama.
           Manual accounting tetap stock NONE.
        */
        IF NEW.debet_kredit = 'D'
        THEN
            NEW.type_in_out := 'IN';
        ELSE
            NEW.type_in_out := 'OUT';
        END IF;


        IF NULLIF(BTRIM(COALESCE(NEW.idcoa,'')), '') IS NULL
        THEN
            RAISE EXCEPTION
                'Perkiraan Asal (idcoa) wajib diisi untuk journal_type %',
                TRIM(NEW.journal_type);
        END IF;


        IF NULLIF(BTRIM(COALESCE(NEW.counter_idcoa,'')), '') IS NULL
        THEN
            RAISE EXCEPTION
                'Perkiraan Lawan (counter_idcoa) wajib diisi untuk journal_type %',
                TRIM(NEW.journal_type);
        END IF;


        IF NOT EXISTS
        (
            SELECT 1
            FROM sc_mst.coa c
            WHERE BTRIM(c.idcoa::TEXT)
                  = BTRIM(NEW.idcoa::TEXT)
        )
        THEN
            RAISE EXCEPTION
                'COA Perkiraan Asal % tidak ditemukan pada sc_mst.coa',
                TRIM(NEW.idcoa);
        END IF;


        IF NOT EXISTS
        (
            SELECT 1
            FROM sc_mst.coa c
            WHERE BTRIM(c.idcoa::TEXT)
                  = BTRIM(NEW.counter_idcoa::TEXT)
        )
        THEN
            RAISE EXCEPTION
                'COA Perkiraan Lawan % tidak ditemukan pada sc_mst.coa',
                TRIM(NEW.counter_idcoa);
        END IF;


        /* Summary side of the origin account. */
        IF COALESCE(NEW.total,0) > 0
        THEN
            IF NEW.debet_kredit = 'D'
            THEN
                NEW.debet  := NEW.total;
                NEW.kredit := 0;
            ELSE
                NEW.debet  := 0;
                NEW.kredit := NEW.total;
            END IF;
        END IF;

    END IF;


    /* ========================================================
       8. EFFECTIVE ACCOUNTING POSTING POINT

       Dokumen berikut bukan titik accounting:
          PURCHS
          PURRET
          DELIVR
          DELRET

       Titik accounting:
          GRNREC
          GRNRET
          SALESX
          SALRET

       Ini menjaga agar jurnal tidak double.
       ======================================================== */

    IF NOT v_is_manual
    THEN

        IF TRIM(NEW.journal_type) IN
           ('PURCHS','PURRET','DELIVR','DELRET')
        THEN

            NEW.accounting_effect := 'NO';

        ELSE

            NEW.accounting_effect := v_master_accounting;

        END IF;

    END IF;


    /* ========================================================
       9. EFFECTIVE STOCK POSTING

       RULE JASA:
           JSA tidak masuk stock.

       RULE POSTING POINT:
           PURCHS/PURRET = NONE
           SALESX/SALRET = NONE
           DELIVR         = OUT
           DELRET         = IN

       Untuk journal type lain:
           gunakan stock_effect dari master.
       ======================================================== */

    IF NOT v_is_manual
    THEN

        v_effective_stock := v_master_stock;


        IF TRIM(NEW.journal_type) IN
           ('PURCHS','PURRET','SALESX','SALRET')
    THEN

        v_effective_stock := 'NONE';

    ELSIF TRIM(NEW.journal_type) = 'DELIVR'
    THEN

        v_effective_stock := 'OUT';

    ELSIF TRIM(NEW.journal_type) = 'DELRET'
    THEN

        v_effective_stock := 'IN';

    END IF;


        /* JASA TIDAK PERNAH masuk stock */

        IF UPPER(COALESCE(v_idgroup,'')) = 'JSA'
           AND v_effective_stock <> 'NONE'
        THEN

            v_effective_stock := 'NONE';

        END IF;


        NEW.module       := COALESCE(v_module, '');
        NEW.direction    := COALESCE(v_direction, '');
        NEW.stock_effect := COALESCE(v_effective_stock, 'NONE');
        NEW.asset_effect := COALESCE(v_master_asset, 'NONE');

    ELSE

        /* Manual accounting route is fixed. */
        NEW.module       := COALESCE(v_module, 'ACCOUNTING');
        NEW.direction    := COALESCE(v_direction, 'INOUT');
        NEW.stock_effect := 'NONE';
        NEW.accounting_effect := 'YES';
        NEW.asset_effect := 'NONE';

    END IF;


    /* ========================================================
       10. STOCK IDENTITY

       Hanya dibentuk jika transaksi benar-benar menuju stock.
       ======================================================== */

    IF NEW.stock_effect <> 'NONE'
       AND NULLIF(BTRIM(COALESCE(NEW.idbarang,'')), '') IS NOT NULL
    THEN

        NEW.stock_uniqueid :=
            sc_trx.fn_stock_uniqueid
            (
                NEW.idbranch::TEXT,
                NEW.warehouse::TEXT,
                NEW.bin::TEXT,
                NEW.idbarang::TEXT,
                NEW.idunit::TEXT,
                NEW.batch::TEXT,
                NEW.lotno::TEXT
            );

        NEW.stock_key :=
            sc_trx.fn_stock_key
            (
                NEW.idbranch::TEXT,
                NEW.warehouse::TEXT,
                NEW.bin::TEXT,
                NEW.idbarang::TEXT,
                NEW.idunit::TEXT,
                NEW.batch::TEXT,
                NEW.lotno::TEXT
            );

    ELSE

        NEW.stock_uniqueid := NULL;
        NEW.stock_key      := NULL;

    END IF;


    /* ========================================================
       NORMALIZE SEBELUM RESOLVE COA
       ======================================================== */

    IF NULLIF(BTRIM(COALESCE(NEW.isinclusive,'')), '') IS NULL
    THEN
        NEW.isinclusive := 'NO';
    END IF;

    IF NULLIF(BTRIM(COALESCE(NEW.currcode,'')), '') IS NULL
    THEN
        NEW.currcode := 'IDR';
    END IF;

    IF NEW.kurs IS NULL
    THEN
        NEW.kurs := 1;
    END IF;

    NEW.keterangan := COALESCE(NEW.keterangan, '');


    /* ========================================================
       10. PRIMARY COA

       JSA:
           pjasa

       BRG:
           ppersediaan

       SALESX:
           ppendapatan

       SALRET:
           pretur

       DELIVR / DELRET:
           psj

       tax:
           tax_dtl

       fallback:
           source idcoa
       ======================================================== */

    IF NOT v_is_manual
    THEN

        v_coa :=
            sc_trx.fn_resolve_transaction_coa
            (
                NEW.journal_type,
                NEW.idbarang,
                NEW.currcode,
                NEW.idtax,
                NEW.type_in_out,
                NEW.idcoa
            );


        IF NULLIF(BTRIM(COALESCE(v_coa,'')), '') IS NOT NULL
        THEN
            NEW.idcoa := BTRIM(v_coa);
        END IF;

    END IF;


    /* ========================================================
       10.1 VALIDATE SNAPSHOT ROUTING
       ======================================================== */

    IF NULLIF(BTRIM(COALESCE(NEW.module,'')), '') IS NULL
       OR NULLIF(BTRIM(COALESCE(NEW.direction,'')), '') IS NULL
    THEN
        RAISE EXCEPTION
            'Routing transaction % tidak lengkap: module/direction kosong',
            TRIM(NEW.uniqueid);
    END IF;


    /* ========================================================
       11. VALIDATE ACCOUNTING TRANSACTION

       Untuk transaction yang memang mempunyai accounting
       effect, primary COA wajib tersedia untuk transaksi
       operasional.

       Manual/general journal boleh menggunakan source idcoa
       dan akan divalidasi lagi pada jurnal detail.
       ======================================================== */

    IF NEW.accounting_effect = 'YES'
       AND UPPER(COALESCE(NEW.module,'')) IN
           ('PURCHASE','SALES','INVENTORY','MANUFACTURING','TAX')
       AND NULLIF(BTRIM(COALESCE(NEW.idcoa,'')), '') IS NULL
    THEN

        RAISE EXCEPTION
            'Primary COA wajib ada untuk journal_type %, idbarang %, idtax %',
            TRIM(NEW.journal_type),
            COALESCE(TRIM(NEW.idbarang),''),
            COALESCE(TRIM(NEW.idtax),'');
    END IF;


    /* ========================================================
       12. VALIDATE TAX DETAIL COA
       ========================================================

       PURCHASE:
           prk_masukan

       SALES:
           prk_keluaran

       Manual NDK:
           NDKAP* -> prk_masukan
           NDKAR* -> prk_keluaran

       Manual generic selain NDK:
           tax master tetap valid, detail tax account akan digunakan
           pada posting jika context tax sudah ditentukan.
       ======================================================== */

    IF COALESCE(NEW.pajak,0) > 0
       AND NEW.idtax <> 'NON'
    THEN

        IF v_tax_manual_flow = 'INPUT'
           OR UPPER(COALESCE(v_module,'')) = 'PURCHASE'
        THEN

            SELECT COUNT(*)
            INTO v_tax_count
            FROM sc_mst.tax_dtl d
            WHERE TRIM(d.idtax) = TRIM(NEW.idtax)
              AND d.status = 'P'
              AND NULLIF(TRIM(d.prk_masukan),'') IS NOT NULL;


            SELECT COUNT(*)
            INTO v_tax_missing_coa
            FROM sc_mst.tax_dtl d
            WHERE TRIM(d.idtax) = TRIM(NEW.idtax)
              AND d.status = 'P'
              AND NULLIF(TRIM(d.prk_masukan),'') IS NULL;


            SELECT COUNT(*)
            INTO v_tax_invalid_coa
            FROM sc_mst.tax_dtl d
            WHERE TRIM(d.idtax) = TRIM(NEW.idtax)
              AND d.status = 'P'
              AND NULLIF(TRIM(d.prk_masukan),'') IS NOT NULL
              AND NOT EXISTS
              (
                  SELECT 1
                  FROM sc_mst.coa c
                  WHERE BTRIM(c.idcoa::TEXT)
                        = BTRIM(d.prk_masukan::TEXT)
              );


        ELSIF v_tax_manual_flow = 'OUTPUT'
              OR UPPER(COALESCE(v_module,'')) = 'SALES'
        THEN

            SELECT COUNT(*)
            INTO v_tax_count
            FROM sc_mst.tax_dtl d
            WHERE TRIM(d.idtax) = TRIM(NEW.idtax)
              AND d.status = 'P'
              AND NULLIF(TRIM(d.prk_keluaran),'') IS NOT NULL;


            SELECT COUNT(*)
            INTO v_tax_missing_coa
            FROM sc_mst.tax_dtl d
            WHERE TRIM(d.idtax) = TRIM(NEW.idtax)
              AND d.status = 'P'
              AND NULLIF(TRIM(d.prk_keluaran),'') IS NULL;


            SELECT COUNT(*)
            INTO v_tax_invalid_coa
            FROM sc_mst.tax_dtl d
            WHERE TRIM(d.idtax) = TRIM(NEW.idtax)
              AND d.status = 'P'
              AND NULLIF(TRIM(d.prk_keluaran),'') IS NOT NULL
              AND NOT EXISTS
              (
                  SELECT 1
                  FROM sc_mst.coa c
                  WHERE BTRIM(c.idcoa::TEXT)
                        = BTRIM(d.prk_keluaran::TEXT)
              );

        ELSE

            /*
               Generic manual accounting:
               tax master sudah divalidasi.
               Jangan memaksa memilih input/output tax tanpa context.
            */
            v_tax_count       := 1;
            v_tax_missing_coa := 0;
            v_tax_invalid_coa := 0;

        END IF;


        IF v_tax_count = 0
        THEN
            RAISE EXCEPTION
                'Tax % tidak mempunyai COA pajak yang valid untuk journal_type %',
                TRIM(NEW.idtax),
                TRIM(NEW.journal_type);
        END IF;


        IF v_tax_missing_coa > 0
        THEN
            RAISE EXCEPTION
                'Tax % mempunyai detail tanpa COA pajak lengkap untuk journal_type %',
                TRIM(NEW.idtax),
                TRIM(NEW.journal_type);
        END IF;


        IF v_tax_invalid_coa > 0
        THEN
            RAISE EXCEPTION
                'Tax % mempunyai COA pajak yang tidak terdaftar pada sc_mst.coa',
                TRIM(NEW.idtax);
        END IF;

    END IF;


    /* ========================================================
       13. NORMALIZE
       ======================================================== */

    IF NULLIF(BTRIM(COALESCE(NEW.isinclusive,'')), '') IS NULL
    THEN
        NEW.isinclusive := 'NO';
    END IF;

    IF NULLIF(BTRIM(COALESCE(NEW.currcode,'')), '') IS NULL
    THEN
        NEW.currcode := 'IDR';
    END IF;

    IF NEW.kurs IS NULL
    THEN
        NEW.kurs := 1;
    END IF;

    /* Jangan biarkan NULL mengalir ke subledger stock/jurnal. */
    NEW.keterangan := COALESCE(NEW.keterangan, '');

    NEW.updateddate := CURRENT_TIMESTAMP;


    RETURN NEW;

END;
$$;


/* ============================================================
   6. FUNCTION VALIDASI FINAL TRANSACTION TYPE
   ============================================================ */

CREATE OR REPLACE FUNCTION sc_trx.fn_validate_transaction_type()
RETURNS TRIGGER
LANGUAGE plpgsql
AS $$
DECLARE
    v_direction CHAR(5);
BEGIN

    SELECT direction
    INTO v_direction
    FROM sc_mst.journal_type
    WHERE journal_type = NEW.journal_type;


    IF NOT FOUND
    THEN
        RAISE EXCEPTION
            'Journal type % tidak ditemukan',
            TRIM(NEW.journal_type);
    END IF;


    IF v_direction = 'IN'
       AND NEW.type_in_out <> 'IN'
    THEN

        RAISE EXCEPTION
            'Journal type % hanya boleh type_in_out IN',
            TRIM(NEW.journal_type);

    END IF;


    IF v_direction = 'OUT'
       AND NEW.type_in_out <> 'OUT'
    THEN

        RAISE EXCEPTION
            'Journal type % hanya boleh type_in_out OUT',
            TRIM(NEW.journal_type);

    END IF;


    IF NEW.stock_effect NOT IN
       ('IN','OUT','INOUT','NONE')
    THEN

        RAISE EXCEPTION
            'stock_effect transaction % tidak valid',
            TRIM(NEW.uniqueid);

    END IF;


    IF NEW.accounting_effect NOT IN ('YES','NO')
    THEN

        RAISE EXCEPTION
            'accounting_effect transaction % tidak valid',
            TRIM(NEW.uniqueid);

    END IF;


    IF NEW.asset_effect NOT IN
       ('IN','OUT','INOUT','NONE')
    THEN

        RAISE EXCEPTION
            'asset_effect transaction % tidak valid',
            TRIM(NEW.uniqueid);

    END IF;


    RETURN NEW;

END;
$$;


/* ============================================================
   7. TRIGGER ORDER
   ============================================================ */

/* ============================================================
   7. TRIGGER - PG18 / MODERN POSTGRESQL

   PostgreSQL mendukung CREATE OR REPLACE TRIGGER.
   Dengan ini trigger yang sudah ada akan langsung diganti
   definisinya tanpa NOTICE "already exists".

   Legacy trigger yang tidak lagi dipakai dibuang diam-diam.
   ============================================================ */

DO $$
BEGIN
    IF EXISTS
    (
        SELECT 1
        FROM pg_trigger
        WHERE tgrelid = 'sc_trx.transaction_dt'::regclass
          AND tgname = 'trg_01_transaction_coa'
          AND NOT tgisinternal
    ) THEN
        EXECUTE 'DROP TRIGGER trg_01_transaction_coa ON sc_trx.transaction_dt';
    END IF;

    IF EXISTS
    (
        SELECT 1
        FROM pg_trigger
        WHERE tgrelid = 'sc_trx.transaction_dt'::regclass
          AND tgname = 'trg_validate_transaction_type'
          AND NOT tgisinternal
    ) THEN
        EXECUTE 'DROP TRIGGER trg_validate_transaction_type ON sc_trx.transaction_dt';
    END IF;
END;
$$;


CREATE OR REPLACE TRIGGER trg_01_transaction_prepare
BEFORE INSERT OR UPDATE
ON sc_trx.transaction_dt
FOR EACH ROW
EXECUTE FUNCTION sc_trx.fn_prepare_transaction_dt();


CREATE OR REPLACE TRIGGER trg_02_transaction_type
BEFORE INSERT OR UPDATE
ON sc_trx.transaction_dt
FOR EACH ROW
EXECUTE FUNCTION sc_trx.fn_validate_transaction_type();


/* ============================================================
   8. ATURAN BUSINESS CRITICAL
   ============================================================

   JSA:
       GRNREC
          accounting YES
          stock NONE

       GRNRET
          accounting YES
          stock NONE

   BRG:
       GRNREC
          accounting YES
          stock IN

       GRNRET
          accounting YES
          stock OUT

       DELIVR
          accounting NO
          stock OUT

       SALESX
          accounting YES
          stock NONE
          -> jurnal AR + SALES + TAX + COGS + INVENTORY

       SALRET
          accounting YES
          stock NONE
          -> jurnal retur + TAX reversal + HPP reversal

   Dengan pola ini tidak ada double stock:

       GRNREC tidak dilanjutkan menjadi stock dua kali.
       DELIVR menjadi stock OUT.
       SALESX menjadi titik accounting.

   ============================================================ */


/* ============================================================
   9. PEMBENTUKAN JURNAL YANG DIHARAPKAN OLEH TAHAP BERIKUTNYA
   ============================================================

   A. GOODS RECEIPT / GRNREC - BARANG

       DEBET   Persediaan              DPP / Nilai Perolehan
       DEBET   PPN Masukan             Pajak
       [DEBET  PPH Masukan             jika ada]
       KREDIT  Hutang Supplier         Total

       COA:
         Persediaan -> mbarang.ppersediaan
         Jasa       -> mbarang.pjasa
         Pajak      -> tax_dtl.prk_masukan
         Hutang     -> currency.phutang


   B. GOODS RECEIPT / GRNREC - JASA

       DEBET   Perkiraan Jasa          DPP
       DEBET   PPN Masukan             Pajak
       KREDIT  Hutang Supplier         Total

       JSA tidak pernah masuk stkblc.


   C. SALESX - BARANG

       DEBET   Piutang Customer        Total
       KREDIT  Penjualan               DPP
       KREDIT  PPN Keluaran            Pajak

       DEBET   HPP                     COST
       KREDIT  Persediaan              COST

       COA:
         AR          -> currency.ppiutang
         Sales       -> currency.ppendapatan
         Tax         -> tax_dtl.prk_keluaran
         COGS        -> mbarang.pcogs
         Inventory   -> mbarang.ppersediaan


   D. SALRET - BARANG

       DEBET   Retur Penjualan         DPP
       DEBET   PPN Keluaran            Pajak
       KREDIT  Piutang                 Total

       DEBET   Persediaan              COST
       KREDIT  HPP                     COST

       COST mengikuti costing reversal,
       bukan harga jual.


   ============================================================ */


/* ============================================================
   10. SOURCE_UNIQUEID
   ============================================================

   TIDAK UNIQUE.

   Satu source:
       PO-DTL-001

   dapat menghasilkan:
       GRN-DTL-001
       GRN-DTL-002
       GRN-DTL-003
       RET-DTL-001

   Yang UNIQUE:
       transaction_dt.uniqueid
   ============================================================ */


/* ============================================================
   11. BATCH / LOT
   ============================================================

   TIDAK UNIQUE.

   Kombinasi:
       idbarang
       batch
       lotno
       lokasi

   adalah stock identity.

   stock_uniqueid:
       gabungan identitas stock.

   stock_key:
       MD5 dari stock_uniqueid.

   Setiap transaksi mempunyai uniqueid sendiri.
   ============================================================ */


/* ============================================================
   12. CHECK QUERY
   ============================================================

   Cek routing journal type:

       SELECT
           journal_type,
           module,
           direction,
           stock_effect,
           accounting_effect,
           asset_effect
       FROM sc_mst.journal_type
       WHERE journal_type IN
       (
           'PURCHS','PURRET','GRNREC','GRNRET',
           'DELIVR','DELRET','SALESX','SALRET'
       )
       ORDER BY journal_type;


   Cek transaksi:

       SELECT
           uniqueid,
           journal_type,
           module,
           direction,
           type_in_out,
           stock_effect,
           accounting_effect,
           asset_effect,
           idtax,
           idcoa,
           stock_uniqueid,
           stock_key
       FROM sc_trx.transaction_dt
       ORDER BY id DESC;


   Cek tax BBB11 untuk GRN:

       SELECT *
       FROM sc_trx.fn_get_transaction_tax_accounts
       (
           'BBB11',
           'GRNREC'
       );


   Cek tax BBB11 untuk SALES:

       SELECT *
       FROM sc_trx.fn_get_transaction_tax_accounts
       (
           'BBB11',
           'SALESX'
       );


   Hasil yang diharapkan:

       GRNREC:
           116103  PPH22 input
           116106  PPN input

       SALESX:
           214103  PPH22 output
           214116  PPN output


   PPN12 SALESX:
           214113
   ============================================================ */


/* ============================================================
   13. CONTOH TRANSACTION - TIDAK DIEKSEKUSI
   ============================================================

   Contoh JSA pada GRNREC:

       INSERT INTO sc_trx.transaction_dt
       (
           uniqueid,
           source_uniqueid,
           docno,
           doctype,
           journal_type,
           line_no,
           docdate,
           type_in_out,
           idbarang,
           qty,
           nilai,
           dpp,
           pajak,
           total,
           idtax,
           currcode,
           kurs,
           createdby
       )
       VALUES
       (
           'TX-GRN-JSA-001',
           'LPB-001-DTL-001',
           'LPB001',
           'LPB',
           'GRNREC',
           1,
           CURRENT_DATE,
           'IN',
           'JSA001',
           1,
           1000000,
           1000000,
           110000,
           1110000,
           'PPN11',
           'IDR',
           1,
           'SYSTEM'
       );

   Trigger akan menghasilkan:
       module = PURCHASE
       accounting_effect = YES
       stock_effect = NONE
       idtax = PPN11
       idcoa = mbarang.pjasa


   Contoh BRG pada GRNREC:

       accounting_effect = YES
       stock_effect = IN
       idtax = tax dari mbarang
       idcoa = mbarang.ppersediaan
       stock_uniqueid = terisi


   Contoh DELIVR BRG:

       accounting_effect = NO
       stock_effect = OUT
       stock_uniqueid = terisi


   Contoh SALESX BRG:

       accounting_effect = YES
       stock_effect = NONE
       idcoa = currency.ppendapatan
       tax account = tax_dtl.prk_keluaran
       HPP / Persediaan = COST pada generator accounting.


   ============================================================ */



/*
===========================================================================
MANUAL ACCOUNTING TRANSACTION SHAPE
===========================================================================

JVGENL / UMTITP / NDKAPD / NDKAPK / NDKARD / NDKARK
GIROIN / GIROUT / FXREAL / FXUNRL
ARWOFF / APWOFF / BADPRV / BADREV
UNEARN / UNEREL / PAYROL

Input:
    idcoa          = Perkiraan Asal
    counter_idcoa  = Perkiraan Lawan
    debet_kredit   = D / K
    idtax          = Tax pilihan
    nilai/dpp/pajak/total = nilai transaksi

Contoh:
    idcoa          = 213102
    counter_idcoa  = 511101
    debet_kredit   = D
    total          = 3.000.000

Hasil TAHAP 16:
    DEBIT   213102  3.000.000
    CREDIT  511101  3.000.000

NDK:
    NDKAPD / NDKAPK -> tax input
    NDKARD / NDKARK -> tax output

Stock:
    selalu NONE.
===========================================================================
*/



/* ============================================================
   TAHAP 04 SELESAI
   ============================================================

   Hasil final transaction_dt:

       SOURCE
          |
          v
       JOURNAL_TYPE
          |
          +---- ACCOUNTING
          |
          +---- STOCK
          |
          +---- ASSET
          |
          +---- TAX
          |
          +---- PRIMARY COA
          |
          +---- STOCK IDENTITY
          |
          v
       transaction_dt

   Tahap berikutnya:
       TAHAP 05  transaction_hd
       TAHAP 06  stkblc
       TAHAP 07  stkblc_avgcost
       TAHAP 08  assetblc
       TAHAP 09  jurnal_hd
       TAHAP 10  jurnal_dt
       TAHAP 11-16 posting / reverse

   ============================================================ */
