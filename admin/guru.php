<?php
/**
 * File: admin/guru.php
 * Deskripsi: Halaman kelola data Asatidz dan Asatidzah (Dewan Guru).
 * Dilengkapi dengan fitur Auto-Compress Image menggunakan PHP GD Library.
 */

session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

include '../koneksi.php';

$status_aksi = null;

$upload_dir = '../uploads/guru/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

/**
 * Fungsi Auto-Compress Image (Menurunkan ukuran file tanpa merusak kualitas)
 */
function compressImage($source, $destination, $quality) {
    $info = getimagesize($source);

    if ($info['mime'] == 'image/jpeg') {
        $image = imagecreatefromjpeg($source);
        imagejpeg($image, $destination, $quality); // 0-100
    } elseif ($info['mime'] == 'image/png') {
        $image = imagecreatefrompng($source);
        // Menjaga transparansi PNG
        imageAlphaBlending($image, true);
        imageSaveAlpha($image, true);
        // Konversi kualitas 0-100 ke 0-9 untuk PNG
        $pngQuality = ($quality - 100) / 11.111111;
        $pngQuality = round(abs($pngQuality));
        imagepng($image, $destination, $pngQuality);
    } elseif ($info['mime'] == 'image/webp') {
        $image = imagecreatefromwebp($source);
        imagewebp($image, $destination, $quality);
    } else {
        // Fallback jika format tidak didukung GD
        move_uploaded_file($source, $destination);
    }
    
    if (isset($image)) {
        imagedestroy($image);
    }
    return $destination;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_guru'])) {
    $nama_lengkap  = mysqli_real_escape_string($conn, trim($_POST['nama_lengkap']));
    $tempat_lahir  = mysqli_real_escape_string($conn, trim($_POST['tempat_lahir']));
    $tanggal_lahir = mysqli_real_escape_string($conn, trim($_POST['tanggal_lahir']));
    $jabatan       = mysqli_real_escape_string($conn, trim($_POST['jabatan']));
    
    $foto_path = 'default_guru.jpg';

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $file_tmp  = $_FILES['foto']['tmp_name'];
        $file_name = $_FILES['foto']['name'];
        $file_ext  = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed   = ['jpg', 'jpeg', 'png', 'webp'];
        
        if (in_array($file_ext, $allowed)) {
            $new_file_name = 'guru_' . time() . '_' . uniqid() . '.' . $file_ext;
            $dest_path = $upload_dir . $new_file_name;
            
            // Menggunakan Fungsi Kompresi (Kualitas 60% sudah sangat cukup & menghemat size)
            compressImage($file_tmp, $dest_path, 60);
            $foto_path = 'uploads/guru/' . $new_file_name; 
        }
    }

    $query = "INSERT INTO guru (nama_lengkap, tempat_lahir, tanggal_lahir, jabatan, foto) 
              VALUES ('$nama_lengkap', '$tempat_lahir', '$tanggal_lahir', '$jabatan', '$foto_path')";
              
    if (mysqli_query($conn, $query)) {
        $status_aksi = 'tambah_success';
    } else {
        $status_aksi = 'error';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_guru'])) {
    $id_guru       = (int) $_POST['id_guru'];
    $nama_lengkap  = mysqli_real_escape_string($conn, trim($_POST['nama_lengkap']));
    $tempat_lahir  = mysqli_real_escape_string($conn, trim($_POST['tempat_lahir']));
    $tanggal_lahir = mysqli_real_escape_string($conn, trim($_POST['tanggal_lahir']));
    $jabatan       = mysqli_real_escape_string($conn, trim($_POST['jabatan']));
    
    $foto_path     = mysqli_real_escape_string($conn, trim($_POST['foto_lama']));

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $file_tmp  = $_FILES['foto']['tmp_name'];
        $file_name = $_FILES['foto']['name'];
        $file_ext  = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed   = ['jpg', 'jpeg', 'png', 'webp'];
        
        if (in_array($file_ext, $allowed)) {
            $new_file_name = 'guru_' . time() . '_' . uniqid() . '.' . $file_ext;
            $dest_path = $upload_dir . $new_file_name;
            
            // Kompres foto baru
            compressImage($file_tmp, $dest_path, 60);
            
            // Hapus foto lama
            if (!empty($foto_path) && $foto_path !== 'default_guru.jpg' && file_exists('../' . $foto_path)) {
                unlink('../' . $foto_path);
            }
            $foto_path = 'uploads/guru/' . $new_file_name;
        }
    }

    $query = "UPDATE guru SET 
                nama_lengkap = '$nama_lengkap',
                tempat_lahir = '$tempat_lahir',
                tanggal_lahir = '$tanggal_lahir',
                jabatan = '$jabatan',
                foto = '$foto_path'
              WHERE id_guru = $id_guru";
              
    if (mysqli_query($conn, $query)) {
        $status_aksi = 'edit_success';
    } else {
        $status_aksi = 'error';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hapus_guru'])) {
    $id_hapus = (int) $_POST['id_hapus'];
    
    $q_file = mysqli_query($conn, "SELECT foto FROM guru WHERE id_guru = $id_hapus");
    if ($q_file && mysqli_num_rows($q_file) > 0) {
        $data_file = mysqli_fetch_assoc($q_file);
        $foto_lama = $data_file['foto'];
        if (!empty($foto_lama) && $foto_lama !== 'default_guru.jpg' && file_exists('../' . $foto_lama)) {
            unlink('../' . $foto_lama);
        }
    }
    
    if (mysqli_query($conn, "DELETE FROM guru WHERE id_guru = $id_hapus")) {
        $status_aksi = 'hapus_success';
    } else {
        $status_aksi = 'error';
    }
}

