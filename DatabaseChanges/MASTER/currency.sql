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

ALTER TABLE sc_mst.currency
ADD CONSTRAINT currency_currcode_unique
UNIQUE (currcode);

/* nilai coa persediaan*/
BEGIN;

-- =========================================================
-- CLEAN EXISTING MASTER
-- =========================================================

DELETE FROM sc_mst.exchangerate;
DELETE FROM sc_mst.currency;


-- =========================================================
-- INSERT MASTER CURRENCY + COA
-- =========================================================

INSERT INTO sc_mst.currency
(
    currcode,
    currname,
    createdby,

    -- PEMBELIAN
    phutang,
    pum,
    pbonus,
    hutangac,
    hutangbiaya1,
    hutangbiaya2,

    -- PENJUALAN
    ppiutang,
    pumjual,
    ppendapatan,
    pretur,
    pdisc,
    pbonusjual,
    ptunai,
    piutangac,
    pendapatanac,
    pps,

    createddate,
    updateby,
    updatedate
)
VALUES

-- =========================================================
-- IDR
-- =========================================================
(
    'IDR',
    'RUPIAH',
    'SYSTEM',

    -- PEMBELIAN
    '213102',
    '117101',
    '5116',
    '213101',
    '216101',
    '216101',

    -- PENJUALAN
    '113101',
    '218101',
    '411101',
    '431101',
    '421101',
    '421101',
    '111103',
    '113101',
    '411101',
    '441101',

    NOW(),
    'SYSTEM',
    NOW()
),

-- =========================================================
-- USD
-- =========================================================
(
    'USD',
    'UNITED STATES DOLLAR (LUAR NEGERI)',
    'SYSTEM',

    -- PEMBELIAN
    '213201',
    '117102',
    '5116',
    '213201',
    '213201',
    '213201',

    -- PENJUALAN
    '113201',
    '218102',
    '411201',
    '431201',
    '421201',
    '421201',
    '111103',
    '113201',
    '441101',
    '441101',

    NOW(),
    'SYSTEM',
    NOW()
),

-- =========================================================
-- EUR
-- =========================================================
(
    'EUR',
    'EURO (LUAR NEGERI)',
    'SYSTEM',

    -- PEMBELIAN
    '213201',
    '117202',
    '5116',
    '213201',
    '213201',
    '213201',

    -- PENJUALAN
    '113201',
    '218102',
    '411201',
    '431201',
    '421201',
    '441101',
    '111103',
    '113201',
    '441101',
    '441101',

    NOW(),
    'SYSTEM',
    NOW()
),

-- =========================================================
-- AUD
-- =========================================================
(
    'AUD',
    'AUSTRALIA DOLLAR (LUAR NEGERI)',
    'SYSTEM',

    -- PEMBELIAN
    '213102',
    '117102',
    '5116',
    '213102',
    '213102',
    '213102',

    -- PENJUALAN
    '113102',
    '218102',
    '411101',
    '431102',
    '421102',
    '441101',
    '111103',
    '113102',
    '441101',
    '441101',

    NOW(),
    'SYSTEM',
    NOW()
),

-- =========================================================
-- SGD
-- =========================================================
(
    'SGD',
    'SINGAPORE DOLLAR (LUAR NEGERI)',
    'SYSTEM',

    -- PEMBELIAN
    '213201',
    '117202',
    '5116',
    '213201',
    '213201',
    '213201',

    -- PENJUALAN
    '113201',
    '218102',
    '411201',
    '431201',
    '421201',
    '421201',
    '111103',
    '113201',
    '441101',
    '441101',

    NOW(),
    'SYSTEM',
    NOW()
),

-- =========================================================
-- RMB
-- =========================================================
(
    'RMB',
    'CHINESE YUAN (LUAR NEGERI)',
    'SYSTEM',

    -- PEMBELIAN
    '213201',
    '117202',
    '5116',
    '213201',
    '213201',
    '213201',

    -- PENJUALAN
    '113201',
    '218102',
    '411201',
    '431201',
    '421201',
    '421201',
    '111103',
    '113201',
    '411101',
    '441101',

    NOW(),
    'SYSTEM',
    NOW()
),

-- =========================================================
-- YEN
-- =========================================================
(
    'YEN',
    'YEN JEPANG (LUAR NEGERI)',
    'SYSTEM',

    -- PEMBELIAN
    '213201',
    '117102',
    '5116',
    '213201',
    '213201',
    '213201',

    -- PENJUALAN
    '113201',
    '218102',
    '411201',
    '431201',
    '421201',
    '421201',
    '111103',
    '113201',
    '441101',
    '441101',

    NOW(),
    'SYSTEM',
    NOW()
);

COMMIT;