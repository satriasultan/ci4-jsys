<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Jsys</title>

    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo base_url('assets/img/logo-depan/jts.ico') ?>">

    <link href="<?php echo base_url('assets/ea/dist/css/style.min.css'); ?>" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg,#0052A2,#00A6E0);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-wrapper {
            width: 380px;
            padding: 40px;
            border-radius: 16px;
            background: rgba(255,255,255,0.12);
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            backdrop-filter: blur(12px);
            color: #fff;
            animation: fadeIn 0.7s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .login-wrapper img {
            width: 160px;
            margin-bottom: 10px;
        }

        /* ================================
    SELECT2 CLEAN BLACK TEXT STYLE
 ================================ */

        /* Selected text */
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #222 !important;
            font-weight: 500;
        }

        /* Placeholder */
        .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: #444 !important;
        }

        /* Arrow */
        .select2-container--default .select2-selection--single .select2-selection__arrow b {
            border-color: #222 transparent transparent transparent !important;
        }

        /* Focus state */
        .select2-container--default.select2-container--focus .select2-selection--single {
            color: #222 !important;
        }

        /* Dropdown */
        .select2-dropdown {
            background: rgba(255,255,255,0.95) !important;
            color: #222 !important;
            border-radius: 12px !important;
            border: 1px solid rgba(0,0,0,0.1) !important;
        }

        /* Dropdown option */
        .select2-results__option {
            color: #222 !important;
            padding: 10px 14px;
        }

        /* Hover option */
        .select2-results__option--highlighted {
            background: #00C4FF !important;
            color: #fff !important;
        }

        /* Selected option */
        .select2-results__option[aria-selected="true"] {
            background: rgba(0,196,255,0.2) !important;
        }

        .btn-login {
            margin-top: 10px;
            width: 100%;
            padding: 12px;
            border-radius: 10px;
            background: #00C4FF;
            border: none;
            color: #fff;
            font-size: 16px;
            font-weight: 600;
            letter-spacing: 1px;
            transition: 0.2s;
        }

        .btn-login:hover {
            background: #14d4ff;
            transform: scale(1.03);
        }

        .recaptcha-box {
            margin: 10px 0;
            text-align: center;
        }

        .remember {
            margin-top: 10px;
        }

        /* FLATPICKR DROPDOWN */
        .flatpickr-calendar {
            background: rgba(0,0,0,0.45) !important;
            border-radius: 12px !important;
            border: 1px solid rgba(255,255,255,0.25) !important;
            backdrop-filter: blur(12px);
            color: #fff !important;
        }

        .flatpickr-day {
            color: #fff !important;
        }

        .flatpickr-day:hover {
            background: rgba(255,255,255,0.2) !important;
        }

        .flatpickr-day.selected {
            background: #00C4FF !important;
            border-color: #00C4FF !important;
        }

        /* DATE WRAPPER */
        .date-wrapper {
            position: relative;
        }

        .date-wrapper input {
            padding-right: 45px !important;
        }

        /* ICON */
        .calendar-icon {
            position: absolute;
            top: 50%;
            right: 15px;
            transform: translateY(-50%);
            cursor: pointer;
            opacity: 0.8;
            transition: 0.2s;
        }

        .calendar-icon:hover {
            opacity: 1;
            transform: translateY(-50%) scale(1.1);
        }

        /* Posisi Container Wave */
        .wave-container {
            position: fixed;
            width: 100%;
            height: 100vh;
            bottom: 0;
            left: 0;
            z-index: -1; /* Supaya berada di belakang form */
            display: flex;
            align-items: flex-end;
        }

        .waves {
            position: relative;
            width: 100%;
            height: 15vh;
            min-height: 100px;
            max-height: 150px;
        }

        /* Animasi Pergerakan Wave */
        .parallax > use {
            animation: move-forever 25s cubic-bezier(.55,.5,.45,.5) infinite;
        }
        .parallax > use:nth-child(1) {
            animation-delay: -2s;
            animation-duration: 7s;
        }
        .parallax > use:nth-child(2) {
            animation-delay: -3s;
            animation-duration: 10s;
        }
        .parallax > use:nth-child(3) {
            animation-delay: -4s;
            animation-duration: 13s;
        }
        .parallax > use:nth-child(4) {
            animation-delay: -5s;
            animation-duration: 20s;
        }

        @keyframes move-forever {
            0% { transform: translate3d(-90px,0,0); }
            100% { transform: translate3d(85px,0,0); }
        }

        /* Supaya Body tetap memiliki gradasi gelap ke terang */
        body {
            margin: 0;
            background: linear-gradient(135deg, #001f3f 0%, #0052A2 50%, #00A6E0 100%);
            overflow: hidden; /* Mencegah scroll karena wave */
        }

        .login-spinner {
            display: inline-block;
            width: 14px;
            height: 14px;
            margin-right: 6px;
            border: 2px solid rgba(255,255,255,0.4);
            border-top-color: #fff;
            border-radius: 50%;
            animation: loginSpin 0.7s linear infinite;
            vertical-align: -2px;
        }

        @keyframes loginSpin {
            to {
                transform: rotate(360deg);
            }
        }

        .btn-login:disabled {
            cursor: not-allowed !important;
        }
    </style>

</head>

<body>
<div class="wave-container">
    <svg class="waves" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
    viewBox="0 24 150 28" preserveAspectRatio="none" shape-rendering="auto">
        <defs>
            <path id="gentle-wave" d="M-160 44c30 0 58-18 88-18s 58 18 88 18 58-18 88-18 58 18 88 18 v44h-352z" />
        </defs>
        <g class="parallax">
            <use xlink:href="#gentle-wave" x="48" y="0" fill="rgba(255,255,255,0.3)" />
            <use xlink:href="#gentle-wave" x="48" y="3" fill="rgba(255,255,255,0.2)" />
            <use xlink:href="#gentle-wave" x="48" y="5" fill="rgba(255,255,255,0.1)" />
            <use xlink:href="#gentle-wave" x="48" y="7" fill="rgba(255,255,255,0.05)" />
        </g>
    </svg>
</div>
<div class="login-wrapper text-center">

    <img src="<?php echo base_url('assets/img/logo-depan/jts-icon.png'); ?>" alt="Logo">

    <h3><b>JSYS ACCOUNTING</b></h3>
    <p style="margin-top: -5px; opacity: .8;">Version 0.1 Development</p>

    <form id="loginform" action="<?php echo base_url('web/login/proses');?>" method="post">

        <input class="form-control mt-3"
               type="text"
               required
               name="username"
               placeholder="Username">

        <input class="form-control mt-3" type="password" required name="password" placeholder="Password">

        <div class="form-group mt-3">
            <div class="date-wrapper">
                <input id="datePick"
                       class="form-control"
                       type="text"
                       name="logindate"
                       placeholder="Select Date"
                       required>

                <span class="calendar-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="white" viewBox="0 0 24 24">
                <path d="M7 2v2H5a2 2 0 0 0-2 2v2h18V6a2 2 0 0 0-2-2h-2V2h-2v2H9V2H7zm14 8H3v10a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V10z"/>
            </svg>
        </span>
            </div>
        </div>

        <div class="form-group mt-3">

            <select class="form-control select2" name="cabang" id="cabang" required>
                <option value="JTS">PT JATIM TAMAN STEEL.MFG</option>
                <option value="P1">PLANT 1</option>
                <option value="P2">PLANT 2</option>
            </select>
        </div>

        <div class="recaptcha-box">
            <div class="g-recaptcha" data-sitekey="<?= $recaptha_sitekey ?>"></div>
        </div>

        <span style="color:#ffaaaa;"><?php echo $session->getFlashdata('message');?></span>

        <div class="remember text-start">
            <input type="checkbox" id="rememberMe">
            <label for="rememberMe"> Remember me</label>
        </div>

        <button class="btn-login" type="submit">LOGIN</button>

    </form>

</div>

<script src="https://www.google.com/recaptcha/api.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function () {

        // =========================================
        // FLATPICKR
        // =========================================

        const fp = flatpickr("#datePick", {
            dateFormat: "d-m-Y",
            defaultDate: new Date(),
            allowInput: false,
            disableMobile: true,
            clickOpens: true
        });

        $('.calendar-icon').on('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            fp.open();
        });


        // =========================================
        // LOGIN
        // =========================================

        $('#loginform').on('submit', function (e) {

            e.preventDefault();

            const form = $(this);
            const button = form.find('.btn-login');

            // Cegah double click
            if (button.prop('disabled')) {
                return;
            }

            // Validasi
            if (!this.checkValidity()) {
                this.reportValidity();
                return;
            }

            // =================================================
            // AMBIL DATA SEBELUM FIELD DI-DISABLE
            // =================================================

            const formData = form.serialize();

            // =================================================
            // LOCK FORM
            // =================================================

            button
                .prop('disabled', true)
                .html('<span class="login-spinner"></span> PROCESSING...')
                .css({
                    'cursor': 'not-allowed',
                    'opacity': '0.7'
                });

            // Disable semua field
            form.find('input, select, button').prop('disabled', true);

            // =================================================
            // AJAX
            // =================================================

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: formData,
                dataType: 'json',

                success: function (response) {

                    console.log(response);

                    if (response.status === true) {

                        button
                            .prop('disabled', true)
                            .html('✓ SUCCESS');

                        window.location.href = response.redirect;

                    } else {

                        unlockLoginForm();

                        showLoginError(
                            response.messages ||
                            'Username atau Password Salah'
                        );

                        if (typeof grecaptcha !== 'undefined') {
                            grecaptcha.reset();
                        }
                    }
                },

                error: function (xhr, status, error) {

                    console.error(xhr.responseText);

                    unlockLoginForm();

                    showLoginError(
                        'Terjadi kesalahan saat proses login. Silakan coba lagi.'
                    );

                    if (typeof grecaptcha !== 'undefined') {
                        grecaptcha.reset();
                    }
                }
            });

        });


        // =========================================
        // UNLOCK FORM
        // =========================================

        function unlockLoginForm() {

            const form = $('#loginform');
            const button = form.find('.btn-login');

            form.find('input, select, button')
                .prop('disabled', false);

            button
                .prop('disabled', false)
                .html('LOGIN')
                .css({
                    'cursor': 'pointer',
                    'opacity': '1'
                });
        }


        // =========================================
        // ERROR
        // =========================================

        function showLoginError(message) {

            let alert = $('#login-error');

            if (alert.length === 0) {

                alert = $('<div id="login-error"></div>');

                alert.css({
                    'color': '#ffaaaa',
                    'margin-top': '10px',
                    'font-weight': '500'
                });

                $('.btn-login').before(alert);
            }

            alert
                .stop(true, true)
                .hide()
                .text(message)
                .fadeIn(200);

            setTimeout(function () {
                alert.fadeOut(300);
            }, 5000);
        }

    });
