<style>
    
    .section-block {
        background-color: #e8e8e8;
        border-left: 4px solid #007bff;
        /* border-bottom: 4px solid #007bff;  */
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
        /* transform: scale(1.02); */
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        border-left-color: #0056b3;
        border-bottom-color: #0056b3;
    }

    .is-invalid {
        border-color: #dc3545;
    }
    .invalid-feedback {
        display: block;
        color: #dc3545;
        font-size: 0.875em;
    }

    .form-control:disabled, .form-control[readonly] {
        background-color: #dfdfdf;
        opacity: 1;
    }

    .section-divider {
        height: 1px;
        background: linear-gradient(
            to right,
            #007bff 0%,
            #cfe2ff 30%,
            #e8e8e8 100%
        );
        margin: 24px 0;
        border: none;
    }

</style>

<div class="section-block">
    <div class="section-header">
        <div class="row">
            <div class="col-md-12">
                <i class="fa  fa-file-text"></i> Description
            </div>
        </div>
        <br>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-12">
            <div class="mb-3" id="coaToolbar">
                

                <?php if (!empty($perm['update'])) : ?>
                    <button type="button" class="btn btn-warning" id="btnEditChild" onclick="editChild()">
                        <i class="fa fa-edit"></i> Edit Job
                    </button>
                <?php endif; ?>

                

            </div>
        </div>
    </div>
    <form id="formJob">
        <div class="form-group">
            <label>ID Job/Branch</label>
            <input type="text" name="idbranch" class="form-control" value="<?= esc($job->idbranch) ?>" readonly>
        </div>
    
        <div class="form-group">
            <label>Nama Job/Branch</label>
            <input type="text" style="text-transform: uppercase;" name="nmbranch" class="form-control" value="<?= esc($job->nmbranch) ?>">
        </div>
    
        <div class="form-group">

            <div class="row">
                <div class="col-md-6">
                    <label>Induk</label>
                    <input type="text" name="induk" class="form-control" value="<?= esc($job->induk) ?>" readonly>
                </div>
                <div class="col-md-6">
                    <label>Level</label>
                    <input type="text" name="level" class="form-control" value="<?= esc($job->level) ?>" readonly>
                </div>
            </div>
        </div>
<!--         
        <div class="form-group">
        </div> -->
        <div class="form-group">
            <label>Keterangan</label>
            <textarea style="text-transform: uppercase;" rows="3" name="keterangan" class="form-control"><?= esc($job->keterangan) ?></textarea>
        </div>
        <div class="form-group">
            <label>Penghubung</label>
            <input type="text" style="text-transform: uppercase;" maxlength="100" name="penghubung" class="form-control" value="<?= esc($job->penghubung) ?>">
        </div>
        <div class="form-group">
            <label>Customer</label>
            <select name="idcustomer" id="idcustomer" class="form-control" required>
            </select>
            <input type="hidden" id="job_idcust" value="<?= trim($job->idcustomer) ?>">
        </div>
        <div class="form-group">
            <label>Alamat Kantor Customer</label>
            <textarea style="text-transform: uppercase;"  rows="3" name="alamatcust" class="form-control"><?= esc($job->alamatcust) ?></textarea>
        </div>
        <div class="form-group">
            <label>Manager</label>
            <input type="text" style="text-transform: uppercase;" maxlength="100" name="manager" class="form-control" value="<?= esc($job->manager) ?>">
        </div>
        <div class="form-group">
            <div class="row">
                <div class="col-md-6">
                    <label for="datestart">Tanggal Mulai</label>
                    <input type="text" name="datestart" class="form-control" id="datestart" placeholder="Tanggal Mulai" value="<?= esc($job->datestart) ?>">
                </div>
                <div class="col-md-6">
                    <label for="dateend">Tanggal Selesai</label>
                    <input type="text" name="dateend" class="form-control" id="dateend" placeholder="Tanggal Selesai" value="<?= esc($job->dateend) ?>">
                </div>
            </div>
        </div>
        <div class="form-group">
            <label>Persentase Pengerjaan (%)</label>
            <input
                type="number"
                name="persentase"
                class="form-control"
                min="0"
                max="100"
                step="1"
                placeholder="0 - 100"
                oninput="
                this.value = this.value.replace(/[^0-9]/g, '');
                if (this.value > 100) this.value = 100;
                "                value="<?= esc($job->persentase ?? 0) ?>"
            >
        </div>
    
        <button type="submit" id="submitJob" class="btn btn-primary">Simpan</button>
    </form>
</div>
<script type="application/javascript" src="<?= base_url('assets/pagejs/master/job/job.js') ?>"></script>

<script type="text/javascript">
      $('#datestart').daterangepicker({
            autoUpdateInput: false,
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: 'DD-MM-YYYY'
            },
            cancelLabel: 'Clear',
        });
        $('#datestart').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD-MM-YYYY'));
        });

        $('#datestart').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });



        $('#dateend').daterangepicker({
            autoUpdateInput: false,
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: 'DD-MM-YYYY'
            },
            cancelLabel: 'Clear',
        });
        $('#dateend').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD-MM-YYYY'));
        });

        $('#dateend').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });



</script>