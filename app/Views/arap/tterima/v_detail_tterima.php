<style>

    .section-block {

        background: #f8f9fa;

        border-left:
                4px solid #0d6efd;

        padding: 20px;

        border-radius: 6px;

        margin-bottom: 20px;

        box-shadow:
                0 2px 4px rgba(0,0,0,.04);
    }


    .section-header {

        font-weight: 700;

        color: #0d6efd;

        margin-bottom: 20px;

        font-size: 1.05rem;
    }


    .section-header i {

        margin-right: 8px;
    }


    .form-control:disabled,
    .form-control[readonly],
    .form-select:disabled,
    textarea:disabled {

        background-color: #e9ecef;

        opacity: 1;

        color: #212529;

        cursor: default;
    }


    .ratakanan {

        text-align: right;
    }


    #tableTterimaDetail tfoot th {

        background: #fff3cd;

        font-weight: bold;
    }


    .detail-summary {

        font-size: 15px;

        font-weight: 600;
    }


    .status-final {

        background-color: #198754;

        color: #fff;

        font-weight: bold;

        padding:
                5px 12px;

        border-radius: 4px;
    }


    .status-posted {

        background-color: #0d6efd;

        color: #fff;

        font-weight: bold;

        padding:
                5px 12px;

        border-radius: 4px;
    }


    .status-draft {

        background-color: #6c757d;

        color: #fff;

        font-weight: bold;

        padding:
                5px 12px;

        border-radius: 4px;
    }


    .detail-readonly {

        background-color: #fff;
    }

</style>


<div class="content-header">

    <div class="container-fluid">

        <div class="row mb-3">

            <div class="col-md-6">

                <h1 class="m-0">

                    <?= esc(
                            ucwords(
                                    strtolower(
                                            trim(
                                                    $title ??
                                                    'Detail Tanda Terima Supplier'
                                            )
                                    )
                            )
                    ) ?>

                </h1>

            </div>


            <div class="col-md-6">

                <ol class="breadcrumb float-end">

                    <?php foreach (($y ?? []) as $y1): ?>

                        <?php if (
                                trim($y1->kodemenu)
                                !=
                                trim($kodemenu ?? '')
                        ): ?>

                            <li class="breadcrumb-item">

                                <a
                                        href="<?= base_url(
                                                trim($y1->linkmenu)
                                        ) ?>"
                                >

                                    <i
                                            class="fa <?= esc(
                                                    trim($y1->iconmenu)
                                            ) ?>"
                                    ></i>

                                    <?= esc(
                                            trim(
                                                    $y1->namamenu
                                            )
                                    ) ?>

                                </a>

                            </li>

                        <?php else: ?>

                            <li class="breadcrumb-item active">

                                <i
                                        class="fa <?= esc(
                                                trim($y1->iconmenu)
                                        ) ?>"
                                ></i>

                                <?= esc(
                                        trim(
                                                $y1->namamenu
                                        )
                                ) ?>

                            </li>

                        <?php endif; ?>

                    <?php endforeach; ?>

                </ol>

            </div>

        </div>

    </div>

</div>


<?= $message ?? '' ?>


<?php

$dtldata =
        $dtldata ?? [];

$detaildata =
        $detaildata ?? [];

$docno =
        trim(
                $dtldata['docno'] ??
                $docno ??
                ''
        );

?>


<form
        id="formTterimaDetail"
        autocomplete="off"
