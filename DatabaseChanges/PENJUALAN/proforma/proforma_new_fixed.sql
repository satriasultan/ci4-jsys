--##############################################################################
-- PROFORMA / INVOICE - POSTGRESQL
-- ONE-SHOT EXECUTION: DROP + CREATE + TRIGGER + DRAFT/FINAL
--
-- PERINGATAN:
--   Script ini MENGHAPUS seluruh data:
--     - sc_tmp.proforma, sc_tmp.proformadtl
--     - sc_trx.proforma, sc_trx.proformadtl
--   Jalankan HANYA pada database DEVELOPMENT / hasil backup.
--   WAJIB backup sebelum eksekusi.
--
-- SKEMA:
--   - Header-detail (1 header : N detail)
--   - idurut_view di header & detail untuk sorting tampilan manual
--   - invmode: SINGLE (per barang) | MULTI (per dokumen PJO)
--   - Kolom disinkronkan dengan sc_trx.penjualan / penjualan_dtl
--   - Tambahan khusus invoice: customerdestination, trader, enduser,
--     pono, podate, jnsinvoice, bank, total invoice, dll.
--   - Data sc_trx TIDAK PERNAH dihapus saat revisi/cancel (histori)
--##############################################################################

BEGIN;

-- ============================================================================
-- 0. DROP OBJECTS
-- ============================================================================
DROP TRIGGER IF EXISTS tr_proforma_finalize ON sc_tmp.proforma;
DROP TRIGGER IF EXISTS tr_proforma          ON sc_trx.proforma;

DROP FUNCTION IF EXISTS sc_tmp.tr_proforma_finalize();
DROP FUNCTION IF EXISTS sc_trx.tr_proforma();

DROP TABLE IF EXISTS sc_tmp.proformadtl CASCADE;
DROP TABLE IF EXISTS sc_tmp.proforma     CASCADE;
DROP TABLE IF EXISTS sc_trx.proformadtl CASCADE;
DROP TABLE IF EXISTS sc_trx.proforma     CASCADE;

DROP SEQUENCE IF EXISTS sc_tmp.proforma_idurut_seq CASCADE;
DROP SEQUENCE IF EXISTS sc_trx.proforma_idurut_seq CASCADE;

-- ============================================================================
-- 1. SEQUENCE
-- ============================================================================
CREATE SEQUENCE sc_tmp.proforma_idurut_seq
    INCREMENT 1 START 1 MINVALUE 1 MAXVALUE 2147483647 CACHE 1;
ALTER SEQUENCE sc_tmp.proforma_idurut_seq OWNER TO postgres;

CREATE SEQUENCE sc_trx.proforma_idurut_seq
    INCREMENT 1 START 1 MINVALUE 1 MAXVALUE 2147483647 CACHE 1;
ALTER SEQUENCE sc_trx.proforma_idurut_seq OWNER TO postgres;

-- ============================================================================
-- 2. HEADER TEMPORARY
-- ============================================================================
CREATE TABLE sc_tmp.proforma
(
    idurut                      INTEGER NOT NULL
        DEFAULT nextval('sc_tmp.proforma_idurut_seq'::regclass),
    idurut_view                 INTEGER,
    docno                       CHARACTER(30) NOT NULL,
    invmode                     CHARACTER(10) NOT NULL DEFAULT 'SINGLE',

    -- Referensi
    docnopjo                    CHARACTER(30),
    docnodo                     CHARACTER(30),
    docnoso                     CHARACTER(30),
    idrev                       CHARACTER(30),
    rolejob                     CHARACTER(10),

    -- Dokumen
    docdate                     DATE,
    pono                        CHARACTER(30),
    podate                      DATE,
    jnsinvoice                  CHARACTER(20),
    doctype                     CHARACTER(10) DEFAULT 'SALES',

    -- Customer
    kdcustomer                  CHARACTER(30),
    nmcustomer                  CHARACTER(250),
    cust                        CHARACTER(100),
    address                     TEXT,
    phone                       VARCHAR(50),
    fax                         VARCHAR(50),
    alamatcustomer              TEXT,
    gradecustomer               CHARACTER(30),

    -- Delivery customer
    kdcustomerdeliv             CHARACTER(30),
    nmcustomerdeliv             CHARACTER(250),
    alamatcustomerdeliv         TEXT,

    -- Customer destination / trader / enduser
    customerdestination         CHARACTER(250),
    customerdestination_addr    TEXT,
    trader                      CHARACTER(250),
    trader_addr                 TEXT,
    enduser                     CHARACTER(250),
    enduser_addr                TEXT,

    -- Sales
    cabang                      CHARACTER(30),
    pemohon                     CHARACTER(100),
    kdsalesman                  CHARACTER(10),

    -- Shipping
    facrisk                     TEXT,
    shipper                     TEXT,
    consignee                   TEXT,
    shippingmark                TEXT,
    notifyparty                 TEXT,
    paymentmethod               VARCHAR(50),
    bank                        VARCHAR(50),
    carabayar                   CHARACTER(30),
    jthtempo                    NUMERIC(18,4),

    -- Pajak & valas
    idtax                       CHARACTER(20),
    isinclusive                 CHARACTER(6),
    isopenprice                 CHARACTER(6),
    currcode                    CHARACTER(3),
    kurs                        NUMERIC(18,4) DEFAULT 1,

    -- Total
    grosssales                  NUMERIC(18,4),
    downpayment                 NUMERIC(18,4),
    netsales                    NUMERIC(18,4),
    taxbasis                    NUMERIC(18,4),
    vat                         NUMERIC(18,4),
    pph22                       NUMERIC(18,4),
    dpp                         NUMERIC(18,4),
    jumlahpajak                 NUMERIC(18,4),
    total                       NUMERIC(18,4),
    ttlprice                    NUMERIC(18,4),

    -- Bank
    nmbank                      CHARACTER(100),
    alamatbank                  TEXT,
    kodeposbank                 CHARACTER(100),
    accname                     CHARACTER(100),
    accno                       TEXT,
    swiftcode                   CHARACTER(50),

    -- Lain-lain
    description                 TEXT,
    keterangan                  TEXT,
    brand                       CHARACTER(50),
    size                        TEXT,
    qty                         CHARACTER(100),
    pembayaran                  TEXT,
    pengiriman                  TEXT,
    expdate                     DATE,
    ketentuan                   TEXT,

    -- Status & audit
    status                      CHARACTER(6),
    jurnalno                    CHARACTER(50),
    inputby                     VARCHAR(50),
    inputdate                   TIMESTAMP,
    updateby                    VARCHAR(50),
    updatedate                  TIMESTAMP,
    printby                     VARCHAR(50),
    printdate                   TIMESTAMP,
    printcount                  INTEGER DEFAULT 0,
    docnotmp                    CHARACTER(30),

    CONSTRAINT pk_tmp_proforma PRIMARY KEY (docno)
);

