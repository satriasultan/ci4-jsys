
-- JALANKAN INI DULU

DROP TABLE IF EXISTS sc_tmp.ndk;
DROP TABLE IF EXISTS sc_trx.ndk;




CREATE TABLE IF NOT EXISTS sc_tmp.ndk
(
    idurut serial NOT NULL,
    docno character(30) COLLATE pg_catalog."default" NOT NULL,
    docdate date,
    -- senddate character(20) COLLATE pg_catalog."default",
    cabang character (30 ) COLLATE pg_catalog."default",    
    pemohon character(100) COLLATE pg_catalog."default",
    kdsupplier character(30) COLLATE pg_catalog."default",
    nmsupplier character(250) COLLATE pg_catalog."default",
    alamatsupplier TEXT,
    -- alamatkirim TEXT,
    jthtempo numeric(18,2),
    dk character(20),
    kdsalesman character(10),
    idtax character(20),
    isinclusive character(6),
    currcode character(3),
    kurs numeric(18,2),
    perkiraanarap character(20),
    perkiraanlawan character(20),
    
    nilai numeric(18,2),
    dpp numeric(18,2),
    jumlahpajak numeric(18,2),
    total numeric(18,2),
    -- syarat TEXT,
    status character(6) COLLATE pg_catalog."default",
    keterangan TEXT,
    inputby character varying(50) COLLATE pg_catalog."default",
    inputdate timestamp without time zone,
    updateby character varying(50) COLLATE pg_catalog."default",
    updatedate timestamp without time zone,
    printby character varying(50) COLLATE pg_catalog."default",
    printdate timestamp without time zone,
    docnotmp character(30) COLLATE pg_catalog."default",
    CONSTRAINT pk_tmp_ndk PRIMARY KEY (docno)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_tmp.ndk
    OWNER to postgres;





CREATE TABLE IF NOT EXISTS sc_trx.ndk
(
    idurut serial NOT NULL,
    docno character(30) COLLATE pg_catalog."default" NOT NULL,
    docdate date,
    -- senddate character(20) COLLATE pg_catalog."default",
    cabang character (30 ) COLLATE pg_catalog."default",    
    pemohon character(100) COLLATE pg_catalog."default",
    kdsupplier character(30) COLLATE pg_catalog."default",
    nmsupplier character(250) COLLATE pg_catalog."default",
    alamatsupplier TEXT,
    -- alamatkirim TEXT,
    jthtempo numeric(18,2),
    dk character(20),
    kdsalesman character(10),
    idtax character(20),
    isinclusive character(6),
    currcode character(3),
    kurs numeric(18,2),
    perkiraanarap character(20),
    perkiraanlawan character(20),
    
    nilai numeric(18,2),
    dpp numeric(18,2),
    jumlahpajak numeric(18,2),
    total numeric(18,2),
    -- syarat TEXT,
    status character(6) COLLATE pg_catalog."default",
    keterangan TEXT,
    inputby character varying(50) COLLATE pg_catalog."default",
    inputdate timestamp without time zone,
    updateby character varying(50) COLLATE pg_catalog."default",
    updatedate timestamp without time zone,
    printby character varying(50) COLLATE pg_catalog."default",
    printdate timestamp without time zone,
    docnotmp character(30) COLLATE pg_catalog."default",
    CONSTRAINT pk_trx_ndk PRIMARY KEY (docno)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_trx.ndk
    OWNER to postgres;






CREATE OR REPLACE FUNCTION sc_tmp.tr_ndk_finalize()
RETURNS trigger
LANGUAGE plpgsql
AS $BODY$
DECLARE
    v_docno       TEXT;
    v_inputby     TEXT;
    v_idurut      INTEGER;
    v_base_docno  TEXT;
    v_new_docno   TEXT;
    v_num         TEXT;
    v_num_int     INTEGER;
    v_lock_key    BIGINT;
BEGIN

    /* ============================================================
       NORMAL FINALIZE
       E -> F
       ============================================================ */

    IF OLD.status = 'E'
       AND NEW.status = 'F'
       AND COALESCE(NEW.docnotmp, '') = '' THEN

        v_docno   := RTRIM(NEW.docno);
        v_inputby := NEW.inputby;
        v_idurut  := NEW.idurut;

        /* ========================================================
           CEK APAKAH SUDAH PERNAH DIFINALKAN
           ======================================================== */

        IF EXISTS (
            SELECT 1
            FROM sc_trx.ndk
            WHERE idurut = v_idurut
              AND TRIM(COALESCE(inputby, '')) =
                  TRIM(COALESCE(v_inputby, ''))
        ) THEN

            DELETE FROM sc_tmp.ndk
            WHERE TRIM(docno) = TRIM(OLD.docno)
              AND idurut = v_idurut;

            RETURN NEW;
        END IF;


        /* ========================================================
           GENERATE DOCNO
           ======================================================== */

        v_base_docno := regexp_replace(v_docno, '[0-9]+$', '');

        v_lock_key := hashtext(v_base_docno);

        PERFORM pg_advisory_xact_lock(v_lock_key);

        v_new_docno := v_docno;

        LOOP

            EXIT WHEN NOT EXISTS (
                SELECT 1
                FROM sc_trx.ndk
                WHERE TRIM(docno) = TRIM(v_new_docno)
            );

            v_num := regexp_replace(
                v_new_docno,
                '.*?([0-9]+)$',
                '\1'
            );

            IF COALESCE(v_num, '') = '' THEN

                RAISE EXCEPTION
                    'Format DOCNO NDK tidak valid: %',
                    v_new_docno;

            END IF;

            v_num_int := v_num::INTEGER + 1;

            v_new_docno :=
                v_base_docno ||
                lpad(
                    v_num_int::TEXT,
                    length(v_num),
                    '0'
                );

        END LOOP;

        v_docno := v_new_docno;


        /* ========================================================
           INSERT HEADER BARU
           ======================================================== */

        INSERT INTO sc_trx.ndk
        (
            idurut,
            docno,
            cabang,
            docdate,
            pemohon,
            kdsupplier,
            nmsupplier,
            alamatsupplier,
            kdsalesman,
            jthtempo,
            isinclusive,
            dk,
            perkiraanarap,
            perkiraanlawan,
            nilai,
            idtax,
            currcode,
            kurs,
            dpp,
            jumlahpajak,
            total,
            keterangan,
            status,
            inputby,
            inputdate,
            updateby,
            updatedate,
            printby,
            printdate
        )
        SELECT
            idurut,
            v_docno,
            cabang,
            docdate,
            pemohon,
            kdsupplier,
            nmsupplier,
            alamatsupplier,
            kdsalesman,
            jthtempo,
            isinclusive,
            dk,
            perkiraanarap,
            perkiraanlawan,
            nilai,
            idtax,
            currcode,
            kurs,
            dpp,
            jumlahpajak,
            total,
            keterangan,
            'F',
            inputby,
            inputdate,
            updateby,
            updatedate,
            printby,
            printdate
        FROM sc_tmp.ndk
        WHERE TRIM(docno) = TRIM(OLD.docno)
          AND TRIM(COALESCE(inputby, '')) =
              TRIM(COALESCE(v_inputby, ''))
          AND idurut = v_idurut;


        /* ========================================================
           CLEANUP TMP
           ======================================================== */

        DELETE FROM sc_tmp.ndk
        WHERE TRIM(docno) = TRIM(OLD.docno)
          AND TRIM(COALESCE(inputby, '')) =
              TRIM(COALESCE(v_inputby, ''))
          AND idurut = v_idurut;


    /* ============================================================
       EDIT FINAL
       E -> F + DOCNOTMP

       DOCNOTMP = DOCNO EXISTING
       TIDAK BOLEH MEMBUAT DOCNO BARU
       ============================================================ */

    ELSIF OLD.status = 'E'
       AND NEW.status = 'F'
       AND COALESCE(NEW.docnotmp, '') <> '' THEN

        /* ========================================================
           JIKA DOKUMEN EXISTING ADA
           UPDATE, BUKAN DELETE + INSERT
           ======================================================== */

        IF EXISTS (
            SELECT 1
            FROM sc_trx.ndk
            WHERE TRIM(docno) = TRIM(NEW.docnotmp)
        ) THEN

            UPDATE sc_trx.ndk t
            SET
                idurut          = x.idurut,
                cabang          = x.cabang,
                docdate         = x.docdate,
                pemohon         = x.pemohon,
                kdsupplier      = x.kdsupplier,
                nmsupplier      = x.nmsupplier,
                alamatsupplier  = x.alamatsupplier,
                kdsalesman      = x.kdsalesman,
                jthtempo        = x.jthtempo,
                isinclusive     = x.isinclusive,
                dk              = x.dk,
                perkiraanarap   = x.perkiraanarap,
                perkiraanlawan  = x.perkiraanlawan,
                nilai           = x.nilai,
                idtax           = x.idtax,
                currcode        = x.currcode,
                kurs            = x.kurs,
                dpp             = x.dpp,
                jumlahpajak     = x.jumlahpajak,
                total           = x.total,
                keterangan      = x.keterangan,
                status          = 'F',
                inputby         = x.inputby,
                inputdate       = x.inputdate,
                updateby        = x.updateby,
                updatedate      = x.updatedate,
                printby         = x.printby,
                printdate       = x.printdate,
                docnotmp        = NEW.docnotmp
            FROM sc_tmp.ndk x
            WHERE TRIM(x.docno) = TRIM(NEW.docno)
              AND TRIM(t.docno) = TRIM(NEW.docnotmp);


        ELSE

            /* ====================================================
               DOKUMEN BELUM ADA
               BARU BOLEH INSERT
               ==================================================== */

            INSERT INTO sc_trx.ndk
            (
                idurut,
                docno,
                cabang,
                docdate,
                pemohon,
                kdsupplier,
                nmsupplier,
                alamatsupplier,
                kdsalesman,
                jthtempo,
                isinclusive,
                dk,
                perkiraanarap,
                perkiraanlawan,
                nilai,
                idtax,
                currcode,
                kurs,
                dpp,
                jumlahpajak,
                total,
                keterangan,
                status,
                inputby,
                inputdate,
                updateby,
                updatedate,
                printby,
                printdate,
                docnotmp
            )
            SELECT
                idurut,
                NEW.docnotmp,
                cabang,
                docdate,
                pemohon,
                kdsupplier,
                nmsupplier,
                alamatsupplier,
                kdsalesman,
                jthtempo,
                isinclusive,
                dk,
                perkiraanarap,
                perkiraanlawan,
                nilai,
                idtax,
                currcode,
                kurs,
                dpp,
                jumlahpajak,
                total,
                keterangan,
                'F',
                inputby,
                inputdate,
                updateby,
                updatedate,
                printby,
                printdate,
                NEW.docnotmp
            FROM sc_tmp.ndk
            WHERE TRIM(docno) = TRIM(NEW.docno);

        END IF;


        /* ========================================================
           CLEANUP TMP
           ======================================================== */

        DELETE FROM sc_tmp.ndk
        WHERE TRIM(docno) = TRIM(NEW.docno);


    /* ============================================================
       CANCEL
       ============================================================ */

    ELSIF OLD.status = 'E'
       AND NEW.status = 'C' THEN

        IF NEW.printby IS NOT NULL
           AND NEW.printby <> ''
           AND NEW.printdate IS NOT NULL THEN

            UPDATE sc_trx.ndk
            SET status = 'P'
            WHERE TRIM(docno) = TRIM(NEW.docnotmp);

        ELSE

            UPDATE sc_trx.ndk
            SET status = 'F'
            WHERE TRIM(docno) = TRIM(NEW.docnotmp);

        END IF;

        DELETE FROM sc_tmp.ndk
        WHERE TRIM(docno) = TRIM(NEW.docno);

    END IF;


    RETURN NEW;

END;
$BODY$;


DROP TRIGGER IF EXISTS tr_ndk_finalize
ON sc_tmp.ndk;

CREATE TRIGGER tr_ndk_finalize
AFTER UPDATE
ON sc_tmp.ndk
FOR EACH ROW
EXECUTE FUNCTION sc_tmp.tr_ndk_finalize();




/* ============================================================
   NDK -> TRANSACTION_DT
   FULL FINAL TRIGGER
   ============================================================

   FLOW:

   INSERT NDK status F
       |
       +--> transaction_dt
              |
              +--> TAHAP 18
              +--> TAHAP 16
              +--> jurnal_hd / jurnal_dt

   UPDATE NDK
       |
       +--> F -> E
       |      |
       |      +--> COPY ke sc_tmp.ndk
       |      +--> DELETE transaction_dt NDK
       |
       +--> E -> F
       |      |
       |      +--> UPDATE / INSERT transaction_dt
       |
       +--> F -> F
              |
              +--> UPDATE transaction_dt

   DELETE NDK
       |
       +--> DELETE transaction_dt
       |
       +--> engine transaction_dt menangani accounting

   ============================================================
   JOURNAL TYPE:

       Supplier + D = NDKAPD
       Supplier + K = NDKAPK

       Customer + D = NDKARD
       Customer + K = NDKARK

   STOCK:
       NONE

   ACCOUNTING:
       YES

   ASSET:
       NONE

   UNIQUEID:
       Jika source NDK memiliki iduniq -> gunakan iduniq
       Jika tidak -> MD5('NDK-TD|' || docno)

   UPDATE:
       berdasarkan DOCNO

   IMPORTANT:
       Tidak memanggil sp_sync_ndk_journal().
       Accounting dibuat oleh transaction_dt engine.
   ============================================================ */


/* ============================================================
   1. FUNCTION NDK -> TRANSACTION_DT
   ============================================================ */

CREATE OR REPLACE FUNCTION sc_trx.fn_sync_ndk_to_transaction_dt(
    p_docno VARCHAR(30)
)
RETURNS VOID
LANGUAGE plpgsql
AS $$
DECLARE
    n                   RECORD;

    v_docno             TEXT;
    v_uniqueid          TEXT;
    v_source_uniqueid   TEXT;

    v_journal_type      CHAR(6);
    v_type_in_out       CHAR(3);
    v_dk                CHAR(1);

    v_user              VARCHAR(50);

    v_existing_id       BIGINT;
    v_existing_uniqueid TEXT;

    v_iduniq_source     TEXT;

    v_customer_code     TEXT := '';
    v_customer_name     TEXT := '';

BEGIN

    /* ========================================================
       1. LOAD NDK
       ======================================================== */

    SELECT *
    INTO n
    FROM sc_trx.ndk
    WHERE BTRIM(docno::TEXT) = BTRIM(p_docno::TEXT)
    LIMIT 1;

    IF NOT FOUND THEN
        RETURN;
    END IF;

    v_docno :=
        BTRIM(COALESCE(n.docno::TEXT, ''));

    IF v_docno = '' THEN
        RETURN;
    END IF;


    /* ========================================================
       2. STATUS
       HANYA FINAL YANG MASUK TRANSACTION_DT
       ======================================================== */

    IF BTRIM(COALESCE(n.status::TEXT, '')) <> 'F' THEN
        RETURN;
    END IF;


    /* ========================================================
       3. DK
       ======================================================== */

    v_dk :=
        UPPER(
            BTRIM(
                COALESCE(n.dk::TEXT, '')
            )
        );

    IF v_dk NOT IN ('D','K') THEN

        RAISE EXCEPTION
            'NDK % gagal: DK harus D atau K. Nilai=%',
            v_docno,
            v_dk;

    END IF;


    /* ========================================================
       4. JOURNAL TYPE

       SUPPLIER
           D -> NDKAPD
           K -> NDKAPK

       CUSTOMER
           D -> NDKARD
           K -> NDKARK
       ======================================================== */

    IF NULLIF(
        BTRIM(
            COALESCE(
                n.kdsupplier::TEXT,
                ''
            )
        ),
        ''
    ) IS NOT NULL
    THEN

        IF v_dk = 'D' THEN
            v_journal_type := 'NDKAPD';
        ELSE
            v_journal_type := 'NDKAPK';
        END IF;

    ELSE

        IF v_dk = 'D' THEN
            v_journal_type := 'NDKARD';
        ELSE
            v_journal_type := 'NDKARK';
        END IF;

    END IF;


    /* ========================================================
       5. VALIDASI JOURNAL TYPE MASTER
       ======================================================== */

    IF NOT EXISTS
    (
        SELECT 1
        FROM sc_mst.journal_type jt
        WHERE BTRIM(jt.journal_type::TEXT)
              = BTRIM(v_journal_type::TEXT)
    )
    THEN

        RAISE EXCEPTION
            'Journal type % tidak ditemukan di sc_mst.journal_type.',
            v_journal_type;

    END IF;


    /* ========================================================
       6. TYPE IN / OUT

       D -> IN
       K -> OUT

       Karena transaction_dt membutuhkan type_in_out.
       Stock tetap NONE.
       ======================================================== */

    v_type_in_out :=
        CASE
            WHEN v_dk = 'D' THEN 'IN'
            ELSE 'OUT'
        END;


    /* ========================================================
       7. USER
       ======================================================== */

    v_user :=
        COALESCE
        (
            NULLIF(
                BTRIM(
                    COALESCE(
                        n.updateby::TEXT,
                        ''
                    )
                ),
                ''
            ),

            NULLIF(
                BTRIM(
                    COALESCE(
                        n.inputby::TEXT,
                        ''
                    )
                ),
                ''
            ),

            CURRENT_USER
        );


    /* ========================================================
       8. IDUNIQ SOURCE

       Jika sc_trx.ndk di database Anda mempunyai field iduniq,
       akan dipakai.

       Jika belum ada, hasil NULL dan memakai deterministic ID.
       ======================================================== */

    v_iduniq_source :=
        NULLIF
        (
            BTRIM(
                COALESCE(
                    TO_JSONB(n)->>'iduniq',
                    ''
                )
            ),
            ''
        );


    /* ========================================================
       9. UNIQUEID TRANSACTION_DT

       Jika iduniq source ada:
           gunakan iduniq

       Jika tidak:
           MD5('NDK-TD|' || docno)
       ======================================================== */

    IF v_iduniq_source IS NOT NULL THEN

        v_uniqueid :=
            v_iduniq_source;

    ELSE

        v_uniqueid :=
            MD5(
                'NDK-TD|' ||
                v_docno
            );

    END IF;


    /* ========================================================
       10. SOURCE UNIQUEID

       Tetap deterministic berdasarkan DOCNO
       bila source iduniq tidak ada.
       ======================================================== */

    v_source_uniqueid :=
        COALESCE
        (
            v_iduniq_source,

            MD5(
                'NDK|' ||
                v_docno
            )
        );


    /* ========================================================
       11. CUSTOMER

       Referensi sc_trx.ndk lama belum mempunyai kdcustomer /
       ncustomer secara eksplisit.

       Bila kolom tersebut tersedia di schema aktual,
       TO_JSONB akan membacanya.
       ======================================================== */

    v_customer_code :=
        COALESCE
        (
            NULLIF(
                BTRIM(
                    COALESCE(
                        TO_JSONB(n)->>'kdcustomer',
                        ''
                    )
                ),
                ''
            ),
            ''
        );

    v_customer_name :=
        COALESCE
        (
            NULLIF(
                BTRIM(
                    COALESCE(
                        TO_JSONB(n)->>'ncustomer',
                        ''
                    )
                ),
                ''
            ),
            ''
        );


    /* ========================================================
       12. VALIDASI COA ACCOUNT
       ======================================================== */

    IF NULLIF(
        BTRIM(
            COALESCE(
                n.perkiraanarap::TEXT,
                ''
            )
        ),
        ''
    ) IS NULL
    THEN

        RAISE EXCEPTION
            'NDK % gagal: perkiraanarap kosong.',
            v_docno;

    END IF;


    IF NULLIF(
        BTRIM(
            COALESCE(
                n.perkiraanlawan::TEXT,
                ''
            )
        ),
        ''
    ) IS NULL
    THEN

        RAISE EXCEPTION
            'NDK % gagal: perkiraanlawan kosong.',
            v_docno;

    END IF;


    /* ACCOUNT / AR / AP */
    IF NOT EXISTS
    (
        SELECT 1
        FROM sc_mst.coa c
        WHERE BTRIM(c.idcoa::TEXT)
              =
              BTRIM(n.perkiraanarap::TEXT)
    )
    THEN

        RAISE EXCEPTION
            'COA AR/AP % tidak ditemukan untuk NDK %.',
            BTRIM(n.perkiraanarap::TEXT),
            v_docno;

    END IF;


    /* COUNTER ACCOUNT */
    IF NOT EXISTS
    (
        SELECT 1
        FROM sc_mst.coa c
        WHERE BTRIM(c.idcoa::TEXT)
              =
              BTRIM(n.perkiraanlawan::TEXT)
    )
    THEN

        RAISE EXCEPTION
            'COA lawan % tidak ditemukan untuk NDK %.',
            BTRIM(n.perkiraanlawan::TEXT),
            v_docno;

    END IF;


    /* ========================================================
       13. CARI TRANSACTION EXISTING BERDASARKAN DOCNO

       IMPORTANT:
       Jika sudah ada, UNIQUEID EXISTING dipertahankan.
       Ini menjaga source identity tetap stabil untuk accounting.
       ======================================================== */

    SELECT
        td.id,
        td.uniqueid
    INTO
        v_existing_id,
        v_existing_uniqueid
    FROM sc_trx.transaction_dt td
    WHERE BTRIM(td.docno::TEXT)
            = v_docno
      AND BTRIM(td.doctype::TEXT)
            = 'NDK'
      AND BTRIM(td.journal_type::TEXT) IN
          (
              'NDKAPD',
              'NDKAPK',
              'NDKARD',
              'NDKARK'
          )
    ORDER BY td.id
    LIMIT 1;


    IF v_existing_id IS NOT NULL THEN

        v_uniqueid :=
            COALESCE(
                NULLIF(
                    BTRIM(v_existing_uniqueid),
                    ''
                ),
                v_uniqueid
            );

    END IF;


    /* ========================================================
       14. UPDATE EXISTING TRANSACTION
       BERDASARKAN DOCNO
       ======================================================== */

    IF v_existing_id IS NOT NULL THEN

        UPDATE sc_trx.transaction_dt
        SET
            uniqueid =
                v_uniqueid,

            source_uniqueid =
                v_source_uniqueid,

            doctype =
                'NDK',

            journal_type =
                v_journal_type,

            line_no =
                1,

            docdate =
                n.docdate,

            idbranch =
                COALESCE(
                    BTRIM(
                        COALESCE(
                            n.cabang::TEXT,
                            ''
                        )
                    ),
                    ''
                ),

            cabang =
                COALESCE(
                    BTRIM(
                        COALESCE(
                            n.cabang::TEXT,
                            ''
                        )
                    ),
                    ''
                ),

            type_in_out =
                v_type_in_out,

            ref_docno =
                '',

            ref_doctype =
                '',

            source_table =
                'sc_trx.ndk',

            source_id =
                n.idurut,

            source_line_id =
                1,

            kdcustomer =
                v_customer_code,

            ncustomer =
                v_customer_name,

            kdsupplier =
                COALESCE(
                    BTRIM(
                        COALESCE(
                            n.kdsupplier::TEXT,
                            ''
                        )
                    ),
                    ''
                ),

            nsupplier =
                COALESCE(
                    BTRIM(
                        COALESCE(
                            n.nmsupplier::TEXT,
                            ''
                        )
                    ),
                    ''
                ),

            /*
               NDK accounting only
               */
            idbarang =
                '',

            namabarang =
                '',

            idunit =
                '',

            idarea =
                '',

            warehouse =
                '',

            bin =
                '',

            batch =
                '',

            lotno =
                '',

            qty =
                0,

            harga =
                0,

            bruto =
                ROUND(
                    COALESCE(
                        n.nilai,
                        0
                    ),
                    2
                ),

            discount =
                0,

            nilai =
                ROUND(
                    COALESCE(
                        n.nilai,
                        0
                    ),
                    2
                ),

            dpp =
                ROUND(
                    COALESCE(
                        n.dpp,
                        0
                    ),
                    2
                ),

            pajak =
                ROUND(
                    COALESCE(
                        n.jumlahpajak,
                        0
                    ),
                    2
                ),

            total =
                ROUND(
                    COALESCE(
                        n.total,
                        0
                    ),
                    2
                ),

            idtax =
                COALESCE(
                    NULLIF(
                        BTRIM(
                            COALESCE(
                                n.idtax::TEXT,
                                ''
                            )
                        ),
                        ''
                    ),
                    'NON'
                ),

            isinclusive =
                COALESCE(
                    NULLIF(
                        BTRIM(
                            COALESCE(
                                n.isinclusive::TEXT,
                                ''
                            )
                        ),
                        ''
                    ),
                    'NO'
                ),

            currcode =
                COALESCE(
                    NULLIF(
                        BTRIM(
                            COALESCE(
                                n.currcode::TEXT,
                                ''
                            )
                        ),
                        ''
                    ),
                    'IDR'
                ),

            kurs =
                COALESCE(
                    n.kurs,
                    1
                ),

            idcoa =
                BTRIM(
                    n.perkiraanarap::TEXT
                ),

            counter_idcoa =
                BTRIM(
                    n.perkiraanlawan::TEXT
                ),

            debet_kredit =
                v_dk,

            module =
                'ACCOUNTING',

            direction =
                'INOUT',

            stock_effect =
                'NONE',

            accounting_effect =
                'YES',

            asset_effect =
                'NONE',

            keterangan =
                COALESCE(
                    n.keterangan,
                    ''
                ),

            updatedby =
                v_user,

            updateddate =
                CURRENT_TIMESTAMP

        WHERE id = v_existing_id;


    /* ========================================================
       15. INSERT BARU
       ======================================================== */

    ELSE

        INSERT INTO sc_trx.transaction_dt
        (
            uniqueid,
            source_uniqueid,

            docno,
            doctype,
            journal_type,
            line_no,
            docdate,

            idbranch,
            cabang,

            type_in_out,

            ref_docno,
            ref_doctype,

            source_table,
            source_id,
            source_line_id,

            kdcustomer,
            ncustomer,

            kdsupplier,
            nsupplier,

            idbarang,
            namabarang,
            idunit,

            idarea,
            warehouse,
            bin,

            batch,
            lotno,

            qty,
            harga,
            bruto,
            discount,
            nilai,
            dpp,
            pajak,
            total,

            idtax,
            isinclusive,

            currcode,
            kurs,

            idcoa,
            counter_idcoa,
            debet_kredit,

            module,
            direction,
            stock_effect,
            accounting_effect,
            asset_effect,

            keterangan,

            createdby,
            createddate
        )
        VALUES
        (
            v_uniqueid,
            v_source_uniqueid,

            v_docno,
            'NDK',
            v_journal_type,
            1,
            n.docdate,

            COALESCE(
                BTRIM(
                    COALESCE(
                        n.cabang::TEXT,
                        ''
                    )
                ),
                ''
            ),

            COALESCE(
                BTRIM(
                    COALESCE(
                        n.cabang::TEXT,
                        ''
                    )
                ),
                ''
            ),

            v_type_in_out,

            '',
            '',

            'sc_trx.ndk',
            n.idurut,
            1,

            v_customer_code,
            v_customer_name,

            COALESCE(
                BTRIM(
                    COALESCE(
                        n.kdsupplier::TEXT,
                        ''
                    )
                ),
                ''
            ),

            COALESCE(
                BTRIM(
                    COALESCE(
                        n.nmsupplier::TEXT,
                        ''
                    )
                ),
                ''
            ),

            '',
            '',
            '',

            '',
            '',
            '',

            '',
            '',

            0,
            0,

            ROUND(
                COALESCE(
                    n.nilai,
                    0
                ),
                2
            ),

            0,

            ROUND(
                COALESCE(
                    n.nilai,
                    0
                ),
                2
            ),

            ROUND(
                COALESCE(
                    n.dpp,
                    0
                ),
                2
            ),

            ROUND(
                COALESCE(
                    n.jumlahpajak,
                    0
                ),
                2
            ),

            ROUND(
                COALESCE(
                    n.total,
                    0
                ),
                2
            ),

            COALESCE(
                NULLIF(
                    BTRIM(
                        COALESCE(
                            n.idtax::TEXT,
                            ''
                        )
                    ),
                    ''
                ),
                'NON'
            ),

            COALESCE(
                NULLIF(
                    BTRIM(
                        COALESCE(
                            n.isinclusive::TEXT,
                            ''
                        )
                    ),
                    ''
                ),
                'NO'
            ),

            COALESCE(
                NULLIF(
                    BTRIM(
                        COALESCE(
                            n.currcode::TEXT,
                            ''
                        )
                    ),
                    ''
                ),
                'IDR'
            ),

            COALESCE(
                n.kurs,
                1
            ),

            BTRIM(
                n.perkiraanarap::TEXT
            ),

            BTRIM(
                n.perkiraanlawan::TEXT
            ),

            v_dk,

            'ACCOUNTING',
            'INOUT',
            'NONE',
            'YES',
            'NONE',

            COALESCE(
                n.keterangan,
                ''
            ),

            v_user,
            COALESCE(
                n.inputdate,
                CURRENT_TIMESTAMP
            )
        );

    END IF;

END;
$$;

CREATE OR REPLACE FUNCTION sc_trx.tr_ndk()
RETURNS TRIGGER
LANGUAGE plpgsql
AS $$
BEGIN

    /* ============================================================
       INSERT
       ============================================================ */

    IF TG_OP = 'INSERT' THEN

        IF BTRIM(
            COALESCE(
                NEW.status::TEXT,
                ''
            )
        ) = 'F'
        THEN

            PERFORM sc_trx.fn_sync_ndk_to_transaction_dt(
                BTRIM(
                    NEW.docno::TEXT
                )
            );

        END IF;

        RETURN NEW;

    END IF;


    /* ============================================================
       UPDATE
       ============================================================ */

    IF TG_OP = 'UPDATE' THEN

        /* ========================================================
           F -> E

           MASUK MODE EDIT

           Tetap COPY ke TMP.
           JANGAN DELETE transaction_dt.

           transaction_dt akan tetap mempertahankan identity.
           Nanti E -> F akan UPDATE transaction_dt existing.
           ======================================================== */

        IF BTRIM(
               COALESCE(
                   OLD.status::TEXT,
                   ''
               )
           ) = 'F'

           AND

           BTRIM(
               COALESCE(
                   NEW.status::TEXT,
                   ''
               )
           ) = 'E'
        THEN

            INSERT INTO sc_tmp.ndk
            (
                idurut,
                docno,
                cabang,
                docdate,
                pemohon,
                kdsupplier,
                nmsupplier,
                alamatsupplier,
                kdsalesman,
                jthtempo,
                isinclusive,
                dk,
                perkiraanarap,
                perkiraanlawan,
                nilai,
                idtax,
                currcode,
                kurs,
                dpp,
                jumlahpajak,
                total,
                keterangan,
                status,
                inputby,
                inputdate,
                updateby,
                updatedate,
                printby,
                printdate,
                docnotmp
            )
            SELECT
                idurut,
                NEW.docno,
                cabang,
                docdate,
                pemohon,
                kdsupplier,
                nmsupplier,
                alamatsupplier,
                kdsalesman,
                jthtempo,
                isinclusive,
                dk,
                perkiraanarap,
                perkiraanlawan,
                nilai,
                idtax,
                currcode,
                kurs,
                dpp,
                jumlahpajak,
                total,
                keterangan,
                NEW.status,
                inputby,
                inputdate,
                updateby,
                updatedate,
                printby,
                printdate,
                NEW.docno
            FROM sc_trx.ndk
            WHERE BTRIM(docno)
                    = BTRIM(NEW.docno);

            /*
             * PENTING:
             * Tidak ada DELETE transaction_dt di sini.
             *
             * transaction_dt tetap ada sebagai identity
             * dari dokumen lama.
             */

            RETURN NEW;

        END IF;


        /* ========================================================
           E -> F
           F -> F
           FINAL UPDATE
           ======================================================== */

        IF BTRIM(
               COALESCE(
                   NEW.status::TEXT,
                   ''
               )
           ) = 'F'
        THEN

            PERFORM sc_trx.fn_sync_ndk_to_transaction_dt(
                BTRIM(
                    NEW.docno::TEXT
                )
            );

            RETURN NEW;

        END IF;


        /* ========================================================
           E -> E

           Tidak ada posting.
           ======================================================== */

        IF BTRIM(
               COALESCE(
                   NEW.status::TEXT,
                   ''
               )
           ) = 'E'
        THEN

            RETURN NEW;

        END IF;


        /* ========================================================
           CANCEL
           ======================================================== */

        IF BTRIM(
               COALESCE(
                   NEW.status::TEXT,
                   ''
               )
           ) = 'C'
        THEN

            DELETE FROM sc_trx.transaction_dt
            WHERE BTRIM(docno::TEXT)
                    = BTRIM(NEW.docno::TEXT)

              AND BTRIM(journal_type::TEXT) IN
                  (
                      'NDKAPD',
                      'NDKAPK',
                      'NDKARD',
                      'NDKARK'
                  );

        END IF;

        RETURN NEW;

    END IF;


    /* ============================================================
       DELETE
       ============================================================ */

    IF TG_OP = 'DELETE' THEN

        DELETE FROM sc_trx.transaction_dt
        WHERE BTRIM(docno::TEXT)
                = BTRIM(OLD.docno::TEXT)

          AND BTRIM(journal_type::TEXT) IN
              (
                  'NDKAPD',
                  'NDKAPK',
                  'NDKARD',
                  'NDKARK'
              );

        RETURN OLD;

    END IF;


    RETURN NULL;

END;
$$;


ALTER FUNCTION sc_trx.tr_ndk()
OWNER TO postgres;


DROP TRIGGER IF EXISTS tr_ndk
ON sc_trx.ndk;

CREATE TRIGGER tr_ndk
AFTER INSERT OR UPDATE OR DELETE
ON sc_trx.ndk
FOR EACH ROW
EXECUTE FUNCTION sc_trx.tr_ndk();