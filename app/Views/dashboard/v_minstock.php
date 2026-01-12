<?php
$this->session = \Config\Services::session();
$this->fiky_encryption = new \App\Libraries\Fiky_encryption();
?>
<?php echo $message;?>
<?php echo $showUnfinish; ?>

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                </div><!-- /.card-header -->
                <div class="card-body table-responsive" style='overflow-x:scroll;'>
                    <table id="tminimumstock" class="table table-bordered table-striped" >
                        <thead>
                        <tr>
                            <th width="1%">No.</th>
                            <th>Id Barang</th>
                            <th>Nama</th>
                            <th>Parameter minimal</th>
                            <th>Stock Saat Ini</th>
                            <th>Unit</th>

                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div><!-- /.card-body -->
            </div><!-- /.card -->
        </div>
    </div>
    <script type="application/javascript" src="<?= base_url('assets/pagejs/dashboard/dashboard.js') ?>"></script>
