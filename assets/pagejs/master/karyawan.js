/*
 * Created by PhpStorm.
 *  * User: FIKY-PC
 *  * Date: 12/2/20, 2:32 PM
 *  * Last Modified: 12/2/20, 2:32 PM.
 *  Developed By: Fiky Ashariza Powered By PhpStorm
 *  Copyright© 2020 .All rights reserved.
 *
 */




function editNik(e){
    Swal.fire({
        title: 'Peringatan..!!!',
        text: 'Data ini akan diubah, Anda yakin..?, Nik: ' + e,
        backdrop: true,
        allowOutsideClick: false,
        showConfirmButton: true,
        showDenyButton: true,
        showCancelButton: false,
        confirmButtonText: `Ok`,
        icon: 'question',
        //denyButtonText: `Don't save`,
    }).then((result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {
            window.location.replace(HOST_URL + 'trans/karyawan/edit_karyawan' + '/?docno=' + e)
        } else if (result.isDenied) {
            return false;
        }
    })
}
function detailNik(e){
    Swal.fire({
        title: 'Peringatan..!!!',
        text: 'Data ini akan diubah, Anda yakin..?, Nik: ' + e,
        backdrop: true,
        allowOutsideClick: false,
        showConfirmButton: true,
        showDenyButton: true,
        showCancelButton: false,
        confirmButtonText: `Ok`,
        icon: 'question',
        //denyButtonText: `Don't save`,
    }).then((result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {
            window.location.replace(HOST_URL + 'trans/karyawan/detail_karyawan' + '/?docno=' + e)
        } else if (result.isDenied) {
            return false;
        }
    })
}
function detailAtributNik(e){
    Swal.fire({
        title: 'Peringatan..!!!',
        text: 'Data ini memuat lampiran, foto,bpjs dan history karyawan,akan diubah, Anda yakin..?, Nik: ' + e,
        backdrop: true,
        allowOutsideClick: false,
        showConfirmButton: true,
        showDenyButton: true,
        showCancelButton: false,
        confirmButtonText: `Ok`,
        icon: 'question',
        //denyButtonText: `Don't save`,
    }).then((result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {
            window.location.replace(HOST_URL + 'trans/karyawan/detail_atribut_karyawan' + '/?docno=' + e)
        } else if (result.isDenied) {
            return false;
        }
    })
}
function editNikResign(e){
    Swal.fire({
        title: 'Peringatan..!!!',
        text: 'Data ini akan diubah, Anda yakin..?, Nik: ' + e,
        backdrop: true,
        allowOutsideClick: false,
        showConfirmButton: true,
        showDenyButton: true,
        showCancelButton: false,
        confirmButtonText: `Ok`,
        icon: 'question',
        //denyButtonText: `Don't save`,
    }).then((result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {
            window.location.replace(HOST_URL + 'trans/karyawan/edit_karyawan_resign' + '/?docno=' + e)
        } else if (result.isDenied) {
            return false;
        }
    })
}
function documentReadable(){
    $("#loadMe").modal({
        backdrop: "static", //remove ability to close modal with click
        keyboard: false, //remove option to close with keyboard
        show: true //Display loader!
    });
    $.ajax({
        type: 'GET',
        url: HOST_URL + 'trans/karyawan/showDetailNik' + '?docno=' + $('[name="docno"]').val(),
        dataType: 'json',
        dataFilter: function(data) {
            var json = jQuery.parseJSON(data);
            json.status = json.dataTables.status;
            json.total_count = json.dataTables.total_count;
            json.items = json.dataTables.items;
            json.incomplete_results = json.dataTables.incomplete_results;



            var nikatasan1 = (json.dataTables.items[0].nikatasan1 === '') ? 'NULL' : json.dataTables.items[0].nikatasan1 ;
            var nikatasan2 = (json.dataTables.items[0].nikatasan2 === '') ? 'NULL' : json.dataTables.items[0].nikatasan2 ;
            var nikatasan3 = (json.dataTables.items[0].nikatasan3 === '') ? 'NULL' : json.dataTables.items[0].nikatasan3 ;
            //console.log("On Ready" + json.dataTables.items[0].nik);
            // Fetch the preselected item, and add to the control
            $('[name="nik"]').val(json.dataTables.items[0].nik);
            $('[name="nmlengkap"]').val(json.dataTables.items[0].nmlengkap);
            $('[name="callname"]').val(json.dataTables.items[0].callname);
            $('[name="alamatlahir"]').val(json.dataTables.items[0].alamatlahir);
            $('[name="tgllahir"]').val(json.dataTables.items[0].tgllahir1);
            $('[name="alasan_resign"]').val(json.dataTables.items[0].alasan_resign);
            //$('[name="jk"]').val(json.dataTables.items[0].jk);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/jenis_kelamin' + '?var=' + json.dataTables.items[0].jk,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {


                // create the option and append to Select2
                var option = new Option(datax.items[0].nmkelamin, datax.items[0].idkelamin, true, true);
                $('[name="jk"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="jk"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            //$('[name="kdagama"]').val(json.dataTables.items[0].kdagama);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/agama' + '?var=' + json.dataTables.items[0].kdagama,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].nmagama, datax.items[0].kdagama, true, true);
                $('[name="kdagama"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="kdagama"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            //$('[name="goldarah"]').val(json.dataTables.items[0].goldarah);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/golongan_darah' + '?var=' + json.dataTables.items[0].goldarah,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].nmgoldarah, datax.items[0].goldarah, true, true);
                $('[name="goldarah"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="goldarah"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            //$('[name="stswn"]').val(json.dataTables.items[0].stswn).trigger('change');
            $.ajax({
                type: 'GET',
                //url: HOST_URL + 'api/globalmodule/list_karyawan_by_id' + '?var=' + json.dataTables.items[0].nik,
                url: HOST_URL + 'api/geolocation/list_negara' + '?var=' + json.dataTables.items[0].stswn,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                //console.log(datax.items[0].nik + 'KONTOL');
                var option = new Option(datax.items[0].namanegara, datax.items[0].kodenegara, true, true);
                $('[name="stswn"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="stswn"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });

            //$('[name="id_division"]').val(json.dataTables.items[0].id_division).trigger('change');
            $.ajax({
                type: 'GET',
                //url: HOST_URL + 'api/globalmodule/list_karyawan_by_id' + '?var=' + json.dataTables.items[0].nik,
                url: HOST_URL + 'api/globalmodule/list_division' + '?var=' + json.dataTables.items[0].id_division,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                //console.log(datax.items[0].nik + 'KONTOL');
                var option = new Option(datax.items[0].nmdivision, datax.items[0].iddivision, true, true);
                $('[name="id_division"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="id_division"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });

            //$('[name="id_dept"]').val(json.dataTables.items[0].id_dept).trigger('change');
            $.ajax({
                type: 'GET',
                //url: HOST_URL + 'api/globalmodule/list_karyawan_by_id' + '?var=' + json.dataTables.items[0].nik,
                url: HOST_URL + 'api/globalmodule/list_departmen' + '?var=' + json.dataTables.items[0].id_dept,
                dataType: 'json',
                delay: 255,
            }).then(function (datax) {
                // create the option and append to Select2
                //console.log(datax.items[0].nik + 'KONTOL');
                var option = new Option(datax.items[0].nmdept, datax.items[0].kddept, true, true);
                $('[name="id_dept"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="id_dept"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            //$('[name="id_subdept"]').val(json.dataTables.items[0].id_subdept).trigger('change');
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_subdepartmen' + '?var=' + json.dataTables.items[0].id_subdept,
                dataType: 'json',
                delay: 300,
            }).then(function (datax) {
                // create the option and append to Select2
                //console.log(datax.items[0].nik + 'KONTOL');
                var option = new Option(datax.items[0].nmsubdept, datax.items[0].kdsubdept, true, true);
                $('[name="id_subdept"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="id_subdept"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            //$('[name="id_jabatan"]').val(json.dataTables.items[0].id_jabatan).trigger('change');
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_jabatan' + '?var=' + json.dataTables.items[0].id_jabatan,
                dataType: 'json',
                delay: 300,
            }).then(function (datax) {
                // create the option and append to Select2
                //console.log(datax.items[0].nik + 'KONTOL');
                var option = new Option(datax.items[0].nmjabatan, datax.items[0].kdjabatan, true, true);
                $('[name="id_jabatan"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="id_jabatan"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            //$('[name="id_grade"]').val(json.dataTables.items[0].id_grade).trigger('change');
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_lvljabatan' + '?var=' + json.dataTables.items[0].id_grade,
                dataType: 'json',
                delay: 300,
            }).then(function (datax) {
                // create the option and append to Select2
                //console.log(datax.items[0].nik + 'KONTOL');
                var option = new Option(datax.items[0].nmlvljabatan, datax.items[0].kdlvl, true, true);
                $('[name="id_grade"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="id_grade"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            //$('[name="costcenter"]').val(json.dataTables.items[0].costcenter);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_costcenter' + '?var=' + json.dataTables.items[0].costcenter,
                dataType: 'json',
                delay: 300,
            }).then(function (datax) {
                // create the option and append to Select2
                //console.log(datax.items[0].nik + 'KONTOL');
                var option = new Option(datax.items[0].nmcostcenter, datax.items[0].idcostcenter, true, true);
                $('[name="costcenter"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="costcenter"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            //$('[name="id_gol"]').val(json.dataTables.items[0].id_gol);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_golongan' + '?var=' + json.dataTables.items[0].id_gol,
                dataType: 'json',
                delay: 100,
            }).then(function (datax) {
                // create the option and append to Select2
                //console.log(datax.items[0].nik + 'KONTOL');
                var option = new Option(datax.items[0].nmgroupgol, datax.items[0].idgroupgol, true, true);
                $('[name="id_gol"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="id_gol"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            //$('[name="plant"]').val(json.dataTables.items[0].plant);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_plant' + '?var=' + json.dataTables.items[0].plant,
                dataType: 'json',
                delay: 100,
            }).then(function (datax) {
                // create the option and append to Select2
                //console.log(datax.items[0].nik + 'KONTOL');
                var option = new Option(datax.items[0].locaname, datax.items[0].loccode, true, true);
                $('[name="plant"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="plant"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            //$('[name="statuskepegawaian"]').val(json.dataTables.items[0].statuskepegawaian);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_kepegawaian' + '?var=' + json.dataTables.items[0].statuskepegawaian,
                dataType: 'json',
                delay: 100,
            }).then(function (datax) {
                // create the option and append to Select2
                //console.log(datax.items[0].nik + 'KONTOL');
                var option = new Option(datax.items[0].nmkepegawaian, datax.items[0].kdkepegawaian, true, true);
                $('[name="statuskepegawaian"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="statuskepegawaian"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            $('[name="tglmasukkerja"]').val(json.dataTables.items[0].tglmasukkerja1);
            //$('[name="nikatasan1"]').val(json.dataTables.items[0].nikatasan1);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_karyawan' + '?var=' + nikatasan1,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].nmlengkap, datax.items[0].nik, true, true);
                $('[name="nikatasan1"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="nikatasan1"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            //$('[name="nikatasan2"]').val(json.dataTables.items[0].nikatasan2);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_karyawan' + '?var=' + nikatasan2,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].nmlengkap, datax.items[0].nik, true, true);
                $('[name="nikatasan2"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="nikatasan2"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            //$('[name="nikatasan3"]').val(json.dataTables.items[0].nikatasan3);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_karyawan' + '?var=' + nikatasan3,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].nmlengkap, datax.items[0].nik, true, true);
                $('[name="nikatasan3"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="nikatasan3"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });


            $('[name="tglkeluarkerja"]').val(json.dataTables.items[0].tglkeluarkerja1);
            $('[name="id_ktp"]').val(json.dataTables.items[0].id_ktp);
            //$('[name="id_provktp"]').val(json.dataTables.items[0].id_provktp);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/geolocation/list_provinsi' + '?var=' + json.dataTables.items[0].id_provktp,
                dataType: 'json',
                delay: 100,
            }).then(function (datax) {
                // create the option and append to Select2
                //console.log(datax.items[0].nik + 'KONTOL');
                var option = new Option(datax.items[0].namaprov, datax.items[0].kodeprov, true, true);
                $('[name="id_provktp"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="id_provktp"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            //$('[name="id_kotaktp"]').val(json.dataTables.items[0].id_kotaktp);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/geolocation/list_kota' + '?var=' + json.dataTables.items[0].id_kotaktp,
                dataType: 'json',
                delay: 100,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].namakotakab, datax.items[0].kodekotakab, true, true);
                $('[name="id_kotaktp"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="id_kotaktp"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            //$('[name="id_kecktp"]').val(json.dataTables.items[0].id_kecktp);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/geolocation/list_kecamatan' + '?var=' + json.dataTables.items[0].id_kecktp,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].namakec, datax.items[0].kodekec, true, true);
                $('[name="id_kecktp"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="id_kecktp"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            //$('[name="id_desaktp"]').val(json.dataTables.items[0].id_desaktp);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/geolocation/list_desa' + '?var=' + json.dataTables.items[0].id_desaktp,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].namakeldesa, datax.items[0].kodekeldesa, true, true);
                $('[name="id_desaktp"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="id_desaktp"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            $('[name="desc_ktp"]').val(json.dataTables.items[0].desc_ktp);

            //$('[name="id_provdom"]').val(json.dataTables.items[0].id_provdom);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/geolocation/list_provinsi' + '?var=' + json.dataTables.items[0].id_provdom,
                dataType: 'json',
                delay: 100,
            }).then(function (datax) {
                // create the option and append to Select2
                //console.log(datax.items[0].nik + 'KONTOL');
                var option = new Option(datax.items[0].namaprov, datax.items[0].kodeprov, true, true);
                $('[name="id_provdom"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="id_provdom"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            //$('[name="id_kotadom"]').val(json.dataTables.items[0].id_kotadom);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/geolocation/list_kota' + '?var=' + json.dataTables.items[0].id_kotadom,
                dataType: 'json',
                delay: 100,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].namakotakab, datax.items[0].kodekotakab, true, true);
                $('[name="id_kotadom"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="id_kotadom"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            //$('[name="id_kecdom"]').val(json.dataTables.items[0].id_kecdom);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/geolocation/list_kecamatan' + '?var=' + json.dataTables.items[0].id_kecdom,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].namakec, datax.items[0].kodekec, true, true);
                $('[name="id_kecdom"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="id_kecdom"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            //$('[name="id_desadom"]').val(json.dataTables.items[0].id_desadom);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/geolocation/list_desa' + '?var=' + json.dataTables.items[0].id_desadom,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].namakeldesa, datax.items[0].kodekeldesa, true, true);
                $('[name="id_desadom"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="id_desadom"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            $('[name="desc_domisili"]').val(json.dataTables.items[0].desc_domisili);
            $('[name="nokk"]').val(json.dataTables.items[0].nokk);
            $('[name="id_npwp"]').val(json.dataTables.items[0].id_npwp);
            //$('[name="sts_ptkp"]').val(json.dataTables.items[0].sts_ptkp);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_ptkp' + '?var=' + json.dataTables.items[0].sts_ptkp,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].kodeptkp, datax.items[0].kodeptkp, true, true);
                $('[name="sts_ptkp"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="sts_ptkp"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            //$('[name="sts_kawin"]').val(json.dataTables.items[0].sts_kawin);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_ptkp' + '?var=' + json.dataTables.items[0].sts_kawin,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].kodeptkp, datax.items[0].kodeptkp, true, true);
                $('[name="sts_kawin"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="sts_kawin"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            //$('[name="id_jenjang"]').val(json.dataTables.items[0].id_jenjang);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_pendidikan' + '?var=' + json.dataTables.items[0].id_jenjang,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].nmpendidikan, datax.items[0].kdpendidikan, true, true);
                $('[name="id_jenjang"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="id_jenjang"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            $('[name="pendidikan_terakhir"]').val(json.dataTables.items[0].pendidikan_terakhir);
            $('[name="jurusan"]').val(json.dataTables.items[0].jurusan);
            $('[name="ipk"]').val(json.dataTables.items[0].ipk);
            $('[name="lulus_tahun"]').val(json.dataTables.items[0].lulus_tahun);
            $('[name="id_bpjs_kes"]').val(json.dataTables.items[0].id_bpjs_kes);
            $('[name="cab_bpjs_kes"]').val(json.dataTables.items[0].cab_bpjs_kes);
            $('[name="id_bpjs_naker"]').val(json.dataTables.items[0].id_bpjs_naker);
            $('[name="cab_bpjs_naker"]').val(json.dataTables.items[0].cab_bpjs_naker);
            $('[name="nohp1"]').val(json.dataTables.items[0].nohp1);
            $('[name="nohp2"]').val(json.dataTables.items[0].nohp2);
            $('[name="email_self"]').val(json.dataTables.items[0].email_self);
            $('[name="email_office"]').val(json.dataTables.items[0].email_office);
            $('[name="emergency_phone"]').val(json.dataTables.items[0].emergency_phone);
            $('[name="emergency_relation"]').val(json.dataTables.items[0].emergency_relation);
            $('[name="emergency_name"]').val(json.dataTables.items[0].emergency_name);
            $('[name="idabsen"]').val(json.dataTables.items[0].idabsen);
            $('[name="type_office"]').val(json.dataTables.items[0].type_office);
            $('[name="area_covid"]').val(json.dataTables.items[0].area_covid).trigger('change');
            $('[name="statuscovid"]').val(json.dataTables.items[0].statuscovid).trigger('change');
            //$('[name="id_bank"]').val(json.dataTables.items[0].id_bank);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_bank' + '?var=' + json.dataTables.items[0].id_bank,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].nmbank, datax.items[0].kdbank, true, true);
                $('[name="id_bank"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="id_bank"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            $('[name="id_rekening"]').val(json.dataTables.items[0].id_rekening);
            $('[name="nmibukandung"]').val(json.dataTables.items[0].nmibukandung);
            $('[name="nmayahkandung"]').val(json.dataTables.items[0].nmayahkandung);
           // $('[name="kdlembur"]').val(json.dataTables.items[0].kdlembur).trigger('change');

            $('[name="rtktp"]').val(json.dataTables.items[0].rtktp);
            $('[name="rwktp"]').val(json.dataTables.items[0].rwktp);
            $('[name="rtdom"]').val(json.dataTables.items[0].rtdom);
            $('[name="rwdom"]').val(json.dataTables.items[0].rwdom);
            $('[name="id_posktp"]').val(json.dataTables.items[0].id_posktp);
            $('[name="id_posdom"]').val(json.dataTables.items[0].id_posdom);
            $('[name="transportasi"]').val(json.dataTables.items[0].transportasi).trigger('change');
            $('[name="typedomisili"]').val(json.dataTables.items[0].typedomisili).trigger('change');
            $('[name="penyakitbawaan"]').val(json.dataTables.items[0].penyakitbawaan).trigger('change');
            $('[name="nmkeluargapenyakitbawaan"]').val(json.dataTables.items[0].nmkeluargapenyakitbawaan);
            $('[name="nakes"]').val(json.dataTables.items[0].nakes);
            $('[name="nmpasangan"]').val(json.dataTables.items[0].nmpasangan);
            $('[name="pekerjaanpasangan"]').val(json.dataTables.items[0].pekerjaanpasangan);
            $('[name="hubunganpasangan"]').val(json.dataTables.items[0].hubunganpasangan);
            $('[name="contactpasangan"]').val(json.dataTables.items[0].contactpasangan);

            $('[name="nik"]').prop('readonly', true);
/*
            var select2_kary = $('.select2_kary');
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_karyawan_by_id' + '?var=' + json.dataTables.items[0].nik,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                //console.log(datax.items[0].nik + 'KONTOL');
                var option = new Option(datax.items[0].nmlengkap, datax.items[0].nik, true, true);
                select2_kary.append(option).trigger('change');

                // manually trigger the `select2:select` event
                select2_kary.trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
*/
            //disabled nik
            //$('[name="nik"]').prop('readonly', true);

            //$('[name="cimage"]').val(json.dataTables.items[0].cimage);
            var ih = $("#image-holder");
            ih.empty();
            if (json.dataTables.items[0].cimage) {
                $("<img />", {
                    "src": HOST_URL+'/assets/img/profile' + '/' + json.dataTables.items[0].cimage,
                    "class": "thumb-image",
                    "style": "max-width: 250px; max-height: 250px;"
                }).appendTo(ih);
            } else {
                $("<img />", {
                    "src": HOST_URL+'/assets/img/user.png',
                    "class": "thumb-image",
                    "style": "max-width: 250px; max-height: 250px;"
                }).appendTo(ih);
            }



        },
        complete: function(){
            $("#loadMe").modal("hide");
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            console.log("Failed To Loading Data");
            $("#loadMe").modal("hide");
        }
    });

    $("#loadMe").modal("hide");
}
function documentReadableDetail(){
    $("#loadMe").modal({
        backdrop: "static", //remove ability to close modal with click
        keyboard: false, //remove option to close with keyboard
        show: true //Display loader!
    });
    $.ajax({
        type: 'GET',
        url: HOST_URL + 'trans/karyawan/showDetailNik' + '?docno=' + $('[name="docno"]').val(),
        dataType: 'json',
        dataFilter: function(data) {
            var json = jQuery.parseJSON(data);
            json.status = json.dataTables.status;
            json.total_count = json.dataTables.total_count;
            json.items = json.dataTables.items;
            json.incomplete_results = json.dataTables.incomplete_results;


            var nikatasan1 = (json.dataTables.items[0].nikatasan1 === '') ? 'NULL' : json.dataTables.items[0].nikatasan1 ;
            var nikatasan2 = (json.dataTables.items[0].nikatasan2 === '') ? 'NULL' : json.dataTables.items[0].nikatasan2 ;
            var nikatasan3 = (json.dataTables.items[0].nikatasan3 === '') ? 'NULL' : json.dataTables.items[0].nikatasan3 ;
            //console.log("On Ready" + json.dataTables.items[0].nik);
            // Fetch the preselected item, and add to the control
            $('[name="nik"]').val(json.dataTables.items[0].nik);
            $('[name="nmlengkap"]').val(json.dataTables.items[0].nmlengkap);
            $('[name="callname"]').val(json.dataTables.items[0].callname);
            $('[name="alamatlahir"]').val(json.dataTables.items[0].alamatlahir);
            $('[name="tgllahir"]').val(json.dataTables.items[0].tgllahir1);
            $('[name="alasan_resign"]').val(json.dataTables.items[0].alasan_resign);
            //$('[name="jk"]').val(json.dataTables.items[0].jk);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/jenis_kelamin' + '?var=' + json.dataTables.items[0].jk,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {


                // create the option and append to Select2
                var option = new Option(datax.items[0].nmkelamin, datax.items[0].idkelamin, true, true);
                $('[name="jk"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="jk"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            //$('[name="kdagama"]').val(json.dataTables.items[0].kdagama);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/agama' + '?var=' + json.dataTables.items[0].kdagama,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].nmagama, datax.items[0].kdagama, true, true);
                $('[name="kdagama"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="kdagama"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            //$('[name="goldarah"]').val(json.dataTables.items[0].goldarah);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/golongan_darah' + '?var=' + json.dataTables.items[0].goldarah,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].nmgoldarah, datax.items[0].goldarah, true, true);
                $('[name="goldarah"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="goldarah"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            //$('[name="stswn"]').val(json.dataTables.items[0].stswn).trigger('change');
            $.ajax({
                type: 'GET',
                //url: HOST_URL + 'api/globalmodule/list_karyawan_by_id' + '?var=' + json.dataTables.items[0].nik,
                url: HOST_URL + 'api/geolocation/list_negara' + '?var=' + json.dataTables.items[0].stswn,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                //console.log(datax.items[0].nik + 'KONTOL');
                var option = new Option(datax.items[0].namanegara, datax.items[0].kodenegara, true, true);
                $('[name="stswn"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="stswn"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });

            //$('[name="id_division"]').val(json.dataTables.items[0].id_division).trigger('change');
            $.ajax({
                type: 'GET',
                //url: HOST_URL + 'api/globalmodule/list_karyawan_by_id' + '?var=' + json.dataTables.items[0].nik,
                url: HOST_URL + 'api/globalmodule/list_division' + '?var=' + json.dataTables.items[0].id_division,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                //console.log(datax.items[0].nik + 'KONTOL');
                var option = new Option(datax.items[0].nmdivision, datax.items[0].iddivision, true, true);
                $('[name="id_division"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="id_division"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });

            //$('[name="id_dept"]').val(json.dataTables.items[0].id_dept).trigger('change');
            $.ajax({
                type: 'GET',
                //url: HOST_URL + 'api/globalmodule/list_karyawan_by_id' + '?var=' + json.dataTables.items[0].nik,
                url: HOST_URL + 'api/globalmodule/list_departmen' + '?var=' + json.dataTables.items[0].id_dept,
                dataType: 'json',
                delay: 255,
            }).then(function (datax) {
                // create the option and append to Select2
                //console.log(datax.items[0].nik + 'KONTOL');
                var option = new Option(datax.items[0].nmdept, datax.items[0].kddept, true, true);
                $('[name="id_dept"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="id_dept"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            //$('[name="id_subdept"]').val(json.dataTables.items[0].id_subdept).trigger('change');
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_subdepartmen' + '?var=' + json.dataTables.items[0].id_subdept,
                dataType: 'json',
                delay: 300,
            }).then(function (datax) {
                // create the option and append to Select2
                //console.log(datax.items[0].nik + 'KONTOL');
                var option = new Option(datax.items[0].nmsubdept, datax.items[0].kdsubdept, true, true);
                $('[name="id_subdept"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="id_subdept"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            //$('[name="id_jabatan"]').val(json.dataTables.items[0].id_jabatan).trigger('change');
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_jabatan' + '?var=' + json.dataTables.items[0].id_jabatan,
                dataType: 'json',
                delay: 300,
            }).then(function (datax) {
                // create the option and append to Select2
                //console.log(datax.items[0].nik + 'KONTOL');
                var option = new Option(datax.items[0].nmjabatan, datax.items[0].kdjabatan, true, true);
                $('[name="id_jabatan"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="id_jabatan"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            //$('[name="id_grade"]').val(json.dataTables.items[0].id_grade).trigger('change');
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_lvljabatan' + '?var=' + json.dataTables.items[0].id_grade,
                dataType: 'json',
                delay: 300,
            }).then(function (datax) {
                // create the option and append to Select2
                //console.log(datax.items[0].nik + 'KONTOL');
                var option = new Option(datax.items[0].nmlvljabatan, datax.items[0].kdlvl, true, true);
                $('[name="id_grade"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="id_grade"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            //$('[name="costcenter"]').val(json.dataTables.items[0].costcenter);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_costcenter' + '?var=' + json.dataTables.items[0].costcenter,
                dataType: 'json',
                delay: 300,
            }).then(function (datax) {
                // create the option and append to Select2
                //console.log(datax.items[0].nik + 'KONTOL');
                var option = new Option(datax.items[0].nmcostcenter, datax.items[0].idcostcenter, true, true);
                $('[name="costcenter"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="costcenter"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            //$('[name="id_gol"]').val(json.dataTables.items[0].id_gol);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_golongan' + '?var=' + json.dataTables.items[0].id_gol,
                dataType: 'json',
                delay: 100,
            }).then(function (datax) {
                // create the option and append to Select2
                //console.log(datax.items[0].nik + 'KONTOL');
                var option = new Option(datax.items[0].nmgroupgol, datax.items[0].idgroupgol, true, true);
                $('[name="id_gol"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="id_gol"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            //$('[name="plant"]').val(json.dataTables.items[0].plant);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_plant' + '?var=' + json.dataTables.items[0].plant,
                dataType: 'json',
                delay: 100,
            }).then(function (datax) {
                // create the option and append to Select2
                //console.log(datax.items[0].nik + 'KONTOL');
                var option = new Option(datax.items[0].locaname, datax.items[0].loccode, true, true);
                $('[name="plant"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="plant"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            //$('[name="statuskepegawaian"]').val(json.dataTables.items[0].statuskepegawaian);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_kepegawaian' + '?var=' + json.dataTables.items[0].statuskepegawaian,
                dataType: 'json',
                delay: 100,
            }).then(function (datax) {
                // create the option and append to Select2
                //console.log(datax.items[0].nik + 'KONTOL');
                var option = new Option(datax.items[0].nmkepegawaian, datax.items[0].kdkepegawaian, true, true);
                $('[name="statuskepegawaian"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="statuskepegawaian"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            $('[name="tglmasukkerja"]').val(json.dataTables.items[0].tglmasukkerja1);
            //$('[name="nikatasan1"]').val(json.dataTables.items[0].nikatasan1);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_karyawan' + '?var=' + nikatasan1,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].nmlengkap, datax.items[0].nik, true, true);
                $('[name="nikatasan1"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="nikatasan1"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            //$('[name="nikatasan2"]').val(json.dataTables.items[0].nikatasan2);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_karyawan' + '?var=' + nikatasan2,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].nmlengkap, datax.items[0].nik, true, true);
                $('[name="nikatasan2"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="nikatasan2"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            //$('[name="nikatasan3"]').val(json.dataTables.items[0].nikatasan3);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_karyawan' + '?var=' + nikatasan3,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].nmlengkap, datax.items[0].nik, true, true);
                $('[name="nikatasan3"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="nikatasan3"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            $('[name="tglkeluarkerja"]').val(json.dataTables.items[0].tglkeluarkerja1);
            $('[name="id_ktp"]').val(json.dataTables.items[0].id_ktp);
            //$('[name="id_provktp"]').val(json.dataTables.items[0].id_provktp);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/geolocation/list_provinsi' + '?var=' + json.dataTables.items[0].id_provktp,
                dataType: 'json',
                delay: 100,
            }).then(function (datax) {
                // create the option and append to Select2
                //console.log(datax.items[0].nik + 'KONTOL');
                var option = new Option(datax.items[0].namaprov, datax.items[0].kodeprov, true, true);
                $('[name="id_provktp"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="id_provktp"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            //$('[name="id_kotaktp"]').val(json.dataTables.items[0].id_kotaktp);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/geolocation/list_kota' + '?var=' + json.dataTables.items[0].id_kotaktp,
                dataType: 'json',
                delay: 100,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].namakotakab, datax.items[0].kodekotakab, true, true);
                $('[name="id_kotaktp"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="id_kotaktp"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            //$('[name="id_kecktp"]').val(json.dataTables.items[0].id_kecktp);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/geolocation/list_kecamatan' + '?var=' + json.dataTables.items[0].id_kecktp,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].namakec, datax.items[0].kodekec, true, true);
                $('[name="id_kecktp"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="id_kecktp"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            //$('[name="id_desaktp"]').val(json.dataTables.items[0].id_desaktp);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/geolocation/list_desa' + '?var=' + json.dataTables.items[0].id_desaktp,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].namakeldesa, datax.items[0].kodekeldesa, true, true);
                $('[name="id_desaktp"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="id_desaktp"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            $('[name="desc_ktp"]').val(json.dataTables.items[0].desc_ktp);

            //$('[name="id_provdom"]').val(json.dataTables.items[0].id_provdom);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/geolocation/list_provinsi' + '?var=' + json.dataTables.items[0].id_provdom,
                dataType: 'json',
                delay: 100,
            }).then(function (datax) {
                // create the option and append to Select2
                //console.log(datax.items[0].nik + 'KONTOL');
                var option = new Option(datax.items[0].namaprov, datax.items[0].kodeprov, true, true);
                $('[name="id_provdom"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="id_provdom"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            //$('[name="id_kotadom"]').val(json.dataTables.items[0].id_kotadom);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/geolocation/list_kota' + '?var=' + json.dataTables.items[0].id_kotadom,
                dataType: 'json',
                delay: 100,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].namakotakab, datax.items[0].kodekotakab, true, true);
                $('[name="id_kotadom"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="id_kotadom"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            //$('[name="id_kecdom"]').val(json.dataTables.items[0].id_kecdom);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/geolocation/list_kecamatan' + '?var=' + json.dataTables.items[0].id_kecdom,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].namakec, datax.items[0].kodekec, true, true);
                $('[name="id_kecdom"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="id_kecdom"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            //$('[name="id_desadom"]').val(json.dataTables.items[0].id_desadom);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/geolocation/list_desa' + '?var=' + json.dataTables.items[0].id_desadom,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].namakeldesa, datax.items[0].kodekeldesa, true, true);
                $('[name="id_desadom"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="id_desadom"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            $('[name="desc_domisili"]').val(json.dataTables.items[0].desc_domisili);
            $('[name="nokk"]').val(json.dataTables.items[0].nokk);
            $('[name="id_npwp"]').val(json.dataTables.items[0].id_npwp);
            //$('[name="sts_ptkp"]').val(json.dataTables.items[0].sts_ptkp);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_ptkp' + '?var=' + json.dataTables.items[0].sts_ptkp,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].kodeptkp, datax.items[0].kodeptkp, true, true);
                $('[name="sts_ptkp"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="sts_ptkp"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            //$('[name="sts_kawin"]').val(json.dataTables.items[0].sts_kawin);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_ptkp' + '?var=' + json.dataTables.items[0].sts_kawin,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].kodeptkp, datax.items[0].kodeptkp, true, true);
                $('[name="sts_kawin"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="sts_kawin"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            //$('[name="id_jenjang"]').val(json.dataTables.items[0].id_jenjang);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_pendidikan' + '?var=' + json.dataTables.items[0].id_jenjang,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].nmpendidikan, datax.items[0].kdpendidikan, true, true);
                $('[name="id_jenjang"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="id_jenjang"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            $('[name="pendidikan_terakhir"]').val(json.dataTables.items[0].pendidikan_terakhir);
            $('[name="jurusan"]').val(json.dataTables.items[0].jurusan);
            $('[name="ipk"]').val(json.dataTables.items[0].ipk);
            $('[name="lulus_tahun"]').val(json.dataTables.items[0].lulus_tahun);
            $('[name="id_bpjs_kes"]').val(json.dataTables.items[0].id_bpjs_kes);
            $('[name="cab_bpjs_kes"]').val(json.dataTables.items[0].cab_bpjs_kes);
            $('[name="id_bpjs_naker"]').val(json.dataTables.items[0].id_bpjs_naker);
            $('[name="cab_bpjs_naker"]').val(json.dataTables.items[0].cab_bpjs_naker);
            $('[name="nohp1"]').val(json.dataTables.items[0].nohp1);
            $('[name="nohp2"]').val(json.dataTables.items[0].nohp2);
            $('[name="email_self"]').val(json.dataTables.items[0].email_self);
            $('[name="email_office"]').val(json.dataTables.items[0].email_office);
            $('[name="emergency_phone"]').val(json.dataTables.items[0].emergency_phone);
            $('[name="emergency_relation"]').val(json.dataTables.items[0].emergency_relation);
            $('[name="emergency_name"]').val(json.dataTables.items[0].emergency_name);
            $('[name="idabsen"]').val(json.dataTables.items[0].idabsen);
            $('[name="type_office"]').val(json.dataTables.items[0].type_office);
            $('[name="area_covid"]').val(json.dataTables.items[0].area_covid).trigger('change');
            $('[name="statuscovid"]').val(json.dataTables.items[0].statuscovid).trigger('change');
            //$('[name="id_bank"]').val(json.dataTables.items[0].id_bank);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_bank' + '?var=' + json.dataTables.items[0].id_bank,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].nmbank, datax.items[0].kdbank, true, true);
                $('[name="id_bank"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="id_bank"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            $('[name="id_rekening"]').val(json.dataTables.items[0].id_rekening);
            $('[name="nmibukandung"]').val(json.dataTables.items[0].nmibukandung);
            $('[name="nmayahkandung"]').val(json.dataTables.items[0].nmayahkandung);
            // $('[name="kdlembur"]').val(json.dataTables.items[0].kdlembur).trigger('change');
            $('[name="rtktp"]').val(json.dataTables.items[0].rtktp);
            $('[name="rwktp"]').val(json.dataTables.items[0].rwktp);
            $('[name="rtdom"]').val(json.dataTables.items[0].rtdom);
            $('[name="rwdom"]').val(json.dataTables.items[0].rwdom);
            $('[name="id_posktp"]').val(json.dataTables.items[0].id_posktp);
            $('[name="id_posdom"]').val(json.dataTables.items[0].id_posdom);
            $('[name="transportasi"]').val(json.dataTables.items[0].transportasi).trigger('change');
            $('[name="typedomisili"]').val(json.dataTables.items[0].typedomisili).trigger('change');
            $('[name="penyakitbawaan"]').val(json.dataTables.items[0].penyakitbawaan).trigger('change');
            $('[name="nmkeluargapenyakitbawaan"]').val(json.dataTables.items[0].nmkeluargapenyakitbawaan);
            $('[name="nakes"]').val(json.dataTables.items[0].nakes);
            $('[name="nmpasangan"]').val(json.dataTables.items[0].nmpasangan);
            $('[name="pekerjaanpasangan"]').val(json.dataTables.items[0].pekerjaanpasangan);
            $('[name="hubunganpasangan"]').val(json.dataTables.items[0].hubunganpasangan);
            $('[name="contactpasangan"]').val(json.dataTables.items[0].contactpasangan);

            /*
                        var select2_kary = $('.select2_kary');
                        $.ajax({
                            type: 'GET',
                            url: HOST_URL + 'api/globalmodule/list_karyawan_by_id' + '?var=' + json.dataTables.items[0].nik,
                            dataType: 'json',
                            delay: 250,
                        }).then(function (datax) {
                            // create the option and append to Select2
                            //console.log(datax.items[0].nik + 'KONTOL');
                            var option = new Option(datax.items[0].nmlengkap, datax.items[0].nik, true, true);
                            select2_kary.append(option).trigger('change');

                            // manually trigger the `select2:select` event
                            select2_kary.trigger({
                                type: 'select2:select',
                                params: {
                                    data: datax
                                }
                            });
                        });
            */
            $('[name="nik"]').prop('readonly', true);
            $('[name="nmlengkap"]').prop('readonly', true);
            $('[name="callname"]').prop('readonly', true);
            $('[name="tgllahir"]').prop('disabled', true);
            $('[name="jk"]').prop('disabled', true);
            $('[name="kdagama"]').prop('disabled', true);
            $('[name="goldarah"]').prop('disabled', true);
            $('[name="stswn"]').prop('disabled', true);
            $('[name="id_division"]').prop('disabled', true);
            $('[name="id_dept"]').prop('disabled', true);
            $('[name="id_subdept"]').prop('disabled', true);
            $('[name="id_jabatan"]').prop('disabled', true);
            $('[name="id_grade"]').prop('disabled', true);
            $('[name="costcenter"]').prop('disabled', true);
            $('[name="id_gol"]').prop('disabled', true);
            $('[name="plant"]').prop('disabled', true);
            $('[name="statuskepegawaian"]').prop('disabled', true);
            $('[name="tglmasukkerja"]').prop('disabled', true);
            $('[name="tglkeluarkerja"]').prop('disabled', true);
            $('[name="id_ktp"]').prop('disabled', true);
            $('[name="id_provktp"]').prop('disabled', true);
            $('[name="id_kotaktp"]').prop('disabled', true);
            $('[name="id_kecktp"]').prop('disabled', true);
            $('[name="id_desaktp"]').prop('disabled', true);
            $('[name="desc_ktp"]').prop('disabled', true);
            $('[name="id_provdom"]').prop('disabled', true);
            $('[name="id_kotadom"]').prop('disabled', true);
            $('[name="id_kecdom"]').prop('disabled', true);
            $('[name="id_desadom"]').prop('disabled', true);
            $('[name="desc_domisili"]').prop('disabled', true);
            $('[name="nokk"]').prop('disabled', true);
            $('[name="id_npwp"]').prop('disabled', true);
            $('[name="sts_ptkp"]').prop('disabled', true);
            $('[name="sts_kawin"]').prop('disabled', true);
            $('[name="id_jenjang"]').prop('disabled', true);
            $('[name="pendidikan_terakhir"]').prop('disabled', true);
            $('[name="jurusan"]').prop('disabled', true);
            $('[name="ipk"]').prop('disabled', true);
            $('[name="lulus_tahun"]').prop('disabled', true);
            $('[name="id_bpjs_kes"]').prop('disabled', true);
            $('[name="cab_bpjs_kes"]').prop('disabled', true);
            $('[name="id_bpjs_naker"]').prop('disabled', true);
            $('[name="cab_bpjs_naker"]').prop('disabled', true);
            $('[name="nohp1"]').prop('disabled', true);
            $('[name="nohp2"]').prop('disabled', true);
            $('[name="email_self"]').prop('disabled', true);
            $('[name="email_office"]').prop('disabled', true);
            $('[name="emergency_phone"]').prop('disabled', true);
            $('[name="emergency_relation"]').prop('disabled', true);
            $('[name="emergency_name"]').prop('disabled', true);
            $('[name="idabsen"]').prop('disabled', true);
            $('[name="type_office"]').prop('disabled', true);
            $('[name="area_covid"]').prop('disabled', true);
            $('[name="statuscovid"]').prop('disabled', true);
            $('[name="id_bank"]').prop('disabled', true);
            $('[name="id_rekening"]').prop('disabled', true);
            $('[name="nmibukandung"]').prop('disabled', true);
            $('[name="nmayahkandung"]').prop('disabled', true);
            $('[name="alasan_resign"]').prop('disabled', true);
            $('[name="nikatasan1"]').prop('disabled', true);
            $('[name="nikatasan2"]').prop('disabled', true);
            $('[name="nikatasan3"]').prop('disabled', true);
            $('[name="rtktp"]').prop('disabled', true);
            $('[name="rwktp"]').prop('disabled', true);
            $('[name="rtdom"]').prop('disabled', true);
            $('[name="rwdom"]').prop('disabled', true);
            $('[name="id_posktp"]').prop('disabled', true);
            $('[name="id_posdom"]').prop('disabled', true);
            $('[name="transportasi"]').prop('disabled', true);
            $('[name="typedomisili"]').prop('disabled', true);
            $('[name="penyakitbawaan"]').prop('disabled', true);
            $('[name="nmkeluargapenyakitbawaan"]').prop('disabled', true);
            $('[name="nakes"]').prop('disabled', true);
            $('[name="nmpasangan"]').prop('disabled', true);
            $('[name="pekerjaanpasangan"]').prop('disabled', true);
            $('[name="hubunganpasangan"]').prop('disabled', true);
            $('[name="contactpasangan"]').prop('disabled', true);

            var ih = $("#image-holder");
            ih.empty();
            if (json.dataTables.items[0].cimage) {
                $("<img />", {
                    "src": HOST_URL+'/assets/img/profile' + '/' + json.dataTables.items[0].cimage,
                    "class": "thumb-image",
                    "style": "max-width: 250px; max-height: 250px;"
                }).appendTo(ih);
            } else {
                $("<img />", {
                    "src": HOST_URL+'/assets/img/user.png',
                    "class": "thumb-image",
                    "style": "max-width: 250px; max-height: 250px;"
                }).appendTo(ih);
            }
            $('[name="cimage"]').prop('disabled', true);

            $('#btnSave').hide();


        },
        complete: function(){
            $("#loadMe").modal("hide");
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            console.log("Failed To Loading Data");
            $("#loadMe").modal("hide");
        }
    });

    $("#loadMe").modal("hide");
}
function documentReadableResign(){
    $("#loadMe").modal({
        backdrop: "static", //remove ability to close modal with click
        keyboard: false, //remove option to close with keyboard
        show: true //Display loader!
    });
    $.ajax({
        type: 'GET',
        url: HOST_URL + 'trans/karyawan/showDetailNik' + '?docno=' + $('[name="docno"]').val(),
        dataType: 'json',
        dataFilter: function(data) {
            var json = jQuery.parseJSON(data);
            json.status = json.dataTables.status;
            json.total_count = json.dataTables.total_count;
            json.items = json.dataTables.items;
            json.incomplete_results = json.dataTables.incomplete_results;


            var nikatasan1 = (json.dataTables.items[0].nikatasan1 === '') ? 'NULL' : json.dataTables.items[0].nikatasan1 ;
            var nikatasan2 = (json.dataTables.items[0].nikatasan2 === '') ? 'NULL' : json.dataTables.items[0].nikatasan2 ;
            var nikatasan3 = (json.dataTables.items[0].nikatasan3 === '') ? 'NULL' : json.dataTables.items[0].nikatasan3 ;
            //console.log("On Ready" + json.dataTables.items[0].nik);
            // Fetch the preselected item, and add to the control
            $('[name="nik"]').val(json.dataTables.items[0].nik);
            $('[name="nmlengkap"]').val(json.dataTables.items[0].nmlengkap);
            $('[name="callname"]').val(json.dataTables.items[0].callname);
            $('[name="alamatlahir"]').val(json.dataTables.items[0].alamatlahir);
            $('[name="tgllahir"]').val(json.dataTables.items[0].tgllahir1);
            $('[name="alasan_resign"]').val(json.dataTables.items[0].alasan_resign);
            //$('[name="jk"]').val(json.dataTables.items[0].jk);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/jenis_kelamin' + '?var=' + json.dataTables.items[0].jk,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {


                // create the option and append to Select2
                var option = new Option(datax.items[0].nmkelamin, datax.items[0].idkelamin, true, true);
                $('[name="jk"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="jk"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            //$('[name="kdagama"]').val(json.dataTables.items[0].kdagama);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/agama' + '?var=' + json.dataTables.items[0].kdagama,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].nmagama, datax.items[0].kdagama, true, true);
                $('[name="kdagama"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="kdagama"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            //$('[name="goldarah"]').val(json.dataTables.items[0].goldarah);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/golongan_darah' + '?var=' + json.dataTables.items[0].goldarah,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].nmgoldarah, datax.items[0].goldarah, true, true);
                $('[name="goldarah"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="goldarah"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            //$('[name="stswn"]').val(json.dataTables.items[0].stswn).trigger('change');
            $.ajax({
                type: 'GET',
                //url: HOST_URL + 'api/globalmodule/list_karyawan_by_id' + '?var=' + json.dataTables.items[0].nik,
                url: HOST_URL + 'api/geolocation/list_negara' + '?var=' + json.dataTables.items[0].stswn,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                //console.log(datax.items[0].nik + 'KONTOL');
                var option = new Option(datax.items[0].namanegara, datax.items[0].kodenegara, true, true);
                $('[name="stswn"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="stswn"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });

            //$('[name="id_division"]').val(json.dataTables.items[0].id_division).trigger('change');
            $.ajax({
                type: 'GET',
                //url: HOST_URL + 'api/globalmodule/list_karyawan_by_id' + '?var=' + json.dataTables.items[0].nik,
                url: HOST_URL + 'api/globalmodule/list_division' + '?var=' + json.dataTables.items[0].id_division,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                //console.log(datax.items[0].nik + 'KONTOL');
                var option = new Option(datax.items[0].nmdivision, datax.items[0].iddivision, true, true);
                $('[name="id_division"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="id_division"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });

            //$('[name="id_dept"]').val(json.dataTables.items[0].id_dept).trigger('change');
            $.ajax({
                type: 'GET',
                //url: HOST_URL + 'api/globalmodule/list_karyawan_by_id' + '?var=' + json.dataTables.items[0].nik,
                url: HOST_URL + 'api/globalmodule/list_departmen' + '?var=' + json.dataTables.items[0].id_dept,
                dataType: 'json',
                delay: 255,
            }).then(function (datax) {
                // create the option and append to Select2
                //console.log(datax.items[0].nik + 'KONTOL');
                var option = new Option(datax.items[0].nmdept, datax.items[0].kddept, true, true);
                $('[name="id_dept"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="id_dept"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            //$('[name="id_subdept"]').val(json.dataTables.items[0].id_subdept).trigger('change');
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_subdepartmen' + '?var=' + json.dataTables.items[0].id_subdept,
                dataType: 'json',
                delay: 300,
            }).then(function (datax) {
                // create the option and append to Select2
                //console.log(datax.items[0].nik + 'KONTOL');
                var option = new Option(datax.items[0].nmsubdept, datax.items[0].kdsubdept, true, true);
                $('[name="id_subdept"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="id_subdept"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            //$('[name="id_jabatan"]').val(json.dataTables.items[0].id_jabatan).trigger('change');
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_jabatan' + '?var=' + json.dataTables.items[0].id_jabatan,
                dataType: 'json',
                delay: 300,
            }).then(function (datax) {
                // create the option and append to Select2
                //console.log(datax.items[0].nik + 'KONTOL');
                var option = new Option(datax.items[0].nmjabatan, datax.items[0].kdjabatan, true, true);
                $('[name="id_jabatan"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="id_jabatan"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            //$('[name="id_grade"]').val(json.dataTables.items[0].id_grade).trigger('change');
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_lvljabatan' + '?var=' + json.dataTables.items[0].id_grade,
                dataType: 'json',
                delay: 300,
            }).then(function (datax) {
                // create the option and append to Select2
                //console.log(datax.items[0].nik + 'KONTOL');
                var option = new Option(datax.items[0].nmlvljabatan, datax.items[0].kdlvl, true, true);
                $('[name="id_grade"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="id_grade"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            //$('[name="costcenter"]').val(json.dataTables.items[0].costcenter);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_costcenter' + '?var=' + json.dataTables.items[0].costcenter,
                dataType: 'json',
                delay: 300,
            }).then(function (datax) {
                // create the option and append to Select2
                //console.log(datax.items[0].nik + 'KONTOL');
                var option = new Option(datax.items[0].nmcostcenter, datax.items[0].idcostcenter, true, true);
                $('[name="costcenter"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="costcenter"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            //$('[name="id_gol"]').val(json.dataTables.items[0].id_gol);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_golongan' + '?var=' + json.dataTables.items[0].id_gol,
                dataType: 'json',
                delay: 100,
            }).then(function (datax) {
                // create the option and append to Select2
                //console.log(datax.items[0].nik + 'KONTOL');
                var option = new Option(datax.items[0].nmgroupgol, datax.items[0].idgroupgol, true, true);
                $('[name="id_gol"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="id_gol"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            //$('[name="plant"]').val(json.dataTables.items[0].plant);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_plant' + '?var=' + json.dataTables.items[0].plant,
                dataType: 'json',
                delay: 100,
            }).then(function (datax) {
                // create the option and append to Select2
                //console.log(datax.items[0].nik + 'KONTOL');
                var option = new Option(datax.items[0].locaname, datax.items[0].loccode, true, true);
                $('[name="plant"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="plant"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            //$('[name="statuskepegawaian"]').val(json.dataTables.items[0].statuskepegawaian);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_kepegawaian' + '?var=' + json.dataTables.items[0].statuskepegawaian,
                dataType: 'json',
                delay: 100,
            }).then(function (datax) {
                // create the option and append to Select2
                //console.log(datax.items[0].nik + 'KONTOL');
                var option = new Option(datax.items[0].nmkepegawaian, datax.items[0].kdkepegawaian, true, true);
                $('[name="statuskepegawaian"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="statuskepegawaian"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            $('[name="tglmasukkerja"]').val(json.dataTables.items[0].tglmasukkerja1);
            //$('[name="nikatasan1"]').val(json.dataTables.items[0].nikatasan1);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_karyawan' + '?var=' + nikatasan1,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].nmlengkap, datax.items[0].nik, true, true);
                $('[name="nikatasan1"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="nikatasan1"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            //$('[name="nikatasan2"]').val(json.dataTables.items[0].nikatasan2);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_karyawan' + '?var=' + nikatasan2,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].nmlengkap, datax.items[0].nik, true, true);
                $('[name="nikatasan2"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="nikatasan2"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            //$('[name="nikatasan3"]').val(json.dataTables.items[0].nikatasan3);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_karyawan' + '?var=' + nikatasan3,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].nmlengkap, datax.items[0].nik, true, true);
                $('[name="nikatasan3"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="nikatasan3"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            $('[name="tglkeluarkerja"]').val(json.dataTables.items[0].tglkeluarkerja1);
            $('[name="id_ktp"]').val(json.dataTables.items[0].id_ktp);
            //$('[name="id_provktp"]').val(json.dataTables.items[0].id_provktp);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/geolocation/list_provinsi' + '?var=' + json.dataTables.items[0].id_provktp,
                dataType: 'json',
                delay: 100,
            }).then(function (datax) {
                // create the option and append to Select2
                //console.log(datax.items[0].nik + 'KONTOL');
                var option = new Option(datax.items[0].namaprov, datax.items[0].kodeprov, true, true);
                $('[name="id_provktp"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="id_provktp"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            //$('[name="id_kotaktp"]').val(json.dataTables.items[0].id_kotaktp);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/geolocation/list_kota' + '?var=' + json.dataTables.items[0].id_kotaktp,
                dataType: 'json',
                delay: 100,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].namakotakab, datax.items[0].kodekotakab, true, true);
                $('[name="id_kotaktp"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="id_kotaktp"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            //$('[name="id_kecktp"]').val(json.dataTables.items[0].id_kecktp);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/geolocation/list_kecamatan' + '?var=' + json.dataTables.items[0].id_kecktp,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].namakec, datax.items[0].kodekec, true, true);
                $('[name="id_kecktp"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="id_kecktp"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            //$('[name="id_desaktp"]').val(json.dataTables.items[0].id_desaktp);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/geolocation/list_desa' + '?var=' + json.dataTables.items[0].id_desaktp,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].namakeldesa, datax.items[0].kodekeldesa, true, true);
                $('[name="id_desaktp"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="id_desaktp"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            $('[name="desc_ktp"]').val(json.dataTables.items[0].desc_ktp);

            //$('[name="id_provdom"]').val(json.dataTables.items[0].id_provdom);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/geolocation/list_provinsi' + '?var=' + json.dataTables.items[0].id_provdom,
                dataType: 'json',
                delay: 100,
            }).then(function (datax) {
                // create the option and append to Select2
                //console.log(datax.items[0].nik + 'KONTOL');
                var option = new Option(datax.items[0].namaprov, datax.items[0].kodeprov, true, true);
                $('[name="id_provdom"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="id_provdom"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            //$('[name="id_kotadom"]').val(json.dataTables.items[0].id_kotadom);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/geolocation/list_kota' + '?var=' + json.dataTables.items[0].id_kotadom,
                dataType: 'json',
                delay: 100,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].namakotakab, datax.items[0].kodekotakab, true, true);
                $('[name="id_kotadom"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="id_kotadom"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            //$('[name="id_kecdom"]').val(json.dataTables.items[0].id_kecdom);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/geolocation/list_kecamatan' + '?var=' + json.dataTables.items[0].id_kecdom,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].namakec, datax.items[0].kodekec, true, true);
                $('[name="id_kecdom"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="id_kecdom"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            //$('[name="id_desadom"]').val(json.dataTables.items[0].id_desadom);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/geolocation/list_desa' + '?var=' + json.dataTables.items[0].id_desadom,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].namakeldesa, datax.items[0].kodekeldesa, true, true);
                $('[name="id_desadom"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="id_desadom"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            $('[name="desc_domisili"]').val(json.dataTables.items[0].desc_domisili);
            $('[name="nokk"]').val(json.dataTables.items[0].nokk);
            $('[name="id_npwp"]').val(json.dataTables.items[0].id_npwp);
            //$('[name="sts_ptkp"]').val(json.dataTables.items[0].sts_ptkp);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_ptkp' + '?var=' + json.dataTables.items[0].sts_ptkp,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].kodeptkp, datax.items[0].kodeptkp, true, true);
                $('[name="sts_ptkp"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="sts_ptkp"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            //$('[name="sts_kawin"]').val(json.dataTables.items[0].sts_kawin);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_ptkp' + '?var=' + json.dataTables.items[0].sts_kawin,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].kodeptkp, datax.items[0].kodeptkp, true, true);
                $('[name="sts_kawin"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="sts_kawin"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            //$('[name="id_jenjang"]').val(json.dataTables.items[0].id_jenjang);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_pendidikan' + '?var=' + json.dataTables.items[0].id_jenjang,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].nmpendidikan, datax.items[0].kdpendidikan, true, true);
                $('[name="id_jenjang"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="id_jenjang"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            $('[name="pendidikan_terakhir"]').val(json.dataTables.items[0].pendidikan_terakhir);
            $('[name="jurusan"]').val(json.dataTables.items[0].jurusan);
            $('[name="ipk"]').val(json.dataTables.items[0].ipk);
            $('[name="lulus_tahun"]').val(json.dataTables.items[0].lulus_tahun);
            $('[name="id_bpjs_kes"]').val(json.dataTables.items[0].id_bpjs_kes);
            $('[name="cab_bpjs_kes"]').val(json.dataTables.items[0].cab_bpjs_kes);
            $('[name="id_bpjs_naker"]').val(json.dataTables.items[0].id_bpjs_naker);
            $('[name="cab_bpjs_naker"]').val(json.dataTables.items[0].cab_bpjs_naker);
            $('[name="nohp1"]').val(json.dataTables.items[0].nohp1);
            $('[name="nohp2"]').val(json.dataTables.items[0].nohp2);
            $('[name="email_self"]').val(json.dataTables.items[0].email_self);
            $('[name="email_office"]').val(json.dataTables.items[0].email_office);
            $('[name="emergency_phone"]').val(json.dataTables.items[0].emergency_phone);
            $('[name="emergency_relation"]').val(json.dataTables.items[0].emergency_relation);
            $('[name="emergency_name"]').val(json.dataTables.items[0].emergency_name);
            $('[name="idabsen"]').val(json.dataTables.items[0].idabsen);
            $('[name="type_office"]').val(json.dataTables.items[0].type_office);
            $('[name="area_covid"]').val(json.dataTables.items[0].area_covid).trigger('change');
            $('[name="statuscovid"]').val(json.dataTables.items[0].statuscovid).trigger('change');
            //$('[name="id_bank"]').val(json.dataTables.items[0].id_bank);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_bank' + '?var=' + json.dataTables.items[0].id_bank,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].nmbank, datax.items[0].kdbank, true, true);
                $('[name="id_bank"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="id_bank"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            $('[name="id_rekening"]').val(json.dataTables.items[0].id_rekening);
            $('[name="nmibukandung"]').val(json.dataTables.items[0].nmibukandung);
            $('[name="nmayahkandung"]').val(json.dataTables.items[0].nmayahkandung);
            // $('[name="kdlembur"]').val(json.dataTables.items[0].kdlembur).trigger('change');
            $('[name="rtktp"]').val(json.dataTables.items[0].rtktp);
            $('[name="rwktp"]').val(json.dataTables.items[0].rwktp);
            $('[name="rtdom"]').val(json.dataTables.items[0].rtdom);
            $('[name="rwdom"]').val(json.dataTables.items[0].rwdom);
            $('[name="id_posktp"]').val(json.dataTables.items[0].id_posktp);
            $('[name="id_posdom"]').val(json.dataTables.items[0].id_posdom);
            $('[name="transportasi"]').val(json.dataTables.items[0].transportasi).trigger('change');
            $('[name="typedomisili"]').val(json.dataTables.items[0].typedomisili).trigger('change');
            $('[name="penyakitbawaan"]').val(json.dataTables.items[0].penyakitbawaan).trigger('change');
            $('[name="nmkeluargapenyakitbawaan"]').val(json.dataTables.items[0].nmkeluargapenyakitbawaan);
            $('[name="nakes"]').val(json.dataTables.items[0].nakes);
            $('[name="nmpasangan"]').val(json.dataTables.items[0].nmpasangan);
            $('[name="pekerjaanpasangan"]').val(json.dataTables.items[0].pekerjaanpasangan);
            $('[name="hubunganpasangan"]').val(json.dataTables.items[0].hubunganpasangan);
            $('[name="contactpasangan"]').val(json.dataTables.items[0].contactpasangan);
            /*
                        var select2_kary = $('.select2_kary');
                        $.ajax({
                            type: 'GET',
                            url: HOST_URL + 'api/globalmodule/list_karyawan_by_id' + '?var=' + json.dataTables.items[0].nik,
                            dataType: 'json',
                            delay: 250,
                        }).then(function (datax) {
                            // create the option and append to Select2
                            //console.log(datax.items[0].nik + 'KONTOL');
                            var option = new Option(datax.items[0].nmlengkap, datax.items[0].nik, true, true);
                            select2_kary.append(option).trigger('change');

                            // manually trigger the `select2:select` event
                            select2_kary.trigger({
                                type: 'select2:select',
                                params: {
                                    data: datax
                                }
                            });
                        });
            */
            $('[name="nik"]').prop('readonly', true);
            $('[name="nmlengkap"]').prop('readonly', true);
            $('[name="callname"]').prop('readonly', true);
            $('[name="tgllahir"]').prop('disabled', true);
            $('[name="alamatlahir"]').prop('disabled', true);
            // $('[name="jk"]').prop('disabled', true);
            // $('[name="kdagama"]').prop('disabled', true);
            // $('[name="goldarah"]').prop('disabled', true);
            // $('[name="stswn"]').prop('disabled', true);
            // $('[name="id_division"]').prop('disabled', true);
            // $('[name="id_dept"]').prop('disabled', true);
            // $('[name="id_subdept"]').prop('disabled', true);
            // $('[name="id_jabatan"]').prop('disabled', true);
            // $('[name="id_grade"]').prop('disabled', true);
            // $('[name="costcenter"]').prop('disabled', true);
            // $('[name="id_gol"]').prop('disabled', true);
            // $('[name="plant"]').prop('disabled', true);
            // $('[name="statuskepegawaian"]').prop('disabled', true);
            // $('[name="tglmasukkerja"]').prop('disabled', true);
            // //$('[name="tglkeluarkerja"]').prop('disabled', true);
            // $('[name="id_ktp"]').prop('disabled', true);
            // $('[name="id_provktp"]').prop('disabled', true);
            // $('[name="id_kotaktp"]').prop('disabled', true);
            // $('[name="id_kecktp"]').prop('disabled', true);
            // $('[name="id_desaktp"]').prop('disabled', true);
            // $('[name="desc_ktp"]').prop('disabled', true);
            // $('[name="id_provdom"]').prop('disabled', true);
            // $('[name="id_kotadom"]').prop('disabled', true);
            // $('[name="id_kecdom"]').prop('disabled', true);
            // $('[name="id_desadom"]').prop('disabled', true);
            // $('[name="desc_domisili"]').prop('disabled', true);
            // $('[name="nokk"]').prop('disabled', true);
            // $('[name="id_npwp"]').prop('disabled', true);
            // $('[name="sts_ptkp"]').prop('disabled', true);
            // $('[name="sts_kawin"]').prop('disabled', true);
            // $('[name="id_jenjang"]').prop('disabled', true);
            // $('[name="pendidikan_terakhir"]').prop('disabled', true);
            // $('[name="jurusan"]').prop('disabled', true);
            // $('[name="ipk"]').prop('disabled', true);
            // $('[name="lulus_tahun"]').prop('disabled', true);
            // $('[name="id_bpjs_kes"]').prop('disabled', true);
            // $('[name="cab_bpjs_kes"]').prop('disabled', true);
            // $('[name="id_bpjs_naker"]').prop('disabled', true);
            // $('[name="cab_bpjs_naker"]').prop('disabled', true);
            // $('[name="nohp1"]').prop('disabled', true);
            // $('[name="nohp2"]').prop('disabled', true);
            // $('[name="email_self"]').prop('disabled', true);
            // $('[name="email_office"]').prop('disabled', true);
            // $('[name="emergency_phone"]').prop('disabled', true);
            // $('[name="emergency_relation"]').prop('disabled', true);
            // $('[name="emergency_name"]').prop('disabled', true);
            // $('[name="idabsen"]').prop('disabled', true);
            // $('[name="type_office"]').prop('disabled', true);
            // $('[name="area_covid"]').prop('disabled', true);
            // $('[name="statuscovid"]').prop('disabled', true);
            // $('[name="id_bank"]').prop('disabled', true);
            // $('[name="id_rekening"]').prop('disabled', true);
            // $('[name="nmibukandung"]').prop('disabled', true);
            // $('[name="nmayahkandung"]').prop('disabled', true);
            // ////$('[name="alasan_resign"]').prop('disabled', true);
            // $('[name="nikatasan1"]').prop('disabled', true);
            // $('[name="nikatasan2"]').prop('disabled', true);
            // $('[name="nikatasan3"]').prop('disabled', true);
            // $('[name="rtktp"]').prop('disabled', true);
            // $('[name="rwktp"]').prop('disabled', true);
            // $('[name="rtdom"]').prop('disabled', true);
            // $('[name="rwdom"]').prop('disabled', true);
            // $('[name="id_posktp"]').prop('disabled', true);
            // $('[name="id_posdom"]').prop('disabled', true);
            // $('[name="transportasi"]').prop('disabled', true);
            // $('[name="typedomisili"]').prop('disabled', true);
            // $('[name="penyakitbawaan"]').prop('disabled', true);
            // $('[name="nmkeluargapenyakitbawaan"]').prop('disabled', true);
            // $('[name="nakes"]').prop('disabled', true);
            // $('[name="nmpasangan"]').prop('disabled', true);
            // $('[name="pekerjaanpasangan"]').prop('disabled', true);
            // $('[name="hubunganpasangan"]').prop('disabled', true);
            // $('[name="contactpasangan"]').prop('disabled', true);

            var ih = $("#image-holder");
            ih.empty();
            if (json.dataTables.items[0].cimage) {
                $("<img />", {
                    "src": HOST_URL+'/assets/img/profile' + '/' + json.dataTables.items[0].cimage,
                    "class": "thumb-image",
                    "style": "max-width: 250px; max-height: 250px;"
                }).appendTo(ih);
            } else {
                $("<img />", {
                    "src": HOST_URL+'/assets/img/user.png',
                    "class": "thumb-image",
                    "style": "max-width: 250px; max-height: 250px;"
                }).appendTo(ih);
            }
            //$('[name="cimage"]').prop('disabled', true);

            //$('#btnSave').hide();


        },
        complete: function(){
            $("#loadMe").modal("hide");
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            console.log("Failed To Loading Data");
            $("#loadMe").modal("hide");
        }
    });

    $("#loadMe").modal("hide");
}



// +++++++++++++++++++++++++++++++++++++++++++++++++++++ RANAH PERMISSION ++++++++++++++++++++++++++++++++++++++++//


// GOLONGAN DARAH

function formatRepoDarah(repo) {
    if (repo.loading) return repo.text;
    var markup = "<div class='select2-result-repository__description'>" + repo.goldarah +" <i class='fa fa-circle-o'></i> "+ " " + repo.nmgoldarah + "</div>";

    return markup;
}

function formatRepoSelectionDarah(repo) {
    return repo.nmgoldarah || repo.text;
}
var defaultInitialDarah = '';
$("#goldarah").select2({
    placeholder: "Ketik Dan Pilih Jenis Golongan Darah",
    allowClear: true,
    ajax: {
        url: HOST_URL + 'api/globalmodule/golongan_darah',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: function(params) {
            return {
                _search_: params.term, // search term
                _page_: params.page,
                _draw_: true,
                _start_: 1,
                _perpage_: 2,
                _paramglobal_: defaultInitialDarah,
            };
        },
        processResults: function(data, params) {
            // parse the results into the format expected by Select2
            // since we are using custom formatting functions we do not need to
            // alter the remote JSON data, except to indicate that infinite
            // scrolling can be used
            params.page = params.page || 1;

            return {
                results: data.items,
                pagination: {
                    more: (params.page * 30) < data.total_count
                }
            };
        },
        cache: true
    },
    escapeMarkup: function(markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatRepoDarah, // omitted for brevity, see the source of this page
    templateSelection: formatRepoSelectionDarah // omitted for brevity, see the source of this page
});



// JENIS KELAMIN

function formatRepojk(repo) {
    if (repo.loading) return repo.text;
    var markup = "<div class='select2-result-repository__description'>" + repo.idkelamin +" <i class='fa fa-circle-o'></i> "+ " " + repo.nmkelamin + "</div>";

    return markup;
}

function formatRepoSelectionjk(repo) {
    return repo.nmkelamin || repo.text;
}
var defaultInitialsp = '';
$("#jk").select2({
    placeholder: "Ketik Dan Pilih Jenis Kelamin",
    allowClear: true,
    ajax: {
        url: HOST_URL + 'api/globalmodule/jenis_kelamin',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: function(params) {
            return {
                _search_: params.term, // search term
                _page_: params.page,
                _draw_: true,
                _start_: 1,
                _perpage_: 2,
                _paramglobal_: defaultInitialsp,
            };
        },
        processResults: function(data, params) {
            // parse the results into the format expected by Select2
            // since we are using custom formatting functions we do not need to
            // alter the remote JSON data, except to indicate that infinite
            // scrolling can be used
            params.page = params.page || 1;

            return {
                results: data.items,
                pagination: {
                    more: (params.page * 30) < data.total_count
                }
            };
        },
        cache: true
    },
    escapeMarkup: function(markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatRepojk, // omitted for brevity, see the source of this page
    templateSelection: formatRepoSelectionjk // omitted for brevity, see the source of this page
});

// JENIS AGAMA

function formatRepokdagama(repo) {
    if (repo.loading) return repo.text;
    var markup = "<div class='select2-result-repository__description'>" + repo.kdagama +" <i class='fa fa-circle-o'></i> "+ " " + repo.nmagama + "</div>";

    return markup;
}
function formatRepoSelectionkdagama(repo) {
    return repo.nmagama || repo.text;
}
var defaultInitialsp = '';
$("#kdagama").select2({
    placeholder: "Ketik Dan Pilih Agama",
    allowClear: true,
    ajax: {
        url: HOST_URL + 'api/globalmodule/agama',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: function(params) {
            return {
                _search_: params.term, // search term
                _page_: params.page,
                _draw_: true,
                _start_: 1,
                _perpage_: 2,
                _paramglobal_: defaultInitialsp,
            };
        },
        processResults: function(data, params) {
            // parse the results into the format expected by Select2
            // since we are using custom formatting functions we do not need to
            // alter the remote JSON data, except to indicate that infinite
            // scrolling can be used
            params.page = params.page || 1;

            return {
                results: data.items,
                pagination: {
                    more: (params.page * 30) < data.total_count
                }
            };
        },
        cache: true
    },
    escapeMarkup: function(markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatRepokdagama, // omitted for brevity, see the source of this page
    templateSelection: formatRepoSelectionkdagama // omitted for brevity, see the source of this page
});


//NEGARA

function formatstswn(repo) {
    if (repo.loading) return repo.text;
    var markup = "<div class='select2-result-repository__description'>" + repo.kodenegara +" <i class='fa fa-circle-o'></i> "+ " " + repo.namanegara + "</div>";
    return markup;
}

function formatstswnSelection(repo) {
    return repo.namanegara || repo.text;
}
var defaultInitialstswn = '';
$("#stswn").select2({
    placeholder: "Ketik/Pilih Negara",
    allowClear: true,
    ajax: {
        url: HOST_URL + 'api/geolocation/list_negara',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: function(params) {
            return {
                _search_: params.term, // search term
                _page_: params.page,
                _draw_: true,
                _start_: 1,
                _perpage_: 2,
                _paramglobal_: defaultInitialstswn,
            };
        },
        processResults: function(data, params) {
            // parse the results into the format expected by Select2
            // since we are using custom formatting functions we do not need to
            // alter the remote JSON data, except to indicate that infinite
            // scrolling can be used
            params.page = params.page || 1;

            return {
                results: data.items,
                pagination: {
                    more: (params.page * 30) < data.total_count
                }
            };
        },
        cache: true
    },
    escapeMarkup: function(markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatstswn, // omitted for brevity, see the source of this page
    templateSelection: formatstswnSelection // omitted for brevity, see the source of this page
}).on("select2:selecting", function () {
    //$("#newsubdept option[value]").remove();
    //var newOptions = []; // the result of your JSON request
    //$("#stswn").val(null).trigger('change');
    //console.log($("#newdept").val());
});

/////////////////////////////////// DEPARTEMENT AREA ////////////////////////////////////////////////////////

function formatNewdept(repo) {
    if (repo.loading) return repo.text;
    var markup = "<div class='select2-result-repository__description'>" + repo.kddept +"   <i class='fa fa-circle-o'></i>   "+ repo.nmdept +"  </div>";
    return markup;
}

function formatNewdeptSelection(repo) {
    return repo.nmdept || repo.text;
}
var defaultInitialNewDept = '';
$("#id_dept").select2({
    placeholder: "Ketik/Pilih Departemen",
    allowClear: true,
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_departmen',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: function(params) {
            return {
                _search_: params.term, // search term
                _page_: params.page,
                _draw_: true,
                _start_: 1,
                _perpage_: 2,
                _paramglobal_: defaultInitialNewDept,
            };
        },
        processResults: function(data, params) {
            // parse the results into the format expected by Select2
            // since we are using custom formatting functions we do not need to
            // alter the remote JSON data, except to indicate that infinite
            // scrolling can be used
            params.page = params.page || 1;

            return {
                results: data.items,
                pagination: {
                    more: (params.page * 30) < data.total_count
                }
            };
        },
        cache: false
    },
    escapeMarkup: function(markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatNewdept, // omitted for brevity, see the source of this page
    templateSelection: formatNewdeptSelection // omitted for brevity, see the source of this page
}).on("select2:selecting", function () {
    //$("#newsubdept option[value]").remove();
    //var newOptions = []; // the result of your JSON request
    $("#id_subdept").val(null).trigger('change');
    //console.log($("#newdept").val());
});


/*sub departement*/
function formatNewsubdept(repo) {
    if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.kdsubdept +"   <i class='fa fa-circle-o'></i>   "+ repo.nmsubdept +"</div>";
    return markup;
}

function formatNewsubdeptSelection(repo) {
    return repo.nmsubdept || repo.text;
}
//var defaultInitialNewSubDept = $("#newdept").val();
$("#id_subdept").select2({
    placeholder: "Ketik/Pilih Sub Departemen/Section ",
    allowClear: true,
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_subdepartmen',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: function(params) {
            return {
                _search_: params.term, // search term
                _page_: params.page,
                _draw_: true,
                _start_: 1,
                _perpage_: 2,
                _paramglobal_: $("#id_dept").val(),
            };
        },
        processResults: function(data, params) {
            // parse the results into the format expected by Select2
            // since we are using custom formatting functions we do not need to
            // alter the remote JSON data, except to indicate that infinite
            // scrolling can be used
            params.page = params.page || 1;

            return {
                results: data.items,
                pagination: {
                    more: (params.page * 30) < data.total_count
                }
            };
        },
        cache: true
    },
    escapeMarkup: function(markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatNewsubdept, // omitted for brevity, see the source of this page
    templateSelection: formatNewsubdeptSelection // omitted for brevity, see the source of this page
}).on("select2:selecting", function () {
    // $("#newjabatan option[value]").remove();
    // var newOptions = []; // the result of your JSON request
    // $("#newjabatan").append(newOptions).val("").trigger("change");
    $("#id_jabatan").val(null).trigger('change');
});





/* jabatan */
function formatJabatan(repo) {
    if (repo.loading) return repo.text;
    var markup = "<div class='select2-result-repository__description'>" + repo.kdjabatan +" <i class='fa fa-circle-o'></i> "+ repo.nmjabatan +"</div>";
    return markup;
}

function formatJabatanSelection(repo) {
    return repo.nmjabatan || repo.text;
}
//var defaultInitialNewSubDept = $("#newdept").val();
$("#id_jabatan").select2({
    placeholder: "Ketik/Pilih Jabatan",
    allowClear: true,
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_jabatan',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: function(params) {
            return {
                _search_: params.term, // search term
                _page_: params.page,
                _draw_: true,
                _start_: 1,
                _perpage_: 2,
                _paramglobal_: $("#id_subdept").val(),
            };
        },
        processResults: function(data, params) {
            // parse the results into the format expected by Select2
            // since we are using custom formatting functions we do not need to
            // alter the remote JSON data, except to indicate that infinite
            // scrolling can be used
            params.page = params.page || 1;

            return {
                results: data.items,
                pagination: {
                    more: (params.page * 30) < data.total_count
                }
            };
        },
        cache: true
    },
    escapeMarkup: function(markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatJabatan, // omitted for brevity, see the source of this page
    templateSelection: formatJabatanSelection // omitted for brevity, see the source of this page
}).on("select2:selecting", function () {
    // $("#newjabatan option[value]").remove();
    // var newOptions = []; // the result of your JSON request
    // $("#newjabatan").append(newOptions).val("").trigger("change");
    $("#id_grade").val(null).trigger('change');
});


/* lvl JABATAN */
function formatLvlJabatan(repo) {
    if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.kdlvl +" <i class='fa fa-circle-o'></i> "+ repo.nmlvljabatan +"</div>";
    return markup;
}

function formatLvlJabatanSelection(repo) {
    return repo.nmlvljabatan || repo.text;
}
//var defaultInitialNewSubDept = $("#newdept").val();
$("#id_grade").select2({
    placeholder: "Ketik/Pilih Level Jabatan/Grade",
    allowClear: true,
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_lvljabatan',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: function(params) {
            return {
                _search_: params.term, // search term
                _page_: params.page,
                _draw_: true,
                _start_: 1,
                _perpage_: 2,
                _paramglobal_: '',
            };
        },
        processResults: function(data, params) {
            // parse the results into the format expected by Select2
            // since we are using custom formatting functions we do not need to
            // alter the remote JSON data, except to indicate that infinite
            // scrolling can be used
            params.page = params.page || 1;

            return {
                results: data.items,
                pagination: {
                    more: (params.page * 30) < data.total_count
                }
            };
        },
        cache: true
    },
    escapeMarkup: function(markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatLvlJabatan, // omitted for brevity, see the source of this page
    templateSelection: formatLvlJabatanSelection // omitted for brevity, see the source of this page
});

///division
/* lvl JABATAN */
function formatDivision(repo) {
    if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.iddivision +"   <i class='fa fa-circle-o'></i>   "+ repo.nmdivision +"</div>";
    return markup;
}

function formatDivisionSelection(repo) {
    return repo.nmdivision || repo.text;
}
//var defaultInitialDivision = $("#newdept").val();
$("#id_division").select2({
    placeholder: "Ketik/Pilih Division",
    allowClear: true,
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_division',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: function(params) {
            return {
                _search_: params.term, // search term
                _page_: params.page,
                _draw_: true,
                _start_: 1,
                _perpage_: 2,
                _paramglobal_: '',
            };
        },
        processResults: function(data, params) {
            // parse the results into the format expected by Select2
            // since we are using custom formatting functions we do not need to
            // alter the remote JSON data, except to indicate that infinite
            // scrolling can be used
            params.page = params.page || 1;

            return {
                results: data.items,
                pagination: {
                    more: (params.page * 30) < data.total_count
                }
            };
        },
        cache: true
    },
    escapeMarkup: function(markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatDivision, // omitted for brevity, see the source of this page
    templateSelection: formatDivisionSelection // omitted for brevity, see the source of this page
});

// COST CENTER
function formatCostcenter(repo) {
    if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.idcostcenter +"   <i class='fa fa-circle-o'></i>   "+ repo.nmcostcenter +"</div>";
    return markup;
}

function formatCostcenterSelection(repo) {
    return repo.nmcostcenter || repo.text;
}
//var defaultInitialDivision = $("#newdept").val();
$("#costcenter").select2({
    placeholder: "Ketik/Pilih Cost Center",
    allowClear: true,
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_costcenter',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: function(params) {
            return {
                _search_: params.term, // search term
                _page_: params.page,
                _draw_: true,
                _start_: 1,
                _perpage_: 2,
                _paramglobal_: '',
            };
        },
        processResults: function(data, params) {
            // parse the results into the format expected by Select2
            // since we are using custom formatting functions we do not need to
            // alter the remote JSON data, except to indicate that infinite
            // scrolling can be used
            params.page = params.page || 1;

            return {
                results: data.items,
                pagination: {
                    more: (params.page * 30) < data.total_count
                }
            };
        },
        cache: true
    },
    escapeMarkup: function(markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatCostcenter, // omitted for brevity, see the source of this page
    templateSelection: formatCostcenterSelection // omitted for brevity, see the source of this page
});



function formatGol(repo) {
    if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.idgroupgol +"   <i class='fa fa-circle-o'></i>   "+ repo.nmgroupgol +"</div>";
    return markup;
}

function formatGolSelection(repo) {
    return repo.nmgroupgol || repo.text;
}
//var defaultInitialGol = $("#newdept").val();
$("#id_gol").select2({
    placeholder: "Ketik/Pilih Golongan",
    allowClear: true,
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_golongan',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: function(params) {
            return {
                _search_: params.term, // search term
                _page_: params.page,
                _draw_: true,
                _start_: 1,
                _perpage_: 2,
                _paramglobal_: '',
            };
        },
        processResults: function(data, params) {
            // parse the results into the format expected by Select2
            // since we are using custom formatting functions we do not need to
            // alter the remote JSON data, except to indicate that infinite
            // scrolling can be used
            params.page = params.page || 1;

            return {
                results: data.items,
                pagination: {
                    more: (params.page * 30) < data.total_count
                }
            };
        },
        cache: true
    },
    escapeMarkup: function(markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatGol, // omitted for brevity, see the source of this page
    templateSelection: formatGolSelection // omitted for brevity, see the source of this page
});


// warehouse/plant

function formatPlant(repo) {
    if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.loccode +"   <i class='fa fa-circle-o'></i>   "+ repo.locaname +"</div>";
    return markup;
}

function formatPlantSelection(repo) {
    return repo.locaname || repo.text;
}
//var defaultInitialGol = $("#newdept").val();
$("#plant").select2({
    placeholder: "Ketik/Pilih Plant",
    allowClear: true,
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_plant',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: function(params) {
            return {
                _search_: params.term, // search term
                _page_: params.page,
                _draw_: true,
                _start_: 1,
                _perpage_: 2,
                _paramglobal_: '',
            };
        },
        processResults: function(data, params) {
            // parse the results into the format expected by Select2
            // since we are using custom formatting functions we do not need to
            // alter the remote JSON data, except to indicate that infinite
            // scrolling can be used
            params.page = params.page || 1;

            return {
                results: data.items,
                pagination: {
                    more: (params.page * 30) < data.total_count
                }
            };
        },
        cache: true
    },
    escapeMarkup: function(markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatPlant, // omitted for brevity, see the source of this page
    templateSelection: formatPlantSelection // omitted for brevity, see the source of this page
});

//// status kepegawaian


function formatStatuskepegawaian(repo) {
    if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.kdkepegawaian +"   <i class='fa fa-circle-o'></i>   "+ repo.nmkepegawaian +"</div>";
    return markup;
}

function formatStatuskepegawaianSelection(repo) {
    return repo.nmkepegawaian || repo.text;
}
//var defaultInitialGol = $("#newdept").val();
$("#statuskepegawaian").select2({
    placeholder: "Ketik/Pilih Status Kepegawaian",
    allowClear: true,
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_kepegawaian',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: function(params) {
            return {
                _search_: params.term, // search term
                _page_: params.page,
                _draw_: true,
                _start_: 1,
                _perpage_: 2,
                _paramglobal_: '',
            };
        },
        processResults: function(data, params) {
            // parse the results into the format expected by Select2
            // since we are using custom formatting functions we do not need to
            // alter the remote JSON data, except to indicate that infinite
            // scrolling can be used
            params.page = params.page || 1;

            return {
                results: data.items,
                pagination: {
                    more: (params.page * 30) < data.total_count
                }
            };
        },
        cache: true
    },
    escapeMarkup: function(markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatStatuskepegawaian, // omitted for brevity, see the source of this page
    templateSelection: formatStatuskepegawaianSelection // omitted for brevity, see the source of this page
});




/////////////////////////////////// END DEPARTEMENT AREA ////////////////////////////////////////////////////////
/////////////////////////////////// START GEOLOCATION AREA ////////////////////////////////////////////////////////


function formatProvinsi(repo) {
    if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.kodeprov +"   <i class='fa fa-circle-o'></i>   "+ repo.namaprov +"</div>";
    return markup;
}

function formatProvinsiSelection(repo) {
    return repo.namaprov || repo.text;
}
//var defaultInitialGol = $("#newdept").val();
$("#id_provktp").select2({
    placeholder: "Ketik/Pilih Provinsi KTP",
    allowClear: true,
    ajax: {
        url: HOST_URL + 'api/geolocation/list_provinsi',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: function(params) {
            return {
                _search_: params.term, // search term
                _page_: params.page,
                _draw_: true,
                _start_: 1,
                _perpage_: 2,
                _paramglobal_: '',
            };
        },
        processResults: function(data, params) {
            // parse the results into the format expected by Select2
            // since we are using custom formatting functions we do not need to
            // alter the remote JSON data, except to indicate that infinite
            // scrolling can be used
            params.page = params.page || 1;

            return {
                results: data.items,
                pagination: {
                    more: (params.page * 30) < data.total_count
                }
            };
        },
        cache: true
    },
    escapeMarkup: function(markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatProvinsi, // omitted for brevity, see the source of this page
    templateSelection: formatProvinsiSelection // omitted for brevity, see the source of this page
}).on("select2:selecting", function () {
    $("#id_kotaktp").val(null).trigger('change');
    $("#id_desaktp").val(null).trigger('change');
    $("#id_kecktp").val(null).trigger('change');
});
$("#id_provdom").select2({
    placeholder: "Ketik/Pilih Provinsi Domisili",
    allowClear: true,
    ajax: {
        url: HOST_URL + 'api/geolocation/list_provinsi',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: function(params) {
            return {
                _search_: params.term, // search term
                _page_: params.page,
                _draw_: true,
                _start_: 1,
                _perpage_: 2,
                _paramglobal_: '',
            };
        },
        processResults: function(data, params) {
            // parse the results into the format expected by Select2
            // since we are using custom formatting functions we do not need to
            // alter the remote JSON data, except to indicate that infinite
            // scrolling can be used
            params.page = params.page || 1;

            return {
                results: data.items,
                pagination: {
                    more: (params.page * 30) < data.total_count
                }
            };
        },
        cache: true
    },
    escapeMarkup: function(markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatProvinsi, // omitted for brevity, see the source of this page
    templateSelection: formatProvinsiSelection // omitted for brevity, see the source of this page
}).on("select2:selecting", function () {
    $("#id_kotadom").val(null).trigger('change');
    $("#id_desadom").val(null).trigger('change');
    $("#id_kecdom").val(null).trigger('change');
});
// kotakab
function formatKota(repo) {
    if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.kodekotakab +"   <i class='fa fa-circle-o'></i>   "+ repo.namakotakab +"</div>";
    return markup;
}
function formatKotaSelection(repo) {
    return repo.namakotakab || repo.text;
}
//var defaultInitialGol = $("#newdept").val();
$("#id_kotaktp").select2({
    placeholder: "Ketik/Pilih Kota KTP",
    allowClear: true,
    ajax: {
        url: HOST_URL + 'api/geolocation/list_kota',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: function(params) {
            return {
                _search_: params.term, // search term
                _page_: params.page,
                _draw_: true,
                _start_: 1,
                _perpage_: 2,
                _paramglobal_: $('#id_provktp').val(),
            };
        },
        processResults: function(data, params) {
            // parse the results into the format expected by Select2
            // since we are using custom formatting functions we do not need to
            // alter the remote JSON data, except to indicate that infinite
            // scrolling can be used
            params.page = params.page || 1;

            return {
                results: data.items,
                pagination: {
                    more: (params.page * 30) < data.total_count
                }
            };
        },
        cache: true
    },
    escapeMarkup: function(markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatKota, // omitted for brevity, see the source of this page
    templateSelection: formatKotaSelection // omitted for brevity, see the source of this page
}).on("select2:selecting", function () {
    $("#id_desaktp").val(null).trigger('change');
    $("#id_kecktp").val(null).trigger('change');
});
$("#id_kotadom").select2({
    placeholder: "Ketik/Pilih Kota Domisili",
    allowClear: true,
    ajax: {
        url: HOST_URL + 'api/geolocation/list_kota',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: function(params) {
            return {
                _search_: params.term, // search term
                _page_: params.page,
                _draw_: true,
                _start_: 1,
                _perpage_: 2,
                _paramglobal_: $('#id_provdom').val(),
            };
        },
        processResults: function(data, params) {
            // parse the results into the format expected by Select2
            // since we are using custom formatting functions we do not need to
            // alter the remote JSON data, except to indicate that infinite
            // scrolling can be used
            params.page = params.page || 1;

            return {
                results: data.items,
                pagination: {
                    more: (params.page * 30) < data.total_count
                }
            };
        },
        cache: true
    },
    escapeMarkup: function(markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatKota, // omitted for brevity, see the source of this page
    templateSelection: formatKotaSelection // omitted for brevity, see the source of this page
}).on("select2:selecting", function () {
    $("#id_desadom").val(null).trigger('change');
    $("#id_kecdom").val(null).trigger('change');
});

// kotakab

function formatKecamatan(repo) {
    if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.kodekec +"   <i class='fa fa-circle-o'></i>   "+ repo.namakec +"</div>";
    return markup;
}

function formatKecamatanSelection(repo) {
    return repo.namakec || repo.text;
}
//var defaultInitialGol = $("#newdept").val();
$("#id_kecktp").select2({
    placeholder: "Ketik/Pilih Kecamatan KTP",
    allowClear: true,
    ajax: {
        url: HOST_URL + 'api/geolocation/list_kecamatan',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: function(params) {
            return {
                _search_: params.term, // search term
                _page_: params.page,
                _draw_: true,
                _start_: 1,
                _perpage_: 2,
                _paramglobal_: $('#id_kotaktp').val(),
            };
        },
        processResults: function(data, params) {
            // parse the results into the format expected by Select2
            // since we are using custom formatting functions we do not need to
            // alter the remote JSON data, except to indicate that infinite
            // scrolling can be used
            params.page = params.page || 1;

            return {
                results: data.items,
                pagination: {
                    more: (params.page * 30) < data.total_count
                }
            };
        },
        cache: true
    },
    escapeMarkup: function(markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatKecamatan, // omitted for brevity, see the source of this page
    templateSelection: formatKecamatanSelection // omitted for brevity, see the source of this page
}).on("select2:selecting", function () {
    $("#id_desaktp").val(null).trigger('change');
});
//domisili
$("#id_kecdom").select2({
    placeholder: "Ketik/Pilih Kecamatan Domisili",
    allowClear: true,
    ajax: {
        url: HOST_URL + 'api/geolocation/list_kecamatan',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: function(params) {
            return {
                _search_: params.term, // search term
                _page_: params.page,
                _draw_: true,
                _start_: 1,
                _perpage_: 2,
                _paramglobal_: $('#id_kotadom').val(),
            };
        },
        processResults: function(data, params) {
            // parse the results into the format expected by Select2
            // since we are using custom formatting functions we do not need to
            // alter the remote JSON data, except to indicate that infinite
            // scrolling can be used
            params.page = params.page || 1;

            return {
                results: data.items,
                pagination: {
                    more: (params.page * 30) < data.total_count
                }
            };
        },
        cache: true
    },
    escapeMarkup: function(markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatKecamatan, // omitted for brevity, see the source of this page
    templateSelection: formatKecamatanSelection // omitted for brevity, see the source of this page
}).on("select2:selecting", function () {
    $("#id_desadom").val(null).trigger('change');
});


// kotakab

function formatDesa(repo) {
    if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.kodekeldesa +"   <i class='fa fa-circle-o'></i>   "+ repo.namakeldesa +"</div>";
    return markup;
}

function formatDesaSelection(repo) {
    return repo.namakeldesa || repo.text;
}
//var defaultInitialGol = $("#newdept").val();
$("#id_desaktp").select2({
    placeholder: "Ketik/Pilih Desa KTP",
    allowClear: true,
    ajax: {
        url: HOST_URL + 'api/geolocation/list_desa',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: function(params) {
            return {
                _search_: params.term, // search term
                _page_: params.page,
                _draw_: true,
                _start_: 1,
                _perpage_: 2,
                _paramglobal_: $('#id_kecktp').val(),
            };
        },
        processResults: function(data, params) {
            // parse the results into the format expected by Select2
            // since we are using custom formatting functions we do not need to
            // alter the remote JSON data, except to indicate that infinite
            // scrolling can be used
            params.page = params.page || 1;

            return {
                results: data.items,
                pagination: {
                    more: (params.page * 30) < data.total_count
                }
            };
        },
        cache: true
    },
    escapeMarkup: function(markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatDesa, // omitted for brevity, see the source of this page
    templateSelection: formatDesaSelection // omitted for brevity, see the source of this page
});

$("#id_desadom").select2({
    placeholder: "Ketik/Pilih Desa Domisili",
    allowClear: true,
    ajax: {
        url: HOST_URL + 'api/geolocation/list_desa',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: function(params) {
            return {
                _search_: params.term, // search term
                _page_: params.page,
                _draw_: true,
                _start_: 1,
                _perpage_: 2,
                _paramglobal_: $('#id_kecdom').val(),
            };
        },
        processResults: function(data, params) {
            // parse the results into the format expected by Select2
            // since we are using custom formatting functions we do not need to
            // alter the remote JSON data, except to indicate that infinite
            // scrolling can be used
            params.page = params.page || 1;

            return {
                results: data.items,
                pagination: {
                    more: (params.page * 30) < data.total_count
                }
            };
        },
        cache: true
    },
    escapeMarkup: function(markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatDesa, // omitted for brevity, see the source of this page
    templateSelection: formatDesaSelection // omitted for brevity, see the source of this page
});
/////////////////////////////////// END GEOLOCATION AREA ////////////////////////////////////////////////////////



function formatStatusptkp(repo) {
    if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.kodeptkp +"   <i class='fa fa-circle-o'></i>   "+ repo.besaranpertahun +"</div>";
    return markup;
}

function formatStatusptkpSelection(repo) {
    return repo.kodeptkp || repo.text;
}
//var defaultInitialGol = $("#newdept").val();
$("#sts_ptkp").select2({
    placeholder: "Ketik/Pilih Status PTKP",
    allowClear: true,
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_ptkp',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: function(params) {
            return {
                _search_: params.term, // search term
                _page_: params.page,
                _draw_: true,
                _start_: 1,
                _perpage_: 2,
                _paramglobal_: '',
            };
        },
        processResults: function(data, params) {
            params.page = params.page || 1;
            return {
                results: data.items,
                pagination: {
                    more: (params.page * 30) < data.total_count
                }
            };
        },
        cache: true
    },
    escapeMarkup: function(markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatStatusptkp, // omitted for brevity, see the source of this page
    templateSelection: formatStatusptkpSelection // omitted for brevity, see the source of this page
});

///////////// status kawin

function formatStatusKawin(repo) {
    if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.kodeptkp +"   <i class='fa fa-circle-o'></i>   "+ repo.kodeptkp +"</div>";
    return markup;
}

function formatStatusKawinSelection(repo) {
    return repo.kodeptkp || repo.text;
}
//var defaultInitialGol = $("#newdept").val();
$("#sts_kawin").select2({
    placeholder: "Ketik/Pilih Status Kawin",
    allowClear: true,
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_ptkp',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: function(params) {
            return {
                _search_: params.term, // search term
                _page_: params.page,
                _draw_: true,
                _start_: 1,
                _perpage_: 2,
                _paramglobal_: '',
            };
        },
        processResults: function(data, params) {
            params.page = params.page || 1;
            return {
                results: data.items,
                pagination: {
                    more: (params.page * 30) < data.total_count
                }
            };
        },
        cache: true
    },
    escapeMarkup: function(markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatStatusKawin, // omitted for brevity, see the source of this page
    templateSelection: formatStatusKawinSelection // omitted for brevity, see the source of this page
});

///////////// jenjang pendidikan

function formatIdJenjang(repo) {
    if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.kdpendidikan +"   <i class='fa fa-circle-o'></i>   "+ repo.nmpendidikan +"</div>";
    return markup;
}

function formatIdJenjangSelection(repo) {
    return repo.nmpendidikan || repo.text;
}
//var defaultInitialGol = $("#newdept").val();

$("#id_jenjang").select2({
    placeholder: "Ketik/Pilih Jenjang Pendidikan",
    allowClear: true,
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_pendidikan',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: function(params) {
            return {
                _search_: params.term, // search term
                _page_: params.page,
                _draw_: true,
                _start_: 1,
                _perpage_: 2,
                _paramglobal_: '',
            };
        },
        processResults: function(data, params) {
            params.page = params.page || 1;
            return {
                results: data.items,
                pagination: {
                    more: (params.page * 30) < data.total_count
                }
            };
        },
        cache: true
    },
    escapeMarkup: function(markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatIdJenjang, // omitted for brevity, see the source of this page
    templateSelection: formatIdJenjangSelection // omitted for brevity, see the source of this page
});

///////////// ID BANK

function formatIdBank(repo) {
    if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.kdbank +"   <i class='fa fa-circle-o'></i>   "+ repo.nmbank +"</div>";
    return markup;
}

function formatIdBankSelection(repo) {
    return repo.nmbank || repo.text;
}
//var defaultInitialGol = $("#newdept").val();
$("#id_bank").select2({
    placeholder: "Ketik/Pilih Bank",
    allowClear: true,
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_bank',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: function(params) {
            return {
                _search_: params.term, // search term
                _page_: params.page,
                _draw_: true,
                _start_: 1,
                _perpage_: 2,
                _paramglobal_: '',
            };
        },
        processResults: function(data, params) {
            params.page = params.page || 1;
            return {
                results: data.items,
                pagination: {
                    more: (params.page * 30) < data.total_count
                }
            };
        },
        cache: true
    },
    escapeMarkup: function(markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatIdBank, // omitted for brevity, see the source of this page
    templateSelection: formatIdBankSelection // omitted for brevity, see the source of this page
});

///////////// NIK ATASAN / ATASAN KARYAWAN
function formatRepoNik(repo) {
    if (repo.loading) return repo.text;
    var markup = "<div class='select2-result-repository__description'>" + repo.nik +" <i class='fa fa-circle-o'></i> "+ " " + repo.nmlengkap + "</div>";

    return markup;
}

function formatRepoSelectionNik(repo) {
    return repo.nmlengkap || repo.text;
}
var defaultInitialnikatasan = '';
$("#nikatasan1").select2({
    placeholder: "Ketik Dan Pilih Atasan 1",
    allowClear: true,
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_karyawan',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: function(params) {
            return {
                _search_: params.term, // search term
                _page_: params.page,
                _draw_: true,
                _start_: 1,
                _perpage_: 2,
                _paramglobal_: defaultInitialnikatasan,
            };
        },
        processResults: function(data, params) {
            // parse the results into the format expected by Select2
            // since we are using custom formatting functions we do not need to
            // alter the remote JSON data, except to indicate that infinite
            // scrolling can be used
            params.page = params.page || 1;

            return {
                results: data.items,
                pagination: {
                    more: (params.page * 30) < data.total_count
                }
            };
        },
        cache: true
    },
    escapeMarkup: function(markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatRepoNik, // omitted for brevity, see the source of this page
    templateSelection: formatRepoSelectionNik // omitted for brevity, see the source of this page
});
$("#nikatasan2").select2({
    placeholder: "Ketik Dan Pilih Atasan 2",
    allowClear: true,
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_karyawan',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: function(params) {
            return {
                _search_: params.term, // search term
                _page_: params.page,
                _draw_: true,
                _start_: 1,
                _perpage_: 2,
                _paramglobal_: defaultInitialnikatasan,
            };
        },
        processResults: function(data, params) {
            // parse the results into the format expected by Select2
            // since we are using custom formatting functions we do not need to
            // alter the remote JSON data, except to indicate that infinite
            // scrolling can be used
            params.page = params.page || 1;

            return {
                results: data.items,
                pagination: {
                    more: (params.page * 30) < data.total_count
                }
            };
        },
        cache: true
    },
    escapeMarkup: function(markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatRepoNik, // omitted for brevity, see the source of this page
    templateSelection: formatRepoSelectionNik // omitted for brevity, see the source of this page
});
$("#nikatasan3").select2({
    placeholder: "Ketik Dan Pilih Atasan 3",
    allowClear: true,
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_karyawan',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: function(params) {
            return {
                _search_: params.term, // search term
                _page_: params.page,
                _draw_: true,
                _start_: 1,
                _perpage_: 2,
                _paramglobal_: defaultInitialnikatasan,
            };
        },
        processResults: function(data, params) {
            // parse the results into the format expected by Select2
            // since we are using custom formatting functions we do not need to
            // alter the remote JSON data, except to indicate that infinite
            // scrolling can be used
            params.page = params.page || 1;

            return {
                results: data.items,
                pagination: {
                    more: (params.page * 30) < data.total_count
                }
            };
        },
        cache: true
    },
    escapeMarkup: function(markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatRepoNik, // omitted for brevity, see the source of this page
    templateSelection: formatRepoSelectionNik // omitted for brevity, see the source of this page
});



$('#formInputKaryawan').bootstrapValidator({
    message: 'This value is not valid',
    feedbackIcons: {
        valid: 'fa fa-check',
        invalid: 'fa fa-close',
        validating: 'fa fa-repeat'
    },
    fields: {
        // nik: {
        //     validators: {
        //         notEmpty: {
        //             message: 'The field can not be empty'
        //         }
        //     }
        // },
        nmlengkap: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                }
            }
        },
        /////callname: {
        /////    validators: {
        /////        notEmpty: {
        /////            message: 'The field can not be empty'
        /////        }
        /////    }
        /////},
        // tgllahir: {
        //     validators: {
        //         notEmpty: {
        //             message: 'The field can not be empty'
        //         }
        //     }
        // },
        jk: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                }
            }
        },
        // kdagama: {
        //     validators: {
        //         notEmpty: {
        //             message: 'The field can not be empty'
        //         }
        //     }
        // },
        // goldarah: {
        //     validators: {
        //         notEmpty: {
        //             message: 'The field can not be empty'
        //         }
        //     }
        // },
        // stswn: {
        //     validators: {
        //         notEmpty: {
        //             message: 'The field can not be empty'
        //         }
        //     }
        // },
        // id_division: {
        //     validators: {
        //         notEmpty: {
        //             message: 'The field can not be empty'
        //         }
        //     }
        // },
        // id_dept: {
        //     validators: {
        //         notEmpty: {
        //             message: 'The field can not be empty'
        //         }
        //     }
        // },
        // id_subdept: {
        //     validators: {
        //         notEmpty: {
        //             message: 'The field can not be empty'
        //         }
        //     }
        // },
        // id_jabatan: {
        //     validators: {
        //         notEmpty: {
        //             message: 'The field can not be empty'
        //         }
        //     }
        // },
        // id_grade: {
        //     validators: {
        //         notEmpty: {
        //             message: 'The field can not be empty'
        //         }
        //     }
        // },
        // costcenter: {
        //     validators: {
        //         notEmpty: {
        //             message: 'The field can not be empty'
        //         }
        //     }
        // },
        // id_gol: {
        //     validators: {
        //         notEmpty: {
        //             message: 'The field can not be empty'
        //         }
        //     }
        // },
        plant: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                }
            }
        },
        statuskepegawaian: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                }
            }
        },
        tglmasukkerja: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                }
            }
        },
        // tglkeluarkerja: {
        //     validators: {
        //         notEmpty: {
        //             message: 'The field can not be empty'
        //         }
        //     }
        // },
        // id_ktp: {
        //     validators: {
        //         notEmpty: {
        //             message: 'The field can not be empty'
        //         }
        //     }
        // },
        // id_provktp: {
        //     validators: {
        //         notEmpty: {
        //             message: 'The field can not be empty'
        //         }
        //     }
        // },
        // id_kotaktp: {
        //     validators: {
        //         notEmpty: {
        //             message: 'The field can not be empty'
        //         }
        //     }
        // },
        // id_kecktp: {
        //     validators: {
        //         notEmpty: {
        //             message: 'The field can not be empty'
        //         }
        //     }
        // },
        // id_desaktp: {
        //     validators: {
        //         notEmpty: {
        //             message: 'The field can not be empty'
        //         }
        //     }
        // },
        // desc_ktp: {
        //     validators: {
        //         notEmpty: {
        //             message: 'The field can not be empty'
        //         }
        //     }
        // },
        // id_provdom: {
        //     validators: {
        //         notEmpty: {
        //             message: 'The field can not be empty'
        //         }
        //     }
        // },
        // id_kotadom: {
        //     validators: {
        //         notEmpty: {
        //             message: 'The field can not be empty'
        //         }
        //     }
        // },
        // id_kecdom: {
        //     validators: {
        //         notEmpty: {
        //             message: 'The field can not be empty'
        //         }
        //     }
        // },
        // id_desadom: {
        //     validators: {
        //         notEmpty: {
        //             message: 'The field can not be empty'
        //         }
        //     }
        // },
        // desc_domisili: {
        //     validators: {
        //         notEmpty: {
        //             message: 'The field can not be empty'
        //         }
        //     }
        // },
        // id_npwp: {
        //     validators: {
        //         notEmpty: {
        //             message: 'The field can not be empty'
        //         }
        //     }
        // },
        // sts_ptkp: {
        //     validators: {
        //         notEmpty: {
        //             message: 'The field can not be empty'
        //         }
        //     }
        // },
        // sts_kawin: {
        //     validators: {
        //         notEmpty: {
        //             message: 'The field can not be empty'
        //         }
        //     }
        // },
        // id_jenjang: {
        //     validators: {
        //         notEmpty: {
        //             message: 'The field can not be empty'
        //         }
        //     }
        // },
        // pendidikan_terakhir: {
        //     validators: {
        //         notEmpty: {
        //             message: 'The field can not be empty'
        //         }
        //     }
        // },
        // jurusan: {
        //     validators: {
        //         notEmpty: {
        //             message: 'The field can not be empty'
        //         }
        //     }
        // },
        // ipk: {
        //     validators: {
        //         notEmpty: {
        //             message: 'The field can not be empty'
        //         }
        //     }
        // },
        // lulus_tahun: {
        //     validators: {
        //         notEmpty: {
        //             message: 'The field can not be empty'
        //         }
        //     }
        // },
        // id_bpjs_kes: {
        //     validators: {
        //         notEmpty: {
        //             message: 'The field can not be empty'
        //         }
        //     }
        // },
        // cab_bpjs_kes: {
        //     validators: {
        //         notEmpty: {
        //             message: 'The field can not be empty'
        //         }
        //     }
        // },
        // id_bpjs_kes: {
        //     validators: {
        //         notEmpty: {
        //             message: 'The field can not be empty'
        //         }
        //     }
        // },
        // cab_bpjs_kes: {
        //     validators: {
        //         notEmpty: {
        //             message: 'The field can not be empty'
        //         }
        //     }
        // },
       /////nohp1: {
       /////    validators: {
       /////        notEmpty: {
       /////            message: 'The field can not be empty'
       /////        }
       /////    }
       /////},
        // nohp2: {
        //     validators: {
        //         notEmpty: {
        //             message: 'The field can not be empty'
        //         }
        //     }
        // },
        /////email_self: {
        /////    validators: {
        /////        notEmpty: {
        /////            message: 'The field can not be empty'
        /////        }
        /////    }
        /////},
        // email_office: {
        //     validators: {
        //         notEmpty: {
        //             message: 'The field can not be empty'
        //         }
        //     }
        // },
        // emergency_phone: {
        //     validators: {
        //         notEmpty: {
        //             message: 'The field can not be empty'
        //         }
        //     }
        // },
        // emergency_relation: {
        //     validators: {
        //         notEmpty: {
        //             message: 'The field can not be empty'
        //         }
        //     }
        // },
        // idabsen: {
        //     validators: {
        //         notEmpty: {
        //             message: 'The field can not be empty'
        //         }
        //     }
        // },
        // id_jenjang: {
        //     validators: {
        //         notEmpty: {
        //             message: 'The field can not be empty'
        //         }
        //     }
        // },
        // id_bank: {
        //     validators: {
        //         notEmpty: {
        //             message: 'The field can not be empty'
        //         }
        //     }
        // },
        // id_rekening: {
        //     validators: {
        //         notEmpty: {
        //             message: 'The field can not be empty'
        //         }
        //     }
        // },
        // nmibukandung: {
        //     validators: {
        //         notEmpty: {
        //             message: 'The field can not be empty'
        //         }
        //     }
        // },
        // nmayahkandung: {
        //     validators: {
        //         notEmpty: {
        //             message: 'The field can not be empty'
        //         }
        //     }
        // }


    },
    excluded: [':disabled']
});

function saveInputKaryawan() {

    Swal.fire({
        title: 'Peringatan..!!!',
        text: 'Data akan disimpan, Apakah anda yakin...?',
        backdrop: true,
        allowOutsideClick: false,
        showConfirmButton: true,
        showDenyButton: true,
        showCancelButton: false,
        confirmButtonText: `Ok`,
        icon: 'question',
        //denyButtonText: `Don't save`,
    }).then((result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {

            var validator = $('#formInputKaryawan').data('bootstrapValidator');
            validator.validate();
            if (validator.isValid()) {
                // ajax adding data to database
                var form = $('#formInputKaryawan');
                var formdata = false;
                if (window.FormData){
                    formdata = new FormData(form[0]);
                }
                $.ajax({
                    url: HOST_URL + 'trans/karyawan/saveDataMasterKaryawan',
                    type: "POST",
                    data: formdata ? formdata : form.serialize(),
                    cache       : false,
                    contentType : false,
                    processData : false,
                    datatype : "JSON",
                    dataFilter: function (data) {
                        var json = jQuery.parseJSON(data);
                        if (json.status) //if success close modal and reload ajax table
                        {
                            Swal.fire({
                                title: 'Berhasil...!!!',
                                text: json.messages,
                                backdrop: true,
                                allowOutsideClick: false,
                                showConfirmButton: true,
                                showDenyButton: false,
                                showCancelButton: false,
                                confirmButtonText: `Ok`,
                                icon: 'success',
                                //denyButtonText: `Don't save`,
                            }).then((result) => {
                                /* Read more about isConfirmed, isDenied below */
                                if (result.isConfirmed) {
                                    //Swal.fire('Saved!', '', 'success')
                                    window.location.replace(HOST_URL + 'trans/karyawan')
                                }
                                // } else if (result.isDenied) {
                                //     Swal.fire('Changes are not saved', '', 'info')
                                // }
                            })
                            // var x = confirm(json.messages);
                            // if (x) return
                            //else return window.location.replace("<?php echo site_url('/trans/skperingatan')?>") + "";
                        } else {
                            //alert(json.messages);
                            Swal.fire({
                                title: 'Galat...!!!',
                                text: json.messages,
                                backdrop: true,
                                allowOutsideClick: false,
                                showConfirmButton: false,
                                showDenyButton: false,
                                showCancelButton: false,
                                icon: 'error',
                                //denyButtonText: `Don't save`,
                            });
                        }
                        $('#btnSave').text('save'); //change button text
                        $('#btnSave').attr('disabled', false); //set button enable


                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        // alert('Gagal Menyimpan / Ubah data / data sudah ada');
                        swal({
                            title: "Galat!!",
                            text: json.messages,
                            type: "error"
                        });
                        $('#btnSave').text('save'); //change button text
                        $('#btnSave').attr('disabled', false); //set button enable

                    }
                });
                //alert("FInish Input");
            }


        } else if (result.isDenied) {
            return false;
        }
    })

}

$(document).ready(function() {

    $('[name="type_office"]').select2();
    $('[name="area_covid"]').select2();
    $('[name="statuscovid"]').select2();
    $('[name="typedomisili"]').select2();
    $('[name="transportasi"]').select2();
    $('[name="penyakitbawaan"]').select2();
    $('[name="resign_filter"]').select2();
    $('#nik').on('change', function(e) {
                    var valnik = $(this).val();
                    var fillData = {
                        'success' : true,
                        'key': '1203jD0j120dkjjKODNOoimdi)D(J)Jmjid0sjd0ijme09wjei0kjisdjfDSojiodksOjO',
                        'message' : '',
                        'body' : {
                            nik : valnik,
                        },
                    };
                    $.ajax({
                        type: "POST",
                        url: HOST_URL + 'trans/candidateos/cekavaiablenik' + '',
                        dataType: 'json',
                        contentType: "application/json",
                        data: JSON.stringify(fillData),
                        success: function (datax){
                            if (datax.status){
                                Swal.fire({
                                    title: 'Berhasil...!!!',
                                    text: datax.messages,
                                    backdrop: true,
                                    allowOutsideClick: false,
                                    showConfirmButton: true,
                                    showDenyButton: false,
                                    showCancelButton: false,
                                    confirmButtonText: `Ok`,
                                    icon: 'success',
                                    //denyButtonText: `Don't save`,
                                });
                                $('#btnSave').attr('disabled', false); //set button enable
                            } else {
                                //console.log('Data Nik Sudah Ada');
                                //alert(json.messages);
                                Swal.fire({
                                    title: 'Galat...!!!',
                                    text: datax.messages,
                                    backdrop: true,
                                    allowOutsideClick: false,
                                    showConfirmButton: false,
                                    showDenyButton: false,
                                    showCancelButton: true,
                                    icon: 'error',
                                    //denyButtonText: `Don't save`,
                                });
                                $('#btnSave').attr('disabled', true); //set button enable
                            }
                        },
                        error: function (jqXHR, textStatus, errorThrown)
                        {
                            Swal.fire({
                                title: 'Galat...!!!',
                                text: 'Data Tidak Bisa Di Proses..!',
                                backdrop: true,
                                allowOutsideClick: false,
                                showConfirmButton: true,
                                showDenyButton: false,
                                showCancelButton: false,
                                confirmButtonText: `Ok`,
                                icon: 'error',
                                //denyButtonText: `Don't save`,
                            });
                            bttn.prop('disabled', false);
                            //console.log("Failed To Loading Data");
                        }
                    });



    });
    $('#checkboxdomisili').change(function() {
        // this will contain a reference to the checkbox
        if (this.checked) {
            $('#id_provdom').prop('disabled', true);
            $('#id_kotadom').prop('disabled', true);
            $('#id_kecdom').prop('disabled', true);
            $('#id_kecdom').prop('disabled', true);
            $('#id_desadom').prop('disabled', true);
            $('#rtdom').prop('readonly', true);
            $('#rwdom').prop('readonly', true);
            $('#id_posdom').prop('readonly', true);
            $('#desc_domisili').prop('readonly', true);
        } else {
            $('#id_provdom').prop('disabled', false);
            $('#id_kotadom').prop('disabled', false);
            $('#id_kecdom').prop('disabled', false);
            $('#id_kecdom').prop('disabled', false);
            $('#id_desadom').prop('disabled', false);
            $('#rtdom').prop('readonly', false);
            $('#rwdom').prop('readonly', false);
            $('#id_posdom').prop('readonly', false);
            $('#desc_domisili').prop('readonly', false);
        }
    });

    $('#tgllahir').datepicker().on('changeDate show', function(e) {
        $('#formInputKaryawan').bootstrapValidator('revalidateField', 'tgllahir');
    });


    $('#tglmasukkerja').datepicker().on('changeDate show', function(e) {
        $('#formInputKaryawan').bootstrapValidator('revalidateField', 'tglmasukkerja');
    });
    // $('#tglkeluarkerja').datepicker().on('changeDate show', function(e) {
    //     $('#formInputKaryawan').bootstrapValidator('revalidateField', 'tglkeluarkerja');
    // });

    if ($('[name="type"]').val() === 'EDIT') {
        documentReadable();
    }
    if ($('[name="type"]').val() === 'DETAIL') {
        documentReadableDetail();
    }

    if ($('[name="type"]').val() === 'EDIT_RESIGN') {
        $('#tglkeluarkerja').datepicker().on('changeDate show', function(e) {
        });
        documentReadableResign();
    }

});