/*

┌─────────────────────┐
│      CUSTOMER       │
│  sc_mst.customer    │
└──────────┬──────────┘
           │
           ▼
┌─────────────────────┐
│    SALES ORDER      │
│                     │
│ sc_trx.so           │
│ sc_trx.so_dtl       │
└──────────┬──────────┘
           │
           ▼
┌─────────────────────┐
│    APPROVAL SO      │
│                     │
│ Status: Draft       │
│ → Approved          │
└──────────┬──────────┘
           │
           ▼
┌─────────────────────┐
│   DELIVERY ORDER    │
│      / DO           │
│                     │
│ sc_trx.do           │
│ sc_trx.do_dtl       │
└──────────┬──────────┘
           │
           │ Barang Keluar
           ▼
╔══════════════════════════════╗
║      INVENTORY POSTING       ║
╠══════════════════════════════╣
║                              ║
║ sc_trx.stkblc                ║
║ → qty_out                    ║
║ → nilai / avg cost           ║
║ → history transaksi          ║
║                              ║
║ sc_trx.stkgdw                ║
║ → saldo gudang berkurang     ║
║                              ║
╚══════════════╦═══════════════╝
               │
               ▼
┌─────────────────────────────────┐
│      POSTING HPP / COGS         │
├─────────────────────────────────┤
│ DEBET   HPP                     │
│ KREDIT  PERSEDIAAN              │
│                                 │
│ sc_trx.jurnal_hd                │
│ sc_trx.jurnal_dt                │
└───────────────┬─────────────────┘
                │
                ▼
┌─────────────────────┐
│   SALES INVOICE     │
│                     │
│ sc_trx.invoice      │
│ sc_trx.invoice_dtl  │
└──────────┬──────────┘
           │
           ▼
┌─────────────────────────────────┐
│     POSTING PENJUALAN / AR      │
├─────────────────────────────────┤
│ DEBET                           │
│ PIUTANG DAGANG                  │
│                                 │
│ KREDIT                          │
│ PENJUALAN                       │
│ PPN KELUARAN                    │
│                                 │
│ sc_trx.jurnal_hd                │
│ sc_trx.jurnal_dt                │
└───────────────┬─────────────────┘
                │
                ▼
┌─────────────────────┐
│    ACCOUNT RECEIVABLE│
│       / PIUTANG      │
│                      │
│ sc_trx.ar            │
│ sc_trx.ar_dtl        │
└──────────┬───────────┘
           │
           ▼
┌─────────────────────┐
│ CUSTOMER PAYMENT    │
│                     │
│ Pembayaran Piutang  │
└──────────┬──────────┘
           │
           ▼
┌─────────────────────────────────┐
│       KAS / BANK                │
├─────────────────────────────────┤
│ DEBET   KAS / BANK              │
│ KREDIT  PIUTANG DAGANG          │
└─────────────────────────────────┘


CUSTOMER
    ↓
SALES ORDER
    ↓
APPROVAL
    ↓
DELIVERY ORDER
    ↓
STOCK OUT
    ├── stkblc  → qty_out
    └── stkgdw  → saldo gudang
    ↓
JURNAL HPP
    ↓
SALES INVOICE
    ↓
JURNAL PENJUALAN
    ├── Piutang
    ├── Penjualan
    └── PPN Keluaran
    ↓
ACCOUNT RECEIVABLE
    ↓
CUSTOMER PAYMENT
    ↓
KAS / BANK


*/