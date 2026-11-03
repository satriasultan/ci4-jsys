# OPENING AR (SALDO AWAL PIUTANG CUSTOMER)

Migrasi saldo awal piutang customer dari report legacy (Excel) ke JSYS ERP
melalui staging `sc_im.import_ar` -> `sc_trx.transaction_dt` (journal_type
`OPENAR`).

## File

| File | Isi |
|------|-----|
| `Template_opening_ar.xlsx` | Template upload. Sheet yang dibaca: `template_import` (baris 1 = header, data mulai baris 2, 598 baris contoh). Sheet `umur_piutang` adalah sumber referensi, sheet `petunjuk` hanya informasi. |
| `opening_ar.sql` | Skema staging (`sc_im.import_ar_batch`, `sc_im.import_ar`), fungsi `sc_im.fn_validate_import_ar()` dan `sc_im.fn_process_import_ar()`, view `sc_im.v_import_ar_monitor`. |
| `opening_ar_accounting_fix.sql` | Perbaikan routing accounting `OPENAR`: (a) membersihkan `OPENAR` dari daftar manual `fn_prepare_transaction_dt()` bila script versi awal pernah dijalankan, (b) COA baris (`kode_prk`) tidak ditimpa COA konfigurasi, (c) `counter_idcoa` dari form dipakai sebagai perkiraan lawan. Wajib dijalankan; tanpa ini kedua sisi jurnal memakai COA yang sama. |
| `trx_hd_recalculate_perf.sql` | (Disarankan) `sc_trx.fn_recalculate_transaction_hd()` versi index-friendly. Sama dengan file di folder `AP`; cukup dijalankan sekali per database. |
| `menu_upload_saldo_awal_ar.sql` | Menu `I.T.D.6 - UPLOAD SALDO AR` + hak akses role (menyalin dari I.T.D.5, fallback ADMTEK). |

## Urutan deploy

```sql
-- 1. skema + fungsi staging
\i opening_ar.sql
-- 2. routing accounting OPENAR
\i opening_ar_accounting_fix.sql
-- 3. performa (opsional, sangat disarankan untuk data > 1.000 baris)
\i trx_hd_recalculate_perf.sql
-- 4. menu aplikasi
\i menu_upload_saldo_awal_ar.sql
```

Semua skrip idempoten (aman dijalankan berulang).

## Alur pemakaian (menu I.T.D.6)

1. Buka `I.T.` -> `IMPORT / EXPORT` -> `UPLOAD SALDO AR`
   (`tools/proses/uploadSaldoAwalAR`).
2. Upload file Excel + isi **Tanggal Saldo** (dipakai bila kolom `tanggal_saldo`
   di file kosong). Upload langsung menjalankan validasi.
3. Periksa panel **Baris ERROR / Warning**:
   - `ERROR` = baris tidak akan diposting (mis. `dk` kosong, COA tidak ada,
     nilai 0, tanggal dokumen kosong, kode customer kosong).
   - `warning` = baris tetap diposting (mis. customer belum ada di master
     `sc_mst.customer`, mata uang kosong -> IDR, kurs kosong -> 1, umur hari
     dihitung dari tanggal).
4. Isi **Counter COA** (perkiraan lawan, mis. akun saldo awal/ekuitas) lalu
   tekan **Proses ke Transaksi**.
5. Status batch: `STAGED` -> `VALIDATED` -> `PROCESSED`
   (atau `PARTIAL` bila sebagian baris masih ERROR, `VALIDATION_ERROR` bila
   tidak ada baris yang bisa diproses).

## Hasil posting

Satu baris staging = satu dokumen (`docno = OAR<batch_id>-<row_no>`,
`ref_docno = OAR<batch_id>`), dengan jurnal:

| `dk` file | Debet | Kredit |
|-----------|-------|--------|
| `D` | `kode_prk` | `counter_idcoa` |
| `K` | `counter_idcoa` | `kode_prk` |

Nilai di file disimpan apa adanya (`DK=D` positif, `DK=K` negatif). Posting
memakai `ABS(nilai)` dan arah mengikuti kolom `dk`, sehingga jurnal tetap
seimbang per dokumen.

## Catatan data (template contoh)

- `tanggal_saldo` seluruh baris = 31/10/2026 (serial `46326` di Excel).
- 598 baris contoh: hasil uji kering = **598 READY, 0 ERROR, 25 warning**,
  posting 598 dokumen dalam ~2 detik (termasuk pembentukan 1.196 baris
  `jurnal_dt`, Dr = Cr).
- Warning muncul pada baris yang kolom `umur_hari`-nya 0; nilainya dihitung
  otomatis dari `tanggal_saldo - tanggal_jatuh_tempo`.
- Tidak ada kode customer yang hilang pada template contoh (semua ada di
  `sc_mst.customer`).
