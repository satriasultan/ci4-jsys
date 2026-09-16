
-- JALANKAN INI DULU
DROP TABLE IF EXISTS sc_trx.lpb;
DROP TABLE IF EXISTS sc_tmp.lpb;
DROP TABLE IF EXISTS sc_tmp.lpb_dtl;
DROP TABLE IF EXISTS sc_trx.lpb_dtl;




DROP TABLE IF EXISTS sc_tmp.lpb CASCADE;

CREATE TABLE sc_tmp.lpb
(
    idurut          SERIAL NOT NULL,
    docno           CHARACTER(30) NOT NULL,
    docdate         DATE,

    cabang          CHARACTER(30),
    pemohon         CHARACTER(100),

    kdsupplier      CHARACTER(30),
    nmsupplier      CHARACTER(250),
    alamatsupplier  TEXT,

    jthtempo        NUMERIC(18,2),

    biayavol        NUMERIC(18,2),
    biayavol2       NUMERIC(18,2),

    idtax           CHARACTER(20),
    isinclusive     CHARACTER(6),

    currcode        CHARACTER(3),
    kurs            NUMERIC(18,2),

    nofaktur        CHARACTER(30),
    nosj            CHARACTER(30),

    dpp             NUMERIC(18,2),
    jumlahpajak     NUMERIC(18,2),
    total           NUMERIC(18,2),

    status          CHARACTER(6),
    keterangan      TEXT,

    inputby         VARCHAR(50),
    inputdate       TIMESTAMP WITHOUT TIME ZONE,

    updateby        VARCHAR(50),
    updatedate      TIMESTAMP WITHOUT TIME ZONE,

    printby         VARCHAR(50),
    printdate       TIMESTAMP WITHOUT TIME ZONE,

    docnotmp        CHARACTER(30),

    -- sudah termasuk ALTER TABLE sebelumnya
    doctype         CHARACTER(10) DEFAULT 'GR',

    CONSTRAINT pk_tmp_lpb
        PRIMARY KEY (docno)
)
TABLESPACE pg_default;

ALTER TABLE sc_tmp.lpb
    OWNER TO postgres;


DROP TABLE IF EXISTS sc_trx.lpb CASCADE;

CREATE TABLE sc_trx.lpb
(
    idurut          SERIAL NOT NULL,
    docno           CHARACTER(30) NOT NULL,
    docdate         DATE,

    cabang          CHARACTER(30),
    pemohon         CHARACTER(100),

    kdsupplier      CHARACTER(30),
    nmsupplier      CHARACTER(250),
    alamatsupplier  TEXT,

    jthtempo        NUMERIC(18,2),

    biayavol        NUMERIC(18,2),
    biayavol2       NUMERIC(18,2),

    idtax           CHARACTER(20),
    isinclusive     CHARACTER(6),

    currcode        CHARACTER(3),
    kurs            NUMERIC(18,2),

    nofaktur        CHARACTER(30),
    nosj            CHARACTER(30),

    dpp             NUMERIC(18,2),
    jumlahpajak     NUMERIC(18,2),
    total           NUMERIC(18,2),

    status          CHARACTER(6),
    keterangan      TEXT,

    inputby         VARCHAR(50),
    inputdate       TIMESTAMP WITHOUT TIME ZONE,

    updateby        VARCHAR(50),
    updatedate      TIMESTAMP WITHOUT TIME ZONE,

    printby         VARCHAR(50),
    printdate       TIMESTAMP WITHOUT TIME ZONE,

    docnotmp        CHARACTER(30),

    -- sudah termasuk ALTER TABLE sebelumnya
    doctype         CHARACTER(10) DEFAULT 'GR',

    CONSTRAINT pk_trx_lpb
        PRIMARY KEY (docno)
)
TABLESPACE pg_default;

ALTER TABLE sc_trx.lpb
    OWNER TO postgres;

DROP TABLE IF EXISTS sc_tmp.lpb_dtl CASCADE;

CREATE TABLE sc_tmp.lpb_dtl
(
    idurut          SERIAL PRIMARY KEY,

    docno           CHARACTER(30) NOT NULL,
    docnopo         CHARACTER(30) NOT NULL,

    uniqueid        VARCHAR(64),

    idbarang        CHARACTER(20),
    nmbarang        CHARACTER(150),

    idprincipal     CHARACTER(20),
    idgudang        CHARACTER(30),
    idspec          CHARACTER(30),

    unit            CHARACTER(20),

    qty             NUMERIC(18,2),
    qtybonus        NUMERIC(18,2),

    harga           NUMERIC(18,2),

    multidisc       NUMERIC(18,2),

    volitem         NUMERIC(18,2),

    biaya           NUMERIC(18,2),
    biaya2          NUMERIC(18,2),

    nilai           NUMERIC(18,2),

    descriptionpo   TEXT,
    descriptionpp   TEXT,

    status          CHARACTER(6),

    inputby         VARCHAR(50),
    inputdate       TIMESTAMP WITHOUT TIME ZONE,

    updateby        VARCHAR(50),
    updatedate      TIMESTAMP WITHOUT TIME ZONE,

    docnotmp        CHARACTER(30),

    -- =========================================
    -- KOLOM YANG SEBELUMNYA DITAMBAHKAN ALTER
    -- =========================================

    doctype         CHARACTER(10) DEFAULT 'GR',

    idtax           CHARACTER(20),
    currcode        CHARACTER(3),
    kurs            NUMERIC(18,2),

    nilaikonversi   NUMERIC(18,2),
    nilaipajak      NUMERIC(18,2),

    qtyretur        NUMERIC(18,2) DEFAULT 0

)
TABLESPACE pg_default;

ALTER TABLE sc_tmp.lpb_dtl
    OWNER TO postgres;



DROP TABLE IF EXISTS sc_trx.lpb_dtl CASCADE;

CREATE TABLE sc_trx.lpb_dtl
(
    idurut          SERIAL PRIMARY KEY,

    docno           CHARACTER(30) NOT NULL,
    docnopo         CHARACTER(30) NOT NULL,

    uniqueid        VARCHAR(64),

    idbarang        CHARACTER(20),
    nmbarang        CHARACTER(150),

    idprincipal     CHARACTER(20),
    idgudang        CHARACTER(30),
    idspec          CHARACTER(30),

    unit            CHARACTER(20),

    qty             NUMERIC(18,2),
    qtybonus        NUMERIC(18,2),

    harga           NUMERIC(18,2),

    multidisc       NUMERIC(18,2),

    volitem         NUMERIC(18,2),

    biaya           NUMERIC(18,2),
    biaya2          NUMERIC(18,2),

    nilai           NUMERIC(18,2),

    descriptionpo   TEXT,
    descriptionpp   TEXT,

    status          CHARACTER(6),

    inputby         VARCHAR(50),
    inputdate       TIMESTAMP WITHOUT TIME ZONE,

    updateby        VARCHAR(50),
    updatedate      TIMESTAMP WITHOUT TIME ZONE,

    docnotmp        CHARACTER(30),

    -- =========================================
    -- KOLOM YANG SEBELUMNYA DITAMBAHKAN ALTER
    -- =========================================

    doctype         CHARACTER(10) DEFAULT 'GR',

    idtax           CHARACTER(20),
    currcode        CHARACTER(3),
    kurs            NUMERIC(18,2),

    nilaikonversi   NUMERIC(18,2),
    nilaipajak      NUMERIC(18,2),

    qtyretur        NUMERIC(18,2) DEFAULT 0

)
TABLESPACE pg_default;