ALTER TABLE sc_tmp.proforma OWNER TO postgres;
ALTER SEQUENCE sc_tmp.proforma_idurut_seq OWNED BY sc_tmp.proforma.idurut;
CREATE INDEX idx_tmp_proforma_view    ON sc_tmp.proforma (idurut_view);
CREATE INDEX idx_tmp_proforma_pjo     ON sc_tmp.proforma (docnopjo);
CREATE INDEX idx_tmp_proforma_invmode ON sc_tmp.proforma (invmode);

-- ============================================================================
-- 3. HEADER TRANSAKSI
-- ============================================================================
CREATE TABLE sc_trx.proforma
(
    idurut                      INTEGER NOT NULL
        DEFAULT nextval('sc_trx.proforma_idurut_seq'::regclass),
    idurut_view                 INTEGER,
    docno                       CHARACTER(30) NOT NULL,
    invmode                     CHARACTER(10) NOT NULL DEFAULT 'SINGLE',

    docnopjo                    CHARACTER(30),
    docnodo                     CHARACTER(30),
    docnoso                     CHARACTER(30),
    idrev                       CHARACTER(30),
    rolejob                     CHARACTER(10),

    docdate                     DATE,
    pono                        CHARACTER(30),
    podate                      DATE,
    jnsinvoice                  CHARACTER(20),
    doctype                     CHARACTER(10) DEFAULT 'SALES',

    kdcustomer                  CHARACTER(30),
    nmcustomer                  CHARACTER(250),
    cust                        CHARACTER(100),
    address                     TEXT,
    phone                       VARCHAR(50),
    fax                         VARCHAR(50),
    alamatcustomer              TEXT,
    gradecustomer               CHARACTER(30),

    kdcustomerdeliv             CHARACTER(30),
    nmcustomerdeliv             CHARACTER(250),
    alamatcustomerdeliv         TEXT,

    customerdestination         CHARACTER(250),
    customerdestination_addr    TEXT,
    trader                      CHARACTER(250),
    trader_addr                 TEXT,
    enduser                     CHARACTER(250),
    enduser_addr                TEXT,

    cabang                      CHARACTER(30),
    pemohon                     CHARACTER(100),
    kdsalesman                  CHARACTER(10),

    facrisk                     TEXT,
    shipper                     TEXT,
    consignee                   TEXT,
    shippingmark                TEXT,
    notifyparty                 TEXT,
    paymentmethod               VARCHAR(50),
    bank                        VARCHAR(50),
    carabayar                   CHARACTER(30),
    jthtempo                    NUMERIC(18,4),

    idtax                       CHARACTER(20),
    isinclusive                 CHARACTER(6),
    isopenprice                 CHARACTER(6),
    currcode                    CHARACTER(3),
    kurs                        NUMERIC(18,4) DEFAULT 1,

    grosssales                  NUMERIC(18,4),
    downpayment                 NUMERIC(18,4),
    netsales                    NUMERIC(18,4),
    taxbasis                    NUMERIC(18,4),
    vat                         NUMERIC(18,4),
    pph22                       NUMERIC(18,4),
    dpp                         NUMERIC(18,4),
    jumlahpajak                 NUMERIC(18,4),
    total                       NUMERIC(18,4),
    ttlprice                    NUMERIC(18,4),

    nmbank                      CHARACTER(100),
    alamatbank                  TEXT,
    kodeposbank                 CHARACTER(100),
    accname                     CHARACTER(100),
    accno                       TEXT,
    swiftcode                   CHARACTER(50),

    description                 TEXT,
    keterangan                  TEXT,
    brand                       CHARACTER(50),
    size                        TEXT,
    qty                         CHARACTER(100),
    pembayaran                  TEXT,
    pengiriman                  TEXT,
    expdate                     DATE,
    ketentuan                   TEXT,

    status                      CHARACTER(6),
    jurnalno                    CHARACTER(50),
    inputby                     VARCHAR(50),
    inputdate                   TIMESTAMP,
    updateby                    VARCHAR(50),
    updatedate                  TIMESTAMP,
    printby                     VARCHAR(50),
    printdate                   TIMESTAMP,
    printcount                  INTEGER DEFAULT 0,
    docnotmp                    CHARACTER(30),

    CONSTRAINT pk_trx_proforma PRIMARY KEY (docno)
);

