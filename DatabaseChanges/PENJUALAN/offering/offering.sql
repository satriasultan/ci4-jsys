-- SEQUENCE: sc_tmp.offering_idurut_seq

-- DROP SEQUENCE IF EXISTS sc_tmp.offering_idurut_seq;


-- Table: sc_tmp.offering

-- DROP TABLE IF EXISTS sc_tmp.offering;

CREATE TABLE IF NOT EXISTS sc_tmp.offering
(
    idurut integer NOT NULL DEFAULT nextval('sc_tmp.offering_idurut_seq'::regclass),
    docno character(30) COLLATE pg_catalog."default" NOT NULL,
    rolejob character(10) COLLATE pg_catalog."default",
    cust character(100) COLLATE pg_catalog."default",
    address text COLLATE pg_catalog."default",
    docdate character(20) COLLATE pg_catalog."default",
    phone character varying(50) COLLATE pg_catalog."default",
    fax character varying(50) COLLATE pg_catalog."default",
    up character varying(100) COLLATE pg_catalog."default",
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
    CONSTRAINT pk_tmp_offering PRIMARY KEY (idurut, docno)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_tmp.offering
    OWNER to postgres;

    -- SEQUENCE: sc_trx.offering_idurut_seq

-- DROP SEQUENCE IF EXISTS sc_trx.offering_idurut_seq;


-- Table: sc_trx.offering

-- DROP TABLE IF EXISTS sc_trx.offering;

CREATE TABLE IF NOT EXISTS sc_trx.offering
(
    idurut integer NOT NULL DEFAULT nextval('sc_trx.offering_idurut_seq'::regclass),
    rolejob character(10) COLLATE pg_catalog."default",
    docno character(30) COLLATE pg_catalog."default" NOT NULL,
    cust character(100) COLLATE pg_catalog."default",
    address text COLLATE pg_catalog."default",
    docdate character(20) COLLATE pg_catalog."default",
    phone character varying(50) COLLATE pg_catalog."default",
    fax character varying(50) COLLATE pg_catalog."default",
    up character varying(100) COLLATE pg_catalog."default",
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

    CONSTRAINT offering_pkey PRIMARY KEY (docno),
    CONSTRAINT offering_idurut_key UNIQUE (idurut)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_trx.offering
    OWNER to postgres;




CREATE TABLE IF NOT EXISTS sc_tmp.offeringdtl
(
    idurut SERIAL PRIMARY KEY,
    docno CHARACTER(30) COLLATE pg_catalog."default" NOT NULL,
    idbarang CHARACTER(20) COLLATE pg_catalog."default",
    nmbarang CHARACTER(150) COLLATE pg_catalog."default",
    unit CHARACTER(20) COLLATE pg_catalog."default",
    qty NUMERIC(18,2),
    price NUMERIC(18,2),
    usdmt NUMERIC(18,2),
    exchange NUMERIC(18,2),
    description TEXT COLLATE pg_catalog."default",
    status CHARACTER(6) COLLATE pg_catalog."default",
    inputby CHARACTER VARYING(50) COLLATE pg_catalog."default",
    inputdate TIMESTAMP WITHOUT TIME ZONE,
    updateby CHARACTER VARYING(50) COLLATE pg_catalog."default",
    updatedate TIMESTAMP WITHOUT TIME ZONE
)
TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_tmp.offeringdtl
    OWNER TO postgres;



CREATE TABLE IF NOT EXISTS sc_trx.offeringdtl
(
    idurut SERIAL PRIMARY KEY,
    docno CHARACTER(30) COLLATE pg_catalog."default" NOT NULL,
    idbarang CHARACTER(20) COLLATE pg_catalog."default",
    nmbarang CHARACTER(150) COLLATE pg_catalog."default",
    unit CHARACTER(20) COLLATE pg_catalog."default",
    qty NUMERIC(18,2),
    price NUMERIC(18,2),
    usdmt NUMERIC(18,2),
    exchange NUMERIC(18,2),
    description TEXT COLLATE pg_catalog."default",
    status CHARACTER(6) COLLATE pg_catalog."default",
    inputby CHARACTER VARYING(50) COLLATE pg_catalog."default",
    inputdate TIMESTAMP WITHOUT TIME ZONE,
    updateby CHARACTER VARYING(50) COLLATE pg_catalog."default",
    updatedate TIMESTAMP WITHOUT TIME ZONE
)
TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_trx.offeringdtl
    OWNER TO postgres;
