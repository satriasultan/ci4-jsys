BEGIN;

-- =============================================================
-- PROFORMA / INVOICE (PRESALES)
-- Satu halaman: header + detail (tmp/trx), kolom PJO, trigger.
-- TIDAK ADA posting jurnal / stok / transaction_dt.
-- Idempotent: CREATE IF NOT EXISTS + ADD COLUMN IF NOT EXISTS.
-- =============================================================

CREATE SEQUENCE IF NOT EXISTS sc_tmp.proforma_idurut_seq
    INCREMENT 1 START 1 MINVALUE 1 MAXVALUE 2147483647 CACHE 1;

CREATE SEQUENCE IF NOT EXISTS sc_trx.proforma_idurut_seq
    INCREMENT 1 START 1 MINVALUE 1 MAXVALUE 2147483647 CACHE 1;

-- =============================================================
-- HEADER TMP
-- =============================================================

CREATE TABLE IF NOT EXISTS sc_tmp.proforma
(
    idurut integer NOT NULL DEFAULT nextval('sc_tmp.proforma_idurut_seq'::regclass),
    docno character(30) NOT NULL,
    rolejob character(10),
    docdate character(20),
    pono character(30),
    podate character(20),
    jnsinvoice character(20),
    cust character(100),
    address text,
    phone character varying(50),
    fax character varying(50),
    facrisk text,
    shipper text,
    consignee text,
    shippingmark text,
    notifyparty text,
    paymentmethod character varying(50),
    bank character varying(50),
    grosssales numeric(18,4),
    downpayment numeric(18,4),
    netsales numeric(18,4),
    taxbasis numeric(18,4),
    vat numeric(18,4),
    pph22 numeric(18,4),
    ttlprice numeric(18,4),
    nmbank character(100),
    alamatbank text,
    kodeposbank character(100),
    accname character(100),
    accno text,
    swiftcode character(50),
    description text,
    brand character(50),
    size text,
    qty character(100),
    pembayaran text,
    pengiriman text,
    expdate character(20),
    ketentuan text,
    status character(6),
    inputby character varying(50),
    inputdate timestamp without time zone,
    updateby character varying(50),
    updatedate timestamp without time zone,
    printby character varying(50),
    printdate timestamp without time zone,
    docnotmp character(30),
    docnopjo character(30),
    jurnalno character(50),
    nmcustomer character(250),
    idtax character(20),
    isinclusive character(6),
    currcode character(3),
    kurs numeric(18,4) DEFAULT 1,
    dpp numeric(18,4),
    jumlahpajak numeric(18,4),
    total numeric(18,4),
    CONSTRAINT pk_tmp_proforma PRIMARY KEY (idurut, docno)
);

-- =============================================================
-- HEADER TRX
-- =============================================================

CREATE TABLE IF NOT EXISTS sc_trx.proforma
(
    idurut integer NOT NULL DEFAULT nextval('sc_trx.proforma_idurut_seq'::regclass),
    docno character(30) NOT NULL,
    rolejob character(10),
    docdate character(20),
    pono character(30),
    podate character(20),
    jnsinvoice character(20),
    cust character(100),
    address text,
    phone character varying(50),
    fax character varying(50),
    facrisk text,
    shipper text,
    consignee text,
    shippingmark text,
    notifyparty text,
    paymentmethod character varying(50),
    bank character varying(50),
    grosssales numeric(18,4),
    downpayment numeric(18,4),
    netsales numeric(18,4),
    taxbasis numeric(18,4),
    vat numeric(18,4),
    pph22 numeric(18,4),
    ttlprice numeric(18,4),
    nmbank character(100),
    alamatbank text,
    kodeposbank character(100),
    accname character(100),
    accno text,
    swiftcode character(50),
    description text,
    brand character(50),
    size text,
    qty character(100),
    pembayaran text,
    pengiriman text,
    expdate character(20),
    ketentuan text,
    status character(6),
    inputby character varying(50),
    inputdate timestamp without time zone,
    updateby character varying(50),
    updatedate timestamp without time zone,
    printby character varying(50),
    printdate timestamp without time zone,
    docnotmp character(30),
    docnopjo character(30),
    jurnalno character(50),
    nmcustomer character(250),
    idtax character(20),
    isinclusive character(6),
    currcode character(3),
    kurs numeric(18,4) DEFAULT 1,
    dpp numeric(18,4),
    jumlahpajak numeric(18,4),
    total numeric(18,4),
    CONSTRAINT proforma_pkey PRIMARY KEY (docno),
    CONSTRAINT proforma_idurut_key UNIQUE (idurut)
);


