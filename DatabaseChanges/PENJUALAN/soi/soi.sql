-- SEQUENCE: sc_tmp.soi_idurut_seq

-- DROP SEQUENCE IF EXISTS sc_tmp.soi_idurut_seq;

CREATE SEQUENCE IF NOT EXISTS sc_tmp.soi_idurut_seq
    INCREMENT 1
    START 1
    MINVALUE 1
    MAXVALUE 2147483647
    CACHE 1;


-- Table: sc_tmp.soi

-- DROP TABLE IF EXISTS sc_tmp.soi;

CREATE TABLE IF NOT EXISTS sc_tmp.soi
(
    idurut serial NOT NULL,
    docno character(30) COLLATE pg_catalog."default" NOT NULL,
    rolejob character(10) COLLATE pg_catalog."default",
    docdate character(20) COLLATE pg_catalog."default",
    cust character(100) COLLATE pg_catalog."default",
    po character(100) COLLATE pg_catalog."default",
    pocust character(100) COLLATE pg_catalog."default",
    description text COLLATE pg_catalog."default",
    revno character(50) COLLATE pg_catalog."default",
    status character(6) COLLATE pg_catalog."default",
    inputby character varying(50) COLLATE pg_catalog."default",
    inputdate timestamp without time zone,
    updateby character varying(50) COLLATE pg_catalog."default",
    updatedate timestamp without time zone,
    printby character varying(50) COLLATE pg_catalog."default",
    printdate timestamp without time zone,
    docnotmp character(30) COLLATE pg_catalog."default",
    CONSTRAINT pk_tmp_soi PRIMARY KEY (idurut, docno)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_tmp.soi
    OWNER to postgres;

ALTER SEQUENCE sc_tmp.soi_idurut_seq
    OWNED BY sc_tmp.soi.idurut;

ALTER SEQUENCE sc_tmp.soi_idurut_seq
    OWNER TO postgres;


    -- SEQUENCE: sc_trx.soi_idurut_seq

-- DROP SEQUENCE IF EXISTS sc_trx.soi_idurut_seq;

CREATE SEQUENCE IF NOT EXISTS sc_trx.soi_idurut_seq
    INCREMENT 1
    START 1
    MINVALUE 1
    MAXVALUE 2147483647
    CACHE 1;

-- Table: sc_trx.soi

-- DROP TABLE IF EXISTS sc_trx.soi;

CREATE TABLE IF NOT EXISTS sc_trx.soi
(
    idurut serial NOT NULL,
    docno character(30) COLLATE pg_catalog."default" NOT NULL,
    rolejob character(10) COLLATE pg_catalog."default",
    docdate character(20) COLLATE pg_catalog."default",
    cust character(100) COLLATE pg_catalog."default",
    po character(100) COLLATE pg_catalog."default",
    pocust character(100) COLLATE pg_catalog."default",
    description text COLLATE pg_catalog."default",
    revno character(50) COLLATE pg_catalog."default",
    status character(6) COLLATE pg_catalog."default",
    inputby character varying(50) COLLATE pg_catalog."default",
    inputdate timestamp without time zone,
    updateby character varying(50) COLLATE pg_catalog."default",
    updatedate timestamp without time zone,
    printby character varying(50) COLLATE pg_catalog."default",
    printdate timestamp without time zone,
    docnotmp character(30) COLLATE pg_catalog."default",
    CONSTRAINT soi_pkey PRIMARY KEY (docno),
    CONSTRAINT soi_idurut_key UNIQUE (idurut)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_trx.soi
    OWNER to postgres;

ALTER SEQUENCE sc_trx.soi_idurut_seq
    OWNED BY sc_trx.soi.idurut;

ALTER SEQUENCE sc_trx.soi_idurut_seq
    OWNER TO postgres;