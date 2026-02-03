-- Table: sc_mst.mlocation

-- DROP TABLE IF EXISTS sc_mst.mlocation;

-- KALAU TABLE COST CENTER BELUM ADA JALANKAN INI
CREATE TABLE IF NOT EXISTS sc_mst.principal
(
    idprincipal character(10) COLLATE pg_catalog."default" NOT NULL,
    nmprincipal character varying(100) COLLATE pg_catalog."default",
    inputdate timestamp without time zone,
    inputby character(20) COLLATE pg_catalog."default",
    updatedate timestamp without time zone,
    updateby character varying(20) COLLATE pg_catalog."default",
    kdsupplier character(30),
    alamat TEXT,
    phone character(20)
    chold character(4) COLLATE pg_catalog."default" DEFAULT 'NO'::bpchar,
    CONSTRAINT principal_pkey PRIMARY KEY (idprincipal)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_mst.principal
    OWNER to postgres;

