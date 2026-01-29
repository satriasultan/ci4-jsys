-- Table: sc_mst.mlocation

-- DROP TABLE IF EXISTS sc_mst.mlocation;

-- KALAU TABLE COST CENTER BELUM ADA JALANKAN INI
CREATE TABLE IF NOT EXISTS sc_mst.costcenter
(
    idcostcenter character(10) COLLATE pg_catalog."default" NOT NULL,
    nmcostcenter character varying(100) COLLATE pg_catalog."default",
    inputdate timestamp without time zone,
    inputby character(20) COLLATE pg_catalog."default",
    updatedate timestamp without time zone,
    updateby character varying(20) COLLATE pg_catalog."default",
    idgroup character(20) COLLATE pg_catalog."default",
    nmgroup character(100) COLLATE pg_catalog."default",
    chold character(4) COLLATE pg_catalog."default" DEFAULT 'NO'::bpchar,
    valuein numeric(18,2),
    valueout numeric(18,2),
    valuesld numeric(18,2),
    pbiaya character(20),
    CONSTRAINT costcenter_pkey PRIMARY KEY (idcostcenter)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_mst.costcenter
    OWNER to postgres;


-- KALAU TABLE COST CENTER SUDAH ADA JALANKAN INI
ALTER TABLE sc_mst.costcenter
ADD COLUMN pbiaya character(20)