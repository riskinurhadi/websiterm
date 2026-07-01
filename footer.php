<?php
/**
 * File: footer.php
 * Deskripsi: Bagian footer penutup website.
 * Desain disesuaikan presisi dengan gambar rujukan pengguna (Ikon bulat gelap, teks kuning/hijau).
 */

if (!isset($conn)) { include 'koneksi.php'; }

// Mengambil data profil untuk Footer
$query_foot = mysqli_query($conn, "SELECT * FROM profil_web WHERE id_profil = 1");
$pf = mysqli_fetch_assoc($query_foot);

$f_nama   = $pf['nama_pesantren'] ?? "Pondok Pesantren Raudlatul Muta'allimin";
$f_logo   = $pf['logo'] ?? "";
$f_alamat = $pf['alamat'] ?? "Jl. Dr. Ak. Gani, No.50, Jaya Tinggi, Kasui, Way Kanan, Lampung.";
$f_telp   = $pf['no_telepon'] ?? "+62 822-xxxx-xxxx";
$f_email  = $pf['email'] ?? "sekretariat@raudlatulmutaallimin.sch.id";

// Sosmed
$f_fb = $pf['facebook'] ?? "";
$f_ig = $pf['instagram'] ?? "";
$f_yt = $pf['youtube'] ?? "";
$f_wa = $pf['whatsapp'] ?? "";
?>
    <footer class="pt-5 pb-4" style="background-color: #0f172a; border-top: 4px solid #00a86b;">
        <div class="container">
            <div class="row g-4">
                
                <!-- Info Pondok -->
                <div class="col-lg-4 col-md-6">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <?php if (!empty($f_logo)): ?>
                            <img src="<?php echo htmlspecialchars($f_logo); ?>" alt="Logo" style="width: 50px; height: 50px; object-fit: contain;">
                        <?php else: ?>
                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background-color: #1e293b; color: #00a86b; font-size: 1.5rem;">
                                <i class="fas fa-mosque"></i>
                            </div>
                        <?php endif; ?>
                        <h5 class="fw-bold mb-0 text-white" style="letter-spacing: 0.5px;"><?php echo htmlspecialchars($f_nama); ?></h5>
                    </div>
                    
                    <!-- Desain Social Icons Sesuai Gambar Rujukan -->
                    <div class="d-flex gap-3 mt-4">
                        <?php if (!empty($f_fb)): ?>
                            <a href="<?php echo htmlspecialchars($f_fb); ?>" target="_blank" class="social-icon-circle"><i class="fab fa-facebook-f"></i></a>
                        <?php endif; ?>
                        <?php if (!empty($f_ig)): ?>
                            <a href="<?php echo htmlspecialchars($f_ig); ?>" target="_blank" class="social-icon-circle"><i class="fab fa-instagram"></i></a>
                        <?php endif; ?>
                        <?php if (!empty($f_yt)): ?>
                            <a href="<?php echo htmlspecialchars($f_yt); ?>" target="_blank" class="social-icon-circle"><i class="fab fa-youtube"></i></a>
                        <?php endif; ?>
                        <?php if (!empty($f_wa)): ?>
                            <a href="<?php echo htmlspecialchars($f_wa); ?>" target="_blank" class="social-icon-circle"><i class="fab fa-whatsapp"></i></a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Link Cepat -->
                <div class="col-lg-3 col-md-6">
                    <h5 class="fw-bold mb-3 pb-2 text-white" style="border-bottom: 2px solid #334155; width: fit-content;">Navigasi</h5>
                    <ul class="list-unstyled footer-links">
                        <li><a href="index.php"><i class="fas fa-chevron-right me-2" style="color: #00a86b; font-size:0.7rem;"></i> Beranda Utama</a></li>
                        <li><a href="index.php#profil"><i class="fas fa-chevron-right me-2" style="color: #00a86b; font-size:0.7rem;"></i> Profil & Visi Misi</a></li>
                        <li><a href="guru.php"><i class="fas fa-chevron-right me-2" style="color: #00a86b; font-size:0.7rem;"></i> Direktori Asatidz</a></li>
                        <li><a href="prestasi.php"><i class="fas fa-chevron-right me-2" style="color: #00a86b; font-size:0.7rem;"></i> Prestasi Santri</a></li>
                    </ul>
                </div>

                <!-- Desain Sekretariat Sesuai Gambar Rujukan -->
                <div class="col-lg-5 col-md-12">
                    <h4 class="fw-bold mb-3 pb-2" style="color: #fbbf24; border-bottom: 2px solid #00a86b; width: fit-content; font-size: 1.3rem;">Sekretariat</h4>
                    
                    <div class="d-flex align-items-start gap-3 mb-3">
                        <i class="fas fa-map-marker-alt mt-1" style="color: #00a86b; font-size: 1.1rem;"></i>
                        <p class="mb-0" style="color: #cbd5e1; font-size: 0.95rem; line-height: 1.6;">
                            <?php echo htmlspecialchars($f_alamat); ?>
                        </p>
                    </div>
                    
                    <div class="d-flex align-items-start gap-3 mb-3">
                        <i class="fas fa-phone-alt mt-1" style="color: #00a86b; font-size: 1.1rem;"></i>
                        <p class="mb-0" style="color: #cbd5e1; font-size: 0.95rem;">
                            <?php echo htmlspecialchars($f_telp); ?>
                        </p>
                    </div>
                    
                    <div class="d-flex align-items-start gap-3">
                        <i class="fas fa-envelope mt-1" style="color: #00a86b; font-size: 1.1rem;"></i>
                        <p class="mb-0" style="color: #cbd5e1; font-size: 0.95rem;">
                            <?php echo htmlspecialchars($f_email); ?>
                        </p>
                    </div>
                </div>

            </div>
            
            <hr class="my-4" style="border-color: #334155;">
            
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                    <p class="mb-0 small" style="color: #64748b;">&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($f_nama); ?>. All Rights Reserved. <br>Support by:<a href="https://dibikininweb.com"> Tim IT Raudlatul Muta'allimin</a> </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- CSS Khusus Footer -->
    <style>
        .social-icon-circle {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #1e293b; /* Warna bulat gelap sesuai gambar */
            color: #ffffff; /* Ikon putih */
            text-decoration: none;
            transition: var(--transition-smooth);
            font-size: 1.1rem;
        }
        .social-icon-circle:hover {
            background-color: #00a86b;
            color: #ffffff;
            transform: translateY(-3px);
        }
        .footer-links li { margin-bottom: 10px; }
        .footer-links li a {
            color: #cbd5e1;
            text-decoration: none;
            transition: var(--transition-smooth);
            font-size: 0.9rem;
        }
        .footer-links li a:hover { color: #00a86b; padding-left: 5px; }
    </style>

    <!-- JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>