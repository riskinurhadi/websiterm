<?php
/**
 * File: admin/prestasi.php
 * Deskripsi: Halaman kelola data prestasi siswa/santri.
 * Fitur: Tambah (Upload Foto), Tampil, Edit, Hapus (CRUD) data prestasi.
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
$upload_dir = '../uploads/prestasi/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// ==============================================
// 1. PROSES TAMBAH PRESTASI
// ==============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_prestasi'])) {
    $judul     = mysqli_real_escape_string($conn, trim($_POST['judul']));
    $deskripsi = mysqli_real_escape_string($conn, trim($_POST['deskripsi']));
    $tingkat   = mysqli_real_escape_string($conn, trim($_POST['tingkat']));
    $tahun     = mysqli_real_escape_string($conn, trim($_POST['tahun']));
    
    $foto_path = '';

    // Proses Upload File Foto
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $file_tmp  = $_FILES['gambar']['tmp_name'];
        $file_name = $_FILES['gambar']['name'];
        $file_ext  = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed   = ['jpg', 'jpeg', 'png', 'webp'];
        
        if (in_array($file_ext, $allowed)) {
            $new_file_name = 'prestasi_' . time() . '_' . uniqid() . '.' . $file_ext;
            $dest_path = $upload_dir . $new_file_name;
            
            if (move_uploaded_file($file_tmp, $dest_path)) {
                $foto_path = 'uploads/prestasi/' . $new_file_name; 
            }
        }
    }

    $query = "INSERT INTO prestasi (judul, deskripsi, tingkat, tahun, gambar) 
              VALUES ('$judul', '$deskripsi', '$tingkat', '$tahun', '$foto_path')";
              
    if (mysqli_query($conn, $query)) {
        $status_aksi = 'tambah_success';
    } else {
        $status_aksi = 'error';
    }
}

// ==============================================
// 2. PROSES EDIT PRESTASI
// ==============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_prestasi'])) {
    $id_prestasi = (int) $_POST['id_prestasi'];
    $judul       = mysqli_real_escape_string($conn, trim($_POST['judul']));
    $deskripsi   = mysqli_real_escape_string($conn, trim($_POST['deskripsi']));
    $tingkat     = mysqli_real_escape_string($conn, trim($_POST['tingkat']));
    $tahun       = mysqli_real_escape_string($conn, trim($_POST['tahun']));
    
    $foto_path   = mysqli_real_escape_string($conn, trim($_POST['gambar_lama']));

    // Cek upload foto baru
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $file_tmp  = $_FILES['gambar']['tmp_name'];
        $file_name = $_FILES['gambar']['name'];
        $file_ext  = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed   = ['jpg', 'jpeg', 'png', 'webp'];
        
        if (in_array($file_ext, $allowed)) {
            $new_file_name = 'prestasi_' . time() . '_' . uniqid() . '.' . $file_ext;
            $dest_path = $upload_dir . $new_file_name;
            
            if (move_uploaded_file($file_tmp, $dest_path)) {
                // Hapus foto lama
                if (!empty($foto_path) && strpos($foto_path, 'uploads/') !== false && file_exists('../' . $foto_path)) {
                    unlink('../' . $foto_path);
                }
                $foto_path = 'uploads/prestasi/' . $new_file_name;
            }
        }
    }

    $query = "UPDATE prestasi SET 
                judul = '$judul',
                deskripsi = '$deskripsi',
                tingkat = '$tingkat',
                tahun = '$tahun',
                gambar = '$foto_path'
              WHERE id_prestasi = $id_prestasi";
              
    if (mysqli_query($conn, $query)) {
        $status_aksi = 'edit_success';
    } else {
        $status_aksi = 'error';
    }
}

// ==============================================
// 3. PROSES HAPUS PRESTASI
// ==============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hapus_prestasi'])) {
    $id_hapus = (int) $_POST['id_hapus'];
    
    // Hapus fisik file
    $q_file = mysqli_query($conn, "SELECT gambar FROM prestasi WHERE id_prestasi = $id_hapus");
    if ($q_file && mysqli_num_rows($q_file) > 0) {
        $data_file = mysqli_fetch_assoc($q_file);
        $foto_lama = $data_file['gambar'];
        if (!empty($foto_lama) && strpos($foto_lama, 'uploads/') !== false && file_exists('../' . $foto_lama)) {
            unlink('../' . $foto_lama);
        }
    }
    
    $query = "DELETE FROM prestasi WHERE id_prestasi = $id_hapus";
    if (mysqli_query($conn, $query)) {
        $status_aksi = 'hapus_success';
    } else {
        $status_aksi = 'error';
    }
}

// Mengambil Data Prestasi
$query_prestasi = mysqli_query($conn, "SELECT * FROM prestasi ORDER BY tahun DESC, id_prestasi DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Prestasi - Admin PP Raudlatul Muta'allimin</title>
    
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

        .badge-tingkat {
            background-color: #f1f5f9;
            color: var(--kemenag-green);
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            border: 1px solid rgba(0,168,107,0.2);
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
                                <i class="fas fa-trophy"></i>
                            </div>
                            <div>
                                <h4 class="fw-bold text-dark mb-1" style="font-weight: 800; letter-spacing: -0.5px;">Prestasi Santri</h4>
                                <p class="text-muted small mb-0">Kelola daftar pencapaian dan kejuaraan siswa-siswi pesantren.</p>
                            </div>
                        </div>
                        <div>
                            <button type="button" class="btn btn-pill-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
                                <i class="fas fa-plus me-2"></i> Tambah Prestasi
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
                                        <th width="30%">Judul Prestasi</th>
                                        <th width="15%">Tingkat</th>
                                        <th width="10%">Tahun</th>
                                        <th width="15%" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($query_prestasi && mysqli_num_rows($query_prestasi) > 0): ?>
                                        <?php $no = 1; while ($row = mysqli_fetch_assoc($query_prestasi)): ?>
                                        <tr>
                                            <td class="text-muted fw-semibold"><?php echo $no++; ?></td>
                                            <td>
                                                <?php 
                                                    $foto_src = $row['gambar'];
                                                    if (empty($foto_src)) {
                                                        $foto_src = 'https://images.unsplash.com/photo-1517841905240-472988babdf9?q=80&w=200&h=150&fit=crop';
                                                    } elseif (!filter_var($foto_src, FILTER_VALIDATE_URL)) {
                                                        $foto_src = '../' . $foto_src;
                                                    }
                                                ?>
                                                <img src="<?php echo htmlspecialchars($foto_src); ?>" class="thumbnail-table shadow-sm" alt="Thumbnail">
                                            </td>
                                            <td>
                                                <h6 class="fw-bold mb-1 text-dark" style="font-size: 0.95rem;"><?php echo htmlspecialchars($row['judul']); ?></h6>
                                                <p class="mb-0 text-muted" style="font-size: 0.8rem; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                                    <?php echo htmlspecialchars($row['deskripsi']); ?>
                                                </p>
                                            </td>
                                            <td>
                                                <span class="badge-tingkat"><?php echo htmlspecialchars($row['tingkat']); ?></span>
                                            </td>
                                            <td>
                                                <span class="fw-semibold text-dark"><?php echo htmlspecialchars($row['tahun']); ?></span>
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-2">
                                                    <button type="button" class="btn-action btn-edit" data-bs-toggle="modal" data-bs-target="#modalEdit<?php echo $row['id_prestasi']; ?>" title="Edit Data">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button" class="btn-action btn-delete" onclick="konfirmasiHapus(<?php echo $row['id_prestasi']; ?>)" title="Hapus Data">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- Modal Edit untuk setiap baris -->
                                        <div class="modal fade" id="modalEdit<?php echo $row['id_prestasi']; ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content" style="border-radius: 20px; border: none;">
                                                    <div class="modal-header border-0 pb-0">
                                                        <h5 class="modal-title fw-bold">Edit Data Prestasi</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <form action="prestasi.php" method="POST" enctype="multipart/form-data">
                                                        <div class="modal-body">
                                                            <input type="hidden" name="id_prestasi" value="<?php echo $row['id_prestasi']; ?>">
                                                            <input type="hidden" name="gambar_lama" value="<?php echo htmlspecialchars($row['gambar']); ?>">
                                                            
                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold small text-dark">Judul Prestasi</label>
                                                                <input type="text" class="form-control form-control-custom" name="judul" value="<?php echo htmlspecialchars($row['judul']); ?>" required>
                                                            </div>
                                                            <div class="row g-3 mb-3">
                                                                <div class="col-md-6">
                                                                    <label class="form-label fw-semibold small text-dark">Tingkat</label>
                                                                    <select class="form-select form-control-custom" name="tingkat" required>
                                                                        <option value="Kecamatan" <?php echo ($row['tingkat'] == 'Kecamatan') ? 'selected' : ''; ?>>Kecamatan</option>
                                                                        <option value="Kabupaten" <?php echo ($row['tingkat'] == 'Kabupaten') ? 'selected' : ''; ?>>Kabupaten</option>
                                                                        <option value="Provinsi" <?php echo ($row['tingkat'] == 'Provinsi') ? 'selected' : ''; ?>>Provinsi</option>
                                                                        <option value="Nasional" <?php echo ($row['tingkat'] == 'Nasional') ? 'selected' : ''; ?>>Nasional</option>
                                                                        <option value="Internasional" <?php echo ($row['tingkat'] == 'Internasional') ? 'selected' : ''; ?>>Internasional</option>
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label class="form-label fw-semibold small text-dark">Tahun</label>
                                                                    <input type="number" class="form-control form-control-custom" name="tahun" value="<?php echo htmlspecialchars($row['tahun']); ?>" required>
                                                                </div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold small text-dark">Ganti Gambar (Opsional)</label>
                                                                <input type="file" class="form-control form-control-custom" name="gambar" accept="image/png, image/jpeg, image/jpg, image/webp">
                                                                <small class="text-muted" style="font-size: 0.7rem;">Kosongkan jika tidak ingin mengubah foto dokumentasi prestasi.</small>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold small text-dark">Deskripsi</label>
                                                                <textarea class="form-control form-control-custom" name="deskripsi" rows="3"><?php echo htmlspecialchars($row['deskripsi']); ?></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer border-0 pt-0">
                                                            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit" name="edit_prestasi" class="btn btn-pill-primary px-4 m-0">Simpan Perubahan</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center py-5 text-muted">
                                                <i class="fas fa-award fs-1 mb-3 opacity-25"></i>
                                                <p class="mb-0">Belum ada data prestasi yang dicatat.</p>
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

    <!-- Modal Tambah -->
    <div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 20px; border: none;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Tambah Prestasi Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="prestasi.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-dark">Judul Prestasi</label>
                            <input type="text" class="form-control form-control-custom" name="judul" placeholder="Contoh: Juara 1 Lomba Pidato Bahasa Arab" required>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-dark">Tingkat</label>
                                <select class="form-select form-control-custom" name="tingkat" required>
                                    <option value="" selected disabled>-- Pilih Tingkat --</option>
                                    <option value="Kecamatan">Kecamatan</option>
                                    <option value="Kabupaten">Kabupaten</option>
                                    <option value="Provinsi Lampung">Provinsi</option>
                                    <option value="Nasional">Nasional</option>
                                    <option value="Internasional">Internasional</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-dark">Tahun</label>
                                <input type="number" class="form-control form-control-custom" name="tahun" value="<?php echo date('Y'); ?>" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-dark">Upload Gambar/Dokumentasi</label>
                            <input type="file" class="form-control form-control-custom" name="gambar" accept="image/png, image/jpeg, image/jpg, image/webp" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-dark">Deskripsi</label>
                            <textarea class="form-control form-control-custom" name="deskripsi" rows="3" placeholder="Jelaskan secara singkat mengenai kejuaraan ini..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="tambah_prestasi" class="btn btn-pill-primary px-4 m-0">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Form Hapus (Hidden) -->
    <form id="formHapus" action="prestasi.php" method="POST" style="display: none;">
        <input type="hidden" name="id_hapus" id="inputIdHapus">
        <input type="hidden" name="hapus_prestasi" value="1">
    </form>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- SweetAlert2 Logic -->
    <script>
        function konfirmasiHapus(id) {
            Swal.fire({
                title: 'Hapus Prestasi?',
                text: "Data dan foto prestasi ini akan dihapus secara permanen.",
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
                Swal.fire({ icon: 'success', title: 'Berhasil!', text: 'Data prestasi berhasil ditambahkan.', confirmButtonColor: '#00a86b' });
            <?php elseif ($status_aksi === 'edit_success'): ?>
                Swal.fire({ icon: 'success', title: 'Berhasil!', text: 'Data prestasi berhasil diperbarui.', confirmButtonColor: '#00a86b' });
            <?php elseif ($status_aksi === 'hapus_success'): ?>
                Swal.fire({ icon: 'success', title: 'Terhapus!', text: 'Data prestasi berhasil dihapus.', confirmButtonColor: '#00a86b' });
            <?php elseif ($status_aksi === 'error'): ?>
                Swal.fire({ icon: 'error', title: 'Gagal!', text: 'Terjadi kesalahan saat memproses data ke database.', confirmButtonColor: '#d33' });
            <?php endif; ?>
        });
    </script>
</body>
</html>