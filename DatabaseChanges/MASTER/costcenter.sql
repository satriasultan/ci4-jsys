-- Table: sc_mst.mlocation

-- DROP TABLE IF EXISTS sc_mst.mlocation;

-- KALAU TABLE COST CENTER BELUM ADA JALANKAN INI
drop table sc_mst.costcenter  CASCADE;
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



delete from sc_mst.costcenter CASCADE;
INSERT INTO sc_mst.costcenter (
    idcostcenter,
    nmcostcenter,
    chold,
    pbiaya
) VALUES
('04325','PERAWATAN BANGUNAN_FINANCE, ACCOUNTING & TAX','NO','611192.13.702'),
('04332','PERAWATAN BANGUNAN_HC','NO','611192.13.702'),
('04425','PERAWATAN INVENTARIS KANTOR_FINANCE, ACCOUNTING & TAX','NO','611192.13.702'),
('04432','PERAWATAN INVENTARIS KANTOR_HC','NO','611192.13.702'),
('04434','PERAWATAN INVENTARIS KANTOR_GENERAL','NO','611192.13.702'),
('04925','SERAGAM DAN APD_FINANCE, ACCOUNTING & TAX','NO','611192.13.702'),
('04932','SERAGAM DAN APD_HC','NO','611192.13.702'),
('04938','SERAGAM DAN APD_S & H JTS 1','NO','611192.13.702'),
('05225','SUPPLIES _FINANCE, ACCOUNTING & TAX','NO','611192.13.702'),
('05232','SUPPLIES _HC','NO','611192.13.702'),
('10207','BAHAN BAKAR ALAT ANGKUT_PPIC & LOGISTIC PLANT 1','NO','611192.13.702'),
('10501','CONSUMABLE_EAF','NO','611192.13.702'),
('10502','CONSUMABLE_LF','NO','611192.13.702'),
('10503','CONSUMABLE_VD','NO','611192.13.702'),
('10504','CONSUMABLE_CCM','NO','611192.13.702'),
('10505','CONSUMABLE_GENERAL STEEL MAKING','NO','611192.13.702'),
('10506','CONSUMABLE_BILLET INSPECTION','NO','611192.13.702'),
('10507','CONSUMABLE_PPIC & LOGISTIC PLANT 1','NO','611192.13.702'),
('10508','CONSUMABLE_ENGINEERING PLANT 1','NO','611192.13.702'),
('10509','CONSUMABLE_MAINTENANCE UTILITY PLANT 1','NO','611192.13.702'),
('10510','CONSUMABLE_MAINTENANCE ELECTRICITY PLANT 1','NO','611192.13.702'),
('10511','CONSUMABLE_MAINTENANCE MECHANIC PLANT 1','NO','611192.13.702'),
('10512','CONSUMABLE_QC PLANT 1','NO','611192.13.702'),
('10901','ELECTRODE_EAF','NO','611192.13.702'),
('10902','ELECTRODE_LF','NO','611192.13.702');