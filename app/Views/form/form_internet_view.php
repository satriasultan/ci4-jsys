<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
<!-- form_perangkat_view.php -->
<style>
    .section-block {
        background-color: #f9f9fc;
        border-left: 4px solid #007bff;
        padding: 20px;
        border-radius: 6px;
        margin-bottom: 30px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.04);
        transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1),
        box-shadow 0.4s ease,
        border-color 0.4s ease;
        transform: scale(1);
        will-change: transform;
    }
    .section-header {
        font-weight: bold;
        color: #007bff;
        margin-bottom: 20px;
        font-size: 1.1rem;
        display: flex;
        align-items: center;
    }
    .section-header i {
        margin-right: 8px;
    }
    .section-block:hover {
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        border-left-color: #0056b3;
        border-bottom-color: #0056b3;
    }
</style>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Form Perangkat</title>

    <!-- Bootstrap 4 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container-fluid mt-5 text-center">
    <div class="row">
        <!-- left column -->
        <div class="col-md-12">
            <div class="card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        Form Permintaan Akses Jaringan & System
                    </h3>
                </div>

                <div class="card-body" style="background-color: #e2e2f9;">
                    <div class="section-block">
                        <div class="section-header">
                            <i class="fa fa-info-circle"></i> Status
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-12">
                                <div class="text-center">
                                <span style="font-size:21px" class="badge <?= $badgeClass ?> w-100">
                                    <?= htmlspecialchars($status) ?>
                                </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="section-block">
                        <div class="section-header">
                            <i class="fa fa-info-circle"></i> Informasi Akses Jaringan & System
                        </div>
                        <?php if (trim($status) === 'DIIZINKAN'): ?>
                            <div class="row mb-2">
                                <div class="col-md-4">
                                    <label><strong>No. Dokumen</strong></label>
                                    <div><?= esc(trim($form->docno ?? '')) ?></div>
                                </div>
                                <div class="col-md-4">
                                    <label><strong>Nama Pemohon</strong></label>
                                    <div><?= esc(trim($form->nmpemohon ?? '')) ?></div>
                                </div>
                                <div class="col-md-4">
                                    <label><strong>Tanggal Permintaan</strong></label>
                                    <div><?= esc(trim($form->docdate ?? '')) ?></div>
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-md-4">
                                    <label><strong>Jenis Akses</strong></label>
                                    <div>
                                        <?php
                                        $aksesList = [];

                                        if (!empty($form->aksesuser) && ($form->aksesuser === true || $form->aksesuser === 't' || $form->aksesuser == 1)) {
                                            $aksesList[] = 'User';
                                        }
                                        if (!empty($form->aksesinternet) && ($form->aksesinternet === true || $form->aksesinternet === 't' || $form->aksesinternet == 1)) {
                                            $aksesList[] = 'Internet';
                                        }
                                        if (!empty($form->aksesemail) && ($form->aksesemail === true || $form->aksesemail === 't' || $form->aksesemail == 1)) {
                                            $aksesList[] = 'Email';
                                        }
                                        if (!empty($form->aksesapp) && ($form->aksesapp === true || $form->aksesapp === 't' || $form->aksesapp == 1)) {
                                            $aksesList[] = 'Aplikasi';
                                        }
                                        if (!empty($form->aksesother) && ($form->aksesother === true || $form->aksesother === 't' || $form->aksesother == 1)) {
                                            $aksesList[] = 'Lain-lain';
                                        }

                                        echo esc(implode(', ', $aksesList));
                                        ?>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label><strong>Perangkat Terdaftar</strong></label>
                                    <div><?= esc(trim($form->perangkatregist ?? '')) ?></div>
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-md-4">
                                    <label><strong>Sifat Akses</strong></label>
                                    <div><?= esc(trim($form->sifatakses ?? '')) ?></div>
                                </div>
                                <div class="col-md-4">
                                    <label><strong>Expired Akses</strong></label>
                                    <div><?= esc(trim($form->expdate ?? '')) ?></div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="row mb-2">
                                <div class="col-md-12">
                                    <div class="alert alert-warning text-center mb-0" style="font-size:12px;">
                                        <strong>Barcode tidak aktif</strong> karena status <strong><?= htmlspecialchars($status) ?></strong>,
                                        sehingga informasi akses tidak dapat kami muat. Silahkan menghubungi departemen IT PT Jatim Taman Steel, MFG
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-12">
                        <span style=" text-align:center; font-size:12px;">
                            &copy; 2025 PT Jatim Taman Steel. All rights reserved.
                        </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>
</body>
</html>



