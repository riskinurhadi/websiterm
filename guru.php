<?php
/**
 * File: guru.php
 * Deskripsi: Halaman front-end untuk menampilkan daftar seluruh asatidz dan asatidzah (guru/staf).
 */

include 'koneksi.php';

// Fungsi untuk menerjemahkan bulan ke Bahasa Indonesia
function tanggal_indo($tanggal) {
    $bulan = array (
        1 =>   'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    );
    $pecahkan = explode('-', $tanggal);
    // $pecahkan[0] = tahun, $pecahkan[1] = bulan, $pecahkan[2] = tanggal
    return $pecahkan[2] . ' ' . $bulan[ (int)$pecahkan[1] ] . ' ' . $pecahkan[0];
}

// Mengambil SEMUA data guru dari database
$query_guru = mysqli_query($conn, "SELECT * FROM guru ORDER BY id_guru DESC");
$guru_list = [];
if ($query_guru && mysqli_num_rows($query_guru) > 0) {
    while ($row = mysqli_fetch_assoc($query_guru)) {
        $guru_list[] = $row;
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
        border-top: 4px solid transparent !important;
    }

    .modern-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px -15px rgba(0, 168, 107, 0.15) !important;
        border-top: 4px solid var(--kemenag-green-primary) !important;
    }

    .foto-guru {
        width: 140px;
        height: 140px;
        object-fit: cover;
        border-radius: 50%;
        border: 4px solid var(--light-neutral);
        box-shadow: 0 8px 20px rgba(0,0,0,0.08);
        margin: -70px auto 15px auto;
        position: relative;
        z-index: 2;
        background-color: white;
    }

    .card-header-bg {
        height: 100px;
        background-color: var(--kemenag-green-light);
        width: 100%;
    }

    .badge-jabatan {
        background-color: var(--kemenag-green-light) !important;
        color: var(--kemenag-green-primary) !important;
        font-weight: 600; 
        border-radius: 30px; 
        padding: 6px 16px; 
        font-size: 0.8rem;
        display: inline-block;
        margin-bottom: 15px;
    }
</style>

<div class="page-header">
    <div class="container">
        <h1 class="display-5 fw-bold mb-3">Asatidz & Asatidzah</h1>
        <p class="lead mb-0 opacity-75">Mengenal lebih dekat dewan guru dan tenaga kependidikan Pondok Pesantren Raudlatul Muta'allimin.</p>
    </div>
</div>

<div class="container pb-5 mb-5">
    <div class="row g-4 justify-content-center">
        <?php if (count($guru_list) > 0): ?>
            <?php foreach ($guru_list as $g): ?>
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="card h-100 modern-card text-center">
                    <div class="card-header-bg"></div>
                    
                    <?php 
                        $foto_profil = $g['foto'];
                        if (empty($foto_profil) || $foto_profil == 'default_guru.jpg') {
                            $foto_profil = 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?q=80&w=256&h=256&fit=crop';
                        }
                    ?>
                    <img src="<?php echo htmlspecialchars($foto_profil); ?>" class="foto-guru" alt="Foto <?php echo htmlspecialchars($g['nama_lengkap']); ?>">
                    
                    <div class="card-body px-3 pb-4 pt-0 d-flex flex-column">
                        <h5 class="fw-bold mb-2" style="color: var(--dark-neutral); font-size: 1.05rem; line-height: 1.3;">
                            <?php echo htmlspecialchars($g['nama_lengkap']); ?>
                        </h5>
                        
                        <div>
                            <span class="badge-jabatan">
                                <?php echo htmlspecialchars($g['jabatan']); ?>
                            </span>
                        </div>
                        
                        <div class="mt-auto pt-2 border-top" style="border-color: rgba(0,0,0,0.05) !important;">
                            <span class="small d-block text-muted" style="font-size: 0.8rem;">
                                <i class="fas fa-map-marker-alt me-1 text-success opacity-75"></i> 
                                <?php echo htmlspecialchars($g['tempat_lahir']); ?>, <br>
                                <?php echo tanggal_indo($g['tanggal_lahir']); ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <i class="fas fa-users-class fs-1 text-muted opacity-25 mb-3"></i>
                <p class="text-muted">Belum ada data asatidz/guru yang ditambahkan.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'footer.php'; ?>