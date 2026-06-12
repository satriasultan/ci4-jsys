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