-- =============================================================
-- DETAIL TMP
-- =============================================================

CREATE TABLE IF NOT EXISTS sc_tmp.proformadtl
(
    idurut serial NOT NULL,
    docno character(30) NOT NULL,
    idbarang character(20),
    nmbarang character(150),
    unit character(20),
    qty numeric(18,4),
    price numeric(18,4),
    usdmt numeric(18,4),
    exchange numeric(18,4),
    amount numeric(18,4),
    description text,
    status character(6),
    inputby character varying(50),
    inputdate timestamp without time zone,
    updateby character varying(50),
    updatedate timestamp without time zone,
    docnotmp character(30),
    docnopjo character(30),
    idurutpjo integer,
    idtax character(20),
    idgudang character(30),
    idspec character(30),
    nilaikonversi numeric(18,4),
    nilaipajak numeric(18,4),
    discount numeric(18,4),
    CONSTRAINT proformadtl_tmp_pkey PRIMARY KEY (idurut)
);

-- =============================================================
-- DETAIL TRX
-- =============================================================

CREATE TABLE IF NOT EXISTS sc_trx.proformadtl
(
    idurut serial NOT NULL,
    docno character(30) NOT NULL,
    idbarang character(20),
    nmbarang character(150),
    unit character(20),
    qty numeric(18,4),
    price numeric(18,4),
    usdmt numeric(18,4),
    exchange numeric(18,4),
    amount numeric(18,4),
    description text,
    status character(6),
    inputby character varying(50),
    inputdate timestamp without time zone,
    updateby character varying(50),
    updatedate timestamp without time zone,
    docnotmp character(30),
    docnopjo character(30),
    idurutpjo integer,
    idtax character(20),
    idgudang character(30),
    idspec character(30),
    nilaikonversi numeric(18,4),
    nilaipajak numeric(18,4),
    discount numeric(18,4),
    CONSTRAINT proformadtl_pkey PRIMARY KEY (idurut)
);

-- =============================================================
-- TAMBAH KOLOM HEADER (TABEL SUDAH ADA)
-- =============================================================

ALTER TABLE sc_tmp.proforma ADD COLUMN IF NOT EXISTS docnopjo character(30);
ALTER TABLE sc_tmp.proforma ADD COLUMN IF NOT EXISTS jurnalno character(50);
ALTER TABLE sc_tmp.proforma ADD COLUMN IF NOT EXISTS nmcustomer character(250);
ALTER TABLE sc_tmp.proforma ADD COLUMN IF NOT EXISTS idtax character(20);
ALTER TABLE sc_tmp.proforma ADD COLUMN IF NOT EXISTS isinclusive character(6);
ALTER TABLE sc_tmp.proforma ADD COLUMN IF NOT EXISTS currcode character(3);
ALTER TABLE sc_tmp.proforma ADD COLUMN IF NOT EXISTS kurs numeric(18,4) DEFAULT 1;
ALTER TABLE sc_tmp.proforma ADD COLUMN IF NOT EXISTS dpp numeric(18,4);
ALTER TABLE sc_tmp.proforma ADD COLUMN IF NOT EXISTS jumlahpajak numeric(18,4);
ALTER TABLE sc_tmp.proforma ADD COLUMN IF NOT EXISTS total numeric(18,4);

ALTER TABLE sc_trx.proforma ADD COLUMN IF NOT EXISTS docnopjo character(30);
ALTER TABLE sc_trx.proforma ADD COLUMN IF NOT EXISTS jurnalno character(50);
ALTER TABLE sc_trx.proforma ADD COLUMN IF NOT EXISTS nmcustomer character(250);
ALTER TABLE sc_trx.proforma ADD COLUMN IF NOT EXISTS idtax character(20);
ALTER TABLE sc_trx.proforma ADD COLUMN IF NOT EXISTS isinclusive character(6);
ALTER TABLE sc_trx.proforma ADD COLUMN IF NOT EXISTS currcode character(3);
ALTER TABLE sc_trx.proforma ADD COLUMN IF NOT EXISTS kurs numeric(18,4) DEFAULT 1;
ALTER TABLE sc_trx.proforma ADD COLUMN IF NOT EXISTS dpp numeric(18,4);
ALTER TABLE sc_trx.proforma ADD COLUMN IF NOT EXISTS jumlahpajak numeric(18,4);
ALTER TABLE sc_trx.proforma ADD COLUMN IF NOT EXISTS total numeric(18,4);

