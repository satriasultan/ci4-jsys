/*
===============================================================================
JSYS ERP - OPENING AR : PERBAIKAN ROUTING ACCOUNTING (OPENAR)

MASALAH YANG DIPERBAIKI
-----------------------
1. sc_trx.fn_resolve_transaction_coa() tidak mengenali OPENAR sebagai manual
   accounting, sehingga COA baris (kode_prk dari file Excel) dapat diganti
   oleh resolver operasional.
2. sc_trx.fn_post_accounting_transaction() tidak menganggap OPENAR sebagai
   "manual accounting", sehingga perkiraan lawan (counter_idcoa) dari form
   diabaikan. Baris jurnal dibentuk dari sc_mst.journal_type_coa dengan role
   ADJUSTMENT (COA dari konfigurasi umum) untuk sisi D maupun K, sehingga
   jurnal yang terbentuk selalu Dr = Cr pada COA yang sama (tidak bermakna).
3. Mapping sc_mst.journal_type_coa untuk OPENAR masih memakai role ADJUSTMENT.

PERBAIKAN
---------
0. OPENAR DIBERSIHKAN dari daftar v_is_manual pada
   fn_prepare_transaction_dt(). Script AR versi awal menambahkan OPENAR ke
   daftar itu; akibatnya baris dk=K dipaksa type_in_out = 'OUT' dan ditolak
   trigger fn_validate_transaction_type() karena sc_mst.journal_type OPENAR
   berarah 'IN'. OPENAP tidak terdaftar di daftar tersebut dan terbukti
   berjalan, jadi OPENAR disamakan.
1. OPENAR ditambahkan ke daftar journal type yang mempertahankan COA input
   (p_current_idcoa) pada fn_resolve_transaction_coa(). COA baris = kode_prk.
2. OPENAR ditambahkan ke daftar manual accounting pada
   fn_post_accounting_transaction() sehingga role ACCOUNT memakai
   transaction_dt.idcoa dan role COUNTER_ACCOUNT memakai
   transaction_dt.counter_idcoa, dengan toggle D/K mengikuti debet_kredit.
3. Mapping OPENAR diubah menjadi:
      seq 1 : ACCOUNT          D  NILAI  (perkiraan dokumen / kode_prk)
      seq 2 : COUNTER_ACCOUNT  K  NILAI  (perkiraan lawan dari form Process)

HASIL PER BARIS (OPENAR)
------------------------
   dk = D : Dr  kode_prk          / Cr counter_idcoa
   dk = K : Dr  counter_idcoa     / Cr kode_prk

Sifat    : idempoten. Patch fungsi hanya dijalankan bila OPENAR belum terdaftar
           pada function body, sehingga aman dijalankan berulang.
Prasyarat: sc_trx.fn_prepare_transaction_dt(), fn_resolve_transaction_coa(),
           fn_post_accounting_transaction() sudah ada (skema accounting JSYS).
Catatan  : Hanya menyentuh journal_type OPENAR. Journal type lain tidak berubah.
           fn_prepare_transaction_dt() TIDAK diubah: OPENAR mengikuti jalur
           OPENAP (module OPENING, stock_effect NONE) yang sudah terbukti.
===============================================================================
*/

BEGIN;

-- ---------------------------------------------------------------------------
-- 0. fn_prepare_transaction_dt() : OPENAR TIDAK boleh masuk daftar v_is_manual
--    Alasan: pada cabang manual, fn_prepare menurunkan type_in_out dari
--    debet_kredit (D -> IN, K -> OUT). sc_mst.journal_type OPENAR berarah 'IN',
--    sehingga baris dk=K akan ditolak trigger fn_validate_transaction_type():
--    "Journal type OPENAR hanya boleh type_in_out IN".
--    OPENAP (menu I.T.D.5) memakai jalur non-manual dan terbukti berjalan,
--    jadi OPENAR disamakan. Blok ini hanya membersihkan bila script AR versi
--    awal (yang menambahkan OPENAR ke daftar manual) pernah dijalankan.
-- ---------------------------------------------------------------------------
DO $$
DECLARE
    v_def     TEXT;
    v_after   TEXT;
    v_pos     INTEGER;
    v_seg     TEXT;
    v_new_seg TEXT;
    v_q       TEXT := chr(39);
