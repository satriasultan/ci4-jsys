# PR SIMBAL — Average Cost / HPP SALESX

> **PR = Problem Report** (bukan Pull Request).
> Dokumen ini adalah bahan perbaikan untuk pemilik domain **SIMBAL BALANCE**.
> **Bukan patch SQL** dan **tidak boleh** dianggap sebagai instruksi eksekusi database.

| Item | Nilai |
|---|---|
| Status | `OPEN — menunggu perbaikan SIMBAL` |
| Tanggal temuan | 2026-11-24 |
| Environment | `JACC_LOCAL_NEW` @ `localhost:5433` |
| Dokumen bukti | `PJO/2611/000001` |
| Modul terdampak | Penjualan (`SALESX`), Persediaan, Jurnal COGS |
| Referensi | `TASK_HANDOVER.md` §3.2 **S2** dan §10 |

---

## 1. Ringkasan eksekutif

HPP pada `PJO/2611/000001` muncul sebesar:

```text
COGS 5511 (Debit)        = 165.177.339,6000
INVENTORY 115103 (Kredit) = 165.177.339,6000
```

Nominal tersebut berasal dari:

```text
6.543,21 KG × 25.244,0835 = 165.177.339,598035
→ dibulatkan 2 desimal = 165.177.339,60
```

**Masalahnya:** average cost `25.244,0835` sudah terinflasi. Jika average cost benar
`13.001,56`, HPP seharusnya:

```text
6.543,21 × 13.001,56 = 85.071.937,4076
```

Selisih HPP overstated:

```text
165.177.339,6000 - 85.071.937,4076 = 80.105.402,1924
```

HPP aktual bahkan lebih besar dari nilai penjualan (DPP `85.071.937,4076`), sehingga
jurnal menunjukkan kerugian besar padahal harga jual per KG sama dengan harga pokok.

---

## 2. Bukti data `PJO/2611/000001`

### 2.1 Header penjualan (`sc_trx.penjualan`)

| Field | Nilai |
|---|---:|
| `docno` | `PJO/2611/000001` |
| `docdate` | 2026-11-24 |
| `cabang` | `01` |
| `doctype` | `SALES` |
| `idtax` | `PPN11` |
| `isinclusive` | `NO` |
| `currcode` / `kurs` | `IDR` / 1,0000 |
| `dpp` | 85.071.937,4076 |
| `jumlahpajak` | 9.357.913,1148 |
| `total` | 94.429.850,5224 |

### 2.2 Detail penjualan (`sc_trx.penjualan_dtl`)

| Field | Nilai |
|---|---:|
| `idurut` | 3 |
| `uniqueid` | `62d3062c5ef9a963f315579ee476f1d516fc0758886ff40d91b0105372c856ed` |
| `idbarang` | `CD750026000000A` |
| `nmbarang` | FLATBAR SUP9A 75.00 X 26.00 X 00.00 |
| `unit` | `KG` |
| `qty` | 6.543,2100 |
| `harga` | 13.001,5600 |
| `nilai` / `nilaikonversi` | 85.071.937,4076 |
| `nilaipajak` | 9.357.913,1148 |
| `multidisc` | 0,0000 |
| `idgudang` | `16501` |
| `idspec` | kosong |
| `idtax` | `PPN11` |
| `docnoso` | `SOF/2611/000001` |

### 2.3 `sc_trx.transaction_dt`

| Journal | Uniqueid | Qty | Harga | Nilai | Batch | Warehouse |
|---|---|---:|---:|---:|---|---|
| `DELIVR` | `eb9c8afcc2c4bdb3ac576c4cdd010db2` | 6.543,2100 | 0 | 0 | kosong | `16501` |
| `SALESX` | `66c9caf25de6d0532d2f21d033b8cc15` | 6.543,2100 | 13.001,5600 | 94.429.850,5224 | kosong | `16501` |

