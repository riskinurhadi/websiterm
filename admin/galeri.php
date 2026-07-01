<?php
/**
 * File: admin/galeri.php
 * Deskripsi: Halaman kelola data dokumentasi kegiatan (Galeri).
 * Fitur: Tambah (Upload Foto), Tampil, Edit, Hapus (CRUD) data galeri.
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

// Setup direktori upload (menyimpan file di folder root "uploads/galeri")
$upload_dir = '../uploads/galeri/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// ==============================================
// 1. PROSES TAMBAH GALERI
// ==============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_galeri'])) {
    $judul_kegiatan   = mysqli_real_escape_string($conn, trim($_POST['judul_kegiatan']));
    $tanggal_kegiatan = mysqli_real_escape_string($conn, trim($_POST['tanggal_kegiatan']));
    $deskripsi        = mysqli_real_escape_string($conn, trim($_POST['deskripsi']));
    
    $foto_path = ''; // Default kosong

    // Proses Eksekusi Upload File Foto
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $file_tmp  = $_FILES['foto']['tmp_name'];
        $file_name = $_FILES['foto']['name'];
        $file_ext  = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed   = ['jpg', 'jpeg', 'png', 'webp'];
        
        if (in_array($file_ext, $allowed)) {
            $new_file_name = 'galeri_' . time() . '_' . uniqid() . '.' . $file_ext;
            $dest_path = $upload_dir . $new_file_name;
            
            if (move_uploaded_file($file_tmp, $dest_path)) {
                // Simpan path relatif ke database agar bisa dibaca halaman depan
                $foto_path = 'uploads/galeri/' . $new_file_name; 
            }
        }
    }

    $query = "INSERT INTO dokumentasi (judul_kegiatan, tanggal_kegiatan, deskripsi, file_foto) 
              VALUES ('$judul_kegiatan', '$tanggal_kegiatan', '$deskripsi', '$foto_path')";
              
    if (mysqli_query($conn, $query)) {
        $status_aksi = 'tambah_success';
    } else {
        $status_aksi = 'error';
    }
}

// ==============================================
// 2. PROSES EDIT GALERI
// ==============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_galeri'])) {
    $id_galeri        = (int) $_POST['id_galeri'];
    $judul_kegiatan   = mysqli_real_escape_string($conn, trim($_POST['judul_kegiatan']));
    $tanggal_kegiatan = mysqli_real_escape_string($conn, trim($_POST['tanggal_kegiatan']));
    $deskripsi        = mysqli_real_escape_string($conn, trim($_POST['deskripsi']));
    
    // Ambil path foto lama dari input hidden
    $foto_path        = mysqli_real_escape_string($conn, trim($_POST['foto_lama']));

    // Cek apakah Admin meng-upload foto baru
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $file_tmp  = $_FILES['foto']['tmp_name'];
        $file_name = $_FILES['foto']['name'];
        $file_ext  = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed   = ['jpg', 'jpeg', 'png', 'webp'];
        
        if (in_array($file_ext, $allowed)) {
            $new_file_name = 'galeri_' . time() . '_' . uniqid() . '.' . $file_ext;
            $dest_path = $upload_dir . $new_file_name;
            
            if (move_uploaded_file($file_tmp, $dest_path)) {
                // Hapus foto lama di server 
                if (!empty($foto_path) && strpos($foto_path, 'uploads/') !== false && file_exists('../' . $foto_path)) {
                    unlink('../' . $foto_path);
                }
                // Timpa path database dengan path file yang baru diupload
                $foto_path = 'uploads/galeri/' . $new_file_name;
            }
        }
    }

    $query = "UPDATE dokumentasi SET 
                judul_kegiatan = '$judul_kegiatan',
                tanggal_kegiatan = '$tanggal_kegiatan',
                deskripsi = '$deskripsi',
                file_foto = '$foto_path'
              WHERE id_galeri = $id_galeri";
              
    if (mysqli_query($conn, $query)) {
        $status_aksi = 'edit_success';
    } else {
        $status_aksi = 'error';
    }
}

// ==============================================
// 3. PROSES HAPUS GALERI
// ==============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hapus_galeri'])) {
    $id_hapus = (int) $_POST['id_hapus'];
    
    // Ambil path file untuk dihapus secara fisik dari server
    $q_file = mysqli_query($conn, "SELECT file_foto FROM dokumentasi WHERE id_galeri = $id_hapus");
    if ($q_file && mysqli_num_rows($q_file) > 0) {
        $data_file = mysqli_fetch_assoc($q_file);
        $foto_lama = $data_file['file_foto'];
        if (!empty($foto_lama) && strpos($foto_lama, 'uploads/') !== false && file_exists('../' . $foto_lama)) {
            unlink('../' . $foto_lama);
        }
    }
    
    $query = "DELETE FROM dokumentasi WHERE id_galeri = $id_hapus";
    if (mysqli_query($conn, $query)) {
        $status_aksi = 'hapus_success';
    } else {
        $status_aksi = 'error';
    }
}

// Mengambil Data Galeri
$query_galeri = mysqli_query($conn, "SELECT * FROM dokumentasi ORDER BY tanggal_kegiatan DESC, id_galeri DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Galeri - Admin PP Raudlatul Muta'allimin</title>
    
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
            width: 80px;
            height: 60px;
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
                                <i class="fas fa-images"></i>
                            </div>
                            <div>
                                <h4 class="fw-bold text-dark mb-1" style="font-weight: 800; letter-spacing: -0.5px;">Galeri Kegiatan</h4>
                                <p class="text-muted small mb-0">Kelola dokumentasi foto aktivitas santri di pesantren.</p>
                            </div>
                        </div>
                        <div>
                            <button type="button" class="btn btn-pill-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
                                <i class="fas fa-plus me-2"></i> Tambah Dokumentasi
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
                                        <th width="15%">Foto</th>
                                        <th width="25%">Judul Kegiatan</th>
                                        <th width="15%">Tanggal</th>
                                        <th width="25%">Deskripsi Singkat</th>
                                        <th width="15%" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($query_galeri && mysqli_num_rows($query_galeri) > 0): ?>
                                        <?php $no = 1; while ($row = mysqli_fetch_assoc($query_galeri)): ?>
                                        <tr>
                                            <td class="text-muted fw-semibold"><?php echo $no++; ?></td>
                                            <td>
                                                <?php 
                                                    $foto_src = $row['file_foto'];
                                                    if (empty($foto_src)) {
                                                        $foto_src = 'https://images.unsplash.com/photo-1577896851231-70ef18881754?q=80&w=200&h=150&fit=crop';
                                                    } elseif (!filter_var($foto_src, FILTER_VALIDATE_URL)) {
                                                        // Naik 1 folder untuk mengakses folder uploads/
                                                        $foto_src = '../' . $foto_src;
                                                    }
                                                ?>
                                                <img src="<?php echo htmlspecialchars($foto_src); ?>" class="thumbnail-table shadow-sm" alt="Thumbnail">
                                            </td>
                                            <td>
                                                <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.9rem;"><?php echo htmlspecialchars($row['judul_kegiatan']); ?></h6>
                                            </td>
                                            <td>
                                                <span class="text-muted" style="font-size: 0.85rem;"><i class="far fa-calendar-alt me-1"></i> <?php echo date('d/m/Y', strtotime($row['tanggal_kegiatan'])); ?></span>
                                            </td>
                                            <td>
                                                <p class="mb-0 text-muted" style="font-size: 0.8rem; line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                                    <?php echo htmlspecialchars($row['deskripsi']); ?>
                                                </p>
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-2">
                                                    <button type="button" class="btn-action btn-edit" data-bs-toggle="modal" data-bs-target="#modalEdit<?php echo $row['id_galeri']; ?>" title="Edit Data">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button" class="btn-action btn-delete" onclick="konfirmasiHapus(<?php echo $row['id_galeri']; ?>)" title="Hapus Data">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- Modal Edit untuk setiap baris -->
                                        <div class="modal fade" id="modalEdit<?php echo $row['id_galeri']; ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content" style="border-radius: 20px; border: none;">
                                                    <div class="modal-header border-0 pb-0">
                                                        <h5 class="modal-title fw-bold">Edit Dokumentasi</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <!-- Tambahkan enctype untuk upload file -->
                                                    <form action="galeri.php" method="POST" enctype="multipart/form-data">
                                                        <div class="modal-body">
                                                            <input type="hidden" name="id_galeri" value="<?php echo $row['id_galeri']; ?>">
                                                            <!-- Simpan URL foto lama untuk fallback/penghapusan -->
                                                            <input type="hidden" name="foto_lama" value="<?php echo htmlspecialchars($row['file_foto']); ?>">
                                                            
                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold small text-dark">Judul Kegiatan</label>
                                                                <input type="text" class="form-control form-control-custom" name="judul_kegiatan" value="<?php echo htmlspecialchars($row['judul_kegiatan']); ?>" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold small text-dark">Tanggal Kegiatan</label>
                                                                <input type="date" class="form-control form-control-custom" name="tanggal_kegiatan" value="<?php echo htmlspecialchars($row['tanggal_kegiatan']); ?>" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold small text-dark">Ganti Foto (Opsional)</label>
                                                                <input type="file" class="form-control form-control-custom" name="foto" accept="image/png, image/jpeg, image/jpg, image/webp">
                                                                <small class="text-muted" style="font-size: 0.7rem;">Kosongkan jika tidak ingin mengubah foto dokumentasi saat ini.</small>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold small text-dark">Deskripsi Singkat</label>
                                                                <textarea class="form-control form-control-custom" name="deskripsi" rows="3"><?php echo htmlspecialchars($row['deskripsi']); ?></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer border-0 pt-0">
                                                            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit" name="edit_galeri" class="btn btn-pill-primary px-4 m-0">Simpan Perubahan</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center py-5 text-muted">
                                                <i class="fas fa-images fs-1 mb-3 opacity-25"></i>
                                                <p class="mb-0">Belum ada data dokumentasi kegiatan.</p>
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

    <!-- Modal Tambah Dokumentasi -->
    <div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 20px; border: none;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Tambah Dokumentasi Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <!-- Tambahkan enctype untuk upload file -->
                <form action="galeri.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-dark">Judul Kegiatan</label>
                            <input type="text" class="form-control form-control-custom" name="judul_kegiatan" placeholder="Contoh: Upacara Hari Santri Nasional" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-dark">Tanggal Pelaksanaan</label>
                            <input type="date" class="form-control form-control-custom" name="tanggal_kegiatan" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-dark">Upload Foto Kegiatan</label>
                            <input type="file" class="form-control form-control-custom" name="foto" accept="image/png, image/jpeg, image/jpg, image/webp" required>
                            <small class="text-muted" style="font-size: 0.7rem;">Format yang didukung: JPG, JPEG, PNG, WEBP.</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-dark">Deskripsi Singkat (Opsional)</label>
                            <textarea class="form-control form-control-custom" name="deskripsi" rows="3" placeholder="Tuliskan keterangan singkat tentang kegiatan ini..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="tambah_galeri" class="btn btn-pill-primary px-4 m-0">Upload Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Form Hapus (Hidden) -->
    <form id="formHapus" action="galeri.php" method="POST" style="display: none;">
        <input type="hidden" name="id_hapus" id="inputIdHapus">
        <input type="hidden" name="hapus_galeri" value="1">
    </form>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- SweetAlert2 Logic -->
    <script>
        function konfirmasiHapus(id) {
            Swal.fire({
                title: 'Hapus Dokumentasi?',
                text: "Foto kegiatan ini akan dihapus secara permanen dari server dan sistem.",
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
                Swal.fire({ icon: 'success', title: 'Berhasil!', text: 'Dokumentasi kegiatan berhasil diunggah.', confirmButtonColor: '#00a86b' });
            <?php elseif ($status_aksi === 'edit_success'): ?>
                Swal.fire({ icon: 'success', title: 'Berhasil!', text: 'Data galeri berhasil diperbarui.', confirmButtonColor: '#00a86b' });
            <?php elseif ($status_aksi === 'hapus_success'): ?>
                Swal.fire({ icon: 'success', title: 'Terhapus!', text: 'Data dokumentasi dan foto berhasil dihapus.', confirmButtonColor: '#00a86b' });
            <?php elseif ($status_aksi === 'error'): ?>
                Swal.fire({ icon: 'error', title: 'Gagal!', text: 'Terjadi kesalahan sistem saat memproses upload file ke database.', confirmButtonColor: '#d33' });
            <?php endif; ?>
        });
    </script>
</body>
</html>