ALTER TABLE sc_trx.lpb_dtl
    OWNER TO postgres;
	
	
	
	
	
	
/* FUNCTION STORE PROCEDURE TRIGGER */

-- FUNCTION: sc_tmp.tr_lpb_finalize()

-- DROP FUNCTION IF EXISTS sc_tmp.tr_lpb_finalize();
CREATE OR REPLACE FUNCTION sc_tmp.tr_lpb_finalize()
RETURNS trigger
LANGUAGE plpgsql
AS $BODY$
DECLARE
    v_docno     TEXT;
    v_inputby   TEXT;
    v_idurut    INTEGER;
    v_prefix    TEXT;
    v_num       TEXT;
    v_num_int   INTEGER;
    v_lock_key  BIGINT;
    v_base_docno TEXT;
    v_new_docno  TEXT;

    v_client_ip TEXT;
    v_uniqueid  VARCHAR(64);
BEGIN
    IF OLD.status = 'E' AND NEW.status = 'F' AND COALESCE(NEW.docnotmp, '') = '' THEN

        -- ===============================
        -- NORMALISASI
        v_docno := rtrim(NEW.docno);
        v_inputby := NEW.inputby;
        v_idurut  := NEW.idurut;
        -- ambil base docno (tanpa angka belakang)
        -- contoh:
        -- 05M/2601/PA0001 -> 05M/2601/PA
        -- PPB/2601/PT0025 -> PPB/2601/PT
        v_base_docno := regexp_replace(v_docno, '[0-9]+$', '');

        -- ===============================
        -- ADVISORY LOCK (ANTI RACE CONDITION)
        -- ===============================
        PERFORM pg_advisory_xact_lock(hashtext(v_base_docno));

        -- ===============================
        -- AUTO INCREMENT JIKA SUDAH ADA
        -- ===============================
        v_new_docno := v_docno;

        LOOP
            EXIT WHEN NOT EXISTS (
                SELECT 1
                FROM sc_trx.lpb
                WHERE rtrim(docno) = v_new_docno
            );

            -- ambil angka terakhir (dinamis)
            v_num := regexp_replace(v_new_docno, '.*?([0-9]+)$', '\1');
            v_num_int := v_num::INTEGER + 1;

            -- padding mengikuti panjang awal
            v_new_docno := v_base_docno
                        || lpad(v_num_int::TEXT, length(v_num), '0');
        END LOOP;

        -- gunakan docno final
        v_docno := v_new_docno;


        -- ===============================
        -- INSERT HEADER
        -- ===============================
        INSERT INTO sc_trx.lpb (
            idurut, docno, cabang, docdate, pemohon, kdsupplier,
            nmsupplier, alamatsupplier, jthtempo,
            biayavol, biayavol2, nosj, nofaktur,
            idtax,isinclusive, currcode, kurs, dpp, 
            jumlahpajak, total,
            keterangan, status, inputby, inputdate,
            updateby, updatedate, printby, printdate, printcount
        )
        SELECT
            idurut, v_docno, cabang, docdate, pemohon, kdsupplier,
            nmsupplier, alamatsupplier, jthtempo,
            biayavol, biayavol2, nosj, nofaktur,
            idtax,isinclusive, currcode, kurs, dpp, 
            jumlahpajak, total,
            keterangan, 'F', inputby, inputdate,
            updateby, updatedate, printby, printdate, printcount
        FROM sc_tmp.lpb
        WHERE rtrim(docno) = rtrim(OLD.docno)
            AND inputby = v_inputby
            AND idurut = v_idurut;

        -- ===============================
        -- INSERT DETAIL
        -- ===============================
        INSERT INTO sc_trx.lpb_dtl (
            idurut, docno, docnopo, idbarang, capexno, uniqueid,  nmbarang,
            idprincipal, idgudang, idspec, volitem, biaya, biaya2, unit, qty, 
            harga, nilai, descriptionpo, descriptionpp, multidisc,
            inputby, inputdate, status, updateby, updatedate,idtax,currcode,kurs,nilaikonversi,nilaipajak,qtyretur,idhistory_price,multidisctype,totaldiscount
        )
        SELECT
            idurut, v_docno, docnopo, idbarang, capexno, uniqueid,  nmbarang,
            idprincipal, idgudang, idspec, volitem, biaya, biaya2, unit, qty, 
            harga, nilai, descriptionpo, descriptionpp, multidisc,
            inputby, inputdate, status, updateby, updatedate,idtax,currcode,kurs,nilaikonversi,nilaipajak,qtyretur,idhistory_price,multidisctype,totaldiscount
        FROM sc_tmp.lpb_dtl
        WHERE rtrim(docno) = rtrim(OLD.docno)
            AND inputby = v_inputby;


        UPDATE sc_trx.po_dtl ppd
        SET qtylpb = COALESCE(ppd.qtylpb, 0) + pod.qty_used
            -- updateby = v_inputby,
            -- updatedate = CURRENT_TIMESTAMP
        FROM (
            SELECT 
                uniqueid,
                SUM(qty) as qty_used
            FROM sc_tmp.lpb_dtl
            WHERE rtrim(docno) = rtrim(OLD.docno)
                AND inputby = v_inputby
                AND uniqueid IS NOT NULL
                AND uniqueid <> ''
            GROUP BY uniqueid
        ) pod
        WHERE ppd.uniqueid = pod.uniqueid;

        UPDATE sc_trx.po_dtl ppd
            SET status = CASE 
                WHEN ppd.qty = COALESCE(ppd.qtylpb, 0) THEN 'LPB'
                ELSE 'F'
            END
            FROM sc_tmp.lpb_dtl t
            WHERE rtrim(t.docno) = rtrim(OLD.docno)
            AND t.inputby = v_inputby
            AND ppd.uniqueid = t.uniqueid;

        UPDATE sc_trx.po po
            SET status = 'LPB'
            WHERE po.docno IN (
                SELECT DISTINCT t.docnopo
                FROM sc_tmp.lpb_dtl t
                WHERE rtrim(t.docno) = rtrim(OLD.docno)
                AND t.inputby = v_inputby
                AND t.docnopo IS NOT NULL
                AND t.docnopo <> ''
            );
        /* NILAI PERSEDIAAN DAN NILAI COA */
 /* =========================================================
   STKBLC LPB
   SOURCE : sc_tmp.lpb + sc_tmp.lpb_dtl

   MENYIMPAN SNAPSHOT:
   - Currency
   - Tax
   - Inclusive / Exclusive
   - DPP
   - PPN
   - Bruto
   - COA Pajak
========================================================= */

