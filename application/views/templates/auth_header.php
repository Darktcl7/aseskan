<!DOCTYPE html>
<html lang="id">

<head>
    <title>Login - Aseskan</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="<?= base_url('assets/img/aseskan-icon.png'); ?>" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <style>
        body,
        html {
            height: 100%;
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
        }

        .main-container {
            display: flex;
            width: 100%;
            height: 100vh;
        }

        .left-panel {
            flex-basis: 55%;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 60px;
            /* Gradient HANYA diterapkan di sini */
            background-image: linear-gradient(310deg, rgba(137, 101, 224, 0.6), rgba(94, 114, 228, 0.6)), url('<?= base_url('assets/img/profile-layout-header.jpg'); ?>');
            background-size: cover;
            background-position: center;
        }

        .left-panel h1 {
            font-weight: bold;
            font-size: 2.8rem;
            margin-bottom: 1rem;
        }

        .left-panel p {
            font-size: 1.1rem;
            color: #d0d0d0;
            max-width: 500px;
        }

        .right-panel {
            flex-basis: 45%;
            display: flex;
            align-items: center;
            justify-content: center;
            /* Panel ini TIDAK menggunakan gradient, hanya warna solid */
            background-color: #f8f9fa;
            overflow-y: auto;
        }

        .login-wrapper {
            width: 100%;
            max-width: 400px;
            padding: 20px;
        }

        .app-logo {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .app-logo img {
            width: 100px;
            height: auto;
        }

        .app-logo h2 {
            font-weight: 600;
            font-size: 1.7rem;
            margin-top: 0.5rem;
            color: #333;
        }

        .form-control-lg {
            padding: 0.8rem 1rem;
        }

        .btn-custom {
            width: 100%;
            padding: 0.75rem;
            font-weight: bold;
            background-color: #9370DB;
            border: none;
            color: white;
            transition: background-color 0.3s;
        }

        .btn-custom:hover {
            background-color: #9370DB;
        }

        .text-center a {
            color: #007bff;
            text-decoration: none;
        }

        .text-center a:hover {
            text-decoration: underline;
        }

        @media (max-width: 991.98px) {
            .left-panel {
                display: none;
            }

            .right-panel {
                flex-basis: 100%;
            }
        }
    </style>
</head>

<body>