ALTER TABLE sc_trx.proforma OWNER TO postgres;
ALTER SEQUENCE sc_trx.proforma_idurut_seq OWNED BY sc_trx.proforma.idurut;
CREATE INDEX idx_trx_proforma_view    ON sc_trx.proforma (idurut_view);
CREATE INDEX idx_trx_proforma_pjo     ON sc_trx.proforma (docnopjo);
CREATE INDEX idx_trx_proforma_invmode ON sc_trx.proforma (invmode);

-- ============================================================================
-- 4. DETAIL TEMPORARY
-- ============================================================================
CREATE TABLE sc_tmp.proformadtl
(
    idurut          SERIAL PRIMARY KEY,
    idurut_view     INTEGER,
    docno           CHARACTER(30) NOT NULL,
    invmode         CHARACTER(10) NOT NULL DEFAULT 'SINGLE',

    -- Referensi PJO
    docnopjo        CHARACTER(30),
    idurutpjo       INTEGER,
    uniqueidpjo     VARCHAR(64),
    uniqueid        VARCHAR(64),

    -- Referensi SO
    docnoso         CHARACTER(30),
    idrev           CHARACTER(30),
    idurutso        INTEGER,
    uniqueidso      VARCHAR(64),

    -- Referensi DO
    docnodo         CHARACTER(30),
    idurutdo        INTEGER,
    uniqueiddo      VARCHAR(64),

    -- Barang
    idbarangref     CHARACTER(20),
    nmbarangref     CHARACTER(150),
    idbarang        CHARACTER(20),
    nmbarang        CHARACTER(150),
    idprincipal     CHARACTER(20),
    idgudang        CHARACTER(30),
    idspec          CHARACTER(30),
    unit            CHARACTER(20),

    -- Qty & harga
    qtyref          NUMERIC(18,4),
    hargasatuanref  NUMERIC(18,4),
    qty             NUMERIC(18,4),
    harga           NUMERIC(18,4),
    hargasatuan     NUMERIC(18,4),
    multidisc       NUMERIC(18,4),
    discount        NUMERIC(18,4),
    usdmt           NUMERIC(18,4),
    exchange        NUMERIC(18,4),
    nilai           NUMERIC(18,4),
    amount          NUMERIC(18,4),

    -- Pajak & kurs
    idtax           CHARACTER(20),
    currcode        CHARACTER(3),
    kurs            NUMERIC(18,4),
    nilaikonversi   NUMERIC(18,4),
    nilaipajak      NUMERIC(18,4),
    qtyretur        NUMERIC(18,4) DEFAULT 0,

    -- Lain-lain
    stokakhir       NUMERIC(18,4),
    bomdesc         TEXT,
    description     TEXT,

    -- Audit
    status          CHARACTER(6),
    inputby         VARCHAR(50),
    inputdate       TIMESTAMP,
    updateby        VARCHAR(50),
    updatedate      TIMESTAMP,
    doctype         CHARACTER(10) DEFAULT 'SALES',
    docnotmp        CHARACTER(30)
);

ALTER TABLE sc_tmp.proformadtl OWNER TO postgres;
CREATE INDEX idx_tmp_proformadtl_view ON sc_tmp.proformadtl (docno, idurut_view);
CREATE INDEX idx_tmp_proformadtl_pjo  ON sc_tmp.proformadtl (docnopjo);
CREATE INDEX idx_tmp_proformadtl_uid  ON sc_tmp.proformadtl (uniqueidpjo);

-- ============================================================================
-- 5. DETAIL TRANSAKSI
-- ============================================================================
CREATE TABLE sc_trx.proformadtl
(
    idurut          SERIAL PRIMARY KEY,
    idurut_view     INTEGER,
    docno           CHARACTER(30) NOT NULL,
    invmode         CHARACTER(10) NOT NULL DEFAULT 'SINGLE',

    docnopjo        CHARACTER(30),
    idurutpjo       INTEGER,
    uniqueidpjo     VARCHAR(64),
    uniqueid        VARCHAR(64),

    docnoso         CHARACTER(30),
    idrev           CHARACTER(30),
    idurutso        INTEGER,
    uniqueidso      VARCHAR(64),

    docnodo         CHARACTER(30),
    idurutdo        INTEGER,
    uniqueiddo      VARCHAR(64),

    idbarangref     CHARACTER(20),
    nmbarangref     CHARACTER(150),
    idbarang        CHARACTER(20),
    nmbarang        CHARACTER(150),
    idprincipal     CHARACTER(20),
    idgudang        CHARACTER(30),
    idspec          CHARACTER(30),
    unit            CHARACTER(20),

    qtyref          NUMERIC(18,4),
    hargasatuanref  NUMERIC(18,4),
    qty             NUMERIC(18,4),
    harga           NUMERIC(18,4),
    hargasatuan     NUMERIC(18,4),
    multidisc       NUMERIC(18,4),
    discount        NUMERIC(18,4),
    usdmt           NUMERIC(18,4),
    exchange        NUMERIC(18,4),
    nilai           NUMERIC(18,4),
    amount          NUMERIC(18,4),

    idtax           CHARACTER(20),
    currcode        CHARACTER(3),
    kurs            NUMERIC(18,4),
    nilaikonversi   NUMERIC(18,4),
    nilaipajak      NUMERIC(18,4),
    qtyretur        NUMERIC(18,4) DEFAULT 0,

    stokakhir       NUMERIC(18,4),
    bomdesc         TEXT,
    description     TEXT,

    status          CHARACTER(6),
    inputby         VARCHAR(50),
    inputdate       TIMESTAMP,
    updateby        VARCHAR(50),
    updatedate      TIMESTAMP,
    doctype         CHARACTER(10) DEFAULT 'SALES',
    docnotmp        CHARACTER(30)
);

