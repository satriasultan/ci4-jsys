
/* untuk module 'I.T.D.1 UPLOAD PP*/

-- Table: sc_im.lap_po_win

-- DROP TABLE IF EXISTS sc_im.lap_po_win;

CREATE TABLE IF NOT EXISTS sc_im.lap_po_win
(
    id  bigserial,
    obl text COLLATE pg_catalog."default" NOT NULL,
    jurnal text COLLATE pg_catalog."default" NOT NULL,
    date_ text COLLATE pg_catalog."default",
    sub text COLLATE pg_catalog."default",
    brg text COLLATE pg_catalog."default" NOT NULL,
    gdgobl text COLLATE pg_catalog."default",
    qty text COLLATE pg_catalog."default",
    lns text COLLATE pg_catalog."default",
    val text COLLATE pg_catalog."default",
    job text COLLATE pg_catalog."default",
    unit text COLLATE pg_catalog."default",
    uniqueidobd text COLLATE pg_catalog."default" NOT NULL,
    nama_supplier text COLLATE pg_catalog."default",
    nama_barang text COLLATE pg_catalog."default",
    jobname text COLLATE pg_catalog."default",
    priceobl text COLLATE pg_catalog."default",
    rempd text COLLATE pg_catalog."default" NOT NULL,
    tglkirim text COLLATE pg_catalog."default" NOT NULL,
    status character(6) COLLATE pg_catalog."default",
    inputdate timestamp without time zone,
    inputby character(20) COLLATE pg_catalog."default",
    updatedate timestamp without time zone,
    updateby character varying(20) COLLATE pg_catalog."default",
    CONSTRAINT lap_po_win_pkey PRIMARY KEY (obl, jurnal, brg, uniqueidobd, rempd, tglkirim)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_im.lap_po_win
    OWNER to postgres;
	
	
	
	
	
	
	
	
	
	
	
	/* untuk module 'I.T.D.2 UPLOAD PP*/
	
	-- Table: sc_im.lap_pp_win

-- DROP TABLE IF EXISTS sc_im.lap_pp_win;

CREATE TABLE IF NOT EXISTS sc_im.lap_pp_win
(
    id  bigserial,
    pp text COLLATE pg_catalog."default" NOT NULL,
    jurnal text COLLATE pg_catalog."default" NOT NULL,
    date_ text COLLATE pg_catalog."default" NOT NULL,
    username text COLLATE pg_catalog."default",
    brg text COLLATE pg_catalog."default" NOT NULL,
    qty text COLLATE pg_catalog."default",
    lns text COLLATE pg_catalog."default",
    gdg text COLLATE pg_catalog."default",
    nama_user text COLLATE pg_catalog."default",
    nama_barang text COLLATE pg_catalog."default",
    unit text COLLATE pg_catalog."default",
    spec text COLLATE pg_catalog."default",
    job text COLLATE pg_catalog."default",
    rem_pp text COLLATE pg_catalog."default" NOT NULL,
    rem_pd text COLLATE pg_catalog."default" NOT NULL,
    date_pakai text COLLATE pg_catalog."default",
    uniqueidpd text COLLATE pg_catalog."default" NOT NULL,
    status character(6) COLLATE pg_catalog."default",
    inputdate timestamp without time zone,
    inputby character(20) COLLATE pg_catalog."default",
    updatedate timestamp without time zone,
    updateby character varying(20) COLLATE pg_catalog."default",
    CONSTRAINT lap_pp_win_pkey PRIMARY KEY (pp, jurnal, date_, brg, rem_pp, rem_pd, uniqueidpd)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_im.lap_pp_win
    OWNER to postgres;