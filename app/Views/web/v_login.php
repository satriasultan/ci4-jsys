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

        /* UNIVERSAL INPUT STYLE (Input, Select2, Flatpickr) */
        .form-control,
        .flatpickr-input,
        .select2-container .select2-selection--single {
            height: 48px !important;
            background: rgba(255,255,255,0.18) !important;
            border: 1px solid rgba(255,255,255,0.3) !important;
            color: #fff !important;
            border-radius: 12px !important;
            padding-left: 14px !important;
            backdrop-filter: blur(10px);
        }

        .form-control::placeholder,
        .flatpickr-input::placeholder,
        .select2-selection__placeholder {
            color: #eaeaea !important;
        }

        /* SELECT2 MAIN STYLE */
        .select2-container .select2-selection--single {
            display: flex !important;
            align-items: center !important;
        }

        .select2-container .select2-selection__rendered {
            color: #fff !important;
            line-height: 48px !important;
            padding-left: 2px !important;
        }

        .select2-container .select2-selection__arrow b {
            border-color: #fff transparent transparent transparent !important;
        }

        /* SELECT2 DROPDOWN */
        .select2-dropdown {
            background: rgba(0,0,0,0.45) !important;
            border: 1px solid rgba(255,255,255,0.25) !important;
            backdrop-filter: blur(12px);
            border-radius: 12px !important;
        }

        .select2-results__option {
            color: #fff !important;
            padding: 10px 14px;
        }

        .select2-results__option--highlighted {
            background: rgba(255,255,255,0.2) !important;
        }

        .select2-results__option[aria-selected="true"] {
            background: rgba(0,196,255,0.35) !important;
        }

        /* Hilangkan search di dropdown */
        .select2-search--dropdown {
            display: none !important;
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
    </style>

</head>

<body>

<div class="login-wrapper text-center">

    <img src="<?php echo base_url('assets/img/logo-depan/jts-icon.png'); ?>" alt="Logo">

    <h3><b>JSYS ACCOUNTING</b></h3>
    <p style="margin-top: -5px; opacity: .8;">Version 0.1 Development Progress Tes</p>

    <form id="loginform" action="<?php echo base_url('web/login/proses');?>" method="post">

        <input class="form-control mt-3" type="text" required name="username" placeholder="Username">

        <input class="form-control mt-3" type="password" required name="password" placeholder="Password">

        <div class="form-group mt-3">
            <input id="datePick" class="form-control" type="text" name="date" placeholder="Select Date" required>
        </div>

        <div class="form-group mt-3">
            <select class="form-control select2" name="branch" required>
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
    flatpickr("#datePick", {
        dateFormat: "Y-m-d",
        allowInput: false,
        disableMobile: true,
        defaultDate: "today"
    });

    $('.select2').select2({
        placeholder: "Pilih Branch",
        allowClear: false,
        minimumResultsForSearch: -1
    }).val('JTS').trigger('change');
</script>

</body>
</html>
