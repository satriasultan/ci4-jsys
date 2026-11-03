# OPENING AP (SALDO AWAL HUTANG SUPPLIER)

Migrasi saldo awal hutang supplier dari report legacy (Excel) ke JSYS ERP
melalui staging `sc_im.import_ap` -> `sc_trx.transaction_dt` (journal_type
`OPENAP`).

## File

| File | Isi |
|------|-----|
| `Template_Opening_AP_Staging.xlsx` | Template upload. Sheet yang dibaca: `template_import` (baris 1 = header, data mulai baris 2). Sheet `referensi_umur_hutang` dan `petunjuk` hanya informasi. |
| `opening_ap.sql` | Skema staging (`sc_im.import_ap_batch`, `sc_im.import_ap`), fungsi `sc_im.fn_validate_import_ap()` dan `sc_im.fn_process_import_ap()`, view `sc_im.v_import_ap_monitor`. |
| `opening_ap_accounting_fix.sql` | Perbaikan routing accounting `OPENAP`: COA baris (`kode_prk`) tidak lagi ditimpa COA konfigurasi, dan `counter_idcoa` dari form dipakai sebagai perkiraan lawan. |
| `trx_hd_recalculate_perf.sql` | (Disarankan) `sc_trx.fn_recalculate_transaction_hd()` versi index-friendly. Tanpa ini, import besar menjadi O(n^2) karena full scan `transaction_dt`/`transaction_hd` setiap baris. |
| `menu_upload_saldo_awal_ap.sql` | Menu `I.T.D.5 - UPLOAD SALDO AP` + hak akses role. |

## Urutan deploy

```sql
-- 1. skema + fungsi staging
\i opening_ap.sql
-- 2. routing accounting OPENAP
\i opening_ap_accounting_fix.sql
-- 3. performa (opsional, sangat disarankan untuk data > 1.000 baris)
\i trx_hd_recalculate_perf.sql
-- 4. menu aplikasi
\i menu_upload_saldo_awal_ap.sql
```

Semua skrip idempoten (aman dijalankan berulang).

## Alur pemakaian (menu I.T.D.5)

1. Buka `I.T.` -> `IMPORT / EXPORT` -> `UPLOAD SALDO AP`
   (`tools/proses/uploadSaldoAwalAP`).
2. Upload file Excel + isi **Tanggal Saldo** (dipakai bila kolom `tanggal_saldo`
   di file kosong). Upload langsung menjalankan validasi.
3. Periksa panel **Baris ERROR / Warning**:
   - `ERROR` = baris tidak akan diposting (mis. `dk` kosong, COA tidak ada,
     nilai 0, tanggal dokumen kosong).
   - `warning` = baris tetap diposting (mis. supplier belum ada di master,
     mata uang kosong -> IDR, kurs kosong -> 1).
4. Isi **Counter COA** (perkiraan lawan, mis. akun saldo awal/ekuitas) lalu
   tekan **Proses ke Transaksi**.
5. Status batch: `STAGED` -> `VALIDATED` -> `PROCESSED`
   (atau `PARTIAL` bila sebagian baris masih ERROR, `VALIDATION_ERROR` bila
   tidak ada baris yang bisa diproses).

## Hasil posting

Satu baris staging = satu dokumen (`docno = OAP<batch_id>-<row_no>`,
`ref_docno = OAP<batch_id>`), dengan jurnal:

| `dk` file | Debet | Kredit |
|-----------|-------|--------|
| `D` | `kode_prk` | `counter_idcoa` |
| `K` | `counter_idcoa` | `kode_prk` |

## Catatan data (template contoh)

- `tanggal_saldo` pada file contoh kosong untuk semua baris -> wajib diisi dari
  form upload (atau isi kolomnya di Excel).
- 4 baris contoh tidak memiliki `dk`/`mata_uang` -> akan berstatus ERROR sampai
  diperbaiki di file sumber.
- Supplier `S-19110`, `S-19111`, `S-19112` belum ada di master supplier ->
  hanya warning.
