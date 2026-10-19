-- ============================================================
-- TEST LENGKAP: INSERT JURNAL PENJUALAN & BEBAN → LAPORAN LABA RUGI
-- Header dibuat DRAFT dulu, detail diinsert, baru di-POSTED.
-- ============================================================

DO $$
DECLARE
    v_id_sales  BIGINT;
    v_id_beban  BIGINT;
    v_uid_sales TEXT;
    v_uid_beban TEXT;
BEGIN

    -- ============================================
    -- 1. INSERT HEADER JURNAL PENJUALAN (status DRAFT)
    -- ============================================
    v_uid_sales := md5(random()::text);

    INSERT INTO sc_trx.jurnal_hd (
        uniqueid, source_uniqueid,
        docno, doctype, journal_type, trxdate, type_in_out,
        idbranch, cabang, module, accounting_effect,
        currcode, kurs, status, keterangan, createdby, createddate
    ) VALUES (
        v_uid_sales, 'TESTSALES001',
        'TST/2610/PA0001', 'TST', 'SALESX', '2026-10-15', 'OUT',
        '01.01', 'PT JATIM TAMAN STEEL MFG', 'SALES', 'YES',
        'IDR', 1, 'DRAFT', 'TEST PENJUALAN', 'FIKY', NOW()
    ) RETURNING id INTO v_id_sales;

    -- ============================================
    -- 2. INSERT DETAIL JURNAL PENJUALAN (header masih DRAFT)
    -- ============================================
    INSERT INTO sc_trx.jurnal_dt (
        jurnal_id, journal_type, status, source_uniqueid, seq,
        account_role, value_source, idcoa,
        debet, kredit,
        ref_docno, ref_doctype, keterangan, currcode, kurs, createdby
    ) VALUES 
    (v_id_sales, 'SALESX', 'DRAFT', 'TESTSALES001', 1,
     'AR', 'TOTAL', '113101', 1110000, 0,
     'TST/2610/PA0001', 'TST', 'PIUTANG', 'IDR', 1, 'FIKY'),

    (v_id_sales, 'SALESX', 'DRAFT', 'TESTSALES001', 2,
     'SALES', 'DPP', '411101', 0, 1000000,
     'TST/2610/PA0001', 'TST', 'PENDAPATAN', 'IDR', 1, 'FIKY'),

    (v_id_sales, 'SALESX', 'DRAFT', 'TESTSALES001', 3,
     'TAX', 'PAJAK', '214113', 0, 110000,
     'TST/2610/PA0001', 'TST', 'PPN KELUARAN', 'IDR', 1, 'FIKY');

    -- ============================================
    -- 3. UPDATE STATUS HEADER JURNAL PENJUALAN → POSTED
    -- (Trigger fn_sync_jurnal_dt_status akan otomatis ubah detail ke POSTED)
    -- ============================================
    UPDATE sc_trx.jurnal_hd 
    SET status = 'POSTED', updateddate = NOW()
    WHERE id = v_id_sales;


    -- ============================================
    -- 4. INSERT HEADER JURNAL BEBAN (status DRAFT)
    -- ============================================
    v_uid_beban := md5(random()::text);

    INSERT INTO sc_trx.jurnal_hd (
        uniqueid, source_uniqueid,
        docno, doctype, journal_type, trxdate, type_in_out,
        idbranch, cabang, module, accounting_effect,
        currcode, kurs, status, keterangan, createdby, createddate
    ) VALUES (
        v_uid_beban, 'TESTBEBAN001',
        'TST/2610/PA0002', 'TST', 'JVGENL', '2026-10-20', 'IN',
        '01.01', 'PT JATIM TAMAN STEEL MFG', 'ACCOUNTING', 'YES',
        'IDR', 1, 'DRAFT', 'TEST BEBAN LISTRIK', 'FIKY', NOW()
    ) RETURNING id INTO v_id_beban;

    -- ============================================
    -- 5. INSERT DETAIL JURNAL BEBAN
    -- ============================================
    INSERT INTO sc_trx.jurnal_dt (
        jurnal_id, journal_type, status, source_uniqueid, seq,
        account_role, value_source, idcoa,
        debet, kredit,
        ref_docno, ref_doctype, keterangan, currcode, kurs, createdby
    ) VALUES 
    (v_id_beban, 'JVGENL', 'DRAFT', 'TESTBEBAN001', 1,
     'ACCOUNT', 'NILAI', '611192.02.710', 5000000, 0,
     'TST/2610/PA0002', 'TST', 'BEBAN LISTRIK', 'IDR', 1, 'FIKY'),

    (v_id_beban, 'JVGENL', 'DRAFT', 'TESTBEBAN001', 2,
     'COUNTER_ACCOUNT', 'NILAI', '111201', 0, 5000000,
     'TST/2610/PA0002', 'TST', 'BANK MANDIRI', 'IDR', 1, 'FIKY');

    -- ============================================
    -- 6. UPDATE STATUS HEADER JURNAL BEBAN → POSTED
    -- ============================================
    UPDATE sc_trx.jurnal_hd 
    SET status = 'POSTED', updateddate = NOW()
    WHERE id = v_id_beban;

    RAISE NOTICE 'Insert selesai: ID Penjualan=% | ID Beban=%', v_id_sales, v_id_beban;

