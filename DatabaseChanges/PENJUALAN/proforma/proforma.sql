BEGIN;

-- =========================================================
-- DROP OBJECT LAMA
-- =========================================================

DROP TABLE IF EXISTS sc_tmp.proforma CASCADE;
DROP TABLE IF EXISTS sc_trx.proforma CASCADE;

DROP SEQUENCE IF EXISTS sc_tmp.proforma_idurut_seq CASCADE;
DROP SEQUENCE IF EXISTS sc_trx.proforma_idurut_seq CASCADE;


-- =========================================================
-- SEQUENCE : SC_TMP.PROFORMA
-- =========================================================

CREATE SEQUENCE sc_tmp.proforma_idurut_seq
    INCREMENT 1
    START 1
    MINVALUE 1
    MAXVALUE 2147483647
    CACHE 1;

ALTER SEQUENCE sc_tmp.proforma_idurut_seq
    OWNER TO postgres;


-- =========================================================
-- TABLE : SC_TMP.PROFORMA
-- =========================================================

CREATE TABLE sc_tmp.proforma
(
    idurut integer NOT NULL
        DEFAULT nextval('sc_tmp.proforma_idurut_seq'::regclass),

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

    grosssales numeric(18,2),

    downpayment numeric(18,2),

    netsales numeric(18,2),

    taxbasis numeric(18,2),

    vat numeric(18,2),

    pph22 numeric(18,2),

    ttlprice numeric(18,2),

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

    CONSTRAINT pk_tmp_proforma
        PRIMARY KEY (idurut, docno)
)
TABLESPACE pg_default;

ALTER TABLE sc_tmp.proforma
    OWNER TO postgres;

ALTER SEQUENCE sc_tmp.proforma_idurut_seq
    OWNED BY sc_tmp.proforma.idurut;


-- =========================================================
-- SEQUENCE : SC_TRX.PROFORMA
-- =========================================================

CREATE SEQUENCE sc_trx.proforma_idurut_seq
    INCREMENT 1
    START 1
    MINVALUE 1
    MAXVALUE 2147483647
    CACHE 1;

ALTER SEQUENCE sc_trx.proforma_idurut_seq
    OWNER TO postgres;


-- =========================================================
-- TABLE : SC_TRX.PROFORMA
-- =========================================================

CREATE TABLE sc_trx.proforma
(
    idurut integer NOT NULL
        DEFAULT nextval('sc_trx.proforma_idurut_seq'::regclass),

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

    grosssales numeric(18,2),

    downpayment numeric(18,2),

    netsales numeric(18,2),

    taxbasis numeric(18,2),

    vat numeric(18,2),

    pph22 numeric(18,2),

    ttlprice numeric(18,2),

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

    CONSTRAINT proforma_pkey
        PRIMARY KEY (docno),

    CONSTRAINT proforma_idurut_key
        UNIQUE (idurut)
)
TABLESPACE pg_default;

ALTER TABLE sc_trx.proforma
    OWNER TO postgres;

ALTER SEQUENCE sc_trx.proforma_idurut_seq
    OWNED BY sc_trx.proforma.idurut;


-- =========================================================
-- SELESAI
-- =========================================================

COMMIT;