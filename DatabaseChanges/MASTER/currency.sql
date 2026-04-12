AUD	AUSTRALIA DOLLAR (LUAR NEGRI)
EUR	EURO (LUAR NEGERI)
IDR	RUPIAH
RMB	CHINESE YUAN (LUAR NEGERI)
SGD	SINGAPORE DOLLAR ( LUAR NEGERI )
USD	UNITED STATES DOLLAR ( LUAR NEGERI )
YEN	YEN JEPANG ( LUAR NEGERI )

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
	
delete from sc_mst.exchangerate ;
delete from sc_mst.currency ;
INSERT INTO sc_mst.currency 
(currcode, currname, createdby,
phutang, pum, pbonus, hutangac, hutangbiaya1, hutangbiaya2,
ppiutang, pumjual, ppendapatan, pretur, pdisc, pbonusjual, ptunai, piutangac, pendapatanac, pps)
VALUES

-- IDR
('IDR','RUPIAH','SYSTEM',
NULL,NULL,NULL,NULL,NULL,NULL,
NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),

-- USD
('USD','UNITED STATES DOLLAR (LUAR NEGERI)','SYSTEM',
NULL,NULL,NULL,NULL,NULL,NULL,
NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),

-- EUR
('EUR','EURO (LUAR NEGERI)','SYSTEM',
NULL,NULL,NULL,NULL,NULL,NULL,
NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),

-- AUD
('AUD','AUSTRALIA DOLLAR (LUAR NEGERI)','SYSTEM',
NULL,NULL,NULL,NULL,NULL,NULL,
NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),

-- SGD
('SGD','SINGAPORE DOLLAR (LUAR NEGERI)','SYSTEM',
NULL,NULL,NULL,NULL,NULL,NULL,
NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),

-- RMB
('RMB','CHINESE YUAN (LUAR NEGERI)','SYSTEM',
NULL,NULL,NULL,NULL,NULL,NULL,
NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),

-- YEN
('YEN','YEN JEPANG (LUAR NEGERI)','SYSTEM',
NULL,NULL,NULL,NULL,NULL,NULL,
NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);

	
	
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
add column createdby CHARACTER(20),
add column createddate TIMESTAMP WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP,
add column updateby CHARACTER(20),
add column updatedate TIMESTAMP WITHOUT TIME ZONE


/* nilai coa persediaan*/
UPDATE sc_mst.currency
SET
    -- ========================
    -- PEMBELIAN
    -- ========================
    phutang = '213102',        -- Hutang Dagang DN-BHN Penolong
    pum = '117101',            -- UM Pembelian
    pbonus = '5116',           -- Potongan Pembelian
    hutangac = '213101',       -- Hutang Antar Cabang
    hutangbiaya1 = '216101',   -- Hutang Lain
    hutangbiaya2 = '216101',   -- Hutang Lain

    -- ========================
    -- PENJUALAN
    -- ========================
    ppiutang = '113101',       -- Piutang Dagang
    pumjual = '218101',        -- UM Penjualan
    ppendapatan = '411101',    -- Pendapatan
    pretur = '431101',         -- Retur Penjualan
    pdisc = '421101',          -- Discount Penjualan
    pbonusjual = '421101',     -- Bonus Penjualan
    ptunai = '111103',         -- Kas
    piutangac = '113101',      -- Piutang Antar Cabang
    pendapatanac = '411101',   -- Pendapatan Antar Cabang
    pps = '711101',            -- Pendapatan Service

    -- ========================
    -- AUDIT
    -- ========================
    updateby = 'SYSTEM',
    updatedate = NOW()

WHERE currcode = 'IDR';

UPDATE sc_mst.currency
SET
    phutang = '213201',      -- hutang USD
    ppiutang = '113201',     -- piutang USD
    ppendapatan = '411201',  -- revenue USD
    ptunai = '111101',
    updateby = 'SYSTEM',
    updatedate = NOW()
WHERE currcode = 'USD';