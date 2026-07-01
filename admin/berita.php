<?php
/**
 * File: admin/berita.php
 * Deskripsi: Halaman kelola data warta berita / artikel pondok pesantren.
 * Fitur: Tambah (Summernote & Upload Cover), Tampil, Edit, Hapus (CRUD).
 */

session_start();

// Proteksi Keamanan: Wajib Login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

// Menghubungkan ke database
include '../koneksi.php';

$status_aksi = null;

// Setup direktori upload cover berita
$upload_dir = '../uploads/berita/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Fungsi Bantuan untuk membuat URL Slug (contoh: "Berita Hari Ini" -> "berita-hari-ini")
function create_slug($string) {
    $slug = preg_replace('/[^A-Za-z0-9-]+/', '-', strtolower($string));
    return trim($slug, '-');
}

// ==============================================
// 1. PROSES TAMBAH BERITA
// ==============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_berita'])) {
    $judul           = mysqli_real_escape_string($conn, trim($_POST['judul']));
    $slug            = create_slug($judul);
    $isi_berita      = mysqli_real_escape_string($conn, $_POST['isi_berita']); // Hasil dari Summernote
    $tanggal_publish = mysqli_real_escape_string($conn, trim($_POST['tanggal_publish']));
    $penulis         = mysqli_real_escape_string($conn, trim($_POST['penulis']));
    
    $foto_path = '';

    // Proses Upload Cover Berita
    if (isset($_FILES['gambar_cover']) && $_FILES['gambar_cover']['error'] === UPLOAD_ERR_OK) {
        $file_tmp  = $_FILES['gambar_cover']['tmp_name'];
        $file_name = $_FILES['gambar_cover']['name'];
        $file_ext  = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed   = ['jpg', 'jpeg', 'png', 'webp'];
        
        if (in_array($file_ext, $allowed)) {
            $new_file_name = 'cover_' . time() . '_' . uniqid() . '.' . $file_ext;
            $dest_path = $upload_dir . $new_file_name;
            
            if (move_uploaded_file($file_tmp, $dest_path)) {
                $foto_path = 'uploads/berita/' . $new_file_name; 
            }
        }
    }

    $query = "INSERT INTO berita (judul, slug, isi_berita, gambar_cover, tanggal_publish, penulis) 
              VALUES ('$judul', '$slug', '$isi_berita', '$foto_path', '$tanggal_publish', '$penulis')";
              
    if (mysqli_query($conn, $query)) {
        $status_aksi = 'tambah_success';
    } else {
        $status_aksi = 'error';
    }
}

// ==============================================
// 2. PROSES EDIT BERITA
// ==============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_berita'])) {
    $id_berita       = (int) $_POST['id_berita'];
    $judul           = mysqli_real_escape_string($conn, trim($_POST['judul']));
    $slug            = create_slug($judul);
    $isi_berita      = mysqli_real_escape_string($conn, $_POST['isi_berita']);
    $tanggal_publish = mysqli_real_escape_string($conn, trim($_POST['tanggal_publish']));
    $penulis         = mysqli_real_escape_string($conn, trim($_POST['penulis']));
    
    $foto_path       = mysqli_real_escape_string($conn, trim($_POST['gambar_lama']));

    // Cek upload foto cover baru
    if (isset($_FILES['gambar_cover']) && $_FILES['gambar_cover']['error'] === UPLOAD_ERR_OK) {
        $file_tmp  = $_FILES['gambar_cover']['tmp_name'];
        $file_name = $_FILES['gambar_cover']['name'];
        $file_ext  = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed   = ['jpg', 'jpeg', 'png', 'webp'];
        
        if (in_array($file_ext, $allowed)) {
            $new_file_name = 'cover_' . time() . '_' . uniqid() . '.' . $file_ext;
            $dest_path = $upload_dir . $new_file_name;
            
            if (move_uploaded_file($file_tmp, $dest_path)) {
                // Hapus cover lama
                if (!empty($foto_path) && strpos($foto_path, 'uploads/') !== false && file_exists('../' . $foto_path)) {
                    unlink('../' . $foto_path);
                }
                $foto_path = 'uploads/berita/' . $new_file_name;
            }
        }
    }

    $query = "UPDATE berita SET 
                judul = '$judul',
                slug = '$slug',
                isi_berita = '$isi_berita',
                tanggal_publish = '$tanggal_publish',
                penulis = '$penulis',
                gambar_cover = '$foto_path'
              WHERE id_berita = $id_berita";
              
    if (mysqli_query($conn, $query)) {
        $status_aksi = 'edit_success';
    } else {
        $status_aksi = 'error';
    }
}