PERFORM sc_trx.sp_unpost_by_doc(v_docno, 'GR');

DELETE FROM sc_trx.stkblc
WHERE TRIM(docno) = TRIM(v_docno)
  AND TRIM(doctype) = 'GR';


INSERT INTO sc_trx.stkblc
(
    idlocation,
    idarea,
    batch,
    idbarang,

    trxdate,
    doctype,
    docno,
    docref,

    qty_in,
    qty_out,

    pricelst_in,
    pricelst_out,

    currcode,
    currvalue,

    tax,
    disc,
    biaya,

    hist,
    ctype,

    idgroup,
    grouptype,

    is_posted,
    posted_at,

    picby,
    unit,
    subunit,
    description,

    created_at,
    created_by,
    status,

    uniqueid,

    /* =============================================
       TAX SNAPSHOT
    ============================================= */

    idtax,
    tax_percent,
    isinclusive,

    nilai_dpp,
    nilai_ppn,
    nilai_bruto,

    coa_tax_masukan,
    coa_tax_keluaran
)

SELECT

    /* =============================================
       LOCATION
    ============================================= */

    TRIM(d.idgudang),

    TRIM(d.idgudang) || '.0000',

    COALESCE(TRIM(d.idspec), ''),

    TRIM(d.idbarang),


    /* =============================================
       TRANSACTION
    ============================================= */

    CAST(h.docdate AS DATE) + NOW()::TIME,

    'GR',

    TRIM(v_docno),

    TRIM(d.docnopo),


    /* =============================================
       QTY

       NON STOCK tidak masuk inventory quantity
    ============================================= */

    CASE
        WHEN TRIM(COALESCE(mb.grouptype, 'STOCK')) = 'NON STOCK'
        THEN 0

        ELSE
            COALESCE(d.qty, 0)
            + COALESCE(d.qtybonus, 0)
    END,

    0,


    /* =============================================
       PRICE
    ============================================= */

    COALESCE(d.harga, 0),

    0,


    /* =============================================
       CURRENCY
    ============================================= */

    TRIM(COALESCE(d.currcode, h.currcode)),

    COALESCE(
        NULLIF(d.kurs, 0),
        NULLIF(h.kurs, 0),
        1
    ),


    /* =============================================
       TAX PERCENT
    ============================================= */

    COALESCE(tx.percentation, 0),

    /* DISCOUNT */
    COALESCE(d.totaldiscount, 0),

    /* BIAYA */
    COALESCE(d.biaya, 0)
    + COALESCE(d.biaya2, 0),


    /* =============================================
       HISTORY
    ============================================= */

    'LPB',


    /* =============================================
       CTYPE
    ============================================= */

    CASE
        WHEN TRIM(COALESCE(mb.grouptype, 'STOCK')) = 'NON STOCK'
        THEN 'NON'

        ELSE 'IN'
    END,


    /* =============================================
       GROUP BARANG
    ============================================= */

    mb.idgroup,

    COALESCE(mb.grouptype, 'STOCK'),


    /* =============================================
       POSTING STATUS
    ============================================= */

    FALSE,

    NULL,


    /* =============================================
       USER / UNIT
    ============================================= */

    h.inputby,

    d.unit,

    NULL,

    COALESCE(
        NULLIF(TRIM(d.descriptionpo), ''),
        NULLIF(TRIM(d.descriptionpp), ''),
        ''
    ),


    NOW(),

    h.inputby,

    'F',


    /* =============================================
       UNIQUE ID
    ============================================= */

    d.uniqueid,


    /* =====================================================
       TAX SNAPSHOT
    ===================================================== */

    NULLIF(TRIM(d.idtax), ''),

    COALESCE(tx.percentation, 0),

    CASE
        WHEN UPPER(TRIM(COALESCE(h.isinclusive, 'NO'))) = 'YES'
        THEN 'YES'
        ELSE 'NO'
    END,


    /* =====================================================
       NILAI DPP

       PRIORITAS:
       1. nilaikonversi dari LPB detail
       2. qty × harga × kurs
    ===================================================== */

    CASE

        /* ---------------------------------------------
           TAX INCLUSIVE
           Harga sudah termasuk pajak
        --------------------------------------------- */

        WHEN UPPER(TRIM(COALESCE(h.isinclusive, 'NO'))) = 'YES'
         AND COALESCE(tx.percentation, 0) > 0

        THEN

            COALESCE(
                d.nilaikonversi,

                (
                    COALESCE(d.qty, 0)
                    * COALESCE(d.harga, 0)
                    * COALESCE(
                        NULLIF(d.kurs, 0),
                        NULLIF(h.kurs, 0),
                        1
                    )
                )
            )
            /
            (
                1 + COALESCE(tx.percentation, 0) / 100
            )


        /* ---------------------------------------------
           TAX EXCLUSIVE
        --------------------------------------------- */

        ELSE

            COALESCE(
                d.nilaikonversi,

                COALESCE(d.qty, 0)
                * COALESCE(d.harga, 0)
                * COALESCE(
                    NULLIF(d.kurs, 0),
                    NULLIF(h.kurs, 0),
                    1
                )
            )

    END,


    /* =====================================================
       NILAI PPN

       PRIORITAS:
       nilaipajak LPB detail

       Jika belum ada:
       DPP × percentage
    ===================================================== */

    COALESCE(

        d.nilaipajak,

        CASE

            WHEN COALESCE(tx.percentation, 0) <= 0
            THEN 0

            WHEN UPPER(TRIM(COALESCE(h.isinclusive, 'NO'))) = 'YES'

            THEN

                (
                    COALESCE(
                        d.nilaikonversi,

                        COALESCE(d.qty, 0)
                        * COALESCE(d.harga, 0)
                        * COALESCE(
                            NULLIF(d.kurs, 0),
                            NULLIF(h.kurs, 0),
                            1
                        )
                    )
                )

                -

                (
                    COALESCE(
                        d.nilaikonversi,

                        COALESCE(d.qty, 0)
                        * COALESCE(d.harga, 0)
                        * COALESCE(
                            NULLIF(d.kurs, 0),
                            NULLIF(h.kurs, 0),
                            1
                        )
                    )
                    /
                    (1 + COALESCE(tx.percentation, 0) / 100)
                )

            ELSE

                (
                    COALESCE(
                        d.nilaikonversi,

                        COALESCE(d.qty, 0)
                        * COALESCE(d.harga, 0)
                        * COALESCE(
                            NULLIF(d.kurs, 0),
                            NULLIF(h.kurs, 0),
                            1
                        )
                    )
                )
                * COALESCE(tx.percentation, 0)
                / 100

        END

    ),


    /* =====================================================
       NILAI BRUTO
    ===================================================== */

    CASE

        /* TAX INCLUSIVE */
        WHEN UPPER(TRIM(COALESCE(h.isinclusive, 'NO'))) = 'YES'

        THEN

            COALESCE(
                d.nilaikonversi,

                COALESCE(d.qty, 0)
                * COALESCE(d.harga, 0)
                * COALESCE(
                    NULLIF(d.kurs, 0),
                    NULLIF(h.kurs, 0),
                    1
                )
            )


        /* TAX EXCLUSIVE */
        ELSE

            COALESCE(
                d.nilaikonversi,

                COALESCE(d.qty, 0)
                * COALESCE(d.harga, 0)
                * COALESCE(
                    NULLIF(d.kurs, 0),
                    NULLIF(h.kurs, 0),
                    1
                )
            )

            +

            COALESCE(d.nilaipajak, 0)

    END,


    /* =====================================================
       COA PAJAK MASUKAN
    ===================================================== */

    NULLIF(TRIM(tx.prk_masukan), ''),


    /* =====================================================
       COA PAJAK KELUARAN
    ===================================================== */

    NULLIF(TRIM(tx.prk_keluaran), '')


