
CREATE TABLE IF NOT EXISTS sc_mst.currency
(
    id SERIAL NOT NULL,
    currcode character(3) COLLATE pg_catalog."default" NOT NULL,
    currname character(50) COLLATE pg_catalog."default" NOT NULL,
    createdby CHARACTER(20),
    createddate TIMESTAMP WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updateby CHARACTER(20),
    updatedate TIMESTAMP WITHOUT TIME ZONE,
    --pembelian
    phutang character(20),
    pum character(20),
    pbonus character(20),
    hutangac character(20),
    hutangbiaya1 character(20),
    hutangbiaya2 character(20),
    --penjualan
    ppiutang character(20),
    pumjual character(20),
    ppendapatan character(20),
    pretur character(20),
    pdisc character(20),
    pbonusjual character(20),
    ptunai character(20),
    piutangac character(20),
    pendapatanac character(20),
    pps character(20),
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




ALTER TABLE sc_mst.exchangerate
createdby CHARACTER(20),
createddate TIMESTAMP WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP,
updateby CHARACTER(20),
updatedate TIMESTAMP WITHOUT TIME ZONE