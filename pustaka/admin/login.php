<?php
session_start();
require_once '../config/database.php';

// Jika sudah login, redirect langsung ke dashboard admin
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: dashboard.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!empty($username) && !empty($password)) {
        // Query cari admin berdasarkan username
        $stmt = $pdo->prepare("SELECT * FROM admin WHERE username = ?");
        $stmt->execute([$username]);
        $admin = $stmt->fetch();

        if ($admin) {
            $dbPassword = $admin['password'];
            $isLoggedIn = false;

            // Validasi multi-format password (Bcrypt, MD5, atau Plain Text)
            if (password_verify($password, $dbPassword)) {
                // 1. Enkripsi Bcrypt (password_hash)
                $isLoggedIn = true;
            } elseif (md5($password) === $dbPassword) {
                // 2. Enkripsi MD5
                $isLoggedIn = true;
            } elseif ($password === $dbPassword) {
                // 3. Plain Text
                $isLoggedIn = true;
            }

            if ($isLoggedIn) {
                // Set Session Login Admin
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id']        = $admin['id_admin'];
                $_SESSION['admin_name']      = $admin['nama_admin'];
                $_SESSION['admin_username']  = $admin['username'];
                
                header("Location: dashboard.php");
                exit;
            } else {
                $error = "Password yang Anda masukkan salah!";
            }
        } else {
            $error = "Username tidak ditemukan!";
        }
    } else {
        $error = "Harap isi username dan password!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Pustaka</title>
    
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- FontAwesome Icons CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            background-color: #f4f6f9;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .login-card {
            width: 100%;
            max-width: 420px;
            border-radius: 20px;
            border: none;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08);
            background: #ffffff;
            padding: 2.5rem 2rem;
        }
        .brand-icon {
            width: 70px;
            height: 70px;
            background-color: #0d6efd;
            color: #ffffff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.25rem auto;
            box-shadow: 0 8px 18px rgba(13, 110, 253, 0.25);
        }
        .input-group-text-custom {
            background-color: #f8f9fa;
            border-right: none;
            color: #6c757d;
        }
        .btn-toggle-password {
            background-color: #ffffff;
            color: #6c757d;
            border-left: none;
            cursor: pointer;
            transition: color 0.2s;
        }
        .btn-toggle-password:hover {
            color: #0d6efd;
            background-color: #ffffff;
        }
        .form-control:focus {
            box-shadow: none;
            border-color: #0d6efd;
        }
    </style>
</head>
<body>

    <div class="login-card">
        
        <!-- Header & Icon Logo -->
        <div class="text-center mb-4">
            <div class="brand-icon">
                <i class="fas fa-book-reader fa-2x"></i>
            </div>
            <h3 class="fw-bold text-dark mb-1">Pustaka Admin</h3>
            <p class="text-muted small mb-0">Silakan masuk untuk mengelola perpustakaan</p>
        </div>

        <!-- Alert Notifikasi Error -->
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show rounded-3 py-2 px-3 mb-3 small" role="alert">
                <i class="fas fa-exclamation-circle me-1"></i> <?= htmlspecialchars($error); ?>
                <button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Form Login -->
        <form action="" method="POST">
            
            <!-- Input Username -->
            <div class="mb-3">
                <label for="username" class="form-label fw-semibold text-dark small mb-1">Username</label>
                <div class="input-group">
                    <span class="input-group-text input-group-text-custom"><i class="fas fa-user"></i></span>
                    <input type="text" name="username" id="username" class="form-control ps-2" placeholder="Masukkan username" required autofocus>
                </div>
            </div>

            <!-- Input Password + Toggle Lihat Password -->
            <div class="mb-4">
                <label for="password" class="form-label fw-semibold text-dark small mb-1">Password</label>
                <div class="input-group">
                    <span class="input-group-text input-group-text-custom"><i class="fas fa-lock"></i></span>
                    <input type="password" name="password" id="password" class="form-control ps-2" placeholder="Masukkan password" required style="border-right: none;">
                    <button class="btn btn-outline-secondary btn-toggle-password border" type="button" id="togglePassword">
                        <i class="fas fa-eye" id="iconEye"></i>
                    </button>
                </div>
            </div>

            <!-- Tombol Masuk -->
            <button type="submit" class="btn btn-primary w-100 py-2.5 rounded-3 fw-bold shadow-sm mb-3">
                <i class="fas fa-sign-in-alt me-2"></i> Masuk
            </button>

            <!-- Link Kembali ke Katalog -->
            <div class="text-center">
                <a href="../index.php" class="text-decoration-none text-muted small">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Katalog Utama
                </a>
            </div>

        </form>

    </div>

    <!-- JS Bootstrap & Interaktivitas Toggle Mata Password -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const passwordInput  = document.querySelector('#password');
        const iconEye        = document.querySelector('#iconEye');

        togglePassword.addEventListener('click', function () {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            iconEye.classList.toggle('fa-eye');
            iconEye.classList.toggle('fa-eye-slash');
        });
    </script>
</body>
</html>