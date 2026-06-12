<style>
    /* Style untuk mode edit */
    .edit-mode .form-control[readonly],
    .edit-mode .form-control[disabled],
    .edit-mode select.select2[disabled] {
        background-color: #ffffff !important;
        opacity: 1;
    }

    .edit-mode .select2-container--disabled .select2-selection {
        background-color: #ffffff !important;
        border-color: #ced4da;
    }
    

</style>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><?php echo ucwords(strtolower(trim($title)));?></h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <div class="float-right" style="margin-right: 10px;vertical-align:middle;padding-top: 0.7%;"><i style="color:transparent;"><?php echo $t; ?></i> Versi: <?php echo $version; ?></div>
                    <input type="hidden" id="classmenu" value="<?= str_replace('.','_',$kodemenu) ?>" required>
                    <?php foreach ($y as $y1) { ?>
                        <?php if( trim($y1->kodemenu)!=trim($kodemenu)) { ?>
                            <li class="breadcrumb-item"><a href="<?php echo base_url( trim($y1->linkmenu)) ; ?>"><i class="fa <?php echo trim($y1->iconmenu); ?>"></i> <?php echo  trim($y1->namamenu); ?></a></li>
                        <?php } else { ?>
                            <li class="breadcrumb-item active"><i class="fa <?php echo trim($y1->iconmenu); ?>"></i> <?php echo trim($y1->namamenu); ?></li>
                        <?php } ?>
                    <?php } ?>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>

