<?php
/**
 * File: admin/testimoni.php
 * Deskripsi: Halaman kelola data testimoni alumni.
 * Fitur: Tambah, Tampil, Edit, Hapus (CRUD) data testimoni.
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

// Setup direktori upload (menyimpan file di folder root "uploads/testimoni")
$upload_dir = '../uploads/testimoni/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// ==============================================
// 1. PROSES TAMBAH TESTIMONI
// ==============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_testimoni'])) {
    $nama_alumni      = mysqli_real_escape_string($conn, trim($_POST['nama_alumni']));
    $profesi_angkatan = mysqli_real_escape_string($conn, trim($_POST['profesi_angkatan']));
    $isi_testimoni    = mysqli_real_escape_string($conn, trim($_POST['isi_testimoni']));
    $status_tampil    = mysqli_real_escape_string($conn, $_POST['status_tampil']);
    
    // Default gambar jika tidak ada upload
    $foto_path = 'https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?q=80&w=120&h=120&fit=crop';

    // Proses Eksekusi Upload File Foto
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $file_tmp  = $_FILES['foto']['tmp_name'];
        $file_name = $_FILES['foto']['name'];
        $file_ext  = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed   = ['jpg', 'jpeg', 'png', 'webp'];
        
        if (in_array($file_ext, $allowed)) {
            $new_file_name = time() . '_' . uniqid() . '.' . $file_ext;
            $dest_path = $upload_dir . $new_file_name;
            
            if (move_uploaded_file($file_tmp, $dest_path)) {
                // Simpan path relatif ke database agar bisa dibaca halaman depan (index.php)
                $foto_path = 'uploads/testimoni/' . $new_file_name; 
            }
        }
    }

    $query = "INSERT INTO testimoni (nama_alumni, profesi_angkatan, foto, isi_testimoni, status_tampil) 
              VALUES ('$nama_alumni', '$profesi_angkatan', '$foto_path', '$isi_testimoni', '$status_tampil')";
              
    if (mysqli_query($conn, $query)) {
        $status_aksi = 'tambah_success';
    } else {
        $status_aksi = 'error';
    }
}

// ==============================================
// 2. PROSES EDIT TESTIMONI
// ==============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_testimoni'])) {
    $id_testimoni     = (int) $_POST['id_testimoni'];
    $nama_alumni      = mysqli_real_escape_string($conn, trim($_POST['nama_alumni']));
    $profesi_angkatan = mysqli_real_escape_string($conn, trim($_POST['profesi_angkatan']));
    $isi_testimoni    = mysqli_real_escape_string($conn, trim($_POST['isi_testimoni']));
    $status_tampil    = mysqli_real_escape_string($conn, $_POST['status_tampil']);
    
    // Ambil path foto lama dari input hidden
    $foto_path        = mysqli_real_escape_string($conn, trim($_POST['foto_lama']));

    // Cek apakah Admin meng-upload foto baru
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $file_tmp  = $_FILES['foto']['tmp_name'];
        $file_name = $_FILES['foto']['name'];
        $file_ext  = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed   = ['jpg', 'jpeg', 'png', 'webp'];
        
        if (in_array($file_ext, $allowed)) {
            $new_file_name = time() . '_' . uniqid() . '.' . $file_ext;
            $dest_path = $upload_dir . $new_file_name;
            
            if (move_uploaded_file($file_tmp, $dest_path)) {
                // Hapus foto lama di server (kecuali jika foto lama berupa link internet / default)
                if (strpos($foto_path, 'uploads/') !== false && file_exists('../' . $foto_path)) {
                    unlink('../' . $foto_path);
                }
                // Timpa path database dengan path file yang baru diupload
                $foto_path = 'uploads/testimoni/' . $new_file_name;
            }
        }
    }

    $query = "UPDATE testimoni SET 
                nama_alumni = '$nama_alumni',
                profesi_angkatan = '$profesi_angkatan',
                foto = '$foto_path',
                isi_testimoni = '$isi_testimoni',
                status_tampil = '$status_tampil'
              WHERE id_testimoni = $id_testimoni";
              
    if (mysqli_query($conn, $query)) {
        $status_aksi = 'edit_success';
    } else {
        $status_aksi = 'error';
    }
}

// ==============================================
// 3. PROSES HAPUS TESTIMONI
// ==============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hapus_testimoni'])) {
    $id_hapus = (int) $_POST['id_hapus'];
    
    $query = "DELETE FROM testimoni WHERE id_testimoni = $id_hapus";
    if (mysqli_query($conn, $query)) {
        $status_aksi = 'hapus_success';
    } else {
        $status_aksi = 'error';
    }
}

// Mengambil Data Testimoni
$query_testimoni = mysqli_query($conn, "SELECT * FROM testimoni ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Testimoni - Admin PP Raudlatul Muta'allimin</title>
    
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

        .avatar-table {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--kemenag-green-light);
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
                                <i class="fas fa-comments"></i>
                            </div>
                            <div>
                                <h4 class="fw-bold text-dark mb-1" style="font-weight: 800; letter-spacing: -0.5px;">Testimoni Alumni</h4>
                                <p class="text-muted small mb-0">Kelola ulasan dan kisah sukses dari alumni pesantren.</p>
                            </div>
                        </div>
                        <div>
                            <button type="button" class="btn btn-pill-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
                                <i class="fas fa-plus me-2"></i> Tambah Testimoni
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
                                        <th width="25%">Profil Alumni</th>
                                        <th width="40%">Isi Testimoni</th>
                                        <th width="15%" class="text-center">Status</th>
                                        <th width="15%" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($query_testimoni && mysqli_num_rows($query_testimoni) > 0): ?>
                                        <?php $no = 1; while ($row = mysqli_fetch_assoc($query_testimoni)): ?>
                                        <tr>
                                            <td class="text-muted fw-semibold"><?php echo $no++; ?></td>
                                            <td>
                                                <div class="d-flex align-items-center gap-3">
                                                    <?php 
                                                        $foto_src = $row['foto'];
                                                        if (empty($foto_src) || $foto_src === 'default_alumni.jpg') {
                                                            $foto_src = 'https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?q=80&w=120&h=120&fit=crop';
                                                        } elseif (!filter_var($foto_src, FILTER_VALIDATE_URL)) {
                                                            // Modifikasi Path: Jika berupa file lokal, naikkan 1 tingkat keluar folder admin
                                                            $foto_src = '../' . $foto_src;
                                                        }
                                                    ?>
                                                    <img src="<?php echo htmlspecialchars($foto_src); ?>" class="avatar-table" alt="Foto">
                                                    <div>
                                                        <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.95rem;"><?php echo htmlspecialchars($row['nama_alumni']); ?></h6>
                                                        <span class="text-muted small" style="font-size: 0.8rem;"><?php echo htmlspecialchars($row['profesi_angkatan']); ?></span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <p class="mb-0 text-muted" style="font-size: 0.85rem; line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                                    "<?php echo htmlspecialchars($row['isi_testimoni']); ?>"
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
                                                    <button type="button" class="btn-action btn-edit" data-bs-toggle="modal" data-bs-target="#modalEdit<?php echo $row['id_testimoni']; ?>" title="Edit Data">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button" class="btn-action btn-delete" onclick="konfirmasiHapus(<?php echo $row['id_testimoni']; ?>)" title="Hapus Data">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- Modal Edit untuk setiap baris -->
                                        <div class="modal fade" id="modalEdit<?php echo $row['id_testimoni']; ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content" style="border-radius: 20px; border: none;">
                                                    <div class="modal-header border-0 pb-0">
                                                        <h5 class="modal-title fw-bold">Edit Testimoni</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <!-- Tambahkan enctype untuk mendukung file upload -->
                                                    <form action="testimoni.php" method="POST" enctype="multipart/form-data">
                                                        <div class="modal-body">
                                                            <input type="hidden" name="id_testimoni" value="<?php echo $row['id_testimoni']; ?>">
                                                            <!-- Simpan URL foto lama untuk fallback -->
                                                            <input type="hidden" name="foto_lama" value="<?php echo htmlspecialchars($row['foto']); ?>">
                                                            
                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold small text-dark">Nama Alumni</label>
                                                                <input type="text" class="form-control form-control-custom" name="nama_alumni" value="<?php echo htmlspecialchars($row['nama_alumni']); ?>" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold small text-dark">Angkatan / Profesi</label>
                                                                <input type="text" class="form-control form-control-custom" name="profesi_angkatan" value="<?php echo htmlspecialchars($row['profesi_angkatan']); ?>" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold small text-dark">Upload Foto Baru (Opsional)</label>
                                                                <input type="file" class="form-control form-control-custom" name="foto" accept="image/png, image/jpeg, image/jpg, image/webp">
                                                                <small class="text-muted" style="font-size: 0.7rem;">Kosongkan jika tidak ingin mengubah foto saat ini.</small>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold small text-dark">Isi Testimoni</label>
                                                                <textarea class="form-control form-control-custom" name="isi_testimoni" rows="4" required><?php echo htmlspecialchars($row['isi_testimoni']); ?></textarea>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold small text-dark">Status Tampil</label>
                                                                <select class="form-select form-control-custom" name="status_tampil" required>
                                                                    <option value="Y" <?php echo ($row['status_tampil'] == 'Y') ? 'selected' : ''; ?>>Tampilkan di Website</option>
                                                                    <option value="N" <?php echo ($row['status_tampil'] == 'N') ? 'selected' : ''; ?>>Sembunyikan</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer border-0 pt-0">
                                                            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit" name="edit_testimoni" class="btn btn-pill-primary px-4 m-0">Simpan Perubahan</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center py-5 text-muted">
                                                <i class="fas fa-comments fs-1 mb-3 opacity-25"></i>
                                                <p class="mb-0">Belum ada data testimoni alumni.</p>
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

    <!-- Modal Tambah Testimoni -->
    <div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 20px; border: none;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Tambah Testimoni Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <!-- Tambahkan enctype untuk mendukung file upload -->
                <form action="testimoni.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-dark">Nama Alumni</label>
                            <input type="text" class="form-control form-control-custom" name="nama_alumni" placeholder="Contoh: Ahmad Fauzi, S.Kom" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-dark">Angkatan / Profesi</label>
                            <input type="text" class="form-control form-control-custom" name="profesi_angkatan" placeholder="Contoh: Lulusan SMK - 2019 / Web Developer" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-dark">Upload Foto (Opsional)</label>
                            <input type="file" class="form-control form-control-custom" name="foto" accept="image/png, image/jpeg, image/jpg, image/webp">
                            <small class="text-muted" style="font-size: 0.7rem;">Format file: JPG, PNG, WEBP. Kosongkan jika ingin menggunakan gambar default.</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-dark">Isi Testimoni</label>
                            <textarea class="form-control form-control-custom" name="isi_testimoni" rows="4" placeholder="Tuliskan ulasan alumni..." required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-dark">Status Tampil</label>
                            <select class="form-select form-control-custom" name="status_tampil" required>
                                <option value="Y">Tampilkan di Website</option>
                                <option value="N">Sembunyikan</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="tambah_testimoni" class="btn btn-pill-primary px-4 m-0">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Form Hapus (Hidden) -->
    <form id="formHapus" action="testimoni.php" method="POST" style="display: none;">
        <input type="hidden" name="id_hapus" id="inputIdHapus">
        <input type="hidden" name="hapus_testimoni" value="1">
    </form>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- SweetAlert2 Logic -->
    <script>
        function konfirmasiHapus(id) {
            Swal.fire({
                title: 'Hapus Testimoni?',
                text: "Data testimoni ini akan dihapus secara permanen dari sistem.",
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
                Swal.fire({ icon: 'success', title: 'Berhasil!', text: 'Testimoni alumni baru berhasil ditambahkan.', confirmButtonColor: '#00a86b' });
            <?php elseif ($status_aksi === 'edit_success'): ?>
                Swal.fire({ icon: 'success', title: 'Berhasil!', text: 'Data testimoni berhasil diperbarui.', confirmButtonColor: '#00a86b' });
            <?php elseif ($status_aksi === 'hapus_success'): ?>
                Swal.fire({ icon: 'success', title: 'Terhapus!', text: 'Data testimoni berhasil dihapus.', confirmButtonColor: '#00a86b' });
            <?php elseif ($status_aksi === 'error'): ?>
                Swal.fire({ icon: 'error', title: 'Gagal!', text: 'Terjadi kesalahan sistem saat memproses database.', confirmButtonColor: '#d33' });
            <?php endif; ?>
        });
    </script>
</body>
</html>