-- =============================================================
-- TAMBAH KOLOM DETAIL (TABEL LAMA)
-- =============================================================

ALTER TABLE sc_tmp.proformadtl ADD COLUMN IF NOT EXISTS docnopjo character(30);
ALTER TABLE sc_tmp.proformadtl ADD COLUMN IF NOT EXISTS idurutpjo integer;
ALTER TABLE sc_tmp.proformadtl ADD COLUMN IF NOT EXISTS idtax character(20);
ALTER TABLE sc_tmp.proformadtl ADD COLUMN IF NOT EXISTS idgudang character(30);
ALTER TABLE sc_tmp.proformadtl ADD COLUMN IF NOT EXISTS idspec character(30);
ALTER TABLE sc_tmp.proformadtl ADD COLUMN IF NOT EXISTS nilaikonversi numeric(18,4);
ALTER TABLE sc_tmp.proformadtl ADD COLUMN IF NOT EXISTS nilaipajak numeric(18,4);
ALTER TABLE sc_tmp.proformadtl ADD COLUMN IF NOT EXISTS discount numeric(18,4);

ALTER TABLE sc_trx.proformadtl ADD COLUMN IF NOT EXISTS docnopjo character(30);
ALTER TABLE sc_trx.proformadtl ADD COLUMN IF NOT EXISTS idurutpjo integer;
ALTER TABLE sc_trx.proformadtl ADD COLUMN IF NOT EXISTS idtax character(20);
ALTER TABLE sc_trx.proformadtl ADD COLUMN IF NOT EXISTS idgudang character(30);
ALTER TABLE sc_trx.proformadtl ADD COLUMN IF NOT EXISTS idspec character(30);
ALTER TABLE sc_trx.proformadtl ADD COLUMN IF NOT EXISTS nilaikonversi numeric(18,4);
ALTER TABLE sc_trx.proformadtl ADD COLUMN IF NOT EXISTS nilaipajak numeric(18,4);
ALTER TABLE sc_trx.proformadtl ADD COLUMN IF NOT EXISTS discount numeric(18,4);

ALTER TABLE sc_tmp.proformadtl ALTER COLUMN qty TYPE numeric(18,4);
ALTER TABLE sc_tmp.proformadtl ALTER COLUMN price TYPE numeric(18,4);
ALTER TABLE sc_tmp.proformadtl ALTER COLUMN usdmt TYPE numeric(18,4);
ALTER TABLE sc_tmp.proformadtl ALTER COLUMN exchange TYPE numeric(18,4);
ALTER TABLE sc_tmp.proformadtl ALTER COLUMN amount TYPE numeric(18,4);

ALTER TABLE sc_trx.proformadtl ALTER COLUMN qty TYPE numeric(18,4);
ALTER TABLE sc_trx.proformadtl ALTER COLUMN price TYPE numeric(18,4);
ALTER TABLE sc_trx.proformadtl ALTER COLUMN usdmt TYPE numeric(18,4);
ALTER TABLE sc_trx.proformadtl ALTER COLUMN exchange TYPE numeric(18,4);
ALTER TABLE sc_trx.proformadtl ALTER COLUMN amount TYPE numeric(18,4);

-- =============================================================
-- INDEX
-- =============================================================

CREATE INDEX IF NOT EXISTS idx_tmp_proforma_docno ON sc_tmp.proforma (docno);
CREATE INDEX IF NOT EXISTS idx_trx_proforma_docno ON sc_trx.proforma (docno);
CREATE INDEX IF NOT EXISTS idx_tmp_proforma_pjo ON sc_tmp.proforma (docnopjo);
CREATE INDEX IF NOT EXISTS idx_trx_proforma_pjo ON sc_trx.proforma (docnopjo);
CREATE INDEX IF NOT EXISTS idx_tmp_proformadtl_docno ON sc_tmp.proformadtl (docno);
CREATE INDEX IF NOT EXISTS idx_trx_proformadtl_docno ON sc_trx.proformadtl (docno);
CREATE INDEX IF NOT EXISTS idx_tmp_proformadtl_pjo ON sc_tmp.proformadtl (docnopjo);
CREATE INDEX IF NOT EXISTS idx_trx_proformadtl_pjo ON sc_trx.proformadtl (docnopjo);


