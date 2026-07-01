<?php
/**
 * File: admin/profil.php
 * Deskripsi: Halaman kelola Profil, Kontak, Tentang, Sejarah, dan Visi Misi.
 * Terintegrasi dengan form tabulasi dan editor Summernote untuk teks panjang.
 */

session_start();

// Proteksi Keamanan
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

include '../koneksi.php';

$status_aksi = null;

// Direktori Upload
$upload_logo = '../uploads/logo/';
if (!file_exists($upload_logo)) mkdir($upload_logo, 0777, true);

$upload_tentang = '../uploads/tentang/';
if (!file_exists($upload_tentang)) mkdir($upload_tentang, 0777, true);

// ==============================================
// PROSES UPDATE PROFIL WEB
// ==============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profil'])) {
    
    // Tab 1: Identitas & Kontak
    $nama_pesantren = mysqli_real_escape_string($conn, trim($_POST['nama_pesantren']));
    $alamat         = mysqli_real_escape_string($conn, trim($_POST['alamat']));
    $no_telepon     = mysqli_real_escape_string($conn, trim($_POST['no_telepon']));
    $email          = mysqli_real_escape_string($conn, trim($_POST['email']));
    $whatsapp       = mysqli_real_escape_string($conn, trim($_POST['whatsapp']));
    $facebook       = mysqli_real_escape_string($conn, trim($_POST['facebook']));
    $instagram      = mysqli_real_escape_string($conn, trim($_POST['instagram']));
    $youtube        = mysqli_real_escape_string($conn, trim($_POST['youtube']));
    
    // Tab 2: Tentang & Sejarah (HTML Allowed for Summernote)
    $tentang_pondok = mysqli_real_escape_string($conn, trim($_POST['tentang_pondok']));
    $sejarah_pondok = mysqli_real_escape_string($conn, trim($_POST['sejarah_pondok']));
    
    // Tab 3: Visi Misi
    $visi = mysqli_real_escape_string($conn, trim($_POST['visi']));
    $misi = mysqli_real_escape_string($conn, trim($_POST['misi']));

    $logo_path = mysqli_real_escape_string($conn, $_POST['logo_lama']);
    $foto_tentang_path = mysqli_real_escape_string($conn, $_POST['foto_tentang_lama']);

    // Proses Upload Logo Baru
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        $file_ext = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
        if (in_array($file_ext, ['jpg', 'jpeg', 'png', 'webp'])) {
            $new_logo = 'logo_' . time() . '.' . $file_ext;
            if (move_uploaded_file($_FILES['logo']['tmp_name'], $upload_logo . $new_logo)) {
                if (!empty($logo_path) && file_exists('../' . $logo_path)) unlink('../' . $logo_path);
                $logo_path = 'uploads/logo/' . $new_logo;
            }
        }
    }

    // Proses Upload Foto Tentang Baru
    if (isset($_FILES['foto_tentang']) && $_FILES['foto_tentang']['error'] === UPLOAD_ERR_OK) {
        $file_ext = strtolower(pathinfo($_FILES['foto_tentang']['name'], PATHINFO_EXTENSION));
        if (in_array($file_ext, ['jpg', 'jpeg', 'png', 'webp'])) {
            $new_foto = 'tentang_' . time() . '.' . $file_ext;
            if (move_uploaded_file($_FILES['foto_tentang']['tmp_name'], $upload_tentang . $new_foto)) {
                if (!empty($foto_tentang_path) && file_exists('../' . $foto_tentang_path)) unlink('../' . $foto_tentang_path);
                $foto_tentang_path = 'uploads/tentang/' . $new_foto;
            }
        }
    }

    // Eksekusi Update
    $query_update = "UPDATE profil_web SET 
        nama_pesantren = '$nama_pesantren',
        logo = '$logo_path',
        alamat = '$alamat',
        no_telepon = '$no_telepon',
        email = '$email',
        whatsapp = '$whatsapp',
        facebook = '$facebook',
        instagram = '$instagram',
        youtube = '$youtube',
        tentang_pondok = '$tentang_pondok',
        sejarah_pondok = '$sejarah_pondok',
        foto_tentang = '$foto_tentang_path',
        visi = '$visi',
        misi = '$misi'
        WHERE id_profil = 1";

    if (mysqli_query($conn, $query_update)) {
        $status_aksi = 'success';
    } else {
        $status_aksi = 'error';
    }
}

