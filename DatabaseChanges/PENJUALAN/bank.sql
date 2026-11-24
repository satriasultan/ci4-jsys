-- Table: sc_mst.banks

CREATE TABLE IF NOT EXISTS sc_mst.banks
(
    id serial NOT NULL,
    nmbank character varying(100) COLLATE pg_catalog."default",
    alamat text COLLATE pg_catalog."default",
    kodepos character varying(20) COLLATE pg_catalog."default",
    accname character varying(100) COLLATE pg_catalog."default",
    accno text COLLATE pg_catalog."default",
    swiftcode character varying(50) COLLATE pg_catalog."default",
    isactive boolean DEFAULT true,
    created_by character(60) COLLATE pg_catalog."default",
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_by character(60) COLLATE pg_catalog."default",
    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    catatan text COLLATE pg_catalog."default",
    status character(6) COLLATE pg_catalog."default",
    CONSTRAINT banks_pkey PRIMARY KEY (id)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_mst.banks
    OWNER to postgres;


INSERT INTO sc_mst.banks (
    nmbank,
    alamat,
    kodepos,
    accname,
    accno,
    swiftcode,
    isactive,
    created_by,
    created_at,
    updated_by,
    updated_at,
    catatan,
    status
) VALUES (
    'MUFG BANK, LTD',
    'GRAHA BUMI SURABAYA, JL BASUKI RAHMAT 106-128 SURABAYA 60271, INDONESIA',
    '60271',
    'PT JATIM TAMAN STEEL MANUFACTURING',
    '71.000.14196(IDR) ; 72.002.36280(JPY) ; 73.002.36276 (USD)',
    'BOTKIDJX',
    true,
    'SULTAN',
    '2025-09-16 16:03:35',
    NULL,
    '2025-09-16 16:03:35.467351',
    NULL,
    'F'
);