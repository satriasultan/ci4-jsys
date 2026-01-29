
create table sc_mst.branchjob(
	idbranch char(20) primary key,
	nmbranch char(250),
    induk char(20),
    level char(20),
    keterangan TEXT,
    penghubung char(100),
    idcustomer char(50),
    alamatcust TEXT,
    manager char(50),
    datestart date,
    dateend date,
    persentase integer,
    plokasi char(20),
    kodearea char(20),
    status char(6),
	createdby CHAR(20),
    createddate TIMESTAMP WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updateby CHAR(20),
    updatedate TIMESTAMP WITHOUT TIME ZONE
)



-- Dalam satu query lengkap (termasuk data sebelumnya)
INSERT INTO sc_mst.branchjob (idbranch, nmbranch, induk, level, status) VALUES
-- Data sebelumnya
-- LEVEL 1
('01', 'PT JATIM TAMAN STEEL MFG', NULL, '1','F'),

-- LEVEL 2
('01.01', 'PLANT I', '01', '2','F'),
('01.02', 'PLANT II', '01', '2','F');
