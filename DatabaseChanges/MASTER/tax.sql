/* ============================================================
   JSYS - TAX MASTER / TAX DETAIL
   Seed + COA TAX resolver

   Sumber akun pajak mengikuti master pada gambar:
   ------------------------------------------------------------
   BBB11
      PPH22  -> Masukan 116103 | Keluaran 214103 | 0.30%
      PPN    -> Masukan 116106 | Keluaran 214116 | 11.00%

   PPN11
      PPN    -> Masukan 116106 | Keluaran 214116 | 11.00%

   PPN12
      PPN    -> Masukan 116106 | Keluaran 214113 | 12.00%

   NON
      tidak mempunyai akun pajak
   ============================================================ */


/* ============================================================
   1. GROUP TAX
   ============================================================ */

CREATE TABLE IF NOT EXISTS sc_mst.grouptax
(
    idgrouptax  CHAR(20) PRIMARY KEY,
    nmgrouptax  CHAR(50),
    status      CHAR(6),
    chold       CHAR(6) DEFAULT 'NO',
    createdby   CHAR(20),
    createddate TIMESTAMP WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    id          SERIAL
);


/* ============================================================
   2. TAX MASTER
   ============================================================ */

CREATE TABLE IF NOT EXISTS sc_mst.tax_mst
(
    idtax       CHAR(20) PRIMARY KEY,
    nmtax       CHAR(50),
    status      CHAR(6),
    chold       CHAR(6) DEFAULT 'NO',
    createdby   CHAR(20),
    createddate TIMESTAMP WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    id          SERIAL
);


/* ============================================================
   3. TAX DETAIL
   ============================================================ */

CREATE TABLE IF NOT EXISTS sc_mst.tax_dtl
(
    idtax             CHAR(20) NOT NULL,
    idgrouptax        CHAR(50) NOT NULL,
    nmgrouptax        CHAR(50),
    kodepajak         CHAR(20),
    prk_masukan       CHAR(20),
    prk_keluaran      CHAR(20),
    prk_fpj_msk       CHAR(20),
    prk_fpj_klr       CHAR(20),
    prk_fpj_msk_rep   CHAR(20),
    prk_fpj_klr_rep   CHAR(20),
    rumus_dpp_lain    CHAR(20),
    rumus_pajak       CHAR(20),
    percentation      NUMERIC(18,2),
    status             CHAR(6),
    chold              CHAR(6) DEFAULT 'NO',
    createdby          CHAR(20),
    createddate        TIMESTAMP WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    id                 SERIAL,

    CONSTRAINT pk_tax_dtl
        PRIMARY KEY (idtax, idgrouptax)
);


/* ============================================================
   4. INSERT GROUP TAX
   ============================================================ */

INSERT INTO sc_mst.grouptax
(
    idgrouptax,
    nmgrouptax,
    status,
    chold
)
VALUES
    ('NON',  'NON',  'P', 'NO'),
    ('PPH',  'PPH',  'P', 'NO'),
    ('PPN',  'PPN',  'P', 'NO'),
    ('PPH22','PPH22','P', 'NO')
ON CONFLICT (idgrouptax) DO UPDATE
SET
    nmgrouptax = EXCLUDED.nmgrouptax,
    status     = EXCLUDED.status,
    chold      = EXCLUDED.chold;


/* ============================================================
   5. INSERT TAX MASTER
   ============================================================ */

INSERT INTO sc_mst.tax_mst
(
    idtax,
    nmtax,
    status,
    chold
)
VALUES
    ('BBB11', 'PPH 22 + PPN 11%', 'P', 'NO'),
    ('NON',   'NON',             'P', 'NO'),
    ('PPN11', 'PPN 11%',         'P', 'NO'),
    ('PPN12', 'PPN 12%',         'P', 'NO')
ON CONFLICT (idtax) DO UPDATE
SET
    nmtax  = EXCLUDED.nmtax,
    status = EXCLUDED.status,
    chold  = EXCLUDED.chold;


/* ============================================================
   6. INSERT TAX DETAIL
   ============================================================ */


