BEGIN;

-- =========================================================
-- REDROP TABLE LAMA
-- =========================================================
DROP TABLE IF EXISTS sc_tmp.mbarang CASCADE;
DROP TABLE IF EXISTS sc_mst.mbarang CASCADE;


-- =========================================================
-- SC_TMP.MBARANG
-- =========================================================
CREATE TABLE sc_tmp.mbarang
(
    id serial NOT NULL,
    idbarang character(20) NOT NULL,
    nmbarang character(150),
    idgroup character(6),
    idsubgroup character(12),
    idtype character(12),
    grade character(20),
    lsize character(20),
    deflocation character(12),
    defarea character(30),
    description text,
    unit character(12),
    subunit character(12),
    subunitenable character(10) DEFAULT 'NO'::bpchar,
    lastprice numeric(18,2),
    onhand numeric(18,2),
    allocated numeric(18,2),
    uninvoiced numeric(18,2),
    tmpalloca numeric(18,2),
    lastrxdate timestamp without time zone,
    lastrxdoc character(20),
    idbarcode character(30),
    sku character(30),
    expdate date,
    batch character(20),
    mfgdate date,
    maks_daystock numeric,
    chold character(6),
    inputby character(20),
    inputdate timestamp without time zone,
    status character(6),
    setminstock character(6),
    minstock numeric(18,2),
    kdtax character(50),
    originalno character(100),
    satuantax character(30),
    volume numeric(18,2),
    berat numeric(18,2),
    gw numeric(18,2),
    psize numeric(18,2),
    tsize numeric(18,2),
    lokasireff character(50),
    idgolonganbarang character(20),
    idjenisproduk character(20),
    idkelompokbarang character(20),
    idprincipal character(20),
    ppersediaan character(20),
    psj character(20),
    salesakun character(20),
    pcogs character(20),
    phpproduksi character(20),
    pjasa character(20),
    pwaste character(20),
    discontinue character(6),
    issn character(6),
    grouptype character(10) DEFAULT 'STOCK'::bpchar,
    defaultcurrency character varying(10) DEFAULT 'IDR'::character varying,
    actualcost numeric(18,2) DEFAULT 0,
    lastcost numeric(18,2) DEFAULT 0,
    CONSTRAINT pk_sc_tmp_mbarang PRIMARY KEY (idbarang)
)
TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_tmp.mbarang OWNER TO postgres;

-- =========================================================
-- SC_MST.MBARANG
-- =========================================================
CREATE TABLE sc_mst.mbarang
(
    id serial NOT NULL,
    idbarang character(20) NOT NULL,
    nmbarang character(150),
    idgroup character(6),
    idsubgroup character(12),
    idtype character(12),
    grade character(20),
    lsize character(20),
    deflocation character(12),
    defarea character(30),
    description text,
    unit character(12),
    subunit character(12),
    subunitenable character(10) DEFAULT 'NO'::bpchar,
    lastprice numeric(18,2),
    onhand numeric(18,2),
    allocated numeric(18,2),
    uninvoiced numeric(18,2),
    tmpalloca numeric(18,2),
    lastrxdate timestamp without time zone,
    lastrxdoc character(20),
    idbarcode character(30),
    sku character(30),
    expdate date,
    batch character(20),
    mfgdate date,
    maks_daystock numeric,
    chold character(6),
    inputby character(20),
    inputdate timestamp without time zone,
    status character(6),
    setminstock character(6),
    minstock numeric(18,2),
    defaultcurrency character varying(10) DEFAULT 'IDR'::character varying,
    kdtax character(50),
    originalno character(100),
    satuantax character(30),
    volume numeric(18,2),
    berat numeric(18,2),
    gw numeric(18,2),
    psize numeric(18,2),
    tsize numeric(18,2),
    lokasireff character(50),
    idgolonganbarang character(20),
    idjenisproduk character(20),
    idkelompokbarang character(20),
    idprincipal character(20),
    ppersediaan character(20),
    psj character(20),
    salesakun character(20),
    pcogs character(20),
    phpproduksi character(20),
    pjasa character(20),
    pwaste character(20),
    discontinue character(6),
    issn character(6),
    grouptype character(10) DEFAULT 'STOCK'::bpchar,
    actualcost numeric(18,2) DEFAULT 0,
    lastcost numeric(18,2) DEFAULT 0,
    CONSTRAINT pk_sc_mst_mbarang PRIMARY KEY (idbarang)
)
TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_mst.mbarang OWNER TO postgres;