// Mengambil Data Profil Saat Ini
$query_profil = mysqli_query($conn, "SELECT * FROM profil_web WHERE id_profil = 1 LIMIT 1");
$data = mysqli_fetch_assoc($query_profil);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Tentang Pondok - Admin</title>
    
    <!-- CSS Dependencies -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-bootstrap-4/bootstrap-4.css" rel="stylesheet">
    
    <!-- Summernote Lite CSS -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">

    <style>
        :root {
            --bg-neutral-light: #f4f6f9;
            --kemenag-green: #00a86b;
            --kemenag-green-dark: #007d4f;
            --slate-900: #0f172a;
            --slate-500: #64748b;
            --transition-smooth: all 0.3s ease;
        }

        html, body {
            height: 100vh;
            overflow: hidden; 
            background-color: var(--bg-neutral-light);
            font-family: 'Poppins', sans-serif;
        }

        .admin-main-content-scroll {
            height: 100vh;
            overflow-y: auto; 
            padding: 30px;
        }
        .admin-main-content-scroll::-webkit-scrollbar { width: 6px; }
        .admin-main-content-scroll::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.1); border-radius: 10px; }

        .page-header-card {
            background-color: #ffffff;
            border-radius: 20px;
            padding: 24px 30px;
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.02);
            border: 1px solid rgba(0, 0, 0, 0.03);
            margin-bottom: 25px;
            display: flex;
            align-items: center;
        }

        .header-icon {
            width: 50px;
            height: 50px;
            border-radius: 14px;
            background-color: #e6f7f1;
            color: var(--kemenag-green);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
        }

        .form-card {
            background-color: #ffffff;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.02);
            border: 1px solid rgba(0, 0, 0, 0.03);
        }

        .form-control-custom {
            background-color: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 12px !important;
            padding: 12px 16px !important;
            font-size: 0.9rem !important;
        }
        .form-control-custom:focus {
            border-color: var(--kemenag-green) !important;
            background-color: #ffffff !important;
            box-shadow: 0 0 0 4px rgba(0, 168, 107, 0.1) !important;
        }

        .btn-pill-primary {
            background-color: var(--kemenag-green) !important;
            color: #ffffff !important;
            border: none !important;
            border-radius: 50px !important;
            padding: 12px 30px !important;
            font-weight: 600 !important;
            transition: var(--transition-smooth) !important;
            box-shadow: 0 10px 25px -5px rgba(0, 168, 107, 0.3) !important;
        }
        .btn-pill-primary:hover {
            background-color: var(--kemenag-green-dark) !important;
            transform: translateY(-2px);
        }

        /* Custom Tabs Styling */
        .nav-pills-custom .nav-link {
            color: var(--slate-500);
            font-weight: 600;
            border-radius: 12px;
            padding: 12px 24px;
            margin-right: 10px;
            transition: var(--transition-smooth);
            background-color: #f1f5f9;
        }
        .nav-pills-custom .nav-link.active {
            background-color: var(--kemenag-green);
            color: #ffffff;
            box-shadow: 0 4px 15px rgba(0,168,107,0.2);
        }

        /* Override Summernote Style */
        .note-editor.note-frame {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            overflow: hidden;
        }
        .note-toolbar {
            background-color: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>

    <div class="container-fluid p-0">
        <div class="row g-0">
            
            <!-- Sidebar -->
            <div class="col-lg-3 col-xl-2 d-none d-lg-block">
                <?php include 'sidebar.php'; ?>
            </div>

            <!-- Konten Utama -->
            <div class="col-lg-9 col-xl-10">
                <div class="admin-main-content-scroll">
                    
                    <div class="page-header-card gap-3">
                        <div class="header-icon">
                            <i class="fas fa-building"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold text-dark mb-1" style="font-weight: 800; letter-spacing: -0.5px;">Tentang Pondok</h4>
                            <p class="text-muted small mb-0">Kelola identitas, sejarah, sekilas info, dan visi misi pesantren.</p>
                        </div>
                    </div>

                    <div class="form-card">
                        <form action="profil.php" method="POST" enctype="multipart/form-data">
                            
                            <!-- Tab Navigation -->
                            <ul class="nav nav-pills nav-pills-custom mb-4" id="pills-tab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="pills-identitas-tab" data-bs-toggle="pill" data-bs-target="#pills-identitas" type="button" role="tab" aria-selected="true"><i class="fas fa-id-card me-2"></i> Identitas & Kontak</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="pills-tentang-tab" data-bs-toggle="pill" data-bs-target="#pills-tentang" type="button" role="tab" aria-selected="false"><i class="fas fa-history me-2"></i> Tentang & Sejarah</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="pills-visi-tab" data-bs-toggle="pill" data-bs-target="#pills-visi" type="button" role="tab" aria-selected="false"><i class="fas fa-bullseye me-2"></i> Visi & Misi</button>
                                </li>
                            </ul>

                            <!-- Tab Content -->
                            <div class="tab-content" id="pills-tabContent">
                                
                                <!-- TAB 1: IDENTITAS & KONTAK -->
                                <div class="tab-pane fade show active" id="pills-identitas" role="tabpanel" tabindex="0">
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold small">Nama Pesantren</label>
                                            <input type="text" name="nama_pesantren" class="form-control form-control-custom" value="<?php echo htmlspecialchars($data['nama_pesantren'] ?? ''); ?>" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold small">Logo Website (Opsional)</label>
                                            <input type="hidden" name="logo_lama" value="<?php echo htmlspecialchars($data['logo'] ?? ''); ?>">
                                            <input type="file" name="logo" class="form-control form-control-custom" accept="image/png, image/jpeg, image/webp">
                                            <?php if (!empty($data['logo'])): ?>
                                                <small class="text-success mt-1 d-block"><i class="fas fa-check-circle"></i> Logo saat ini telah terpasang.</small>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label fw-semibold small">Alamat Lengkap</label>
                                            <textarea name="alamat" class="form-control form-control-custom" rows="2" required><?php echo htmlspecialchars($data['alamat'] ?? ''); ?></textarea>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold small">Nomor Telepon</label>
                                            <input type="text" name="no_telepon" class="form-control form-control-custom" value="<?php echo htmlspecialchars($data['no_telepon'] ?? ''); ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold small">Email Resmi</label>
                                            <input type="email" name="email" class="form-control form-control-custom" value="<?php echo htmlspecialchars($data['email'] ?? ''); ?>">
                                        </div>
                                        
                                        <div class="col-12 mt-4 border-top pt-4">
                                            <h6 class="fw-bold mb-3"><i class="fas fa-share-alt me-2 text-muted"></i>Tautan Sosial Media</h6>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold small">Link WhatsApp</label>
                                            <input type="url" name="whatsapp" class="form-control form-control-custom" placeholder="https://wa.me/..." value="<?php echo htmlspecialchars($data['whatsapp'] ?? ''); ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold small">Link Facebook</label>
                                            <input type="url" name="facebook" class="form-control form-control-custom" placeholder="https://facebook.com/..." value="<?php echo htmlspecialchars($data['facebook'] ?? ''); ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold small">Link Instagram</label>
                                            <input type="url" name="instagram" class="form-control form-control-custom" placeholder="https://instagram.com/..." value="<?php echo htmlspecialchars($data['instagram'] ?? ''); ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold small">Link YouTube</label>
                                            <input type="url" name="youtube" class="form-control form-control-custom" placeholder="https://youtube.com/..." value="<?php echo htmlspecialchars($data['youtube'] ?? ''); ?>">
                                        </div>
                                    </div>
                                </div>

                                <!-- TAB 2: TENTANG & SEJARAH -->
                                <div class="tab-pane fade" id="pills-tentang" role="tabpanel" tabindex="0">
                                    <div class="row g-4">
                                        <div class="col-12">
                                            <div class="alert alert-info border-0 bg-opacity-10 text-dark small mb-0">
                                                <i class="fas fa-info-circle text-primary me-2"></i> Bagian <strong>Sekilas Tentang</strong> akan muncul di halaman beranda depan. Sedangkan bagian <strong>Sejarah Lengkap</strong> akan muncul di halaman khusus profil pondok.
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <label class="form-label fw-semibold small">Foto Pendukung (Akan Muncul di Beranda Depan)</label>
                                            <input type="hidden" name="foto_tentang_lama" value="<?php echo htmlspecialchars($data['foto_tentang'] ?? ''); ?>">
                                            <input type="file" name="foto_tentang" class="form-control form-control-custom" accept="image/png, image/jpeg, image/webp">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label fw-semibold small">Sekilas Tentang Pondok</label>
                                            <textarea name="tentang_pondok" class="summernote-editor"><?php echo htmlspecialchars($data['tentang_pondok'] ?? ''); ?></textarea>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label fw-semibold small">Sejarah Lengkap Raudlatul Muta'allimin</label>
                                            <textarea name="sejarah_pondok" class="summernote-editor"><?php echo htmlspecialchars($data['sejarah_pondok'] ?? ''); ?></textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- TAB 3: VISI & MISI -->
                                <div class="tab-pane fade" id="pills-visi" role="tabpanel" tabindex="0">
                                    <div class="row g-4">
                                        <div class="col-12">
                                            <label class="form-label fw-semibold small">Visi Pesantren</label>
                                            <textarea name="visi" class="form-control form-control-custom" rows="3" placeholder="Tuliskan visi utama pondok pesantren..." required><?php echo htmlspecialchars($data['visi'] ?? ''); ?></textarea>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label fw-semibold small">Misi Pesantren (Gunakan Enter/Baris Baru untuk tiap poin)</label>
                                            <textarea name="misi" class="form-control form-control-custom" rows="8" placeholder="1. Menyelenggarakan...&#10;2. Menanamkan..." required><?php echo htmlspecialchars($data['misi'] ?? ''); ?></textarea>
                                            <small class="text-muted mt-2 d-block"><i class="fas fa-lightbulb text-warning"></i> Tips: Setiap Anda menekan tombol Enter (baris baru), di halaman depan otomatis akan dibentuk menjadi satu poin daftar (bullet point).</small>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            
                            <hr class="my-5" style="border-color: #e2e8f0;">
                            <div class="text-end">
                                <button type="submit" name="update_profil" class="btn btn-pill-primary px-5">
                                    <i class="fas fa-save me-2"></i> Simpan Semua Perubahan
                                </button>
                            </div>

                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>

    <script>
        $(document).ready(function() {
            // Inisialisasi Summernote
            $('.summernote-editor').summernote({
                height: 250,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'italic', 'underline', 'clear']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['insert', ['link', 'hr']],
                    ['view', ['fullscreen', 'codeview']]
                ],
                placeholder: 'Ketikkan konten selengkapnya di sini...'
            });
        });

        // Notifikasi SweetAlert
        <?php if ($status_aksi === 'success'): ?>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Data profil dan tentang pondok berhasil diperbarui.',
                confirmButtonColor: '#00a86b'
            });
        <?php elseif ($status_aksi === 'error'): ?>
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: 'Terjadi kesalahan sistem saat menyimpan ke database.',
                confirmButtonColor: '#d33'
            });
        <?php endif; ?>
    </script>
</body>
</html>