BEGIN
    SELECT pg_get_functiondef(p.oid)
    INTO v_def
    FROM pg_proc p
    JOIN pg_namespace n ON n.oid = p.pronamespace
    WHERE n.nspname = 'sc_trx'
      AND p.proname = 'fn_prepare_transaction_dt'
    LIMIT 1;

    IF v_def IS NULL THEN
        RAISE EXCEPTION 'Fungsi sc_trx.fn_prepare_transaction_dt() tidak ditemukan';
    END IF;

    IF position(v_q || 'OPENAR' || v_q IN v_def) = 0 THEN
        RAISE NOTICE 'fn_prepare_transaction_dt: OPENAR tidak terdaftar (sesuai OPENAP), tidak ada perubahan';
    ELSE
        v_pos := position('v_is_manual :=' IN v_def);

        IF v_pos = 0 THEN
            RAISE EXCEPTION 'Variabel v_is_manual tidak ditemukan pada fn_prepare_transaction_dt';
        END IF;

        -- Potongan daftar v_is_manual (jauh di bawah 1500 karakter).
        v_seg := substring(v_def FROM v_pos FOR 1500);

        v_new_seg := regexp_replace(v_seg, '\s*' || v_q || 'OPENAR' || v_q || '\s*,', '', 'g');
        v_new_seg := regexp_replace(v_new_seg, ',\s*' || v_q || 'OPENAR' || v_q, '', 'g');
        v_new_seg := regexp_replace(v_new_seg, '\s*' || v_q || 'OPENAR' || v_q, '', 'g');

        v_def := left(v_def, v_pos - 1)
              || v_new_seg
              || substring(v_def FROM v_pos + length(v_seg));

        EXECUTE v_def;

        SELECT pg_get_functiondef(p.oid)
        INTO v_after
        FROM pg_proc p
        JOIN pg_namespace n ON n.oid = p.pronamespace
        WHERE n.nspname = 'sc_trx'
          AND p.proname = 'fn_prepare_transaction_dt'
        LIMIT 1;

        IF position(v_q || 'OPENAR' || v_q IN v_after) > 0 THEN
            RAISE EXCEPTION 'OPENAR masih terdaftar pada fn_prepare_transaction_dt(). Periksa manual.';
        END IF;

        RAISE NOTICE 'fn_prepare_transaction_dt: OPENAR dikeluarkan dari daftar manual (type_in_out tetap IN)';
    END IF;
END;
$$;


-- ---------------------------------------------------------------------------
-- 1. fn_resolve_transaction_coa() : OPENAR mempertahankan COA input (kode_prk)
-- ---------------------------------------------------------------------------
DO $$
DECLARE
    v_def TEXT;
    v_pos INTEGER;
    v_jv  INTEGER;