// Ambil data untuk tabel
$query_guru = mysqli_query($conn, "SELECT * FROM guru ORDER BY id_guru DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Dewan Guru - Admin PP Raudlatul Muta'allimin</title>
    
    <!-- Bootstrap & Vendor CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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

        body { height: 100vh; overflow: hidden; background-color: var(--bg-neutral-light); font-family: 'Poppins', sans-serif; }
        .admin-main-content-scroll { height: 100vh; overflow-y: auto; padding: 30px; }
        .admin-main-content-scroll::-webkit-scrollbar { width: 6px; }
        .admin-main-content-scroll::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.1); border-radius: 10px; }

        .page-header-card { background: #fff; border-radius: 20px; padding: 24px 30px; border: 1px solid rgba(0,0,0,0.03); margin-bottom: 25px; display: flex; align-items: center; justify-content: space-between; }
        .header-icon { width: 50px; height: 50px; border-radius: 14px; background: #e6f7f1; color: var(--kemenag-green); display: flex; align-items: center; justify-content: center; font-size: 1.4rem; }
        
        .widget-card { background: #fff; border-radius: 20px; padding: 25px; border: 1px solid rgba(0,0,0,0.03); }
        .btn-pill-primary { background: var(--kemenag-green); color: #fff; border-radius: 50px; padding: 10px 24px; font-weight: 600; border: none; }
        
        .table-custom thead th { background: #f8fafc; color: var(--slate-500); font-size: 0.8rem; text-transform: uppercase; padding: 15px; }
        .table-custom tbody td { padding: 15px; vertical-align: middle; border-bottom: 1px solid rgba(0,0,0,0.03); }
        .avatar-table { width: 45px; height: 45px; border-radius: 50%; object-fit: cover; border: 2px solid #e6f7f1; }
        
        .btn-action { width: 35px; height: 35px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; border: none; font-size: 0.85rem; }
        .btn-edit { background: #fffbeb; color: #d97706; }
        .btn-delete { background: #fef2f2; color: #dc2626; }
        .form-control-custom { border-radius: 12px; padding: 12px 16px; background-color: var(--bg-neutral-light); border: 1px solid rgba(0,0,0,0.05); }
    </style>
</head>
<body>

    <div class="container-fluid p-0">
        <div class="row g-0">
            <div class="col-lg-3 col-xl-2 d-none d-lg-block h-100">
                <?php include 'sidebar.php'; ?>
            </div>

            <!-- Area Konten Utama -->
            <div class="col-lg-9 col-xl-10 h-100">
                <div class="admin-main-content-scroll">
                    
                    <div class="page-header-card flex-column flex-md-row gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="header-icon"><i class="fas fa-chalkboard-teacher"></i></div>
                            <div>
                                <h4 class="fw-bold text-dark mb-1">Asatidz & Asatidzah</h4>
                                <p class="text-muted small mb-0">Kelola data dewan guru. <span class="badge bg-success bg-opacity-10 text-success ms-1">Auto-Compress Aktif</span></p>
                            </div>
                        </div>
                        <div>
                            <button type="button" class="btn btn-pill-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
                                <i class="fas fa-plus me-2"></i> Tambah Guru
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
                                        <th width="30%">Nama & TTL</th>
                                        <th width="25%">Jabatan / Mapel</th>
                                        <th width="15%" class="text-center">Foto</th>
                                        <th width="15%" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($query_guru && mysqli_num_rows($query_guru) > 0): ?>
                                        <?php $no = 1; while ($row = mysqli_fetch_assoc($query_guru)): ?>
                                        <tr>
                                            <td class="text-muted"><?php echo $no++; ?></td>
                                            <td>
                                                <h6 class="fw-bold mb-1 text-dark"><?php echo htmlspecialchars($row['nama_lengkap']); ?></h6>
                                                <span class="text-muted small"><i class="fas fa-map-marker-alt me-1"></i> <?php echo htmlspecialchars($row['tempat_lahir']); ?>, <?php echo date('d M Y', strtotime($row['tanggal_lahir'])); ?></span>
                                            </td>
                                            <td>
                                                <span class="badge bg-light border text-dark px-2 py-1"><?php echo htmlspecialchars($row['jabatan']); ?></span>
                                            </td>
                                            <td class="text-center">
                                                <?php 
                                                    $foto_src = $row['foto'];
                                                    if ($foto_src === 'default_guru.jpg' || empty($foto_src)) {
                                                        $foto_src = 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?q=80&w=256&h=256&fit=crop';
                                                    } elseif (!filter_var($foto_src, FILTER_VALIDATE_URL)) {
                                                        $foto_src = '../' . $foto_src;
                                                    }
                                                ?>
                                                <img src="<?php echo htmlspecialchars($foto_src); ?>" class="avatar-table shadow-sm" alt="Foto">
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-2">
                                                    <button type="button" class="btn-action btn-edit" data-bs-toggle="modal" data-bs-target="#modalEdit<?php echo $row['id_guru']; ?>"><i class="fas fa-edit"></i></button>
                                                    <button type="button" class="btn-action btn-delete" onclick="konfirmasiHapus(<?php echo $row['id_guru']; ?>)"><i class="fas fa-trash-alt"></i></button>
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- Modal Edit -->
                                        <div class="modal fade" id="modalEdit<?php echo $row['id_guru']; ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0" style="border-radius: 20px;">
                                                    <div class="modal-header border-0 pb-0">
                                                        <h5 class="modal-title fw-bold">Edit Data Guru</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form action="guru.php" method="POST" enctype="multipart/form-data">
                                                        <div class="modal-body">
                                                            <input type="hidden" name="id_guru" value="<?php echo $row['id_guru']; ?>">
                                                            <input type="hidden" name="foto_lama" value="<?php echo htmlspecialchars($row['foto']); ?>">
                                                            
                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold small">Nama Lengkap & Gelar</label>
                                                                <input type="text" class="form-control form-control-custom" name="nama_lengkap" value="<?php echo htmlspecialchars($row['nama_lengkap']); ?>" required>
                                                            </div>
                                                            <div class="row g-3 mb-3">
                                                                <div class="col-md-6">
                                                                    <label class="form-label fw-semibold small">Tempat Lahir</label>
                                                                    <input type="text" class="form-control form-control-custom" name="tempat_lahir" value="<?php echo htmlspecialchars($row['tempat_lahir']); ?>" required>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label class="form-label fw-semibold small">Tanggal Lahir</label>
                                                                    <input type="date" class="form-control form-control-custom" name="tanggal_lahir" value="<?php echo htmlspecialchars($row['tanggal_lahir']); ?>" required>
                                                                </div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold small">Jabatan / Mapel Utama</label>
                                                                <input type="text" class="form-control form-control-custom" name="jabatan" value="<?php echo htmlspecialchars($row['jabatan']); ?>" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold small">Ganti Foto (Otomatis Terkompresi)</label>
                                                                <input type="file" class="form-control form-control-custom" name="foto" accept="image/png, image/jpeg, image/jpg, image/webp">
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer border-0 pt-0">
                                                            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit" name="edit_guru" class="btn btn-pill-primary px-4">Simpan</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr><td colspan="5" class="text-center py-5 text-muted"><i class="fas fa-user-slash fs-1 mb-3 opacity-25"></i><p>Belum ada data guru.</p></td></tr>
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
            <div class="modal-content border-0" style="border-radius: 20px;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Tambah Data Guru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="guru.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Nama Lengkap & Gelar</label>
                            <input type="text" class="form-control form-control-custom" name="nama_lengkap" placeholder="Contoh: Ust. Ahmad, S.Pd" required>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Tempat Lahir</label>
                                <input type="text" class="form-control form-control-custom" name="tempat_lahir" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Tanggal Lahir</label>
                                <input type="date" class="form-control form-control-custom" name="tanggal_lahir" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Jabatan / Mapel Utama</label>
                            <input type="text" class="form-control form-control-custom" name="jabatan" placeholder="Contoh: Guru Bahasa Arab" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Upload Foto (Opsional - Auto Kompresi)</label>
                            <input type="file" class="form-control form-control-custom" name="foto" accept="image/png, image/jpeg, image/jpg, image/webp">
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="tambah_guru" class="btn btn-pill-primary px-4">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Form Hapus -->
    <form id="formHapus" action="guru.php" method="POST" style="display: none;">
        <input type="hidden" name="id_hapus" id="inputIdHapus">
        <input type="hidden" name="hapus_guru" value="1">
    </form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function konfirmasiHapus(id) {
            Swal.fire({
                title: 'Hapus Data Guru?',
                text: "Profil dan foto guru akan dihapus permanen.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('inputIdHapus').value = id;
                    document.getElementById('formHapus').submit();
                }
            });
        }

        <?php if ($status_aksi === 'tambah_success'): ?>
            Swal.fire({ icon: 'success', title: 'Berhasil!', text: 'Data guru berhasil ditambahkan & foto dikompresi.', confirmButtonColor: '#00a86b' });
        <?php elseif ($status_aksi === 'edit_success'): ?>
            Swal.fire({ icon: 'success', title: 'Diperbarui!', text: 'Data guru berhasil diubah.', confirmButtonColor: '#00a86b' });
        <?php elseif ($status_aksi === 'hapus_success'): ?>
            Swal.fire({ icon: 'success', title: 'Terhapus!', text: 'Data guru berhasil dihapus dari sistem.', confirmButtonColor: '#00a86b' });
        <?php elseif ($status_aksi === 'error'): ?>
            Swal.fire({ icon: 'error', title: 'Gagal!', text: 'Terjadi kesalahan pemrosesan database.', confirmButtonColor: '#d33' });
        <?php endif; ?>
    </script>
</body>
</html>