CREATE TABLE sc_mst.customer(
	id serial primary key,
	kdcustomer char(20),
	nmcustomer char(200),
    alamat1 text COLLATE pg_catalog."default",
    alamat2 text COLLATE pg_catalog."default",	
	
    alamat_kantor text COLLATE pg_catalog."default",
    kodepos_kantor character varying(10) COLLATE pg_catalog."default",
    provinsi_kantor character varying(100) COLLATE pg_catalog."default",
    kota_kantor character varying(100) COLLATE pg_catalog."default",
    kec_kantor character varying(100) COLLATE pg_catalog."default",
    kel_kantor character varying(100) COLLATE pg_catalog."default",
	
	alamat_pengiriman text COLLATE pg_catalog."default",
    kodepos_pengiriman character varying(10) COLLATE pg_catalog."default",
    provinsi_pengiriman character varying(100) COLLATE pg_catalog."default",
    kota_pengiriman character varying(100) COLLATE pg_catalog."default",
    kec_pengiriman character varying(100) COLLATE pg_catalog."default",
    kel_pengiriman character varying(100) COLLATE pg_catalog."default",
	
    alamat_penagihan text COLLATE pg_catalog."default",
    kodepos_penagihan character varying(10) COLLATE pg_catalog."default",
    provinsi_penagihan character varying(100) COLLATE pg_catalog."default",
    kota_penagihan character varying(100) COLLATE pg_catalog."default",
    kec_penagihan character varying(100) COLLATE pg_catalog."default",
    kel_penagihan character varying(100) COLLATE pg_catalog."default",
	
	phone CHAR(20),
	fax CHAR(30),
	email CHAR(150),
	npwp CHAR(30),
	npkp CHAR(30),
	jthtempo numeric(18,2),
	plafon numeric(18,2),
	idmarket CHAR(10),
	cp CHAR(150),
	jabatan CHAR(100),
	keterangan TEXT,
	coa char(6),
	blacklist char(6),
    namanpwp char(50),
    alamatnpwp TEXT,
    idkotanpwp char(20),
    idcoretax char(100),
    idtku char(100),
    docnopembeli char(100),
    grade char(30),
    salesman char(30),
    kolektor char(30),
    koderetur char(20),

    isfaktur char(6),
    harifakturin TEXT,
    haripenagihan TEXT,
    haripembayaran TEXT,
    defaultfor char(30),
	status CHAR(6),
    createdby CHAR(20),
    createddate TIMESTAMP WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updateby CHAR(20),
    updatedate TIMESTAMP WITHOUT TIME ZONE
)


-- GRADECUST
create table sc_mst.gradecust(
	kdgradecust char(6) primary key,
	nmgradecust char(50),
    isopenprice char(10),
	createdby CHAR(20),
    createddate TIMESTAMP WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updateby CHAR(20),
    updatedate TIMESTAMP WITHOUT TIME ZONE
)


INSERT INTO sc_mst.gradecust
    (kdgradecust, nmgradecust, isopenprice, createdby)
VALUES
    ('O', 'OPEN', '1', 'SYSTEM');

-- END GRADECUST
-- SALESMAN

create table sc_mst.salesman(
	kdsalesman char(6) primary key,
	nmsalesman char(50),
    alamat TEXT,
    idkota char(10),
	createdby CHAR(20),
    createddate TIMESTAMP WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updateby CHAR(20),
    updatedate TIMESTAMP WITHOUT TIME ZONE
)


INSERT INTO sc_mst.salesman
    (kdsalesman, nmsalesman, alamat, idkota ,createdby)
VALUES
    ('MKT', 'MARKETING', 'RAYA TAMAN NO. 1 SEPANJANG', '3515','SYSTEM');


-- END OF SALESMAN
-- kolektor

create table sc_mst.kolektor(
	kdkolektor char(6) primary key,
	nmkolektor char(50),
    alamat TEXT,
    idkota char(10),
	createdby CHAR(20),
    createddate TIMESTAMP WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updateby CHAR(20),
    updatedate TIMESTAMP WITHOUT TIME ZONE
)


-- INSERT INTO sc_mst.kolektor
--     (kdkolektor, nmkolektor, alamat, idkota ,createdby)
-- VALUES
--     ('MKT', 'MARKETING', 'RAYA TAMAN NO. 1 SEPANJANG', '3515','SYSTEM');
-- END OF KOLEKTOR