BEGIN;

-- =========================================================
-- DROP TABLE LAMA
-- =========================================================

DROP TABLE IF EXISTS sc_tmp.proformadtl CASCADE;
DROP TABLE IF EXISTS sc_trx.proformadtl CASCADE;


-- =========================================================
-- SC_TMP.PROFORMADTL
-- =========================================================

CREATE TABLE sc_tmp.proformadtl
(
    idurut SERIAL NOT NULL,

    docno CHARACTER(30) NOT NULL,

    idbarang CHARACTER(20),

    nmbarang CHARACTER(150),

    unit CHARACTER(20),

    qty NUMERIC(18,2),

    price NUMERIC(18,2),

    usdmt NUMERIC(18,2),

    exchange NUMERIC(18,2),

    amount NUMERIC(18,2),

    description TEXT,

    status CHARACTER(6),

    inputby CHARACTER VARYING(50),

    inputdate TIMESTAMP WITHOUT TIME ZONE,

    updateby CHARACTER VARYING(50),

    updatedate TIMESTAMP WITHOUT TIME ZONE,

    docnotmp CHARACTER(30),

    CONSTRAINT proformadtl_tmp_pkey
        PRIMARY KEY (idurut)
)
TABLESPACE pg_default;

ALTER TABLE sc_tmp.proformadtl
    OWNER TO postgres;


-- =========================================================
-- SC_TRX.PROFORMADTL
-- =========================================================

CREATE TABLE sc_trx.proformadtl
(
    idurut SERIAL NOT NULL,

    docno CHARACTER(30) NOT NULL,

    idbarang CHARACTER(20),

    nmbarang CHARACTER(150),

    unit CHARACTER(20),

    qty NUMERIC(18,2),

    price NUMERIC(18,2),

    usdmt NUMERIC(18,2),

    exchange NUMERIC(18,2),

    amount NUMERIC(18,2),

    description TEXT,

    status CHARACTER(6),

    inputby CHARACTER VARYING(50),

    inputdate TIMESTAMP WITHOUT TIME ZONE,

    updateby CHARACTER VARYING(50),

    updatedate TIMESTAMP WITHOUT TIME ZONE,

    docnotmp CHARACTER(30),

    CONSTRAINT proformadtl_pkey
        PRIMARY KEY (idurut)
)
TABLESPACE pg_default;

ALTER TABLE sc_trx.proformadtl
    OWNER TO postgres;


COMMIT;