/*DROP SAJA GROUP BARANG*/
CREATE TABLE IF NOT EXISTS sc_mst.groupbarang (
    idgroupbarang VARCHAR(10) PRIMARY KEY,
    nmgroupbarang VARCHAR(100),
    inputdate TIMESTAMP,
    inputby VARCHAR(20),
    updatedate TIMESTAMP,
    updateby VARCHAR(20),
    chold CHAR(4) DEFAULT 'NO'
);




/* PAKAI INI*/
-- Table: sc_mst.mgroup

-- DROP TABLE IF EXISTS sc_mst.mgroup;

CREATE TABLE IF NOT EXISTS sc_mst.mgroup
(
    id integer NOT NULL DEFAULT nextval('sc_mst.mgroup_id_seq'::regclass),
    idgroup character(6) COLLATE pg_catalog."default" NOT NULL,
    nmgroup character varying(30) COLLATE pg_catalog."default" NOT NULL,
    grouptype character(10) COLLATE pg_catalog."default" NOT NULL,
    chold character(3) COLLATE pg_catalog."default",
    description text COLLATE pg_catalog."default",
    inputdate timestamp without time zone,
    inputby character(20) COLLATE pg_catalog."default",
    updatedate timestamp without time zone,
    updateby character(20) COLLATE pg_catalog."default",
    idlocation character(12) COLLATE pg_catalog."default",
    CONSTRAINT mgroup_pkey PRIMARY KEY (idgroup, grouptype)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_mst.mgroup
    OWNER to postgres;
	
	
INSERT INTO sc_mst.mgroup 
(idgroup, nmgroup, inputdate, inputby, chold,grouptype)
VALUES
('BRG','BARANG',NOW(),'SYSTEM','NO','STOCK'),
('JSA','JASA',NOW(),'SYSTEM','NO','NON STOCK'),
('PRD','PRODUKSI',NOW(),'SYSTEM','NO','STOCK'),
('PKT','PAKET',NOW(),'SYSTEM','NO','STOCK'),
('WIP','WORK IN PROCESS',NOW(),'SYSTEM','NO','STOCK'),
('BB','BAHAN BAKU',NOW(),'SYSTEM','NO','STOCK'),
('BP','BAHAN PEMBANTU',NOW(),'SYSTEM','NO','STOCK');