FROM sc_tmp.lpb h

JOIN sc_tmp.lpb_dtl d

    ON TRIM(d.docno) = TRIM(h.docno)


LEFT JOIN sc_mst.mbarang mb

    ON TRIM(mb.idbarang) = TRIM(d.idbarang)


/* =====================================================
   TAX DETAIL

   LPB_DTL.idtax
        ↓
   sc_mst.tax_dtl.idtax
===================================================== */

LEFT JOIN sc_mst.tax_dtl tx

    ON TRIM(tx.idtax) = TRIM(d.idtax)


WHERE TRIM(h.docno) = TRIM(OLD.docno)

  AND TRIM(h.inputby) = TRIM(v_inputby)


ON CONFLICT
(
    docno,
    idbarang,
    idlocation,
    batch,
    uniqueid
)

DO UPDATE SET

    trxdate            = EXCLUDED.trxdate,

    docref             = EXCLUDED.docref,

    qty_in             = EXCLUDED.qty_in,

    pricelst_in        = EXCLUDED.pricelst_in,

    currcode           = EXCLUDED.currcode,

    currvalue          = EXCLUDED.currvalue,

    tax                = EXCLUDED.tax,

    disc               = EXCLUDED.disc,

    biaya              = EXCLUDED.biaya,

    ctype              = EXCLUDED.ctype,

    idgroup            = EXCLUDED.idgroup,

    grouptype          = EXCLUDED.grouptype,

    unit               = EXCLUDED.unit,

    description        = EXCLUDED.description,


    /* TAX */

    idtax              = EXCLUDED.idtax,

    tax_percent        = EXCLUDED.tax_percent,

    isinclusive        = EXCLUDED.isinclusive,

    nilai_dpp          = EXCLUDED.nilai_dpp,

    nilai_ppn          = EXCLUDED.nilai_ppn,

    nilai_bruto        = EXCLUDED.nilai_bruto,

    coa_tax_masukan    = EXCLUDED.coa_tax_masukan,

    coa_tax_keluaran   = EXCLUDED.coa_tax_keluaran,


    /* REPOST GL */

    is_posted          = FALSE,

    posted_at          = NULL;
        /* END NILAI PERSEDIAAN DAN NILAI COA */	
        PERFORM sc_trx.sp_post_gl(v_inputby);	


        -- ===============================
        -- LOG: INSERT HEADER LPB
        -- ===============================
        PERFORM sc_log.fn_log_transaction(
            v_docno::CHAR(30),
            NULL,
            'I.P',                  -- kode module dari menuprg
            'I.P.A.6',              -- kode menu untuk LPB
            'I',                    -- action: INPUT (1 huruf)
            v_inputby,
            v_client_ip,
            v_inputby
        );



        -- -- ===============================
        -- -- CLEANUP TMP
        -- -- ===============================
        DELETE FROM sc_tmp.lpb
        WHERE rtrim(docno) = rtrim(OLD.docno)
            AND inputby = v_inputby
            
            AND idurut = v_idurut;

        DELETE FROM sc_tmp.lpb_dtl
        WHERE rtrim(docno) = rtrim(OLD.docno)
            AND inputby = v_inputby;

    -- ===============================
    -- DOCNOTMP FLOW (TETAP)
    -- ===============================
    ELSIF OLD.status = 'E' AND NEW.status = 'F' AND COALESCE(NEW.docnotmp, '') <> '' THEN

        UPDATE sc_trx.po_dtl ppd
            SET qtylpb = COALESCE(ppd.qtylpb, 0) - pod_lama.qty_lpb_lama
            FROM (
                SELECT uniqueid, SUM(qty) as qty_lpb_lama
                FROM sc_trx.lpb_dtl
                WHERE rtrim(docno) = rtrim(NEW.docno)
                    AND inputby = NEW.inputby
                    AND uniqueid IS NOT NULL AND uniqueid <> ''
                GROUP BY uniqueid
            ) pod_lama
            WHERE ppd.uniqueid = pod_lama.uniqueid;

        DELETE FROM sc_trx.lpb WHERE docno = NEW.docnotmp;
        DELETE FROM sc_trx.lpb_dtl WHERE docno = NEW.docnotmp;

        INSERT INTO sc_trx.lpb_dtl
        (idurut, docno, docnopo, idbarang, capexno, uniqueid,  nmbarang,
        idprincipal, idgudang, idspec, volitem, biaya, biaya2, unit, qty, 
        harga, nilai, descriptionpo, descriptionpp, multidisc,
        inputby, inputdate, status, updateby, updatedate, docnotmp,idtax,currcode,kurs,nilaikonversi,nilaipajak,qtyretur,idhistory_price,multidisctype,totaldiscount)
        SELECT
            idurut, NEW.docnotmp, docnopo, idbarang, capexno, uniqueid,  nmbarang,
            idprincipal, idgudang, idspec, volitem, biaya, biaya2, unit, qty, 
            harga, nilai, descriptionpo, descriptionpp, multidisc,
            inputby, inputdate, status, updateby, updatedate, docnotmp,idtax,currcode,kurs,nilaikonversi,nilaipajak,qtyretur,idhistory_price,multidisctype,totaldiscount
        FROM sc_tmp.lpb_dtl
        WHERE rtrim(docno) = rtrim(NEW.docno);

        INSERT INTO sc_trx.lpb
        (idurut, docno, cabang, docdate, pemohon, kdsupplier,
        nmsupplier, alamatsupplier, jthtempo,
        biayavol, biayavol2, nosj, nofaktur,
        idtax,isinclusive, currcode, kurs, dpp, 
        jumlahpajak, total,
        keterangan, status, inputby, inputdate,
        updateby, updatedate, printby, printdate, printcount, docnotmp)
        SELECT
            idurut, NEW.docnotmp, cabang, docdate, pemohon, kdsupplier,
            nmsupplier, alamatsupplier, jthtempo,
            biayavol, biayavol2, nosj, nofaktur,
            idtax,isinclusive, currcode, kurs, dpp, 
            jumlahpajak, total,
            keterangan, status, inputby, inputdate,
            updateby, updatedate, printby, printdate, printcount, docnotmp
        FROM sc_tmp.lpb
        WHERE rtrim(docno) = rtrim(NEW.docno);

        UPDATE sc_trx.po_dtl ppd
        SET qtylpb = COALESCE(ppd.qtylpb, 0) + pod.qty_used
            -- updateby = v_inputby,
            -- updatedate = CURRENT_TIMESTAMP
        FROM (
            SELECT 
                uniqueid,
                SUM(qty) as qty_used
            FROM sc_tmp.lpb_dtl
            WHERE rtrim(docno) = rtrim(NEW.docno)
                AND inputby = v_inputby
                AND uniqueid IS NOT NULL
                AND uniqueid <> ''
            GROUP BY uniqueid
        ) pod
        WHERE ppd.uniqueid = pod.uniqueid;

        
        UPDATE sc_trx.po_dtl ppd
            SET status = CASE 
                WHEN ppd.qty = COALESCE(ppd.qtylpb, 0) THEN 'LPB'
                ELSE 'F'
            END
            FROM sc_tmp.lpb_dtl t
            WHERE rtrim(t.docno) = rtrim(NEW.docno)
            AND t.inputby = v_inputby
            AND ppd.uniqueid = t.uniqueid;

        UPDATE sc_trx.po po
            SET status = 'LPB'
            WHERE po.docno IN (
                SELECT DISTINCT t.docnopo
                FROM sc_tmp.lpb_dtl t
                WHERE rtrim(t.docno) = rtrim(NEW.docno)
                AND t.inputby = v_inputby
                AND t.docnopo IS NOT NULL
                AND t.docnopo <> ''
            );

        /* NILAI PERSEDIAAN DAN NILAI COA */
        -- =========================================
        -- UPSERT STKBLC (SOURCE: sc_tmp)
        -- =========================================
        -- =========================================
        -- UPSERT STKBLC (DOCNOTMP - sc_tmp)
        -- =========================================
        PERFORM sc_trx.sp_unpost_by_doc(NEW.docnotmp,'GR');
        DELETE FROM sc_trx.stkblc WHERE docno = trim(NEW.docnotmp) and doctype='GR' ;
        INSERT INTO sc_trx.stkblc ( 
					idlocation, 
					idarea, 
					batch, 
					idbarang, 
					trxdate, 
					doctype, 
					docno, 
					docref, 

					qty_in, 
					qty_out, 
					qty_sld, 

					pricelst_in, 
					pricelst_out, 
					pricelst_sld, 

					currcode, 
					currvalue, 

					tax, 
					disc, 
					biaya, 

					idgroup, 
					grouptype, 

					hist, 
					ctype, 

					/* ==========================================
					   TAX SNAPSHOT
					========================================== */
					idtax,
					tax_percent,
					isinclusive,

					nilai_dpp,
					nilai_ppn,
					nilai_bruto,

					coa_tax_masukan,
					coa_tax_keluaran,

					/* ==========================================
					   POSTING
					========================================== */
					is_posted, 
					posted_at,

					picby,
					description,
					created_at,
					created_by,
					status
				) 

				SELECT 

					/* ==========================================
					   LOCATION
					========================================== */

					d.idgudang, 

					TRIM(d.idgudang) || '.0000', 

					COALESCE(TRIM(d.idspec), ''), 

					d.idbarang, 


					/* ==========================================
					   TRANSACTION
					========================================== */

					CAST(h.docdate AS DATE) + NOW()::TIME, 

					'GR', 

					d.docnotmp, 

					d.docnopo, 


					/* ==========================================
					   QTY
					========================================== */

					CASE  
						WHEN TRIM(COALESCE(mb.grouptype, 'STOCK')) = 'NON STOCK'
							THEN 0 
						ELSE
							COALESCE(d.qty, 0)
							+ COALESCE(d.qtybonus, 0)
					END,

					0,

					0,


					/* ==========================================
					   PRICE
					========================================== */

					COALESCE(d.harga, 0),

					0,

					0,


					/* ==========================================
					   CURRENCY
					========================================== */

					h.currcode, 

					COALESCE(h.kurs, 1),


					/* ==========================================
					   TAX / DISCOUNT / BIAYA
					========================================== */

					COALESCE(d.tax, 0),

					COALESCE(d.disc, 0),

					COALESCE(d.biaya, 0),


					/* ==========================================
					   GROUP
					========================================== */

					mb.idgroup, 

					COALESCE(mb.grouptype, 'STOCK'), 


					/* ==========================================
					   HISTORY
					========================================== */

					'LPB', 


					/* ==========================================
					   CTYPE
					========================================== */

					CASE  
						WHEN TRIM(COALESCE(mb.grouptype, 'STOCK')) = 'NON STOCK'
							THEN 'NON' 
						ELSE 'IN' 
					END, 


					/* ==========================================
					   TAX SNAPSHOT
					========================================== */

					NULLIF(TRIM(d.idtax), ''),

					COALESCE(d.tax_percent, 0),

					CASE
						WHEN UPPER(TRIM(COALESCE(d.isinclusive, 'NO'))) = 'YES'
							THEN 'YES'
						ELSE 'NO'
					END,


					/* ==========================================
					   NILAI DPP

					   PRIORITAS:
					   nilai_dpp dari LPB detail
					========================================== */

					COALESCE(
						d.nilai_dpp,

						CASE
							WHEN UPPER(TRIM(COALESCE(d.isinclusive, 'NO'))) = 'YES'
							 AND COALESCE(d.tax_percent, 0) > 0
							THEN
								(
									COALESCE(d.qty, 0)
									* COALESCE(d.harga, 0)
								)
								/
								(
									1 + COALESCE(d.tax_percent, 0) / 100
								)

							ELSE
								COALESCE(d.qty, 0)
								* COALESCE(d.harga, 0)
						END
					)
					* COALESCE(h.kurs, 1),


					/* ==========================================
					   NILAI PPN
					========================================== */

					COALESCE(
						d.nilai_ppn,

						CASE

							WHEN NULLIF(TRIM(COALESCE(d.idtax, '')), '') IS NULL
								THEN 0

							WHEN UPPER(TRIM(COALESCE(d.isinclusive, 'NO'))) = 'YES'
							 AND COALESCE(d.tax_percent, 0) > 0
							THEN

								(
									COALESCE(d.qty, 0)
									* COALESCE(d.harga, 0)
								)

								-

								(
									(
										COALESCE(d.qty, 0)
										* COALESCE(d.harga, 0)
									)

									/

									(
										1
										+ COALESCE(d.tax_percent, 0) / 100
									)
								)

							ELSE

								(
									COALESCE(d.qty, 0)
									* COALESCE(d.harga, 0)
								)

								*
								COALESCE(d.tax_percent, 0)
								/ 100

						END
					)
					* COALESCE(h.kurs, 1),


					/* ==========================================
					   NILAI BRUTO
					========================================== */

					COALESCE(
						d.nilai_bruto,

						CASE

							/* TAX INCLUSIVE */
							WHEN UPPER(TRIM(COALESCE(d.isinclusive, 'NO'))) = 'YES'
							THEN
								COALESCE(d.qty, 0)
								* COALESCE(d.harga, 0)


							/* TAX EXCLUSIVE */
							ELSE

								(
									COALESCE(d.qty, 0)
									* COALESCE(d.harga, 0)
								)

								+

								(
									(
										COALESCE(d.qty, 0)
										* COALESCE(d.harga, 0)
									)

									*
									COALESCE(d.tax_percent, 0)
									/ 100
								)

						END
					)
					* COALESCE(h.kurs, 1),


					/* ==========================================
					   COA TAX MASUKAN

					   Dari LPB snapshot.
					   Jika belum disimpan, sebaiknya ambil dari
					   sc_mst.tax_dtl.
					========================================== */

					NULLIF(TRIM(d.coa_tax_masukan), ''),


					/* ==========================================
					   COA TAX KELUARAN
					========================================== */

					NULLIF(TRIM(d.coa_tax_keluaran), ''),


					/* ==========================================
					   POSTING
					========================================== */

					FALSE, 

					NULL,


					/* ==========================================
					   AUDIT
					========================================== */

					h.inputby,

					COALESCE(d.description, 'LPB'),

					NOW(),

					h.inputby,

					'P'


				FROM sc_tmp.lpb h

				JOIN sc_tmp.lpb_dtl d 
					ON RTRIM(d.docno) = RTRIM(h.docno) 

				LEFT JOIN sc_mst.mbarang mb 
					ON TRIM(mb.idbarang) = TRIM(d.idbarang) 


				WHERE TRIM(h.docno) = TRIM(NEW.docno) 

				AND TRIM(h.inputby) = TRIM(v_inputby) 


				/* =====================================================
				   UPSERT STKBLC
				===================================================== */

				ON CONFLICT (docno, idbarang, idlocation, batch) 

				DO UPDATE SET 

					/* QTY */
					qty_in = EXCLUDED.qty_in, 
					qty_out = EXCLUDED.qty_out, 


					/* PRICE */
					pricelst_in = EXCLUDED.pricelst_in, 


					/* CURRENCY */
					currcode = EXCLUDED.currcode, 
					currvalue = EXCLUDED.currvalue, 


					/* TAX */
					tax = EXCLUDED.tax,
					disc = EXCLUDED.disc,
					biaya = EXCLUDED.biaya,

					idtax = EXCLUDED.idtax,
					tax_percent = EXCLUDED.tax_percent,
					isinclusive = EXCLUDED.isinclusive,

					nilai_dpp = EXCLUDED.nilai_dpp,
					nilai_ppn = EXCLUDED.nilai_ppn,
					nilai_bruto = EXCLUDED.nilai_bruto,

					coa_tax_masukan = EXCLUDED.coa_tax_masukan,
					coa_tax_keluaran = EXCLUDED.coa_tax_keluaran,


					/* GROUP */
					idgroup = EXCLUDED.idgroup, 
					grouptype = EXCLUDED.grouptype, 


					/* RESET GL POSTING */
					is_posted = FALSE, 
					posted_at = NULL,


					/* AUDIT */
					picby = EXCLUDED.picby,
					description = EXCLUDED.description,
					created_at = NOW(),
					created_by = EXCLUDED.created_by;
            
            
        /* END NILAI PERSEDIAAN DAN NILAI COA */
        PERFORM sc_trx.sp_post_gl(v_inputby);

         -- ===============================
        -- LOG: INSERT HEADER LPB
        -- ===============================
        PERFORM sc_log.fn_log_transaction(
            NEW.docno,
            NULL,
            'I.P',                  -- kode module dari menuprg
            'I.P.A.6',              -- kode menu untuk LPB
            'U',                    -- action: INPUT (1 huruf)
            v_inputby,
            v_client_ip,
            v_inputby
        );


        DELETE FROM sc_tmp.lpb WHERE rtrim(docno) = rtrim(NEW.docno);
        DELETE FROM sc_tmp.lpb_dtl WHERE rtrim(docno) = rtrim(NEW.docno);

    ELSEIF (OLD.STATUS = 'E' AND NEW.STATUS = 'C') THEN
        IF NEW.printby IS NOT NULL AND NEW.printby <> '' AND NEW.printdate IS NOT NULL THEN
            UPDATE sc_trx.lpb SET status = 'P' WHERE docno = NEW.docnotmp;
        ELSE
            UPDATE sc_trx.lpb SET status = 'F' WHERE docno = NEW.docnotmp;
        END IF;

            
        DELETE FROM sc_tmp.lpb WHERE docno = NEW.docno;
        DELETE FROM sc_tmp.lpb_dtl WHERE docno = NEW.docno;
    
    END IF;

    RETURN NEW;