/* ------------------------------------------------------------
   BBB11
   ------------------------------------------------------------
   PPH22:
       prk_masukan  = 116103
       prk_keluaran = 214103
       percent      = 0.30

   PPN:
       prk_masukan  = 116106
       prk_keluaran = 214116
       percent      = 11.00
   ------------------------------------------------------------ */

INSERT INTO sc_mst.tax_dtl
(
    idtax,
    idgrouptax,
    nmgrouptax,
    kodepajak,
    prk_masukan,
    prk_keluaran,
    prk_fpj_msk,
    prk_fpj_klr,
    prk_fpj_msk_rep,
    prk_fpj_klr_rep,
    rumus_dpp_lain,
    rumus_pajak,
    percentation,
    status,
    chold
)
VALUES
(
    'BBB11',
    'PPH22',
    'PPH22',
    '',
    '116103',
    '214103',
    '',
    '',
    '',
    '',
    '',
    '',
    0.30,
    'P',
    'NO'
),
(
    'BBB11',
    'PPN',
    'PPN',
    '',
    '116106',
    '214116',
    '',
    '',
    '',
    '',
    '',
    '',
    11.00,
    'P',
    'NO'
)
ON CONFLICT (idtax, idgrouptax) DO UPDATE
SET
    nmgrouptax       = EXCLUDED.nmgrouptax,
    kodepajak        = EXCLUDED.kodepajak,
    prk_masukan      = EXCLUDED.prk_masukan,
    prk_keluaran     = EXCLUDED.prk_keluaran,
    prk_fpj_msk      = EXCLUDED.prk_fpj_msk,
    prk_fpj_klr      = EXCLUDED.prk_fpj_klr,
    prk_fpj_msk_rep  = EXCLUDED.prk_fpj_msk_rep,
    prk_fpj_klr_rep  = EXCLUDED.prk_fpj_klr_rep,
    rumus_dpp_lain   = EXCLUDED.rumus_dpp_lain,
    rumus_pajak      = EXCLUDED.rumus_pajak,
    percentation     = EXCLUDED.percentation,
    status            = EXCLUDED.status,
    chold             = EXCLUDED.chold;


/* ------------------------------------------------------------
   NON
   ------------------------------------------------------------ */

INSERT INTO sc_mst.tax_dtl
(
    idtax,
    idgrouptax,
    nmgrouptax,
    kodepajak,
    prk_masukan,
    prk_keluaran,
    prk_fpj_msk,
    prk_fpj_klr,
    prk_fpj_msk_rep,
    prk_fpj_klr_rep,
    rumus_dpp_lain,
    rumus_pajak,
    percentation,
    status,
    chold
)
VALUES
(
    'NON',
    'NON',
    'NON',
    '',
    '',
    '',
    '',
    '',
    '',
    '',
    '',
    '',
    NULL,
    'P',
    'NO'
)
ON CONFLICT (idtax, idgrouptax) DO UPDATE
SET
    nmgrouptax       = EXCLUDED.nmgrouptax,
    kodepajak        = EXCLUDED.kodepajak,
    prk_masukan      = EXCLUDED.prk_masukan,
    prk_keluaran     = EXCLUDED.prk_keluaran,
    prk_fpj_msk      = EXCLUDED.prk_fpj_msk,
    prk_fpj_klr      = EXCLUDED.prk_fpj_klr,
    prk_fpj_msk_rep  = EXCLUDED.prk_fpj_msk_rep,
    prk_fpj_klr_rep  = EXCLUDED.prk_fpj_klr_rep,
    rumus_dpp_lain   = EXCLUDED.rumus_dpp_lain,
    rumus_pajak      = EXCLUDED.rumus_pajak,
    percentation     = EXCLUDED.percentation,
    status            = EXCLUDED.status,
    chold             = EXCLUDED.chold;


/* ------------------------------------------------------------
   PPN11
   ------------------------------------------------------------ */

