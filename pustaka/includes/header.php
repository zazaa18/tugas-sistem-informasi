<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Pustaka - Sistem Informasi Perpustakaan</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">

    <style>
        /* Tampilan navbar */
        .navbar-modern {
            position: relative;
            z-index: 1050;
            background: linear-gradient(135deg, #1e3a8a, #2563eb);
            box-shadow: 0 4px 18px rgba(30, 58, 138, 0.18);
            padding: 14px 0;
        }

        .navbar-modern .navbar-brand {
            color: #fff;
            font-size: 1.35rem;
            font-weight: 700;
            letter-spacing: .3px;
        }

        .navbar-modern .navbar-brand:hover {
            color: #dbeafe;
        }

        .navbar-modern .navbar-collapse {
            position: relative;
            z-index: 1051;
        }

        .navbar-modern .navbar-nav,
        .navbar-modern .nav-item,
        .navbar-modern .nav-link {
            position: relative;
            z-index: 1052;
            pointer-events: auto;
        }

        .navbar-modern .nav-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: rgba(255, 255, 255, .85);
            padding: 10px 14px;
            margin: 0 3px;
            border-radius: 10px;
            font-weight: 500;
            transition: all .25s ease;
            cursor: pointer;
        }

        .navbar-modern .nav-link:hover,
        .navbar-modern .nav-link.active {
            color: #fff;
            background: rgba(255, 255, 255, .16);
        }

        /* Ikon profil admin */
        .navbar-modern .btn-profile {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 43px;
            height: 43px;
            padding: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, .12);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, .3);
            font-size: 1.4rem;
            text-decoration: none;
            transition: all .25s ease;
        }

        .navbar-modern .btn-profile:hover {
            background: #fff;
            color: #1e40af;
            transform: translateY(-2px);
        }

        .navbar-modern .navbar-toggler {
            border-color: rgba(255, 255, 255, .4);
        }

        .navbar-modern .navbar-toggler:focus {
            box-shadow: 0 0 0 .2rem rgba(255, 255, 255, .2);
        }

        @media (max-width: 991.98px) {
            .navbar-modern .navbar-collapse {
                padding-top: 12px;
            }

            .navbar-modern .nav-link {
                display: flex;
                width: 100%;
                margin: 3px 0;
            }

            .navbar-modern .btn-profile {
                width: 44px;
                height: 44px;
                margin-top: 10px;
            }
        }
    </style>
</head>

<body class="bg-light d-flex flex-column min-vh-100">

    <!-- Navbar Utama -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-modern">
        <div class="container">

            <a class="navbar-brand" href="index.php">
                <i class="fas fa-book-open me-2"></i>
                Pustaka
            </a>

            <button
                class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarNav"
                aria-controls="navbarNav"
                aria-expanded="false"
                aria-label="Buka navigasi"
            >
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">

                <ul class="navbar-nav ms-auto align-items-lg-center">


                    <li class="nav-item">
                        <a class="nav-link" href="cek_status.php">
                            <i class="fas fa-clipboard-list"></i>
                            Cek Status Peminjaman
                        </a>
                    </li>

                    <li class="nav-item">
                        <a
                            href="admin/login.php"
                            class="btn-profile"
                            title="Login Admin"
                            aria-label="Login Admin"
                        >
                            <i class="fas fa-user-circle"></i>
                        </a>
                    </li>

                </ul>

            </div>
        </div>
    </nav>

    <main class="flex-grow-1">