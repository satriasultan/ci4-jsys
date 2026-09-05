CREATE TABLE sc_log.logtrans (
    id_serial SERIAL PRIMARY KEY NOT NULL,
    docno CHAR(30) NOT NULL,
    uniqueid VARCHAR(64),
    modul CHAR(30),
    menu CHAR(30),
    action CHAR(10),
    inputby VARCHAR(50),
    inputdate TIMESTAMP WITHOUT TIME ZONE,
    ip TEXT,
    username CHAR(50)
);

CREATE TABLE IF NOT EXISTS sc_mst.action (
    id SERIAL PRIMARY KEY,
    action_code CHAR(1) NOT NULL UNIQUE,   -- 1 huruf
    action_name VARCHAR(100) NOT NULL
);

INSERT INTO sc_mst.action (action_code, action_name) VALUES
    ('I', 'INPUT DATA'),
    ('U', 'UPDATE DATA'),
    ('D', 'DELETE DATA'),
    ('C', 'CANCEL DATA'),
    ('A', 'APPROVED'),
    ('R', 'REJECT'),
    ('P', 'PRINT'),
    ('E', 'EDIT'),        -- V = Revisi (karena R sudah dipakai Reject)
    ('L', 'LOGIN'),
    ('G', 'LOGOUT'),
    ('O', 'VOID'),
    ('N', 'UNVOID');


-- Fungsi get IP dari useronline
CREATE OR REPLACE FUNCTION sc_log.fn_get_user_ip(
    p_username VARCHAR(50)
)
RETURNS TEXT
LANGUAGE plpgsql
AS $BODY$
DECLARE
    v_ip TEXT;
BEGIN
    SELECT ip
    INTO v_ip
    FROM sc_log.useronline
    WHERE username = p_username
    ORDER BY tgl DESC
    LIMIT 1;
    
    RETURN v_ip;  -- Bisa NULL jika user tidak ditemukan
END;
$BODY$;



    -- Fungsi untuk mencatat log transaksi
    CREATE OR REPLACE FUNCTION sc_log.fn_log_transaction(
        p_docno CHAR(30),
        p_uniqueid VARCHAR(64),
        p_modul CHAR(30),
        p_menu CHAR(30),
        p_action CHAR(1),      -- ← sekarang hanya 1 huruf!
        p_inputby VARCHAR(50),
        p_ip TEXT,
        p_username CHAR(50)
    )
    RETURNS VOID
    LANGUAGE plpgsql
    AS $BODY$
    DECLARE
        v_modul_name VARCHAR(100);
        v_menu_name VARCHAR(100);
        v_action_name VARCHAR(100);
    BEGIN
        -- Ambil nama module
        SELECT namamenu INTO v_modul_name
        FROM sc_mst.menuprg
        WHERE kodemenu = p_modul
        LIMIT 1;
        
        IF v_modul_name IS NULL THEN
            v_modul_name := p_modul;
        END IF;
        
        -- Ambil nama menu
        SELECT namamenu INTO v_menu_name
        FROM sc_mst.menuprg
        WHERE kodemenu = p_menu
        LIMIT 1;
        
        IF v_menu_name IS NULL THEN
            v_menu_name := p_menu;
        END IF;
        
        -- Ambil nama action dari 1 huruf
        SELECT action_name INTO v_action_name
        FROM sc_mst.action
        WHERE action_code = p_action
        LIMIT 1;
        
        IF v_action_name IS NULL THEN
            v_action_name := p_action;
        END IF;
        
        -- Insert ke logtrans
        INSERT INTO sc_log.logtrans (
            docno,
            uniqueid,
            modul,
            menu,
            action,
            inputby,
            inputdate,
            ip,
            username
        ) VALUES (
            p_docno,
            p_uniqueid,
            v_modul_name,
            v_menu_name,
            v_action_name,
            p_inputby,
            CURRENT_TIMESTAMP,
            p_ip,
            p_username
        );
    END;
    $BODY$;