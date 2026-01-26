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
                <?php if (!empty($perm['input'])) : ?>
                    <button type="button" class="btn btn-success" id="btnAddChild" onclick="addChild()">
                        <i class="fa fa-plus"></i> Add Child COA
                    </button>
                <?php endif; ?>

                <?php if (!empty($perm['update'])) : ?>
                    <button type="button" class="btn btn-warning" id="btnEditChild" onclick="editChild()">
                        <i class="fa fa-edit"></i> Edit COA
                    </button>
                <?php endif; ?>

                <?php if (!empty($perm['delete'])) : ?>
                    <button type="button" class="btn btn-danger" id="btnDeleteCoa" onclick="deleteChild()">
                        <i class="fa fa-trash"></i> Delete COA
                    </button>
                <?php endif; ?>

            </div>
        </div>
    </div>
    <form id="formCoa">
        <div class="form-group">
            <label>ID Chart of Account</label>
            <input type="text" name="idcoa" class="form-control" value="<?= esc($coa->idcoa) ?>" readonly>
        </div>
    
        <div class="form-group">
            <label>Nama Chart of Account</label>
            <input type="text" style="text-transform: uppercase;" name="nmcoa" class="form-control" value="<?= esc($coa->nmcoa) ?>">
        </div>
    
        <div class="form-group">
            <label>Nama COA 2</label>
            <input type="text" style="text-transform: uppercase;" name="nm2coa" class="form-control" value="<?= esc($coa->nm2coa) ?>">
        </div>
    
        <div class="form-group">
            <label>Induk</label>
            <input type="text" name="induk" class="form-control" value="<?= esc($coa->induk) ?>" readonly>
        </div>
    
        <div class="form-group">
            <label>Level</label>
            <input type="text" name="level" class="form-control" value="<?= esc($coa->level) ?>" readonly>
        </div>
    
        <div class="form-group">
            <label>
                <input type="checkbox"
                name="isdetail"
                value="1"
                <?= isset($coa) && $coa->isdetail == '1' ? 'checked' : '' ?>>
                &nbsp;Is Detail
            </label>
        </div>
            
        <div class="form-group">
            <label>Group COA</label>
            <div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="groupcoa" id="aktiva" value="AKTIVA" <?= (isset($coa->groupcoa) && strtoupper(trim($coa->groupcoa)) == 'AKTIVA') ? 'checked' : '' ?>>
                    <label class="form-check-label" for="aktiva">Aktiva</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="groupcoa" id="pasiva" value="PASIVA" <?= (isset($coa->groupcoa) && strtoupper(trim($coa->groupcoa)) == 'PASIVA') ? 'checked' : '' ?>>
                    <label class="form-check-label" for="pasiva">Pasiva</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="groupcoa" id="modal" value="MODAL" <?= (isset($coa->groupcoa) && strtoupper(trim($coa->groupcoa)) == 'MODAL') ? 'checked' : '' ?>>
                    <label class="form-check-label" for="modal">Modal</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="groupcoa" id="pendapatan" value="PENDAPATAN" <?= (isset($coa->groupcoa) && strtoupper(trim($coa->groupcoa)) == 'PENDAPATAN') ? 'checked' : '' ?>>
                    <label class="form-check-label" for="pendapatan">Pendapatan</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="groupcoa" id="biaya" value="BIAYA" <?= (isset($coa->groupcoa) && strtoupper(trim($coa->groupcoa)) == 'BIAYA') ? 'checked' : '' ?>>
                    <label class="form-check-label" for="biaya">Biaya</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="groupcoa" id="pdlu" value="PDLU" <?= (isset($coa->groupcoa) && strtoupper(trim($coa->groupcoa)) == 'PDLU') ? 'checked' : '' ?>>
                    <label class="form-check-label" for="pdlu">Pendapatan di Luar Usaha</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="groupcoa" id="bdlu" value="BDLU" <?= (isset($coa->groupcoa) && strtoupper(trim($coa->groupcoa)) == 'BDLU') ? 'checked' : '' ?>>
                    <label class="form-check-label" for="bdlu">Biaya di Luar Usaha</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="groupcoa" id="lain" value="LAIN" <?= (isset($coa->groupcoa) && strtoupper($coa->groupcoa) == 'LAIN') ? 'checked' : '' ?>>
                    <label class="form-check-label" for="lain">Lain-Lain</label>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label>Currency</label>
            <select name="idcur" id="idcur" class="form-control" required>
            </select>
            <input type="hidden" id="coa_idcur" value="<?= trim($coa->idcur) ?>">
        </div>
    
        <div class="form-group">
            <label>Jenis</label>
            <select name="jenis" class="form-control">
                <option value="" disabled <?= empty($coa->jenis) ? 'selected' : '' ?>>-- Pilih Jenis --</option>
                <option value="LAIN" <?= (isset($coa->jenis) && trim($coa->jenis) === 'LAIN') ? 'selected' : '' ?>>LAIN-LAIN</option>
                <option value="KAS"  <?= (isset($coa->jenis) && trim($coa->jenis) === 'KAS')  ? 'selected' : '' ?>>KAS</option>
                <option value="BANK" <?= (isset($coa->jenis) && trim($coa->jenis) === 'BANK') ? 'selected' : '' ?>>BANK</option>
                <option value="ARAP" <?= (isset($coa->jenis) && trim($coa->jenis) === 'ARAP') ? 'selected' : '' ?>>AR/AP</option>
            </select>
        </div>
    
        <button type="submit" id="submitCOA" class="btn btn-primary">Simpan</button>
    </form>
</div>
<script type="application/javascript" src="<?= base_url('assets/pagejs/master/coa/coa.js') ?>"></script>
<!-- 
<script type="text/javascript">
    



</script> -->