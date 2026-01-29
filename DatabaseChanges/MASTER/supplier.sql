CREATE TABLE sc_mst.mstsupplier (
   id serial PRIMARY KEY,
   kdsupplier char(30) NOT NULL,
   nmsupplier CHAR(250),
   alamat TEXT,
   idkota CHAR(20),
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
   interngroup char(6),
   blacklist char(6),
   status CHAR(6),
   createdby CHAR(20),
   createddate TIMESTAMP WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP,
   updateby CHAR(20),
   updatedate TIMESTAMP WITHOUT TIME ZONE
)

create table sc_mst.market(
	idmarket char(6) primary key,
	nmmarket char(50),
	createdby CHAR(20),
   createddate TIMESTAMP WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP,
   updateby CHAR(20),
   updatedate TIMESTAMP WITHOUT TIME ZONE
)

alter table sc_mst.mstsupplier
add column idprovinsi CHAR(20),
add column chold char(6)


INSERT INTO sc_mst.market
    (idmarket, nmmarket, createdby)
VALUES
    ('SR001', 'SERVICE',      'SYSTEM'),
    ('AC001', 'ACCESSORIES',  'SYSTEM'),
    ('VE001', 'VEHICLE',      'SYSTEM'),
    ('PR001', 'PART',         'SYSTEM');


