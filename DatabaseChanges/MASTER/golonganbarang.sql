-- Table: sc_mst.mlocation

-- DROP TABLE IF EXISTS sc_mst.mlocation;

-- KALAU TABLE COST CENTER BELUM ADA JALANKAN INI
CREATE TABLE IF NOT EXISTS sc_mst.golonganbarang
(
    idgolonganbarang character(10) COLLATE pg_catalog."default" NOT NULL,
    nmgolonganbarang character varying(100) COLLATE pg_catalog."default",
    inputdate timestamp without time zone,
    inputby character(20) COLLATE pg_catalog."default",
    updatedate timestamp without time zone,
    updateby character varying(20) COLLATE pg_catalog."default",
    chold character(4) COLLATE pg_catalog."default" DEFAULT 'NO'::bpchar,
    CONSTRAINT golonganbarang_pkey PRIMARY KEY (idgolonganbarang)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_mst.golonganbarang
    OWNER to postgres;




INSERT INTO sc_mst.golonganbarang 
(idgolonganbarang, nmgolonganbarang, inputdate, inputby, chold)
VALUES
('ALL','ALLOY',NOW(),'SYSTEM','NO'),
('AST','ASET / INVENTARIS',NOW(),'SYSTEM','NO'),
('BP1','BILLET WIP 1',NOW(),'SYSTEM','NO'),
('BP2','BILLET WIP 2',NOW(),'SYSTEM','NO'),
('COP','CONSUMABLE PRODUKSI',NOW(),'SYSTEM','NO'),
('COU','CONSUMABLE UMUM',NOW(),'SYSTEM','NO'),
('ELE','ELECTRODE',NOW(),'SYSTEM','NO'),
('FFG','FL FG',NOW(),'SYSTEM','NO'),
('FIP','FL WIP',NOW(),'SYSTEM','NO'),
('FRP','FL RB WIP',NOW(),'SYSTEM','NO'),
('J0001','JASA SEWA KENDARAAN',NOW(),'SYSTEM','NO'),
('J0002','JASA ANGKUT',NOW(),'SYSTEM','NO'),
('J0003','JASA POTONG SCRAP',NOW(),'SYSTEM','NO'),
('J0004','JASA KALIBRASI',NOW(),'SYSTEM','NO'),
('J0005','JASA PERBAIKAN MESIN DAN PERALATAN',NOW(),'SYSTEM','NO'),
('J0006','JASA PERBAIKAN KENDARAAN',NOW(),'SYSTEM','NO'),
('J0007','JASA PERBAIKAN ALAT BERAT',NOW(),'SYSTEM','NO'),
('J0008','JASA PENGUJIAN LINGKUNGAN',NOW(),'SYSTEM','NO'),
('J0009','JASA SEWA ALAT KERJA',NOW(),'SYSTEM','NO'),
('J0010','JASA PEKERJAAN SIPIL DAN BANGUNAN',NOW(),'SYSTEM','NO'),
('REF','REFRACTORIES',NOW(),'SYSTEM','NO'),
('RFG','RB FG',NOW(),'SYSTEM','NO'),
('RIP','RB WIP',NOW(),'SYSTEM','NO'),
('SPR','SPARE PART',NOW(),'SYSTEM','NO'),
('SUB','SUBMAT',NOW(),'SYSTEM','NO'),
('J0011','JASA PEMBUATAN ALAT UKUR',NOW(),'SYSTEM','NO'),
('RAR','RAWMATERIAL RETURN',NOW(),'SYSTEM','NO'),
('RAB','RAWMATERIAL BUY',NOW(),'SYSTEM','NO'),
('J0012','JASA PENGUJIAN MATERIAL',NOW(),'SYSTEM','NO'),
('J0013','JASA TERKAIT HUMAN CAPITAL',NOW(),'SYSTEM','NO');