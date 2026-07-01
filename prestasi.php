<?php
/**
 * File: prestasi.php
 * Deskripsi: Halaman front-end untuk menampilkan seluruh daftar prestasi santri 
 * dari database. Terintegrasi dengan navbar dan footer utama.
 */

include 'koneksi.php';

// Mengambil SEMUA data prestasi dari database
$query_prestasi = mysqli_query($conn, "SELECT * FROM prestasi ORDER BY tahun DESC, id_prestasi DESC");
$prestasi_list = [];
if ($query_prestasi && mysqli_num_rows($query_prestasi) > 0) {
    while ($row = mysqli_fetch_assoc($query_prestasi)) {
        $prestasi_list[] = $row;
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

    .deco-line {
        width: 60px; height: 4px; background-color: var(--kemenag-green-primary);
        border-radius: 2px; margin: 15px auto 0 auto;
    }
</style>

<div class="page-header">
    <div class="container">
        <h1 class="display-5 fw-bold mb-3">Prestasi Santri</h1>
        <p class="lead mb-0 opacity-75">Daftar lengkap pencapaian dan kejuaraan siswa-siswi Yayasan Raudlatul Muta'allimin.</p>
    </div>
</div>

<div class="container pb-5 mb-5">
    <div class="row g-4 justify-content-center">
        <?php if (count($prestasi_list) > 0): ?>
            <?php foreach ($prestasi_list as $p): ?>
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 modern-card">
                    <div class="position-relative overflow-hidden" style="height: 220px;">
                        <?php 
                            $foto_prestasi = $p['gambar'];
                            if (empty($foto_prestasi)) {
                                $foto_prestasi = 'https://images.unsplash.com/photo-1517841905240-472988babdf9?q=80&w=600&h=400&fit=crop';
                            }
                        ?>
                        <img src="<?php echo htmlspecialchars($foto_prestasi); ?>" class="w-100 h-100" alt="Foto Prestasi" style="object-fit: cover;">
                        <span class="position-absolute px-3 py-1 text-white rounded-pill small fw-semibold" style="bottom: 15px; right: 15px; background-color: var(--kemenag-green-primary); font-size: 0.75rem;">
                            <?php echo htmlspecialchars($p['tingkat']); ?>
                        </span>
                    </div>
                    <div class="card-body p-4 text-start">
                        <h5 class="fw-bold mb-3" style="color: var(--dark-neutral); font-size: 1.15rem; line-height: 1.4; letter-spacing: -0.3px;">
                            <?php echo strtoupper($p['judul']); ?>
                        </h5>
                        <p class="small mb-4" style="color: var(--text-muted-custom); line-height: 1.7;">
                            <?php echo htmlspecialchars($p['deskripsi']); ?>
                        </p>
                        <div class="pt-3 border-top d-flex justify-content-between align-items-center" style="border-color: rgba(0,0,0,0.05) !important;">
                            <span class="small text-muted fw-medium"><i class="fas fa-calendar-alt me-1"></i> Tahun <?php echo htmlspecialchars($p['tahun']); ?></span>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <i class="fas fa-award fs-1 text-muted opacity-25 mb-3"></i>
                <p class="text-muted">Belum ada data prestasi yang dicatat.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'footer.php'; ?>