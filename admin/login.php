<?php
/**
 * File: admin/login.php
 * Deskripsi: Halaman login administrator untuk Yayasan Pondok Pesantren Raudlatul Muta'allimin.
 * Mengamankan akses login menggunakan session PHP dan password_verify() berbasis Bcrypt.
 */

// Memulai session PHP
session_start();

// Memasukkan file koneksi (menggunakan jalur relatif keluar satu folder)
include '../koneksi.php';

// Jika admin sudah dalam keadaan login, langsung arahkan ke halaman dashboard admin
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: index.php"); // atau dashboard.php sesuai struktur Anda
    exit;
}

$status_login = null;

// Memproses form ketika tombol login ditekan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_login'])) {
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $password = trim($_POST['password']);

    if (!empty($username) && !empty($password)) {
        // Query mencari user berdasarkan username
        $query_user = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username' LIMIT 1");
        
        if ($query_user && mysqli_num_rows($query_user) > 0) {
            $user_data = mysqli_fetch_assoc($query_user);
            
            // Verifikasi password hash Bcrypt dari database
            if (password_verify($password, $user_data['password'])) {
                // Menyimpan data user ke dalam session login
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['id_user']         = $user_data['id_user'];
                $_SESSION['username']        = $user_data['username'];
                $_SESSION['nama_lengkap']    = $user_data['nama_lengkap'];
                $_SESSION['role']            = $user_data['role'];
                
                $status_login = 'success';
            } else {
                $status_login = 'wrong_password';
            }
        } else {
            $status_login = 'username_not_found';
        }
    } else {
        $status_login = 'empty_fields';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Administrator - Raudlatul Muta'allimin</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- FontAwesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Google Fonts Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-bootstrap-4/bootstrap-4.css" rel="stylesheet">

    <!-- Styling Kustom UI Modern Minimalis -->
    <style>
        :root {
            --kemenag-green-primary: #00a86b;
            --kemenag-green-dark: #007d4f;
            --kemenag-green-light: #f0faf5;
            --light-neutral: #f8fafc;
            --dark-neutral: #0f172a;
            --text-muted-custom: #475569;
            --transition-smooth: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--light-neutral);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            overflow-x: hidden;
            position: relative;
        }

        /* Dekorasi Geometris Latar Belakang */
        .bg-deco-1 {
            position: absolute;
            top: -10%;
            right: -10%;
            width: 40%;
            height: 50%;
            background: radial-gradient(circle, var(--kemenag-green-light) 0%, rgba(255,255,255,0) 70%);
            z-index: 1;
            pointer-events: none;
        }

        .bg-deco-2 {
            position: absolute;
            bottom: -10%;
            left: -10%;
            width: 40%;
            height: 50%;
            background: radial-gradient(circle, var(--kemenag-green-light) 0%, rgba(255,255,255,0) 70%);
            z-index: 1;
            pointer-events: none;
        }

        .login-container {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 440px;
        }

        .login-card {
            background-color: #ffffff;
            border: none;
            border-radius: 28px;
            box-shadow: 0 20px 50px -15px rgba(15, 23, 42, 0.06);
            padding: 40px 35px;
            transition: var(--transition-smooth);
        }

        .brand-logo-wrapper {
            width: 65px;
            height: 65px;
            background-color: var(--kemenag-green-light);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--kemenag-green-primary);
            font-size: 1.8rem;
            margin: 0 auto 20px auto;
            border: 1px solid rgba(0, 168, 107, 0.1);
        }

        .brand-title {
            font-weight: 800;
            color: var(--dark-neutral);
            font-size: 1.35rem;
            letter-spacing: -0.5px;
            line-height: 1.2;
            margin-bottom: 5px;
        }

        .brand-subtitle {
            font-size: 0.8rem;
            color: var(--text-muted-custom);
            font-weight: 500;
            margin-bottom: 30px;
        }

        .form-label-custom {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--dark-neutral);
            margin-bottom: 8px;
        }

        .input-group-custom {
            position: relative;
        }

        .form-control-custom {
            background-color: var(--light-neutral) !important;
            border: 1.5px solid transparent !important;
            border-radius: 14px !important;
            padding: 12px 16px 12px 45px !important;
            font-size: 0.9rem !important;
            color: var(--dark-neutral) !important;
            transition: var(--transition-smooth) !important;
        }

        .form-control-custom:focus {
            border-color: var(--kemenag-green-primary) !important;
            background-color: #ffffff !important;
            box-shadow: 0 0 0 4px rgba(0, 168, 107, 0.15) !important;
        }

        .input-icon {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted-custom);
            font-size: 0.95rem;
            z-index: 5;
            transition: var(--transition-smooth);
        }

        .form-control-custom:focus + .input-icon {
            color: var(--kemenag-green-primary);
        }

        /* Toggle Password visibility */
        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted-custom);
            cursor: pointer;
            z-index: 5;
            border: none;
            background: none;
            padding: 0;
        }

        .password-toggle:hover {
            color: var(--kemenag-green-primary);
        }

        .btn-login {
            background-color: var(--kemenag-green-primary) !important;
            color: #ffffff !important;
            border: none !important;
            border-radius: 50px !important;
            padding: 14px 20px !important;
            font-weight: 600 !important;
            font-size: 0.95rem !important;
            width: 100%;
            transition: var(--transition-smooth) !important;
            box-shadow: 0 10px 25px -5px rgba(0, 168, 107, 0.3) !important;
        }

        .btn-login:hover {
            background-color: var(--kemenag-green-dark) !important;
            transform: translateY(-2px);
            box-shadow: 0 15px 30px -5px rgba(0, 168, 107, 0.4) !important;
        }

        .btn-back {
            color: var(--text-muted-custom);
            text-decoration: none;
            font-size: 0.8rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: var(--transition-smooth);
            margin-top: 25px;
        }

        .btn-back:hover {
            color: var(--kemenag-green-primary);
        }
    </style>