ALTER TABLE sc_trx.proformadtl OWNER TO postgres;
CREATE INDEX idx_trx_proformadtl_view ON sc_trx.proformadtl (docno, idurut_view);
CREATE INDEX idx_trx_proformadtl_pjo  ON sc_trx.proformadtl (docnopjo);
CREATE INDEX idx_trx_proformadtl_uid  ON sc_trx.proformadtl (uniqueidpjo);

-- ============================================================================
-- 6. FUNCTION: sc_tmp.tr_proforma_finalize()
--    AFTER UPDATE ON sc_tmp.proforma
--
--    E -> D : Simpan Draft   (normal: INSERT; revisi: UPDATE existing)
--    E -> F : Simpan Final   (normal: INSERT; revisi: UPDATE existing)
--    E -> C : Batal input    (hapus tmp saja)
-- ============================================================================
CREATE OR REPLACE FUNCTION sc_tmp.tr_proforma_finalize()
RETURNS trigger
LANGUAGE plpgsql
AS $BODY$
DECLARE
    v_docno       TEXT;
    v_inputby     TEXT;
    v_idurut      INTEGER;
    v_num         TEXT;
    v_num_int     BIGINT;
    v_base_docno  TEXT;
    v_new_docno   TEXT;
    v_client_ip   TEXT;
    v_docnotmp    TEXT;
    v_new_view    INTEGER;
    v_new_status  CHAR(6);