INSERT INTO sc_mst.tax_dtl
(
    idtax,
    idgrouptax,
    nmgrouptax,
    kodepajak,
    prk_masukan,
    prk_keluaran,
    prk_fpj_msk,
    prk_fpj_klr,
    prk_fpj_msk_rep,
    prk_fpj_klr_rep,
    rumus_dpp_lain,
    rumus_pajak,
    percentation,
    status,
    chold
)
VALUES
(
    'PPN11',
    'PPN',
    'PPN',
    '',
    '116106',
    '214116',
    '116106',
    '214116',
    '116106',
    '214116',
    '',
    '',
    11.00,
    'P',
    'NO'
)
ON CONFLICT (idtax, idgrouptax) DO UPDATE
SET
    nmgrouptax       = EXCLUDED.nmgrouptax,
    kodepajak        = EXCLUDED.kodepajak,
    prk_masukan      = EXCLUDED.prk_masukan,
    prk_keluaran     = EXCLUDED.prk_keluaran,
    prk_fpj_msk      = EXCLUDED.prk_fpj_msk,
    prk_fpj_klr      = EXCLUDED.prk_fpj_klr,
    prk_fpj_msk_rep  = EXCLUDED.prk_fpj_msk_rep,
    prk_fpj_klr_rep  = EXCLUDED.prk_fpj_klr_rep,
    rumus_dpp_lain   = EXCLUDED.rumus_dpp_lain,
    rumus_pajak      = EXCLUDED.rumus_pajak,
    percentation     = EXCLUDED.percentation,
    status            = EXCLUDED.status,
    chold             = EXCLUDED.chold;


/* ------------------------------------------------------------
   PPN12
   ------------------------------------------------------------ */

INSERT INTO sc_mst.tax_dtl
(
    idtax,
    idgrouptax,
    nmgrouptax,
    kodepajak,
    prk_masukan,
    prk_keluaran,
    prk_fpj_msk,
    prk_fpj_klr,
    prk_fpj_msk_rep,
    prk_fpj_klr_rep,
    rumus_dpp_lain,
    rumus_pajak,
    percentation,
    status,
    chold
)
VALUES
(
    'PPN12',
    'PPN',
    'PPN',
    '',
    '116106',
    '214113',
    '116106',
    '214113',
    '116106',
    '214113',
    '',
    '',
    12.00,
    'P',
    'NO'
)
ON CONFLICT (idtax, idgrouptax) DO UPDATE
SET
    nmgrouptax       = EXCLUDED.nmgrouptax,
    kodepajak        = EXCLUDED.kodepajak,
    prk_masukan      = EXCLUDED.prk_masukan,
    prk_keluaran     = EXCLUDED.prk_keluaran,
    prk_fpj_msk      = EXCLUDED.prk_fpj_msk,
    prk_fpj_klr      = EXCLUDED.prk_fpj_klr,
    prk_fpj_msk_rep  = EXCLUDED.prk_fpj_msk_rep,
    prk_fpj_klr_rep  = EXCLUDED.prk_fpj_klr_rep,
    rumus_dpp_lain   = EXCLUDED.rumus_dpp_lain,
    rumus_pajak      = EXCLUDED.rumus_pajak,
    percentation     = EXCLUDED.percentation,
    status            = EXCLUDED.status,
    chold             = EXCLUDED.chold;


/* ============================================================
   7. INDEX UNTUK POSTING ACCOUNTING
   ============================================================ */

CREATE INDEX IF NOT EXISTS idx_tax_dtl_idtax_status
ON sc_mst.tax_dtl (idtax, status);

CREATE INDEX IF NOT EXISTS idx_tax_dtl_group
ON sc_mst.tax_dtl (idgrouptax, status);


/* ============================================================
   8. TAX ACCOUNT RESOLVER
   ============================================================

   ATURAN:
   PURCHASE / RECEIPT
       -> prk_masukan

   SALES / INVOICE
       -> prk_keluaran

   PENTING:
   Satu idtax boleh menghasilkan >1 baris pajak.
   Contoh BBB11 menghasilkan:
       PPH22 + PPN
   Karena itu function mengembalikan SETOF.

   Function ini dipakai oleh generator jurnal (TAHAP 10/16),
   bukan untuk mengganti transaction_dt.idcoa yang hanya
   menyimpan satu COA utama.
   ============================================================ */

DROP FUNCTION IF EXISTS sc_trx.fn_get_tax_accounts(CHAR(20), TEXT);

