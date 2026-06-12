CREATE TABLE IF NOT EXISTS sc_tmp.soidtl
(
    idurut SERIAL PRIMARY KEY,
    docno CHARACTER(30) COLLATE pg_catalog."default" NOT NULL,
    idbarang CHARACTER(20) COLLATE pg_catalog."default",
    cust CHARACTER(100) COLLATE pg_catalog."default",
    nmbarang CHARACTER(150) COLLATE pg_catalog."default",
    grade CHARACTER(100) COLLATE pg_catalog."default",
    size CHARACTER(100) COLLATE pg_catalog."default",
    cutlength CHARACTER(100) COLLATE pg_catalog."default",
    qty NUMERIC(18,2),
    unit CHARACTER(20) COLLATE pg_catalog."default",
    usdmt NUMERIC(18,2),
    price NUMERIC(18,2),
    exchange NUMERIC(18,2),
    amount NUMERIC(18,2),
    etd character(20) COLLATE pg_catalog."default",
    ordernumbermsr character(50) COLLATE pg_catalog."default",
    specno character(150) COLLATE pg_catalog."default",
    totaldelivery NUMERIC(18,2),
    balanceorder NUMERIC(18,2),
    description TEXT COLLATE pg_catalog."default",
    status CHARACTER(6) COLLATE pg_catalog."default",
    inputby CHARACTER VARYING(50) COLLATE pg_catalog."default",
    inputdate TIMESTAMP WITHOUT TIME ZONE,
    updateby CHARACTER VARYING(50) COLLATE pg_catalog."default",
    updatedate TIMESTAMP WITHOUT TIME ZONE,
    docnotmp character(30)
)
TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_tmp.soidtl
    OWNER TO postgres;



CREATE TABLE IF NOT EXISTS sc_trx.soidtl
(
    idurut SERIAL PRIMARY KEY,
    docno CHARACTER(30) COLLATE pg_catalog."default" NOT NULL,
    idbarang CHARACTER(20) COLLATE pg_catalog."default",
    cust CHARACTER(100) COLLATE pg_catalog."default",
    nmbarang CHARACTER(150) COLLATE pg_catalog."default",
    grade CHARACTER(100) COLLATE pg_catalog."default",
    size CHARACTER(100) COLLATE pg_catalog."default",
    cutlength CHARACTER(100) COLLATE pg_catalog."default",
    qty NUMERIC(18,2),
    unit CHARACTER(20) COLLATE pg_catalog."default",
    usdmt NUMERIC(18,2),
    price NUMERIC(18,2),
    exchange NUMERIC(18,2),
    amount NUMERIC(18,2),
    etd character(20) COLLATE pg_catalog."default",
    ordernumbermsr character(50) COLLATE pg_catalog."default",
    specno character(150) COLLATE pg_catalog."default",
    totaldelivery NUMERIC(18,2),
    balanceorder NUMERIC(18,2),
    description TEXT COLLATE pg_catalog."default",
    status CHARACTER(6) COLLATE pg_catalog."default",
    inputby CHARACTER VARYING(50) COLLATE pg_catalog."default",
    inputdate TIMESTAMP WITHOUT TIME ZONE,
    updateby CHARACTER VARYING(50) COLLATE pg_catalog."default",
    updatedate TIMESTAMP WITHOUT TIME ZONE,
    docnotmp character(30)
)
TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_trx.soidtl
    OWNER TO postgres;