BEGIN
    IF NOT (
        (OLD.status = 'E' AND NEW.status = 'D') OR
        (OLD.status = 'E' AND NEW.status = 'F') OR
        (OLD.status = 'E' AND NEW.status = 'C')
    ) THEN
        RETURN NEW;
    END IF;

    v_inputby   := NEW.inputby;
    v_idurut    := NEW.idurut;
    v_docno     := BTRIM(NEW.docno);
    v_docnotmp  := NULLIF(BTRIM(NEW.docnotmp), '');
    v_client_ip := sc_log.fn_get_user_ip(v_inputby);

    -- ===================== E -> C (BATAL) =====================
    IF OLD.status = 'E' AND NEW.status = 'C' THEN
        DELETE FROM sc_tmp.proformadtl WHERE BTRIM(docno) = BTRIM(NEW.docno);
        DELETE FROM sc_tmp.proforma     WHERE BTRIM(docno) = BTRIM(NEW.docno);
        RETURN NEW;
    END IF;

    v_new_status := CASE WHEN NEW.status = 'D' THEN 'D'::CHAR(6)
                         ELSE 'F'::CHAR(6) END;

    -- ============================ PATH A: NORMAL =========================
    IF v_docnotmp IS NULL THEN

        IF v_docno IS NULL OR v_docno = '' THEN
            RAISE EXCEPTION 'Nomor Proforma tidak boleh kosong';
        END IF;

        IF v_docno !~ '[0-9]+$' THEN
            RAISE EXCEPTION
                'Format nomor Proforma tidak valid (harus diakhiri angka): %',
                v_docno;
        END IF;

        v_base_docno := regexp_replace(v_docno, '[0-9]+$', '');
        v_num        := substring(v_docno FROM '([0-9]+)$');

        PERFORM pg_advisory_xact_lock(hashtextextended(v_base_docno, 0));

        v_new_docno := v_docno;

        LOOP
            EXIT WHEN NOT EXISTS (
                SELECT 1 FROM sc_trx.proforma px
                WHERE BTRIM(px.docno) = v_new_docno
            );

            v_num := substring(v_new_docno FROM '([0-9]+)$');
            IF v_num IS NULL OR v_num = '' THEN
                RAISE EXCEPTION 'Tidak dapat membaca nomor urut dari dokumen: %', v_new_docno;
            END IF;

            v_num_int   := v_num::BIGINT + 1;
            v_new_docno := v_base_docno || lpad(v_num_int::TEXT, length(v_num), '0');

            IF length(v_new_docno) > 30 THEN
                RAISE EXCEPTION 'Nomor Proforma melebihi 30 karakter: %', v_new_docno;
            END IF;
        END LOOP;

        v_docno := v_new_docno;

        SELECT COALESCE(MAX(idurut_view), 0) + 1 INTO v_new_view
        FROM sc_trx.proforma;

        -- INSERT HEADER (status D/F)
        INSERT INTO sc_trx.proforma (
            idurut_view, docno, invmode, docnopjo, docnodo, docnoso, idrev,
            rolejob, docdate, pono, podate, jnsinvoice, doctype,
            kdcustomer, nmcustomer, cust, address, phone, fax,
            alamatcustomer, gradecustomer,
            kdcustomerdeliv, nmcustomerdeliv, alamatcustomerdeliv,
            customerdestination, customerdestination_addr,
            trader, trader_addr, enduser, enduser_addr,
            cabang, pemohon, kdsalesman,
            facrisk, shipper, consignee, shippingmark, notifyparty,
            paymentmethod, bank, carabayar, jthtempo,
            idtax, isinclusive, isopenprice, currcode, kurs,
            grosssales, downpayment, netsales, taxbasis, vat, pph22,
            dpp, jumlahpajak, total, ttlprice,
            nmbank, alamatbank, kodeposbank, accname, accno, swiftcode,
            description, keterangan, brand, size, qty, pembayaran, pengiriman,
            expdate, ketentuan, status, jurnalno,
            inputby, inputdate, updateby, updatedate,
            printby, printdate, printcount, docnotmp
        )
        SELECT
            v_new_view, v_docno, invmode, docnopjo, docnodo, docnoso, idrev,
            rolejob, docdate, pono, podate, jnsinvoice, COALESCE(doctype, 'SALES'),
            kdcustomer, nmcustomer, cust, address, phone, fax,
            alamatcustomer, gradecustomer,
            kdcustomerdeliv, nmcustomerdeliv, alamatcustomerdeliv,
            customerdestination, customerdestination_addr,
            trader, trader_addr, enduser, enduser_addr,
            cabang, pemohon, kdsalesman,
            facrisk, shipper, consignee, shippingmark, notifyparty,
            paymentmethod, bank, carabayar, jthtempo,
            idtax, isinclusive, isopenprice, currcode, kurs,
            grosssales, downpayment, netsales, taxbasis, vat, pph22,
            dpp, jumlahpajak, total, ttlprice,
            nmbank, alamatbank, kodeposbank, accname, accno, swiftcode,
            description, keterangan, brand, size, qty, pembayaran, pengiriman,
            expdate, ketentuan, v_new_status, jurnalno,
            inputby, inputdate, updateby, updatedate,
            printby, printdate, COALESCE(printcount, 0), docnotmp
        FROM sc_tmp.proforma
        WHERE BTRIM(docno) = BTRIM(OLD.docno)
          AND inputby = v_inputby
          AND idurut = v_idurut;

        IF NOT FOUND THEN
            RAISE EXCEPTION
                'Header temporary Proforma tidak ditemukan. docno=%, inputby=%, idurut=%',
                OLD.docno, v_inputby, v_idurut;
        END IF;

        -- INSERT DETAIL
        INSERT INTO sc_trx.proformadtl (
            idurut_view, docno, invmode,
            docnopjo, idurutpjo, uniqueidpjo, uniqueid,
            docnoso, idrev, idurutso, uniqueidso,
            docnodo, idurutdo, uniqueiddo,
            idbarangref, nmbarangref, idbarang, nmbarang,
            idprincipal, idgudang, idspec, unit,
            qtyref, hargasatuanref,
            qty, harga, hargasatuan, multidisc, discount,
            usdmt, exchange, nilai, amount,
            idtax, currcode, kurs, nilaikonversi, nilaipajak, qtyretur,
            stokakhir, bomdesc, description,
            status, inputby, inputdate, updateby, updatedate,
            doctype, docnotmp
        )
        SELECT
            ROW_NUMBER() OVER (ORDER BY COALESCE(idurut_view, idurut), idurut)::INTEGER,
            v_docno, invmode,
            docnopjo, idurutpjo, uniqueidpjo, uniqueid,
            docnoso, idrev, idurutso, uniqueidso,
            docnodo, idurutdo, uniqueiddo,
            idbarangref, nmbarangref, idbarang, nmbarang,
            idprincipal, idgudang, idspec, unit,
            qtyref, hargasatuanref,
            qty, harga, hargasatuan, multidisc, discount,
            usdmt, exchange, nilai, amount,
            idtax, currcode, kurs, nilaikonversi, nilaipajak, COALESCE(qtyretur, 0),
            stokakhir, bomdesc, description,
            status, inputby, inputdate, updateby, updatedate,
            COALESCE(doctype, 'SALES'), docnotmp
        FROM sc_tmp.proformadtl
        WHERE BTRIM(docno) = BTRIM(OLD.docno)
          AND inputby = v_inputby;

        PERFORM sc_log.fn_log_transaction(
            v_docno::CHAR(30), NULL, 'I.S', 'I.S.B.5',
            CASE WHEN v_new_status = 'D' THEN 'D' ELSE 'I' END,
            v_inputby, v_client_ip, v_inputby
        );

        DELETE FROM sc_tmp.proformadtl
        WHERE BTRIM(docno) = BTRIM(OLD.docno) AND inputby = v_inputby;

        DELETE FROM sc_tmp.proforma
        WHERE BTRIM(docno) = BTRIM(OLD.docno)
          AND inputby = v_inputby
          AND idurut = v_idurut;

    -- ============================ PATH B: REVISI (UPDATE EXISTING) =======
    ELSE

        -- docnotmp = docno asli di sc_trx. Kita UPDATE, bukan hapus.

        -- 1) Update HEADER di sc_trx (status D/F)
        UPDATE sc_trx.proforma h
        SET
            invmode                     = t.invmode,
            docnopjo                    = t.docnopjo,
            docnodo                     = t.docnodo,
            docnoso                     = t.docnoso,
            idrev                       = t.idrev,
            rolejob                     = t.rolejob,
            docdate                     = t.docdate,
            pono                        = t.pono,
            podate                      = t.podate,
            jnsinvoice                  = t.jnsinvoice,
            doctype                     = t.doctype,
            kdcustomer                  = t.kdcustomer,
            nmcustomer                  = t.nmcustomer,
            cust                        = t.cust,
            address                     = t.address,
            phone                       = t.phone,
            fax                         = t.fax,
            alamatcustomer              = t.alamatcustomer,
            gradecustomer               = t.gradecustomer,
            kdcustomerdeliv             = t.kdcustomerdeliv,
            nmcustomerdeliv             = t.nmcustomerdeliv,
            alamatcustomerdeliv         = t.alamatcustomerdeliv,
            customerdestination         = t.customerdestination,
            customerdestination_addr    = t.customerdestination_addr,
            trader                      = t.trader,
            trader_addr                 = t.trader_addr,
            enduser                     = t.enduser,
            enduser_addr                = t.enduser_addr,
            cabang                      = t.cabang,
            pemohon                     = t.pemohon,
            kdsalesman                  = t.kdsalesman,
            facrisk                     = t.facrisk,
            shipper                     = t.shipper,
            consignee                   = t.consignee,
            shippingmark                = t.shippingmark,
            notifyparty                 = t.notifyparty,
            paymentmethod               = t.paymentmethod,
            bank                        = t.bank,
            carabayar                   = t.carabayar,
            jthtempo                    = t.jthtempo,
            idtax                       = t.idtax,
            isinclusive                 = t.isinclusive,
            isopenprice                 = t.isopenprice,
            currcode                    = t.currcode,
            kurs                        = t.kurs,
            grosssales                  = t.grosssales,
            downpayment                 = t.downpayment,
            netsales                    = t.netsales,
            taxbasis                    = t.taxbasis,
            vat                         = t.vat,
            pph22                       = t.pph22,
            dpp                         = t.dpp,
            jumlahpajak                 = t.jumlahpajak,
            total                       = t.total,
            ttlprice                    = t.ttlprice,
            nmbank                      = t.nmbank,
            alamatbank                  = t.alamatbank,
            kodeposbank                 = t.kodeposbank,
            accname                     = t.accname,
            accno                       = t.accno,
            swiftcode                   = t.swiftcode,
            description                 = t.description,
            keterangan                  = t.keterangan,
            brand                       = t.brand,
            size                        = t.size,
            qty                         = t.qty,
            pembayaran                  = t.pembayaran,
            pengiriman                  = t.pengiriman,
            expdate                     = t.expdate,
            ketentuan                   = t.ketentuan,
            status                      = v_new_status,
            jurnalno                    = t.jurnalno,
            updateby                    = t.updateby,
            updatedate                  = t.updatedate,
            printby                     = t.printby,
            printdate                   = t.printdate,
            printcount                  = COALESCE(t.printcount, 0)
        FROM sc_tmp.proforma t
        WHERE BTRIM(h.docno) = BTRIM(v_docnotmp)
          AND BTRIM(t.docno) = BTRIM(NEW.docno);

        IF NOT FOUND THEN
            RAISE EXCEPTION 'Header transaksi Proforma tidak ditemukan untuk update: %', v_docnotmp;
        END IF;

        -- 2) Hapus detail lama di sc_trx
        DELETE FROM sc_trx.proformadtl
        WHERE BTRIM(docno) = BTRIM(v_docnotmp);

        -- 3) Insert ulang detail dari sc_tmp
        INSERT INTO sc_trx.proformadtl (
            idurut_view, docno, invmode,
            docnopjo, idurutpjo, uniqueidpjo, uniqueid,
            docnoso, idrev, idurutso, uniqueidso,
            docnodo, idurutdo, uniqueiddo,
            idbarangref, nmbarangref, idbarang, nmbarang,
            idprincipal, idgudang, idspec, unit,
            qtyref, hargasatuanref,
            qty, harga, hargasatuan, multidisc, discount,
            usdmt, exchange, nilai, amount,
            idtax, currcode, kurs, nilaikonversi, nilaipajak, qtyretur,
            stokakhir, bomdesc, description,
            status, inputby, inputdate, updateby, updatedate,
            doctype, docnotmp
        )
        SELECT
            ROW_NUMBER() OVER (ORDER BY COALESCE(idurut_view, idurut), idurut)::INTEGER,
            v_docnotmp, invmode,
            docnopjo, idurutpjo, uniqueidpjo, uniqueid,
            docnoso, idrev, idurutso, uniqueidso,
            docnodo, idurutdo, uniqueiddo,
            idbarangref, nmbarangref, idbarang, nmbarang,
            idprincipal, idgudang, idspec, unit,
            qtyref, hargasatuanref,
            qty, harga, hargasatuan, multidisc, discount,
            usdmt, exchange, nilai, amount,
            idtax, currcode, kurs, nilaikonversi, nilaipajak, COALESCE(qtyretur, 0),
            stokakhir, bomdesc, description,
            status, inputby, inputdate, updateby, updatedate,
            COALESCE(doctype, 'SALES'), docnotmp
        FROM sc_tmp.proformadtl
        WHERE BTRIM(docno) = BTRIM(NEW.docno);

        PERFORM sc_log.fn_log_transaction(
            NEW.docno, NULL, 'I.S', 'I.S.B.5',
            CASE WHEN v_new_status = 'D' THEN 'D' ELSE 'U' END,
            COALESCE(NEW.updateby, NEW.inputby),
            v_client_ip,
            COALESCE(NEW.updateby, NEW.inputby)
        );

        DELETE FROM sc_tmp.proformadtl WHERE BTRIM(docno) = BTRIM(NEW.docno);
        DELETE FROM sc_tmp.proforma     WHERE BTRIM(docno) = BTRIM(NEW.docno);
    END IF;

    RETURN NEW;