Kedua baris memakai `source_uniqueid` yang sama:
`62d3062c5ef9a963f315579ee476f1d516fc0758886ff40d91b0105372c856ed`.

Baris `DELIVR` adalah sumber stok OUT. Baris `SALESX` adalah sumber jurnal.

### 2.4 `sc_trx.stkblc` (baris stok OUT)

| Field | Nilai |
|---|---:|
| `id` | 91 |
| `uniqueid` | `eb9c8afcc2c4bdb3ac576c4cdd010db2` |
| `source_uniqueid` | `62d3062c5ef9a963f315579ee476f1d516fc0758886ff40d91b0105372c856ed` |
| `journal_type` | `DELIVR` |
| `type_in_out` | `OUT` |
| `qty_out` | 6.543,2100 |
| `unitcost` | **25.244,0835** |
| `totalcost` | **165.177.339,6000** |
| `qty_sld` | 0,00 |
| `batch` | kosong |
| `idlocation` / `idarea` | `16501` / `16501` |
| `docno` / `docref` | `PJO/2611/000001` / `SOF/2611/000001` |

### 2.5 `sc_trx.jurnal_hd` / `jurnal_dt`

Header jurnal:

| Field | Nilai |
|---|---:|
| `id` | 101 |
| `journal_type` | `SALESX` |
| `status` | `POSTED` |
| `total_debet` | 259.607.190,1200 |
| `total_kredit` | 259.607.190,1200 |
| `balance` | 0,0000 |

Detail jurnal:

| Seq | Role | COA | Debit | Kredit |
|---:|---|---|---:|---:|
| 1 | AR | `113101` | 94.429.850,5224 | 0 |
| 2 | SALES | `411101` | 0 | 85.071.937,4076 |
| 3 | TAX | `214116` | 0 | 9.357.913,1148 |
| 4 | **COGS / HPP** | **`5511`** | **165.177.339,6000** | 0 |
| 5 | INVENTORY | `115103` | 0 | 165.177.339,6000 |

Jumlah detail:

```text
Debit  = 94.429.850,5224 + 165.177.339,6000 = 259.607.190,1224
Kredit = 85.071.937,4076 + 9.357.913,1148 + 165.177.339,6000
       = 259.607.190,1224
Header = 259.607.190,1200
Selisih header vs detail = 0,0024 (pembulatan 2 desimal lama)
```

---

## 3. Riwayat stok item `CD750026000000A` di gudang `16501`

Batch kosong (`''`).

| id | Journal | Tipe | Qty | Pricelst in | Unitcost | Totalcost | Docno |
|---:|---|---|---:|---:|---:|---:|---|
| 69 | `WORECV` | IN | 20.620,0000 | 13.001,5600 | 13.001,5635 | 268.092.239,3700 | `PR4/2610/PT0001` |
| 88 | `DELIVR` | OUT | 10.000,0000 | 0 | 13.001,5600 | 130.015.600,0000 | `INV/2610/PT0001` |
| 91 | `DELIVR` | OUT | 6.543,2100 | 0 | **25.244,0835** | **165.177.339,6000** | `PJO/2611/000001` |

> Catatan: `sp_rebuild_avgcost` memakai `qty_in × pricelst_in`, bukan `totalcost` penerimaan.
> Nilai awal yang dipakai = `20.620 × 13.001,56 = 268.092.167,20`.

### 3.1 `stkblc_avgcost` saat ini

| Key | Qty | Total value | Avg cost | Updated |
|---|---:|---:|---:|---|
| `CD750026000000A` / `16501` / `''` | 4.076,7900 | 268.092.167,2000 | **65.760,6026** | 2026-11-24 15:38:43.130555 |
| `CD750026000000A` / `16501` / `'NULL'` | 0,0000 | 0,0000 | 0,0000 | 2026-10-31 16:21:10.022591 |

Baris batch `'NULL'` adalah data lama; patch penjualan baru menormalkan batch menjadi `''`,
tetapi baris lama ini tetap ada dan perlu dibersihkan/diabaikan oleh perbaikan SIMBAL.

