CREATE TABLE sc_trx.stkblc_snapshot (
    periode CHAR(6), -- YYYYMM
    idbarang VARCHAR(20),
    idlocation VARCHAR(12),
    batch VARCHAR(100),

    qty NUMERIC(18,4),
    total_value NUMERIC(18,4),
    avg_cost NUMERIC(18,4),

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (periode, idbarang, idlocation, batch)
);

/* FUNCTION SNAPSHOT (CLOSING BULAN) */
CREATE OR REPLACE FUNCTION sc_trx.sp_generate_snapshot(
    p_periode CHAR(6)
)
RETURNS VOID
LANGUAGE plpgsql
AS $$
BEGIN

    DELETE FROM sc_trx.stkblc_snapshot
    WHERE periode = p_periode;

    INSERT INTO sc_trx.stkblc_snapshot (
        periode,
        idbarang,
        idlocation,
        batch,
        qty,
        total_value,
        avg_cost,
        created_at
    )
    SELECT
        p_periode,
        s.idbarang,
        s.idlocation,
        s.batch,
        SUM(s.qty_in - s.qty_out) AS qty,

        -- 🔥 FIX: gunakan avgcost saat itu
        SUM(
            CASE 
                WHEN s.qty_in > 0 THEN s.qty_in * s.pricelst_in
                WHEN s.qty_out > 0 THEN -s.qty_out * COALESCE(a.avg_cost,0)
                ELSE 0
            END
        ) AS total_value,

        CASE 
            WHEN SUM(s.qty_in - s.qty_out) <> 0
            THEN SUM(
                CASE 
                    WHEN s.qty_in > 0 THEN s.qty_in * s.pricelst_in
                    WHEN s.qty_out > 0 THEN -s.qty_out * COALESCE(a.avg_cost,0)
                    ELSE 0
                END
            ) / SUM(s.qty_in - s.qty_out)
            ELSE 0
        END AS avg_cost,

        NOW()

    FROM sc_trx.stkblc s
    LEFT JOIN sc_trx.stkblc_avgcost a
        ON a.idbarang = s.idbarang
       AND a.idlocation = s.idlocation
       AND a.batch = s.batch

    WHERE to_char(s.trxdate,'YYYYMM') <= p_periode
    GROUP BY s.idbarang, s.idlocation, s.batch;

END;
$$;



CREATE OR REPLACE FUNCTION sc_trx.sp_rebuild_avgcost_snapshot(
    p_idbarang VARCHAR,
    p_idlocation VARCHAR,
    p_batch VARCHAR,
    p_periode CHAR(6)
)
RETURNS VOID
LANGUAGE plpgsql
AS $$
DECLARE
    v_qty NUMERIC := 0;
    v_total NUMERIC := 0;
    v_avg NUMERIC := 0;
    rec RECORD;
BEGIN

    -- 🔥 OPENING
    SELECT qty, total_value, avg_cost
    INTO v_qty, v_total, v_avg
    FROM sc_trx.stkblc_snapshot
    WHERE periode = p_periode
      AND idbarang = p_idbarang
      AND idlocation = p_idlocation
      AND batch = p_batch;

    v_qty := COALESCE(v_qty,0);
    v_total := COALESCE(v_total,0);
    v_avg := COALESCE(v_avg,0);

    -- 🔥 LOOP TRANSAKSI BULAN SETELAHNYA
    FOR rec IN
        SELECT *
        FROM sc_trx.stkblc
        WHERE idbarang = p_idbarang
          AND idlocation = p_idlocation
          AND batch = p_batch
          AND to_char(trxdate,'YYYYMM') > p_periode
        ORDER BY trxdate, idsort
    LOOP

        -- IN
        IF rec.qty_in > 0 THEN
            v_total := v_total + (rec.qty_in * rec.pricelst_in);
            v_qty := v_qty + rec.qty_in;
        END IF;

        -- OUT (pakai avg saat itu)
        IF rec.qty_out > 0 THEN
            v_total := v_total - (rec.qty_out * v_avg);
            v_qty := v_qty - rec.qty_out;
        END IF;

        -- RECALC AVG
        IF v_qty <> 0 THEN
            v_avg := v_total / v_qty;
        ELSE
            v_avg := 0;
        END IF;

    END LOOP;

    -- 🔥 UPSERT
    INSERT INTO sc_trx.stkblc_avgcost (
        idbarang, idlocation, batch,
        qty, total_value, avg_cost,
        created_at, updated_at
    )
    VALUES (
        p_idbarang, p_idlocation, p_batch,
        v_qty, v_total, v_avg,
        NOW(), NOW()
    )
    ON CONFLICT (idbarang, idlocation, batch)
    DO UPDATE SET
        qty = EXCLUDED.qty,
        total_value = EXCLUDED.total_value,
        avg_cost = EXCLUDED.avg_cost,
        updated_at = NOW();

END;
$$;

