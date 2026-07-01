<?php
/**
 * File: detail_berita.php
 * Deskripsi: Halaman front-end untuk membaca warta berita secara penuh.
 * Menampilkan isi berita dari Summernote, menghitung view, dan menampilkan berita terkait.
 */

include 'koneksi.php';

// Mendapatkan ID berita dari URL
$id_berita = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Mengambil data berita utama berdasarkan ID
$query_berita = mysqli_query($conn, "SELECT * FROM berita WHERE id_berita = $id_berita");
$berita = mysqli_fetch_assoc($query_berita);

// Mengambil 3 berita lainnya untuk direkomendasikan di bagian bawah (kecuali berita yang sedang dibaca)
$query_lainnya = mysqli_query($conn, "SELECT * FROM berita WHERE id_berita != $id_berita ORDER BY tanggal_publish DESC LIMIT 3");
$berita_lainnya = [];
if ($query_lainnya && mysqli_num_rows($query_lainnya) > 0) {
    while ($row = mysqli_fetch_assoc($query_lainnya)) {
        $berita_lainnya[] = $row;
    }
}

// Jika berita ditemukan, tambahkan view count (jumlah dilihat)
if ($berita) {
    mysqli_query($conn, "UPDATE berita SET dilihat = dilihat + 1 WHERE id_berita = $id_berita");
}

include 'navbar.php';
?>

<style>
    :root {
        --kemenag-green-primary: #00a86b !important;
        --kemenag-green-dark: #007d4f !important;
        --kemenag-green-light: #f0faf5 !important;
        --light-neutral: #f8fafc !important;
        --dark-neutral: #0f172a !important;
        --text-muted-custom: #475569 !important;
        --transition-smooth: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    }

    body { background-color: var(--light-neutral) !important; font-family: 'Poppins', sans-serif; }
    
    .article-header {
        background: linear-gradient(135deg, var(--kemenag-green-dark) 0%, var(--kemenag-green-primary) 100%);
        padding: 90px 0 50px 0; 
        color: white;
        margin-bottom: -30px; 
    }

    .article-card {
        background-color: #ffffff !important;
        border: none !important;
        border-radius: 20px !important;
        box-shadow: 0 20px 40px -15px rgba(15, 23, 42, 0.08) !important;
        padding: 40px;
        margin-bottom: 50px;
        position: relative;
        z-index: 2;
    }

    /* Penyesuaian Responsif untuk HP */
    @media (max-width: 767.98px) {
        .article-card {
            padding: 25px 20px;
            border-radius: 16px !important;
        }
        .article-title {
            font-size: 1.6rem !important;
            line-height: 1.3 !important;
        }
    }

    .article-title {
        font-weight: 800;
        color: var(--dark-neutral);
        font-size: 2.2rem;
        line-height: 1.2;
        letter-spacing: -0.5px;
    }

    .article-meta {
        font-size: 0.85rem;
        color: var(--text-muted-custom);
        font-weight: 500;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        padding-bottom: 20px;
        margin-bottom: 30px;
    }

    .article-meta i {
        color: var(--kemenag-green-primary);
    }

    .article-cover {
        width: 100%;
        height: auto;
        max-height: 500px;
        object-fit: cover;
        border-radius: 16px;
        box-shadow: 0 10px 20px -10px rgba(0,0,0,0.1);
    }

    /* Styling konten dari Summernote */
    .article-content {
        font-size: 1.05rem;
        line-height: 1.9;
        color: #334155;
    }

    .article-content img {
        max-width: 100% !important;
        height: auto !important;
        border-radius: 12px;
        margin: 15px 0;
    }

    .article-content p {
        margin-bottom: 1.2rem;
    }

    .btn-back-outline {
        border: 2px solid var(--kemenag-green-primary);
        color: var(--kemenag-green-primary);
        font-weight: 600;
        border-radius: 50px;
        padding: 10px 25px;
        transition: var(--transition-smooth);
        display: inline-block;
        text-decoration: none;
    }

    .btn-back-outline:hover {
        background-color: var(--kemenag-green-primary);
        color: white;
    }

    /* Tambahan CSS untuk Card Berita Lainnya */
    .modern-card {
        background-color: #ffffff !important;
        border: 1px solid rgba(0,0,0,0.05) !important;
        border-radius: 16px !important;
        box-shadow: 0 10px 30px -10px rgba(15, 23, 42, 0.03) !important;
        transition: var(--transition-smooth) !important;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        height: 100%;
    }
    .modern-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px -15px rgba(0, 168, 107, 0.15) !important;
        border-color: rgba(0, 168, 107, 0.2) !important;
    }
    .badge-modern {
        background-color: var(--kemenag-green-light) !important;
        color: var(--kemenag-green-primary) !important;
        font-weight: 600; 
        border-radius: 30px; 
        padding: 4px 12px; 
    }
