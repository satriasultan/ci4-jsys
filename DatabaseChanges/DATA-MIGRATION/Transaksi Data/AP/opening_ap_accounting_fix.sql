/*
===============================================================================
JSYS ERP - OPENING AP : PERBAIKAN ROUTING ACCOUNTING (OPENAP)

MASALAH YANG DIPERBAIKI
-----------------------
1. sc_trx.fn_resolve_transaction_coa() untuk journal_type OPENAP masuk ke cabang
   module = 'OPENING' sehingga COA baris (kode_prk dari file Excel) DIBUANG dan
   diganti COA konfigurasi umum (ppersediaan). Akibatnya semua baris opening AP
   terposting ke satu COA yang sama.
2. sc_trx.fn_post_accounting_transaction() tidak menganggap OPENAP sebagai
   "manual accounting", sehingga perkiraan lawan (counter_idcoa) dari form
   diabaikan. Baris jurnal dibentuk dari sc_mst.journal_type_coa dengan role
   ADJUSTMENT (COA dari konfigurasi umum) untuk sisi D maupun K, sehingga
   jurnal yang terbentuk selalu Dr = Cr pada COA yang sama (tidak bermakna).
3. Mapping sc_mst.journal_type_coa untuk OPENAP masih memakai role ADJUSTMENT.

PERBAIKAN
---------
1. OPENAP ditambahkan ke daftar journal type yang mempertahankan COA input
   (p_current_idcoa) pada fn_resolve_transaction_coa(). COA baris = kode_prk.
2. OPENAP ditambahkan ke daftar manual accounting pada
   fn_post_accounting_transaction() sehingga role ACCOUNT memakai
   transaction_dt.idcoa dan role COUNTER_ACCOUNT memakai
   transaction_dt.counter_idcoa, dengan toggle D/K mengikuti debet_kredit.
3. Mapping OPENAP diubah menjadi:
      seq 1 : ACCOUNT          D  NILAI  (perkiraan dokumen / kode_prk)
      seq 2 : COUNTER_ACCOUNT  K  NILAI  (perkiraan lawan dari form Process)

HASIL PER BARIS (OPENAP)
------------------------
   dk = D : Dr  kode_prk          / Cr counter_idcoa
   dk = K : Dr  counter_idcoa     / Cr kode_prk

Sifat    : idempoten. Patch fungsi hanya dijalankan bila OPENAP belum terdaftar
           pada function body, sehingga aman dijalankan berulang.
Prasyarat: sc_trx.fn_prepare_transaction_dt() & fn_post_accounting_transaction()
           sudah ada (skema accounting JSYS standard).
Catatan  : Hanya menyentuh journal_type OPENAP. Journal type lain tidak berubah.
===============================================================================
*/

BEGIN;