-- Pastikan panjang nama barang 150 karakter
ALTER TABLE sc_tmp.mbarang
    ALTER COLUMN nmbarang TYPE character(150);

ALTER TABLE sc_mst.mbarang
    ALTER COLUMN nmbarang TYPE character(150);

-- =========================================================
-- SC_MST.MBARANG_UNIT
-- =========================================================
CREATE TABLE IF NOT EXISTS sc_mst.mbarang_unit
(
    idbarang character(20) NOT NULL,
    idunit character varying(10) NOT NULL,
    basic_value numeric(18,2) DEFAULT 1,
    conv_value numeric(18,2),
    inputdate timestamp without time zone,
    inputby character(20),
    cdefault character(5) DEFAULT 'NO'::bpchar,
    chold character(4) DEFAULT 'NO'::bpchar,
    CONSTRAINT mbarang_unit_pkey PRIMARY KEY (idbarang, idunit)
)
TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_mst.mbarang_unit OWNER TO postgres;


-- =========================================================
-- FUNCTION: sc_tmp.tr_mbarang
--
-- STATUS I -> F:
-- 1. Copy seluruh kolom master ke sc_mst.mbarang jika belum ada
-- 2. Buat unit default jika belum ada
-- 3. Hapus data temporary
-- =========================================================
CREATE OR REPLACE FUNCTION sc_tmp.tr_mbarang()
RETURNS trigger
LANGUAGE plpgsql
AS $BODY$
BEGIN
    IF TG_OP = 'UPDATE'
       AND COALESCE(NEW.status, '') = 'F'
       AND COALESCE(OLD.status, '') = 'I'
    THEN
        -- =====================================================
        -- INSERT MASTER BARANG
        -- Semua kolom baru ikut dipindahkan.
        -- =====================================================
        INSERT INTO sc_mst.mbarang
        (
            idbarang,
            nmbarang,
            idgroup,
            idsubgroup,
            idtype,
            grade,
            lsize,
            deflocation,
            defarea,
            description,
            unit,
            subunit,
            subunitenable,
            lastprice,
            onhand,
            allocated,
            uninvoiced,
            tmpalloca,
            lastrxdate,
            lastrxdoc,
            idbarcode,
            sku,
            expdate,
            batch,
            mfgdate,
            maks_daystock,
            chold,
            inputby,
            inputdate,
            status,
            setminstock,
            minstock,
            defaultcurrency,
            kdtax,
            originalno,
            satuantax,
            volume,
            berat,
            gw,
            psize,
            tsize,
            lokasireff,
            idgolonganbarang,
            idjenisproduk,
            idkelompokbarang,
            idprincipal,
            ppersediaan,
            psj,
            salesakun,
            pcogs,
            phpproduksi,
            pjasa,
            pwaste,
            discontinue,
            issn,
            grouptype,
            actualcost,
            lastcost
        )
        SELECT
            NEW.idbarang,
            NEW.nmbarang,
            NEW.idgroup,
            NEW.idsubgroup,
            NEW.idtype,
            NEW.grade,
            NEW.lsize,
            NEW.deflocation,
            NEW.defarea,
            NEW.description,
            NEW.unit,
            NEW.subunit,
            NEW.subunitenable,
            NEW.lastprice,
            NEW.onhand,
            NEW.allocated,
            NEW.uninvoiced,
            NEW.tmpalloca,
            NEW.lastrxdate,
            NEW.lastrxdoc,
            NEW.idbarcode,
            NEW.sku,
            NEW.expdate,
            NEW.batch,
            NEW.mfgdate,
            NEW.maks_daystock,
            NEW.chold,
            NEW.inputby,
            NEW.inputdate,
            NEW.status,
            NEW.setminstock,
            NEW.minstock,
            NEW.defaultcurrency,
            NEW.kdtax,
            NEW.originalno,
            NEW.satuantax,
            NEW.volume,
            NEW.berat,
            NEW.gw,
            NEW.psize,
            NEW.tsize,
            NEW.lokasireff,
            NEW.idgolonganbarang,
            NEW.idjenisproduk,
            NEW.idkelompokbarang,
            NEW.idprincipal,
            NEW.ppersediaan,
            NEW.psj,
            NEW.salesakun,
            NEW.pcogs,
            NEW.phpproduksi,
            NEW.pjasa,
            NEW.pwaste,
            NEW.discontinue,
            NEW.issn,
            COALESCE(NULLIF(TRIM(NEW.grouptype), ''), 'STOCK'),
            COALESCE(NEW.actualcost, 0),
            COALESCE(NEW.lastcost, 0)
        WHERE NOT EXISTS
        (
            SELECT 1
            FROM sc_mst.mbarang m
            WHERE TRIM(m.idbarang) = TRIM(NEW.idbarang)
        );

        -- =====================================================
        -- INSERT UNIT DEFAULT JIKA BELUM ADA
        -- =====================================================
        IF NULLIF(TRIM(COALESCE(NEW.unit, '')), '') IS NOT NULL THEN
            -- Jika database mempunyai object sc_mst.mbarang_idunit,
            -- gunakan object tersebut. Jika tidak, gunakan
            -- sc_mst.mbarang_unit sesuai struktur pada script Anda.
            IF to_regclass('sc_mst.mbarang_idunit') IS NOT NULL THEN
                EXECUTE $SQL$
                    INSERT INTO sc_mst.mbarang_idunit
                    (
                        idbarang,
                        idunit,
                        basic_value,
                        conv_value,
                        inputdate,
                        inputby,
                        cdefault,
                        chold
                    )
                    SELECT
                        TRIM($1),
                        TRIM($2),
                        1,
                        1,
                        CURRENT_TIMESTAMP,
                        $3,
                        'YES',
                        'NO'
                    WHERE NOT EXISTS
                    (
                        SELECT 1
                        FROM sc_mst.mbarang_idunit u
                        WHERE TRIM(u.idbarang) = TRIM($1)
                          AND TRIM(u.idunit) = TRIM($2)
                    )
                $SQL$
                USING NEW.idbarang, NEW.unit, NEW.inputby;
            ELSE
                INSERT INTO sc_mst.mbarang_unit
                (
                    idbarang,
                    idunit,
                    basic_value,
                    conv_value,
                    inputdate,
                    inputby,
                    cdefault,
                    chold
                )
                SELECT
                    TRIM(NEW.idbarang),
                    TRIM(NEW.unit),
                    1,
                    1,
                    CURRENT_TIMESTAMP,
                    NEW.inputby,
                    'YES',
                    'NO'
                WHERE NOT EXISTS
                (
                    SELECT 1
                    FROM sc_mst.mbarang_unit u
                    WHERE TRIM(u.idbarang) = TRIM(NEW.idbarang)
                      AND TRIM(u.idunit) = TRIM(NEW.unit)
                );
            END IF;
        END IF;

        -- =====================================================
        -- CLEANUP TEMP
        -- =====================================================
        DELETE FROM sc_tmp.mbarang
        WHERE TRIM(idbarang) = TRIM(NEW.idbarang)
          AND inputby = NEW.inputby;
    END IF;

    RETURN NEW;
END;
$BODY$;

ALTER FUNCTION sc_tmp.tr_mbarang()
    OWNER TO postgres;


-- =========================================================
-- TRIGGER
-- Function dibuat terlebih dahulu agar trigger aman dibuat
-- dalam sekali execute.
-- =========================================================
DROP TRIGGER IF EXISTS tr_mbarang ON sc_tmp.mbarang;

CREATE TRIGGER tr_mbarang
    AFTER UPDATE
    ON sc_tmp.mbarang
    FOR EACH ROW
    EXECUTE FUNCTION sc_tmp.tr_mbarang();


COMMIT;
