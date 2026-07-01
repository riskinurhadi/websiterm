<?php
/**
 * File: admin/generate_hash.php
 * Deskripsi: Alat bantu otomatis untuk memperbarui hash password 'admin' menjadi 'admin123'
 * menggunakan enkripsi standar Bcrypt yang kompatibel dengan password_verify().
 * HAPUS FILE INI SETELAH DIGUNAKAN DEMI KEAMANAN!
 */

// Memasukkan file koneksi database
include '../koneksi.php';

$username_target = 'admin';
$password_baru   = 'admin123';

// Membuat hash Bcrypt yang aman dan valid
$hash_bcrypt = password_hash($password_baru, PASSWORD_DEFAULT);

$sukses = false;
$pesan_error = "";

// Cek apakah koneksi database berfungsi
if ($conn) {
    // Jalankan query update password untuk username admin
    $query_update = "UPDATE users SET password = '$hash_bcrypt' WHERE username = '$username_target'";
    if (mysqli_query($conn, $query_update)) {
        $sukses = true;
    } else {
        $pesan_error = mysqli_error($conn);
    }
} else {
    $pesan_error = "Koneksi database tidak aktif.";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penyelaras Password - PP Raudlatul Muta'allimin</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --kemenag-green-primary: #00a86b;
            --kemenag-green-dark: #007d4f;
            --light-neutral: #f8fafc;
            --dark-neutral: #0f172a;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--light-neutral);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .update-card {
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            max-width: 500px;
            width: 100%;
            padding: 40px 30px;
            border: 1px solid rgba(0,0,0,0.05);
        }
        .icon-box {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin: 0 auto 20px auto;
        }
        .icon-success {
            background-color: #e6f7f1;
            color: var(--kemenag-green-primary);
        }
        .icon-fail {
            background-color: #fdf2f2;
            color: #f87171;
        }
        .btn-custom {
            background-color: var(--kemenag-green-primary) !important;
            color: #ffffff !important;
            border-radius: 50px !important;
            padding: 10px 25px !important;
            font-weight: 600 !important;
            border: none !important;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }
        .btn-custom:hover {
            background-color: var(--kemenag-green-dark) !important;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>

    <div class="update-card text-center">
        <?php if ($sukses): ?>
            <div class="icon-box icon-success">
                <i class="fas fa-check-circle"></i>
            </div>
            <h4 class="fw-bold text-dark mb-2">Password Sinkron!</h4>
            <p class="text-muted small mb-4">
                Berhasil menyelaraskan hash Bcrypt baru di database. Sekarang password untuk username <strong>admin</strong> telah resmi diperbarui menjadi <strong>admin123</strong>.
            </p>
            <div class="alert alert-warning py-2 mb-4 small text-start">
                <i class="fas fa-exclamation-triangle me-1"></i> <strong>Keamanan:</strong> Harap segera hapus file <code>generate_hash.php</code> ini dari server hosting Anda demi keamanan sistem!
            </div>
            <a href="login.php" class="btn-custom">
                <i class="fas fa-sign-in-alt"></i> Ke Halaman Login
            </a>
        <?php else: ?>
            <div class="icon-box icon-fail">
                <i class="fas fa-times-circle"></i>
            </div>
            <h4 class="fw-bold text-danger mb-2">Gagal Menyelaraskan!</h4>
            <p class="text-muted small mb-4">
                Terjadi kesalahan teknis saat memperbarui data kata sandi di server database.
            </p>
            <div class="alert alert-danger py-2 mb-4 small text-start">
                <strong>Error:</strong> <?php echo htmlspecialchars($pesan_error); ?>
            </div>
            <a href="login.php" class="btn btn-secondary rounded-pill px-4">Kembali</a>
        <?php endif; ?>
    </div>

</body>
</html>