CREATE OR REPLACE FUNCTION sc_trx.fn_get_tax_accounts
(
    p_idtax      CHAR(20),
    p_flow       TEXT
)
RETURNS TABLE
(
    idtax             CHAR(20),
    idgrouptax        CHAR(50),
    percentation      NUMERIC(18,2),
    idcoa             CHAR(20),
    idcoa_faktur      CHAR(20)
)
LANGUAGE SQL
STABLE
AS $$
    SELECT
        d.idtax,
        d.idgrouptax,
        d.percentation,

        CASE
            WHEN UPPER(TRIM(COALESCE(p_flow, ''))) IN
                 ('PURCHASE','RECEIPT','GRN','IN','INPUT')
                THEN NULLIF(TRIM(d.prk_masukan), '')
            WHEN UPPER(TRIM(COALESCE(p_flow, ''))) IN
                 ('SALES','INVOICE','OUT','OUTPUT')
                THEN NULLIF(TRIM(d.prk_keluaran), '')
            ELSE NULL
        END AS idcoa,

        CASE
            WHEN UPPER(TRIM(COALESCE(p_flow, ''))) IN
                 ('PURCHASE','RECEIPT','GRN','IN','INPUT')
                THEN NULLIF(TRIM(d.prk_fpj_msk), '')
            WHEN UPPER(TRIM(COALESCE(p_flow, ''))) IN
                 ('SALES','INVOICE','OUT','OUTPUT')
                THEN NULLIF(TRIM(d.prk_fpj_klr), '')
            ELSE NULL
        END AS idcoa_faktur

    FROM sc_mst.tax_dtl d
    WHERE TRIM(d.idtax) = TRIM(COALESCE(p_idtax, ''))
      AND d.status = 'P'
      AND NULLIF(TRIM(
            CASE
                WHEN UPPER(TRIM(COALESCE(p_flow, ''))) IN
                     ('PURCHASE','RECEIPT','GRN','IN','INPUT')
                    THEN d.prk_masukan
                WHEN UPPER(TRIM(COALESCE(p_flow, ''))) IN
                     ('SALES','INVOICE','OUT','OUTPUT')
                    THEN d.prk_keluaran
                ELSE ''
            END
      ), '') IS NOT NULL
    ORDER BY d.idgrouptax;
$$;


/* ============================================================
   9. CONTOH PEMAKAIAN
   ============================================================ */


/* Penerimaan BBB11:
   -> 116103 PPH22
   -> 116106 PPN
*/
SELECT *
FROM sc_trx.fn_get_tax_accounts('BBB11', 'PURCHASE');


/* Penjualan BBB11:
   -> 214103 PPH22
   -> 214116 PPN
*/
SELECT *
FROM sc_trx.fn_get_tax_accounts('BBB11', 'SALES');


/* PPN11 - pembelian */
SELECT *
FROM sc_trx.fn_get_tax_accounts('PPN11', 'PURCHASE');


/* PPN12 - penjualan */
SELECT *
FROM sc_trx.fn_get_tax_accounts('PPN12', 'SALES');


/* ============================================================
   10. PENTING UNTUK MASTER BARANG
   ============================================================

   Bila master barang menyimpan idtax, maka effective tax
   untuk transaksi harus mengikuti:

       transaction_dt.idtax
               ↓
       bila kosong
               ↓
       mbarang.idtax
               ↓
       tax_dtl
               ↓
       prk_masukan / prk_keluaran

   Jadi akun pajak TIDAK boleh di-hard-code seperti:
       116106
       214116
       214113

   Kode tersebut hanya berasal dari master TAX.

   Contoh query resolver berdasarkan master barang:

   SELECT
       b.idbarang,
       b.idtax,
       d.idgrouptax,
       d.percentation,
       d.prk_masukan,
       d.prk_keluaran
   FROM sc_mst.mbarang b
   LEFT JOIN sc_mst.tax_dtl d
          ON d.idtax = b.idtax
         AND d.status = 'P'
   WHERE b.idbarang = 'BRG001';

   Untuk transaction_dt, lebih aman:

       effective_idtax =
           COALESCE(
               NULLIF(TRIM(transaction_dt.idtax), ''),
               NULLIF(TRIM(mbarang.idtax), ''),
               'NON'
           )

   Artinya transaksi yang mempunyai PPN pada master barang
   otomatis mengambil COA pajak dari tax_dtl.
   ============================================================ */
