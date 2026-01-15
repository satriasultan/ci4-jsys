INSERT INTO sc_mst.menuprg (
    branch, 
    urut, 
    kodemenu, 
    namamenu, 
    parentmenu, 
    parentsub, 
    child, 
    holdmenu, 
    iconmenu, 
    linkmenu, 
    menuposition, 
    chold
) VALUES 
('JTS', 1, 'I.M.B.1', 'MASTER CUSTOMER', 'I.M', 'I.M.B', 'P', false, 'fa-lightbulb-o', 'master/data/customer', 'LEFT', 'NO'),
('JTS', 2, 'I.M.B.2', 'MASTER SUPPLIER', 'I.M', 'I.M.B', 'P', false, 'fa-list-alt', 'master/data/supplier', 'LEFT', 'NO');
-- ('JTS', 3, 'I.M.B.3', 'MASTER TAX', 'I.M', 'I.M.B', 'P', false, 'fa-calendar', 'master/data/tax', 'LEFT', 'NO'),
-- ('JTS', 4, 'I.M.B.4', 'MASTER COA', 'I.M', 'I.M.B', 'P', false, 'fa-sliders', 'master/data/coa', 'LEFT', 'NO'),
-- ('JTS', 5, 'I.M.B.5', 'MASTER JOB', 'I.M', 'I.M.B', 'P', false, 'fa-folder', 'master/data/job', 'LEFT', 'NO'),
-- ('JTS', 6, 'I.M.B.6', 'MASTER WAREHOUSE', 'I.M', 'I.M.B', 'P', false, 'fa-external-link', 'master/location', 'LEFT', 'NO');
-- ('JTS', 7, 'I.M.B.7', 'MASTER SUB LOCATION', 'I.M', 'I.M.B', 'P', false, 'fa-traceability', 'master/data/sub', 'LEFT', 'NO'),
-- ('JTS', 8, 'I.M.B.8', 'MASTER COST CENTER', 'I.M', 'I.M.B', 'P', false, 'fa-traceability', 'master/data/cc', 'LEFT', 'NO');
