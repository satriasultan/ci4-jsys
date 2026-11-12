

-- Table: sc_im.capex

-- DROP TABLE IF EXISTS sc_im.capex;

CREATE TABLE IF NOT EXISTS sc_im.capex
(
    docno character(30) COLLATE pg_catalog."default" NOT NULL,
    docref character(30) COLLATE pg_catalog."default",
    doctype character(30) COLLATE pg_catalog."default",
    deptpic character(50) COLLATE pg_catalog."default",
    deppengguna character(50) COLLATE pg_catalog."default",
    deptpelaksana character(50) COLLATE pg_catalog."default",
    assetslocation character(50) COLLATE pg_catalog."default",
    docdate date,
    objcapex character(50) COLLATE pg_catalog."default",
    pricelst numeric(18,2),
    kodeform character(30) COLLATE pg_catalog."default",
    assetsjns character(50) COLLATE pg_catalog."default",
    assetsjnsothers character(50) COLLATE pg_catalog."default",
    docjns character(20) COLLATE pg_catalog."default",
    docjnscapex character(50) COLLATE pg_catalog."default",
    desc_jns text COLLATE pg_catalog."default",
    qtyasset numeric(18,2),
    age_estasset numeric,
    desc_spec text COLLATE pg_catalog."default",
    budgetjns character(30) COLLATE pg_catalog."default",
    budgetid character(30) COLLATE pg_catalog."default",
    valbudget numeric(18,2),
    valprice numeric(18,2),
    valremainbudget numeric(18,2),
    desc_unb text COLLATE pg_catalog."default",
    status character(20) COLLATE pg_catalog."default",
    inputby character(20) COLLATE pg_catalog."default",
    inputdate timestamp without time zone,
    docnotmp character(30) COLLATE pg_catalog."default",
    printby character(20) COLLATE pg_catalog."default",
    printdate timestamp without time zone,
    approveby character(20) COLLATE pg_catalog."default",
    approvedate timestamp without time zone,
    ttlprice numeric(18,2),
    typecapex character(20) COLLATE pg_catalog."default",
    accno character(30) COLLATE pg_catalog."default",
    approvalremarks text COLLATE pg_catalog."default",
    approvalbyfa character(20) COLLATE pg_catalog."default",
    approvaldatebyfa timestamp without time zone,
    offerby character(20) COLLATE pg_catalog."default",
    offerdate timestamp without time zone,
    plant character(20) COLLATE pg_catalog."default",
    updated_at timestamp without time zone,
    cancelby character(20) COLLATE pg_catalog."default",
    canceldate timestamp without time zone,
    cancelremarks text COLLATE pg_catalog."default",
    printcount integer DEFAULT 0,
    filedoc character(100) COLLATE pg_catalog."default",
    CONSTRAINT pk_sc_im_capex PRIMARY KEY (docno)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_im.capex
    OWNER to postgres;
	
	
-- ============================================================
-- TRANSFER DATA CAPEX: DB_Wms -> DB_Jaccounting_Trial
-- Source      : DB_Wms.sc_trx.capex
-- Destination : DB_Jaccounting_Trial.sc_im.capex
--
-- Jalankan file ini saat koneksi aktif ke database:
-- DB_Jaccounting_Trial
--
-- GANTI parameter koneksi di bawah:
--   HOST_WMS, USERNAME_WMS, PASSWORD_WMS
--
-- Catatan:
--   ON CONFLICT (docno) DO NOTHING mencegah error jika docno
--   sudah ada di tabel tujuan. Data yang sudah ada tidak di-update.
-- ============================================================

CREATE EXTENSION IF NOT EXISTS dblink;

BEGIN;

INSERT INTO sc_im.capex (
    docno,
    docref,
    doctype,
    deptpic,
    deppengguna,
    deptpelaksana,
    assetslocation,
    docdate,
    objcapex,
    pricelst,
    kodeform,
    assetsjns,
    assetsjnsothers,
    docjns,
    docjnscapex,
    desc_jns,
    qtyasset,
    age_estasset,
    desc_spec,
    budgetjns,
    budgetid,
    valbudget,
    valprice,
    valremainbudget,
    desc_unb,
    status,
    inputby,
    inputdate,
    docnotmp,
    printby,
    printdate,
    approveby,
    approvedate,
    ttlprice,
    typecapex,
    accno,
    approvalremarks,
    approvalbyfa,
    approvaldatebyfa,
    offerby,
    offerdate,
    plant,
    updated_at,
    cancelby,
    canceldate,
    cancelremarks,
    printcount,
    filedoc
)
SELECT
    src.docno,
    src.docref,
    src.doctype,
    src.deptpic,
    src.deppengguna,
    src.deptpelaksana,
    src.assetslocation,
    src.docdate,
    src.objcapex,
    src.pricelst,
    src.kodeform,
    src.assetsjns,
    src.assetsjnsothers,
    src.docjns,
    src.docjnscapex,
    src.desc_jns,
    src.qtyasset,
    src.age_estasset,
    src.desc_spec,
    src.budgetjns,
    src.budgetid,
    src.valbudget,
    src.valprice,
    src.valremainbudget,
    src.desc_unb,
    src.status,
    src.inputby,
    src.inputdate,
    src.docnotmp,
    src.printby,
    src.printdate,
    src.approveby,
    src.approvedate,
    src.ttlprice,
    src.typecapex,
    src.accno,
    src.approvalremarks,
    src.approvalbyfa,
    src.approvaldatebyfa,
    src.offerby,
    src.offerdate,
    src.plant,
    src.updated_at,
    src.cancelby,
    src.canceldate,
    src.cancelremarks,
    src.printcount,
    src.filedoc
FROM dblink(
    'host=10.0.0.4 port=5430 dbname=DB_Wms user=postgres password=ADMjts8888',
    $REMOTE$
        SELECT
            docno,
            docref,
            doctype,
            deptpic,
            deppengguna,
            deptpelaksana,
            assetslocation,
            docdate,
            objcapex,
            pricelst,
            kodeform,
            assetsjns,
            assetsjnsothers,
            docjns,
            docjnscapex,
            desc_jns,
            qtyasset,
            age_estasset,
            desc_spec,
            budgetjns,
            budgetid,
            valbudget,
            valprice,
            valremainbudget,
            desc_unb,
            status,
            inputby,
            inputdate,
            docnotmp,
            printby,
            printdate,
            approveby,
            approvedate,
            ttlprice,
            typecapex,
            accno,
            approvalremarks,
            approvalbyfa,
            approvaldatebyfa,
            offerby,
            offerdate,
            plant,
            updated_at,
            cancelby,
            canceldate,
            cancelremarks,
            printcount,
            filedoc
        FROM sc_trx.capex
    $REMOTE$
) AS src (
    docno character(30),
    docref character(30),
    doctype character(30),
    deptpic character(50),
    deppengguna character(50),
    deptpelaksana character(50),
    assetslocation character(50),
    docdate date,
    objcapex character(50),
    pricelst numeric(18,2),
    kodeform character(30),
    assetsjns character(50),
    assetsjnsothers character(50),
    docjns character(20),
    docjnscapex character(50),
    desc_jns text,
    qtyasset numeric(18,2),
    age_estasset numeric,
    desc_spec text,
    budgetjns character(30),
    budgetid character(30),
    valbudget numeric(18,2),
    valprice numeric(18,2),
    valremainbudget numeric(18,2),
    desc_unb text,
    status character(20),
    inputby character(20),
    inputdate timestamp without time zone,
    docnotmp character(30),
    printby character(20),
    printdate timestamp without time zone,
    approveby character(20),
    approvedate timestamp without time zone,
    ttlprice numeric(18,2),
    typecapex character(20),
    accno character(30),
    approvalremarks text,
    approvalbyfa character(20),
    approvaldatebyfa timestamp without time zone,
    offerby character(20),
    offerdate timestamp without time zone,
    plant character(20),
    updated_at timestamp without time zone,
    cancelby character(20),
    canceldate timestamp without time zone,
    cancelremarks text,
    printcount integer,
    filedoc character(100)
)
ON CONFLICT (docno) DO NOTHING;

-- Ringkasan hasil pada database tujuan
SELECT
    COUNT(*) AS total_data_capex_tujuan
FROM sc_im.capex;

COMMIT;
	