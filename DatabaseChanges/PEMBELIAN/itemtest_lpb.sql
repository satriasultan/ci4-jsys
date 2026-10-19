/* ITEM TEST LPB */
INSERT INTO sc_tmp.lpb (
    idurut, docno, docdate, cabang, pemohon, kdsupplier, nmsupplier, alamatsupplier, 
    jthtempo, biayavol, biayavol2, idtax, isinclusive, currcode, kurs, nofaktur, 
    nosj, dpp, jumlahpajak, total, status, keterangan, inputby, inputdate, 
    updateby, updatedate, printby, printdate, printcount, docnotmp, doctype
) VALUES (
    2, 
    'LPB/2610/PA0001', 
    '2026-10-18', 
    '01.01', 
    'FIKY', 
    '10517', 
    'ASIA TRANSMISSION PTE LTD', 
    'S9 BUILDING, 55 SERANGOON NORTH AVE 4, #08-01 SINGAPORE 555859', 
    14.00, 
    0.00, 
    0.00, 
    'PPN11', 
    'NO', 
    'IDR', 
    1.00, 
    '', 
    '', 
    4600600000.00, 
    506066000.00, 
    5106666000.00, 
    'E', 
    'TEST', 
    'FIKY', 
    '2026-10-18 22:09:18', 
    'FIKY', 
    '2026-10-18 22:09:18', 
    NULL, 
    NULL, 
    NULL, 
    NULL, 
    'GR'
);


INSERT INTO sc_tmp.lpb_dtl (
    idurut, docno, docnopo, uniqueid, uniqueidpo, uniqueidlpb, idbarang, nmbarang, 
    idprincipal, idgudang, unit, qty, qtybonus, harga, multidisc, volitem, 
    biaya, biaya2, nilai, descriptionpo, descriptionpp, status, inputby, inputdate, 
    updateby, updatedate, docnotmp, capexno, doctype, idtax, currcode, kurs, 
    nilaikonversi, nilaipajak, qtyretur, idhistory_price, multidisctype, 
    totaldiscount, batch, etd, etdnotes
) VALUES 
(
    4, 
    'LPB/2610/PA0001', 
    'POB/2610/PA0001', 
    'a205692689c5b9caaa26195f5d497887926d95130728a9d8a5a80f958b893b7b', 
    '1d73a1c89796904a34afb8117ab52eff', 
    'b5ee336c67670de2852075b25273dcdb', 
    '01010206000001', 
    'GARDU INDUK 150 KV ( JGN DIPAKAI )', 
    '', 
    '16401', 
    'UNIT', 
    2000.00, 
    0, 
    2000000.00, 
    0, 
    0, 
    0, 
    0, 
    4000000000.00, 
    'TEST', 
    'TEST', 
    '', 
    'FIKY', 
    '2026-10-18 22:09:18', 
    NULL, 
    NULL, 
    '', 
    '', 
    'GR', 
    'PPN11', 
    'IDR', 
    1.00, 
    4000000000.00, 
    440000000.00, 
    0.00, 
    '', 
    '', 
    0, 
    '', 
    '2026-10-25', 
    'BBSC'
),
(
    5, 
    'LPB/2610/PA0001', 
    'POB/2610/PA0001', 
    '91c37185dc7d7294541350428759e96b8b94cb28827c8df49b2761ddcf0b0846', 
    '4c2411b6c9783565b4ff92d8e0864190', 
    'f70acd37cd76ec050c38858a6f9d395c', 
    '01010206000001', 
    'GARDU INDUK 150 KV ( JGN DIPAKAI )', 
    '', 
    '16401', 
    'UNIT', 
    2000.00, 
    0, 
    300000.00, 
    0, 
    0, 
    0, 
    0, 
    600000000.00, 
    'TEST', 
    'TEST', 
    '', 
    'FIKY', 
    '2026-10-18 22:09:18', 
    NULL, 
    NULL, 
    '', 
    '', 
    'GR', 
    'PPN11', 
    'IDR', 
    1.00, 
    600000000.00, 
    66000000.00, 
    0.00, 
    '', 
    '', 
    0, 
    'BBC', 
    '2026-10-25', 
    ''
),
(
    6, 
    'LPB/2610/PA0001', 
    'POB/2610/PA0001', 
    'af322c96253909cd9e90d962d65f15993832a75a2563d335f4b6c17ed2f11e47', 
    '2b6688fe73c6981ef4cb98afd51a3f19', 
    '2fe3d7e7bb3b847046d9868cbd72649e', 
    '01010206000017', 
    'GARDU INDUK 150 KV ( JGN DIPAKAI )', 
    '', 
    '16401', 
    'UNIT', 
    2.00, 
    0, 
    300000.00, 
    0, 
    0, 
    0, 
    0, 
    600000.00, 
    'SDF', 
    'SDF', 
    '', 
    'FIKY', 
    '2026-10-18 22:09:18', 
    NULL, 
    NULL, 
    '', 
    'SDF', 
    'GR', 
    'PPN11', 
    'IDR', 
    1.00, 
    600000.00, 
    66000.00, 
    0.00, 
    '', 
    '', 
    0, 
    '', 
    '2026-10-25', 
    ''
);


