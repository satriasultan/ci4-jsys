-- Table: sc_mst.user

-- DROP TABLE IF EXISTS sc_mst."user";

CREATE TABLE IF NOT EXISTS sc_mst."user"
(
    branch character(6) COLLATE pg_catalog."default",
    nik character(12) COLLATE pg_catalog."default" NOT NULL,
    username character(25) COLLATE pg_catalog."default" NOT NULL,
    passwordweb character varying(500) COLLATE pg_catalog."default",
    level_id character varying(6) COLLATE pg_catalog."default",
    level_akses character varying(6) COLLATE pg_catalog."default",
    expdate timestamp without time zone,
    hold_id character(3) COLLATE pg_catalog."default",
    inputdate timestamp without time zone,
    inputby character varying(12) COLLATE pg_catalog."default",
    editdate timestamp without time zone,
    editby character(12) COLLATE pg_catalog."default",
    lastlogin timestamp without time zone,
    image character(200) COLLATE pg_catalog."default",
    loccode character(12) COLLATE pg_catalog."default",
    roleid character(8) COLLATE pg_catalog."default",
    lang character(15) COLLATE pg_catalog."default" DEFAULT 'english'::bpchar,
    iduser integer NOT NULL DEFAULT nextval('sc_mst.user_iduser_seq'::regclass),
    bagian character(20) COLLATE pg_catalog."default",
    plant character(6) COLLATE pg_catalog."default",
    email character varying(100) COLLATE pg_catalog."default",
    phone character(20) COLLATE pg_catalog."default",
    CONSTRAINT pk_user PRIMARY KEY (username)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_mst."user"
    OWNER to postgres;
	
ALTER TABLE sc_mst."user"
    ADD COLUMN IF NOT EXISTS nmlengkap    CHAR(100),
    ADD COLUMN IF NOT EXISTS nickname     CHAR(20),
    ADD COLUMN IF NOT EXISTS idbranchjob  CHAR(20),
    ADD COLUMN IF NOT EXISTS nmbranchjob  CHAR(100);
	
	
	
	