END;
$BODY$;



CREATE TRIGGER tr_lpb_finalize
    AFTER UPDATE ON sc_tmp.lpb
    FOR EACH ROW
    EXECUTE FUNCTION sc_tmp.tr_lpb_finalize();







-- DROP FUNCTION IF EXISTS sc_trx.tr_lpb();

CREATE OR REPLACE FUNCTION sc_trx.tr_lpb()
    RETURNS trigger
    LANGUAGE 'plpgsql'
    COST 100
    VOLATILE NOT LEAKPROOF
AS $BODY$

DECLARE 
	vr_nomor char(15); 
	vr_cekprefix char(15);
	vr_nowprefix char(15);  
	vr_id_dtl numeric;
	vr_lastdoc NUMERIC(18);

    v_docno     TEXT;
    v_client_ip TEXT;
    v_inputby   TEXT;


    v_total_po  NUMERIC(18,2);
    v_total_void NUMERIC(18,2);
    v_rec       RECORD;
BEGIN		

        -- ===============================
        -- AMBIL IP DARI sc_log.useronline
        -- ===============================
        v_docno := rtrim(NEW.docno);
        v_inputby := NEW.inputby;

        v_client_ip := sc_log.fn_get_user_ip(v_inputby);
        -- ===============================
        -- ADVISORY LOCK (CEGAH RACE CONDITION)
        -- ===============================
        PERFORM pg_advisory_xact_lock(hashtext(NEW.docno));
        -- ===============================
        -- PO DIBATALKAN (F -> C) - REVERT QTYPO
        -- ===============================

        IF (OLD.STATUS = 'F' AND NEW.STATUS = 'C') THEN
            -- REVERT QTYLPB
            UPDATE sc_trx.po_dtl ppd
            SET qtylpb = COALESCE(ppd.qtylpb, 0) - pod.qty_used
            FROM (
                SELECT uniqueid, SUM(qty) as qty_used
                FROM sc_trx.lpb_dtl
                WHERE rtrim(docno) = rtrim(NEW.docno)
                    AND uniqueid IS NOT NULL AND uniqueid <> ''
                GROUP BY uniqueid
            ) pod
            WHERE ppd.uniqueid = pod.uniqueid;

            -- UPDATE STATUS PO_DTL
            UPDATE sc_trx.po_dtl ppd
            SET status = CASE 
                WHEN ppd.qty = COALESCE(ppd.qtylpb, 0) THEN 'LPB'
                ELSE 'F'
            END
            FROM sc_trx.lpb_dtl vd
            WHERE ppd.uniqueid = vd.uniqueid
                AND rtrim(vd.docno) = rtrim(NEW.docno);

            -- UPDATE STATUS PO HEADER
            UPDATE sc_trx.po po
            SET status = CASE 
                WHEN EXISTS (
                    SELECT 1 
                    FROM sc_trx.po_dtl ppd
                    WHERE rtrim(ppd.docno) = rtrim(po.docno)
                    AND COALESCE(ppd.qtylpb, 0) > 0
                ) THEN 'LPB'
                ELSE 'P'
            END
            WHERE EXISTS (
                SELECT 1 
                FROM sc_trx.lpb_dtl vd
                WHERE rtrim(vd.docno) = rtrim(NEW.docno)
                AND vd.uniqueid IN (
                    SELECT uniqueid 
                    FROM sc_trx.po_dtl 
                    WHERE rtrim(docno) = rtrim(po.docno)
                )
            );

            -- LOG: CANCEL LPB
            PERFORM sc_log.fn_log_transaction(
                NEW.docno,
                NULL,
                'I.P',
                'I.P.A.6',
                'C',
                COALESCE(NEW.updateby, NEW.inputby),
                v_client_ip,
                COALESCE(NEW.updateby, NEW.inputby)
            );
        END IF;


		IF (OLD.STATUS='F' AND NEW.STATUS='E') THEN
			-- Insert into pp_dtl with new columns
			INSERT INTO sc_tmp.lpb_dtl
			( idurut, docno, docnopo, idbarang, capexno, uniqueid, nmbarang,
            idprincipal, idgudang, idspec, volitem, biaya, biaya2, unit, qty, 
            harga, nilai, descriptionpo, descriptionpp, multidisc,
            inputby, inputdate, status, updateby, updatedate, docnotmp,idtax,currcode,kurs,nilaikonversi,nilaipajak,qtyretur,idhistory_price,multidisctype,totaldiscount)
			SELECT idurut, NEW.docno, docnopo, idbarang, capexno, uniqueid, nmbarang,
            idprincipal, idgudang, idspec, volitem, biaya, biaya2, unit, qty, 
            harga, nilai, descriptionpo, descriptionpp, multidisc,
            inputby, inputdate, status, updateby, updatedate, NEW.docno,idtax,currcode,kurs,nilaikonversi,nilaipajak,qtyretur,idhistory_price,multidisctype,totaldiscount
			FROM sc_trx.lpb_dtl 
			WHERE docno = NEW.docno;

			-- Insert into pp with new columns
			INSERT INTO sc_tmp.lpb
            (
                idurut, docno, cabang, docdate, pemohon, kdsupplier,
                nmsupplier, alamatsupplier, jthtempo,
                biayavol, biayavol2, nosj, nofaktur,
                idtax,isinclusive, currcode, kurs, dpp, 
                jumlahpajak, total,
                keterangan, status, inputby, inputdate, updateby, updatedate,
                printby, printdate, printcount, docnotmp
            )
			SELECT  idurut, NEW.docno, cabang, docdate, pemohon, kdsupplier,
            nmsupplier, alamatsupplier, jthtempo,
            biayavol, biayavol2, nosj, nofaktur,
            idtax,isinclusive, currcode, kurs, dpp, 
            jumlahpajak, total,
            keterangan, status , inputby, inputdate, updateby, updatedate,
            printby, printdate, printcount, NEW.docno
			FROM sc_trx.lpb 
			WHERE docno = NEW.docno;

		END IF;	
			
		RETURN NEW;

