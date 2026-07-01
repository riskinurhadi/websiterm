<?php
/**
 * File: berita.php
 * Deskripsi: Halaman front-end untuk menampilkan seluruh warta berita dan pengumuman.
 */

include 'koneksi.php';

// Mengambil SEMUA data berita dari database
$query_berita = mysqli_query($conn, "SELECT * FROM berita ORDER BY tanggal_publish DESC, id_berita DESC");
$berita_list = [];
if ($query_berita && mysqli_num_rows($query_berita) > 0) {
    while ($row = mysqli_fetch_assoc($query_berita)) {
        $berita_list[] = $row;
    }
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
    
    .page-header {
        background: linear-gradient(135deg, var(--kemenag-green-dark) 0%, var(--kemenag-green-primary) 100%);
        padding: 120px 0 60px 0;
        color: white;
        text-align: center;
        margin-bottom: 50px;
    }

    .modern-card {
        background-color: #ffffff !important;
        border: none !important;
        border-radius: 20px !important;
        box-shadow: 0 10px 30px -10px rgba(15, 23, 42, 0.05) !important;
        transition: var(--transition-smooth) !important;
        overflow: hidden;
    }

    .modern-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px -15px rgba(0, 168, 107, 0.15) !important;
    }

    .badge-modern {
        background-color: var(--kemenag-green-light) !important;
        color: var(--kemenag-green-primary) !important;
        font-weight: 600; border-radius: 30px; padding: 6px 14px; font-size: 0.75rem;
    }
</style>

<div class="page-header">
    <div class="container">
        <h1 class="display-5 fw-bold mb-3">Warta Pesantren</h1>
        <p class="lead mb-0 opacity-75">Kumpulan informasi, pengumuman, dan artikel kegiatan terbaru pondok pesantren.</p>
    </div>
</div>

<div class="container pb-5 mb-5">
    <div class="row g-4">
        <?php if (count($berita_list) > 0): ?>
            <?php foreach ($berita_list as $b): ?>
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 modern-card">
                    <div class="position-relative overflow-hidden" style="height: 220px;">
                        <?php 
                            $cover_berita = $b['gambar_cover'];
                            if (empty($cover_berita)) {
                                $cover_berita = 'https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?q=80&w=600&h=400&fit=crop';
                            }
                        ?>
                        <img src="<?php echo htmlspecialchars($cover_berita); ?>" class="w-100 h-100" alt="Cover Berita" style="object-fit: cover;">
                    </div>
                    <div class="card-body p-4 d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="badge-modern">
                                <?php echo isset($b['penulis']) ? htmlspecialchars($b['penulis']) : 'Admin'; ?>
                            </span>
                            <span class="small" style="color: var(--text-muted-custom);">
                                <i class="far fa-calendar-alt me-1"></i> 
                                <?php echo date('d M Y', strtotime($b['tanggal_publish'])); ?>
                            </span>
                        </div>
                        <h5 class="fw-bold mb-2" style="color: var(--dark-neutral); font-size: 1.15rem; line-height: 1.4;">
                            <?php echo htmlspecialchars($b['judul']); ?>
                        </h5>
                        <p class="small mb-4 flex-grow-1" style="color: var(--text-muted-custom); line-height: 1.6;">
                            <?php 
                                // Membersihkan tag HTML (dari Summernote) dan memotong panjang teks
                                $ringkasan = strip_tags($b['isi_berita']);
                                echo htmlspecialchars(substr($ringkasan, 0, 120)) . '...'; 
                            ?>
                        </p>
                        <div class="mt-auto">
                            <a href="detail_berita.php?id=<?php echo $b['id_berita']; ?>" class="text-decoration-none fw-bold small" style="color: var(--kemenag-green-primary) !important;">
                                Baca Selengkapnya <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <i class="far fa-newspaper fs-1 text-muted opacity-25 mb-3"></i>
                <p class="text-muted">Belum ada warta berita yang diterbitkan.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'footer.php'; ?>