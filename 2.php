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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            /* Warna yang diadaptasi dari gambar referensi */
            --kemenag-green: #007c4b; /* Hijau solid utama */
            --kemenag-green-dark: #005a36; 
            --accent-yellow: #ffc107; /* Kuning terang untuk CTA */
            --accent-yellow-hover: #e0a800;
            --bg-light: #f4f7f6; /* Latar belakang abu-abu sangat muda */
            --text-dark: #2c3e50;
            --text-muted: #6c757d;
            --card-radius: 20px;
        }

        body {
            font-family: 'Inter', sans-serif;
            color: var(--text-dark);
            background-color: #ffffff;
            -webkit-font-smoothing: antialiased;
        }

        h1, h2, h3, h4, h5, h6 {
            font-weight: 700;
        }

        p {
            line-height: 1.6;
            color: var(--text-muted);
        }

        /* Navbar */
        .navbar-custom {
            background-color: #ffffff;
            padding: 15px 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        .navbar-brand {
            color: var(--kemenag-green) !important;
            font-weight: 800;
            font-size: 1.4rem;
        }
        .nav-link {
            color: var(--text-dark) !important;
            font-weight: 600;
            font-size: 1rem;
            margin: 0 10px;
            transition: color 0.3s ease;
        }
        .nav-link:hover, .nav-link.active {
            color: var(--kemenag-green) !important;
        }
        .btn-outline-green {
            color: var(--kemenag-green);
            border: 2px solid var(--kemenag-green);
            border-radius: 50px;
            font-weight: 600;
            padding: 8px 25px;
            transition: all 0.3s ease;
        }
        .btn-outline-green:hover {
            background-color: var(--kemenag-green);
            color: white;
        }

        /* Modern Hero Section */
        .hero-section {
            background-color: var(--bg-light);
            padding: 80px 0 100px;
            overflow: hidden;
        }
        .hero-title {
            color: var(--text-dark);
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 20px;
            line-height: 1.2;
            letter-spacing: -1px;
        }
        .hero-title span {
            color: var(--kemenag-green);
        }
        .hero-subtitle {
            color: var(--text-muted);
            font-size: 1.15rem;
            margin-bottom: 40px;
            line-height: 1.7;
        }
        .hero-img-box {
            position: relative;
            border-radius: 30px;
            overflow: hidden;
            box-shadow: 0 20px 50px rgba(0, 124, 75, 0.15);
        }
        .hero-img-box img {
            width: 100%;
            height: auto;
            object-fit: cover;
        }
        .badge-modern {
            background-color: rgba(0, 124, 75, 0.1);
            color: var(--kemenag-green);
            font-weight: 700;
            padding: 8px 20px;
            border-radius: 50px;
            display: inline-flex;
            align-items: center;
            margin-bottom: 25px;
            font-size: 0.9rem;
        }

        /* Tombol CTA di Hero */
        .btn-yellow {
            background-color: var(--accent-yellow);
            color: #000;
            border: none;
            border-radius: 50px;
            padding: 14px 35px;
            font-weight: 700;
            transition: all 0.3s;
            box-shadow: 0 10px 20px rgba(255, 193, 7, 0.2);
        }
        .btn-yellow:hover {
            background-color: var(--accent-yellow-hover);
            transform: translateY(-3px);
            box-shadow: 0 15px 25px rgba(255, 193, 7, 0.3);
        }
        .btn-outline-green-hero {
            background-color: transparent;
            color: var(--kemenag-green);
            border: 2px solid var(--kemenag-green);
            border-radius: 50px;
            padding: 12px 35px;
            font-weight: 700;
            transition: all 0.3s;
        }
        .btn-outline-green-hero:hover {
            background-color: var(--kemenag-green);
            color: white;
        }

        /* Umum Section */
        .section-padding {
            padding: 100px 0;
        }
        .bg-grey {
            background-color: var(--bg-light);
        }
        .section-title-center {
            text-align: center;
            margin-bottom: 50px;
        }
        .section-title-center h2 {
            color: var(--text-dark);
            font-weight: 800;
            margin-bottom: 15px;
        }
        .title-underline {
            width: 60px;
            height: 4px;
            background-color: var(--kemenag-green);
            margin: 0 auto;
            border-radius: 2px;
        }

        /* Card Style (Berdasarkan referensi "Alur Pendaftaran") */
        .clean-card {
            background: #ffffff;
            border: none;
            border-radius: var(--card-radius);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
            padding: 40px 30px;
            text-align: center;
            height: 100%;
            transition: transform 0.3s ease;
            position: relative;
        }
        .clean-card:hover {
            transform: translateY(-5px);
        }
        
        /* Ikon Lembaga Pendidikan */
        .icon-circle {
            width: 80px;
            height: 80px;
            background-color: rgba(0, 124, 75, 0.08); 
            color: var(--kemenag-green);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin: 0 auto 20px;
        }

        /* Sambutan / Struktur Card (Desain Baru dari Referensi) */
        .struktur-card {
            background: white;
            border-radius: var(--card-radius);
            box-shadow: 0 10px 30px rgba(0,0,0,0.03);
            border-left: 6px solid var(--kemenag-green);
            padding: 30px;
            margin-bottom: 20px;
            transition: transform 0.3s ease;
        }
        .struktur-card:hover {
            transform: translateY(-4px);
        }
        .badge-role {
            background-color: rgba(0, 124, 75, 0.1);
            color: var(--kemenag-green);
            font-size: 0.75rem;
            font-weight: 700;
            padding: 6px 15px;
            border-radius: 50px;
            display: inline-block;
            margin-bottom: 10px;
        }

        /* Berita Style Modern */
        .berita-card {
            border-radius: var(--card-radius);
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.04);
            border: none;
            height: 100%;
            background: white;
            transition: transform 0.3s ease;
        }
        .berita-card:hover {
            transform: translateY(-5px);
        }
        .berita-img {
            height: 220px;
            object-fit: cover;
            width: 100%;
        }
        .berita-body {
            padding: 30px;
        }
        .berita-date {
            color: var(--kemenag-green);
            font-size: 0.85rem;
            font-weight: 700;
            margin-bottom: 10px;
            display: block;
        }
        .berita-title {
            font-weight: 800;
            color: var(--text-dark);
            margin-bottom: 15px;
            line-height: 1.4;
        }

        /* Footer */
        .footer {
            background-color: var(--kemenag-green-dark);
            color: white;
            padding: 60px 0 20px;
        }
        .footer h5 {
            font-weight: 700;
            margin-bottom: 20px;
        }
        .footer p, .footer a {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.95rem;
            text-decoration: none;
        }
        .footer a:hover {
            color: var(--accent-yellow);
        }
        .social-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 35px;
            height: 35px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            color: white;
            margin-right: 10px;
            transition: all 0.3s;
        }
        .social-icon:hover {
            background: var(--accent-yellow);
            color: var(--kemenag-green-dark);
        }

        /* Form Input Clean Minimalist */
        .form-control {
            border-radius: 12px;
            padding: 15px 20px;
            border: 1px solid transparent;
            background-color: #f4f7f6;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            box-shadow: 0 0 0 4px rgba(0, 124, 75, 0.1);
            border-color: var(--kemenag-green);
            background-color: white;
        }
    </style>