END;
$BODY$;

ALTER FUNCTION sc_trx.tr_lpb()
    OWNER TO postgres;


    

-- FUNCTION: sc_trx.tr_lpb()
-- Trigger: tr_lpb

-- DROP TRIGGER IF EXISTS tr_lpb ON sc_trx.lpb;

CREATE OR REPLACE TRIGGER tr_lpb
    AFTER UPDATE 
    ON sc_trx.lpb
    FOR EACH ROW
    EXECUTE FUNCTION sc_trx.tr_lpb();





-- =========== TAMBAHAN 24/8/26 ====================
ALTER TABLE sc_tmp.lpb_dtl
ADD COLUMN capexno character(30)

ALTER TABLE sc_trx.lpb_dtl
ADD COLUMN capexno character(30)



-- docdate
ALTER TABLE sc_trx.lpb
ALTER COLUMN docdate TYPE DATE
USING TRIM(docdate)::DATE;
ALTER TABLE sc_tmp.lpb
ALTER COLUMN docdate TYPE DATE
USING TRIM(docdate)::DATE;



-- printcount
ALTER TABLE sc_tmp.lpb
ADD COLUMN printcount integer
ALTER TABLE sc_trx.lpb
ADD COLUMN printcount integer

-- ==================== END OFTAMBAHAN 24/8/26  ====================