-- =============================================================
-- TRIGGER FINAL: sc_tmp.proforma E -> F
-- =============================================================

CREATE OR REPLACE FUNCTION sc_tmp.tr_proforma_finalize()
RETURNS trigger
LANGUAGE plpgsql
AS $BODY$
DECLARE
    v_docno      TEXT;
    v_docnotmp   TEXT;
BEGIN

    IF TRIM(OLD.status) = 'E' AND TRIM(NEW.status) = 'F' THEN

        v_docno    := RTRIM(NEW.docno);
        v_docnotmp := RTRIM(COALESCE(NULLIF(NEW.docnotmp, ''), NEW.docno));

        -- Jika bukan mode edit (docnotmp kosong), jangan menimpa dokumen lain.
        IF COALESCE(BTRIM(NEW.docnotmp), '') = ''
           AND EXISTS (
               SELECT 1 FROM sc_trx.proforma
               WHERE RTRIM(docno) = RTRIM(v_docno)
           )
        THEN
            RAISE EXCEPTION 'Docno proforma % sudah ada. Gunakan nomor lain.', v_docno;
        END IF;

        -- Mode edit: hapus dokumen lama dengan docnotmp yang sama.
        IF COALESCE(BTRIM(NEW.docnotmp), '') <> '' THEN
            DELETE FROM sc_trx.proformadtl
            WHERE RTRIM(docno) = RTRIM(v_docno);

            DELETE FROM sc_trx.proforma
            WHERE RTRIM(docno) = RTRIM(v_docno);
        END IF;

        INSERT INTO sc_trx.proforma
        (
            docno, rolejob, docdate, pono, podate, jnsinvoice,
            cust, address, phone, fax, facrisk, shipper, consignee,
            shippingmark, notifyparty, paymentmethod, bank,
            grosssales, downpayment, netsales, taxbasis, vat, pph22, ttlprice,
            nmbank, alamatbank, kodeposbank, accname, accno, swiftcode,
            description, brand, size, qty, pembayaran, pengiriman,
            expdate, ketentuan, status, inputby, inputdate, updateby,
            updatedate, printby, printdate, docnotmp,
            docnopjo, jurnalno, nmcustomer, idtax, isinclusive,
            currcode, kurs, dpp, jumlahpajak, total
        )
        SELECT
            v_docno, rolejob, docdate, pono, podate, jnsinvoice,
            cust, address, phone, fax, facrisk, shipper, consignee,
            shippingmark, notifyparty, paymentmethod, bank,
            grosssales, downpayment, netsales, taxbasis, vat, pph22, ttlprice,
            nmbank, alamatbank, kodeposbank, accname, accno, swiftcode,
            description, brand, size, qty, pembayaran, pengiriman,
            expdate, ketentuan, 'F', inputby, inputdate, updateby,
            updatedate, printby, printdate, v_docnotmp,
            docnopjo, jurnalno, nmcustomer, idtax, isinclusive,
            currcode, kurs, dpp, jumlahpajak, total
        FROM sc_tmp.proforma
        WHERE RTRIM(docno) = RTRIM(OLD.docno)
          AND COALESCE(inputby, '') = COALESCE(NEW.inputby, '')
          AND idurut = NEW.idurut;

        INSERT INTO sc_trx.proformadtl
        (
            docno, idbarang, nmbarang, unit, qty, price, usdmt, exchange,
            amount, description, status, inputby, inputdate, updateby,
            updatedate, docnotmp, docnopjo, idurutpjo, idtax, idgudang,
            idspec, nilaikonversi, nilaipajak, discount
        )
        SELECT
            v_docno, idbarang, nmbarang, unit, qty, price, usdmt, exchange,
            amount, description, status, inputby, inputdate, updateby,
            updatedate, v_docnotmp, docnopjo, idurutpjo, idtax, idgudang,
            idspec, nilaikonversi, nilaipajak, discount
        FROM sc_tmp.proformadtl
        WHERE RTRIM(docno) = RTRIM(OLD.docno)
          AND COALESCE(inputby, '') = COALESCE(NEW.inputby, '');

        DELETE FROM sc_tmp.proformadtl
        WHERE RTRIM(docno) = RTRIM(OLD.docno);

        DELETE FROM sc_tmp.proforma
        WHERE RTRIM(docno) = RTRIM(OLD.docno)
          AND idurut = NEW.idurut;

    ELSIF TRIM(OLD.status) = 'E' AND TRIM(NEW.status) = 'C' THEN

        DELETE FROM sc_tmp.proformadtl
        WHERE RTRIM(docno) = RTRIM(OLD.docno);

        DELETE FROM sc_tmp.proforma
        WHERE RTRIM(docno) = RTRIM(OLD.docno);

    END IF;

    RETURN NEW;
