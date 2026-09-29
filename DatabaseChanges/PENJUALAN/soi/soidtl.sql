BEGIN;

-- =========================================================
-- DROP TABLE LAMA
-- =========================================================

DROP TABLE IF EXISTS sc_tmp.soidtl CASCADE;
DROP TABLE IF EXISTS sc_trx.soidtl CASCADE;


-- =========================================================
-- SC_TMP.SOIDTL
-- =========================================================

CREATE TABLE sc_tmp.soidtl
(
    idurut SERIAL NOT NULL,

    docno CHARACTER(30) NOT NULL,

    idbarang CHARACTER(20),

    cust CHARACTER(100),

    nmbarang CHARACTER(150),

    grade CHARACTER(100),

    size CHARACTER(100),

    cutlength CHARACTER(100),

    qty NUMERIC(18,2),

    unit CHARACTER(20),

    usdmt NUMERIC(18,2),

    price NUMERIC(18,2),

    exchange NUMERIC(18,2),

    amount NUMERIC(18,2),

    etd CHARACTER(20),

    ordernumbermsr CHARACTER(50),

    specno CHARACTER(150),

    totaldelivery NUMERIC(18,2),

    balanceorder NUMERIC(18,2),

    description TEXT,

    status CHARACTER(6),

    inputby CHARACTER VARYING(50),

    inputdate TIMESTAMP WITHOUT TIME ZONE,

    updateby CHARACTER VARYING(50),

    updatedate TIMESTAMP WITHOUT TIME ZONE,

    docnotmp CHARACTER(30),

    CONSTRAINT soidtl_tmp_pkey
        PRIMARY KEY (idurut)
)
TABLESPACE pg_default;


ALTER TABLE sc_tmp.soidtl
    OWNER TO postgres;


-- =========================================================
-- SC_TRX.SOIDTL
-- =========================================================

CREATE TABLE sc_trx.soidtl
(
    idurut SERIAL NOT NULL,

    docno CHARACTER(30) NOT NULL,

    idbarang CHARACTER(20),

    cust CHARACTER(100),

    nmbarang CHARACTER(150),

    grade CHARACTER(100),

    size CHARACTER(100),

    cutlength CHARACTER(100),

    qty NUMERIC(18,2),

    unit CHARACTER(20),

    usdmt NUMERIC(18,2),

    price NUMERIC(18,2),

    exchange NUMERIC(18,2),

    amount NUMERIC(18,2),

    etd CHARACTER(20),

    ordernumbermsr CHARACTER(50),

    specno CHARACTER(150),

    totaldelivery NUMERIC(18,2),

    balanceorder NUMERIC(18,2),

    description TEXT,

    status CHARACTER(6),

    inputby CHARACTER VARYING(50),

    inputdate TIMESTAMP WITHOUT TIME ZONE,

    updateby CHARACTER VARYING(50),

    updatedate TIMESTAMP WITHOUT TIME ZONE,

    docnotmp CHARACTER(30),

    CONSTRAINT soidtl_pkey
        PRIMARY KEY (idurut)
)
TABLESPACE pg_default;


ALTER TABLE sc_trx.soidtl
    OWNER TO postgres;


COMMIT;