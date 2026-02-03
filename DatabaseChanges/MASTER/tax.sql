create table sc_mst.grouptax(
	idgrouptax char(20) primary key,
	nmgrouptax char(50),
    status char(6),
	chold char(6) default 'NO',
	createdby CHAR(20),
    createddate TIMESTAMP WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP,
	id serial
);

insert into sc_mst.grouptax
(idgrouptax,nmgrouptax,status)
VALUES
('NON','NON','P'),
('PPH','PPH','P'),
('PPN','PPN','P'),
('PPH22','PPH22','P');



create table sc_mst.tax_mst(
	idtax char(20) primary key,
	nmtax char(50),
    status char(6),
	chold char(6) default 'NO',
	createdby CHAR(20),
    createddate TIMESTAMP WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP,
	id serial
);

insert into sc_mst.tax_mst
(idtax,nmtax,status)
VALUES
('BBB11','PPH 22 + PPN 11%','P'),
('NON','NON','P'),
('PPN11','PPN 11%','P'),
('PPN12','PPN 12%','P');



CREATE TABLE sc_mst.tax_dtl (
    idtax CHAR(20),
    idgrouptax CHAR(50),
    nmgrouptax CHAR(50),
    kodepajak CHAR(20),
    prk_masukan CHAR(20),
    prk_keluaran CHAR(20),
    prk_fpj_msk CHAR(20),
    prk_fpj_klr CHAR(20),
    prk_fpj_msk_rep CHAR(20),
    prk_fpj_klr_rep CHAR(20),
    rumus_dpp_lain CHAR(20),
    rumus_pajak CHAR(20),
    percentation NUMERIC(18,2),
    status CHAR(6),
    chold CHAR(6) DEFAULT 'NO',
    createdby CHAR(20),
    createddate TIMESTAMP WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    id SERIAL,
    CONSTRAINT pk_tax_dtl PRIMARY KEY (idtax, idgrouptax)
);


insert into sc_mst.tax_dtl
(idtax,idgrouptax,nmgrouptax,kodepajak,prk_masukan,prk_keluaran,prk_fpj_msk,prk_fpj_klr, prk_fpj_msk_rep, prk_fpj_klr_rep,rumus_dpp_lain,rumus_pajak,percentation,status,chold)
values
('BBB11','PPH22','PPH22','','116103','214103','','', '', '','','',0.30,'P','NO'),
('BBB11','PPN','PPN','','116106','214116','','', '', '','','',11.00,'P','NO');


insert into sc_mst.tax_dtl
(idtax,idgrouptax,nmgrouptax,kodepajak,prk_masukan,prk_keluaran,prk_fpj_msk,prk_fpj_klr, prk_fpj_msk_rep, prk_fpj_klr_rep,rumus_dpp_lain,rumus_pajak,percentation,status,chold)
values
('NON','NON','NON','','','','','', '', '','','',NULL,'P','NO');

insert into sc_mst.tax_dtl
(idtax,idgrouptax,nmgrouptax,kodepajak,prk_masukan,prk_keluaran,prk_fpj_msk,prk_fpj_klr, prk_fpj_msk_rep, prk_fpj_klr_rep,rumus_dpp_lain,rumus_pajak,percentation,status,chold)
values
('PPN11','PPN','PPN','','116106','214116','116106','214116', '116106', '214116','','',11.00,'P','NO');

insert into sc_mst.tax_dtl
(idtax,idgrouptax,nmgrouptax,kodepajak,prk_masukan,prk_keluaran,prk_fpj_msk,prk_fpj_klr, prk_fpj_msk_rep, prk_fpj_klr_rep,rumus_dpp_lain,rumus_pajak,percentation,status,chold)
values
('PPN12','PPN','PPN','','116106','214113','116106','214113', '116106', '214113','','',12.00,'P','NO');