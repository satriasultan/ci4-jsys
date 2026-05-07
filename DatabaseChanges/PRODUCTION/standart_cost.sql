--I.Q.A.3
--DELETE FROM sc_mst.trxtype WHERE jenistrx='I.Q.A.3';
insert into sc_mst.trxtype
(kdtrx,jenistrx,uraian)
VALUES
('I','I.Q.A.3','DRAFT'),
('E','I.Q.A.3','REVISI/EDIT'),
('F','I.R.A.1','FINAL USER'),
('A','I.R.A.1','APPROVE'),
('A2','I.R.A.1','APPROVE 2'),
('A3','I.R.A.1','APPROVE 3'),
('P','I.R.A.1','CETAK/PRINT'),
('O','I.R.A.1','OBSOLATE'),
('C','I.R.A.1','CANCEL'),
('D','I.R.A.1','DELETE');




--drop table sc_tmp.standart_cost_mst;
CREATE TABLE IF NOT EXISTS sc_tmp.standart_cost_mst
(
    docno character(30) COLLATE pg_catalog."default" NOT NULL,
    doctype character(20) default 'STD_COST' ,
	cabang character (30 ) COLLATE pg_catalog."default",    
    pemohon character(100) COLLATE pg_catalog."default",
    docdate date,
    activedate date,
    docref character(30) COLLATE pg_catalog."default",
    status character(6) COLLATE pg_catalog."default",
    description TEXT,
	penyesuaian_a character(30) COLLATE pg_catalog."default",
	penyesuaian_b character(30) COLLATE pg_catalog."default",
	dari_bagian	character(30),
	ajustment BOOLEAN default false,
    inputby character varying(50) COLLATE pg_catalog."default",
    inputdate timestamp without time zone,
    updateby character varying(50) COLLATE pg_catalog."default",
    updatedate timestamp without time zone,
    docnotmp character(30) COLLATE pg_catalog."default",
    CONSTRAINT pk_tmp_standart_cost_mst PRIMARY KEY (docno)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_tmp.standart_cost_mst
    OWNER to postgres;


--drop table sc_tmp.standart_cost_dtl;
CREATE TABLE IF NOT EXISTS sc_tmp.standart_cost_dtl
(
    docno character(30) COLLATE pg_catalog."default" NOT NULL,
    doctype character(20) default 'STD_COST' ,
    cabang character (30 ) COLLATE pg_catalog."default",    
    pemohon character(100) COLLATE pg_catalog."default",
    docdate date,
	activedate date,
    idbarang character(50) COLLATE pg_catalog."default",
    nmbarang text,
    description text,
    unit char(20),
    actualcost numeric(18,2),
    lastcost numeric(18,2),
    newcost numeric(18,2),
	currcode char(3),
	status character(6) COLLATE pg_catalog."default",
	inputby character(50) COLLATE pg_catalog."default",
    inputdate timestamp without time zone,
    updateby character varying(50) COLLATE pg_catalog."default",
    updatedate timestamp without time zone,
    docnotmp character(30) COLLATE pg_catalog."default",
	idurut bigserial,
	uniqueid text,
    CONSTRAINT pk_tmp_standart_cost_dtl PRIMARY KEY (docno,uniqueid,idurut)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_tmp.standart_cost_dtl
    OWNER to postgres;


/* MST ATAU TRANSAKSI */

--drop table sc_mst.standart_cost_mst;
CREATE TABLE IF NOT EXISTS sc_mst.standart_cost_mst
(
    docno character(30) COLLATE pg_catalog."default" NOT NULL,
    doctype character(20) default 'STD_COST' ,
	cabang character (30 ) COLLATE pg_catalog."default",    
    pemohon character(100) COLLATE pg_catalog."default",
    docdate date,
    activedate date,
    docref character(30) COLLATE pg_catalog."default",
    status character(6) COLLATE pg_catalog."default",
    description TEXT,
	penyesuaian_a character(30) COLLATE pg_catalog."default",
	penyesuaian_b character(30) COLLATE pg_catalog."default",
	dari_bagian	character(30),
	ajustment BOOLEAN default false,
    inputby character varying(50) COLLATE pg_catalog."default",
    inputdate timestamp without time zone,
    updateby character varying(50) COLLATE pg_catalog."default",
    updatedate timestamp without time zone,
    docnotmp character(30) COLLATE pg_catalog."default",
    CONSTRAINT pk_tmp_standart_cost_mst PRIMARY KEY (docno)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_mst.standart_cost_mst
    OWNER to postgres;


--drop table sc_mst.standart_cost_dtl;
CREATE TABLE IF NOT EXISTS sc_mst.standart_cost_dtl
(
    docno character(30) COLLATE pg_catalog."default" NOT NULL,
    doctype character(20) default 'STD_COST' ,
    cabang character (30 ) COLLATE pg_catalog."default",    
    pemohon character(100) COLLATE pg_catalog."default",
    docdate date,
	activedate  date,
    idbarang character(50) COLLATE pg_catalog."default",
    nmbarang text,
    description text,
    unit char(20),
    actualcost numeric(18,2),
    lastcost numeric(18,2),
    newcost numeric(18,2),
	currcode char(3),
	status character(6) COLLATE pg_catalog."default",
	inputby character(50) COLLATE pg_catalog."default",
    inputdate timestamp without time zone,
    updateby character(50) COLLATE pg_catalog."default",
    updatedate timestamp without time zone,
    docnotmp character(30) COLLATE pg_catalog."default",
	idurut INTEGER,
	uniqueid text,
    CONSTRAINT pk_tmp_standart_cost_dtl PRIMARY KEY (docno,uniqueid,idurut)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_mst.standart_cost_dtl
    OWNER to postgres;
