DELETE FROM sc_mst.trxtype WHERE jenistrx = 'I.P.A.1';

-- Kemudian masukkan data baru
INSERT INTO sc_mst.trxtype (kdtrx, jenistrx, uraian) VALUES
    ('I', 'I.P.A.1', 'DRAFT'),
    ('E', 'I.P.A.1', 'REVISION/EDITING'),
    ('F', 'I.P.A.1', 'FINAL USER'),
    ('A', 'I.P.A.1', 'APPROVED'),
    ('P', 'I.P.A.1', 'CETAK/PRINT');




DELETE FROM sc_mst.trxtype WHERE jenistrx = 'I.P.A.2';

-- Kemudian masukkan data baru
INSERT INTO sc_mst.trxtype (kdtrx, jenistrx, uraian) VALUES
    ('I', 'I.P.A.2', 'DRAFT'),
    ('E', 'I.P.A.2', 'REVISION/EDITING'),
    ('F', 'I.P.A.2', 'FINAL USER'),
    ('A', 'I.P.A.2', 'APPROVED'),
    ('P', 'I.P.A.2', 'CETAK/PRINT');