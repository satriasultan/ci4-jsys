BEGIN;

-- =========================================================
-- 1. SEQUENCE
-- =========================================================

CREATE SEQUENCE IF NOT EXISTS sc_mst.rolepo_id_seq
    INCREMENT 1
    START 1
    MINVALUE 1
    MAXVALUE 2147483647
    CACHE 1;


-- =========================================================
-- 2. CREATE TABLE
-- =========================================================

CREATE TABLE IF NOT EXISTS sc_mst.rolepo
(
    id integer NOT NULL
        DEFAULT nextval('sc_mst.rolepo_id_seq'::regclass),

    jobcode character varying(20),
    codemenu character varying(10),

    infix character(4),

    prefix character varying(50),
    suffix character(5),

    tahun integer,
    bulan integer,

    inputby character varying(50),
    inputdate timestamp without time zone,

    CONSTRAINT rolepo_pkey
        PRIMARY KEY (id)
)
TABLESPACE pg_default;


-- =========================================================
-- 3. TAMBAHKAN KOLOM JIKA TABLE SUDAH ADA SEBELUMNYA
-- =========================================================

ALTER TABLE IF EXISTS sc_mst.rolepo
    ADD COLUMN IF NOT EXISTS tahun integer;

ALTER TABLE IF EXISTS sc_mst.rolepo
    ADD COLUMN IF NOT EXISTS bulan integer;


-- =========================================================
-- 4. OWNER
-- =========================================================

ALTER TABLE IF EXISTS sc_mst.rolepo
    OWNER TO postgres;


-- =========================================================
-- 5. SEQUENCE OWNER
-- =========================================================

ALTER SEQUENCE sc_mst.rolepo_id_seq
    OWNED BY sc_mst.rolepo.id;

ALTER SEQUENCE sc_mst.rolepo_id_seq
    OWNER TO postgres;


-- =========================================================
-- 6. UNIQUE CONSTRAINT
-- =========================================================

DO $$
BEGIN

    IF NOT EXISTS (
        SELECT 1
        FROM pg_constraint
        WHERE conname = 'uq_rolepo_combo'
          AND conrelid = 'sc_mst.rolepo'::regclass
    ) THEN

        ALTER TABLE sc_mst.rolepo
            ADD CONSTRAINT uq_rolepo_combo
            UNIQUE (jobcode, codemenu, tahun, bulan);

    END IF;

END
$$;


-- =========================================================
-- 7. INSERT DATA
--    infix 2508 = tahun 2025, bulan 8
-- =========================================================

INSERT INTO sc_mst.rolepo
(
    jobcode,
    codemenu,
    infix,
    prefix,
    suffix,
    tahun,
    bulan,
    inputby,
    inputdate
)
VALUES
(
    'MSMI',
    'I.S.A.2',
    '2508',
    'MSMI-PH',
    '00000',
    2025,
    8,
    'SYSTEM',
    '2025-08-07 16:48:12.704786'
),
(
    'MSMJ',
    'I.S.A.2',
    '2508',
    'MSM-PH',
    '00000',
    2025,
    8,
    'SYSTEM',
    '2025-08-07 16:48:12.704786'
),
(
    'JTS',
    'I.S.A.2',
    '2508',
    'JTS-PH',
    '00000',
    2025,
    8,
    'SYSTEM',
    '2025-08-07 16:48:12.704786'
)
ON CONFLICT (jobcode, codemenu, tahun, bulan)
DO UPDATE SET
    infix     = EXCLUDED.infix,
    prefix    = EXCLUDED.prefix,
    suffix    = EXCLUDED.suffix,
    inputby   = EXCLUDED.inputby,
    inputdate = EXCLUDED.inputdate;


COMMIT;