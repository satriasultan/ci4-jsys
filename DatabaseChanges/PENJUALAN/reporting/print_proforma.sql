CREATE TABLE IF NOT EXISTS sc_trx.view_print_proforma_dtl
(
    docno character(30),
    idurut integer,
    idbarang character(20),
    nmbarang character(150),
    unit character(20),
    qty numeric(18,4),
    price numeric(18,4),
    usdmt numeric(18,4),
    exchange numeric(18,4),
    amount numeric(18,4),
    description text,
    status character(6),
    inputby character varying(50),
    inputdate timestamp without time zone,
    updateby character varying(50),
    updatedate timestamp without time zone,
    docnotmp character(30),
    printby character varying(50)
)
TABLESPACE pg_default;

ALTER TABLE sc_trx.view_print_proforma_dtl ALTER COLUMN qty TYPE numeric(18,4);
ALTER TABLE sc_trx.view_print_proforma_dtl ALTER COLUMN price TYPE numeric(18,4);
ALTER TABLE sc_trx.view_print_proforma_dtl ALTER COLUMN usdmt TYPE numeric(18,4);
ALTER TABLE sc_trx.view_print_proforma_dtl ALTER COLUMN exchange TYPE numeric(18,4);
ALTER TABLE sc_trx.view_print_proforma_dtl ALTER COLUMN amount TYPE numeric(18,4);






CREATE OR REPLACE FUNCTION sc_trx.pr_generate_pov_proforma_dtl(
    vr_docno character,
    vr_userid character
)
RETURNS SETOF void
LANGUAGE plpgsql
AS $BODY$
DECLARE
    v_jumlah INT;
    v_tgl TIMESTAMP := now();
    i INT;
BEGIN
    -- Hapus data lama untuk user ini
    DELETE FROM sc_trx.view_print_proforma_dtl 
    WHERE printby = vr_userid;

    -- Copy dari proformadtl ke view_print
    INSERT INTO sc_trx.view_print_proforma_dtl (
        docno,
        idurut,
        idbarang,
        nmbarang,
        unit,
        qty,
        price,
        usdmt,
        exchange,
        amount,
        description,
        status,
        inputby,
        inputdate,
        updateby,
        updatedate,
        docnotmp,
        printby
    )
    SELECT 
        docno,
        idurut,
        idbarang,
        nmbarang,
        unit,
        qty,
        price,
        usdmt,
        exchange,
        amount,
        description,
        status,
        inputby,
        inputdate,
        updateby,
        updatedate,
        docnotmp,
        vr_userid
    FROM sc_trx.proformadtl
    WHERE docno = vr_docno;

    -- Hitung baris
    SELECT COUNT(*) INTO v_jumlah
    FROM sc_trx.view_print_proforma_dtl
    WHERE docno = vr_docno
      AND printby = vr_userid;

    -- Tambah baris kosong sampai minimal 10 baris
    IF v_jumlah < 10 THEN
        FOR i IN (v_jumlah + 1)..10 LOOP
            INSERT INTO sc_trx.view_print_proforma_dtl (
                docno,
                idurut,
                idbarang,
                nmbarang,
                unit,
                qty,
                price,
                usdmt,
                exchange,
                amount,
                description,
                status,
                inputby,
                inputdate,
                updateby,
                updatedate,
                docnotmp,
                printby
            )
            VALUES (
                vr_docno,
                i,
                NULL,
                NULL,
                NULL,
                NULL,
                NULL,
                NULL,
                NULL,
                NULL,
                NULL,
                NULL,
                vr_userid,
                v_tgl,
                NULL,
                NULL,
                NULL,
                vr_userid
            );
        END LOOP;
    END IF;

    RETURN;
END;
$BODY$;

ALTER FUNCTION sc_trx.pr_generate_pov_proforma_dtl(character, character)
OWNER TO postgres;