END;
$$;


-- ============================================================
-- VERIFIKASI: cek data yang baru diinsert
-- ============================================================
SELECT 
    jh.docno, jh.trxdate, jh.journal_type, jh.status AS status_hd,
    jd.idcoa, c.nmcoa, c.groupcoa, jd.status AS status_dt,
    jd.debet, jd.kredit
FROM sc_trx.jurnal_dt jd
JOIN sc_trx.jurnal_hd jh ON jh.id = jd.jurnal_id
JOIN sc_mst.coa c ON TRIM(c.idcoa) = TRIM(jd.idcoa)
WHERE jh.docno LIKE 'TST/%'
ORDER BY jh.docno, jd.seq;


-- ============================================================
-- LAPORAN LABA RUGI (hasil akhir)
-- ============================================================
WITH parameter AS (
    SELECT 
        DATE '2026-10-31' AS tgl_laporan,
        ''::VARCHAR AS cabang
),
mutasi AS (
    SELECT 
        jd.idcoa,
        jh.trxdate,
        COALESCE(TRIM(jh.idbranch), 'PUSAT') AS job,
        SUM(jd.debet - jd.kredit) AS saldo
    FROM sc_trx.jurnal_dt jd
    JOIN sc_trx.jurnal_hd jh ON jh.id = jd.jurnal_id
    WHERE jd.status = 'POSTED'
      AND (
          (SELECT cabang FROM parameter) = '' 
          OR TRIM(jh.idbranch) = TRIM((SELECT cabang FROM parameter))
      )
      AND jh.trxdate <= (SELECT tgl_laporan FROM parameter)
    GROUP BY jd.idcoa, jh.trxdate, TRIM(jh.idbranch)
),
rekap AS (
    SELECT 
        m.idcoa,
        m.job,
        SUM(CASE 
            WHEN m.trxdate BETWEEN 
                (DATE_TRUNC('month', (SELECT tgl_laporan FROM parameter)) - INTERVAL '1 year')
                AND 
                ((SELECT tgl_laporan FROM parameter) - INTERVAL '1 year')
            THEN m.saldo ELSE 0 END) AS bulan_lalu,
        SUM(CASE 
            WHEN m.trxdate BETWEEN 
                DATE_TRUNC('month', (SELECT tgl_laporan FROM parameter))
                AND 
                (SELECT tgl_laporan FROM parameter)
            THEN m.saldo ELSE 0 END) AS bulan_berjalan,
        SUM(CASE 
            WHEN m.trxdate BETWEEN 
                DATE_TRUNC('year', (SELECT tgl_laporan FROM parameter))
                AND 
                (SELECT tgl_laporan FROM parameter)
            THEN m.saldo ELSE 0 END) AS tahun_berjalan
    FROM mutasi m
    GROUP BY m.idcoa, m.job
)
SELECT 
    CASE TRIM(c.groupcoa)
        WHEN 'PENDAPATAN' THEN 'PENDAPATAN'
        WHEN 'BIAYA' THEN 'HARGA POKOK PENJUALAN'
        WHEN 'PENDAPATAN DI LUAR USAHA' THEN 'BEBAN OPERASIONAL'
        WHEN 'BIAYA DI LUAR USAHA' THEN 'PENDAPATAN / BIAYA LAIN-LAIN'
        ELSE 'LAIN-LAIN'
    END AS parent,
    TRIM(c.nmcoa) AS description,
    COALESCE(r.bulan_lalu, 0)     AS bulan_lalu,
    COALESCE(r.bulan_berjalan, 0) AS bulan_berjalan,
    COALESCE(r.tahun_berjalan, 0) AS tahun_berjalan,
    COALESCE(r.job, '-')          AS job
FROM rekap r
JOIN sc_mst.coa c ON TRIM(c.idcoa) = TRIM(r.idcoa)
WHERE TRIM(c.groupcoa) IN (
    'PENDAPATAN', 
    'BIAYA', 
    'PENDAPATAN DI LUAR USAHA', 
    'BIAYA DI LUAR USAHA'
)
ORDER BY 
    CASE TRIM(c.groupcoa) 
        WHEN 'PENDAPATAN' THEN 1 
        WHEN 'BIAYA' THEN 2 
        WHEN 'PENDAPATAN DI LUAR USAHA' THEN 3 
        WHEN 'BIAYA DI LUAR USAHA' THEN 4 
        ELSE 5 
    END,
    TRIM(c.idcoa),
    r.job;

