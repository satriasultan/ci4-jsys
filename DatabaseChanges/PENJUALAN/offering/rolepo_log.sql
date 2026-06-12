-- SEQUENCE: sc_mst.rolepo_log_id_seq

-- DROP SEQUENCE IF EXISTS sc_mst.rolepo_log_id_seq;

CREATE SEQUENCE IF NOT EXISTS sc_mst.rolepo_log_id_seq
    INCREMENT 1
    START 1
    MINVALUE 1
    MAXVALUE 2147483647
    CACHE 1;

-- Table: sc_mst.rolepo_log

-- DROP TABLE IF EXISTS sc_mst.rolepo_log;

CREATE TABLE IF NOT EXISTS sc_mst.rolepo_log
(
    id integer NOT NULL DEFAULT nextval('sc_mst.rolepo_log_id_seq'::regclass),
    jobcode character varying(20) COLLATE pg_catalog."default",
    codemenu character varying(10) COLLATE pg_catalog."default",
    tahun character(2) COLLATE pg_catalog."default",
    bulan character(2) COLLATE pg_catalog."default",
    suffix character(5) COLLATE pg_catalog."default",
    is_used boolean DEFAULT false,
    docno character varying(100) COLLATE pg_catalog."default",
    used_by character varying(50) COLLATE pg_catalog."default",
    used_at timestamp without time zone,
    CONSTRAINT rolepo_log_pkey PRIMARY KEY (id),
    CONSTRAINT uq_rolepo_log_combo UNIQUE (jobcode, codemenu, tahun, bulan, suffix)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_mst.rolepo_log
    OWNER to postgres;
    
ALTER SEQUENCE sc_mst.rolepo_log_id_seq
    OWNED BY sc_mst.rolepo_log.id;

ALTER SEQUENCE sc_mst.rolepo_log_id_seq
    OWNER TO postgres;