</head>
<body>

    <!-- Latar Belakang Geometris -->
    <div class="bg-deco-1"></div>
    <div class="bg-deco-2"></div>

    <div class="login-container">
        <div class="login-card text-center">
            
            <!-- Logo Brand -->
            <div class="brand-logo-wrapper">
                <i class="fas fa-mosque"></i>
            </div>
            
            <!-- Identitas Lembaga -->
            <h3 class="brand-title">ADMIN PANEL</h3>
            <p class="brand-subtitle">PP Raudlatul Muta'allimin Kasui</p>

            <!-- Form Input Login -->
            <form action="login.php" method="POST" autocomplete="off">
                
                <!-- Input Username -->
                <div class="mb-3 text-start">
                    <label for="username" class="form-label-custom">Nama Pengguna (Username)</label>
                    <div class="input-group-custom">
                        <input type="text" class="form-control form-control-custom w-100" id="username" name="username" placeholder="Masukkan username Anda" required>
                        <i class="fas fa-user input-icon"></i>
                    </div>
                </div>

                <!-- Input Password -->
                <div class="mb-4 text-start">
                    <label for="password" class="form-label-custom">Kata Sandi (Password)</label>
                    <div class="input-group-custom">
                        <input type="password" class="form-control form-control-custom w-100" id="password" name="password" placeholder="Masukkan password Anda" required>
                        <i class="fas fa-lock input-icon"></i>
                        <button type="button" class="password-toggle" onclick="togglePasswordVisibility()">
                            <i class="fas fa-eye" id="passwordEyeIcon"></i>
                        </button>
                    </div>
                </div>

                <!-- Tombol Submit -->
                <button type="submit" name="submit_login" class="btn-login">
                    Masuk Sekarang <i class="fas fa-sign-in-alt ms-2"></i>
                </button>

            </form>

            <!-- Navigasi Kembali ke Landing Page -->
            <a href="../index.php" class="btn-back">
                <i class="fas fa-arrow-left"></i> Kembali ke Beranda Utama
            </a>

        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Skrip Tampilan Password -->
    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('passwordEyeIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        }
    </script>

    <!-- Integrasi Alur Notifikasi Login SweetAlert2 -->
    <?php if (!is_null($status_login)): ?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            <?php if ($status_login === 'success'): ?>
            Swal.fire({
                icon: 'success',
                title: 'Login Berhasil!',
                text: 'Selamat datang kembali, <?php echo htmlspecialchars($_SESSION['nama_lengkap']); ?>. Mengarahkan Anda ke Dashboard...',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true
            }).then(() => {
                window.location.href = 'index.php'; // Arahkan ke dashboard admin
            });
            <?php elseif ($status_login === 'wrong_password'): ?>
            Swal.fire({
                icon: 'error',
                title: 'Password Salah!',
                text: 'Kata sandi yang Anda masukkan tidak sesuai. Silakan coba kembali.',
                confirmButtonColor: '#00a86b'
            });
            <?php elseif ($status_login === 'username_not_found'): ?>
            Swal.fire({
                icon: 'error',
                title: 'Admin Tidak Ditemukan!',
                text: 'Username tidak terdaftar dalam database sistem kami.',
                confirmButtonColor: '#00a86b'
            });
            <?php elseif ($status_login === 'empty_fields'): ?>
            Swal.fire({
                icon: 'warning',
                title: 'Isian Belum Lengkap!',
                text: 'Harap isi kolom username dan password sebelum masuk.',
                confirmButtonColor: '#00a86b'
            });
            <?php else: ?>
            Swal.fire({
                icon: 'error',
                title: 'Kegagalan Sistem!',
                text: 'Terjadi kendala autentikasi eksternal pada server database.',
                confirmButtonColor: '#00a86b'
            });
            <?php endif; ?>
        });
    </script>
    <?php endif; ?>
</body>
</html>