</style>

<!-- Latar Belakang Hijau Atas -->
<div class="article-header">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php" class="text-white-50 text-decoration-none">Beranda</a></li>
                <li class="breadcrumb-item"><a href="berita.php" class="text-white-50 text-decoration-none">Warta</a></li>
                <li class="breadcrumb-item active text-white" aria-current="page">Baca Artikel</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container">
    <div class="row justify-content-center">
        <!-- Membatasi lebar konten agar nyaman dibaca -->
        <div class="col-lg-9">
            
            <?php if ($berita): ?>
                <div class="article-card">
                    <!-- 1. Gambar Cover Di Atas -->
                    <?php 
                        $cover_berita = $berita['gambar_cover'];
                        if (empty($cover_berita)) {
                            $cover_berita = 'https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?q=80&w=1200&h=600&fit=crop';
                        }
                    ?>
                    <img src="<?php echo htmlspecialchars($cover_berita); ?>" class="article-cover mb-4" alt="Cover Berita">

                    <!-- 2. Judul Artikel Di Bawah Gambar -->
                    <h1 class="article-title mb-3"><?php echo htmlspecialchars($berita['judul']); ?></h1>
                    
                    <!-- 3. Meta Data / Keterangan Penulis & Tanggal -->
                    <div class="article-meta d-flex flex-wrap gap-3 align-items-center">
                        <span><i class="fas fa-user-edit me-1"></i> Ditulis oleh: <strong class="text-dark"><?php echo htmlspecialchars($berita['penulis']); ?></strong></span>
                        <span><i class="far fa-calendar-alt me-1"></i> <?php echo date('d M Y', strtotime($berita['tanggal_publish'])); ?></span>
                        <span><i class="far fa-eye me-1"></i> Dibaca <?php echo number_format($berita['dilihat'] + 1); ?> kali</span>
                    </div>

                    <!-- 4. Isi Artikel Utama -->
                    <div class="article-content">
                        <?php echo $berita['isi_berita']; ?>
                    </div>
                    
                    <!-- Tombol Kembali -->
                    <div class="mt-5 pt-4 border-top">
                        <a href="berita.php" class="btn-back-outline">
                            <i class="fas fa-arrow-left me-2"></i> Kembali ke Indeks Berita
                        </a>
                    </div>

                    <!-- 5. Section Berita Lainnya (Terkait) -->
                    <?php if (count($berita_lainnya) > 0): ?>
                    <div class="mt-5 pt-5 border-top">
                        <h4 class="fw-bold mb-4" style="color: var(--dark-neutral);">Baca Juga Warta Lainnya</h4>
                        <div class="row g-4">
                            <?php foreach ($berita_lainnya as $b_lain): ?>
                            <div class="col-md-4 col-sm-6">
                                <a href="detail_berita.php?id=<?php echo $b_lain['id_berita']; ?>" class="text-decoration-none">
                                    <div class="modern-card">
                                        <div class="position-relative overflow-hidden" style="height: 140px;">
                                            <?php 
                                                $cover_lain = $b_lain['gambar_cover'];
                                                if (empty($cover_lain)) {
                                                    $cover_lain = 'https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?q=80&w=600&h=400&fit=crop';
                                                }
                                            ?>
                                            <img src="<?php echo htmlspecialchars($cover_lain); ?>" class="w-100 h-100" alt="Cover" style="object-fit: cover;">
                                        </div>
                                        <div class="p-3 d-flex flex-column flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="badge-modern" style="font-size: 0.65rem;"><?php echo isset($b_lain['penulis']) ? htmlspecialchars($b_lain['penulis']) : 'Admin'; ?></span>
                                                <span class="small" style="font-size: 0.7rem; color: var(--text-muted-custom);">
                                                    <i class="far fa-calendar-alt me-1"></i> <?php echo date('d M', strtotime($b_lain['tanggal_publish'])); ?>
                                                </span>
                                            </div>
                                            <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.9rem; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                                                <?php echo htmlspecialchars($b_lain['judul']); ?>
                                            </h6>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                </div>
            <?php else: ?>
                <!-- Jika ID Berita Tidak Ditemukan -->
                <div class="article-card text-center py-5">
                    <i class="fas fa-exclamation-triangle text-warning mb-3" style="font-size: 4rem;"></i>
                    <h3 class="fw-bold text-dark">Artikel Tidak Ditemukan</h3>
                    <p class="text-muted">Maaf, warta berita yang Anda cari mungkin telah dihapus atau tautannya salah.</p>
                    <a href="berita.php" class="btn-back-outline mt-3">
                        <i class="fas fa-arrow-left me-2"></i> Kembali ke Daftar Warta
                    </a>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<?php include 'footer.php'; ?>