---

## 4. Perhitungan lengkap

### 4.1 Kondisi awal

```text
Qty awal        = 20.620 KG
Harga pokok in  = 13.001,56 / KG
Total value     = 20.620 × 13.001,56
                = 268.092.167,20
Avg cost        = 13.001,56
```

### 4.2 Penjualan pertama `INV/2610/PT0001`

```text
Qty OUT         = 10.000 KG
HPP seharusnya  = 10.000 × 13.001,56
                = 130.015.600,00   ← ini yang terposting & benar

Sisa qty        = 10.620 KG
Sisa value      = 268.092.167,20 - 130.015.600,00
                = 138.076.567,20
Avg seharusnya  = 138.076.567,20 / 10.620
                = 13.001,56
```

### 4.3 Kondisi average cost setelah penjualan pertama

Yang terjadi justru:

```text
Qty             = 10.620 KG
Total value     = 268.092.167,20   ← tidak dikurangi HPP
Avg cost        = 268.092.167,20 / 10.620
                = 25.244,0835
```

Nilai `25.244,0835` inilah yang dipakai untuk penjualan kedua.

### 4.4 Penjualan kedua `PJO/2611/000001`

```text
Qty OUT         = 6.543,21 KG
Avg cost dipakai= 25.244,0835

HPP             = 6.543,21 × 25.244,0835
                = 165.177.339,598035
                → ROUND(...,2) = 165.177.339,60
```

### 4.5 Kondisi average cost setelah penjualan kedua

Yang tersimpan saat ini:

```text
Qty             = 20.620 - 10.000 - 6.543,21
                = 4.076,79 KG
Total value     = 268.092.167,20   ← masih tidak dikurangi
Avg cost        = 268.092.167,20 / 4.076,79
                = 65.760,6026
```

**Risiko:** penjualan berikutnya untuk item/gudang yang sama akan memakai average cost
`65.760,6026`, sehingga HPP bisa jauh lebih besar lagi.

### 4.6 Perbandingan HPP

| Skenario | Perhitungan | HPP |
|---|---|---:|
| Seharusnya (avg 13.001,56) | 6.543,21 × 13.001,56 | **85.071.937,4076** |
| Aktual (avg 25.244,0835) | 6.543,21 × 25.244,0835 | **165.177.339,6000** |
| Overstatement | selisih | **80.105.402,1924** |

---

## 5. Akar masalah teknis

### 5.1 Trigger average cost yang aktif pada `sc_trx.stkblc`

| Trigger | Fungsi | Perilaku |
|---|---|---|
| `trg_avgcost_stkblc` | `fn_update_avgcost` | Incremental; mengurangi nilai OUT dengan average cost |
| `trg_rebuild_avgcost` | `tr_rebuild_avgcost` → `sp_rebuild_avgcost` | **Mengabaikan nilai OUT** |
| `trg_stkblc_avgcost` | `fn_stkblc_avgcost_trigger` → `fn_recalculate_avgcost_group` | Secara teori benar, tetapi **di-skip saat nested trigger** |

Ketiganya `AFTER INSERT OR DELETE OR UPDATE` (kecuali `trg_avgcost_stkblc` khusus INSERT),
dan semuanya aktif (`tgenabled = 'O'`).

### 5.2 `sp_rebuild_avgcost` salah menghitung nilai persediaan

Kode inti:

```sql
SELECT
    SUM(qty_in - qty_out),
    SUM(qty_in * pricelst_in)
INTO v_qty, v_total
FROM sc_trx.stkblc
WHERE idbarang = p_idbarang
  AND idlocation = p_idlocation
  AND batch = p_batch;
```

Fungsi ini:

- mengurangi **qty** dengan `qty_out`;
- tetapi **tidak mengurangi `total_value`** dengan `qty_out × avg_cost`.

Akibatnya setiap OUT membuat average cost naik:

```text
OUT 10.000 → qty turun, value tetap → avg naik
OUT 6.543,21 → qty turun, value tetap → avg naik lagi
```

### 5.3 `fn_stkblc_avgcost_trigger` melewati recalculate yang benar

Guard:

```sql
IF pg_trigger_depth() > 1 THEN
    RETURN NEW;
END IF;
```

Insert `stkblc` dari penjualan tidak terjadi langsung, melainkan:

```text
INSERT transaction_dt
  → trg_transaction_stock (depth 1)
    → fn_post_stkblc_from_transaction
      → INSERT stkblc
        → trigger stkblc berjalan pada depth 2
```

Pada depth 2, `fn_stkblc_avgcost_trigger` langsung `RETURN NEW`, sehingga
`fn_recalculate_avgcost_group` — fungsi yang seharusnya menghitung ulang average cost
secara kronologis dan mengurangi nilai OUT — **tidak pernah dijalankan**.

### 5.4 Urutan trigger memperburuk keadaan

Urutan nama trigger:

```text
trg_avgcost_stkblc
trg_rebuild_avgcost
trg_stkblc_avgcost
```

Perkiraan alur saat `stkblc` OUT di-insert:

1. `fn_update_avgcost` menghitung incremental dengan benar.
2. `sp_rebuild_avgcost` menimpa hasilnya dengan rumus yang tidak mengurangi nilai OUT.
3. `fn_recalculate_avgcost_group` seharusnya mengoreksi, tetapi di-skip oleh guard depth.

Hasil akhir = nilai dari `sp_rebuild_avgcost`, yaitu average cost yang terinflasi.

### 5.5 Pembulatan 2 desimal

- `sp_rebuild_avgcost` menyimpan `avg_cost` hasil pembagian tanpa pembulatan eksplisit.
- `fn_recalculate_avgcost_group` memakai `ROUND(...,2)` untuk `v_cost_out`.
- `fn_post_stkblc_from_transaction` versi terpasang memakai `v_totalcost NUMERIC(18,2)`
  dan `ROUND(...,2)`.
- `stkblc.unitcost` saat ini 18,4, sehingga `25.244,0835` hanya 4 desimal tampilan.

Patch 4 desimal akan mengubah presisi, tetapi **tidak memperbaiki nilai average cost**.


---

## 6. Dampak

### 6.1 Finansial / jurnal

- COGS overstated `80.105.402,1924` untuk dokumen ini.
- Persediaan overstated karena `total_value` tidak berkurang saat OUT.
- Gross profit penjualan terlihat negatif meskipun harga jual = harga pokok.
- Penjualan berikutnya berpotensi memakai average cost `65.760,6026` (lebih buruk).
- `jurnal_hd` memakai 2 desimal: `259.607.190,12` vs detail `259.607.190,1224`.

### 6.2 Operasional

- HPP tidak dapat dijadikan dasar harga jual / margin.
- Laporan persediaan dan COGS tidak dapat direkonsiliasi dengan GL.
- Setiap penjualan baru akan memperbesar inflasi average cost jika mesin belum diperbaiki.

### 6.3 Data terdampak

Minimal:

- `PJO/2611/000001` — HPP 165.177.339,60 (seharusnya 85.071.937,4076).
- `INV/2610/PT0001` — HPP 130.015.600,00 (benar), tetapi menjadi pemicu inflasi
  average cost setelahnya.
- Item `CD750026000000A`, gudang `16501`, batch kosong.
- Dokumen lain yang memakai item/gudang/batch sama setelah `INV/2610/PT0001`
  perlu diaudit.

---

## 7. Opsi perbaikan (untuk pemilik SIMBAL)

### Opsi A — Perbaiki `sp_rebuild_avgcost`

Ubah `sp_rebuild_avgcost` agar menghitung nilai persediaan secara kronologis:

- IN: `value += qty_in × pricelst_in` (atau `totalcost` untuk transfer/OPENST).
- OUT: `value -= qty_out × avg_cost_sebelum_out`.
- Setelah OUT: `avg_cost = value / qty`.

