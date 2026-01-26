
CREATE TABLE IF NOT EXISTS sc_mst.currency
(
    id SERIAL NOT NULL,
    currcode character(3) COLLATE pg_catalog."default" NOT NULL,
    currname character(50) COLLATE pg_catalog."default" NOT NULL,
    createdby CHAR(20),
    createddate TIMESTAMP WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updateby CHAR(20),
    updatedate TIMESTAMP WITHOUT TIME ZONE,
    --pembelian
    phutang char(20),
    pum char(20),
    pbonus char(20),
    hutangac char(20),
    hutangbiaya1 char(20),
    hutangbiaya2 char(20),
    --penjualan
    ppiutang char(20),
    pumjual char(20),
    ppendapatan char(20),
    pretur char(20),
    pdisc char(20),
    pbonusjual char(20),
    ptunai char(20),
    piutangac char(20),
    pendapatanac char(20),
    pps char(20),
    CONSTRAINT currency_pkey PRIMARY KEY (id)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_mst.currency
    OWNER to postgres;
-- Table: sc_mst.exchangerate

-- DROP TABLE IF EXISTS sc_mst.exchangerate;

CREATE TABLE IF NOT EXISTS sc_mst.exchangerate
(
    id SERIAL NOT NULL,
    exchangedate timestamp without time zone,
    nilai numeric(18,2),
    idcurr integer NOT NULL,
    CONSTRAINT exchangerate_pkey PRIMARY KEY (id),
    CONSTRAINT fk_currcode FOREIGN KEY (idcurr)
        REFERENCES sc_mst.currency (id) MATCH SIMPLE
        ON UPDATE CASCADE
        ON DELETE RESTRICT
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_mst.exchangerate
    OWNER to postgres;