BEGIN
    SELECT pg_get_functiondef(p.oid)
    INTO v_def
    FROM pg_proc p
    JOIN pg_namespace n ON n.oid = p.pronamespace
    WHERE n.nspname = 'sc_trx'
      AND p.proname = 'fn_resolve_transaction_coa'
      AND pg_get_function_identity_arguments(p.oid) LIKE '%p_idtax%'
    LIMIT 1;

    IF v_def IS NULL THEN
        RAISE EXCEPTION 'Fungsi sc_trx.fn_resolve_transaction_coa(6 parameter) tidak ditemukan';
    END IF;

    IF position('''OPENAR''' IN v_def) > 0 THEN
        RAISE NOTICE 'fn_resolve_transaction_coa: OPENAR sudah terdaftar, patch dilewati';
    ELSE
        v_pos := position('IF TRIM(p_journal_type) IN' IN v_def);

        IF v_pos = 0 THEN
            RAISE EXCEPTION 'Pola "IF TRIM(p_journal_type) IN" tidak ditemukan pada fn_resolve_transaction_coa';
        END IF;

        v_jv := position('''JVGENL''' IN substring(v_def FROM v_pos));

        IF v_jv = 0 THEN
            RAISE EXCEPTION 'Daftar journal type manual tidak ditemukan pada fn_resolve_transaction_coa';
        END IF;

        v_def := overlay(v_def placing '''OPENAR'', ' from v_pos + v_jv - 1 for 0);

        EXECUTE v_def;

        RAISE NOTICE 'fn_resolve_transaction_coa: OPENAR ditambahkan (COA input dipertahankan)';
    END IF;
END;
$$;


-- ---------------------------------------------------------------------------
-- 2. fn_post_accounting_transaction() : OPENAR memakai idcoa + counter_idcoa
-- ---------------------------------------------------------------------------
DO $$
DECLARE
    v_def TEXT;
    v_pos INTEGER;
    v_jv  INTEGER;
BEGIN
    SELECT pg_get_functiondef(p.oid)
    INTO v_def
    FROM pg_proc p
    JOIN pg_namespace n ON n.oid = p.pronamespace
    WHERE n.nspname = 'sc_trx'
      AND p.proname = 'fn_post_accounting_transaction'
    LIMIT 1;

    IF v_def IS NULL THEN
        RAISE EXCEPTION 'Fungsi sc_trx.fn_post_accounting_transaction() tidak ditemukan';
    END IF;

    IF position('''OPENAR''' IN v_def) > 0 THEN
        RAISE NOTICE 'fn_post_accounting_transaction: OPENAR sudah terdaftar, patch dilewati';
    ELSE
        -- 2a. v_is_manual (role ACCOUNT / COUNTER_ACCOUNT memakai COA transaksi)
        v_pos := position('v_is_manual :=' IN v_def);

        IF v_pos = 0 THEN
            RAISE EXCEPTION 'Variabel v_is_manual tidak ditemukan pada fn_post_accounting_transaction';
        END IF;

        v_jv := position('''JVGENL''' IN substring(v_def FROM v_pos));

        IF v_jv = 0 THEN
            RAISE EXCEPTION 'Daftar v_is_manual tidak ditemukan pada fn_post_accounting_transaction';
        END IF;

        v_def := overlay(v_def placing '''OPENAR'', ' from v_pos + v_jv - 1 for 0);

        -- 2b. v_manual_toggle (membalik D/K mengikuti debet_kredit)
        v_pos := position('v_manual_toggle :=' IN v_def);

        IF v_pos = 0 THEN
            RAISE EXCEPTION 'Variabel v_manual_toggle tidak ditemukan pada fn_post_accounting_transaction';
        END IF;

        v_jv := position('''JVGENL''' IN substring(v_def FROM v_pos));

        IF v_jv = 0 THEN
            RAISE EXCEPTION 'Daftar v_manual_toggle tidak ditemukan pada fn_post_accounting_transaction';
        END IF;

        v_def := overlay(v_def placing '''OPENAR'', ' from v_pos + v_jv - 1 for 0);

        EXECUTE v_def;

        RAISE NOTICE 'fn_post_accounting_transaction: OPENAR ditambahkan (idcoa + counter_idcoa)';
    END IF;
END;
$$;

-- ---------------------------------------------------------------------------
-- 3. Mapping sc_mst.journal_type_coa untuk OPENAR
-- ---------------------------------------------------------------------------
INSERT INTO sc_mst.journal_type_coa
    (journal_type, account_role, idcoa, debit_credit, value_source, seq, active,
     description, createdby, createddate)
SELECT 'OPENAR', 'ACCOUNT', '', 'D', 'NILAI', 1, 'YES',
       'OPENAR - perkiraan dokumen (kode_prk)', 'SYSTEM', CURRENT_TIMESTAMP
 WHERE NOT EXISTS
       (SELECT 1 FROM sc_mst.journal_type_coa
         WHERE btrim(journal_type::text) = 'OPENAR' AND seq = 1);

INSERT INTO sc_mst.journal_type_coa
    (journal_type, account_role, idcoa, debit_credit, value_source, seq, active,
     description, createdby, createddate)
SELECT 'OPENAR', 'COUNTER_ACCOUNT', '', 'K', 'NILAI', 2, 'YES',
       'OPENAR - perkiraan lawan (counter COA dari form Process)', 'SYSTEM', CURRENT_TIMESTAMP
 WHERE NOT EXISTS
       (SELECT 1 FROM sc_mst.journal_type_coa
         WHERE btrim(journal_type::text) = 'OPENAR' AND seq = 2);

UPDATE sc_mst.journal_type_coa
   SET account_role = 'ACCOUNT',
       idcoa        = '',
       debit_credit = 'D',
       value_source = 'NILAI',
       active       = 'YES',
       description  = 'OPENAR - perkiraan dokumen (kode_prk)',
       updatedby    = 'SYSTEM',
       updateddate  = CURRENT_TIMESTAMP
 WHERE btrim(journal_type::text) = 'OPENAR'
   AND seq = 1;

UPDATE sc_mst.journal_type_coa
   SET account_role = 'COUNTER_ACCOUNT',
       idcoa        = '',
       debit_credit = 'K',
       value_source = 'NILAI',
       active       = 'YES',
       description  = 'OPENAR - perkiraan lawan (counter COA dari form Process)',
       updatedby    = 'SYSTEM',
       updateddate  = CURRENT_TIMESTAMP
 WHERE btrim(journal_type::text) = 'OPENAR'
   AND seq = 2;

-- ---------------------------------------------------------------------------
-- 4. Verifikasi
-- ---------------------------------------------------------------------------
DO $$
DECLARE
    v_row RECORD;
BEGIN
    RAISE NOTICE '--- Mapping OPENAR (sc_mst.journal_type_coa) ---';

    FOR v_row IN
        SELECT seq, account_role, debit_credit, value_source, active
        FROM sc_mst.journal_type_coa
        WHERE btrim(journal_type::text) = 'OPENAR'
        ORDER BY seq
    LOOP
        RAISE NOTICE 'seq=% role=% dc=% source=% active=%',
            v_row.seq, v_row.account_role, v_row.debit_credit,
            v_row.value_source, v_row.active;
    END LOOP;

    RAISE NOTICE '--- Cek patch fungsi ---';
    RAISE NOTICE 'fn_resolve_transaction_coa OPENAR : %',
        (SELECT CASE WHEN position('''OPENAR''' IN pg_get_functiondef(p.oid)) > 0
                     THEN 'OK' ELSE 'BELUM' END
           FROM pg_proc p JOIN pg_namespace n ON n.oid = p.pronamespace
          WHERE n.nspname = 'sc_trx' AND p.proname = 'fn_resolve_transaction_coa'
            AND pg_get_function_identity_arguments(p.oid) LIKE '%p_idtax%' LIMIT 1);
    RAISE NOTICE 'fn_post_accounting_transaction OPENAR : %',
        (SELECT CASE WHEN position('''OPENAR''' IN pg_get_functiondef(p.oid)) > 0
                     THEN 'OK' ELSE 'BELUM' END
           FROM pg_proc p JOIN pg_namespace n ON n.oid = p.pronamespace
          WHERE n.nspname = 'sc_trx' AND p.proname = 'fn_post_accounting_transaction' LIMIT 1);
END;
$$;

COMMIT;


/*
===============================================================================
UJI CEPAT (opsional, jalankan manual; seluruh transaksi di-rollback)

BEGIN;

INSERT INTO sc_trx.transaction_dt
 (uniqueid, source_uniqueid, docno, doctype, journal_type, line_no, docdate,
  idbranch, cabang, type_in_out, ref_docno, ref_doctype, source_table,
  source_id, source_line_id, kdcustomer, ncustomer, qty, bruto, nilai, dpp,
  total, idtax, isinclusive, currcode, kurs, idcoa, counter_idcoa,
  debet_kredit, debet, kredit, keterangan, createdby)
VALUES
 ('UJI-OPENAR-K','UJI-OPENAR-K','OARUJI01','OPENAR','OPENAR',1,CURRENT_DATE,
  'JTS','PT JATIM TAMAN STEEL MFG','IN','X','AR-LEGACY','sc_im.import_ar',1,1,
  'C-A010','ARTHA TEKNIK MANDIRI, PT.',0,1000,1000,1000,1000,'NON','NO','IDR',1,
  '214116','311101','K',0,1000,'uji opening ar','TEST'),
 ('UJI-OPENAR-D','UJI-OPENAR-D','OARUJI01','OPENAR','OPENAR',2,CURRENT_DATE,
  'JTS','PT JATIM TAMAN STEEL MFG','IN','X','AR-LEGACY','sc_im.import_ar',1,2,
  'C-B0016','BPK BAHAR',0,500,500,500,500,'NON','NO','IDR',1,
  '114005','311101','D',500,0,'uji opening ar','TEST');

-- Harapan:
--   baris dk=K : Dr 311101 (counter)  / Cr 214116 (kode_prk)
--   baris dk=D : Dr 114005 (kode_prk) / Cr 311101 (counter)
SELECT t.line_no, t.debet_kredit, t.idcoa, t.counter_idcoa,
       j.idcoa AS jurnal_coa, j.debet, j.kredit
FROM sc_trx.transaction_dt t
JOIN sc_trx.jurnal_dt j ON j.source_uniqueid = t.uniqueid
WHERE t.docno = 'OARUJI01'
ORDER BY t.line_no, j.debet DESC;

ROLLBACK;
===============================================================================
*/

