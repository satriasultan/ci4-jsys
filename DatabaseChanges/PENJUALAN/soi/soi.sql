BEGIN;

-- =========================================================
-- DROP OBJECT LAMA
-- =========================================================

DROP TABLE IF EXISTS sc_tmp.soi CASCADE;
DROP TABLE IF EXISTS sc_trx.soi CASCADE;

DROP SEQUENCE IF EXISTS sc_tmp.soi_idurut_seq CASCADE;
DROP SEQUENCE IF EXISTS sc_trx.soi_idurut_seq CASCADE;


-- =========================================================
-- SEQUENCE : SC_TMP.SOI
-- =========================================================

CREATE SEQUENCE sc_tmp.soi_idurut_seq
    INCREMENT 1
    START 1
    MINVALUE 1
    MAXVALUE 2147483647
    CACHE 1;

ALTER SEQUENCE sc_tmp.soi_idurut_seq
    OWNER TO postgres;


-- =========================================================
-- TABLE : SC_TMP.SOI
-- =========================================================

CREATE TABLE sc_tmp.soi
(
    idurut integer NOT NULL
        DEFAULT nextval('sc_tmp.soi_idurut_seq'::regclass),

    docno character(30) NOT NULL,

    rolejob character(10),

    docdate character(20),

    cust character(100),

    po character(100),

    pocust character(100),

    description text,

    revno character(50),

    status character(6),

    inputby character varying(50),

    inputdate timestamp without time zone,

    updateby character varying(50),

    updatedate timestamp without time zone,

    printby character varying(50),

    printdate timestamp without time zone,

    docnotmp character(30),

    CONSTRAINT pk_tmp_soi
        PRIMARY KEY (idurut, docno)
)
TABLESPACE pg_default;


ALTER TABLE sc_tmp.soi
    OWNER TO postgres;


ALTER SEQUENCE sc_tmp.soi_idurut_seq
    OWNED BY sc_tmp.soi.idurut;


-- =========================================================
-- SEQUENCE : SC_TRX.SOI
-- =========================================================

CREATE SEQUENCE sc_trx.soi_idurut_seq
    INCREMENT 1
    START 1
    MINVALUE 1
    MAXVALUE 2147483647
    CACHE 1;

ALTER SEQUENCE sc_trx.soi_idurut_seq
    OWNER TO postgres;


-- =========================================================
-- TABLE : SC_TRX.SOI
-- =========================================================

CREATE TABLE sc_trx.soi
(
    idurut integer NOT NULL
        DEFAULT nextval('sc_trx.soi_idurut_seq'::regclass),

    docno character(30) NOT NULL,

    rolejob character(10),

    docdate character(20),

    cust character(100),

    po character(100),

    pocust character(100),

    description text,

    revno character(50),

    status character(6),

    inputby character varying(50),

    inputdate timestamp without time zone,

    updateby character varying(50),

    updatedate timestamp without time zone,

    printby character varying(50),

    printdate timestamp without time zone,

    docnotmp character(30),

    CONSTRAINT soi_pkey
        PRIMARY KEY (docno),

    CONSTRAINT soi_idurut_key
        UNIQUE (idurut)
)
TABLESPACE pg_default;


ALTER TABLE sc_trx.soi
    OWNER TO postgres;


ALTER SEQUENCE sc_trx.soi_idurut_seq
    OWNED BY sc_trx.soi.idurut;


COMMIT;