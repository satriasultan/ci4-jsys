-- Table: sc_mst.rolepo

-- DROP TABLE IF EXISTS sc_mst.rolepo;

-- SEQUENCE: sc_mst.rolepo_id_seq

-- DROP SEQUENCE IF EXISTS sc_mst.rolepo_id_seq;

CREATE SEQUENCE IF NOT EXISTS sc_mst.rolepo_id_seq
    INCREMENT 1
    START 1
    MINVALUE 1
    MAXVALUE 2147483647
    CACHE 1;



CREATE TABLE IF NOT EXISTS sc_mst.rolepo
(
    id integer NOT NULL DEFAULT nextval('sc_mst.rolepo_id_seq'::regclass),
    jobcode character varying(20) COLLATE pg_catalog."default",
    codemenu character varying(10) COLLATE pg_catalog."default",
    infix character(4) COLLATE pg_catalog."default",
    prefix character varying(50) COLLATE pg_catalog."default",
    suffix character(5) COLLATE pg_catalog."default",
    inputby character varying(50) COLLATE pg_catalog."default",
    inputdate timestamp without time zone,
    CONSTRAINT rolepo_pkey PRIMARY KEY (id),
    CONSTRAINT uq_rolepo_combo UNIQUE (jobcode, codemenu, tahun, bulan)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_mst.rolepo
    OWNER to postgres;

ALTER SEQUENCE sc_mst.rolepo_id_seq
OWNED BY sc_mst.rolepo.id;

ALTER SEQUENCE sc_mst.rolepo_id_seq
    OWNER TO postgres;



    INSERT INTO sc_mst.rolepo (jobcode, codemenu, infix, prefix, suffix, inputby, inputdate)
VALUES 
    ('MSMI', 'I.S.A.2', '2508', 'MSMI-PH', '00000', 'SYSTEM', '2025-08-07 16:48:12.704786'),
    ('MSMJ', 'I.S.A.2', '2508', 'MSM-PH', '00000', 'SYSTEM', '2025-08-07 16:48:12.704786'),
    ('JTS', 'I.S.A.2', '2508', 'JTS-PH', '00000', 'SYSTEM', '2025-08-07 16:48:12.704786')