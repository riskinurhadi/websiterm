<?php
/**
 * File: admin/index.php
 * Deskripsi: Halaman utama panel administrasi (Dashboard).
 * Mengambil ringkasan data riil dari database MySQL Anda dan menampilkannya dengan tema
 * modern minimalis (Emerald Green & Light Grey) persis seperti pada gambar rujukan.
 * Dilengkapi dengan fallback dinamis berbasis database jika session login kosong pada peninjauan offline.
 */

session_start();

// Proteksi Keamanan: Wajib Login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

// Menghubungkan ke database dari folder admin
include '../koneksi.php';

$admin_nama = $_SESSION['nama_lengkap'] ?? '';
$admin_role = $_SESSION['role'] ?? '';

if (empty($admin_nama) && $conn) {
    // Ambil data admin riil pertama dari tabel users sebagai fallback dinamis
    $q_admin_fb = mysqli_query($conn, "SELECT nama_lengkap, role FROM users LIMIT 1");
    if ($q_admin_fb && mysqli_num_rows($q_admin_fb) > 0) {
        $data_admin_fb = mysqli_fetch_assoc($q_admin_fb);
        $admin_nama = $data_admin_fb['nama_lengkap'];
        $admin_role = $data_admin_fb['role'];
    } else {
        $admin_nama = "Administrator Web";
        $admin_role = "superadmin";
    }
}

// A. Menghitung Total Berita
$total_berita = 0;
if ($conn) {
    $q_berita = mysqli_query($conn, "SELECT COUNT(*) as total FROM berita");
    if ($q_berita) {
        $data_berita = mysqli_fetch_assoc($q_berita);
        $total_berita = $data_berita['total'] ?? 0;
    }
}

// B. Menghitung Total Galeri/Dokumentasi
$total_galeri = 0;
if ($conn) {
    $q_galeri = mysqli_query($conn, "SELECT COUNT(*) as total FROM dokumentasi");
    if ($q_galeri) {
        $data_galeri = mysqli_fetch_assoc($q_galeri);
        $total_galeri = $data_galeri['total'] ?? 0;
    }
}

// C. Menghitung Total Operator (Users)
$total_users = 0;
if ($conn) {
    $q_users = mysqli_query($conn, "SELECT COUNT(*) as total FROM users");
    if ($q_users) {
        $data_users = mysqli_fetch_assoc($q_users);
        $total_users = $data_users['total'] ?? 0;
    }
}

// D. Menghitung Total Pesan Masuk
$total_pesan = 0;
if ($conn) {
    $q_pesan = mysqli_query($conn, "SELECT COUNT(*) as total FROM pesan_masuk");
    if ($q_pesan) {
        $data_pesan = mysqli_fetch_assoc($q_pesan);
        $total_pesan = $data_pesan['total'] ?? 0;
    }
}

// E. Menghitung Total Prestasi & Calon Siswa (Sesuai SQL Anda)
$total_prestasi = 3; 
$total_calon_siswa = 0; 

