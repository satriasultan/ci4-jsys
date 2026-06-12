-- SEQUENCE: sc_tmp.proforma_idurut_seq

-- DROP SEQUENCE IF EXISTS sc_tmp.proforma_idurut_seq;

CREATE SEQUENCE IF NOT EXISTS sc_tmp.proforma_idurut_seq
    INCREMENT 1
    START 1
    MINVALUE 1
    MAXVALUE 2147483647
    CACHE 1;


-- Table: sc_tmp.proforma

-- DROP TABLE IF EXISTS sc_tmp.proforma;

CREATE TABLE IF NOT EXISTS sc_tmp.proforma
(
    idurut serial NOT NULL,
    docno character(30) COLLATE pg_catalog."default" NOT NULL,
    rolejob character(10) COLLATE pg_catalog."default",
    docdate character(20) COLLATE pg_catalog."default",
    pono character (30 ) COLLATE pg_catalog."default",
    podate character(20) COLLATE pg_catalog."default",
    jnsinvoice character(20) COLLATE pg_catalog."default",
    cust character(100) COLLATE pg_catalog."default",
    address text COLLATE pg_catalog."default",
    phone character varying(50) COLLATE pg_catalog."default",
    fax character varying(50) COLLATE pg_catalog."default",
    facrisk text COLLATE pg_catalog."default",
    shipper text COLLATE pg_catalog."default",
    consignee text COLLATE pg_catalog."default",
    shippingmark text COLLATE pg_catalog."default",
    notifyparty text COLLATE pg_catalog."default",
    paymentmethod character varying(50) COLLATE pg_catalog."default",
    bank character varying(50) COLLATE pg_catalog."default",
    grosssales numeric(18,2),
    downpayment numeric(18,2),
    netsales numeric(18,2),
    taxbasis numeric(18,2),
    vat numeric(18,2),
    pph22 numeric(18,2),
    ttlprice numeric(18,2),
    nmbank character(100),
	alamatbank TEXT,
	kodeposbank character(100),
	accname character(100),
	accno TEXT,
	swiftcode character(50),
    description text COLLATE pg_catalog."default",
    
    brand character(50) COLLATE pg_catalog."default",
    size text COLLATE pg_catalog."default",
    qty character(100) COLLATE pg_catalog."default",
    pembayaran text COLLATE pg_catalog."default",
    pengiriman text COLLATE pg_catalog."default",
    expdate character(20) COLLATE pg_catalog."default",
    ketentuan text COLLATE pg_catalog."default",
    status character(6) COLLATE pg_catalog."default",
    inputby character varying(50) COLLATE pg_catalog."default",
    inputdate timestamp without time zone,
    updateby character varying(50) COLLATE pg_catalog."default",
    updatedate timestamp without time zone,
    printby character varying(50) COLLATE pg_catalog."default",
    printdate timestamp without time zone,
    docnotmp character(30) COLLATE pg_catalog."default",
    CONSTRAINT pk_tmp_proforma PRIMARY KEY (idurut, docno)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_tmp.proforma
    OWNER to postgres;

ALTER SEQUENCE sc_tmp.proforma_idurut_seq
    OWNED BY sc_tmp.proforma.idurut;

ALTER SEQUENCE sc_tmp.proforma_idurut_seq
    OWNER TO postgres;


    -- SEQUENCE: sc_trx.proforma_idurut_seq

-- DROP SEQUENCE IF EXISTS sc_trx.proforma_idurut_seq;

CREATE SEQUENCE IF NOT EXISTS sc_trx.proforma_idurut_seq
    INCREMENT 1
    START 1
    MINVALUE 1
    MAXVALUE 2147483647
    CACHE 1;

-- Table: sc_trx.proforma

-- DROP TABLE IF EXISTS sc_trx.proforma;

CREATE TABLE IF NOT EXISTS sc_trx.proforma
(
    idurut serial NOT NULL,
    docno character(30) COLLATE pg_catalog."default" NOT NULL,
    rolejob character(10) COLLATE pg_catalog."default",
    docdate character(20) COLLATE pg_catalog."default",
    pono character (30 ) COLLATE pg_catalog."default",
    podate character(20) COLLATE pg_catalog."default",
    jnsinvoice character(20) COLLATE pg_catalog."default",
    cust character(100) COLLATE pg_catalog."default",
    address text COLLATE pg_catalog."default",
    phone character varying(50) COLLATE pg_catalog."default",
    fax character varying(50) COLLATE pg_catalog."default",
    facrisk text COLLATE pg_catalog."default",
    shipper text COLLATE pg_catalog."default",
    consignee text COLLATE pg_catalog."default",
    shippingmark text COLLATE pg_catalog."default",
    notifyparty text COLLATE pg_catalog."default",
    paymentmethod character varying(50) COLLATE pg_catalog."default",
    bank character varying(50) COLLATE pg_catalog."default",
    grosssales numeric(18,2),
    downpayment numeric(18,2),
    netsales numeric(18,2),
    taxbasis numeric(18,2),
    vat numeric(18,2),
    pph22 numeric(18,2),
    ttlprice numeric(18,2),
    nmbank character(100),
	alamatbank TEXT,
	kodeposbank character(100),
	accname character(100),
	accno TEXT,
	swiftcode character(50).
    description text COLLATE pg_catalog."default",
    brand character(50) COLLATE pg_catalog."default",
    size text COLLATE pg_catalog."default",
    qty character(100) COLLATE pg_catalog."default",
    pembayaran text COLLATE pg_catalog."default",
    pengiriman text COLLATE pg_catalog."default",
    expdate character(20) COLLATE pg_catalog."default",
    ketentuan text COLLATE pg_catalog."default",
    status character(6) COLLATE pg_catalog."default",
    inputby character varying(50) COLLATE pg_catalog."default",
    inputdate timestamp without time zone,
    updateby character varying(50) COLLATE pg_catalog."default",
    updatedate timestamp without time zone,
    printby character varying(50) COLLATE pg_catalog."default",
    printdate timestamp without time zone,
    docnotmp character(30) COLLATE pg_catalog."default",

    CONSTRAINT proforma_pkey PRIMARY KEY (docno),
    CONSTRAINT proforma_idurut_key UNIQUE (idurut)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_trx.proforma
    OWNER to postgres;

ALTER SEQUENCE sc_trx.proforma_idurut_seq
    OWNED BY sc_trx.proforma.idurut;

ALTER SEQUENCE sc_trx.proforma_idurut_seq
    OWNER TO postgres;