// ==============================================
// 3. PROSES HAPUS BERITA
// ==============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hapus_berita'])) {
    $id_hapus = (int) $_POST['id_hapus'];
    
    // Hapus fisik file cover
    $q_file = mysqli_query($conn, "SELECT gambar_cover FROM berita WHERE id_berita = $id_hapus");
    if ($q_file && mysqli_num_rows($q_file) > 0) {
        $data_file = mysqli_fetch_assoc($q_file);
        $foto_lama = $data_file['gambar_cover'];
        if (!empty($foto_lama) && strpos($foto_lama, 'uploads/') !== false && file_exists('../' . $foto_lama)) {
            unlink('../' . $foto_lama);
        }
    }
    
    $query = "DELETE FROM berita WHERE id_berita = $id_hapus";
    if (mysqli_query($conn, $query)) {
        $status_aksi = 'hapus_success';
    } else {
        $status_aksi = 'error';
    }
}

// Mengambil Data Berita
$query_berita = mysqli_query($conn, "SELECT * FROM berita ORDER BY tanggal_publish DESC, id_berita DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Berita - Admin PP Raudlatul Muta'allimin</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-bootstrap-4/bootstrap-4.css" rel="stylesheet">
    
    <!-- SUMMERNOTE LITE CSS -->
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
            color: var(--slate-900);
            font-family: 'Poppins', sans-serif;
        }

        /* Tata Letak Panel Utama */
        .admin-main-content-scroll {
            height: 100vh;
            overflow-y: auto; 
            padding: 30px;
        }

        .admin-main-content-scroll::-webkit-scrollbar { width: 6px; }
        .admin-main-content-scroll::-webkit-scrollbar-track { background: transparent; }
        .admin-main-content-scroll::-webkit-scrollbar-thumb { background: rgba(0, 0, 0, 0.08); border-radius: 10px; }

        /* Banner Card */
        .page-header-card {
            background-color: #ffffff;
            border-radius: 20px;
            padding: 24px 30px;
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.02);
            border: 1px solid rgba(0, 0, 0, 0.03);
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            justify-content: space-between;
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

        /* Widget Card */
        .widget-card {
            background-color: #ffffff;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.01);
            border: 1px solid rgba(0, 0, 0, 0.03);
        }

        /* Button Custom */
        .btn-pill-primary {
            background-color: var(--kemenag-green) !important;
            color: #ffffff !important;
            border: none !important;
            border-radius: 50px !important;
            padding: 10px 24px !important;
            font-weight: 600 !important;
            font-size: 0.9rem !important;
            transition: var(--transition-smooth) !important;
            box-shadow: 0 10px 25px -5px rgba(0, 168, 107, 0.3) !important;
        }

        .btn-pill-primary:hover {
            background-color: var(--kemenag-green-dark) !important;
            transform: translateY(-2px);
        }

        /* Table Custom */
        .table-custom {
            vertical-align: middle;
        }
        
        .table-custom thead th {
            background-color: #f8fafc;
            color: var(--slate-500);
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
            border-bottom: none;
            padding: 15px;
        }

        .table-custom tbody td {
            padding: 15px;
            color: var(--slate-900);
            font-size: 0.9rem;
            border-bottom: 1px solid rgba(0,0,0,0.03);
        }

        .thumbnail-table {
            width: 80px;
            height: 50px;
            border-radius: 8px;
            object-fit: cover;
            border: 1px solid rgba(0,0,0,0.1);
        }

        .btn-action {
            width: 35px;
            height: 35px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: none;
            transition: var(--transition-smooth);
            font-size: 0.85rem;
        }
        .btn-edit { background-color: #fffbeb; color: #d97706; }
        .btn-edit:hover { background-color: #fef3c7; transform: translateY(-2px); }
        .btn-delete { background-color: #fef2f2; color: #dc2626; }
        .btn-delete:hover { background-color: #fee2e2; transform: translateY(-2px); }

        /* Form Custom */
        .form-control-custom {
            background-color: var(--bg-neutral-light) !important;
            border: 1px solid rgba(0,0,0,0.05) !important;
            border-radius: 12px !important;
            padding: 12px 16px !important;
            font-size: 0.9rem !important;
        }
        .form-control-custom:focus {
            border-color: var(--kemenag-green) !important;
            background-color: #ffffff !important;
            box-shadow: 0 0 0 4px rgba(0, 168, 107, 0.1) !important;
        }

        /* Penyesuaian Summernote agar serasi dengan UI */
        .note-editor.note-frame {
            border-radius: 12px;
            border: 1px solid rgba(0,0,0,0.08);
            overflow: hidden;
        }
        .note-toolbar {
            background-color: #f8fafc !important;
            border-bottom: 1px solid rgba(0,0,0,0.05) !important;
        }
        /* Fix z-index untuk modal internal summernote (link/image dialog) */
        .note-modal {
            z-index: 1060 !important;
        }
    </style>
</head>
<body>

    <div class="container-fluid p-0" style="height: 100vh; overflow: hidden;">
        <div class="row g-0" style="height: 100vh; overflow: hidden;">
            
            <!-- Panel Kiri: Sidebar -->
            <div class="col-lg-3 col-xl-2 d-none d-lg-block" style="height: 100vh; overflow: hidden;">
                <?php include 'sidebar.php'; ?>
            </div>

            <!-- Panel Kanan: Area Konten -->
            <div class="col-lg-9 col-xl-10" style="height: 100vh; overflow: hidden;">
                <div class="admin-main-content-scroll">
                    
                    <!-- Page Header -->
                    <div class="page-header-card flex-column flex-md-row gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="header-icon">
                                <i class="fas fa-newspaper"></i>
                            </div>
                            <div>
                                <h4 class="fw-bold text-dark mb-1" style="font-weight: 800; letter-spacing: -0.5px;">Warta Berita</h4>
                                <p class="text-muted small mb-0">Publikasikan informasi, artikel, dan kegiatan pesantren terbaru.</p>
                            </div>
                        </div>
                        <div>
                            <button type="button" class="btn btn-pill-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
                                <i class="fas fa-pen me-2"></i> Tulis Berita
                            </button>
                        </div>
                    </div>

                    <!-- Tabel Data -->
                    <div class="widget-card">
                        <div class="table-responsive">
                            <table class="table table-custom table-hover">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="15%">Cover</th>
                                        <th width="35%">Judul Berita & Penulis</th>
                                        <th width="15%">Tanggal</th>
                                        <th width="15%" class="text-center">Dilihat</th>
                                        <th width="15%" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($query_berita && mysqli_num_rows($query_berita) > 0): ?>
                                        <?php $no = 1; while ($row = mysqli_fetch_assoc($query_berita)): ?>
                                        <tr>
                                            <td class="text-muted fw-semibold"><?php echo $no++; ?></td>
                                            <td>
                                                <?php 
                                                    $foto_src = $row['gambar_cover'];
                                                    if (empty($foto_src)) {
                                                        $foto_src = 'https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?q=80&w=200&h=150&fit=crop';
                                                    } elseif (!filter_var($foto_src, FILTER_VALIDATE_URL)) {
                                                        $foto_src = '../' . $foto_src;
                                                    }
                                                ?>
                                                <img src="<?php echo htmlspecialchars($foto_src); ?>" class="thumbnail-table shadow-sm" alt="Cover">
                                            </td>
                                            <td>
                                                <h6 class="fw-bold mb-1 text-dark" style="font-size: 0.95rem; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;"><?php echo htmlspecialchars($row['judul']); ?></h6>
                                                <span class="text-muted small" style="font-size: 0.75rem;"><i class="fas fa-pen-nib me-1"></i> <?php echo htmlspecialchars($row['penulis']); ?></span>
                                            </td>
                                            <td>
                                                <span class="text-dark fw-medium small"><i class="far fa-calendar-alt text-muted me-1"></i> <?php echo date('d M Y', strtotime($row['tanggal_publish'])); ?></span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-light text-secondary border px-2 py-1"><i class="fas fa-eye me-1"></i> <?php echo htmlspecialchars($row['dilihat']); ?>x</span>
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-2">
                                                    <button type="button" class="btn-action btn-edit" data-bs-toggle="modal" data-bs-target="#modalEdit<?php echo $row['id_berita']; ?>" title="Edit Data">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button" class="btn-action btn-delete" onclick="konfirmasiHapus(<?php echo $row['id_berita']; ?>)" title="Hapus Data">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- Modal Edit untuk setiap baris -->
                                        <div class="modal fade" id="modalEdit<?php echo $row['id_berita']; ?>" tabindex="-1" aria-hidden="true" data-bs-focus="false">
                                            <div class="modal-dialog modal-dialog-centered modal-xl">
                                                <div class="modal-content" style="border-radius: 20px; border: none;">
                                                    <div class="modal-header border-0 pb-0">
                                                        <h5 class="modal-title fw-bold">Edit Warta Berita</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <form action="berita.php" method="POST" enctype="multipart/form-data">
                                                        <div class="modal-body pt-4">
                                                            <input type="hidden" name="id_berita" value="<?php echo $row['id_berita']; ?>">
                                                            <input type="hidden" name="gambar_lama" value="<?php echo htmlspecialchars($row['gambar_cover']); ?>">
                                                            
                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold small text-dark">Judul Berita</label>
                                                                <input type="text" class="form-control form-control-custom" name="judul" value="<?php echo htmlspecialchars($row['judul']); ?>" required>
                                                            </div>
                                                            
                                                            <div class="row g-3 mb-3">
                                                                <div class="col-md-6">
                                                                    <label class="form-label fw-semibold small text-dark">Tanggal Publish</label>
                                                                    <input type="date" class="form-control form-control-custom" name="tanggal_publish" value="<?php echo htmlspecialchars($row['tanggal_publish']); ?>" required>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label class="form-label fw-semibold small text-dark">Nama Penulis</label>
                                                                    <input type="text" class="form-control form-control-custom" name="penulis" value="<?php echo htmlspecialchars($row['penulis']); ?>" required>
                                                                </div>
                                                            </div>

                                                            <div class="mb-4">
                                                                <label class="form-label fw-semibold small text-dark">Ganti Gambar Cover (Opsional)</label>
                                                                <input type="file" class="form-control form-control-custom" name="gambar_cover" accept="image/png, image/jpeg, image/jpg, image/webp">
                                                                <small class="text-muted" style="font-size: 0.7rem;">Kosongkan jika tidak ingin mengubah cover.</small>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold small text-dark">Isi Berita Lengkap</label>
                                                                <textarea class="form-control summernote-editor" name="isi_berita" required><?php echo htmlspecialchars($row['isi_berita']); ?></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer border-0 pt-0">
                                                            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit" name="edit_berita" class="btn btn-pill-primary px-4 m-0"><i class="fas fa-save me-1"></i> Simpan Perubahan</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center py-5 text-muted">
                                                <i class="fas fa-newspaper fs-1 mb-3 opacity-25"></i>
                                                <p class="mb-0">Belum ada warta berita yang diterbitkan.</p>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Warta Berita -->
    <div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true" data-bs-focus="false">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content" style="border-radius: 20px; border: none;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Tulis Warta Berita Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="berita.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-body pt-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-dark">Judul Berita</label>
                            <input type="text" class="form-control form-control-custom" name="judul" placeholder="Contoh: Penerimaan Santri Baru Tahun 2026 Resmi Dibuka" required>
                        </div>
                        
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-dark">Tanggal Publish</label>
                                <input type="date" class="form-control form-control-custom" name="tanggal_publish" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-dark">Nama Penulis</label>
                                <input type="text" class="form-control form-control-custom" name="penulis" value="<?php echo htmlspecialchars($_SESSION['nama_lengkap']); ?>" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold small text-dark">Upload Gambar Cover</label>
                            <input type="file" class="form-control form-control-custom" name="gambar_cover" accept="image/png, image/jpeg, image/jpg, image/webp" required>
                            <small class="text-muted" style="font-size: 0.7rem;">Disarankan gambar landscape rasio 16:9 agar tampil maksimal.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-dark">Isi Berita Lengkap</label>
                            <textarea class="form-control summernote-editor" name="isi_berita" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="tambah_berita" class="btn btn-pill-primary px-4 m-0"><i class="fas fa-paper-plane me-1"></i> Terbitkan Warta</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Form Hapus (Hidden) -->
    <form id="formHapus" action="berita.php" method="POST" style="display: none;">
        <input type="hidden" name="id_hapus" id="inputIdHapus">
        <input type="hidden" name="hapus_berita" value="1">
    </form>

    <!-- jQuery (Wajib untuk Summernote) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Summernote Lite JS -->
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Inisialisasi Teks Editor Summernote
        $(document).ready(function() {
            $('.summernote-editor').summernote({
                placeholder: 'Tuliskan artikel atau rincian kegiatan berita di sini...',
                tabsize: 2,
                height: 300,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'italic', 'underline', 'clear']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture', 'video']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ]
            });
        });

        // Logika Hapus Data
        function konfirmasiHapus(id) {
            Swal.fire({
                title: 'Hapus Warta Berita?',
                text: "Berita beserta gambar cover ini akan dihapus secara permanen.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('inputIdHapus').value = id;
                    document.getElementById('formHapus').submit();
                }
            });
        }

        // Notifikasi SweetAlert
        document.addEventListener("DOMContentLoaded", function() {
            <?php if ($status_aksi === 'tambah_success'): ?>
                Swal.fire({ icon: 'success', title: 'Diterbitkan!', text: 'Warta berita baru berhasil dipublikasikan.', confirmButtonColor: '#00a86b' });
            <?php elseif ($status_aksi === 'edit_success'): ?>
                Swal.fire({ icon: 'success', title: 'Diperbarui!', text: 'Perubahan pada warta berita berhasil disimpan.', confirmButtonColor: '#00a86b' });
            <?php elseif ($status_aksi === 'hapus_success'): ?>
                Swal.fire({ icon: 'success', title: 'Terhapus!', text: 'Berita tersebut telah dihapus dari sistem.', confirmButtonColor: '#00a86b' });
            <?php elseif ($status_aksi === 'error'): ?>
                Swal.fire({ icon: 'error', title: 'Gagal!', text: 'Terjadi kesalahan sistem saat memproses database.', confirmButtonColor: '#d33' });
            <?php endif; ?>
        });
    </script>
</body>
</html>