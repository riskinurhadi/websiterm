<?php
/**
 * File: admin/sambutan.php
 * Deskripsi: Halaman kelola data sambutan pejabat pondok (Pengasuh, Ketua Yayasan, dll).
 * Fitur: Tambah (Upload Foto), Tampil, Edit, Hapus (CRUD).
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

// Setup direktori upload
$upload_dir = '../uploads/sambutan/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// ==============================================
// 1. PROSES TAMBAH SAMBUTAN
// ==============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_sambutan'])) {
    $nama_pejabat  = mysqli_real_escape_string($conn, trim($_POST['nama_pejabat']));
    $jabatan       = mysqli_real_escape_string($conn, trim($_POST['jabatan']));
    $isi_sambutan  = mysqli_real_escape_string($conn, trim($_POST['isi_sambutan']));
    $urutan        = (int) $_POST['urutan'];
    $status_tampil = mysqli_real_escape_string($conn, $_POST['status_tampil']);
    
    $foto_path = 'default_pejabat.jpg'; // default dari DB awal

    // Proses Upload File Foto
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $file_tmp  = $_FILES['foto']['tmp_name'];
        $file_name = $_FILES['foto']['name'];
        $file_ext  = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed   = ['jpg', 'jpeg', 'png', 'webp'];
        
        if (in_array($file_ext, $allowed)) {
            $new_file_name = 'pejabat_' . time() . '_' . uniqid() . '.' . $file_ext;
            $dest_path = $upload_dir . $new_file_name;
            
            if (move_uploaded_file($file_tmp, $dest_path)) {
                $foto_path = 'uploads/sambutan/' . $new_file_name; 
            }
        }
    }

    $query = "INSERT INTO sambutan (nama_pejabat, jabatan, foto, isi_sambutan, urutan, status_tampil) 
              VALUES ('$nama_pejabat', '$jabatan', '$foto_path', '$isi_sambutan', $urutan, '$status_tampil')";
              
    if (mysqli_query($conn, $query)) {
        $status_aksi = 'tambah_success';
    } else {
        $status_aksi = 'error';
    }
}

// ==============================================
// 2. PROSES EDIT SAMBUTAN
// ==============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_sambutan'])) {
    $id_sambutan   = (int) $_POST['id_sambutan'];
    $nama_pejabat  = mysqli_real_escape_string($conn, trim($_POST['nama_pejabat']));
    $jabatan       = mysqli_real_escape_string($conn, trim($_POST['jabatan']));
    $isi_sambutan  = mysqli_real_escape_string($conn, trim($_POST['isi_sambutan']));
    $urutan        = (int) $_POST['urutan'];
    $status_tampil = mysqli_real_escape_string($conn, $_POST['status_tampil']);
    
    $foto_path     = mysqli_real_escape_string($conn, trim($_POST['foto_lama']));

    // Cek upload foto baru
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $file_tmp  = $_FILES['foto']['tmp_name'];
        $file_name = $_FILES['foto']['name'];
        $file_ext  = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed   = ['jpg', 'jpeg', 'png', 'webp'];
        
        if (in_array($file_ext, $allowed)) {
            $new_file_name = 'pejabat_' . time() . '_' . uniqid() . '.' . $file_ext;
            $dest_path = $upload_dir . $new_file_name;
            
            if (move_uploaded_file($file_tmp, $dest_path)) {
                // Hapus foto lama di server 
                if (!empty($foto_path) && $foto_path !== 'default_pejabat.jpg' && strpos($foto_path, 'uploads/') !== false && file_exists('../' . $foto_path)) {
                    unlink('../' . $foto_path);
                }
                $foto_path = 'uploads/sambutan/' . $new_file_name;
            }
        }
    }

    $query = "UPDATE sambutan SET 
                nama_pejabat = '$nama_pejabat',
                jabatan = '$jabatan',
                isi_sambutan = '$isi_sambutan',
                urutan = $urutan,
                status_tampil = '$status_tampil',
                foto = '$foto_path'
              WHERE id_sambutan = $id_sambutan";
              
    if (mysqli_query($conn, $query)) {
        $status_aksi = 'edit_success';
    } else {
        $status_aksi = 'error';
    }
}

// ==============================================
// 3. PROSES HAPUS SAMBUTAN
// ==============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hapus_sambutan'])) {
    $id_hapus = (int) $_POST['id_hapus'];
    
    // Ambil path file untuk dihapus secara fisik
    $q_file = mysqli_query($conn, "SELECT foto FROM sambutan WHERE id_sambutan = $id_hapus");
    if ($q_file && mysqli_num_rows($q_file) > 0) {
        $data_file = mysqli_fetch_assoc($q_file);
        $foto_lama = $data_file['foto'];
        if (!empty($foto_lama) && $foto_lama !== 'default_pejabat.jpg' && strpos($foto_lama, 'uploads/') !== false && file_exists('../' . $foto_lama)) {
            unlink('../' . $foto_lama);
        }
    }
    
    $query = "DELETE FROM sambutan WHERE id_sambutan = $id_hapus";
    if (mysqli_query($conn, $query)) {
        $status_aksi = 'hapus_success';
    } else {
        $status_aksi = 'error';
    }
}

// Mengambil Data Sambutan (diurutkan berdasarkan urutan tampil terkecil ke terbesar)
$query_sambutan = mysqli_query($conn, "SELECT * FROM sambutan ORDER BY urutan ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Sambutan Pejabat - Admin PP Raudlatul Muta'allimin</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-bootstrap-4/bootstrap-4.css" rel="stylesheet">
    
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
            width: 70px;
            height: 70px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--kemenag-green-light);
        }

        .badge-urutan {
            background-color: #f1f5f9;
            color: var(--slate-900);
            padding: 5px 12px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 700;
            border: 1px solid rgba(0,0,0,0.05);
        }

        .badge-status-y { background-color: #d1fae5; color: #059669; padding: 6px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
        .badge-status-n { background-color: #f1f5f9; color: #64748b; padding: 6px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }

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
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <div>
                                <h4 class="fw-bold text-dark mb-1" style="font-weight: 800; letter-spacing: -0.5px;">Sambutan Pejabat</h4>
                                <p class="text-muted small mb-0">Kelola daftar profil dan kata sambutan jajaran direksi/pimpinan.</p>
                            </div>
                        </div>
                        <div>
                            <button type="button" class="btn btn-pill-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
                                <i class="fas fa-plus me-2"></i> Tambah Sambutan
                            </button>
                        </div>
                    </div>

                    <!-- Tabel Data -->
                    <div class="widget-card">
                        <div class="table-responsive">
                            <table class="table table-custom table-hover">
                                <thead>
                                    <tr>
                                        <th width="5%" class="text-center">Urutan</th>
                                        <th width="20%">Foto Profil</th>
                                        <th width="30%">Nama & Jabatan</th>
                                        <th width="20%">Potongan Sambutan</th>
                                        <th width="10%" class="text-center">Status</th>
                                        <th width="15%" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($query_sambutan && mysqli_num_rows($query_sambutan) > 0): ?>
                                        <?php while ($row = mysqli_fetch_assoc($query_sambutan)): ?>
                                        <tr>
                                            <td class="text-center">
                                                <span class="badge-urutan">#<?php echo htmlspecialchars($row['urutan']); ?></span>
                                            </td>
                                            <td>
                                                <?php 
                                                    $foto_src = $row['foto'];
                                                    // Set placeholder jika default atau string kosong
                                                    if (empty($foto_src) || $foto_src === 'default_pejabat.jpg') {
                                                        $foto_src = 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?q=80&w=256&h=256&fit=crop';
                                                    } elseif (!filter_var($foto_src, FILTER_VALIDATE_URL)) {
                                                        $foto_src = '../' . $foto_src;
                                                    }
                                                ?>
                                                <img src="<?php echo htmlspecialchars($foto_src); ?>" class="thumbnail-table shadow-sm" alt="Foto Pejabat">
                                            </td>
                                            <td>
                                                <h6 class="fw-bold mb-1 text-dark" style="font-size: 0.95rem;"><?php echo htmlspecialchars($row['nama_pejabat']); ?></h6>
                                                <span class="text-success fw-semibold small px-2 py-1 rounded bg-success bg-opacity-10" style="font-size: 0.75rem;">
                                                    <?php echo htmlspecialchars($row['jabatan']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <p class="mb-0 text-muted" style="font-size: 0.8rem; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; font-style: italic;">
                                                    "<?php echo htmlspecialchars($row['isi_sambutan']); ?>"
                                                </p>
                                            </td>
                                            <td class="text-center">
                                                <?php if ($row['status_tampil'] == 'Y'): ?>
                                                    <span class="badge-status-y">Ditampilkan</span>
                                                <?php else: ?>
                                                    <span class="badge-status-n">Disembunyikan</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-2">
                                                    <button type="button" class="btn-action btn-edit" data-bs-toggle="modal" data-bs-target="#modalEdit<?php echo $row['id_sambutan']; ?>" title="Edit Data">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button" class="btn-action btn-delete" onclick="konfirmasiHapus(<?php echo $row['id_sambutan']; ?>)" title="Hapus Data">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- Modal Edit untuk setiap baris -->
                                        <div class="modal fade" id="modalEdit<?php echo $row['id_sambutan']; ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content" style="border-radius: 20px; border: none;">
                                                    <div class="modal-header border-0 pb-0">
                                                        <h5 class="modal-title fw-bold">Edit Pejabat & Sambutan</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <form action="sambutan.php" method="POST" enctype="multipart/form-data">
                                                        <div class="modal-body">
                                                            <input type="hidden" name="id_sambutan" value="<?php echo $row['id_sambutan']; ?>">
                                                            <input type="hidden" name="foto_lama" value="<?php echo htmlspecialchars($row['foto']); ?>">
                                                            
                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold small text-dark">Nama Lengkap & Gelar</label>
                                                                <input type="text" class="form-control form-control-custom" name="nama_pejabat" value="<?php echo htmlspecialchars($row['nama_pejabat']); ?>" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold small text-dark">Jabatan dalam Yayasan/Pondok</label>
                                                                <input type="text" class="form-control form-control-custom" name="jabatan" value="<?php echo htmlspecialchars($row['jabatan']); ?>" required>
                                                            </div>
                                                            <div class="row g-3 mb-3">
                                                                <div class="col-md-6">
                                                                    <label class="form-label fw-semibold small text-dark">Urutan Tampil</label>
                                                                    <input type="number" class="form-control form-control-custom" name="urutan" value="<?php echo htmlspecialchars($row['urutan']); ?>" required min="1">
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label class="form-label fw-semibold small text-dark">Status Visibilitas</label>
                                                                    <select class="form-select form-control-custom" name="status_tampil" required>
                                                                        <option value="Y" <?php echo ($row['status_tampil'] == 'Y') ? 'selected' : ''; ?>>Tampilkan</option>
                                                                        <option value="N" <?php echo ($row['status_tampil'] == 'N') ? 'selected' : ''; ?>>Sembunyikan</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold small text-dark">Ubah Foto Profil (Opsional)</label>
                                                                <input type="file" class="form-control form-control-custom" name="foto" accept="image/png, image/jpeg, image/jpg, image/webp">
                                                                <small class="text-muted" style="font-size: 0.7rem;">Biarkan kosong jika tidak ingin mengganti foto saat ini.</small>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold small text-dark">Isi Kata Sambutan</label>
                                                                <textarea class="form-control form-control-custom" name="isi_sambutan" rows="4" required><?php echo htmlspecialchars($row['isi_sambutan']); ?></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer border-0 pt-0">
                                                            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit" name="edit_sambutan" class="btn btn-pill-primary px-4 m-0">Simpan Perubahan</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center py-5 text-muted">
                                                <i class="fas fa-user-tie fs-1 mb-3 opacity-25"></i>
                                                <p class="mb-0">Belum ada data sambutan pejabat yang tersimpan.</p>
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

    <!-- Modal Tambah Sambutan -->
    <div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 20px; border: none;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Tambah Pejabat Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="sambutan.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-dark">Nama Lengkap & Gelar</label>
                            <input type="text" class="form-control form-control-custom" name="nama_pejabat" placeholder="Contoh: KH. Marsudi" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-dark">Jabatan dalam Yayasan/Pondok</label>
                            <input type="text" class="form-control form-control-custom" name="jabatan" placeholder="Contoh: Pengasuh Utama" required>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-dark">Urutan Tampil</label>
                                <input type="number" class="form-control form-control-custom" name="urutan" placeholder="Cth: 1" value="1" required min="1">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-dark">Status Visibilitas</label>
                                <select class="form-select form-control-custom" name="status_tampil" required>
                                    <option value="Y" selected>Tampilkan</option>
                                    <option value="N">Sembunyikan</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-dark">Upload Foto Profil</label>
                            <input type="file" class="form-control form-control-custom" name="foto" accept="image/png, image/jpeg, image/jpg, image/webp">
                            <small class="text-muted" style="font-size: 0.7rem;">Jika kosong, sistem akan menggunakan gambar bawaan otomatis.</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-dark">Isi Kata Sambutan</label>
                            <textarea class="form-control form-control-custom" name="isi_sambutan" rows="4" placeholder="Tuliskan petuah atau sambutan pejabat..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="tambah_sambutan" class="btn btn-pill-primary px-4 m-0">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Form Hapus (Hidden) -->
    <form id="formHapus" action="sambutan.php" method="POST" style="display: none;">
        <input type="hidden" name="id_hapus" id="inputIdHapus">
        <input type="hidden" name="hapus_sambutan" value="1">
    </form>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- SweetAlert2 Logic -->
    <script>
        function konfirmasiHapus(id) {
            Swal.fire({
                title: 'Hapus Sambutan?',
                text: "Profil pejabat dan pesan sambutannya akan dihapus permanen.",
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

        document.addEventListener("DOMContentLoaded", function() {
            <?php if ($status_aksi === 'tambah_success'): ?>
                Swal.fire({ icon: 'success', title: 'Berhasil!', text: 'Profil pejabat dan sambutan berhasil ditambahkan.', confirmButtonColor: '#00a86b' });
            <?php elseif ($status_aksi === 'edit_success'): ?>
                Swal.fire({ icon: 'success', title: 'Berhasil!', text: 'Perubahan pada sambutan telah disimpan.', confirmButtonColor: '#00a86b' });
            <?php elseif ($status_aksi === 'hapus_success'): ?>
                Swal.fire({ icon: 'success', title: 'Terhapus!', text: 'Data sambutan berhasil dihapus dari sistem.', confirmButtonColor: '#00a86b' });
            <?php elseif ($status_aksi === 'error'): ?>
                Swal.fire({ icon: 'error', title: 'Gagal!', text: 'Terjadi kesalahan sistem saat memproses basis data.', confirmButtonColor: '#d33' });
            <?php endif; ?>
        });
    </script>
</body>
</html>