update sc_tmp.lpb set status='F' where docno='LPB/2610/PA0001';


/* ============================================================================
   ITEM TEST LPB 002 (AKUMULASI STOK)
   ============================================================================ */
-- 1. Insert Header TMP
INSERT INTO sc_tmp.lpb (
    idurut, docno, docdate, cabang, pemohon, kdsupplier, nmsupplier, alamatsupplier, 
    jthtempo, biayavol, biayavol2, idtax, isinclusive, currcode, kurs, nofaktur, 
    nosj, dpp, jumlahpajak, total, status, keterangan, inputby, inputdate, 
    updateby, updatedate, printby, printdate, printcount, docnotmp, doctype
) VALUES (
    3, 
    'LPB/2610/PA0002', 
    '2026-10-18', 
    '01.01', 
    'FIKY', 
    '10517', 
    'ASIA TRANSMISSION PTE LTD', 
    'S9 BUILDING, 55 SERANGOON NORTH AVE 4, #08-01 SINGAPORE 555859', 
    14.00, 0.00, 0.00, 'PPN11', 'NO', 'IDR', 1.00, '', '', 
    3300000000.00, 363000000.00, 3663000000.00, 'E', 'TEST LPB 002', 'FIKY', 
    '2026-10-18 22:30:00', 'FIKY', '2026-10-18 22:30:00', NULL, NULL, NULL, NULL, 'GR'
);

-- 2. Insert Detail TMP
INSERT INTO sc_tmp.lpb_dtl (
    idurut, docno, docnopo, uniqueid, uniqueidpo, uniqueidlpb, idbarang, nmbarang, 
    idprincipal, idgudang, unit, qty, qtybonus, harga, multidisc, volitem, 
    biaya, biaya2, nilai, descriptionpo, descriptionpp, status, inputby, inputdate, 
    updateby, updatedate, docnotmp, capexno, doctype, idtax, currcode, kurs, 
    nilaikonversi, nilaipajak, qtyretur, idhistory_price, multidisctype, 
    totaldiscount, batch, etd, etdnotes
) VALUES 
(
    7, 
    'LPB/2610/PA0002', 
    'POB/2610/PA0002', 
    'c1a2b3d4e5f67890123456789abcdef0123456789abcdef0123456789abcdef0', 
    '2b6688fe73c6981ef4cb98afd51a3f19', 
    '1a2b3c4d5e6f708192a3b4c5d6e7f809', 
    '01010206000001', 
    'GARDU INDUK 150 KV ( JGN DIPAKAI )', 
    '', 
    '16401', 
    'UNIT', 
    1000.00,  -- Qty 1000
    0, 
    500000.00, -- Harga lebih mahal dari LPB sebelumnya
    0, 0, 0, 0, 
    500000000.00, 
    'TEST LPB 002', 
    'TEST LPB 002', 
    '', 
    'FIKY', 
    '2026-10-18 22:30:00', 
    NULL, NULL, '', '', 'GR', 'PPN11', 'IDR', 1.00, 
    500000000.00, 
    55000000.00, 
    0.00, '', '', 0, 
    'BBC', -- Pakai batch BBC yang sama seperti LPB sebelumnya
    '2026-10-25', 
    ''
),
(
    8, 
    'LPB/2610/PA0002', 
    'POB/2610/PA0002', 
    'd4e5f67890123456789abcdef0123456789abcdef0123456789abcdef012345', 
    '2b6688fe73c6981ef4cb98afd51a3f19', 
    '2b3c4d5e6f708192a3b4c5d6e7f8091a', 
    '01010206000017', 
    'GARDU INDUK 150 KV ( JGN DIPAKAI )', 
    '', 
    '16401', 
    'UNIT', 
    5.00,  -- Qty 5
    0, 
    400000.00, 
    0, 0, 0, 0, 
    2000000.00, 
    'SDF 002', 
    'SDF 002', 
    '', 
    'FIKY', 
    '2026-10-18 22:30:00', 
    NULL, NULL, '', 'SDF', 'GR', 'PPN11', 'IDR', 1.00, 
    2000000.00, 
    220000.00, 
    0.00, '', '', 0, 
    '', 
    '2026-10-25', 
    ''
);

/* ============================================================================
   PEMICU FINAL (Ubah Status E -> F untuk LPB 002)
   ============================================================================ */
UPDATE sc_tmp.lpb SET status = 'F' WHERE docno = 'LPB/2610/PA0002';


select * from sc_trx.transaction_hd;
select * from sc_trx.transaction_dt;
select * from sc_trx.jurnal_hd;
select * from sc_trx.jurnal_dt


select * from sc_mst.stkgdw;
select * from sc_trx.stkblc;
select * from sc_trx.stkblc_avgcost;





