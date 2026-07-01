<?php
/**
 * File: navbar.php
 * Deskripsi: Bagian header dan navigasi utama website.
 * Ditambahkan query dinamis untuk menarik Logo dan Nama Pesantren dari database.
 */

// Memastikan koneksi tersedia
if (!isset($conn)) {
    include 'koneksi.php';
}

// Mengambil data profil untuk Navbar
$query_nav = mysqli_query($conn, "SELECT nama_pesantren, logo FROM profil_web WHERE id_profil = 1");
$profil_nav = mysqli_fetch_assoc($query_nav);

$nama_web = $profil_nav['nama_pesantren'] ?? "Raudlatul Muta'allimin";
$logo_web = $profil_nav['logo'] ?? "";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($nama_web); ?></title>
    
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons CDN -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- SweetAlert2 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-bootstrap-4/bootstrap-4.css" rel="stylesheet">

    <style>
        :root {
            --kemenag-green-primary: #00a86b;
            --kemenag-green-dark: #007d4f;
            --kemenag-green-light: #f0faf5;
            --dark-neutral: #0f172a;
            --transition-smooth: all 0.3s ease;
        }

        body { font-family: 'Poppins', sans-serif; overflow-x: hidden; }

        .navbar-custom {
            background-color: #ffffff;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
            padding: 12px 0;
        }

        .navbar-brand-wrapper {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        /* Styling Dinamis Logo */
        .brand-logo-img {
            width: 48px;
            height: 48px;
            object-fit: contain;
        }

        .brand-logo-placeholder {
            width: 48px;
            height: 48px;
            background-color: var(--kemenag-green-light);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--kemenag-green-primary);
            font-size: 1.4rem;
        }

        .brand-text h1 {
            font-size: 1.1rem;
            font-weight: 800;
            color: var(--dark-neutral);
            margin: 0;
            line-height: 1.2;
            letter-spacing: -0.5px;
        }

        .brand-text span {
            font-size: 0.75rem;
            color: #64748b;
            display: block;
            font-weight: 500;
        }

        .nav-link-custom {
            color: var(--dark-neutral) !important;
            font-weight: 600;
            font-size: 0.9rem;
            padding: 8px 16px !important;
            border-radius: 8px;
            transition: var(--transition-smooth);
        }

        .nav-link-custom:hover, .nav-link-custom.active {
            color: var(--kemenag-green-primary) !important;
            background-color: var(--kemenag-green-light);
        }

        .btn-register {
            background-color: var(--kemenag-green-primary);
            color: white !important;
            font-weight: 600;
            padding: 10px 24px !important;
            border-radius: 50px;
            font-size: 0.9rem;
            transition: var(--transition-smooth);
        }

        .btn-register:hover {
            background-color: var(--kemenag-green-dark);
            transform: translateY(-2px);
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg sticky-top navbar-custom">
        <div class="container">
            <a class="navbar-brand text-decoration-none" href="index.php">
                <div class="navbar-brand-wrapper">
                    <?php if (!empty($logo_web)): ?>
                        <img src="<?php echo htmlspecialchars($logo_web); ?>" alt="Logo" class="brand-logo-img">
                    <?php else: ?>
                        <div class="brand-logo-placeholder"><i class="fas fa-mosque"></i></div>
                    <?php endif; ?>
                    
                    <div class="brand-text">
                        <h1><?php echo strtoupper(htmlspecialchars($nama_web)); ?></h1>
                        <span>Yayasan Pondok Pesantren</span>
                    </div>
                </div>
            </a>

            <button class="navbar-toggler border-0 p-2" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                <span class="fas fa-bars fs-4 text-dark"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav ms-auto align-items-lg-center">
                    <li class="nav-item"><a class="nav-link nav-link-custom" href="index.php">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link nav-link-custom" href="index.php#profil">Visi Misi</a></li>
                    <li class="nav-item"><a class="nav-link nav-link-custom" href="index.php#lembaga">Lembaga</a></li>
                    <li class="nav-item"><a class="nav-link nav-link-custom" href="guru.php">Asatidz</a></li>
                    <li class="nav-item"><a class="nav-link nav-link-custom" href="berita.php">Berita</a></li>
                    <li class="nav-item"><a class="nav-link nav-link-custom" href="galeri.php">Galeri</a></li>
                    <li class="nav-item"><a class="nav-link nav-link-custom me-2" href="index.php#kontak">Kontak</a></li>
                    <li class="nav-item">
                        <a class="nav-link btn-register" href="index.php#kontak"><i class="fas fa-user-plus me-1"></i> Pendaftaran</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>