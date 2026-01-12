
<?php echo $message; ?>
<div class="row">
    <div class="col-md-3">

        <!-- Profile Image -->
        <div class="card card-primary card-outline">
            <div class="card-body card-profile">
                <div class="text-center">
                    <img class="profile-user-img img-fluid img-circle"
                         src="<?php echo base_url('assets/img/profile').'/'.trim($userinfo['image']); ?>"
                         alt="User profile picture">
                </div>

                <h3 class="profile-username text-center"><?= trim($userinfo['username']) ?></h3>

                <p class="text-muted text-center"><?= trim($userinfo['rolename']) ?> - Location : <?= trim($userinfo['loccode']) ?></p>

                <a href="#" class="btn btn-primary btn-block"><b>Follow</b></a>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->


    </div>
    <!-- /.col -->
    <div class="col-md-9">
        <div class="card">
            <div class="card-header p-2">
                <ul class="nav nav-pills">
                    <li class="nav-item"><a class="nav-link active" href="#settings" data-bs-toggle="tab">Settings</a></li>
                    <li class="nav-item"><a class="nav-link" href="#activity" data-bs-toggle="tab">Activity</a></li>
                </ul>
            </div><!-- /.card-header -->
            <div class="card-body">
                <div class="tab-content">
                    <div class=" tab-pane" id="activity">
                        #No Activity Recorded
                    </div>

                    <div class="active tab-pane" id="settings">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="card card-danger">
                                    <div class="card-body">
                                        <div class="form-horizontal">
                                            <form action="<?php echo base_url('profile/saveprofile')?>" method="post">
                                            <div class="form-group">
                                                <label class="col-sm-4">ID</label>
                                                <div class="col-sm-8">
                                                    <input type="input" class="form-control input-sm" value="<?php echo trim($userinfo['iduser']);?>" name="iduser" readonly>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-4">Username</label>
                                                <div class="col-sm-8">
                                                    <input type="hidden" class="form-control input-sm" value="edit" id="tipe" name="tipe" required>
                                                    <input type="hidden" class="form-control input-sm" value="<?php echo trim($userinfo['username']); ?>" id="username" name="username" required>
                                                    <input type="input" class="form-control input-sm" value="<?php echo trim($userinfo['username']);?>" name="user" readonly>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-4">Password</label>
                                                <div class="col-sm-8">
                                                    <input type="password" class="form-control input-sm" id="password1" name="passwordweb" title="Masukkan Password Baru Anda(Harus Memiliki Huruf Besar,Kecil & Simbol)" placeholder="Masukkan Password Baru Anda (Harus Memiliki Huruf Besar,Kecil & Simbol)" required>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-4">Repeat Password</label>
                                                <div class="col-sm-8">
                                                    <input type="password" id="password2" class="form-control input-sm" name="passwordweb2" title="Masukan Ulang Password Sama dengan sebelumnya(Harus Memiliki Huruf Besar,Kecil & Simbol)" placeholder="Masukkan Ulang Password Baru Anda (Harus Memiliki Huruf Besar,Kecil & Simbol)"  required>
                                                </div>
                                            </div>

                                                <button type="submit" onclick="return confirm('Anda Yakin Password Akan Dirubah?')" class="btn btn-primary float-right" style="margin:10px">Ubah Password</button>
                                            </form>
                                        </div>
                                    </div><!-- /.card-body -->
                                </div><!-- /.card -->
                            </div>
                        </div>
                    </div>
                    <!-- /.tab-pane -->
                </div>
                <!-- /.tab-content -->
            </div><!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
    <!-- /.col -->
</div>