>


    <!-- =====================================================
         HEADER
    ====================================================== -->

    <div class="card border-primary mb-3">

        <div class="card-header bg-primary text-white">

            <h5 class="mb-0">

                <i class="fa fa-file-invoice"></i>

                Header Tanda Terima Supplier

            </h5>

        </div>


        <div class="card-body">


            <div class="section-block">


                <div class="section-header">

                    <i class="fa fa-address-card"></i>

                    Informasi Dokumen

                </div>


                <div class="row g-3">


                    <!-- =================================================
                         CABANG
                    ================================================== -->

                    <div class="col-md-3">

                        <label
                                for="cabang"
                                class="form-label"
                        >
                            Cabang / Job
                        </label>


                        <input
                                type="text"
                                name="cabang"
                                id="cabang"
                                class="form-control"
                                value="<?= esc(
                                        trim(
                                                $dtldata['cabang']
                                                ?? ''
                                        )
                                ) ?>"
                                disabled
                        >

                    </div>


                    <!-- =================================================
                         NO DOKUMEN
                    ================================================== -->

                    <div class="col-md-3">

                        <label
                                class="form-label"
                        >
                            No. Jurnal Tanda Terima
                        </label>


                        <input
                                type="text"
                                name="docno"
                                id="docno"
                                class="form-control"
                                value="<?= esc(
                                        $docno
                                ) ?>"
                                disabled
                        >

                    </div>


                    <!-- =================================================
                         TANGGAL
                    ================================================== -->

                    <div class="col-md-3">

                        <label
                                for="docdate"
                                class="form-label"
                        >
                            Tanggal Dokumen
                        </label>


                        <input
                                type="text"
                                name="docdate"
                                id="docdate"
                                class="form-control"
                                value="<?= esc(
                                        trim(
                                                $dtldata['docdate']
                                                ?? ''
                                        )
                                ) ?>"
                                disabled
                        >

                    </div>


                    <!-- =================================================
                         STATUS
                    ================================================== -->

                    <div class="col-md-3">

                        <label
                                for="status"
                                class="form-label"
                        >
                            Status
                        </label>


                        <?php

                        $status =
                                strtoupper(
                                        trim(
                                                $dtldata['status']
                                                ?? ''
                                        )
                                );

                        $statusText =
                                $status;

                        if ($status === 'F') {

                            $statusText =
                                    'FINAL';

                        } elseif ($status === 'P') {

                            $statusText =
                                    'POSTED';

                        } elseif ($status === 'E') {

                            $statusText =
                                    'DRAFT';
                        }

                        ?>


                        <input
                                type="text"
                                id="status"
                                class="form-control"
                                value="<?= esc(
                                        $statusText
                                ) ?>"
                                disabled
                        >

                    </div>


                    <!-- =================================================
                         SUPPLIER
                    ================================================== -->

                    <div class="col-md-4">

                        <label
                                for="kdsupplier"
                                class="form-label"
                        >
                            Supplier
                        </label>


                        <input
                                type="text"
                                name="kdsupplier"
                                id="kdsupplier"
                                class="form-control"
                                value="<?= esc(
                                        trim(
                                                $dtldata['kdsupplier']
                                                ?? ''
                                        )
                                ) ?>"
                                disabled
                        >

                    </div>


                    <!-- =================================================
                         NAMA SUPPLIER
                    ================================================== -->

                    <div class="col-md-4">

                        <label
                                for="nmsupplier"
                                class="form-label"
                        >
                            Nama Supplier
                        </label>


                        <input
                                type="text"
                                name="nmsupplier"
                                id="nmsupplier"
                                class="form-control"
                                value="<?= esc(
                                        trim(
                                                $dtldata['nmsupplier']
                                                ?? ''
                                        )
                                ) ?>"
                                disabled
                        >

                    </div>


                    <!-- =================================================
                         KOTA SUPPLIER
                    ================================================== -->

                    <div class="col-md-4">

                        <label
                                for="kotasupplier"
                                class="form-label"
                        >
                            Kota Supplier
                        </label>


                        <input
                                type="text"
                                name="kotasupplier"
                                id="kotasupplier"
                                class="form-control"
                                value="<?= esc(
                                        trim(
                                                $dtldata['kotasupplier']
                                                ?? ''
                                        )
                                ) ?>"
                                disabled
                        >

                    </div>


                    <!-- =================================================
                         ALAMAT
                    ================================================== -->

                    <div class="col-md-6">

                        <label
                                for="alamatsupplier"
                                class="form-label"
                        >
                            Alamat Supplier
                        </label>


                        <textarea
                                name="alamatsupplier"
                                id="alamatsupplier"
                                class="form-control"
                                rows="2"
                                disabled
                        ><?= esc(
                                    trim(
                                            $dtldata['alamatsupplier']
                                            ?? ''
                                    )
                            ) ?></textarea>

                    </div>


                    <!-- =================================================
                         INVOICE
                    ================================================== -->

                    <div class="col-md-3">

                        <label
                                for="noinvoice"
                                class="form-label"
                        >
                            No. Invoice
                        </label>


                        <input
                                type="text"
                                name="noinvoice"
                                id="noinvoice"
                                class="form-control"
                                value="<?= esc(
                                        trim(
                                                $dtldata['noinvoice']
                                                ?? ''
                                        )
                                ) ?>"
                                disabled
                        >

                    </div>


                    <div class="col-md-3">

                        <label
                                for="tglinvoice"
                                class="form-label"
                        >
                            Tanggal Invoice
                        </label>


                        <input
                                type="text"
                                name="tglinvoice"
                                id="tglinvoice"
                                class="form-control"
                                value="<?= esc(
                                        trim(
                                                $dtldata['tglinvoice']
                                                ?? ''
                                        )
                                ) ?>"
                                disabled
                        >

                    </div>


                    <!-- =================================================
                         SURAT JALAN
                    ================================================== -->

                    <div class="col-md-3">

                        <label
                                for="nosj"
                                class="form-label"
                        >
                            No. Surat Jalan
                        </label>


                        <input
                                type="text"
                                name="nosj"
                                id="nosj"
                                class="form-control"
                                value="<?= esc(
                                        trim(
                                                $dtldata['nosj']
                                                ?? ''
                                        )
                                ) ?>"
                                disabled
                        >

                    </div>


                    <div class="col-md-3">

                        <label
                                for="tglsj"
                                class="form-label"
                        >
                            Tanggal Surat Jalan
                        </label>


                        <input
                                type="text"
                                name="tglsj"
                                id="tglsj"
                                class="form-control"
                                value="<?= esc(
                                        trim(
                                                $dtldata['tglsj']
                                                ?? ''
                                        )
                                ) ?>"
                                disabled
                        >

                    </div>


                    <!-- =================================================
                         NO AJU
                    ================================================== -->

                    <div class="col-md-3">

                        <label
                                for="noaju"
                                class="form-label"
                        >
                            No. AJU
                        </label>


                        <input
                                type="text"
                                name="noaju"
                                id="noaju"
                                class="form-control"
                                value="<?= esc(
                                        trim(
                                                $dtldata['noaju']
                                                ?? ''
                                        )
                                ) ?>"
                                disabled
                        >

                    </div>


                    <!-- =================================================
                         NO BL
                    ================================================== -->

                    <div class="col-md-3">

                        <label
                                for="nobl"
                                class="form-label"
                        >
                            No. BL
                        </label>


                        <input
                                type="text"
                                name="nobl"
                                id="nobl"
                                class="form-control"
                                value="<?= esc(
                                        trim(
                                                $dtldata['nobl']
                                                ?? ''
                                        )
                                ) ?>"
                                disabled
                        >

                    </div>


                    <!-- =================================================
                         NO AWB
                    ================================================== -->

                    <div class="col-md-3">

                        <label
                                for="noawb"
                                class="form-label"
                        >
                            No. AWB
                        </label>


                        <input
                                type="text"
                                name="noawb"
                                id="noawb"
                                class="form-control"
                                value="<?= esc(
                                        trim(
                                                $dtldata['noawb']
                                                ?? ''
                                        )
                                ) ?>"
                                disabled
                        >

                    </div>


                    <!-- =================================================
                         BK REV
                    ================================================== -->

                    <div class="col-md-3">

                        <label
                                for="nobkrev"
                                class="form-label"
                        >
                            No. BK Rev
                        </label>


                        <input
                                type="text"
                                name="nobkrev"
                                id="nobkrev"
                                class="form-control"
                                value="<?= esc(
                                        trim(
                                                $dtldata['nobkrev']
                                                ?? ''
                                        )
                                ) ?>"
                                disabled
                        >

                    </div>


                    <!-- =================================================
                         FAKTUR PAJAK
                    ================================================== -->

                    <div class="col-md-4">

                        <label
                                for="nofakturpajak"
                                class="form-label"
                        >
                            No. Faktur Pajak
                        </label>


                        <input
                                type="text"
                                name="nofakturpajak"
                                id="nofakturpajak"
                                class="form-control"
                                value="<?= esc(
                                        trim(
                                                $dtldata['nofakturpajak']
                                                ?? ''
                                        )
                                ) ?>"
                                disabled
                        >

                    </div>


                    <!-- =================================================
                         CURRENCY
                    ================================================== -->

                    <div class="col-md-2">

                        <label
                                for="currcode"
                                class="form-label"
                        >
                            Mata Uang
                        </label>


                        <input
                                type="text"
                                name="currcode"
                                id="currcode"
                                class="form-control"
                                value="<?= esc(
                                        trim(
                                                $dtldata['currcode']
                                                ?? 'IDR'
                                        )
                                ) ?>"
                                disabled
                        >

                    </div>


                    <!-- =================================================
                         KURS
                    ================================================== -->

                    <div class="col-md-2">

                        <label
                                for="kurs"
                                class="form-label"
                        >
                            Kurs
                        </label>


                        <input
                                type="text"
                                name="kurs"
                                id="kurs"
                                class="form-control ratakanan"
                                value="<?= esc(
                                        $dtldata['kurs']
                                        ?? 1
                                ) ?>"
                                disabled
                        >

                    </div>


                    <!-- =================================================
                         JATUH TEMPO
                    ================================================== -->

                    <div class="col-md-2">

                        <label
                                for="jthtempo"
                                class="form-label"
                        >
                            Jatuh Tempo
                        </label>


                        <input
                                type="text"
                                name="jthtempo"
                                id="jthtempo"
                                class="form-control ratakanan"
                                value="<?= esc(
                                        $dtldata['jthtempo']
                                        ?? 0
                                ) ?>"
                                disabled
                        >

                    </div>


                    <div class="col-md-2">

                        <label
                                for="tgljthtempo"
                                class="form-label"
                        >
                            Tgl. Jatuh Tempo
                        </label>


                        <input
                                type="text"
                                name="tgljthtempo"
                                id="tgljthtempo"
                                class="form-control"
                                value="<?= esc(
                                        trim(
                                                $dtldata['tgljthtempo']
                                                ?? ''
                                        )
                                ) ?>"
                                disabled
                        >

                    </div>


                    <!-- =================================================
                         DPP
                    ================================================== -->

                    <div class="col-md-3">

                        <label
                                for="dpp"
                                class="form-label"
                        >
                            DPP
                        </label>


                        <input
                                type="text"
                                name="dpp"
                                id="dpp"
                                class="form-control ratakanan"
                                value="<?= esc(
                                        $dtldata['dpp']
                                        ?? 0
                                ) ?>"
                                disabled
                        >

                    </div>


                    <!-- =================================================
                         PAJAK
                    ================================================== -->

                    <div class="col-md-3">

                        <label
                                for="jumlahpajak"
                                class="form-label"
                        >
                            Pajak
                        </label>


                        <input
                                type="text"
                                name="jumlahpajak"
                                id="jumlahpajak"
                                class="form-control ratakanan"
                                value="<?= esc(
                                        $dtldata['jumlahpajak']
                                        ?? 0
                                ) ?>"
                                disabled
                        >

                    </div>


                    <!-- =================================================
                         TOTAL
                    ================================================== -->

                    <div class="col-md-3">

                        <label
                                for="total"
                                class="form-label"
                        >
                            Total
                        </label>


                        <input
                                type="text"
                                name="total"
                                id="total"
                                class="form-control ratakanan fw-bold"
                                value="<?= esc(
                                        $dtldata['total']
                                        ?? 0
                                ) ?>"
                                disabled
                        >

                    </div>


                    <!-- =================================================
                         BALANCE
                    ================================================== -->

                    <div class="col-md-3">

                        <label
                                for="balance"
                                class="form-label"
                        >
                            Balance
                        </label>


                        <input
                                type="text"
                                name="balance"
                                id="balance"
                                class="form-control ratakanan fw-bold"
                                value="<?= esc(
                                        $dtldata['balance']
                                        ?? 0
                                ) ?>"
                                disabled
                        >

                    </div>


                    <!-- =================================================
                         KETERANGAN
                    ================================================== -->

                    <div class="col-md-12">

                        <label
                                for="keterangan"
                                class="form-label"
                        >
                            Keterangan
                        </label>


                        <textarea
                                name="keterangan"
                                id="keterangan"
                                class="form-control"
                                rows="2"
                                disabled
                        ><?= esc(
                                    trim(
                                            $dtldata['keterangan']
                                            ?? ''
                                    )
                            ) ?></textarea>

                    </div>


                </div>

            </div>

        </div>

    </div>


    <!-- =====================================================
         DETAIL
    ====================================================== -->

    <div class="card border-primary mb-3">

        <div class="card-header bg-primary text-white">

            <h5 class="mb-0">

                <i class="fa fa-list"></i>

                Detail Jurnal Akuntansi

            </h5>

        </div>


        <div class="card-body">

            <div class="table-responsive">

                <table
                        id="tableTterimaDetail"
                        class="table table-bordered table-striped table-hover w-100"
                >

                    <thead
                            class="table-primary text-center"
                    >

                    <tr>

                        <th style="width:40px">
                            No.
                        </th>

                        <th style="width:130px">
                            No. Bukti
                        </th>

                        <th style="width:140px">
                            Kode Perkiraan
                        </th>

                        <th style="min-width:220px">
                            Nama Perkiraan
                        </th>

                        <th style="min-width:250px">
                            Keterangan
                        </th>

                        <th style="width:90px">
                            D/K
                        </th>

                        <th style="width:150px">
                            Cost Center
                        </th>

                        <th style="width:150px">
                            Nilai
                        </th>

                    </tr>

                    </thead>


                    <tbody></tbody>


                    <tfoot>

                    <tr>

                        <th
                                colspan="7"
                                class="text-end"
                        >
                            TOTAL DEBIT
                        </th>

                        <th
                                id="totalDebit"
                                class="text-end"
                        >
                            0.00
                        </th>

                    </tr>


                    <tr>

                        <th
                                colspan="7"
                                class="text-end"
                        >
                            TOTAL KREDIT
                        </th>

                        <th
                                id="totalKredit"
                                class="text-end"
                        >
                            0.00
                        </th>

                    </tr>


                    <tr>

                        <th
                                colspan="7"
                                class="text-end"
                        >
                            SELISIH
                        </th>

                        <th
                                id="totalSelisih"
                                class="text-end"
                        >
                            0.00
                        </th>

                    </tr>

                    </tfoot>

                </table>

            </div>

        </div>

    </div>

    <!-- =====================================================
         FOOTER ACTION
    ====================================================== -->
    <div class="card mb-3 border-0">
        <div class="card-body px-0 py-2">
            <div class="d-flex justify-content-start align-items-center gap-2">

                <!-- KEMBALI -->
                <a href="<?= base_url('arap/transaksi/tterima'); ?>"
                   class="btn btn-secondary">
                    <i class="fa fa-arrow-left me-1"></i>
                    Kembali
                </a>

                <!-- HAPUS TANDA TERIMA -->
                <button type="button"
                        id="btnDeleteTterima"
                        class="btn btn-danger"
                        onclick="deleteTterima()">
                    <i class="fa fa-trash me-1"></i>
                    Hapus Tanda Terima
                </button>

            </div>
        </div>
    </div>

</form>
</form>


<script>

    window.tterimaDetailData =
            <?= json_encode(
                    $detaildata,
                    JSON_UNESCAPED_UNICODE |
                    JSON_UNESCAPED_SLASHES |
                    JSON_HEX_TAG |
                    JSON_HEX_APOS |
                    JSON_HEX_QUOT |
                    JSON_HEX_AMP
            ) ?>;


    window.tterimaDetailDocno =
            <?= json_encode(
                    $docno,
                    JSON_UNESCAPED_UNICODE |
                    JSON_UNESCAPED_SLASHES
            ) ?>;

</script>


<script
        src="<?= base_url(
                'assets/pagejs/arap/tterima_detail.js'
        ) ?>"
></script>