Catatan: tidak bisa hanya `SUM(qty_in × pricelst_in) - SUM(qty_out × harga_jual)`;
harus memakai average cost berjalan.

### Opsi B — Jadikan `fn_recalculate_avgcost_group` satu-satunya recalculator

- Nonaktifkan `trg_rebuild_avgcost` dan `trg_avgcost_stkblc`.
- Pertahankan `trg_stkblc_avgcost` → `fn_recalculate_avgcost_group`.
- Pastikan fungsi ini selalu terpanggil, termasuk saat insert `stkblc` berasal dari
  trigger `transaction_dt`.

### Opsi C — Perbaiki guard anti-recursion

Jangan memakai `pg_trigger_depth() > 1` sebagai satu-satunya penanda, karena insert
normal dari `transaction_dt` juga berada di depth 2.

Gunakan salah satu:

- custom GUC, misalnya `current_setting('app.avgcost_recalc', true)`;
- `session_replication_role` sementara;
- flag tabel/ advisory lock per `idbarang + idlocation + batch`;
- `pg_trigger_depth()` hanya untuk trigger yang benar-benar rekursif.

### Opsi D — Pastikan hanya satu mesin average cost

Saat ini ada tiga trigger yang menulis `stkblc_avgcost`. Pilih satu mekanisme resmi,
lalu nonaktifkan sisanya agar tidak saling menimpa.

### Opsi E — 4 desimal end-to-end

- `fn_recalculate_avgcost_group`: `v_cost_out` dan `totalcost` → `ROUND(...,4)`.
- `fn_post_stkblc_from_transaction`: `v_totalcost`, `v_out_cost` → 18,4.
- `fn_sync_jurnal_hd`: total/balance → 18,4.
- Patch `PATCH_numeric_4digit_functions.sql` sudah menyiapkan ini,
  tetapi **bukan** perbaikan nilai average cost.

### Opsi F — Rebuild & audit data

Setelah mesin diperbaiki:

1. Backup database.
2. Rebuild `stkblc_avgcost` untuk item/gudang/batch terdampak.
3. Audit dokumen yang sudah memakai average cost inflasi.
4. Repost dokumen bila perlu (dengan persetujuan bisnis).
5. Rekonsiliasi `Σ stkblc` vs akun persediaan.

---

## 8. Acceptance criteria

Perbaikan dinyatakan berhasil jika:

- [ ] Setelah OUT, `stkblc_avgcost.total_value` berkurang sebesar HPP.
- [ ] Average cost item `CD750026000000A` / `16501` / `''` tetap `13.001,56`
      pada contoh `INV/2610/PT0001` → `PJO/2611/000001`.
- [ ] HPP `PJO/2611/000001` menjadi `85.071.937,4076` (bukan 165.177.339,60).
- [ ] `COGS 5511 = INVENTORY 115103 = stkblc.totalcost`.
- [ ] `jurnal_hd.balance = 0,0000` pada 4 desimal.
- [ ] `SUM(debet) = SUM(kredit)` detail jurnal tepat sampai 4 desimal.
- [ ] Penjualan berikutnya tidak memakai average cost `65.760,6026`.
- [ ] Tidak ada double posting / double stock OUT.
- [ ] Trigger average cost tidak rekursif dan tidak saling menimpa.

---

## 9. Query audit (read-only)

### 9.1 Kartu stok

```sql
SELECT id, trxdate, journal_type, type_in_out,
       qty_in, qty_out, pricelst_in, unitcost, totalcost,
       qty_sld, batch, docno, docref
FROM sc_trx.stkblc
WHERE BTRIM(idbarang) = 'CD750026000000A'
  AND BTRIM(idlocation) = '16501'
  AND BTRIM(COALESCE(batch, '')) = ''
ORDER BY id;
```

### 9.2 Average cost

