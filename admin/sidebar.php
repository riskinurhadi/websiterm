<?php
/**
 * File: admin/sidebar.php
 * Deskripsi: Komponen sidebar navigasi vertikal terpusat untuk panel admin PP Raudlatul Muta'allimin.
 * Menggunakan independen scroll agar tidak terpengaruh oleh scroll halaman utama.
 */

// Mengambil jumlah pesan belum terbaca untuk indikator notifikasi orange di menu sidebar
$query_unread = mysqli_query($conn, "SELECT COUNT(*) as unread FROM pesan_masuk WHERE status_baca = 'Belum'");
$unread_data  = mysqli_fetch_assoc($query_unread);
$unread_count = $unread_data['unread'] ?? 0;

// Mendapatkan nama file saat ini untuk menentukan link menu yang sedang aktif
$current_page = basename($_SERVER['PHP_SELF']);
?>
<style>
    .sidebar-wrapper {
        background-color: #ffffff;
        height: 100vh; /* Membatasi tinggi setinggi layar penuh */
        overflow-y: auto; /* Mengaktifkan scroll mandiri */
        border-right: 1px solid rgba(0, 0, 0, 0.05);
        padding: 30px 20px;
        position: relative;
    }

    /* Kustomisasi scrollbar sidebar agar tetap tipis dan elegan */
    .sidebar-wrapper::-webkit-scrollbar {
        width: 4px;
    }
    .sidebar-wrapper::-webkit-scrollbar-track {
        background: transparent;
    }
    .sidebar-wrapper::-webkit-scrollbar-thumb {
        background: rgba(0, 0, 0, 0.08);
        border-radius: 10px;
    }

    .sidebar-brand h3 {
        font-size: 1.35rem;
        font-weight: 850;
        color: #0f172a;
        letter-spacing: -0.5px;
        margin-bottom: 35px;
        padding-left: 10px;
    }

    .nav-menu-list {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .nav-menu-link {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 16px;
        color: #64748b !important;
        text-decoration: none;
        font-weight: 550;
        font-size: 0.9rem;
        border-radius: 12px;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        border-left: 3px solid transparent;
    }

    .nav-menu-link i {
        font-size: 1.1rem;
        width: 24px;
    }

    .nav-menu-link:hover {
        background-color: #f8fafc;
        color: #0f172a !important;
    }

    /* Status Aktif Sesuai Persis dengan Gambar Referensi (Indikator Hijau Emerald) */
    .nav-menu-link.active {
        background-color: #e6f7f1;
        color: #00a86b !important;
        border-left: 3.5px solid #00a86b;
    }

    /* Notifikasi Dot Bulat Orange di Samping Pesan Masuk */
    .notification-dot {
        width: 8px;
        height: 8px;
        background-color: #f97316;
        border-radius: 50%;
        display: inline-block;
    }
</style>

<div class="sidebar-wrapper d-flex flex-column h-100">
    <div class="sidebar-brand">
        <h3>Admin RM</h3>
    </div>
    
    <div class="nav-menu-list flex-grow-1">
        <!-- 1. Menu Dashboard -->
        <a href="index.php" class="nav-menu-link <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">
            <span class="d-flex align-items-center gap-3">
                <i class="fas fa-chart-pie"></i> Dashboard
            </span>
        </a>

        <!-- 2. Menu Pesan Masuk -->
        <a href="pesan.php" class="nav-menu-link <?php echo ($current_page == 'pesan.php') ? 'active' : ''; ?>">
            <span class="d-flex align-items-center gap-3">
                <i class="fas fa-envelope"></i> Pesan Masuk
            </span>
            <?php if ($unread_count > 0): ?>
                <span class="notification-dot"></span>
            <?php endif; ?>
        </a>

        <!-- 3. Menu Profil & Visi Misi -->
        <a href="profil.php" class="nav-menu-link <?php echo ($current_page == 'profil.php') ? 'active' : ''; ?>">
            <span class="d-flex align-items-center gap-3">
                <i class="fas fa-id-card"></i> Tentang
            </span>
        </a>

        <!-- 4. Menu Sambutan Pejabat -->
        <a href="sambutan.php" class="nav-menu-link <?php echo ($current_page == 'sambutan.php') ? 'active' : ''; ?>">
            <span class="d-flex align-items-center gap-3">
                <i class="fas fa-user-tie"></i> Sambutan Pejabat
            </span>
        </a>

        <!-- 5. Menu Testimoni Alumni -->
        <a href="testimoni.php" class="nav-menu-link <?php echo ($current_page == 'testimoni.php') ? 'active' : ''; ?>">
            <span class="d-flex align-items-center gap-3">
                <i class="fas fa-comment-dots"></i> Testimoni Alumni
            </span>
        </a>

        <!-- 6. Menu Warta Berita -->
        <a href="berita.php" class="nav-menu-link <?php echo ($current_page == 'berita.php') ? 'active' : ''; ?>">
            <span class="d-flex align-items-center gap-3">
                <i class="fas fa-newspaper"></i> Warta Berita
            </span>
        </a>

        <!-- 7. Menu Galeri Dokumentasi -->
        <a href="galeri.php" class="nav-menu-link <?php echo ($current_page == 'galeri.php') ? 'active' : ''; ?>">
            <span class="d-flex align-items-center gap-3">
                <i class="fas fa-images"></i> Galeri Kegiatan
            </span>
        </a>

        <!-- 8. Menu Prestasi Santri -->
        <a href="prestasi.php" class="nav-menu-link <?php echo ($current_page == 'prestasi.php') ? 'active' : ''; ?>">
            <span class="d-flex align-items-center gap-3">
                <i class="fas fa-trophy"></i> Prestasi Santri
            </span>
        </a>

        <!-- 9. Menu Kelola Operator -->
        <a href="operator.php" class="nav-menu-link <?php echo ($current_page == 'operator.php') ? 'active' : ''; ?>">
            <span class="d-flex align-items-center gap-3">
                <i class="fas fa-user-cog"></i> Kelola Operator
            </span>
        </a>

        <!-- Menu Dropdown Lainnya -->
        <a href="#lainnya" class="nav-menu-link d-flex align-items-center justify-content-between" onclick="event.preventDefault();">
            <span class="d-flex align-items-center gap-3">
                <i class="fas fa-ellipsis-h"></i> Lainnya
            </span>
            <i class="fas fa-chevron-down fs-8 opacity-50 text-end"></i>
        </a>
    </div>
</div>