/* SIMULASI INPUT LEGER
FLOW PURCHASE → INVENTORY → ACCOUNTING → AP → PAYMENT


PURCHASE
│
├── Purchase Request (PP)
│   ├── sc_trx.pp
│   └── sc_trx.pp_dtl
│
├── Purchase Order (PO)
│   ├── sc_trx.po
│   └── sc_trx.po_dtl
│
└── Goods Receipt / LPB
    ├── sc_trx.lpb
    └── sc_trx.lpb_dtl


INVENTORY
│
├── Stock Transaction
│   └── sc_trx.stkblc
│
├── Stock Card
│   └── sc_trx.v_kartu_stock
│
└── Average Cost
    └── sc_trx.stkblc_avgcost


ACCOUNTING
│
├── Journal Transaction
│   ├── sc_trx.jurnal_hd
│   └── sc_trx.jurnal_dt
│
├── General Ledger
│
└── Trial Balance


FINANCE
│
├── Account Payable
│   ├── AP Invoice
│   │   ├── sc_trx.ap_hd
│   │   └── sc_trx.ap_dtl
│   │
│   └── AP Payment
│       ├── sc_trx.ap_payment_hd
│       └── sc_trx.ap_payment_dtl
│
├── Cash & Bank
│
└── Other Expense
    ├── Expense Voucher
    └── Payment Voucher
 
/*
┌─────────────────────────────────────────────────────────────────────┐
│                        PURCHASE REQUEST (PP)                         │
│                         Permintaan Pembelian                         │
├─────────────────────────────────────────────────────────────────────┤
│ MENU   : Purchase → Purchase Request                                │
│ HEADER : sc_trx.pp                                                   │
│ DETAIL : sc_trx.pp_dtl                                               │
└──────────────────────────────┬──────────────────────────────────────┘
                               │
                               │ Approval
                               ▼
┌─────────────────────────────────────────────────────────────────────┐
│                         PURCHASE ORDER (PO)                          │
│                            Pesanan Pembelian                         │
├─────────────────────────────────────────────────────────────────────┤
│ MENU   : Purchase → Purchase Order                                  │
│ HEADER : sc_trx.po                                                   │
│ DETAIL : sc_trx.po_dtl                                               │
│                                                                      │
│ STATUS : P → LPB                                                     │
│ qtylpb : Bertambah saat barang diterima                              │
└──────────────────────────────┬──────────────────────────────────────┘
                               │
                               │ Barang Datang
                               ▼
┌─────────────────────────────────────────────────────────────────────┐
│                     LPB / GOODS RECEIPT (GR)                        │
│                        Penerimaan Barang                             │
├─────────────────────────────────────────────────────────────────────┤
│ MENU   : Purchase → LPB / Goods Receipt                             │
│ HEADER : sc_trx.lpb                                                  │
│ DETAIL : sc_trx.lpb_dtl                                              │
│                                                                      │
│ DATA PENTING:                                                        │
│ • Supplier                                                           │
│ • PO Reference                                                       │
│ • Currency                                                           │
│ • Kurs                                                               │
│ • Harga                                                              │
│ • Tax / PPN                                                          │
│ • Tax Inclusive / Exclusive                                          │
└──────────────────────────────┬──────────────────────────────────────┘
                               │
                               │ FINALIZE LPB
                               ▼
┌─────────────────────────────────────────────────────────────────────┐
│                         INVENTORY POSTING                            │
│                           STOCK MOVEMENT                             │
├─────────────────────────────────────────────────────────────────────┤
│ TABLE : sc_trx.stkblc                                                │
│                                                                      │
│ MENYIMPAN:                                                           │
│ • Stock IN / OUT                                                     │
│ • Qty                                                                │
│ • Harga                                                              │
│ • Currency                                                           │
│ • Exchange Rate                                                      │
│ • Tax ID                                                             │
│ • Tax Percent                                                        │
│ • Tax Inclusive                                                      │
│ • DPP                                                                │
│ • PPN                                                                │
│ • Gross Value                                                        │
│ • COA Tax                                                            │
│                                                                      │
│ DOCTYPE : GR                                                         │
│ HIST    : LPB                                                        │
│ CTYPE   : IN                                                         │
└──────────────────────────────┬──────────────────────────────────────┘
                               │
                               │ Update Cost
                               ▼
┌─────────────────────────────────────────────────────────────────────┐
│                       INVENTORY AVERAGE COST                         │
├─────────────────────────────────────────────────────────────────────┤
│ TABLE : sc_trx.stkblc_avgcost                                        │
│                                                                      │
│ • idbarang                                                           │
│ • idlocation                                                         │
│ • batch                                                              │
│ • qty                                                                │
│ • total_value                                                        │
│ • avg_cost                                                           │
│                                                                      │
│ FUNCTION:                                                            │
│ sc_trx.sp_rebuild_avgcost()                                          │
└──────────────────────────────┬──────────────────────────────────────┘
                               │
                               │ POST GL
                               ▼
┌─────────────────────────────────────────────────────────────────────┐
│                         GENERAL LEDGER POSTING                       │
├─────────────────────────────────────────────────────────────────────┤
│ VIEW     : sc_trx.v_stk_to_gl                                        │
│ FUNCTION : sc_trx.sp_post_gl(p_user)                                 │
│                                                                      │
│ Mengambil data dari:                                                  │
│ • sc_trx.stkblc                                                      │
│ • sc_trx.stkblc_avgcost                                              │
│ • sc_mst.mbarang                                                     │
│ • sc_mst.currency                                                    │
│ • Tax snapshot pada stkblc                                           │
└──────────────────────────────┬──────────────────────────────────────┘
                               │
                               ▼
╔═════════════════════════════════════════════════════════════════════╗
║                         JOURNAL HEADER                              ║
╠═════════════════════════════════════════════════════════════════════╣
║ TABLE : sc_trx.jurnal_hd                                            ║
║                                                                     ║
║ • id                                                                ║
║ • docno                                                             ║
║ • doctype                                                           ║
║ • trxdate                                                           ║
║ • total_debet                                                       ║
║ • total_kredit                                                      ║
║ • status                                                            ║
╚═══════════════════════════════┬═════════════════════════════════════╝
                                │
                                ▼
╔═════════════════════════════════════════════════════════════════════╗
║                         JOURNAL DETAIL                              ║
╠═════════════════════════════════════════════════════════════════════╣
║ TABLE : sc_trx.jurnal_dt                                            ║
║                                                                     ║
║ • jurnal_id                                                         ║
║ • idcoa                                                             ║
║ • debet                                                             ║
║ • kredit                                                            ║
║ • ref_docno                                                         ║
║ • ref_doctype                                                       ║
╚═══════════════════════════════┬═════════════════════════════════════╝
                                │
                                ▼
                               │
                               ▼
┌─────────────────────────────────────────────────────────────────────┐
│                    JURNAL DAGANG / ACCOUNT PAYABLE                  │
│                              (AP)                                   │
├─────────────────────────────────────────────────────────────────────┤
│ MENU : Finance → Account Payable → Invoice / Hutang Dagang          │
│                                                                      │
│ HEADER : sc_trx.ap_hd        ← rekomendasi                          │
│ DETAIL : sc_trx.ap_dtl       ← rekomendasi                          │
│                                                                      │
│ REFERENSI:                                                           │
│ LPB / GR                                                             │
│ PO                                                                   │
│ Supplier                                                             │
│                                                                      │
│ STATUS:                                                              │
│ OPEN → PARTIAL → PAID                                                │
└──────────────────────────────┬──────────────────────────────────────┘
                               │
                               │ Payment
                               ▼
┌─────────────────────────────────────────────────────────────────────┐
│                        PAYMENT HUTANG DAGANG                         │
├─────────────────────────────────────────────────────────────────────┤
│ MENU : Finance → AP Payment                                         │
│                                                                      │
│ HEADER : sc_trx.ap_payment_hd   ← rekomendasi                       │
│ DETAIL : sc_trx.ap_payment_dtl  ← rekomendasi                       │
│                                                                      │
│ REFERENSI:                                                           │
│ • Supplier                                                           │
│ • AP Invoice                                                         │
│ • LPB                                                                │
│ • Payment Number                                                     │
└──────────────────────────────┬──────────────────────────────────────┘
                               │
                               ▼
┌─────────────────────────────────────────────────────────────────────┐
│                          CASH / BANK                                │
├─────────────────────────────────────────────────────────────────────┤
│ MENU : Finance → Cash & Bank                                        │
│                                                                      │
│ TABLE HEADER : sc_trx.cashbank_hd ← rekomendasi                     │
│ TABLE DETAIL : sc_trx.cashbank_dt ← rekomendasi                     │
│                                                                      │
│ JURNAL PEMBAYARAN:                                                   │
│                                                                      │
│ DEBET  Hutang Dagang                                                 │
│ KREDIT Kas / Bank                                                    │
└─────────────────────────────────────────────────────────────────────┘
*/


/* MANUAL INSERT */

/* MANUAL INSERT */
select * from sc_mst.currency
select * from sc_trx.stkblc;
select * from sc_trx.stkblc_avgcost; --persedian dan stock
select * from sc_trx.stkblc_snapshot; --jurnal posting saldo akhir per bulan
select * from sc_mst.stkgdw; --stock perarea

/*posting perkiraan jurnal */
select * from sc_trx.jurnal_hd;
select * from sc_trx.jurnal_dt a;
select * from  sc_trx.v_stk_to_gl
--DELETE FROM sc_trx.stkblc_avgcost;
COMMIT;

--reverse juga dari table ini
select * from sc_trx.stkblc_avgcost; 
select * from sc_trx.jurnal_hd;
select * from sc_trx.jurnal_dt a;
select * from sc_mst.tax_dtl
select * from sc_trx.lpb a le
select * from sc_trx.lpb_dtl a left outer join sc_mst.where docno='LPB/2609/PA0001'