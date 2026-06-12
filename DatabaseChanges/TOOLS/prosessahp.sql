CREATE TABLE IF NOT EXISTS sc_trx.prosessahp
(
    idurut serial NOT NULL,
    periode character(20) COLLATE pg_catalog."default",
    flagproses character(6) COLLATE pg_catalog."default",
    keterangan TEXT,
    inputby character varying(50) COLLATE pg_catalog."default",
    inputdate timestamp without time zone,
    updateby character varying(50) COLLATE pg_catalog."default",
    updatedate timestamp without time zone,
    printby character varying(50) COLLATE pg_catalog."default",
    printdate timestamp without time zone,
    docnotmp character(30) COLLATE pg_catalog."default",
    CONSTRAINT pk_trx_prosessahp PRIMARY KEY (idurut)
)
