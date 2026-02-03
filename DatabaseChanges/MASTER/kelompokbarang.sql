-- Table: sc_mst.mlocation

-- DROP TABLE IF EXISTS sc_mst.mlocation;

-- KALAU TABLE COST CENTER BELUM ADA JALANKAN INI
CREATE TABLE IF NOT EXISTS sc_mst.kelompokbarang
(
    idkelompokbarang character(10) COLLATE pg_catalog."default" NOT NULL,
    nmkelompokbarang character varying(100) COLLATE pg_catalog."default",
    inputdate timestamp without time zone,
    inputby character(20) COLLATE pg_catalog."default",
    updatedate timestamp without time zone,
    updateby character varying(20) COLLATE pg_catalog."default",
    chold character(4) COLLATE pg_catalog."default" DEFAULT 'NO'::bpchar,
    CONSTRAINT kelompokbarang_pkey PRIMARY KEY (idkelompokbarang)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_mst.kelompokbarang
    OWNER to postgres;