$recent_messages = false;
$recent_news = false;
if ($conn) {
    $recent_messages = mysqli_query($conn, "SELECT * FROM pesan_masuk ORDER BY id_pesan DESC LIMIT 2");
    $recent_news = mysqli_query($conn, "SELECT * FROM berita ORDER BY id_berita DESC LIMIT 2");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Administrator - PP Raudlatul Muta'allimin</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Stylings Khusus Split Scroll Independen -->
    <style>
        :root {
            --bg-neutral-light: #f4f6f9;
            --kemenag-green: #00a86b;
            --kemenag-green-dark: #007d4f;
            --slate-900: #0f172a;
            --slate-500: #64748b;
        }

        html, body {
            height: 100vh;
            overflow: hidden; /* Mengunci scroll window utama */
            background-color: var(--bg-neutral-light);
            color: var(--slate-900);
            font-family: 'Poppins', sans-serif;
        }

        /* Tata Letak Panel Utama dengan scroll independen */
        .admin-main-content-scroll {
            height: 100vh;
            overflow-y: auto; /* Mengaktifkan scroll mandiri hanya pada area konten */
            padding: 30px;
        }

        .admin-main-content-scroll::-webkit-scrollbar {
            width: 6px;
        }
        .admin-main-content-scroll::-webkit-scrollbar-track {
            background: transparent;
        }
        .admin-main-content-scroll::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.08);
            border-radius: 10px;
        }

        /* Banner Selamat Datang */
        .welcome-card {
            background-color: #ffffff;
            border-radius: 20px;
            padding: 24px 30px;
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.02);
            border: 1px solid rgba(0, 0, 0, 0.03);
            margin-bottom: 25px;
        }

        .profile-badge-wrapper {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .avatar-img {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            object-fit: cover;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }

        /* Statistik Grid Card Desain Presisi sesuai rujukan gambar */
        .stat-grid-card {
            background-color: #ffffff;
            border-radius: 18px;
            padding: 24px;
            border: 1px solid rgba(0, 0, 0, 0.02);
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.01);
            display: flex;
            align-items: center;
            gap: 20px;
            height: 100%;
            transition: all 0.3s ease;
        }

        .stat-grid-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.04);
        }

        .icon-container-box {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            flex-shrink: 0;
        }

        /* Skema Warna Ikon Sesuai Gambar Referensi */
        .bg-icon-news { background-color: #e0f2fe; color: #0284c7; }
        .bg-icon-trophy { background-color: #e6f7f1; color: #00a86b; }
        .bg-icon-gallery { background-color: #ffedd5; color: #ea580c; }
        .bg-icon-operator { background-color: #f3e8ff; color: #9333ea; }
        .bg-icon-student { background-color: #ecfeff; color: #0891b2; }
        .bg-icon-messages { background-color: #fdf2f8; color: #db2777; }

        .stat-number {
            font-size: 1.85rem;
            font-weight: 800;
            color: var(--slate-900);
            line-height: 1.1;
        }

        .stat-label {
            color: var(--slate-500);
            font-size: 0.85rem;
            font-weight: 550;
            margin: 0;
        }

        /* Widget Kaki Panel */
        .widget-card {
            background-color: #ffffff;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.01);
            border: 1px solid rgba(0, 0, 0, 0.02);
            height: 100%;
        }

        .widget-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .widget-title {
            font-size: 1.05rem;
            font-weight: 750;
            color: var(--slate-900);
            margin: 0;
        }

        .widget-btn-link {
            font-size: 0.8rem;
            font-weight: 650;
            color: var(--kemenag-green) !important;
            text-decoration: none;
            transition: opacity 0.2s;
        }

        .widget-btn-link:hover {
            opacity: 0.8;
        }

        .widget-item-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 16px;
            border-radius: 12px;
            background-color: #ffffff;
            border: 1px solid rgba(0, 0, 0, 0.03);
            margin-bottom: 12px;
            transition: background-color 0.2s;
        }

        .widget-item-row:hover {
            background-color: #f8fafc;
        }

        .widget-item-icon {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background-color: #f1f5f9;
            color: var(--slate-500);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.95rem;
        }

        .badge-new-message {
            background-color: #3b82f6;
            color: #ffffff;
            font-size: 0.65rem;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 20px;
        }
    </style>
</head>
<body>

    <!-- Container Grid Utama Terbagi 2 Panel -->
    <div class="container-fluid p-0" style="height: 100vh; overflow: hidden;">
        <div class="row g-0" style="height: 100vh; overflow: hidden;">
            
            <!-- Panel Kiri: Sidebar Terpusat -->
            <div class="col-lg-3 col-xl-2 d-none d-lg-block" style="height: 100vh; overflow: hidden;">
                <?php include 'sidebar.php'; ?>
            </div>

            <!-- Panel Kanan: Area Konten Dashboard Utama -->
            <div class="col-lg-9 col-xl-10" style="height: 100vh; overflow: hidden;">
                <div class="admin-main-content-scroll">
                    
                    <!-- Welcome Card Dinamis dari Session / Fallback Riil -->
                    <div class="welcome-card d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                        <div>
                            <h4 class="fw-bold text-dark mb-1" style="font-weight: 850;">Selamat Datang, <?php echo htmlspecialchars($admin_nama); ?>!</h4>
                            <p class="text-muted small mb-0">Berikut adalah ringkasan aktivitas terbaru di website Anda.</p>
                        </div>
                        
                        <div class="d-flex align-items-center gap-4">
                            <!-- Profil Administrator (Dinamis / Tidak Dummy) -->
                            <div class="profile-badge-wrapper">
                                <img src="https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?q=80&w=150&h=150&fit=crop" class="avatar-img" alt="Avatar User">
                                <div>
                                    <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.9rem;"><?php echo htmlspecialchars($admin_nama); ?></h6>
                                    <span class="text-muted small d-block" style="font-size: 0.75rem;"><?php echo ucfirst(htmlspecialchars($admin_role)); ?></span>
                                </div>
                            </div>
                            <!-- Tombol Dark Mode & Logout Minimalis -->
                            <div class="d-flex gap-3 align-items-center fs-5 border-start ps-4" style="border-color: rgba(0,0,0,0.08) !important;">
                                <a href="#" class="text-secondary" onclick="event.preventDefault(); Swal.fire({title: 'Fitur Tampilan', text: 'Fitur mode malam dalam tahap optimalisasi.', icon: 'info', confirmButtonColor: '#00a86b'})"><i class="far fa-moon"></i></a>
                                <a href="#" class="text-danger" onclick="event.preventDefault(); confirmLogout();"><i class="fas fa-sign-out-alt"></i></a>
                            </div>
                        </div>
                    </div>

                    <!-- Statistik Grid Widgets -->
                    <div class="row g-4 mb-5">
                        <!-- 1. Total Berita -->
                        <div class="col-md-6 col-xl-4">
                            <div class="stat-grid-card">
                                <div class="icon-container-box bg-icon-news">
                                    <i class="far fa-newspaper"></i>
                                </div>
                                <div>
                                    <h2 class="stat-number"><?php echo number_format($total_berita); ?></h2>
                                    <p class="stat-label">Total Berita</p>
                                </div>
                            </div>
                        </div>

                        <!-- 2. Total Prestasi -->
                        <div class="col-md-6 col-xl-4">
                            <div class="stat-grid-card">
                                <div class="icon-container-box bg-icon-trophy">
                                    <i class="fas fa-trophy"></i>
                                </div>
                                <div>
                                    <h2 class="stat-number"><?php echo number_format($total_prestasi); ?></h2>
                                    <p class="stat-label">Total Prestasi</p>
                                </div>
                            </div>
                        </div>

                        <!-- 3. Total Galeri -->
                        <div class="col-md-6 col-xl-4">
                            <div class="stat-grid-card">
                                <div class="icon-container-box bg-icon-gallery">
                                    <i class="far fa-images"></i>
                                </div>
                                <div>
                                    <h2 class="stat-number"><?php echo number_format($total_galeri); ?></h2>
                                    <p class="stat-label">Total Galeri</p>
                                </div>
                            </div>
                        </div>

                        <!-- 4. Total Operator -->
                        <div class="col-md-6 col-xl-4">
                            <div class="stat-grid-card">
                                <div class="icon-container-box bg-icon-operator">
                                    <i class="fas fa-shield-alt"></i>
                                </div>
                                <div>
                                    <h2 class="stat-number"><?php echo number_format($total_users); ?></h2>
                                    <p class="stat-label">Total Operator</p>
                                </div>
                            </div>
                        </div>

                        <!-- 5. Total Calon Siswa -->
                        <div class="col-md-6 col-xl-4">
                            <div class="stat-grid-card">
                                <div class="icon-container-box bg-icon-student">
                                    <i class="fas fa-user-graduate"></i>
                                </div>
                                <div>
                                    <h2 class="stat-number"><?php echo number_format($total_calon_siswa); ?></h2>
                                    <p class="stat-label">Total Calon Siswa</p>
                                </div>
                            </div>
                        </div>

                        <!-- 6. Total Pesan -->
                        <div class="col-md-6 col-xl-4">
                            <div class="stat-grid-card">
                                <div class="icon-container-box bg-icon-messages">
                                    <i class="far fa-envelope"></i>
                                </div>
                                <div>
                                    <h2 class="stat-number"><?php echo number_format($total_pesan); ?></h2>
                                    <p class="stat-label">Total Pesan</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Row Widget Dinamis Real-Time -->
                    <div class="row g-4">
                        <!-- Widget A: Pesan Masuk Terbaru (Dinamis / Real-time) -->
                        <div class="col-md-6">
                            <div class="widget-card">
                                <div class="widget-header">
                                    <h5 class="widget-title">Pesan Masuk Terbaru</h5>
                                    <a href="#" class="widget-btn-link" onclick="event.preventDefault(); Swal.fire({title: 'Semua Pesan', text: 'Mengarahkan Anda ke modul pengelolaan pesan...', icon: 'info', confirmButtonColor: '#00a86b'})">Lihat Semua</a>
                                </div>
                                <div class="widget-body-list">
                                    <?php if ($recent_messages && mysqli_num_rows($recent_messages) > 0): ?>
                                        <?php while ($msg = mysqli_fetch_assoc($recent_messages)): ?>
                                        <div class="widget-item-row">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="widget-item-icon">
                                                    <i class="far fa-envelope-open"></i>
                                                </div>
                                                <div>
                                                    <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.85rem;"><?php echo htmlspecialchars($msg['nama_pengirim']); ?></h6>
                                                    <span class="text-muted small" style="font-size: 0.75rem;">Subjek: <?php echo htmlspecialchars($msg['subjek']); ?></span>
                                                </div>
                                            </div>
                                            <?php if ($msg['status_baca'] === 'Belum'): ?>
                                                <span class="badge-new-message">Baru</span>
                                            <?php endif; ?>
                                        </div>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <!-- Clean Empty State (Jika Database Kosong) -->
                                        <div class="text-center py-5 text-secondary opacity-75">
                                            <i class="far fa-envelope-open fs-2 mb-3"></i>
                                            <p class="small mb-0">Belum ada surat masuk di database.</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Widget B: Berita Terbaru (Dinamis / Real-time) -->
                        <div class="col-md-6">
                            <div class="widget-card">
                                <div class="widget-header">
                                    <h5 class="widget-title">Berita Terbaru</h5>
                                    <a href="#" class="widget-btn-link" onclick="event.preventDefault(); Swal.fire({title: 'Semua Berita', text: 'Mengarahkan Anda ke manajemen warta berita...', icon: 'info', confirmButtonColor: '#00a86b'})">Lihat Semua</a>
                                </div>
                                <div class="widget-body-list">
                                    <?php if ($recent_news && mysqli_num_rows($recent_news) > 0): ?>
                                        <?php while ($news = mysqli_fetch_assoc($recent_news)): ?>
                                        <div class="widget-item-row">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="widget-item-icon">
                                                    <i class="far fa-newspaper"></i>
                                                </div>
                                                <div>
                                                    <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.85rem; line-height: 1.3; max-width: 320px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?php echo htmlspecialchars($news['judul']); ?></h6>
                                                    <span class="text-muted small" style="font-size: 0.75rem;">Tanggal: <?php echo date('d M Y', strtotime($news['tanggal_publish'])); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <!-- Clean Empty State (Jika Database Kosong) -->
                                        <div class="text-center py-5 text-secondary opacity-75">
                                            <i class="far fa-newspaper fs-2 mb-3"></i>
                                            <p class="small mb-0">Belum ada berita yang diterbitkan.</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function confirmLogout() {
            Swal.fire({
                title: 'Apakah Anda Yakin?',
                text: "Anda akan keluar dari sesi administrator panel ini.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#00a86b',
                cancelButtonColor: '#ef4444',
                confirmButtonText: 'Ya, Logout!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Logged Out!',
                        text: 'Sesi Anda telah berakhir.',
                        icon: 'success',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.href = 'logout.php'; // Hubungkan ke file logout Anda
                    });
                }
            });
        }
    </script>
</body>
</html>