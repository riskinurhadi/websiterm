<?php
/**
 * File: index.php
 * Develop by Risky Nurhadi, code with coffe and love <3
 * Mau Clone Code ? Boleh banget. tapi jangan lupa izin ya bang 🗿
 * Code nya ada di github riskinurhadi 
 */
include 'koneksi.php';
$status_pesan = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['kirim_pesan'])) {
    $nama = mysqli_real_escape_string($conn, trim($_POST['nama_pengirim']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $subjek = mysqli_real_escape_string($conn, trim($_POST['subjek']));
    $pesan = mysqli_real_escape_string($conn, trim($_POST['pesan']));
    if (!empty($nama) && !empty($pesan)) {
        $query_pesan = "INSERT INTO pesan_masuk (nama_pengirim, email, subjek, pesan, status_baca, tanggal_kirim) 
                        VALUES ('$nama', '$email', '$subjek', '$pesan', 'Belum', NOW())";
        if (mysqli_query($conn, $query_pesan)) {
            $status_pesan = 'success';
        } else {
            $status_pesan = 'error';
        }
    } else {
        $status_pesan = 'validation_error';
    }
}
$query_profil = mysqli_query($conn, "SELECT * FROM profil_web WHERE id_profil = 1 LIMIT 1");
$profil = mysqli_fetch_assoc($query_profil);
$nama_pesantren   = $profil['nama_pesantren'] ?? "Pondok Pesantren Raudlatul Muta'allimin";
$alamat_pesantren = $profil['alamat'] ?? "Jl. Dr. Ak. Gani, No.50, Jaya Tinggi, Kasui, Way Kanan, Lampung";
$tentang_pondok   = $profil['tentang_pondok'] ?? "Raudlatul Muta'allimin adalah lembaga pendidikan Islam yang berdiri dengan komitmen kuat untuk mencetak generasi penerus bangsa yang unggul dalam ilmu pengetahuan dan teknologi (IPTEK), serta kokoh dalam iman dan taqwa (IMTAQ). Kami memadukan kurikulum nasional dengan kurikulum kepesantrenan salafiah untuk membentuk karakter santri yang mandiri, berdisiplin, dan berakhlakul karimah.";
$foto_tentang     = $profil['foto_tentang'] ?? "https://images.unsplash.com/photo-1584697964400-2af6a2f62651?q=80&w=800&h=600&fit=crop";
$visi_pesantren   = $profil['visi'] ?? "Mencetak generasi Islami yang bertaqwa, cerdas, terampil, mandiri, dan berakhlakul karimah serta unggul dalam penguasaan IPTEK.";
$misi_raw         = $profil['misi'] ?? "Menyelenggarakan pendidikan formal berkualitas yang terintegrasi dengan pesantren.\nMenanamkan pemahaman kitab salaf serta bimbingan tahfidz Al-Qur'an.\nMembentuk lingkungan belajar mandiri guna melatih kedisiplinan.";
$no_telp          = $profil['no_telepon'] ?? "081234567890";
$email_pesantren  = $profil['email'] ?? "sekretariat@raudlatulmutaallimin.sch.id";
$sosmed_fb        = !empty($profil['facebook']) ? $profil['facebook'] : "#";
$sosmed_ig        = !empty($profil['instagram']) ? $profil['instagram'] : "#";
$sosmed_yt        = !empty($profil['youtube']) ? $profil['youtube'] : "#";

// Membagi data misi per baris untuk ditampilkan sebagai list
$misi_items = explode("\n", str_replace("\r", "", $misi_raw));

// ==============================================
// 3. QUERY DATA SAMBUTAN PEJABAT (sambutan)
// ==============================================
$query_sambutan = mysqli_query($conn, "SELECT * FROM sambutan WHERE status_tampil = 'Y' ORDER BY urutan ASC");
$sambutan_list = [];
if ($query_sambutan && mysqli_num_rows($query_sambutan) > 0) {
    while ($row = mysqli_fetch_assoc($query_sambutan)) {
        $sambutan_list[] = $row;
    }
} else {
    // Fallback jika kosong
    $sambutan_list = [
        [
            'nama_pejabat' => 'KH. Marsudi',
            'jabatan' => 'Pengasuh Utama',
            'foto' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?q=80&w=256&h=256&fit=crop',
            'isi_sambutan' => 'Pondok pesantren bukan hanya lembaga pendidikan transfer ilmu (dirasah), tetapi tempat pembentukan akhlak, kepribadian taqwa, serta melestarikan ajaran ahlussunnah wal jama’ah.'
        ],
        [
            'nama_pejabat' => 'Ust. Sudi, S.Pd.I',
            'jabatan' => 'Ketua Yayasan',
            'foto' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?q=80&w=256&h=256&fit=crop',
            'isi_sambutan' => 'Yayasan Raudlatul Muta\'allimin berkomitmen menghadirkan infrastruktur pendidikan terlengkap mulai dari RA hingga SMK. Integrasi kurikulum pesantren salaf dengan keahlian praktis siap bersaing.'
        ],
        [
            'nama_pejabat' => 'Ust. Oktawidodo, S.Pd.I',
            'jabatan' => 'Dewan Pembina Pondok (DPP)',
            'foto' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?q=80&w=256&h=256&fit=crop',
            'isi_sambutan' => 'Kedisiplinan, ukhuwah islamiyah, dan pemantapan akidah adalah pilar utama pembinaan santri kami sehari-hari di asrama pondok pesantren.'
        ]
    ];
}

// ==============================================
// 4. QUERY DATA TESTIMONI ALUMNI (testimoni)
// ==============================================
$query_testimoni = mysqli_query($conn, "SELECT * FROM testimoni WHERE status_tampil = 'Y' ORDER BY id_testimoni DESC");
$testimoni_list = [];
if ($query_testimoni && mysqli_num_rows($query_testimoni) > 0) {
    while ($row = mysqli_fetch_assoc($query_testimoni)) {
        $testimoni_list[] = $row;
    }
} else {
    // Fallback jika kosong
    $testimoni_list = [
        [
            'nama_alumni' => 'Ahmad Fauzi, S.Kom',
            'profesi_angkatan' => 'Lulusan SMK - 2019',
            'foto' => 'https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?q=80&w=120&h=120&fit=crop',
            'isi_testimoni' => 'Belajar di SMK Raudlatul Muta\'allimin sangat luar biasa. Selain mahir pemrograman web, saya digembleng ilmu agama dan akhlak pesantren.'
        ],
        [
            'nama_alumni' => 'Nabila Putri S.H',
            'profesi_angkatan' => 'Lulusan MA - 2018',
            'foto' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?q=80&w=120&h=120&fit=crop',
            'isi_testimoni' => 'Lingkungan pondok yang asri, asatidz yang sabar dan mumpuni membimbing hafalan Qur\'an, membuat saya terlatih mandiri dan berdisiplin.'
        ]
    ];
}

// ==============================================
// 5. QUERY DATA BERITA (berita)
// ==============================================
$query_berita = mysqli_query($conn, "SELECT * FROM berita ORDER BY tanggal_publish DESC, id_berita DESC LIMIT 3");
$berita_list = [];
if ($query_berita && mysqli_num_rows($query_berita) > 0) {
    while ($row = mysqli_fetch_assoc($query_berita)) {
        $berita_list[] = $row;
    }
} else {
    // Fallback jika kosong
    $berita_list = [
        [
            'id_berita' => 1,
            'judul' => 'Pondok Pesantren Rayakan HSN 2026 dengan Pawai Akbar',
            'tanggal_publish' => '2026-10-22',
            'isi_berita' => 'Ribuan santri dari jenjang RA, MI, MTs, MA, dan SMK Raudlatul Muta\'allimin tumpah ruah merayakan Hari Santri Nasional...',
            'gambar_cover' => 'https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?q=80&w=600&h=400&fit=crop'
        ],
        [
            'id_berita' => 2,
            'judul' => 'Siswa SMK Sabet Juara 1 Lomba Coding Se-Provinsi Lampung',
            'tanggal_publish' => '2026-09-15',
            'isi_berita' => 'Kabar gembira datang dari bidang keahlian rekayasa teknologi, santri SMK berhasil menyisihkan puluhan sekolah umum...',
            'gambar_cover' => 'https://images.unsplash.com/photo-1515187029135-18ee286d815b?q=80&w=600&h=400&fit=crop'
        ],
        [
            'id_berita' => 3,
            'judul' => 'Penerimaan Santri Baru (PSB) Tahun Ajaran Resmi Dipercepat',
            'tanggal_publish' => '2026-01-01',
            'isi_berita' => 'Brosur, jalur seleksi beasiswa prestasi, rincian biaya asrama, dan pendaftaran online resmi dirilis...',
            'gambar_cover' => 'https://images.unsplash.com/photo-1577896851231-70ef18881754?q=80&w=600&h=400&fit=crop'
        ]
    ];
}

// ==============================================
// 6. QUERY DATA DOKUMENTASI/GALERI (dokumentasi)
// ==============================================
$query_galeri = mysqli_query($conn, "SELECT * FROM dokumentasi ORDER BY tanggal_kegiatan DESC, id_galeri DESC LIMIT 6");
$galeri_list = [];
if ($query_galeri && mysqli_num_rows($query_galeri) > 0) {
    while ($row = mysqli_fetch_assoc($query_galeri)) {
        $galeri_list[] = $row;
    }
} else {
    // Fallback jika kosong
    $galeri_list = [
        ['judul_kegiatan' => 'Pembelajaran Al-Qur\'an', 'file_foto' => 'https://images.unsplash.com/photo-1577896851231-70ef18881754?q=80&w=600&h=600&fit=crop'],
        ['judul_kegiatan' => 'Mujahadah Mingguan', 'file_foto' => 'https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?q=80&w=600&h=600&fit=crop'],
        ['judul_kegiatan' => 'Upacara Bendera', 'file_foto' => 'https://images.unsplash.com/photo-1517841905240-472988babdf9?q=80&w=600&h=600&fit=crop'],
        ['judul_kegiatan' => 'Praktek Lab Komputer', 'file_foto' => 'https://images.unsplash.com/photo-1515187029135-18ee286d815b?q=80&w=600&h=600&fit=crop'],
        ['judul_kegiatan' => 'Motorik Santri RA', 'file_foto' => 'https://images.unsplash.com/photo-1509062522246-3755977927d7?q=80&w=600&h=600&fit=crop'],
        ['judul_kegiatan' => 'Haflah Akhirussanah', 'file_foto' => 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?q=80&w=600&h=600&fit=crop']
    ];
}

// ==============================================
// 7. QUERY DATA PRESTASI (prestasi)
// ==============================================
$query_prestasi = mysqli_query($conn, "SELECT * FROM prestasi ORDER BY tahun DESC, id_prestasi DESC LIMIT 3");
$prestasi_list = [];
if ($query_prestasi && mysqli_num_rows($query_prestasi) > 0) {
    while ($row = mysqli_fetch_assoc($query_prestasi)) {
        $prestasi_list[] = $row;
    }
} else {
    // Fallback jika database masih kosong
    $prestasi_list = [
        [
            'judul' => 'JUARA UMUM SMANSAKA DAY 2026',
            'deskripsi' => 'Siswa/Santri Yayasan Pondok Pesantren Raudlatul Muta\'allimin berhasil mencuri perhatian dengan meraih gelar Juara Umum pada ajang SMANSAKA DAY Tahun 2026.',
            'tingkat' => 'Kabupaten',
            'tahun' => '2026',
            'gambar' => 'https://images.unsplash.com/photo-1517841905240-472988babdf9?q=80&w=600&h=400&fit=crop'
        ],
        [
            'judul' => 'Juara Umum Galang Scout Competition Ke-IV',
            'deskripsi' => 'Way Kanan - Prestasi membanggakan kembali ditorehkan oleh Pramuka Santri Raudlatul Muta\'allimin pada ajang kompetisi Galang Scout Competition Ke-IV.',
            'tingkat' => 'Kabupaten',
            'tahun' => '2025',
            'gambar' => 'https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?q=80&w=600&h=400&fit=crop'
        ],
        [
            'judul' => 'GAC III Kejuaraan Renang Antar Perkumpulan se-Lampung',
            'deskripsi' => 'GAC III Kejuaraan Renang Antar Perkumpulan & Fun Swim Se-Lampung Utara & Invitation. Santri kami meraih medali kejuaraan yang membanggakan.',
            'tingkat' => 'Provinsi Lampung',
            'tahun' => '2025',
            'gambar' => 'https://images.unsplash.com/photo-1515187029135-18ee286d815b?q=80&w=600&h=400&fit=crop'
        ]
    ];
}

// Memasukkan navbar terpusat
include 'navbar.php';
?>

<!-- override style global untuk mematikan warna kuning/emas dan menerapkan konsep modern minimalis -->
<style>
    :root {
        --kemenag-green-primary: #00a86b !important; /* Hijau Emerald Modern */
        --kemenag-green-dark: #007d4f !important;    /* Hijau Gelap Solid */
        --kemenag-green-light: #f0faf5 !important;   /* Hijau Sangat Lembut untuk Background */
        --light-neutral: #f8fafc !important;         /* Latar Abu-abu Bersih (Slate 50) */
        --dark-neutral: #0f172a !important;          /* Hitam/Biru Dongker Sangat Gelap (Slate 900) */
        --text-muted-custom: #475569 !important;     /* Deskripsi Abu-abu Gelap (Slate 600) */
        --transition-smooth: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    }

    body {
        background-color: var(--light-neutral) !important;
        font-family: 'Poppins', sans-serif;
    }

    /* Override top-bar agar selaras (Hijau Gelap dan Putih) */
    .top-bar {
        background-color: var(--kemenag-green-dark) !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
    }
    .top-bar span, .top-bar a {
        color: #ffffff !important;
    }
    .top-bar i {
        color: #ffffff !important;
    }

    /* Penyesuaian Tombol Bulat Sempurna (Pill Button) */
    .btn-pill-primary {
        background-color: var(--kemenag-green-primary) !important;
        color: #ffffff !important;
        border: 2px solid transparent !important;
        border-radius: 50px !important;
        padding: 12px 30px !important;
        font-weight: 600 !important;
        font-size: 0.95rem !important;
        transition: var(--transition-smooth) !important;
        box-shadow: 0 10px 25px -5px rgba(0, 168, 107, 0.3) !important;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-pill-primary:hover {
        background-color: var(--kemenag-green-dark) !important;
        transform: translateY(-3px);
        box-shadow: 0 15px 30px -5px rgba(0, 168, 107, 0.4) !important;
    }

    .btn-pill-outline {
        background-color: transparent !important;
        color: var(--kemenag-green-primary) !important;
        border: 2px solid var(--kemenag-green-primary) !important;
        border-radius: 50px !important;
        padding: 12px 30px !important;
        font-weight: 600 !important;
        font-size: 0.95rem !important;
        transition: var(--transition-smooth) !important;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-pill-outline:hover {
        background-color: var(--kemenag-green-light) !important;
        transform: translateY(-3px);
    }

    /* Kartu Modern Berlatar Belakang Putih di atas Latar Belakang Abu-abu */
    .modern-card {
        background-color: #ffffff !important;
        border: none !important;
        border-radius: 24px !important;
        box-shadow: 0 10px 35px -10px rgba(15, 23, 42, 0.04) !important;
        transition: var(--transition-smooth) !important;
        overflow: hidden;
    }

    .modern-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 30px 60px -15px rgba(0, 168, 107, 0.12) !important;
    }

    /* Header Section Modern */
    .section-title {
        font-weight: 800;
        color: var(--dark-neutral);
        letter-spacing: -1px;
        font-size: 2.25rem;
    }

    .section-subtitle {
        color: var(--kemenag-green-primary);
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 2px;
        font-size: 0.8rem;
    }

    /* Badges */
    .badge-modern {
        background-color: var(--kemenag-green-light) !important;
        color: var(--kemenag-green-primary) !important;
        font-weight: 600;
        border-radius: 30px;
        padding: 8px 18px;
        font-size: 0.8rem;
        border: 1px solid rgba(0, 168, 107, 0.1);
        display: inline-block;
    }

    /* Custom scroll indicator / minimal element */
    .deco-line {
        width: 60px;
        height: 4px;
        background-color: var(--kemenag-green-primary);
        border-radius: 2px;
        margin: 15px auto 0 auto;
    }

    /* Menghilangkan border atas kustom yang berwarna kuning/emas */
    .card-top-border-green {
        border-top: 4px solid var(--kemenag-green-primary) !important;
    }

    /* --- FEATURE CARDS SECTION --- */
    .features-section {
        background-color: var(--light-neutral);
        padding: 0 0 60px 0;
        margin-top: -60px;
        position: relative;
        z-index: 10;
    }
    .feature-card {
        background-color: #ffffff;
        border-radius: 16px;
        padding: 35px 25px;
        height: 100%;
        text-align: center;
        box-shadow: 0 10px 30px rgba(0,0,0,0.03);
        border: 1px solid rgba(0,0,0,0.02);
        transition: all 0.3s ease;
    }
    .feature-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 35px rgba(0,168,107,0.1);
    }
    .feature-icon-wrapper {
        width: 75px;
        height: 75px;
        border-radius: 50%;
        background-color: var(--kemenag-green-light);
        color: var(--kemenag-green-primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        margin: 0 auto 20px auto;
    }

    /* --- JADWAL SHOLAT SECTION (BARU) --- */
    .prayer-section {
        background: linear-gradient(135deg, var(--kemenag-green-dark) 0%, var(--dark-neutral) 100%);
        padding: 60px 0;
        color: white;
        position: relative;
    }
    .prayer-card {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 16px;
        padding: 20px 15px;
        text-align: center;
        transition: var(--transition-smooth);
    }
    .prayer-card:hover {
        background: rgba(255, 255, 255, 0.2);
        transform: translateY(-5px);
    }
    .prayer-icon {
        font-size: 1.8rem;
        color: #fde047; /* Yellow/Gold */
        margin-bottom: 10px;
    }

    /* --- FLOATING DEV BADGE (BARU) --- */
    .dev-badge {
        position: fixed;
        bottom: 25px;
        left: 25px;
        background: rgba(15, 23, 42, 0.9);
        backdrop-filter: blur(8px);
        color: white;
        padding: 12px 22px;
        border-radius: 50px;
        z-index: 9999;
        border: 1px solid rgba(255,255,255,0.1);
        display: flex;
        align-items: center;
        gap: 12px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        animation: floatBubble 3s ease-in-out infinite;
    }
    @keyframes floatBubble {
        0% { transform: translateY(0px); }
        50% { transform: translateY(-8px); }
        100% { transform: translateY(0px); }
    }
    @media (max-width: 768px) {
        .dev-badge {
            bottom: 15px;
            left: 15px;
            right: 15px;
            justify-content: center;
        }
    }

    /* --- PARALLAX CTA SECTION --- */
    .parallax-cta {
        background-image: linear-gradient(rgba(0, 168, 107, 0.85), rgba(0, 125, 79, 0.9)), url('https://images.unsplash.com/photo-1541829070764-84a7d30dd3f3?q=80&w=2000&auto=format&fit=crop');
        background-attachment: fixed;
        background-position: center;
        background-repeat: no-repeat;
        background-size: cover;
        padding: 100px 0;
        color: white;
        text-align: center;
        position: relative;
    }
    .btn-cta-white {
        background-color: #ffffff !important;
        color: var(--kemenag-green-dark) !important;
        font-weight: 700;
        font-size: 1.05rem;
        padding: 14px 40px;
        border-radius: 50px;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s ease;
        box-shadow: 0 10px 20px rgba(0,0,0,0.15);
    }
    .btn-cta-white:hover {
        background-color: var(--kemenag-green-light) !important;
        color: var(--kemenag-green-dark) !important;
        transform: translateY(-3px);
        box-shadow: 0 15px 25px rgba(0,0,0,0.2);
    }
</style>

<!-- ==============================================
     1. HERO / LANDING BANNER SECTION
     ============================================== -->
<section id="beranda" class="hero-section position-relative d-flex align-items-center" style="background-color: #ffffff; min-height: 80vh; padding: 140px 0 160px 0;">
    <div style="position: absolute; top: -10%; right: -5%; width: 50%; height: 70%; background: radial-gradient(circle, var(--kemenag-green-light) 0%, rgba(255,255,255,0) 70%); z-index: 1; pointer-events: none;"></div>
    <div style="position: absolute; bottom: -5%; left: -5%; width: 40%; height: 60%; background: radial-gradient(circle, var(--kemenag-green-light) 0%, rgba(255,255,255,0) 60%); z-index: 1; pointer-events: none;"></div>

    <div class="container position-relative" style="z-index: 2;">
        <div class="row">
            <div class="col-lg-8 text-start">
                <div class="mb-4 d-inline-flex">
                    <span class="badge-modern">
                        <i class="fas fa-mosque me-2"></i> <?php echo htmlspecialchars($nama_pesantren); ?>
                    </span>
                </div>
                <h1 class="display-4 fw-extrabold mb-4 text-dark" style="font-weight: 850; line-height: 1.2; letter-spacing: -1.5px;">
                    Mendidik Generasi <span style="color: var(--kemenag-green-primary);">Cerdas</span><br>& Berkarakter Islami
                </h1>
                <p class="lead mb-5" style="max-width: 680px; font-size: 1.15rem; line-height: 1.9; color: var(--text-muted-custom);">
                    Menyelenggarakan pendidikan formal terpadu yang memadukan kedalaman spiritual pesantren salafiah dengan keahlian praktis teknologi masa kini di Kasui, Way Kanan, Lampung.
                </p>
                <div class="d-flex flex-column flex-sm-row justify-content-start gap-3">
                    <a href="#lembaga" class="btn btn-pill-primary"><i class="fas fa-graduation-cap"></i> Lembaga Pendidikan</a>
                    <a href="#profil" class="btn btn-pill-outline"><i class="fas fa-info-circle"></i> Visi & Misi</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ==============================================
     1A. SECTION FITUR (RINGKASAN DATA - 4 KARTU)
     ============================================== -->
<section class="features-section">
    <div class="container">
        <div class="row g-4 justify-content-center">
            <div class="col-md-6 col-lg-3">
                <div class="feature-card">
                    <div class="feature-icon-wrapper"><i class="fas fa-university"></i></div>
                    <h5 class="fw-bold text-dark mb-3">Profil Madrasah</h5>
                    <p class="text-muted small mb-0">Kenali lebih dalam sejarah, visi, misi, serta fasilitas yang kami sediakan untuk menunjang pendidikan.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="feature-card">
                    <div class="feature-icon-wrapper"><i class="far fa-newspaper"></i></div>
                    <h5 class="fw-bold text-dark mb-3">Pusat Informasi</h5>
                    <p class="text-muted small mb-0">Dapatkan berita terbaru, pengumuman penting, dan agenda kegiatan madrasah secara cepat dan akurat.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="feature-card">
                    <div class="feature-icon-wrapper"><i class="fas fa-trophy"></i></div>
                    <h5 class="fw-bold text-dark mb-3">Prestasi & Galeri</h5>
                    <p class="text-muted small mb-0">Lihat berbagai pencapaian siswa dan guru, serta dokumentasi kegiatan dalam galeri foto dan video kami.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="feature-card">
                    <div class="feature-icon-wrapper"><i class="fas fa-globe"></i></div>
                    <h5 class="fw-bold text-dark mb-3">Layanan Digital</h5>
                    <p class="text-muted small mb-0">Akses mudah untuk pendaftaran siswa baru (PPDB), informasi kontak, dan layanan digital lainnya.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ==============================================
     1B. SECTION JADWAL SHOLAT (API REAL-TIME)
     ============================================== -->
<section class="prayer-section">
    <div class="container">
        <div class="text-center mb-4">
            <h3 class="fw-bold mb-2" style="letter-spacing: -0.5px;">Jadwal Sholat Hari Ini</h3>
            <p class="mb-0 text-white-50 small">
                <i class="fas fa-map-marker-alt text-warning me-1"></i> Kasui, Way Kanan & Sekitarnya | <span id="hijri-date">Memuat kalender...</span>
            </p>
        </div>
        
        <div class="row justify-content-center g-3" id="prayer-times-container">
            <!-- Fallback Loader UI sebelum data API ditarik -->
            <div class="col-12 text-center text-white-50 small" id="prayer-loader">
                <div class="spinner-border spinner-border-sm me-2" role="status"></div> Mengambil data waktu sholat...
            </div>
        </div>
    </div>
</section>

<!-- ==============================================
     1C. SECTION TENTANG PONDOK (SEKILAS ABOUT US)
     ============================================== -->
<section id="tentang" class="py-5" style="background-color: #ffffff; padding-top: 80px !important; padding-bottom: 80px !important;">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-5 order-2 order-lg-1 d-none d-lg-block">
                <div class="position-relative">
                    <?php 
                        $foto_about = $foto_tentang;
                        if(empty($foto_about)){
                            $foto_about = 'https://images.unsplash.com/photo-1584697964400-2af6a2f62651?q=80&w=800&h=600&fit=crop';
                        }
                    ?>
                    <img src="<?php echo htmlspecialchars($foto_about); ?>" class="img-fluid rounded-4 shadow" alt="Tentang Pesantren" style="width: 100%; height: auto; object-fit: cover; border: 4px solid var(--kemenag-green-light);">
                    <div style="position: absolute; bottom: -20px; right: -20px; width: 100px; height: 100px; background-image: radial-gradient(var(--kemenag-green-primary) 20%, transparent 20%); background-size: 10px 10px; z-index: -1; opacity: 0.3;"></div>
                </div>
            </div>
            
            <div class="col-lg-7 order-1 order-lg-2 text-start">
                <span class="section-subtitle">Sekilas Profil</span>
                <h2 class="section-title mb-4" style="font-size: 2rem;">Tentang <?php echo htmlspecialchars($nama_pesantren); ?></h2>
                <div class="deco-line ms-0 mb-4"></div>
                
                <p class="text-muted" style="line-height: 1.8; font-size: 1.05rem;">
                    <?php 
                        $excerpt = strip_tags($tentang_pondok);
                        if (strlen($excerpt) > 300) {
                            echo htmlspecialchars(substr($excerpt, 0, 300)) . '...';
                        } else {
                            echo htmlspecialchars($excerpt);
                        }
                    ?>
                </p>
                
                <div class="mt-4 pt-2">
                    <a href="tentang.php" class="btn btn-pill-outline">
                        Baca Selengkapnya <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ==============================================
     2. SECTION SAMBUTAN PEJABAT (DINAMIS DARI DATABASE)
     ============================================== -->
<section id="sambutan" class="py-5" style="background-color: var(--light-neutral); padding-top: 80px !important; padding-bottom: 80px !important;">
    <div class="container">
        <div class="text-center mb-5">
            <span class="section-subtitle">Sambutan Pimpinan</span>
            <h2 class="section-title">Sambutan Petinggi Pondok</h2>
            <div class="deco-line"></div>
        </div>

        <div class="row g-4 justify-content-center">
            <?php foreach ($sambutan_list as $s): ?>
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 modern-card text-center p-4" style="background-color: #ffffff !important; border-top: 4px solid var(--kemenag-green-primary) !important;">
                    <div class="mb-4 mt-2 mx-auto" style="width: 100px; height: 100px; border-radius: 50%; padding: 4px; background: white; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                        <?php 
                            $foto_pejabat = $s['foto'];
                            if (empty($foto_pejabat)) {
                                $foto_pejabat = 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?q=80&w=256&h=256&fit=crop';
                            }
                        ?>
                        <img src="<?php echo htmlspecialchars($foto_pejabat); ?>" class="w-100 h-100 rounded-circle object-fit-cover" alt="Foto Pejabat">
                    </div>
                    <h5 class="fw-bold mb-1" style="color: var(--dark-neutral); font-size: 1.15rem;">
                        <?php echo htmlspecialchars($s['nama_pejabat']); ?>
                    </h5>
                    <span class="badge-modern mb-3" style="padding: 4px 12px; font-size: 0.75rem;">
                        <?php echo htmlspecialchars($s['jabatan']); ?>
                    </span>
                    <p class="small mb-0" style="color: var(--text-muted-custom); line-height: 1.7; font-style: italic;">
                        "<?php echo htmlspecialchars($s['isi_sambutan']); ?>"
                    </p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ==============================================
     3. SECTION INFORMASI SEKTOR (PRESTASI)
     ============================================== -->
<section id="informasi-sektor" class="py-5" style="background-color: #ffffff; padding-top: 80px !important; padding-bottom: 80px !important;">
    <div class="container">
        <div class="text-center mb-5">
            <span class="section-subtitle">Informasi Sektor</span>
            <h2 class="section-title">Prestasi Para Santri</h2>
            <div class="deco-line"></div>
        </div>
        
        <div class="row g-4 justify-content-center">
            <?php foreach ($prestasi_list as $p): ?>
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 modern-card" style="background-color: var(--light-neutral) !important;">
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
                        <p class="small mb-4" style="color: var(--text-muted-custom); line-height: 1.7; height: 75px; overflow: hidden;">
                            <?php echo htmlspecialchars($p['deskripsi']); ?>
                        </p>
                        <div class="pt-3 border-top d-flex justify-content-between align-items-center" style="border-color: rgba(0,0,0,0.05) !important;">
                            <span class="small text-muted fw-medium">Raudlatul Muta'allimin - <?php echo htmlspecialchars($p['tahun']); ?></span>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="text-center mt-5">
            <a href="prestasi.php" class="btn btn-pill-outline">
                <i class="fas fa-award me-2"></i> Lihat Prestasi Lainnya
            </a>
        </div>
    </div>
</section>

<!-- ==============================================
     6B. SECTION PARALLAX CALL TO ACTION (CTA)
     ============================================== -->
<section class="parallax-cta">
    <div class="container">
        <h2 class="fw-bold mb-3" style="font-size: 2.5rem; letter-spacing: -0.5px;">Pendaftaran Santri Baru Online</h2>
        <p class="lead mb-4 mx-auto" style="max-width: 800px; font-size: 1.15rem; font-weight: 300; opacity: 0.95; line-height: 1.6;">
            Jadilah bagian dari generasi unggul berikutnya. Bergabunglah dengan keluarga besar <?php echo htmlspecialchars($nama_pesantren); ?>. Daftar sekarang juga, di sini.
        </p>
        <a href="#" class="btn-cta-white">Daftar Sekarang</a>
    </div>
</section>

<!-- ==============================================
     7. SECTION DOKUMENTASI KEGIATAN (DINAMIS DARI DATABASE)
     ============================================== -->
<section id="dokumentasi" class="py-5" style="background-color: var(--light-neutral); padding-top: 80px !important; padding-bottom: 80px !important;">
    <div class="container">
        <div class="text-center mb-5">
            <span class="section-subtitle">Galeri Dokumentasi</span>
            <h2 class="section-title">Dokumentasi Aktivitas Santri</h2>
            <div class="deco-line"></div>
        </div>

        <div class="row g-3">
            <?php foreach ($galeri_list as $g): ?>
            <div class="col-lg-4 col-md-6 col-6">
                <div class="gallery-item rounded-4 overflow-hidden position-relative ratio ratio-1x1 shadow-sm">
                    <?php 
                        $foto_file = isset($g['file_foto']) ? $g['file_foto'] : 'https://images.unsplash.com/photo-1577896851231-70ef18881754?q=80&w=600&h=600&fit=crop';
                    ?>
                    <img src="<?php echo $foto_file; ?>" class="img-fluid object-fit-cover" alt="Galeri">
                    <div class="gallery-overlay d-flex align-items-center justify-content-center">
                        <span class="text-white fw-semibold text-center p-2 small">
                            <?php echo htmlspecialchars($g['judul_kegiatan'] ?? 'Kegiatan Pondok'); ?>
                        </span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="text-center mt-5">
            <a href="galeri.php" class="btn btn-pill-outline">
                <i class="fas fa-images me-2"></i> Lihat Galeri Lainnya
            </a>
        </div>
    </div>
</section>

<!-- CSS Kustom Galeri Dokumentasi Minimalis -->
<style>
    .gallery-item img {
        transition: var(--transition-smooth);
        cursor: pointer;
    }
    .gallery-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 168, 107, 0.95);
        opacity: 0;
        transition: var(--transition-smooth);
    }
    .gallery-item:hover img {
        transform: scale(1.06);
    }
    .gallery-item:hover .gallery-overlay {
        opacity: 1;
    }
</style>

<!-- ==============================================
     6. SECTION BERITA & PENGUMUMAN (DINAMIS DARI DATABASE)
     ============================================== -->
<section id="berita" class="py-5" style="background-color: #ffffff; padding-top: 80px !important; padding-bottom: 80px !important;">
    <div class="container">
        <div class="text-center mb-5">
            <span class="section-subtitle">Warta Pesantren</span>
            <h2 class="section-title">Berita & Kegiatan Terbaru</h2>
            <div class="deco-line"></div>
        </div>

        <div class="row g-4">
            <?php foreach ($berita_list as $b): ?>
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 modern-card" style="background-color: var(--light-neutral) !important;">
                    <div class="position-relative overflow-hidden" style="height: 220px;">
                        <?php 
                            $cover_berita = $b['gambar_cover'];
                            if (empty($cover_berita)) {
                                $cover_berita = 'https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?q=80&w=600&h=400&fit=crop';
                            }
                        ?>
                        <img src="<?php echo $cover_berita; ?>" class="w-100 h-100" alt="Cover Berita" style="object-fit: cover;">
                    </div>
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="badge-modern" style="padding: 4px 12px; font-size: 0.7rem;">
                                <?php echo isset($b['penulis']) ? htmlspecialchars($b['penulis']) : 'Admin'; ?>
                            </span>
                            <span class="small" style="color: var(--text-muted-custom);">
                                <i class="far fa-calendar-alt me-1"></i> 
                                <?php 
                                    $tgl_raw = isset($b['tanggal_publish']) ? $b['tanggal_publish'] : date('Y-m-d');
                                    echo date('d M Y', strtotime($tgl_raw)); 
                                ?>
                            </span>
                        </div>
                        <h5 class="fw-bold mb-2" style="color: var(--dark-neutral); font-size: 1.15rem; line-height: 1.4;">
                            <?php echo htmlspecialchars($b['judul']); ?>
                        </h5>
                        <p class="small mb-4" style="color: var(--text-muted-custom); line-height: 1.6;">
                            <?php 
                                $ringkasan = isset($b['isi_berita']) ? strip_tags($b['isi_berita']) : '';
                                echo htmlspecialchars(substr($ringkasan, 0, 100)) . '...'; 
                            ?>
                        </p>
                        <a href="detail_berita.php?id=<?php echo $b['id_berita']; ?>" class="text-decoration-none fw-bold small" style="color: var(--kemenag-green-primary) !important;">Baca Detail Berita <i class="fas fa-arrow-right ms-1"></i></a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="text-center mt-5">
            <a href="berita.php" class="btn btn-pill-outline">
                <i class="fas fa-newspaper me-2"></i> Lihat Berita Lainnya
            </a>
        </div>
    </div>
</section>

<!-- ==============================================
     8. SECTION TESTIMONI ALUMNI (DINAMIS DARI DATABASE)
     ============================================== -->
<section id="testimoni" class="py-5" style="background-color: var(--light-neutral); padding-top: 80px !important; padding-bottom: 80px !important;">
    <div class="container">
        <div class="text-center mb-5">
            <span class="section-subtitle">Dengar Cerita Mereka</span>
            <h2 class="section-title">Testimoni Alumni Sukses</h2>
            <div class="deco-line"></div>
        </div>

        <div id="testimoniCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <?php foreach ($testimoni_list as $index => $t): ?>
                <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                    <div class="row justify-content-center">
                        <div class="col-lg-8 col-md-10">
                            <div class="card p-4 p-md-5 text-center bg-white modern-card" style="background-color: #ffffff !important;">
                                <i class="fas fa-quote-left fs-2 text-opacity-25 mb-4" style="color: var(--kemenag-green-primary) !important; opacity: 0.2 !important;"></i>
                                <p class="lead mb-4" style="font-size: 1.05rem; line-height: 1.8; font-style: italic; color: var(--text-muted-custom);">
                                    "<?php echo htmlspecialchars($t['isi_testimoni'] ?? $t['isi']); ?>"
                                </p>
                                <div class="d-flex align-items-center justify-content-center gap-3 mt-3">
                                    <?php 
                                        $foto_alumni = $t['foto'];
                                        if ($foto_alumni === 'default_alumni.jpg' || empty($foto_alumni)) {
                                            $foto_alumni = 'https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?q=80&w=120&h=120&fit=crop';
                                        }
                                    ?>
                                    <img src="<?php echo $foto_alumni; ?>" class="rounded-circle shadow-sm" alt="Foto Alumni" style="width: 55px; height: 55px; object-fit: cover; border: 2px solid var(--kemenag-green-primary);">
                                    <div class="text-start">
                                        <h6 class="fw-bold mb-0" style="color: var(--dark-neutral);"><?php echo htmlspecialchars($t['nama_alumni'] ?? $t['nama']); ?></h6>
                                        <small class="fw-semibold" style="color: var(--kemenag-green-primary);"><?php echo htmlspecialchars($t['profesi_angkatan'] ?? $t['angkatan']); ?></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <button class="carousel-control-prev d-none d-md-flex text-dark" type="button" data-bs-target="#testimoniCarousel" data-bs-slide="prev" style="width: 50px;">
                <span class="fas fa-arrow-left fs-4" aria-hidden="true" style="color: var(--kemenag-green-primary);"></span>
            </button>
            <button class="carousel-control-next d-none d-md-flex text-dark" type="button" data-bs-target="#testimoniCarousel" data-bs-slide="next" style="width: 50px;">
                <span class="fas fa-arrow-right fs-4" aria-hidden="true" style="color: var(--kemenag-green-primary);"></span>
            </button>
        </div>
    </div>
</section>

<!-- ==============================================
     9. SECTION HUBUNGI KAMI / KONTAK (CLEAN BOX)
     ============================================== -->
<section id="kontak" class="py-5" style="background-color: #ffffff; padding-top: 80px !important; padding-bottom: 80px !important;">
    <div class="container">
        <div class="text-center mb-5">
            <span class="section-subtitle">Sektor Komunikasi</span>
            <h2 class="section-title">Hubungi Sekretariat Yayasan</h2>
            <div class="deco-line"></div>
        </div>

        <div class="row g-4 align-items-stretch">
            <div class="col-lg-5">
                <div class="p-4 p-md-5 rounded-4 text-white h-100 d-flex flex-column justify-content-between" style="background-color: var(--dark-neutral);">
                    <div>
                        <h4 class="fw-bold mb-4" style="color: #ffffff; letter-spacing: -0.5px;">Kantor Sekretariat</h4>
                        
                        <div class="d-flex gap-3 align-items-start mb-4">
                            <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background-color: rgba(255,255,255,0.05); color: var(--kemenag-green-primary); flex-shrink: 0;">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-white mb-1">Alamat Utama</h6>
                                <p class="small mb-0 opacity-75"><?php echo htmlspecialchars($alamat_pesantren); ?></p>
                            </div>
                        </div>

                        <div class="d-flex gap-3 align-items-start mb-4">
                            <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background-color: rgba(255,255,255,0.05); color: var(--kemenag-green-primary); flex-shrink: 0;">
                                <i class="fas fa-phone"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-white mb-1">Telepon Humas</h6>
                                <p class="small mb-0 opacity-75"><?php echo htmlspecialchars($no_telp); ?></p>
                            </div>
                        </div>

                        <div class="d-flex gap-3 align-items-start mb-4">
                            <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background-color: rgba(255,255,255,0.05); color: var(--kemenag-green-primary); flex-shrink: 0;">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-white mb-1">E-mail Resmi</h6>
                                <p class="small mb-0 opacity-75"><?php echo htmlspecialchars($email_pesantren); ?></p>
                            </div>
                        </div>
                    </div>

                    <a href="https://api.whatsapp.com/send?phone=<?php echo preg_replace('/[^0-9]/', '', $no_telp); ?>" target="_blank" class="btn btn-pill-primary w-100 justify-content-center py-3 mt-4">
                        <i class="fab fa-whatsapp fs-5"></i> Chat Whatsapp Sekarang
                    </a>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="card p-4 p-md-5 rounded-4 bg-white h-100" style="border: 1px solid rgba(0,0,0,0.05); box-shadow: 0 10px 30px -10px rgba(0,0,0,0.03) !important;">
                    <h4 class="fw-bold mb-4" style="color: var(--dark-neutral); letter-spacing: -0.5px;">Kirim Surat Elektronik</h4>
                    <form action="index.php#kontak" method="POST">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="namaKontak" class="form-label text-dark fw-semibold small">Nama Lengkap</label>
                                <input type="text" class="form-control rounded-3 py-2 border-light-subtle" id="namaKontak" name="nama_pengirim" placeholder="Masukkan nama Anda" required>
                            </div>
                            <div class="col-md-6">
                                <label for="emailKontak" class="form-label text-dark fw-semibold small">Alamat Email (Opsional)</label>
                                <input type="email" class="form-control rounded-3 py-2 border-light-subtle" id="emailKontak" name="email" placeholder="Alamat email Anda">
                            </div>
                            <div class="col-12">
                                <label for="subjekKontak" class="form-label text-dark fw-semibold small">Subjek Pesan</label>
                                <input type="text" class="form-control rounded-3 py-2 border-light-subtle" id="subjekKontak" name="subjek" placeholder="Pertanyaan seputar pendaftaran / sarana prasarana" required>
                            </div>
                            <div class="col-12">
                                <label for="pesanKontak" class="form-label text-dark fw-semibold small">Isi Pesan / Pertanyaan</label>
                                <textarea class="form-control rounded-3 border-light-subtle" id="pesanKontak" name="pesan" rows="4" placeholder="Tuliskan pesan atau pertanyaan Anda di sini..." required></textarea>
                            </div>
                            <div class="col-12 text-end">
                                <button type="submit" name="kirim_pesan" class="btn btn-pill-primary px-5 py-2 text-white"><i class="fas fa-paper-plane"></i> Kirim Pesan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ==============================================
     10. FLOATING BADGE (STATUS PENGEMBANGAN)
     ============================================== -->
<div class="dev-badge text-decoration-none" title="Website ini masih dalam tahap pembangunan.">
    <div class="spinner-grow spinner-grow-sm text-warning" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
    <span class="fw-bold small m-0 p-0" style="font-size: 0.75rem;">Tahap Pengembangan</span>
</div>

<!-- Eksekusi SweetAlert2 & JS Fetch Jadwal Sholat -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // JS Untuk API Jadwal Sholat Aladhan (Gratis & Real-time)
    document.addEventListener("DOMContentLoaded", function() {
        const url = 'https://api.aladhan.com/v1/timingsByCity?city=Way%20Kanan&country=Indonesia&method=11'; // Method 11 = Kemenag RI
        
        fetch(url)
            .then(response => response.json())
            .then(data => {
                if(data.code === 200) {
                    const timings = data.data.timings;
                    const hijri = data.data.date.hijri;
                    
                    // Update kalender Hijriah
                    document.getElementById('hijri-date').innerText = `${hijri.day} ${hijri.month.en} ${hijri.year} H`;
                    
                    // Siapkan array data sholat untuk loop
                    const prayers = [
                        { name: 'Subuh', time: timings.Fajr, icon: 'fa-cloud-moon' },
                        { name: 'Terbit', time: timings.Sunrise, icon: 'fa-sun text-opacity-50' },
                        { name: 'Dzuhur', time: timings.Dhuhr, icon: 'fa-sun' },
                        { name: 'Ashar', time: timings.Asr, icon: 'fa-cloud-sun' },
                        { name: 'Maghrib', time: timings.Maghrib, icon: 'fa-cloud-sun-rain' },
                        { name: 'Isya', time: timings.Isha, icon: 'fa-moon' }
                    ];
                    
                    // Render HTML
                    let html = '';
                    prayers.forEach(p => {
                        html += `
                            <div class="col-4 col-md-2">
                                <div class="prayer-card shadow-sm h-100">
                                    <i class="fas ${p.icon} prayer-icon"></i>
                                    <p class="small fw-semibold mb-1 opacity-75">${p.name}</p>
                                    <h5 class="fw-bold mb-0 text-white">${p.time}</h5>
                                </div>
                            </div>
                        `;
                    });
                    
                    document.getElementById('prayer-times-container').innerHTML = html;
                }
            })
            .catch(error => {
                console.error("Gagal mengambil jadwal sholat:", error);
                document.getElementById('prayer-loader').innerHTML = '<span class="text-danger"><i class="fas fa-exclamation-triangle"></i> Gagal memuat jadwal sholat.</span>';
            });
    });

    // Alert Notifikasi Kirim Pesan
    <?php if (!is_null($status_pesan)): ?>
        <?php if ($status_pesan === 'success'): ?>
        Swal.fire({
            icon: 'success',
            title: 'Pesan Terkirim!',
            text: 'Terima kasih, pesan Anda berhasil disimpan ke database. Sekretariat kami akan segera merespon.',
            confirmButtonColor: '#00a86b'
        });
        <?php elseif ($status_pesan === 'validation_error'): ?>
        Swal.fire({
            icon: 'warning',
            title: 'Isian Kosong!',
            text: 'Harap isi kolom Nama dan Pesan sebelum mengirimkan formulir.',
            confirmButtonColor: '#00a86b'
        });
        <?php else: ?>
        Swal.fire({
            icon: 'error',
            title: 'Gagal Mengirim!',
            text: 'Terjadi kesalahan sistem saat mencoba mengirimkan pesan Anda.',
            confirmButtonColor: '#d33'
        });
        <?php endif; ?>
    <?php endif; ?>
</script>

<?php
// Memasukkan footer yang terpusat
include 'footer.php';
?>