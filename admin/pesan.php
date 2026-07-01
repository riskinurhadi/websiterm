<?php
/**
 * File: admin/pesan.php
 * Deskripsi: Halaman kelola pesan masuk dari form kontak pengunjung website.
 * Fitur: Tampil Pesan, Baca Detail, Tandai Sudah Dibaca, Hapus Pesan.
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

// ==============================================
// 1. PROSES TANDAI SUDAH DIBACA
// ==============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tandai_dibaca'])) {
    $id_pesan = (int) $_POST['id_pesan'];
    
    $query = "UPDATE pesan_masuk SET status_baca = 'Sudah' WHERE id_pesan = $id_pesan";
    if (mysqli_query($conn, $query)) {
        $status_aksi = 'read_success';
    } else {
        $status_aksi = 'error';
    }
}

// ==============================================
// 2. PROSES HAPUS PESAN
// ==============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hapus_pesan'])) {
    $id_hapus = (int) $_POST['id_hapus'];
    
    $query = "DELETE FROM pesan_masuk WHERE id_pesan = $id_hapus";
    if (mysqli_query($conn, $query)) {
        $status_aksi = 'hapus_success';
    } else {
        $status_aksi = 'error';
    }
}

// Mengambil Data Pesan (Diurutkan: Pesan Belum Dibaca Tampil Paling Atas, lalu berdasarkan Tanggal Terbaru)
$query_pesan = mysqli_query($conn, "SELECT * FROM pesan_masuk ORDER BY status_baca ASC, tanggal_kirim DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kotak Pesan Masuk - Admin PP Raudlatul Muta'allimin</title>
    
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
            transition: var(--transition-smooth);
        }

        /* Gaya Khusus Pesan Belum Dibaca */
        .row-unread {
            background-color: rgba(0, 168, 107, 0.03);
        }
        .row-unread td {
            font-weight: 600;
            color: #0f172a;
        }

        .badge-status-new { background-color: #3b82f6; color: #ffffff; padding: 5px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; letter-spacing: 0.5px;}
        .badge-status-read { background-color: #f1f5f9; color: #64748b; padding: 5px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; letter-spacing: 0.5px;}

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
        .btn-view { background-color: #e0f2fe; color: #0284c7; }
        .btn-view:hover { background-color: #bae6fd; transform: translateY(-2px); }
        .btn-read { background-color: #d1fae5; color: #059669; }
        .btn-read:hover { background-color: #a7f3d0; transform: translateY(-2px); }
        .btn-delete { background-color: #fef2f2; color: #dc2626; }
        .btn-delete:hover { background-color: #fee2e2; transform: translateY(-2px); }
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
                                <i class="fas fa-envelope-open-text"></i>
                            </div>
                            <div>
                                <h4 class="fw-bold text-dark mb-1" style="font-weight: 800; letter-spacing: -0.5px;">Kotak Pesan Masuk</h4>
                                <p class="text-muted small mb-0">Baca dan kelola surat pertanyaan dari pengunjung website.</p>
                            </div>
                        </div>
                        <div>
                            <!-- Indikator Jumlah Pesan Belum Dibaca -->
                            <?php 
                                $q_unread = mysqli_query($conn, "SELECT COUNT(*) as unread FROM pesan_masuk WHERE status_baca = 'Belum'");
                                $c_unread = mysqli_fetch_assoc($q_unread)['unread'];
                            ?>
                            <span class="badge bg-danger rounded-pill px-3 py-2 fw-semibold" style="font-size: 0.85rem; box-shadow: 0 4px 10px rgba(220, 38, 38, 0.2);">
                                <i class="fas fa-bell me-1"></i> <?php echo $c_unread; ?> Pesan Baru
                            </span>
                        </div>
                    </div>

                    <!-- Tabel Data -->
                    <div class="widget-card">
                        <div class="table-responsive">
                            <table class="table table-custom table-hover">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="20%">Nama & Email</th>
                                        <th width="20%">Waktu Diterima</th>
                                        <th width="25%">Subjek Pesan</th>
                                        <th width="10%" class="text-center">Status</th>
                                        <th width="20%" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($query_pesan && mysqli_num_rows($query_pesan) > 0): ?>
                                        <?php $no = 1; while ($row = mysqli_fetch_assoc($query_pesan)): ?>
                                        <tr class="<?php echo ($row['status_baca'] == 'Belum') ? 'row-unread' : ''; ?>">
                                            <td class="text-muted"><?php echo $no++; ?></td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span class="fw-bold text-dark" style="font-size: 0.95rem;"><?php echo htmlspecialchars($row['nama_pengirim']); ?></span>
                                                    <span class="text-muted small" style="font-size: 0.75rem;"><i class="fas fa-at me-1"></i> <?php echo htmlspecialchars($row['email']); ?></span>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="text-muted small" style="font-size: 0.85rem;"><i class="far fa-clock me-1"></i> <?php echo date('d M Y - H:i', strtotime($row['tanggal_kirim'])); ?></span>
                                            </td>
                                            <td>
                                                <span class="d-block text-truncate" style="max-width: 250px;">
                                                    <?php echo htmlspecialchars($row['subjek']); ?>
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <?php if ($row['status_baca'] == 'Belum'): ?>
                                                    <span class="badge-status-new">Baru</span>
                                                <?php else: ?>
                                                    <span class="badge-status-read">Dibaca</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-2">
                                                    <!-- Tombol Baca Modal -->
                                                    <button type="button" class="btn-action btn-view" data-bs-toggle="modal" data-bs-target="#modalDetail<?php echo $row['id_pesan']; ?>" title="Baca Pesan">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    
                                                    <!-- Tombol Langsung Tandai Dibaca (Jika belum) -->
                                                    <?php if ($row['status_baca'] == 'Belum'): ?>
                                                    <form action="pesan.php" method="POST" class="d-inline m-0">
                                                        <input type="hidden" name="id_pesan" value="<?php echo $row['id_pesan']; ?>">
                                                        <button type="submit" name="tandai_dibaca" class="btn-action btn-read" title="Tandai Sudah Dibaca">
                                                            <i class="fas fa-check-double"></i>
                                                        </button>
                                                    </form>
                                                    <?php endif; ?>

                                                    <!-- Tombol Hapus -->
                                                    <button type="button" class="btn-action btn-delete" onclick="konfirmasiHapus(<?php echo $row['id_pesan']; ?>)" title="Hapus Pesan">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- Modal Detail Pesan -->
                                        <div class="modal fade" id="modalDetail<?php echo $row['id_pesan']; ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                                <div class="modal-content" style="border-radius: 20px; border: none;">
                                                    <div class="modal-header border-0 pb-0">
                                                        <h5 class="modal-title fw-bold">Detail Surat Masuk</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body pt-4">
                                                        <div class="row mb-4">
                                                            <div class="col-md-6 mb-3 mb-md-0">
                                                                <small class="text-muted fw-semibold text-uppercase d-block mb-1" style="font-size: 0.7rem; letter-spacing: 1px;">Pengirim</small>
                                                                <h6 class="fw-bold text-dark mb-0"><?php echo htmlspecialchars($row['nama_pengirim']); ?></h6>
                                                                <span class="text-muted small"><?php echo htmlspecialchars($row['email']); ?></span>
                                                            </div>
                                                            <div class="col-md-6 text-md-end">
                                                                <small class="text-muted fw-semibold text-uppercase d-block mb-1" style="font-size: 0.7rem; letter-spacing: 1px;">Waktu Kirim</small>
                                                                <span class="badge bg-light text-dark border px-3 py-2 fw-semibold">
                                                                    <i class="far fa-calendar-alt text-success me-1"></i> <?php echo date('d F Y, H:i', strtotime($row['tanggal_kirim'])); ?>
                                                                </span>
                                                            </div>
                                                        </div>

                                                        <div class="p-4 rounded-4" style="background-color: var(--bg-neutral-light); border: 1px solid rgba(0,0,0,0.03);">
                                                            <h6 class="fw-bold text-dark mb-3" style="border-bottom: 2px solid rgba(0,0,0,0.05); padding-bottom: 10px;">
                                                                Subjek: <span style="color: var(--kemenag-green-primary);"><?php echo htmlspecialchars($row['subjek']); ?></span>
                                                            </h6>
                                                            <p class="mb-0 text-dark" style="line-height: 1.8; white-space: pre-wrap;"><?php echo htmlspecialchars($row['pesan']); ?></p>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer border-0 pt-0">
                                                        <?php if ($row['status_baca'] == 'Belum'): ?>
                                                            <!-- Jika Belum Dibaca, sediakan form tandai & tutup -->
                                                            <form action="pesan.php" method="POST" class="w-100 d-flex justify-content-end gap-2">
                                                                <input type="hidden" name="id_pesan" value="<?php echo $row['id_pesan']; ?>">
                                                                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Tutup Saja</button>
                                                                <button type="submit" name="tandai_dibaca" class="btn btn-pill-primary px-4 m-0"><i class="fas fa-check-double me-1"></i> Tandai Sudah Dibaca</button>
                                                            </form>
                                                        <?php else: ?>
                                                            <!-- Jika Sudah Dibaca -->
                                                            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Tutup Pesan</button>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center py-5 text-muted">
                                                <i class="far fa-envelope-open fs-1 mb-3 opacity-25"></i>
                                                <p class="mb-0">Tidak ada pesan yang masuk di kotak masuk Anda.</p>
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

    <!-- Form Hapus (Hidden) -->
    <form id="formHapus" action="pesan.php" method="POST" style="display: none;">
        <input type="hidden" name="id_hapus" id="inputIdHapus">
        <input type="hidden" name="hapus_pesan" value="1">
    </form>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- SweetAlert2 Logic -->
    <script>
        function konfirmasiHapus(id) {
            Swal.fire({
                title: 'Hapus Surat?',
                text: "Surat / Pesan masuk ini akan dihapus secara permanen dari database.",
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
            <?php if ($status_aksi === 'read_success'): ?>
                Swal.fire({ icon: 'success', title: 'Ditandai!', text: 'Pesan berhasil ditandai sebagai sudah dibaca.', confirmButtonColor: '#00a86b', timer: 2000, showConfirmButton: false });
            <?php elseif ($status_aksi === 'hapus_success'): ?>
                Swal.fire({ icon: 'success', title: 'Terhapus!', text: 'Surat masuk berhasil dibersihkan.', confirmButtonColor: '#00a86b', timer: 2000, showConfirmButton: false });
            <?php elseif ($status_aksi === 'error'): ?>
                Swal.fire({ icon: 'error', title: 'Gagal!', text: 'Terjadi kesalahan sistem saat memproses basis data.', confirmButtonColor: '#d33' });
            <?php endif; ?>
        });
    </script>
</body>
</html>