-- Table: sc_mst.mlocation

-- DROP TABLE IF EXISTS sc_mst.mlocation;
-- KALAU TABLE COST CENTER BELUM ADA JALANKAN INI
CREATE TABLE IF NOT EXISTS sc_mst.mlocation
(
    id serial,
    idlocation character(12) COLLATE pg_catalog."default" NOT NULL,
    nmlocation character(60) COLLATE pg_catalog."default",
    chold character(5) COLLATE pg_catalog."default",
    description text COLLATE pg_catalog."default",
    idcomplex character(30) COLLATE pg_catalog."default",
    idbarcode character(30) COLLATE pg_catalog."default",
    inputdate timestamp without time zone,
    inputby character(20) COLLATE pg_catalog."default",
    updatedate timestamp without time zone,
    updateby character(20) COLLATE pg_catalog."default",
    idarea_default character(30) COLLATE pg_catalog."default",
    pselisih character(20),
    CONSTRAINT mlocation_pkey PRIMARY KEY (idlocation)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_mst.mlocation
    OWNER to postgres;





-- KALAU TABLE COST CENTER SUDAH ADA JALANKAN INI
ALTER TABLE sc_mst.mlocation
ADD COLUMN pbiaya character(20)