END;
$BODY$;

ALTER FUNCTION sc_tmp.tr_proforma_finalize() OWNER TO postgres;

-- ============================================================================
-- 7. FUNCTION: sc_trx.tr_proforma()
--    AFTER UPDATE ON sc_trx.proforma
--
--    D -> F : Finalize Draft (UPDATE status F)
--    D -> C : Cancel Draft (UPDATE status C, data tetap)
--    F -> C : Cancel Final (UPDATE status C, data tetap)
--    D -> E : Revisi Draft (COPY ke tmp, data tetap)
--    F -> E : Revisi Final (COPY ke tmp, data tetap)
-- ============================================================================
CREATE OR REPLACE FUNCTION sc_trx.tr_proforma()
RETURNS trigger
LANGUAGE plpgsql
COST 100
VOLATILE
AS $BODY$
DECLARE
    v_client_ip TEXT;
    v_inputby   TEXT;
BEGIN
    IF OLD.status IS NOT DISTINCT FROM NEW.status THEN
        RETURN NEW;
    END IF;

    v_inputby   := COALESCE(NEW.updateby, NEW.inputby);
    v_client_ip := sc_log.fn_get_user_ip(v_inputby);

    -- ============================ D -> F (Finalize Draft) ===============
    IF OLD.status = 'D' AND NEW.status = 'F' THEN
        PERFORM sc_log.fn_log_transaction(
            NEW.docno, NULL, 'I.S', 'I.S.B.5', 'U',
            v_inputby, v_client_ip, v_inputby
        );
        RETURN NEW;
    END IF;

    -- ============================ D -> C (Cancel Draft) =================
    IF OLD.status = 'D' AND NEW.status = 'C' THEN
        PERFORM sc_log.fn_log_transaction(
            NEW.docno, NULL, 'I.S', 'I.S.B.5', 'C',
            v_inputby, v_client_ip, v_inputby
        );
        RETURN NEW;
    END IF;

    -- ============================ F -> C (Cancel Final) =================
    IF OLD.status = 'F' AND NEW.status = 'C' THEN
        -- Data sc_trx TETAP (status C), tidak dihapus.
        PERFORM sc_log.fn_log_transaction(
            NEW.docno, NULL, 'I.S', 'I.S.B.5', 'C',
            v_inputby, v_client_ip, v_inputby
        );
        RETURN NEW;
    END IF;

    -- ============================ D -> E atau F -> E (REVISI) ===========
    -- PENTING: JANGAN hapus sc_trx. Cukup COPY ke sc_tmp.
    IF NEW.status = 'E' AND OLD.status IN ('D', 'F') THEN

        -- Bersihkan draft lama di tmp
        DELETE FROM sc_tmp.proformadtl WHERE BTRIM(docno) = BTRIM(NEW.docno);
        DELETE FROM sc_tmp.proforma     WHERE BTRIM(docno) = BTRIM(NEW.docno);

        -- Insert HEADER ke tmp
        INSERT INTO sc_tmp.proforma (
            idurut_view, docno, invmode, docnopjo, docnodo, docnoso, idrev,
            rolejob, docdate, pono, podate, jnsinvoice, doctype,
            kdcustomer, nmcustomer, cust, address, phone, fax,
            alamatcustomer, gradecustomer,
            kdcustomerdeliv, nmcustomerdeliv, alamatcustomerdeliv,
            customerdestination, customerdestination_addr,
            trader, trader_addr, enduser, enduser_addr,
            cabang, pemohon, kdsalesman,
            facrisk, shipper, consignee, shippingmark, notifyparty,
            paymentmethod, bank, carabayar, jthtempo,
            idtax, isinclusive, isopenprice, currcode, kurs,
            grosssales, downpayment, netsales, taxbasis, vat, pph22,
            dpp, jumlahpajak, total, ttlprice,
            nmbank, alamatbank, kodeposbank, accname, accno, swiftcode,
            description, keterangan, brand, size, qty, pembayaran, pengiriman,
            expdate, ketentuan, status, jurnalno,
            inputby, inputdate, updateby, updatedate,
            printby, printdate, printcount, docnotmp
        )
        SELECT
            COALESCE(h.idurut_view, h.idurut),
            h.docno, h.invmode, h.docnopjo, h.docnodo, h.docnoso, h.idrev,
            h.rolejob, h.docdate, h.pono, h.podate, h.jnsinvoice, h.doctype,
            h.kdcustomer, h.nmcustomer, h.cust, h.address, h.phone, h.fax,
            h.alamatcustomer, h.gradecustomer,
            h.kdcustomerdeliv, h.nmcustomerdeliv, h.alamatcustomerdeliv,
            h.customerdestination, h.customerdestination_addr,
            h.trader, h.trader_addr, h.enduser, h.enduser_addr,
            h.cabang, h.pemohon, h.kdsalesman,
            h.facrisk, h.shipper, h.consignee, h.shippingmark, h.notifyparty,
            h.paymentmethod, h.bank, h.carabayar, h.jthtempo,
            h.idtax, h.isinclusive, h.isopenprice, h.currcode, h.kurs,
            h.grosssales, h.downpayment, h.netsales, h.taxbasis, h.vat, h.pph22,
            h.dpp, h.jumlahpajak, h.total, h.ttlprice,
            h.nmbank, h.alamatbank, h.kodeposbank, h.accname, h.accno, h.swiftcode,
            h.description, h.keterangan, h.brand, h.size, h.qty, h.pembayaran, h.pengiriman,
            h.expdate, h.ketentuan, 'E', h.jurnalno,
            h.inputby, h.inputdate, h.updateby, h.updatedate,
            h.printby, h.printdate, COALESCE(h.printcount, 0),
            h.docno
        FROM sc_trx.proforma h
        WHERE BTRIM(h.docno) = BTRIM(NEW.docno);

        IF NOT FOUND THEN
            RAISE EXCEPTION
                'Header transaksi Proforma tidak ditemukan saat revisi ->E: %',
                NEW.docno;
        END IF;

        -- Insert DETAIL ke tmp (idurut_view 1..N)
        INSERT INTO sc_tmp.proformadtl (
            idurut_view, docno, invmode,
            docnopjo, idurutpjo, uniqueidpjo, uniqueid,
            docnoso, idrev, idurutso, uniqueidso,
            docnodo, idurutdo, uniqueiddo,
            idbarangref, nmbarangref, idbarang, nmbarang,
            idprincipal, idgudang, idspec, unit,
            qtyref, hargasatuanref,
            qty, harga, hargasatuan, multidisc, discount,
            usdmt, exchange, nilai, amount,
            idtax, currcode, kurs, nilaikonversi, nilaipajak, qtyretur,
            stokakhir, bomdesc, description,
            status, inputby, inputdate, updateby, updatedate,
            doctype, docnotmp
        )
        SELECT
            ROW_NUMBER() OVER (
                ORDER BY COALESCE(d.idurut_view, d.idurut), d.idurut
            )::INTEGER,
            NEW.docno, d.invmode,
            d.docnopjo, d.idurutpjo, d.uniqueidpjo, d.uniqueid,
            d.docnoso, d.idrev, d.idurutso, d.uniqueidso,
            d.docnodo, d.idurutdo, d.uniqueiddo,
            d.idbarangref, d.nmbarangref, d.idbarang, d.nmbarang,
            d.idprincipal, d.idgudang, d.idspec, d.unit,
            d.qtyref, d.hargasatuanref,
            d.qty, d.harga, d.hargasatuan, d.multidisc, d.discount,
            d.usdmt, d.exchange, d.nilai, d.amount,
            d.idtax, d.currcode, d.kurs, d.nilaikonversi, d.nilaipajak, COALESCE(d.qtyretur, 0),
            d.stokakhir, d.bomdesc, d.description,
            COALESCE(d.status, 'F'), d.inputby, d.inputdate, d.updateby, d.updatedate,
            COALESCE(d.doctype, 'SALES'), NEW.docno
        FROM sc_trx.proformadtl d
        WHERE BTRIM(d.docno) = BTRIM(NEW.docno);

        -- TIDAK ADA DELETE dari sc_trx -- histori tetap ada dengan status E.
    END IF;

    RETURN NEW;