<div class="row">
	<div class="col-sm-12">
		<div class="card">
            <div class="card-header">
                <div class="float-right">
                    <button type="button" id="btnEditKonfigurasi" class="btn btn-primary btn-lg text-white">
                        <i class="fa fa-edit"></i> Edit Konfigurasi
                    </button>
                    <button type="button" id="btnSimpanKonfigurasi" class="btn btn-success btn-lg text-white" style="display: none;">
                        <i class="fa fa-save"></i> Simpan Konfigurasi
                    </button>
                    <button type="button" id="btnCancelEdit" class="btn btn-danger btn-lg text-white" style="display: none;">
                        <i class="fa fa-times"></i> Cancel Edit
                    </button>
                </div>
            </div>
            <ul class="nav nav-tabs" id="ppTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" 
                            id="jurnal-tab" 
                            data-bs-toggle="tab" 
                            data-bs-target="#jurnal-content" 
                            type="button" 
                            role="tab">
                        <i class="fa fa-book"></i> Jurnal 
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" 
                            id="perkiraan-tab" 
                            data-bs-toggle="tab" 
                            data-bs-target="#perkiraan-content" 
                            type="button" 
                            role="tab">
                        <i class="fa fa-sitemap"></i> Perkiraan
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" 
                            id="default-tab" 
                            data-bs-toggle="tab" 
                            data-bs-target="#default-content" 
                            type="button" 
                            role="tab">
                        <i class="fa fa-wrench"></i> Default
                    </button>
                </li>
            </ul>
            <form id="formKonfigurasi">
                <div class="card-body">
                    <div class="tab-content" id="jurnalTabContent">
                        <div class="tab-pane fade show active" id="jurnal-content" role="tabpanel">
                            <div class="container-fluid">
                                    <!-- PEMBELIAN -->
                                    <div class="card mb-4">
                                        <div class="card-header bg-info text-white">
                                            <h5 class="mb-0">Pembelian</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="pp">Permintaan Pembelian</label>
                                                        <input type="text" name="pp" id="pp" class="form-control" maxlength="3" style="text-transform:uppercase" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="voidpp">Void PP</label>
                                                        <input type="text" name="voidpp" id="voidpp" class="form-control" maxlength="3" style="text-transform:uppercase" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="po">Purchase Order</label>
                                                        <input type="text" name="po" id="po" class="form-control" maxlength="3" style="text-transform:uppercase" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="voidpo">Void PO</label>
                                                        <input type="text" name="voidpo" id="voidpo" class="form-control" maxlength="3" style="text-transform:uppercase" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="lpb">Pembelian</label>
                                                        <input type="text" name="lpb" id="lpb" class="form-control" maxlength="3" style="text-transform:uppercase" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="returbeli">Return Pembelian</label>
                                                        <input type="text" name="returbeli" id="returbeli" class="form-control" maxlength="3" style="text-transform:uppercase" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="refundbeli">Refund Beli</label>
                                                        <input type="text" name="refundbeli" id="refundbeli" class="form-control" maxlength="3" style="text-transform:uppercase" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- PENJUALAN -->
                                    <div class="card mb-4">
                                        <div class="card-header" style="background-color: #ffe668;">
                                            <h5 class="mb-0">Penjualan</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="salesorder">Sales Order</label>
                                                        <input type="text" name="salesorder" id="salesorder" class="form-control" maxlength="3" style="text-transform:uppercase" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="voidso">Void SO</label>
                                                        <input type="text" name="voidso" id="voidso" class="form-control" maxlength="3" style="text-transform:uppercase" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="deliveryorder">Delivery Order</label>
                                                        <input type="text" name="deliveryorder" id="deliveryorder" class="form-control" maxlength="3" style="text-transform:uppercase" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="suratjalan">Surat Jalan</label>
                                                        <input type="text" name="suratjalan" id="suratjalan" class="form-control" maxlength="3" style="text-transform:uppercase" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="penjualan">Penjualan</label>
                                                        <input type="text" name="penjualan" id="penjualan" class="form-control" maxlength="3" style="text-transform:uppercase" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="penjualannon">Penjualan Non</label>
                                                        <input type="text" name="penjualannon" id="penjualannon" class="form-control" maxlength="3" style="text-transform:uppercase" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="returpenjualan">Retur Penjualan</label>
                                                        <input type="text" name="returpenjualan" id="returpenjualan" class="form-control" maxlength="3" style="text-transform:uppercase" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="retursj">Retur Surat Jalan</label>
                                                        <input type="text" name="retursj" id="retursj" class="form-control" maxlength="3" style="text-transform:uppercase" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="refundjual">Refund Jual</label>
                                                        <input type="text" name="refundjual" id="refundjual" class="form-control" maxlength="3" style="text-transform:uppercase" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- PRODUKSI -->
                                    <div class="card mb-4">
                                        <div class="card-header" style="background-color:#b4ccbc">
                                            <h5 class="mb-0">Produksi</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="workorder">Work Order</label>
                                                        <input type="text" name="workorder" id="workorder" class="form-control" maxlength="3" style="text-transform:uppercase" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="workorderexecution">WO Execution/SPK</label>
                                                        <input type="text" name="workorderexecution" id="workorderexecution" class="form-control" maxlength="3" style="text-transform:uppercase" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="materialrelease">Material Release</label>
                                                        <input type="text" name="materialrelease" id="materialrelease" class="form-control" maxlength="3" style="text-transform:uppercase" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="bpnm">Biaya Prod Non Material</label>
                                                        <input type="text" name="bpnm" id="bpnm" class="form-control" maxlength="3" style="text-transform:uppercase" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="penerimaanbarangprod">Penerimaan Barang Prod</label>
                                                        <input type="text" name="penerimaanbarangprod" id="penerimaanbarangprod" class="form-control" maxlength="3" style="text-transform:uppercase" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="setorantarbagian">Setor Antar Bagian</label>
                                                        <input type="text" name="setorantarbagian" id="setorantarbagian" class="form-control" maxlength="3" style="text-transform:uppercase" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="pmkbarang">Pemakaian Barang</label>
                                                        <input type="text" name="pmkbarang" id="pmkbarang" class="form-control" maxlength="3" style="text-transform:uppercase" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="pnmbarang">Penerimaan Barang</label>
                                                        <input type="text" name="pnmbarang" id="pnmbarang" class="form-control" maxlength="3" style="text-transform:uppercase" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- KAS / BANK -->
                                    <div class="card mb-4">
                                        <div class="card-header text-dark" style="background-color: #f09b5a;">
                                            <h5 class="mb-0">Kas / Bank</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="kasmasuk">Kas Masuk</label>
                                                        <input type="text" name="kasmasuk" id="kasmasuk" class="form-control" maxlength="3" style="text-transform:uppercase" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="kaskeluar">Kas Keluar</label>
                                                        <input type="text" name="kaskeluar" id="kaskeluar" class="form-control" maxlength="3" style="text-transform:uppercase" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="bankmasuk">Bank Masuk</label>
                                                        <input type="text" name="bankmasuk" id="bankmasuk" class="form-control" maxlength="3" style="text-transform:uppercase" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="bankkeluar">Bank Keluar</label>
                                                        <input type="text" name="bankkeluar" id="bankkeluar" class="form-control" maxlength="3" style="text-transform:uppercase" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="setorangiro">Setoran Giro</label>
                                                        <input type="text" name="setorangiro" id="setorangiro" class="form-control" maxlength="3" style="text-transform:uppercase" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="pencairangiro">Pencairan Giro</label>
                                                        <input type="text" name="pencairangiro" id="pencairangiro" class="form-control" maxlength="3" style="text-transform:uppercase" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="tolakangiro">Tokan Giro</label>
                                                        <input type="text" name="tolakangiro" id="tolakangiro" class="form-control" maxlength="3" style="text-transform:uppercase" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="buktikaskecil">Bukti Kas Kecil</label>
                                                        <input type="text" name="buktikaskecil" id="buktikaskecil" class="form-control" maxlength="3" style="text-transform:uppercase" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- FAKTUR PAJAK -->
                                    <div class="card mb-4">
                                        <div class="card-header bg-blue text-white">
                                            <h5 class="mb-0">Faktur Pajak</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="fpm">Faktur Pajak Masukan</label>
                                                        <input type="text" name="fpm" id="fpm" class="form-control" maxlength="3" style="text-transform:uppercase" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="fpk">Faktur Pajak Keluaran</label>
                                                        <input type="text" name="fpk" id="fpk" class="form-control" maxlength="3" style="text-transform:uppercase" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="bppph">Bukti Pungut PPH</label>
                                                        <input type="text" name="bppph" id="bppph" class="form-control" maxlength="3" style="text-transform:uppercase" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- LAIN-LAIN -->
                                    <div class="card mb-4">
                                        <div class="card-header text-white" style="background-color: #5ccf79;">
                                            <h5 class="mb-0">Lain-Lain</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="notadk">Nota Debet/Kredit</label>
                                                        <input type="text" name="notadk" id="notadk" class="form-control" maxlength="3" style="text-transform:uppercase" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="jurnalumump">Jurnal Umum Perkiraan</label>
                                                        <input type="text" name="jurnalumump" id="jurnalumump" class="form-control" maxlength="3" style="text-transform:uppercase" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="ptal">Perintah Transf. Ant. Lok</label>
                                                        <input type="text" name="ptal" id="ptal" class="form-control" maxlength="3" style="text-transform:uppercase" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="koreksihargajual">Koreksi Harga Jual</label>
                                                        <input type="text" name="koreksihargajual" id="koreksihargajual" class="form-control" maxlength="3" style="text-transform:uppercase" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="adjusmentstock">Adjustment Stock</label>
                                                        <input type="text" name="adjusmentstock" id="adjusmentstock" class="form-control" maxlength="3" style="text-transform:uppercase" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="perkiraan-content" role="tabpanel">
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="hpp">Harga Pokok Penjualan</label>
                                        <select name="hpp" id="hpp"
                                        class="form-control select2" disabled
                                        style="width:100%"></select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="labakurs">Laba Kurs</label>
                                        <select name="labakurs" id="labakurs"
                                        class="form-control select2" disabled
                                        style="width:100%"></select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="rugikurs">Rugi Kurs</label>
                                        <select name="rugikurs" id="rugikurs"
                                        class="form-control select2" disabled
                                        style="width:100%"></select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="ldtb">Laba Ditahan Th Berjalan</label>
                                        <select name="ldtb" id="ldtb"
                                        class="form-control select2" disabled
                                        style="width:100%"></select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="ldtl">Laba Ditahan Th Lalu</label>
                                        <select name="ldtl" id="ldtl"
                                        class="form-control select2" disabled
                                        style="width:100%"></select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="pproduksi">Perkiraan Produksi</label>
                                        <select name="pproduksi" id="pproduksi"
                                        class="form-control select2" disabled
                                        style="width:100%"></select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="default-content" role="tabpanel">
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="idtax">Pajak</label>
                                        <select name="idtax" id="idtax"
                                        class="form-control select2" disabled
                                        style="width:100%"></select>
                                    </div>
                                </div>
                                <div class="col-md-2" style="margin-top: 30px;">
                                    <div class="form-group">
                                        <!-- Radio untuk Interngroup -->
                                        <div class="form-check">
                                            <input class="form-check-input" 
                                                type="checkbox" 
                                                disabled
                                                name="ispajak" 
                                                id="ispajak"
                                                <?= isset($data['ispajak']) && $data['ispajak'] === 'YES' ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="ispajak">
                                                Harga Sudah Termasuk Pajak
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="currcode">Mata Uang</label>
                                        <select name="currcode" id="currcode"
                                        class="form-control select2" disabled
                                        style="width:100%"></select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="gudang">Gudang</label>
                                        <select name="gudang" id="gudang"
                                        class="form-control select2" disabled
                                        style="width:100%"></select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="kaskecil">Kas Kecil</label>
                                        <select name="kaskecil" id="kaskecil"
                                        class="form-control select2" disabled
                                        style="width:100%"></select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="pkas">Perk. Kas</label>
                                        <select name="pkas" id="pkas"
                                        class="form-control select2" disabled
                                        style="width:100%"></select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="ppersediaan">Perk. Persediaan</label>
                                        <select name="ppersediaan" id="ppersediaan"
                                        class="form-control select2" disabled
                                        style="width:100%"></select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="psj">Perk. Surat Jalan</label>
                                        <select name="psj" id="psj"
                                        class="form-control select2" disabled
                                        style="width:100%"></select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="pselisih">Perk. Selisih</label>
                                        <select name="pselisih" id="pselisih"
                                        class="form-control select2" disabled
                                        style="width:100%"></select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="gudangretail">Gudang Retail</label>
                                        <select name="gudangretail" id="gudangretail"
                                        class="form-control select2" disabled
                                        style="width:100%"></select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="pmutasimasuk">Perk. Mutasi Masuk</label>
                                        <select name="pmutasimasuk" id="pmutasimasuk"
                                        class="form-control select2" disabled
                                        style="width:100%"></select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="pmutasikeluar">Perk. Mutasi Keluar</label>
                                        <select name="pmutasikeluar" id="pmutasikeluar"
                                        class="form-control select2" disabled
                                        style="width:100%"></select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="prefixnofp">Prefix No. Faktur Pajak</label>
                                        <input type="text" name="prefixnofp" id="prefixnofp" class="form-control" readonly>
                                    </div>
                                </div>
                                <div class="col-md-2" style="margin-top: 30px;">
                                    <div class="form-group">
                                        <!-- Radio untuk Interngroup -->
                                        <div class="form-check">
                                            <input class="form-check-input" 
                                                type="checkbox" 
                                                disabled
                                                name="sembunyilokasi" 
                                                id="sembunyilokasi"
                                                <?= isset($data['sembunyilokasi']) && $data['sembunyilokasi'] === 'YES' ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="sembunyilokasi">
                                                Sembunyikan Lokasi Pada Transaksi
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div><!-- /.card-body -->
            </form>
		</div><!-- /.card -->
	</div>
</div>




<script type="application/javascript" src="<?= base_url('assets/pagejs/tools/konfigurasi.js') ?>"></script>
<script type="text/javascript">
    $(function() {
        $("#example1").dataTable();
        $("#example2").dataTable();
        $("#example4").dataTable();
        //datemask
        //$("#datemaskinput").inputmask("dd/mm/yyyy", {"placeholder": "dd/mm/yyyy"});
        //$("#datemaskinput").daterangepicker();
        //Date picker

        $('#periode').datepicker({
            format: "yymm",
            viewMode: "months",
            minViewMode: "months",
            autoclose: true
        });

        $(".tglrange").daterangepicker({
            autoUpdateInput: false,
            locale: {
                cancelLabel: 'Clear'
            }
        });

        $(".tglrange").on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD-MM-YYYY') + ' - ' + picker.endDate.format('DD-MM-YYYY'));
        });

        $(".tglrange").on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });

        $('#dateinputx').daterangepicker({
            autoUpdateInput: false,
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: 'DD-M-YYYY'
            },
            cancelLabel: 'Clear',
        });
        $('#dateinputx').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD-M-YYYY'));
        });

        $('#dateinputx').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });
    });

</script>