</head>
<body class="bg-grey">

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom sticky-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                Raudlatul Muta'allimin
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link active" href="#beranda">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link" href="#profil">Profil</a></li>
                    <li class="nav-item"><a class="nav-link" href="#pendidikan">Pendidikan</a></li>
                    <li class="nav-item"><a class="nav-link" href="#informasi">Informasi</a></li>
                    <li class="nav-item ms-lg-3 mt-3 mt-lg-0">
                        <a class="btn btn-outline-green" href="login.php">Login Admin</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="beranda" class="hero-section">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <span class="badge-modern"><i class="fas fa-bullhorn me-2"></i> Pendaftaran 2024/2025 Dibuka</span>
                    <h1 class="hero-title">Pondok Pesantren<br><span>Raudlatul Muta'allimin</span></h1>
                    <p class="hero-subtitle">Membentuk generasi Islami yang cerdas, berakhlak mulia, dan berwawasan global melalui pendidikan terpadu dari RA hingga SMK di Way Kanan, Lampung.</p>
                    <div class="d-flex gap-3 flex-wrap">
                        <a href="#kontak" class="btn btn-yellow">Daftar Sekarang <i class="fas fa-arrow-right ms-2"></i></a>
                        <a href="#profil" class="btn btn-outline-green-hero">Jelajahi Profil</a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="hero-img-box">
                        <!-- Modern clean image placeholder (Nantinya diganti foto gedung/santri asli) -->
                        <img src="https://images.unsplash.com/photo-1542816417-0983c9c9ad53?auto=format&fit=crop&q=80&w=800" alt="Pesantren">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Jenjang Pendidikan (Mirip Alur Pendaftaran pada referensi) -->
    <section id="pendidikan" class="section-padding bg-grey pb-0">
        <div class="container">
            <div class="section-title-center">
                <h2>Lembaga Pendidikan</h2>
                <div class="title-underline"></div>
                <p class="mt-3">Jenjang pendidikan terpadu di bawah naungan yayasan.</p>
            </div>
            
            <div class="row g-4 justify-content-center">
                <div class="col-lg-2 col-md-4 col-6">
                    <div class="clean-card">
                        <div class="icon-circle"><i class="fas fa-child"></i></div>
                        <h5 class="mb-0 text-dark">RA / TK</h5>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-6">
                    <div class="clean-card">
                        <div class="icon-circle"><i class="fas fa-book-reader"></i></div>
                        <h5 class="mb-0 text-dark">MI</h5>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-6">
                    <div class="clean-card">
                        <div class="icon-circle"><i class="fas fa-mosque"></i></div>
                        <h5 class="mb-0 text-dark">MTs</h5>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-6">
                    <div class="clean-card">
                        <div class="icon-circle"><i class="fas fa-graduation-cap"></i></div>
                        <h5 class="mb-0 text-dark">MA</h5>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-6">
                    <div class="clean-card">
                        <div class="icon-circle"><i class="fas fa-laptop-code"></i></div>
                        <h5 class="mb-0 text-dark">SMK</h5>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Profil & Struktur (Menggunakan gaya Informasi & Pengumuman) -->
    <section id="profil" class="section-padding bg-grey">
        <div class="container">
            <div class="row g-5">
                <!-- Kiri: Visi Misi -->
                <div class="col-lg-6">
                    <h3 class="mb-4 text-dark fw-bold">Visi & Misi Pesantren</h3>
                    <div class="struktur-card">
                        <h5 class="text-success fw-bold mb-3"><i class="fas fa-eye me-2"></i> Visi</h5>
                        <p class="mb-0 text-dark">Mencetak generasi Islami yang berakhlakul karimah, mandiri, dan berprestasi.</p>
                    </div>
                    <div class="struktur-card">
                        <h5 class="text-success fw-bold mb-3"><i class="fas fa-bullseye me-2"></i> Misi</h5>
                        <ul class="mb-0 text-dark text-start" style="padding-left: 20px;">
                            <li class="mb-2">Menyelenggarakan pendidikan agama dan umum yang berkualitas.</li>
                            <li class="mb-2">Membentuk karakter santri yang tangguh dan beradab.</li>
                            <li>Mengembangkan keterampilan santri menghadapi era digital.</li>
                        </ul>
                    </div>
                </div>

                <!-- Kanan: Pimpinan -->
                <div class="col-lg-6">
                    <h3 class="mb-4 text-dark fw-bold">Pimpinan Pondok</h3>
                    <div class="struktur-card">
                        <div class="d-flex align-items-center">
                            <img src="https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?auto=format&fit=crop&q=80&w=150" class="rounded-circle me-4 shadow-sm" style="width: 90px; height: 90px; object-fit: cover;" alt="Pengasuh">
                            <div>
                                <span class="badge-role">Pengasuh Pondok</span>
                                <h5 class="fw-bold mb-1 text-dark">KH. Marsudi</h5>
                                <p class="mb-0 small fst-italic text-muted">"Ahlan Wa Sahlan di website resmi Pondok Pesantren Raudlatul Muta'allimin."</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="struktur-card">
                        <div class="d-flex align-items-center">
                            <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?auto=format&fit=crop&q=80&w=150" class="rounded-circle me-4 shadow-sm" style="width: 90px; height: 90px; object-fit: cover;" alt="Ketua Yayasan">
                            <div>
                                <span class="badge-role">Ketua Yayasan</span>
                                <h5 class="fw-bold mb-1 text-dark">Ust. Sudi. S.Pd.I</h5>
                                <p class="mb-0 small fst-italic text-muted">"Kami berkomitmen memfasilitasi pendidikan terbaik mulai RA hingga SMK."</p>
                            </div>
                        </div>
                    </div>

                    <div class="struktur-card">
                        <div class="d-flex align-items-center">
                            <img src="https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?auto=format&fit=crop&q=80&w=150" class="rounded-circle me-4 shadow-sm" style="width: 90px; height: 90px; object-fit: cover;" alt="DPP">
                            <div>
                                <span class="badge-role">DPP</span>
                                <h5 class="fw-bold mb-1 text-dark">Ust. Oktawidodo, S.Pd.I</h5>
                                <p class="mb-0 small fst-italic text-muted">"Membina karakter santri agar matang secara spiritual dan intelektual."</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Berita & Informasi -->
    <section id="informasi" class="section-padding bg-grey">
        <div class="container">
            <div class="section-title-center">
                <h2>Berita & Informasi Terbaru</h2>
                <p class="mt-2 text-muted">Ikuti perkembangan dan informasi terkini dari kegiatan, prestasi, dan pengumuman penting di Pondok Pesantren.</p>
            </div>

            <div class="row g-4">
                <!-- Berita 1 -->
                <div class="col-lg-4 col-md-6">
                    <div class="berita-card">
                        <div class="berita-img-wrapper">
                            <span class="badge-kegiatan">Kegiatan Madrasah</span>
                            <img src="https://images.unsplash.com/photo-1577896851231-70ef18881754?auto=format&fit=crop&q=80&w=600" class="berita-img" alt="Berita 1">
                        </div>
                        <div class="berita-body">
                            <span class="berita-date">02 June 2026</span>
                            <h5 class="berita-title">Pembukaan ASAS Genap Berlangsung Khidmat, Siswa Siap Berkompetisi</h5>
                            <p class="berita-excerpt">Pondok Pesantren Raudlatul Muta'allimin menggelar kegiatan pembukaan Asesmen Sumatif Akhir Semester (ASAS) dengan lancar...</p>
                            <a href="#" class="berita-link">Baca Selengkapnya <i class="fas fa-arrow-right ms-2"></i></a>
                        </div>
                    </div>
                </div>
                <!-- Berita 2 -->
                <div class="col-lg-4 col-md-6">
                    <div class="berita-card">
                        <div class="berita-img-wrapper">
                            <span class="badge-kegiatan">Kegiatan Madrasah</span>
                            <img src="https://images.unsplash.com/photo-1601055903647-8f1af67451e5?auto=format&fit=crop&q=80&w=600" class="berita-img" alt="Berita 2">
                        </div>
                        <div class="berita-body">
                            <span class="berita-date">30 May 2026</span>
                            <h5 class="berita-title">Pondok Potong 4 Sapi, Bagikan 229 Bungkus Daging Qurban</h5>
                            <p class="berita-excerpt">Pondok Pesantren Raudlatul Muta'allimin menggelar pemotongan hewan qurban berupa empat ekor sapi dan membagikannya ke warga sekitar...</p>
                            <a href="#" class="berita-link">Baca Selengkapnya <i class="fas fa-arrow-right ms-2"></i></a>
                        </div>
                    </div>
                </div>
                <!-- Berita 3 -->
                <div class="col-lg-4 col-md-6">
                    <div class="berita-card">
                        <div class="berita-img-wrapper">
                            <span class="badge-kegiatan">Prestasi</span>
                            <img src="https://images.unsplash.com/photo-1584485590747-cf4f0283f5fb?auto=format&fit=crop&q=80&w=600" class="berita-img" alt="Berita 3">
                        </div>
                        <div class="berita-body">
                            <span class="berita-date">18 May 2026</span>
                            <h5 class="berita-title">Raih Piala Bergilir Juara Umum PORSENI Untuk Ketiga Kali</h5>
                            <p class="berita-excerpt">Prestasi membanggakan kembali ditorehkan siswa-siswi kita dalam ajang Pekan Olahraga dan Seni (PORSENI) tingkat kabupaten...</p>
                            <a href="#" class="berita-link">Baca Selengkapnya <i class="fas fa-arrow-right ms-2"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-5">
                <a href="#" class="btn-outline-green-rounded">Lihat Semua Berita</a>
            </div>
        </div>
    </section>

    <!-- Dokumentasi Kegiatan (Terpisah) -->
    <section id="galeri" class="section-padding bg-white">
        <div class="container">
            <div class="section-title-center">
                <h2>Dokumentasi Kegiatan</h2>
                <p class="mt-2 text-muted">Momen-momen berharga yang terekam dari berbagai aktivitas dan kegiatan di lingkungan madrasah.</p>
            </div>
            
            <div class="row g-3">
                <div class="col-lg-4 col-md-6">
                    <img src="https://images.unsplash.com/photo-1511649475669-e288648b2339?auto=format&fit=crop&q=80&w=500" class="galeri-img shadow-sm" alt="Dokumentasi 1">
                </div>
                <div class="col-lg-4 col-md-6">
                    <img src="https://images.unsplash.com/photo-1577563908411-50cb989766a3?auto=format&fit=crop&q=80&w=500" class="galeri-img shadow-sm" alt="Dokumentasi 2">
                </div>
                <div class="col-lg-4 col-md-6">
                    <img src="https://images.unsplash.com/photo-1580582932707-520aed937b7b?auto=format&fit=crop&q=80&w=500" class="galeri-img shadow-sm" alt="Dokumentasi 3">
                </div>
                <div class="col-lg-4 col-md-6">
                    <img src="https://images.unsplash.com/photo-1509062522246-3755977927d7?auto=format&fit=crop&q=80&w=500" class="galeri-img shadow-sm" alt="Dokumentasi 4">
                </div>
                <div class="col-lg-4 col-md-6">
                    <img src="https://images.unsplash.com/photo-1525926476834-f257a07bf122?auto=format&fit=crop&q=80&w=500" class="galeri-img shadow-sm" alt="Dokumentasi 5">
                </div>
                <div class="col-lg-4 col-md-6">
                    <img src="https://images.unsplash.com/photo-1588072432836-e10032774350?auto=format&fit=crop&q=80&w=500" class="galeri-img shadow-sm" alt="Dokumentasi 6">
                </div>
            </div>
            <div class="text-center mt-5 pt-3">
                <a href="#" class="btn-outline-green-rounded">Lihat Galeri Lengkap</a>
            </div>
        </div>
    </section>

    <!-- Kontak -->
    <section id="kontak" class="section-padding bg-grey">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="struktur-card p-5">
                        <h3 class="fw-bold text-center mb-2 text-dark">Hubungi / Daftar</h3>
                        <p class="text-center text-muted mb-5">Silakan tinggalkan pesan untuk informasi pendaftaran.</p>
                        
                        <form id="formKontak">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark small mb-2">Nama Lengkap</label>
                                    <input type="text" class="form-control" id="nama" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark small mb-2">No. WhatsApp</label>
                                    <input type="text" class="form-control" id="wa" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold text-dark small mb-2">Pesan / Pertanyaan</label>
                                    <textarea class="form-control" rows="5" required></textarea>
                                </div>
                                <div class="col-12 text-center mt-5">
                                    <button type="submit" class="btn btn-yellow px-5 py-3 rounded-pill fw-bold">Kirim Pesan</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-5">
                    <h4 class="fw-bold text-white mb-3">Raudlatul Muta'allimin</h4>
                    <p>Jl. Dr. Ak. Gani, No.50, Jaya Tinggi, Kasui, Way Kanan, Lampung.</p>
                    <div class="mt-4">
                        <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h5>Lembaga</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="d-block mb-2">RA / TK</a></li>
                        <li><a href="#" class="d-block mb-2">Madrasah Ibtidaiyah</a></li>
                        <li><a href="#" class="d-block mb-2">Madrasah Tsanawiyah</a></li>
                        <li><a href="#" class="d-block mb-2">Madrasah Aliyah</a></li>
                        <li><a href="#" class="d-block">SMK</a></li>
                    </ul>
                </div>
                <div class="col-lg-4 col-md-6">
                    <h5>Kontak</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="fas fa-phone-alt me-2 text-warning"></i> 0812-3456-7890</li>
                        <li class="mb-2"><i class="fas fa-envelope me-2 text-warning"></i> info@raudlatulmutaallimin.sch.id</li>
                    </ul>
                </div>
            </div>
            <div class="text-center mt-5 pt-4 border-top border-secondary">
                <p class="mb-0">&copy; <?php echo date('Y'); ?> Yayasan PP Raudlatul Muta'allimin. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Smooth scroll
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if(target) {
                        window.scrollTo({
                            top: target.offsetTop - 70,
                            behavior: "smooth"
                        });
                    }
                });
            });

            // SweetAlert Form
            const formKontak = document.getElementById('formKontak');
            if(formKontak) {
                formKontak.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const nama = document.getElementById('nama').value;
                    Swal.fire({
                        title: 'Berhasil!',
                        text: `Terima kasih ${nama}, pesan Anda akan segera kami tindak lanjuti.`,
                        icon: 'success',
                        confirmButtonColor: '#007c4b', // Warna kemenag
                        confirmButtonText: 'OK'
                    }).then(() => {
                        formKontak.reset();
                    });
                });
            }
        });
    </script>
</body>
</html>