-- ---------------------------------------------------------------------------
-- 1. fn_resolve_transaction_coa() : OPENAP mempertahankan COA input (kode_prk)
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

    IF position('''OPENAP''' IN v_def) > 0 THEN
        RAISE NOTICE 'fn_resolve_transaction_coa: OPENAP sudah terdaftar, patch dilewati';
    ELSE
        v_pos := position('IF TRIM(p_journal_type) IN' IN v_def);

        IF v_pos = 0 THEN
            RAISE EXCEPTION 'Pola "IF TRIM(p_journal_type) IN" tidak ditemukan pada fn_resolve_transaction_coa';
        END IF;

        v_jv := position('''JVGENL''' IN substring(v_def FROM v_pos));

        IF v_jv = 0 THEN
            RAISE EXCEPTION 'Daftar journal type manual tidak ditemukan pada fn_resolve_transaction_coa';
        END IF;

        v_def := overlay(v_def placing '''OPENAP'', ' from v_pos + v_jv - 1 for 0);

        EXECUTE v_def;

        RAISE NOTICE 'fn_resolve_transaction_coa: OPENAP ditambahkan (COA input dipertahankan)';
    END IF;
END;
$$;


-- ---------------------------------------------------------------------------
-- 2. fn_post_accounting_transaction() : OPENAP memakai idcoa + counter_idcoa
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

    IF position('''OPENAP''' IN v_def) > 0 THEN
        RAISE NOTICE 'fn_post_accounting_transaction: OPENAP sudah terdaftar, patch dilewati';
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

        v_def := overlay(v_def placing '''OPENAP'', ' from v_pos + v_jv - 1 for 0);

        -- 2b. v_manual_toggle (membalik D/K mengikuti debet_kredit)
        v_pos := position('v_manual_toggle :=' IN v_def);

        IF v_pos = 0 THEN
            RAISE EXCEPTION 'Variabel v_manual_toggle tidak ditemukan pada fn_post_accounting_transaction';
        END IF;

        v_jv := position('''JVGENL''' IN substring(v_def FROM v_pos));

        IF v_jv = 0 THEN
            RAISE EXCEPTION 'Daftar v_manual_toggle tidak ditemukan pada fn_post_accounting_transaction';
        END IF;

        v_def := overlay(v_def placing '''OPENAP'', ' from v_pos + v_jv - 1 for 0);

        EXECUTE v_def;

        RAISE NOTICE 'fn_post_accounting_transaction: OPENAP ditambahkan (idcoa + counter_idcoa)';
    END IF;
END;
$$;

-- ---------------------------------------------------------------------------
-- 3. Mapping sc_mst.journal_type_coa untuk OPENAP
-- ---------------------------------------------------------------------------
INSERT INTO sc_mst.journal_type_coa
    (journal_type, account_role, idcoa, debit_credit, value_source, seq, active,
     description, createdby, createddate)
SELECT 'OPENAP', 'ACCOUNT', '', 'D', 'NILAI', 1, 'YES',
       'OPENAP - perkiraan dokumen (kode_prk)', 'SYSTEM', CURRENT_TIMESTAMP
 WHERE NOT EXISTS
       (SELECT 1 FROM sc_mst.journal_type_coa
         WHERE btrim(journal_type::text) = 'OPENAP' AND seq = 1);

INSERT INTO sc_mst.journal_type_coa
    (journal_type, account_role, idcoa, debit_credit, value_source, seq, active,
     description, createdby, createddate)
SELECT 'OPENAP', 'COUNTER_ACCOUNT', '', 'K', 'NILAI', 2, 'YES',
       'OPENAP - perkiraan lawan (counter COA dari form Process)', 'SYSTEM', CURRENT_TIMESTAMP
 WHERE NOT EXISTS
       (SELECT 1 FROM sc_mst.journal_type_coa
         WHERE btrim(journal_type::text) = 'OPENAP' AND seq = 2);

UPDATE sc_mst.journal_type_coa
   SET account_role = 'ACCOUNT',
       idcoa        = '',
       debit_credit = 'D',
       value_source = 'NILAI',
       active       = 'YES',
       description  = 'OPENAP - perkiraan dokumen (kode_prk)',
       updatedby    = 'SYSTEM',
       updateddate  = CURRENT_TIMESTAMP
 WHERE btrim(journal_type::text) = 'OPENAP'
   AND seq = 1;

UPDATE sc_mst.journal_type_coa
   SET account_role = 'COUNTER_ACCOUNT',
       idcoa        = '',
       debit_credit = 'K',
       value_source = 'NILAI',
       active       = 'YES',
       description  = 'OPENAP - perkiraan lawan (counter COA dari form Process)',
       updatedby    = 'SYSTEM',
       updateddate  = CURRENT_TIMESTAMP
 WHERE btrim(journal_type::text) = 'OPENAP'
   AND seq = 2;

-- ---------------------------------------------------------------------------
-- 4. Verifikasi
-- ---------------------------------------------------------------------------
DO $$
DECLARE
    v_row RECORD;
BEGIN
    RAISE NOTICE '--- Mapping OPENAP (sc_mst.journal_type_coa) ---';

    FOR v_row IN
        SELECT seq, account_role, debit_credit, value_source, active
        FROM sc_mst.journal_type_coa
        WHERE btrim(journal_type::text) = 'OPENAP'
        ORDER BY seq
    LOOP
        RAISE NOTICE 'seq=% role=% dc=% source=% active=%',
            v_row.seq, v_row.account_role, v_row.debit_credit,
            v_row.value_source, v_row.active;
    END LOOP;

    RAISE NOTICE '--- Cek patch fungsi ---';
    RAISE NOTICE 'fn_resolve_transaction_coa OPENAP : %',
        (SELECT CASE WHEN position('''OPENAP''' IN pg_get_functiondef(p.oid)) > 0
                     THEN 'OK' ELSE 'BELUM' END
           FROM pg_proc p JOIN pg_namespace n ON n.oid = p.pronamespace
          WHERE n.nspname = 'sc_trx' AND p.proname = 'fn_resolve_transaction_coa'
            AND pg_get_function_identity_arguments(p.oid) LIKE '%p_idtax%' LIMIT 1);
    RAISE NOTICE 'fn_post_accounting_transaction OPENAP : %',
        (SELECT CASE WHEN position('''OPENAP''' IN pg_get_functiondef(p.oid)) > 0
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
  source_id, source_line_id, kdsupplier, nsupplier, qty, bruto, nilai, dpp,
  total, idtax, isinclusive, currcode, kurs, idcoa, counter_idcoa,
  debet_kredit, debet, kredit, keterangan, createdby)
VALUES
 ('UJI-OPENAP-K','UJI-OPENAP-K','OAPUJI01','OPENAP','OPENAP',1,CURRENT_DATE,
  'JTS','PT JATIM TAMAN STEEL MFG','IN','X','AP-LEGACY','sc_im.import_ap',1,1,
  'S-00001','SUPPLIER',0,1000,1000,1000,1000,'NON','NO','IDR',1,
  '213201','311101','K',0,1000,'uji opening ap','TEST'),
 ('UJI-OPENAP-D','UJI-OPENAP-D','OAPUJI01','OPENAP','OPENAP',2,CURRENT_DATE,
  'JTS','PT JATIM TAMAN STEEL MFG','IN','X','AP-LEGACY','sc_im.import_ap',1,2,
  'S-00001','SUPPLIER',0,500,500,500,500,'NON','NO','IDR',1,
  '117102','311101','D',500,0,'uji opening ap','TEST');

-- Harapan:
--   baris dk=K : Dr 311101 (counter)  / Cr 213201 (kode_prk)
--   baris dk=D : Dr 117102 (kode_prk) / Cr 311101 (counter)
SELECT t.line_no, t.debet_kredit, t.idcoa, t.counter_idcoa,
       j.idcoa AS jurnal_coa, j.debet, j.kredit
FROM sc_trx.transaction_dt t
JOIN sc_trx.jurnal_dt j ON j.source_uniqueid = t.uniqueid
WHERE t.docno = 'OAPUJI01'
ORDER BY t.line_no, j.debet DESC;

ROLLBACK;
===============================================================================
*/