END;
$BODY$;

ALTER FUNCTION sc_trx.tr_proforma() OWNER TO postgres;

-- ============================================================================
-- 8. PASANG TRIGGER
-- ============================================================================
CREATE TRIGGER tr_proforma_finalize
    AFTER UPDATE ON sc_tmp.proforma
    FOR EACH ROW
    EXECUTE FUNCTION sc_tmp.tr_proforma_finalize();

CREATE TRIGGER tr_proforma
    AFTER UPDATE ON sc_trx.proforma
    FOR EACH ROW
    EXECUTE FUNCTION sc_trx.tr_proforma();

COMMIT;

-- ============================================================================
-- 9. VERIFIKASI (jalankan manual setelah COMMIT)
-- ============================================================================
-- -- Cek trigger:
-- SELECT n.nspname AS schema_name, c.relname AS table_name,
--        t.tgname AS trigger_name, pg_get_triggerdef(t.oid) AS trigger_definition
-- FROM pg_trigger t
-- JOIN pg_class c ON c.oid = t.tgrelid
-- JOIN pg_namespace n ON n.oid = c.relnamespace
-- WHERE NOT t.tgisinternal
--   AND n.nspname IN ('sc_tmp','sc_trx')
--   AND c.relname IN ('proforma','proformadtl')
-- ORDER BY n.nspname, c.relname, t.tgname;
--
-- -- Cek status draft:
-- SELECT docno, status, invmode, docdate, kdcustomer, total
-- FROM sc_trx.proforma
-- WHERE status IN ('D','E','C')
-- ORDER BY idurut_view, idurut;