```sql
SELECT idbarang, idlocation, batch,
       qty, total_value, avg_cost, updated_at
FROM sc_trx.stkblc_avgcost
WHERE BTRIM(idbarang) = 'CD750026000000A'
ORDER BY idlocation, batch;
```

### 9.3 Transaksi penjualan

```sql
SELECT uniqueid, source_uniqueid, journal_type, type_in_out,
       qty, harga, nilai, dpp, pajak, total, batch, warehouse
FROM sc_trx.transaction_dt
WHERE BTRIM(docno) = 'PJO/2611/000001'
ORDER BY journal_type;
```

### 9.4 Jurnal

```sql
SELECT jh.id, jh.docno, jh.status, jh.total_debet, jh.total_kredit, jh.balance,
       jd.seq, jd.account_role, jd.idcoa, jd.debet, jd.kredit
FROM sc_trx.jurnal_hd jh
JOIN sc_trx.jurnal_dt jd ON jd.jurnal_id = jh.id
WHERE BTRIM(jh.docno) = 'PJO/2611/000001'
ORDER BY jh.id, jd.seq;
```

### 9.5 Trigger average cost

```sql
SELECT c.relname, t.tgname, p.proname, t.tgenabled,
       pg_get_triggerdef(t.oid) AS trigger_def
FROM pg_trigger t
JOIN pg_class c ON c.oid = t.tgrelid
JOIN pg_namespace n ON n.oid = c.relnamespace
JOIN pg_proc p ON p.oid = t.tgfoid
WHERE n.nspname = 'sc_trx'
  AND c.relname = 'stkblc'
  AND NOT t.tgisinternal
ORDER BY t.tgname;
```

---

## 10. Hubungan dengan patch COGS 4 desimal

Patch yang sudah disiapkan:

| File | Hubungan dengan PR ini |
|---|---|
| `DatabaseChanges/SIMULASI BALANCE/PATCH/PATCH_numeric_4digit_functions.sql` | Mengubah presisi stok/jurnal menjadi 4 desimal. **Tidak memperbaiki nilai average cost.** |
| `DatabaseChanges/SIMULASI BALANCE/PATCH/PATCH_salesx_cogs_simbal.sql` | Cost fallback membaca `stkblc.totalcost`. Jika `stkblc.totalcost` salah, COGS tetap salah meskipun 4 desimal. |
| `DatabaseChanges/PENJUALAN/penjualan/PATCH_salesx_cogs.sql` | Menormalkan batch dan memastikan DELIVR + SALESX terbentuk. Tidak memperbaiki average cost. |
| `DatabaseChanges/PENJUALAN/penjualan/PATCH_salesx_cogs_repair.sql` | **JANGAN dijalankan sebelum average cost diperbaiki.** |

### ⚠️ Peringatan repair historis

Saat ini `stkblc_avgcost.avg_cost` untuk `CD750026000000A` / `16501` / `''`
adalah **65.760,6026**.

Repair patch melakukan UPDATE pada baris `DELIVR`. Trigger SIMBAL akan:

1. menghapus stok OUT lama;
2. memposting ulang stok OUT;
3. membaca `stkblc_avgcost.avg_cost` **yang sekarang** (65.760,6026).

Akibatnya:

```text
INV/2610/PT0001 (10.000 KG):
10.000 × 65.760,6026 = 657.606.026,00

PJO/2611/000001 (6.543,21 KG):
6.543,21 × 65.760,6026 ≈ 430.285.432,54
```

Jadi repair sebelum perbaikan average cost akan **memperburuk** COGS, bukan memperbaikinya.

Urutan yang benar:

```text
1. Perbaiki mesin average cost (SIMBAL)
2. Rebuild stkblc_avgcost
3. Verifikasi average cost = 13.001,56
4. Baru jalankan patch 4 desimal / repair
5. Audit & repost dokumen terdampak
```

---

## 11. Checklist pekerjaan berikutnya

### Tahap 1 — Perbaikan SIMBAL (pemilik domain)

