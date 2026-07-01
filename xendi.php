<?php
// Nantinya, kode koneksi database dan pengambilan data (SELECT) akan diletakkan di sini.
// Contoh: include 'koneksi.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pondok Pesantren Raudlatul Muta'allimin</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            /* Palet Warna Modern & Elegan */
            --primary: #056a38;      /* Hijau Kemenag Elegan */
            --primary-dark: #024724; /* Hijau Gelap */
            --primary-light: #169d53;/* Hijau Terang/Aksen */
            --accent: #d4af37;       /* Emas Premium */
            --accent-hover: #b5952f;
            --bg-light: #f8fafc;     /* Abu-abu sangat muda kebiruan (Slate) */
            --text-main: #334155;    /* Teks utama (Slate 700) */
            --text-muted: #64748b;   /* Teks sekunder (Slate 500) */
            --border-color: #e2e8f0;
        }

        body {
            font-family: 'Inter', sans-serif;
            color: var(--text-main);
            background-color: #ffffff;
            -webkit-font-smoothing: antialiased;
        }

        /* Typography */
        h1, h2, h3, h4, h5, h6 {
            font-weight: 700;
            color: var(--primary-dark);
        }
        p {
            line-height: 1.7;
            color: var(--text-muted);
        }

        /* Navbar Modern */
        .navbar-custom {
            background-color: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            transition: all 0.3s ease;
            padding: 15px 0;
        }
        .navbar-custom .navbar-brand {
            color: var(--primary-dark) !important;
            font-weight: 800;
            font-size: 1.25rem;
        }
        .navbar-custom .navbar-brand i {
            color: var(--accent);
        }
        .navbar-custom .nav-link {
            color: var(--text-main) !important;
            font-weight: 500;
            font-size: 0.95rem;
            margin: 0 5px;
            transition: color 0.3s ease;
        }
        .navbar-custom .nav-link:hover,
        .navbar-custom .nav-link.active {
            color: var(--primary) !important;
        }
        .navbar-toggler {
            border: none;
            color: var(--primary-dark);
        }
        .navbar-toggler:focus {
            box-shadow: none;
        }

        /* Buttons */
        .btn-primary-custom {
            background-color: var(--primary);
            color: white;
            border: none;
            padding: 12px 30px;
            font-weight: 600;
            border-radius: 50px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(5, 106, 56, 0.2);
        }
        .btn-primary-custom:hover {
            background-color: var(--primary-dark);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(5, 106, 56, 0.3);
        }
        .btn-accent-custom {
            background-color: var(--accent);
            color: white;
            border: none;
            padding: 12px 30px;
            font-weight: 600;
            border-radius: 50px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(212, 175, 55, 0.2);
        }
        .btn-accent-custom:hover {
            background-color: var(--accent-hover);
            color: white;
            transform: translateY(-2px);
        }

        /* Hero Section */
        .hero-section {
            position: relative;
            background: url('https://images.unsplash.com/photo-1542816417-0983c9c9ad53?auto=format&fit=crop&q=80&w=1920') center/cover no-repeat;
            min-height: 90vh;
            display: flex;
            align-items: center;
            border-bottom-left-radius: 40px;
            border-bottom-right-radius: 40px;
            margin-bottom: 40px;
            overflow: hidden;
        }
        .hero-overlay {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(135deg, rgba(2, 71, 36, 0.9) 0%, rgba(5, 106, 56, 0.75) 100%);
            z-index: 1;
        }
        .hero-content {
            position: relative;
            z-index: 2;
            text-align: center;
            color: white;
            padding-top: 60px;
        }
        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 20px;
            color: white;
            line-height: 1.2;
        }
        .hero-subtitle {
            font-size: 1.25rem;
            font-weight: 400;
            margin-bottom: 40px;
            color: rgba(255, 255, 255, 0.9);
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Section Titles */
        .section-header {
            margin-bottom: 60px;
            text-align: center;
        }
        .section-title {
            font-size: 2.25rem;
            font-weight: 800;
            color: var(--primary-dark);
            margin-bottom: 15px;
        }
        .section-subtitle {
            color: var(--accent);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-size: 0.85rem;
            display: block;
            margin-bottom: 10px;
        }

        /* Modern Cards */
        .card-modern {
            background: #ffffff;
            border: 1px solid rgba(0,0,0,0.04);
            border-radius: 24px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
            transition: all 0.4s ease;
            height: 100%;
            overflow: hidden;
        }
        .card-modern:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
        }

        /* Pendidikan Section */
        .edu-icon-wrapper {
            width: 80px;
            height: 80px;
            background: rgba(22, 157, 83, 0.1);
            color: var(--primary);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 2rem;
            transition: all 0.3s ease;
        }
        .card-modern:hover .edu-icon-wrapper {
            background: var(--primary);
            color: white;
        }

        /* Profil Pengurus Card */
        .profile-card {
            text-align: center;
            padding: 40px 20px;
        }
        .profile-img-wrapper {
            position: relative;
            width: 140px;
            height: 140px;
            margin: 0 auto 25px;
        }
        .profile-img-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
            border: 4px solid white;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .profile-role {
            background: var(--accent);
            color: white;
            font-size: 0.8rem;
            font-weight: 600;
            padding: 5px 15px;
            border-radius: 20px;
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            white-space: nowrap;
        }

        /* Berita & Galeri */
        .news-img {
            height: 220px;
            object-fit: cover;
        }
        .news-date {
            font-size: 0.85rem;
            color: var(--primary-light);
            font-weight: 600;
        }
        .news-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--primary-dark);
            margin: 10px 0;
            line-height: 1.4;
        }
        .gallery-img {
            border-radius: 16px;
            transition: transform 0.3s ease;
            cursor: pointer;
        }
        .gallery-img:hover {
            transform: scale(1.03);
        }

        /* Testimonials */
        .testimonial-section {
            background-color: var(--bg-light);
            border-radius: 40px;
            padding: 80px 0;
            margin: 40px 0;
        }
        .testi-card {
            background: white;
            padding: 40px;
            border-radius: 24px;
            position: relative;
        }
        .testi-quote-icon {
            position: absolute;
            top: -20px;
            right: 30px;
            font-size: 3rem;
            color: rgba(212, 175, 55, 0.2);
        }

        /* Footer */
        .footer-modern {
            background-color: var(--primary-dark);
            color: white;
            padding: 80px 0 30px;
            border-top-left-radius: 40px;
            border-top-right-radius: 40px;
            margin-top: 60px;
        }
        .footer-modern h5 {
            color: white;
            font-weight: 700;
            margin-bottom: 25px;
        }
        .footer-modern p {
            color: rgba(255,255,255,0.7);
        }
        .social-links a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,0.1);
            color: white;
            border-radius: 50%;
            margin-right: 10px;
            transition: all 0.3s ease;
            text-decoration: none;
        }
        .social-links a:hover {
            background: var(--accent);
            transform: translateY(-3px);
        }
        .footer-bottom {
            border-top: 1px solid rgba(255,255,255,0.1);
            padding-top: 25px;
            margin-top: 40px;
            text-align: center;
            color: rgba(255,255,255,0.6);
            font-size: 0.9rem;
        }

        /* Form Controls */
        .form-control {
            border-radius: 12px;
            padding: 15px;
            border: 1px solid var(--border-color);
            background-color: var(--bg-light);
            transition: all 0.3s ease;
        }
        .form-control:focus {
            box-shadow: 0 0 0 4px rgba(22, 157, 83, 0.1);
            border-color: var(--primary-light);
            background-color: #fff;
        }

        /* Responsive */
        @media (max-width: 991px) {
            .hero-title { font-size: 2.5rem; }
            .hero-section { min-height: 70vh; }
            .navbar-custom { padding: 10px 0; }
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom sticky-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-mosque me-2"></i> Raudlatul Muta'allimin
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <i class="fas fa-bars fs-4"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link active" href="#beranda">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link" href="#profil">Profil</a></li>
                    <li class="nav-item"><a class="nav-link" href="#pendidikan">Pendidikan</a></li>
                    <li class="nav-item"><a class="nav-link" href="#sambutan">Struktur</a></li>
                    <li class="nav-item"><a class="nav-link" href="#berita">Berita & Galeri</a></li>
                    <li class="nav-item ms-lg-3 mt-3 mt-lg-0">
                        <a class="btn btn-primary-custom px-4" href="#kontak">Hubungi Kami</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="beranda" class="hero-section">
        <div class="hero-overlay"></div>
        <div class="container hero-content">
            <div class="row justify-content-center">
                <div class="col-lg-9">
                    <span class="badge bg-warning text-dark mb-3 px-3 py-2 rounded-pill fw-bold">Penerimaan Santri Baru Dibuka!</span>
                    <h1 class="hero-title">Membangun Generasi Islami yang Beradab & Berprestasi</h1>
                    <p class="hero-subtitle">Pondok Pesantren Raudlatul Muta'allimin Way Kanan, Lampung. Mendidik dari usia dini (RA) hingga Sekolah Menengah Kejuruan (SMK).</p>
                    <div class="d-flex justify-content-center gap-3 flex-wrap">
                        <a href="#profil" class="btn btn-accent-custom">Kenali Kami Lebih Jauh</a>
                        <a href="#pendidikan" class="btn btn-outline-light px-4" style="border-radius: 50px; font-weight: 600; padding: 12px 30px;">Program Pendidikan</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Profil & Visi Misi -->
    <section id="profil" class="py-5">
        <div class="container">
            <div class="section-header">
                <span class="section-subtitle">Tentang Kami</span>
                <h2 class="section-title">Visi & Misi Pesantren</h2>
            </div>
            <div class="row g-5 align-items-center">
                <div class="col-lg-6">
                    <div class="position-relative">
                        <img src="https://images.unsplash.com/photo-1590212002162-87850a582b13?auto=format&fit=crop&q=80&w=800" alt="Pesantren" class="img-fluid" style="border-radius: 30px; box-shadow: 0 20px 40px rgba(0,0,0,0.1);">
                        <!-- Aksen kotak dekoratif -->
                        <div class="position-absolute" style="width: 100%; height: 100%; border: 3px solid var(--accent); border-radius: 30px; top: -20px; left: -20px; z-index: -1;"></div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mb-5">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-success bg-opacity-10 p-3 rounded-circle text-success me-3">
                                <i class="fas fa-eye fa-xl"></i>
                            </div>
                            <h3 class="mb-0">Visi Kami</h3>
                        </div>
                        <p class="lead fw-normal">Mencetak generasi Islami yang berakhlakul karimah, mandiri, dan berprestasi dalam ilmu agama maupun ilmu umum.</p>
                    </div>
                    
                    <div>
                        <div class="d-flex align-items-center mb-4">
                            <div class="bg-warning bg-opacity-10 p-3 rounded-circle text-warning me-3">
                                <i class="fas fa-bullseye fa-xl"></i>
                            </div>
                            <h3 class="mb-0">Misi Kami</h3>
                        </div>
                        <ul class="list-unstyled">
                            <li class="d-flex mb-3">
                                <i class="fas fa-check-circle text-success mt-1 me-3 fs-5"></i> 
                                <span>Menyelenggarakan pendidikan agama dan umum yang berkualitas sesuai tuntutan zaman.</span>
                            </li>
                            <li class="d-flex mb-3">
                                <i class="fas fa-check-circle text-success mt-1 me-3 fs-5"></i> 
                                <span>Membentuk karakter santri yang tangguh, mandiri, dan beradab.</span>
                            </li>
                            <li class="d-flex mb-3">
                                <i class="fas fa-check-circle text-success mt-1 me-3 fs-5"></i> 
                                <span>Mengembangkan keterampilan santri dalam menghadapi era digital dan dunia kerja.</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Lembaga Pendidikan -->
    <section id="pendidikan" class="py-5 bg-light" style="border-radius: 40px; margin: 0 15px;">
        <div class="container py-4">
            <div class="section-header">
                <span class="section-subtitle">Jenjang Pendidikan</span>
                <h2 class="section-title">Program Pendidikan Terpadu</h2>
                <p class="text-muted mx-auto" style="max-width: 600px;">Sistem pendidikan terpadu yang bernaung di bawah Yayasan Pondok Pesantren Raudlatul Muta'allimin.</p>
            </div>
            
            <div class="row g-4 justify-content-center text-center">
                <!-- Data ini nantinya statis atau dilooping -->
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="card-modern p-4">
                        <div class="edu-icon-wrapper"><i class="fas fa-child"></i></div>
                        <h5 class="fw-bold mb-0">RA / TK</h5>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="card-modern p-4">
                        <div class="edu-icon-wrapper"><i class="fas fa-book-reader"></i></div>
                        <h5 class="fw-bold mb-0">MI (SD)</h5>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="card-modern p-4">
                        <div class="edu-icon-wrapper"><i class="fas fa-mosque"></i></div>
                        <h5 class="fw-bold mb-0">MTs (SMP)</h5>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="card-modern p-4">
                        <div class="edu-icon-wrapper"><i class="fas fa-graduation-cap"></i></div>
                        <h5 class="fw-bold mb-0">MA (SMA)</h5>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="card-modern p-4">
                        <div class="edu-icon-wrapper"><i class="fas fa-laptop-code"></i></div>
                        <h5 class="fw-bold mb-0">SMK</h5>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Struktur & Sambutan (Mockup Data Dinamis PHP) -->
    <section id="sambutan" class="py-5 mt-4">
        <div class="container">
            <div class="section-header">
                <span class="section-subtitle">Struktur Organisasi</span>
                <h2 class="section-title">Pimpinan & Pengasuh Pondok</h2>
            </div>
            <div class="row g-4 justify-content-center">
                
                <!-- Card 1 -->
                <div class="col-md-6 col-lg-4">
                    <div class="card-modern profile-card h-100">
                        <div class="profile-img-wrapper">
                            <img src="https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?auto=format&fit=crop&q=80&w=200" alt="KH. Marsudi">
                            <span class="profile-role">Pengasuh Pondok</span>
                        </div>
                        <h4>KH. Marsudi</h4>
                        <hr class="w-25 mx-auto my-3" style="border-color: var(--accent); opacity: 1; border-width: 2px;">
                        <p class="fst-italic">"Ahlan Wa Sahlan di website resmi Pondok Pesantren Raudlatul Muta'allimin. Semoga media ini menjadi jembatan silaturahmi kita semua."</p>
                    </div>
                </div>
                
                <!-- Card 2 -->
                <div class="col-md-6 col-lg-4">
                    <div class="card-modern profile-card h-100">
                        <div class="profile-img-wrapper">
                            <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?auto=format&fit=crop&q=80&w=200" alt="Ust. Sudi">
                            <span class="profile-role">Ketua Yayasan</span>
                        </div>
                        <h4>Ust. Sudi. S.Pd.I</h4>
                        <hr class="w-25 mx-auto my-3" style="border-color: var(--accent); opacity: 1; border-width: 2px;">
                        <p class="fst-italic">"Pendidikan adalah kunci peradaban. Kami berkomitmen terus memfasilitasi santri dengan pendidikan terbaik dari usia dini hingga kejuruan."</p>
                    </div>
                </div>

                <!-- Card 3 -->
                <div class="col-md-6 col-lg-4">
                    <div class="card-modern profile-card h-100">
                        <div class="profile-img-wrapper">
                            <img src="https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?auto=format&fit=crop&q=80&w=200" alt="Ust. Oktawidodo">
                            <span class="profile-role">DPP (Dewan Pembina)</span>
                        </div>
                        <h4>Ust. Oktawidodo, S.Pd.I</h4>
                        <hr class="w-25 mx-auto my-3" style="border-color: var(--accent); opacity: 1; border-width: 2px;">
                        <p class="fst-italic">"Kami terus membina karakter santri agar tidak hanya cerdas secara intelektual, namun juga matang secara spiritual dan emosional."</p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Berita & Galeri -->
    <section id="berita" class="py-5">
        <div class="container">
            <div class="row g-5">
                <!-- Kolom Berita -->
                <div class="col-lg-8">
                    <div class="d-flex justify-content-between align-items-end mb-4">
                        <div>
                            <span class="section-subtitle mb-1">Informasi Terkini</span>
                            <h3 class="fw-bold text-dark mb-0">Berita Pesantren</h3>
                        </div>
                        <a href="#" class="text-decoration-none fw-bold" style="color: var(--primary);">Lihat Semua <i class="fas fa-arrow-right ms-1"></i></a>
                    </div>
                    
                    <div class="row g-4">
                        <!-- Mockup Berita 1 -->
                        <div class="col-md-6">
                            <div class="card-modern h-100">
                                <img src="https://images.unsplash.com/photo-1577896851231-70ef18881754?auto=format&fit=crop&q=80&w=600" class="w-100 news-img" alt="Berita 1">
                                <div class="p-4">
                                    <div class="news-date"><i class="far fa-calendar-alt me-2"></i>12 Oktober 2023</div>
                                    <h4 class="news-title">Penerimaan Santri Baru Tahun Ajaran 2024/2025 Telah Dibuka</h4>
                                    <p class="text-muted mb-4" style="font-size: 0.95rem;">Pendaftaran santri baru untuk jenjang RA hingga SMK telah resmi dibuka. Segera daftarkan putra-putri Anda...</p>
                                    <a href="#" class="text-decoration-none fw-bold" style="color: var(--primary);">Baca Selengkapnya &rarr;</a>
                                </div>
                            </div>
                        </div>
                        <!-- Mockup Berita 2 -->
                        <div class="col-md-6">
                            <div class="card-modern h-100">
                                <img src="https://images.unsplash.com/photo-1601055903647-8f1af67451e5?auto=format&fit=crop&q=80&w=600" class="w-100 news-img" alt="Berita 2">
                                <div class="p-4">
                                    <div class="news-date"><i class="far fa-calendar-alt me-2"></i>05 Oktober 2023</div>
                                    <h4 class="news-title">Alhamdulillah, Santri MTs Juara 1 Lomba Tahfidz Tingkat Kabupaten</h4>
                                    <p class="text-muted mb-4" style="font-size: 0.95rem;">Prestasi membanggakan kembali diraih oleh ananda Fulan, santri kelas IX MTs Raudlatul Muta'allimin...</p>
                                    <a href="#" class="text-decoration-none fw-bold" style="color: var(--primary);">Baca Selengkapnya &rarr;</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kolom Galeri -->
                <div class="col-lg-4">
                    <div class="mb-4">
                        <span class="section-subtitle mb-1">Dokumentasi</span>
                        <h3 class="fw-bold text-dark mb-0">Galeri Kegiatan</h3>
                    </div>
                    <div class="row g-3">
                        <div class="col-6">
                            <img src="https://images.unsplash.com/photo-1584485590747-cf4f0283f5fb?auto=format&fit=crop&q=80&w=300" class="img-fluid gallery-img shadow-sm" alt="Galeri 1">
                        </div>
                        <div class="col-6">
                            <img src="https://images.unsplash.com/photo-1511649475669-e288648b2339?auto=format&fit=crop&q=80&w=300" class="img-fluid gallery-img shadow-sm" alt="Galeri 2">
                        </div>
                        <div class="col-6">
                            <img src="https://images.unsplash.com/photo-1577563908411-50cb989766a3?auto=format&fit=crop&q=80&w=300" class="img-fluid gallery-img shadow-sm" alt="Galeri 3">
                        </div>
                        <div class="col-6">
                            <img src="https://images.unsplash.com/photo-1580582932707-520aed937b7b?auto=format&fit=crop&q=80&w=300" class="img-fluid gallery-img shadow-sm" alt="Galeri 4">
                        </div>
                    </div>
                    <a href="#" class="btn btn-outline-success w-100 mt-4 rounded-pill fw-bold">Lihat Semua Dokumentasi</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimoni Alumni (PHP Dinamis nantinya) -->
    <section class="testimonial-section container-fluid px-4">
        <div class="container">
            <div class="section-header">
                <span class="section-subtitle">Kisah Sukses</span>
                <h2 class="section-title">Apa Kata Alumni Kami?</h2>
            </div>
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="testi-card h-100 card-modern">
                        <i class="fas fa-quote-right testi-quote-icon"></i>
                        <p class="fs-5 fst-italic mb-4 mt-2">"Pondok ini memberikan saya dasar agama yang sangat kuat. Kedisiplinan yang diajarkan sangat berguna bagi saya beradaptasi di dunia perkuliahan saat ini."</p>
                        <div class="d-flex align-items-center">
                            <img src="https://images.unsplash.com/photo-1599566150163-29194dcaad36?auto=format&fit=crop&q=80&w=100" class="rounded-circle me-3" style="width:60px; height:60px; object-fit:cover;" alt="Alumni 1">
                            <div>
                                <h6 class="mb-0 fw-bold fs-5 text-dark">Ahmad Fulan</h6>
                                <span class="text-muted small">Mahasiswa UIN, Alumni MA 2020</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="testi-card h-100 card-modern">
                        <i class="fas fa-quote-right testi-quote-icon"></i>
                        <p class="fs-5 fst-italic mb-4 mt-2">"Lulus dari SMK di pondok ini, saya tidak hanya memiliki sertifikat keahlian, tapi juga bekal adab. Alhamdulillah saya langsung diterima bekerja di perusahaan swasta."</p>
                        <div class="d-flex align-items-center">
                            <img src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?auto=format&fit=crop&q=80&w=100" class="rounded-circle me-3" style="width:60px; height:60px; object-fit:cover;" alt="Alumni 2">
                            <div>
                                <h6 class="mb-0 fw-bold fs-5 text-dark">Siti Aminah</h6>
                                <span class="text-muted small">Karyawan Swasta, Alumni SMK 2021</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section Kontak -->
    <section id="kontak" class="py-5 mb-5">
        <div class="container">
            <div class="card-modern p-0 overflow-hidden">
                <div class="row g-0">
                    <div class="col-lg-5 text-white p-5" style="background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 100%);">
                        <h3 class="text-white fw-bold mb-4">Informasi Kontak</h3>
                        <p class="mb-5 text-white-50">Silakan hubungi kami untuk informasi pendaftaran, program donasi, atau pertanyaan lainnya.</p>
                        
                        <div class="d-flex mb-4">
                            <i class="fas fa-map-marker-alt fs-4 text-warning me-3 mt-1"></i>
                            <div>
                                <h6 class="text-white fw-bold mb-1">Alamat Pesantren</h6>
                                <p class="mb-0 text-white-50">Jl. Dr. Ak. Gani, No.50, Jaya Tinggi,<br>Kasui, Way Kanan, Lampung</p>
                            </div>
                        </div>
                        <div class="d-flex mb-4">
                            <i class="fas fa-phone-alt fs-4 text-warning me-3 mt-1"></i>
                            <div>
                                <h6 class="text-white fw-bold mb-1">Telepon / WhatsApp</h6>
                                <p class="mb-0 text-white-50">0812-3456-7890</p>
                            </div>
                        </div>
                        <div class="d-flex mb-5">
                            <i class="fas fa-envelope fs-4 text-warning me-3 mt-1"></i>
                            <div>
                                <h6 class="text-white fw-bold mb-1">Email Resmi</h6>
                                <p class="mb-0 text-white-50">info@raudlatulmutaallimin.sch.id</p>
                            </div>
                        </div>
                        
                        <h6 class="text-white fw-bold mb-3">Media Sosial Kami</h6>
                        <div class="social-links">
                            <a href="#"><i class="fab fa-facebook-f"></i></a>
                            <a href="#"><i class="fab fa-instagram"></i></a>
                            <a href="#"><i class="fab fa-youtube"></i></a>
                        </div>
                    </div>
                    
                    <div class="col-lg-7 p-5">
                        <h3 class="fw-bold mb-4 text-dark">Kirim Pesan ke Pengurus</h3>
                        <form id="formKontak" method="POST" action="">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-muted small">Nama Lengkap</label>
                                    <input type="text" class="form-control" id="nama" placeholder="Masukkan nama Anda" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-muted small">Alamat Email</label>
                                    <input type="email" class="form-control" id="email" placeholder="email@contoh.com" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold text-muted small">Subjek Pesan</label>
                                    <input type="text" class="form-control" id="subjek" placeholder="Contoh: Info Pendaftaran" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold text-muted small">Isi Pesan</label>
                                    <textarea class="form-control" id="pesan" rows="5" placeholder="Tuliskan pertanyaan atau pesan Anda di sini..." required></textarea>
                                </div>
                                <div class="col-12 mt-4">
                                    <button type="submit" class="btn btn-primary-custom w-100 py-3">Kirim Pesan <i class="fas fa-paper-plane ms-2"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer Modern -->
    <footer class="footer-modern">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4 pe-lg-5">
                    <div class="d-flex align-items-center mb-4">
                        <i class="fas fa-mosque fa-2x text-warning me-2"></i>
                        <h4 class="text-white mb-0 fw-bold">Raudlatul Muta'allimin</h4>
                    </div>
                    <p>Mencetak generasi Islami yang berakhlakul karimah, mandiri, dan berprestasi. Berdedikasi untuk umat dan bangsa melalui pendidikan berkualitas di Way Kanan.</p>
                </div>
                <div class="col-lg-2 col-md-6">
                    <h5>Tautan Cepat</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#beranda" class="text-white-50 text-decoration-none hover-white">Beranda</a></li>
                        <li class="mb-2"><a href="#profil" class="text-white-50 text-decoration-none hover-white">Profil Pondok</a></li>
                        <li class="mb-2"><a href="#pendidikan" class="text-white-50 text-decoration-none hover-white">Pendidikan</a></li>
                        <li class="mb-2"><a href="#berita" class="text-white-50 text-decoration-none hover-white">Berita Terbaru</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h5>Lembaga Kami</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><span class="text-white-50"><i class="fas fa-chevron-right text-warning me-2" style="font-size:10px;"></i> RA / TK</span></li>
                        <li class="mb-2"><span class="text-white-50"><i class="fas fa-chevron-right text-warning me-2" style="font-size:10px;"></i> Madrasah Ibtidaiyah (MI)</span></li>
                        <li class="mb-2"><span class="text-white-50"><i class="fas fa-chevron-right text-warning me-2" style="font-size:10px;"></i> Madrasah Tsanawiyah (MTs)</span></li>
                        <li class="mb-2"><span class="text-white-50"><i class="fas fa-chevron-right text-warning me-2" style="font-size:10px;"></i> Madrasah Aliyah (MA)</span></li>
                        <li class="mb-2"><span class="text-white-50"><i class="fas fa-chevron-right text-warning me-2" style="font-size:10px;"></i> Sekolah Menengah Kejuruan (SMK)</span></li>
                    </ul>
                </div>
                <div class="col-lg-3">
                    <h5>Jam Operasional Kantor</h5>
                    <ul class="list-unstyled text-white-50">
                        <li class="mb-2">Senin - Kamis: 08.00 - 15.00 WIB</li>
                        <li class="mb-2">Jumat: Tutup (Libur Pondok)</li>
                        <li class="mb-2">Sabtu - Minggu: 08.00 - 14.00 WIB</li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p class="mb-0">&copy; <?php echo date('Y'); ?> Yayasan PP Raudlatul Muta'allimin. All Rights Reserved. <br>
                <span class="small">Dikembangkan dengan <i class="fas fa-heart text-danger"></i> untuk Way Kanan, Lampung.</span></p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Navbar effect on scroll
            window.addEventListener('scroll', function() {
                const navbar = document.querySelector('.navbar-custom');
                if (window.scrollY > 50) {
                    navbar.style.padding = '10px 0';
                    navbar.style.boxShadow = '0 10px 30px rgba(0,0,0,0.08)';
                } else {
                    navbar.style.padding = '15px 0';
                    navbar.style.boxShadow = '0 4px 20px rgba(0, 0, 0, 0.03)';
                }
            });

            // Smooth scrolling for navigation links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const targetId = this.getAttribute('href');
                    const targetElement = document.querySelector(targetId);
                    
                    if(targetElement) {
                        const headerOffset = 80; 
                        const elementPosition = targetElement.getBoundingClientRect().top;
                        const offsetPosition = elementPosition + window.pageYOffset - headerOffset;
        
                        window.scrollTo({
                            top: offsetPosition,
                            behavior: "smooth"
                        });
                    }
                });
            });

            // Form Submit Interception (SweetAlert2)
            const formKontak = document.getElementById('formKontak');
            if(formKontak) {
                formKontak.addEventListener('submit', function(e) {
                    e.preventDefault(); // Nanti ini diganti dengan pemrosesan form PHP/AJAX asli
                    
                    const nama = document.getElementById('nama').value;

                    Swal.fire({
                        title: 'Alhamdulillah!',
                        text: `Terima kasih ${nama}, pesan Anda akan segera dibaca oleh admin pondok.`,
                        icon: 'success',
                        confirmButtonColor: '#056a38', 
                        confirmButtonText: 'Tutup',
                        customClass: {
                            confirmButton: 'btn btn-primary-custom',
                            popup: 'rounded-4'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            formKontak.reset();
                        }
                    });
                });
            }
        });
    </script>
</body>
</html>