--TAMBAHN MULTIDISCOUNT----
alter table sc_tmp.lpb_dtl
add column idhistory_price char(30),
add column multidisctype char(30),
add column totaldiscount numeric(18,2);

alter table sc_trx.lpb_dtl
add column idhistory_price char(30),
add column multidisctype char(30),
add column totaldiscount numeric(18,2);

--,idhistory_price,multidisctype,totaldiscount







/* DELETE LPB ALL */

-- ============================================================
-- FUNCTION : sc_trx.sp_delete_lpb
-- PURPOSE  : Delete LPB dan reverse PO, GL, serta inventory
-- ============================================================

/* DELETE LPB ALL */

-- ============================================================
-- FUNCTION : sc_trx.sp_delete_lpb
-- PURPOSE  : Delete LPB dan reverse PO, GL, serta inventory
-- ============================================================

CREATE OR REPLACE FUNCTION sc_trx.sp_delete_lpb(
    p_docno TEXT,
    p_user  TEXT
)
RETURNS JSONB
LANGUAGE plpgsql
AS $$
DECLARE
    v_docno        TEXT;
    v_client_ip    TEXT;
    v_total_detail INTEGER;
    v_deleted_stk  INTEGER;
BEGIN

    -- =========================================================
    -- VALIDASI DOCNO
    -- =========================================================

    v_docno := TRIM(p_docno);

    IF COALESCE(v_docno, '') = '' THEN
        RAISE EXCEPTION 'DOCNO LPB tidak boleh kosong';
    END IF;


    -- =========================================================
    -- LOCK DOCUMENT
    -- Mencegah delete bersamaan
    -- =========================================================

    PERFORM pg_advisory_xact_lock(hashtext(v_docno));


    -- =========================================================
    -- VALIDASI LPB
    -- =========================================================

    IF NOT EXISTS (
        SELECT 1
        FROM sc_trx.lpb
        WHERE TRIM(docno) = v_docno
    ) THEN

        RETURN jsonb_build_object(
            'success', false,
            'message', 'Dokumen LPB tidak ditemukan',
            'docno', v_docno
        );

    END IF;


    -- =========================================================
    -- HITUNG DETAIL
    -- =========================================================

    SELECT COUNT(*)
    INTO v_total_detail
    FROM sc_trx.lpb_dtl
    WHERE TRIM(docno) = v_docno;


    -- =========================================================
    -- GET CLIENT IP
    -- =========================================================

    v_client_ip := sc_log.fn_get_user_ip(p_user);


    -- =========================================================
    -- 1. REVERSE QTY LPB KE PO DETAIL
    -- =========================================================

    UPDATE sc_trx.po_dtl ppd

    SET qtylpb = GREATEST(
        0,
        COALESCE(ppd.qtylpb, 0)
        -
        COALESCE(x.qty_used, 0)
    )

    FROM (

        SELECT
            TRIM(uniqueid) AS uniqueid,
            SUM(COALESCE(qty, 0)) AS qty_used

        FROM sc_trx.lpb_dtl

        WHERE TRIM(docno) = v_docno

          AND uniqueid IS NOT NULL
          AND TRIM(uniqueid) <> ''

        GROUP BY TRIM(uniqueid)

    ) x

    WHERE TRIM(ppd.uniqueid) = x.uniqueid;


    -- =========================================================
    -- 2. UPDATE STATUS PO DETAIL
    -- =========================================================

    UPDATE sc_trx.po_dtl ppd

    SET status = CASE

        WHEN COALESCE(ppd.qtylpb, 0) <= 0
            THEN 'F'

        WHEN COALESCE(ppd.qtylpb, 0)
             >= COALESCE(ppd.qty, 0)
            THEN 'LPB'

        ELSE 'F'

    END

    WHERE TRIM(ppd.uniqueid) IN (

        SELECT DISTINCT TRIM(uniqueid)

        FROM sc_trx.lpb_dtl

        WHERE TRIM(docno) = v_docno

          AND uniqueid IS NOT NULL
          AND TRIM(uniqueid) <> ''

    );


    -- =========================================================
    -- 3. UPDATE STATUS PO HEADER
    -- =========================================================

    UPDATE sc_trx.po po

    SET status = CASE

        WHEN EXISTS (

            SELECT 1

            FROM sc_trx.po_dtl pd

            WHERE TRIM(pd.docno) = TRIM(po.docno)

              AND COALESCE(pd.qtylpb, 0) > 0

        )

        THEN 'LPB'

        ELSE 'P'

    END

    WHERE TRIM(po.docno) IN (

        SELECT DISTINCT TRIM(docnopo)

        FROM sc_trx.lpb_dtl

        WHERE TRIM(docno) = v_docno

          AND docnopo IS NOT NULL
          AND TRIM(docnopo) <> ''

    );


    -- =========================================================
    -- 4. DELETE JOURNAL DETAIL
    -- Harus dihapus sebelum jurnal_hd
    -- =========================================================

    DELETE FROM sc_trx.jurnal_dt jd

    USING sc_trx.jurnal_hd jh

    WHERE jd.jurnal_id = jh.id

      AND TRIM(jh.docno) = v_docno

      AND TRIM(jh.doctype) = 'GR';


    -- =========================================================
    -- 5. DELETE JOURNAL HEADER
    -- =========================================================

    DELETE FROM sc_trx.jurnal_hd

    WHERE TRIM(docno) = v_docno

      AND TRIM(doctype) = 'GR';


    -- =========================================================
    -- 6. DELETE INVENTORY TRANSACTION
    --
    -- Trigger stkblc seharusnya otomatis:
    --   - Update stkgdw
    --   - Recalculate avg cost
    -- =========================================================

    DELETE FROM sc_trx.stkblc

    WHERE TRIM(docno) = v_docno

      AND TRIM(doctype) = 'GR';

    GET DIAGNOSTICS v_deleted_stk = ROW_COUNT;


    -- =========================================================
    -- 7. DELETE TEMP LPB DETAIL
    -- =========================================================

    DELETE FROM sc_tmp.lpb_dtl

    WHERE TRIM(docno) = v_docno;


    -- =========================================================
    -- 8. DELETE TEMP LPB HEADER
    -- =========================================================

    DELETE FROM sc_tmp.lpb

    WHERE TRIM(docno) = v_docno;


    -- =========================================================
    -- 9. DELETE LPB DETAIL
    -- =========================================================

    DELETE FROM sc_trx.lpb_dtl

    WHERE TRIM(docno) = v_docno;


    -- =========================================================
    -- 10. DELETE LPB HEADER
    -- =========================================================

    DELETE FROM sc_trx.lpb

    WHERE TRIM(docno) = v_docno;


    -- =========================================================
    -- 11. AUDIT LOG
    -- =========================================================

    PERFORM sc_log.fn_log_transaction(
        v_docno::CHAR(30),
        NULL,
        'I.P',
        'I.P.A.6',
        'D',
        p_user,
        v_client_ip,
        p_user
    );


    -- =========================================================
    -- SUCCESS
    -- =========================================================

    RETURN jsonb_build_object(

        'success', true,

        'message',
        'LPB berhasil dihapus dan seluruh transaksi terkait telah direverse',

        'docno', v_docno,

        'total_detail', v_total_detail,

        'total_stock_deleted', v_deleted_stk

    );