- [ ] Pilih strategi: Opsi A, B, atau C di §7.
- [ ] Backup database.
- [ ] Perbaiki `sp_rebuild_avgcost` / nonaktifkan `trg_rebuild_avgcost`.
- [ ] Perbaiki guard `fn_stkblc_avgcost_trigger`.
- [ ] Pastikan hanya satu mesin average cost yang menulis `stkblc_avgcost`.
- [ ] Uji di lingkungan simulasi / backup.

### Tahap 2 — Rebuild & validasi

- [ ] Rebuild `stkblc_avgcost` untuk `CD750026000000A` / `16501` / `''`.
- [ ] Verifikasi avg cost = `13.001,56`.
- [ ] Verifikasi HPP `INV/2610/PT0001` = `130.015.600,00`.
- [ ] Verifikasi HPP `PJO/2611/000001` = `85.071.937,4076`.
- [ ] Verifikasi nilai persediaan sisa = `53.004.629,7924` (qty 4.076,79).

### Tahap 3 — Audit dokumen

- [ ] Cari semua `stkblc` OUT untuk item/gudang/batch tersebut.
- [ ] Bandingkan `totalcost` vs `qty × avg_cost` yang benar.
- [ ] Daftar dokumen yang perlu repost.
- [ ] Dapatkan persetujuan bisnis sebelum repost.

### Tahap 4 — Setelah average cost benar

- [ ] Jalankan `PATCH_numeric_4digit_functions.sql`.
- [ ] Jalankan `PATCH_salesx_cogs_simbal.sql`.
- [ ] Jalankan `PATCH_salesx_cogs.sql`.
- [ ] Jalankan repair hanya untuk dokumen yang sudah disetujui.
- [ ] Verifikasi 5 baris jurnal, COGS = INVENTORY, balance 0,0000.

---

## 12. Catatan koordinasi

- **Domain SIMBAL:** `DatabaseChanges/SIMULASI BALANCE/**` adalah milik rekan SIMBAL.
  PR ini hanya laporan; jangan mengubah trigger/fungsi SIMBAL tanpa koordinasi.
- **Modul Penjualan:** perbaikan menu penjualan lain (SO/DO/SJ/Penjualan) boleh lanjut,
  tetapi **jangan** menjalankan repair COGS historis sebelum average cost diperbaiki.
- **Patch 4 desimal:** aman untuk presisi, bukan solusi nilai average cost.
- **Tidak ada perubahan database** yang dilakukan saat PR ini dibuat. Semua angka
  berasal dari query `SELECT` read-only.
- **File terkait:**
  - `TASK_HANDOVER.md` — §3.2 S2, §10 (Modul Penjualan)
  - `PATCH_numeric_4digit_functions.sql`
  - `PATCH_salesx_cogs_simbal.sql`
  - `PATCH_salesx_cogs.sql`
  - `PATCH_salesx_cogs_repair.sql`

### Status log

| Tanggal | Status | Catatan |
|---|---|---|
| 2026-11-24 | `OPEN` | Temuan HPP `PJO/2611/000001` = 165.177.339,60; average cost terinflasi |
| — | `WAITING SIMBAL OWNER` | Menunggu perbaikan mesin average cost & rebuild |
| — | `READY FOR REPOST` | Setelah avg cost benar dan disetujui bisnis |

---

> **Kesimpulan:** HPP `PJO/2611/000001` sebesar **165.177.339,60** bukan berasal dari
> harga jual atau pajak, melainkan dari **average cost 25.244,0835** yang sudah
> terinflasi. Inflasi terjadi karena `total_value` di `stkblc_avgcost` tidak dikurangi
> saat stok OUT, diperparah oleh `sp_rebuild_avgcost` dan guard `pg_trigger_depth()`
> yang melewati `fn_recalculate_avgcost_group`. Perbaikan harus dimulai dari mesin
> average cost SIMBAL, bukan dari patch COGS 4 desimal.