</script>
<script>



    var defaultInitialBranch = '';
    $("#cabang").select2({
        placeholder: "Type/ Choose your Branch",
        allowClear: true,
        width: '100%',
        //minimumInputLength: 2, // only start searching when the user has input 3 or more characters
        maximumSelectionLength: 1,
        multiple: false,
        ajax: {
            url: HOST_URL + 'api/globalmodule/list_branchjob',
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
                    _paramglobal_: defaultInitialBranch,
                    _parameterx_: defaultInitialBranch,
                    term: params.term,
                };
            },
            processResults: function (data, params) {
                var searchTerm = $("#cabang").data("select2").$dropdown.find("input").val();
                if (data.items.length === 1 && data.items[0].text === searchTerm) {
                    var option = new Option(data.items[0].nmbranch, data.items[0].idbranch, true, true);
                    $('#cabang').append(option).trigger('change').select2("close");
                    // manually trigger the `select2:select` event
                    $('#cabang').trigger({
                        type: 'select2:select',
                        params: {
                            data: data
                        }
                    });
                }
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
        templateResult: formatBranch, // omitted for brevity, see the source of this page
        templateSelection: formatBranchSelection // omitted for brevity, see the source of this page
    }).on("change", function () {
        console.log('Selecting =>' + $(this).val());
        //var table = $('#tsearchitem');
        //table.DataTable().ajax.reload(); //reload datatable ajax
        ///table.append().search( $(this).val() ).draw();
        //$('#filter').modal('hide');
    });
    /* Format Group */
    function formatBranch(repo) {
        if (repo.loading) return repo.text;
        var markup ="<div class='select2-result-repository__description'>" +"   <i class='fa fa-circle-o'></i>   "+ repo.nmbranch +"</div>";
        return markup;
    }
    function formatBranchSelection(repo) {
        return repo.nmbranch || repo.text;
    }

</script>

</body>
</html>
