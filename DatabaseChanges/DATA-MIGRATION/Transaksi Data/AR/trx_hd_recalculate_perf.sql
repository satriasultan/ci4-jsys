/*
===============================================================================
JSYS ERP - PERFORMA : sc_trx.fn_recalculate_transaction_hd()

MASALAH
-------
Fungsi ini dipanggil trigger sc_trx.tr_transaction_dt_sync_hd untuk SETIAP baris
transaction_dt yang di-insert. Versi lama memakai predikat:

    WHERE p_docno IS NULL OR h.docno = p_docno
    WHERE p_docno IS NULL OR t.docno = p_docno

Predikat "OR p_docno IS NULL" membuat PostgreSQL TIDAK bisa memakai index
docno, sehingga setiap insert memicu full scan tabel transaction_dt dan
transaction_hd. Untuk import n baris, total biaya menjadi O(n^2)
(930 baris opening AP = +-16 detik hanya untuk recalculate header;
5.000 baris bisa menjadi beberapa menit).

PERBAIKAN
---------
Versi ini memisahkan dua jalur:
    - p_docno NULL      -> rebuild penuh (delete semua header, lalu rebuild
                           per docno secara berulang).
    - p_docno NOT NULL  -> hanya docno tersebut (index docno dipakai).
Semantik hasil (isi kolom header) tidak berubah sama sekali.

Sifat   : idempoten (CREATE OR REPLACE), aman dijalankan berulang.
Dampak  : hanya cara pengambilan baris; kolom & nilai header tetap sama.
===============================================================================
*/

BEGIN;

CREATE OR REPLACE FUNCTION sc_trx.fn_recalculate_transaction_hd(p_docno text DEFAULT NULL::text)
 RETURNS void
 LANGUAGE plpgsql
AS $function$
DECLARE
    v_docno TEXT;
BEGIN

    /* Rebuild penuh: tetap didukung, dijalankan per docno. */
    IF p_docno IS NULL THEN

        DELETE FROM sc_trx.transaction_hd;

        FOR v_docno IN
            SELECT DISTINCT t.docno
            FROM sc_trx.transaction_dt t
        LOOP
            PERFORM sc_trx.fn_recalculate_transaction_hd(v_docno);
        END LOOP;

        RETURN;

    END IF;


    DELETE FROM sc_trx.transaction_hd h
    WHERE h.docno = p_docno;


    INSERT INTO sc_trx.transaction_hd
    (
        docno,
        doctype,
        journal_type,
        type_in_out,
        docdate,
        idbranch,
        cabang,
        ref_docno,
        ref_doctype,
        module,
        direction,
        stock_effect,
        accounting_effect,
        asset_effect,
        total_qty,
        total_qty_in,
        total_qty_out,
        total_bruto,
        total_discount,
        total_nilai,
        total_dpp,
        total_pajak,
        total_total,
        total_debet,
        total_kredit,
        balance,
        total_line
    )
    SELECT
        t.docno,
        t.doctype,
        t.journal_type,
        t.type_in_out,
        MIN(t.docdate),
        t.idbranch,
        t.cabang,
        t.ref_docno,
        t.ref_doctype,
        MAX(COALESCE(t.module, '')),
        MAX(COALESCE(t.direction, '')),
        MAX(COALESCE(t.stock_effect, 'NONE')),
        MAX(COALESCE(t.accounting_effect, 'NO')),
        MAX(COALESCE(t.asset_effect, 'NONE')),
        COALESCE(SUM(t.qty), 0),
        COALESCE(
            SUM(
                CASE
                    WHEN t.type_in_out = 'IN'
                    THEN COALESCE(t.qty, 0)
                    ELSE 0
                END
            ),
            0
        ),
        COALESCE(
            SUM(
                CASE
                    WHEN t.type_in_out = 'OUT'
                    THEN COALESCE(t.qty, 0)
                    ELSE 0
                END
            ),
            0
        ),
        COALESCE(SUM(t.bruto), 0),
        COALESCE(SUM(t.discount), 0),
        COALESCE(SUM(t.nilai), 0),
        COALESCE(SUM(t.dpp), 0),
        COALESCE(SUM(t.pajak), 0),
        COALESCE(SUM(t.total), 0),
        COALESCE(SUM(t.debet), 0),
        COALESCE(SUM(t.kredit), 0),
        ABS(
            ROUND(
                COALESCE(SUM(t.debet), 0)
                -
                COALESCE(SUM(t.kredit), 0),
                2
            )
        ),
        COUNT(*)
    FROM sc_trx.transaction_dt t
    WHERE t.docno = p_docno
    GROUP BY
        t.docno,
        t.doctype,
        t.journal_type,
        t.type_in_out,
        t.idbranch,
        t.cabang,
        t.ref_docno,
        t.ref_doctype;

END;
$function$;

COMMIT;