EXCEPTION

    WHEN OTHERS THEN

        RAISE EXCEPTION
            'Gagal menghapus LPB % : %',
            COALESCE(v_docno, p_docno),
            SQLERRM;

END;

$$;


-- ============================================================
-- CONTOH
-- ============================================================

-- SELECT sc_trx.sp_delete_lpb(
--     'LPB/2609/PA0001',
--     'USERNAME'
-- );
-- Contoh:
-- SELECT sc_trx.sp_delete_lpb('LPB/2609/PA0001', 'USERNAME');




-- FUNCTION: sc_trx.sp_unpost_by_doc(character varying, character varying)

-- DROP FUNCTION IF EXISTS sc_trx.sp_unpost_by_doc(character varying, character varying);

CREATE OR REPLACE FUNCTION sc_trx.sp_unpost_by_doc(
	p_docno character varying,
	p_doctype character varying)
    RETURNS void
    LANGUAGE 'plpgsql'
    COST 100
    VOLATILE PARALLEL UNSAFE
AS $BODY$
BEGIN

    -- DELETE DETAIL
    DELETE FROM sc_trx.jurnal_dt
    WHERE jurnal_id IN (
        SELECT id FROM sc_trx.jurnal_hd
        WHERE TRIM(docno)=TRIM(p_docno)
          AND TRIM(doctype)=TRIM(p_doctype)
    );

    -- DELETE HEADER
    DELETE FROM sc_trx.jurnal_hd
    WHERE TRIM(docno)=TRIM(p_docno)
      AND TRIM(doctype)=TRIM(p_doctype);

    -- RESET STKBLC
    UPDATE sc_trx.stkblc
    SET is_posted = FALSE,
        posted_at = NULL
    WHERE TRIM(docno)=TRIM(p_docno)
      AND TRIM(doctype)=TRIM(p_doctype);

END;
$BODY$;

ALTER FUNCTION sc_trx.sp_unpost_by_doc(character varying, character varying)
    OWNER TO postgres;

