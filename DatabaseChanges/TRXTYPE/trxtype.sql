DELETE FROM sc_mst.trxtype WHERE jenistrx = 'I.P.A.1';

-- Kemudian masukkan data baru
INSERT INTO sc_mst.trxtype (kdtrx, jenistrx, uraian) VALUES
    ('I', 'I.P.A.1', 'DRAFT'),
    ('E', 'I.P.A.1', 'REVISION/EDITING'),
    ('C', 'I.P.A.1', 'CANCELED'),
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


    
DELETE FROM sc_mst.trxtype WHERE jenistrx = 'I.P.A.3';

-- Kemudian masukkan data baru
INSERT INTO sc_mst.trxtype (kdtrx, jenistrx, uraian) VALUES
    ('I', 'I.P.A.3', 'DRAFT'),
    ('E', 'I.P.A.3', 'REVISION/EDITING'),
    ('F', 'I.P.A.3', 'FINAL USER'),
    ('A', 'I.P.A.3', 'APPROVED'),
    ('D', 'I.P.A.3', 'DISAPPROVED'),
    ('P', 'I.P.A.3', 'CETAK/PRINT');






DELETE FROM sc_mst.trxtype WHERE jenistrx = 'I.S.B.1';

-- Kemudian masukkan data baru
INSERT INTO sc_mst.trxtype (kdtrx, jenistrx, uraian) VALUES
    ('I', 'I.S.B.1', 'DRAFT'),
    ('E', 'I.S.B.1', 'REVISION/EDITING'),
    ('F', 'I.S.B.1', 'FINAL USER'),
    ('C', 'I.S.B.1', 'CANCEL'),
    ('A', 'I.S.B.1', 'APPROVED'),
    ('D', 'I.S.B.1', 'DISAPPROVED'),
    ('P', 'I.S.B.1', 'CETAK/PRINT');