END;
$BODY$;


-- =============================================================
-- TRIGGER EDIT: sc_trx.proforma F -> E
-- =============================================================

CREATE OR REPLACE FUNCTION sc_trx.tr_proforma()
RETURNS trigger
LANGUAGE plpgsql
AS $BODY$
BEGIN

    IF TRIM(OLD.status) IN ('F','P') AND TRIM(NEW.status) = 'E' THEN

        DELETE FROM sc_tmp.proformadtl
        WHERE RTRIM(docno) = RTRIM(NEW.docno);

        DELETE FROM sc_tmp.proforma
        WHERE RTRIM(docno) = RTRIM(NEW.docno);

        INSERT INTO sc_tmp.proforma
        (
            docno, rolejob, docdate, pono, podate, jnsinvoice,
            cust, address, phone, fax, facrisk, shipper, consignee,
            shippingmark, notifyparty, paymentmethod, bank,
            grosssales, downpayment, netsales, taxbasis, vat, pph22, ttlprice,
            nmbank, alamatbank, kodeposbank, accname, accno, swiftcode,
            description, brand, size, qty, pembayaran, pengiriman,
            expdate, ketentuan, status, inputby, inputdate, updateby,
            updatedate, printby, printdate, docnotmp,
            docnopjo, jurnalno, nmcustomer, idtax, isinclusive,
            currcode, kurs, dpp, jumlahpajak, total
        )
        SELECT
            NEW.docno, NEW.rolejob, NEW.docdate, NEW.pono, NEW.podate, NEW.jnsinvoice,
            NEW.cust, NEW.address, NEW.phone, NEW.fax, NEW.facrisk, NEW.shipper, NEW.consignee,
            NEW.shippingmark, NEW.notifyparty, NEW.paymentmethod, NEW.bank,
            NEW.grosssales, NEW.downpayment, NEW.netsales, NEW.taxbasis, NEW.vat,
            NEW.pph22, NEW.ttlprice,
            NEW.nmbank, NEW.alamatbank, NEW.kodeposbank, NEW.accname, NEW.accno, NEW.swiftcode,
            NEW.description, NEW.brand, NEW.size, NEW.qty, NEW.pembayaran, NEW.pengiriman,
            NEW.expdate, NEW.ketentuan, 'E', NEW.inputby, NEW.inputdate, NEW.updateby,
            NEW.updatedate, NEW.printby, NEW.printdate, NEW.docno,
            NEW.docnopjo, NEW.jurnalno, NEW.nmcustomer, NEW.idtax, NEW.isinclusive,
            NEW.currcode, NEW.kurs, NEW.dpp, NEW.jumlahpajak, NEW.total;

        INSERT INTO sc_tmp.proformadtl
        (
            docno, idbarang, nmbarang, unit, qty, price, usdmt, exchange,
            amount, description, status, inputby, inputdate, updateby,
            updatedate, docnotmp, docnopjo, idurutpjo, idtax, idgudang,
            idspec, nilaikonversi, nilaipajak, discount
        )
        SELECT
            NEW.docno, idbarang, nmbarang, unit, qty, price, usdmt, exchange,
            amount, description, status, inputby, inputdate, updateby,
            updatedate, NEW.docno, docnopjo, idurutpjo, idtax, idgudang,
            idspec, nilaikonversi, nilaipajak, discount
        FROM sc_trx.proformadtl
        WHERE RTRIM(docno) = RTRIM(NEW.docno);

    END IF;

    RETURN NEW;
END;
$BODY$;

-- =============================================================
-- TRIGGER
-- =============================================================

DROP TRIGGER IF EXISTS tr_proforma_finalize ON sc_tmp.proforma;
CREATE TRIGGER tr_proforma_finalize
    AFTER UPDATE ON sc_tmp.proforma
    FOR EACH ROW
    EXECUTE FUNCTION sc_tmp.tr_proforma_finalize();

DROP TRIGGER IF EXISTS tr_proforma ON sc_trx.proforma;
CREATE TRIGGER tr_proforma
    AFTER UPDATE ON sc_trx.proforma
    FOR EACH ROW
    EXECUTE FUNCTION sc_trx.tr_proforma();

COMMIT;
