<?php
/**
 * File: tentang.php
 * Deskripsi: Halaman front-end untuk menampilkan profil sekilas dan sejarah lengkap pesantren.
 */

session_start();
include 'koneksi.php';

// Mengambil Data Profil & Sejarah
$query_profil = mysqli_query($conn, "SELECT * FROM profil_web WHERE id_profil = 1 LIMIT 1");
$profil = mysqli_fetch_assoc($query_profil);

// Fallback jika kosong
$nama_pesantren = $profil['nama_pesantren'] ?? "Pondok Pesantren Raudlatul Muta'allimin";
$tentang_pondok = $profil['tentang_pondok'] ?? "<p>Belum ada informasi profil singkat yang ditambahkan.</p>";
$sejarah_pondok = $profil['sejarah_pondok'] ?? "<p>Belum ada informasi sejarah yang ditambahkan.</p>";
$foto_tentang   = $profil['foto_tentang'] ?? "https://images.unsplash.com/photo-1584697964400-2af6a2f62651?q=80&w=1200&h=600&fit=crop";

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang & Sejarah - <?php echo htmlspecialchars($nama_pesantren); ?></title>
    
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
            --text-muted-custom: #475569;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--light-neutral);
            color: var(--dark-neutral);
        }

        /* Top Header Navigation Adjustments (From index) */
        .top-bar {
            background-color: var(--kemenag-green-dark) !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .top-bar span, .top-bar a, .top-bar i {
            color: #ffffff !important;
        }

        /* Page Header Banner */
        .page-header {
            background: linear-gradient(135deg, var(--kemenag-green-primary) 0%, var(--kemenag-green-dark) 100%);
            padding: 120px 0 80px 0; /* Padding disesuaikan agar proporsional */
            color: white;
            text-align: center;
            position: relative;
        }

        /* Content Card */
        .content-card {
            background-color: #ffffff;
            border-radius: 24px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.03);
            border: none;
            overflow: hidden;
            margin-top: -40px; /* Overlap yang rapi di atas header lurus */
            position: relative;
            z-index: 10;
        }

        .hero-img {
            width: 100%;
            height: 400px;
            object-fit: cover;
        }

        /* Style for HTML Content from Summernote */
        .html-content {
            color: var(--text-muted-custom);
            line-height: 1.8;
            font-size: 1.05rem;
        }
        
        .html-content p {
            margin-bottom: 1.5rem;
        }

        .html-content h1, .html-content h2, .html-content h3, .html-content h4 {
            color: var(--dark-neutral);
            font-weight: 700;
            margin-top: 2rem;
            margin-bottom: 1rem;
        }

        .html-content ul, .html-content ol {
            margin-bottom: 1.5rem;
            padding-left: 2rem;
        }

        .html-content li {
            margin-bottom: 0.5rem;
        }

        .section-title {
            font-weight: 800;
            color: var(--dark-neutral);
            letter-spacing: -0.5px;
            font-size: 2rem;
            position: relative;
            margin-bottom: 1.5rem;
            padding-bottom: 10px;
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 4px;
            background-color: var(--kemenag-green-primary);
            border-radius: 2px;
        }

    </style>
</head>
<body>

    <!-- Navbar -->
    <?php include 'navbar.php'; ?>

    <!-- Header Halaman -->
    <header class="page-header">
        <div class="container position-relative" style="z-index: 2;">
            <div class="d-inline-flex mb-3">
                <span class="badge bg-white text-success rounded-pill px-3 py-2 fw-bold shadow-sm">
                    <i class="fas fa-info-circle me-1"></i> Profil Lembaga
                </span>
            </div>
            <h1 class="display-5 fw-bold mb-3">Tentang & Sejarah Kami</h1>
            <p class="lead opacity-75 mb-0 mx-auto" style="max-width: 600px;">
                Mengenal lebih dekat visi, sejarah, dan nilai-nilai luhur yang ditanamkan di <?php echo htmlspecialchars($nama_pesantren); ?>.
            </p>
        </div>
    </header>

    <!-- Main Content -->
    <section class="py-5" style="background-color: var(--light-neutral);">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="content-card">
                        <!-- Gambar Cover (Dari foto tentang) -->
                        <img src="<?php echo htmlspecialchars($foto_tentang); ?>" class="hero-img" alt="Cover Tentang Kami">
                        
                        <div class="p-4 p-md-5">
                            
                            <!-- Bagian 1: Sekilas Tentang Pondok -->
                            <div class="mb-5">
                                <h2 class="section-title">Sekilas Profil</h2>
                                <div class="html-content">
                                    <!-- Echo tanpa htmlspecialchars karena data ini adalah format HTML dari Summernote -->
                                    <?php echo $tentang_pondok; ?>
                                </div>
                            </div>

                            <hr class="my-5" style="border-color: rgba(0,0,0,0.08);">

                            <!-- Bagian 2: Sejarah Lengkap -->
                            <div>
                                <h2 class="section-title">Sejarah Berdiri</h2>
                                <div class="html-content">
                                    <?php echo $sejarah_pondok; ?>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Memanggil Footer -->
    <?php include 'footer.php'; ?>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>