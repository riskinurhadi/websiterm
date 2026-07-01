<?php
/**
 * File: galeri.php
 * Deskripsi: Halaman front-end untuk menampilkan seluruh dokumentasi foto kegiatan.
 */

include 'koneksi.php';

// Mengambil SEMUA data galeri dari database
$query_galeri = mysqli_query($conn, "SELECT * FROM dokumentasi ORDER BY tanggal_kegiatan DESC, id_galeri DESC");
$galeri_list = [];
if ($query_galeri && mysqli_num_rows($query_galeri) > 0) {
    while ($row = mysqli_fetch_assoc($query_galeri)) {
        $galeri_list[] = $row;
    }
}

include 'navbar.php';
?>

<style>
    :root {
        --kemenag-green-primary: #00a86b !important;
        --kemenag-green-dark: #007d4f !important;
        --light-neutral: #f8fafc !important;
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

    /* Efek Hover Galeri */
    .gallery-item img {
        transition: var(--transition-smooth);
        cursor: pointer;
    }
    .gallery-overlay {
        position: absolute;
        top: 0; left: 0; width: 100%; height: 100%;
        background-color: rgba(0, 168, 107, 0.9);
        opacity: 0;
        transition: var(--transition-smooth);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 20px;
        text-align: center;
    }
    .gallery-item:hover img {
        transform: scale(1.08);
    }
    .gallery-item:hover .gallery-overlay {
        opacity: 1;
    }
</style>

<div class="page-header">
    <div class="container">
        <h1 class="display-5 fw-bold mb-3">Galeri Dokumentasi</h1>
        <p class="lead mb-0 opacity-75">Kumpulan foto dan dokumentasi kegiatan pembelajaran santri.</p>
    </div>
</div>

<div class="container pb-5 mb-5">
    <div class="row g-3">
        <?php if (count($galeri_list) > 0): ?>
            <?php foreach ($galeri_list as $g): ?>
            <div class="col-lg-3 col-md-4 col-6">
                <!-- Membuka foto layar penuh menggunakan SweetAlert (sederhana) atau bisa diganti Lightbox -->
                <div class="gallery-item rounded-4 overflow-hidden position-relative ratio ratio-1x1 shadow-sm" onclick="Swal.fire({imageUrl: '<?php echo $g['file_foto']; ?>', imageAlt: 'Galeri', title: '<?php echo addslashes($g['judul_kegiatan']); ?>', text: '<?php echo addslashes($g['deskripsi']); ?>', confirmButtonColor: '#00a86b', width: 800})">
                    <?php 
                        $foto_file = isset($g['file_foto']) ? $g['file_foto'] : 'https://images.unsplash.com/photo-1577896851231-70ef18881754?q=80&w=600&h=600&fit=crop';
                    ?>
                    <img src="<?php echo htmlspecialchars($foto_file); ?>" class="img-fluid object-fit-cover" alt="Galeri">
                    
                    <div class="gallery-overlay">
                        <span class="text-white fw-bold mb-1" style="font-size: 0.9rem;">
                            <?php echo htmlspecialchars($g['judul_kegiatan']); ?>
                        </span>
                        <span class="text-white-50 small">
                            <i class="far fa-calendar-alt me-1"></i> <?php echo date('d M Y', strtotime($g['tanggal_kegiatan'])); ?>
                        </span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <i class="far fa-images fs-1 text-muted opacity-25 mb-3"></i>
                <p class="text-muted">Belum ada foto kegiatan yang diunggah.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'footer.php'; ?>