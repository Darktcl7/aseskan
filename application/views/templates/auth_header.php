<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Aseskan-2025</title>

    <link rel="icon" type="image/png" href="<?= base_url('assets/'); ?>/img/aseskan-icon.png">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="<?= base_url('Login_v16/'); ?>vendor/bootstrap/css/bootstrap.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="<?= base_url('Login_v16/'); ?>fonts/font-awesome-4.7.0/css/font-awesome.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="<?= base_url('Login_v16/'); ?>fonts/Linearicons-Free-v1.0.0/icon-font.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="<?= base_url('Login_v16/'); ?>vendor/animate/animate.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="<?= base_url('Login_v16/'); ?>vendor/css-hamburgers/hamburgers.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="<?= base_url('Login_v16/'); ?>vendor/animsition/css/animsition.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="<?= base_url('Login_v16/'); ?>vendor/select2/select2.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="<?= base_url('Login_v16/'); ?>vendor/daterangepicker/daterangepicker.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="<?= base_url('Login_v16/'); ?>css/util.css">
    <link rel="stylesheet" type="text/css" href="<?= base_url('Login_v16/'); ?>css/main.css">

    <!-- [BARU] CSS untuk Tombol Kustom -->
    <style>
        .button-29 {
            align-items: center;
            appearance: none;
            border: 0;
            border-radius: 8px;
            box-shadow: rgba(45, 35, 66, .4) 0 2px 4px, rgba(45, 35, 66, .3) 0 7px 13px -3px, rgba(58, 65, 111, .5) 0 -3px 0 inset;
            box-sizing: border-box;
            color: #fff;
            cursor: pointer;
            display: inline-flex;
            /* Ukuran dibuat kotak dan kecil */
            height: 40px;
            width: 40px;
            justify-content: center;
            padding: 0;
            /* Padding tidak diperlukan karena ukuran tetap */
            position: relative;
            transition: box-shadow .15s, transform .15s;
            user-select: none;
            -webkit-user-select: none;
            touch-action: manipulation;
            will-change: box-shadow, transform;
        }

        .button-29:active {
            box-shadow: #3c4fe0 0 3px 7px inset;
            transform: translateY(2px);
        }

        /* Warna untuk Tombol LIKE */
        .button-29.like {
            background-image: radial-gradient(100% 100% at 100% 0, #5adaff 0, #5468ff 100%);
        }

        .button-29.like:focus {
            box-shadow: #3c4fe0 0 0 0 1.5px inset, rgba(45, 35, 66, .4) 0 2px 4px, rgba(45, 35, 66, .3) 0 7px 13px -3px, #3c4fe0 0 -3px 0 inset;
        }

        .button-29.like:hover {
            box-shadow: rgba(45, 35, 66, .4) 0 4px 8px, rgba(45, 35, 66, .3) 0 7px 13px -3px, #3c4fe0 0 -3px 0 inset;
            transform: translateY(-2px);
        }


        /* Warna untuk Tombol SHARE */
        .button-29.share {
            background-image: radial-gradient(100% 100% at 100% 0, #a0aec0 0, #718096 100%);
        }

        .button-29.share:focus {
            box-shadow: #6b7280 0 0 0 1.5px inset, rgba(45, 35, 66, .4) 0 2px 4px, rgba(45, 35, 66, .3) 0 7px 13px -3px, #6b7280 0 -3px 0 inset;
        }

        .button-29.share:hover {
            box-shadow: rgba(45, 35, 66, .4) 0 4px 8px, rgba(45, 35, 66, .3) 0 7px 13px -3px, #6b7280 0 -3px 0 inset;
            transform: translateY(-2px);
        }
    </style>


</head>

<body>