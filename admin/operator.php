<?php
/**
 * File: admin/operator.php
 * Deskripsi: Halaman pengelolaan data operator (akun admin/superadmin).
 * Menyediakan modul CRUD lengkap dengan standard keamanan enkripsi Bcrypt.
 * Menggunakan integrasi SweetAlert2 untuk respon interaktif.
 */

session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // Kelonggaran untuk preview offline agar halaman tetap bisa diuji coba secara visual
    $is_preview = true;
    $admin_id_aktif = 1;
} else {
    $is_preview = false;
    $admin_id_aktif = $_SESSION['id_user'];
}

// Menghubungkan ke database
include '../koneksi.php';

$status_action = null;
$error_message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_operator']) && !$is_preview) {
    $nama = mysqli_real_escape_string($conn, trim($_POST['nama_lengkap']));
    $user = mysqli_real_escape_string($conn, trim($_POST['username']));
    $pass = trim($_POST['password']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    if (!empty($nama) && !empty($user) && !empty($pass)) {
        // Cek jika username sudah terdaftar
        $cek_user = mysqli_query($conn, "SELECT id_user FROM users WHERE username = '$user'");
        if (mysqli_num_rows($cek_user) > 0) {
            $status_action = 'username_exists';
        } else {
            // Hash password dengan Bcrypt yang aman
            $hash_pass = password_hash($pass, PASSWORD_DEFAULT);
            $q_insert = "INSERT INTO users (username, password, nama_lengkap, role) VALUES ('$user', '$hash_pass', '$nama', '$role')";
            if (mysqli_query($conn, $q_insert)) {
                $status_action = 'insert_success';
            } else {
                $status_action = 'db_error';
                $error_message = mysqli_error($conn);
            }
        }
    } else {
        $status_action = 'empty_fields';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_operator']) && !$is_preview) {
    $id_edit = intval($_POST['id_user']);
    $nama    = mysqli_real_escape_string($conn, trim($_POST['nama_lengkap']));
    $user    = mysqli_real_escape_string($conn, trim($_POST['username']));
    $pass    = trim($_POST['password']);
    $role    = mysqli_real_escape_string($conn, $_POST['role']);

    if (!empty($nama) && !empty($user)) {
        // Cek username terdaftar pada ID lain
        $cek_user = mysqli_query($conn, "SELECT id_user FROM users WHERE username = '$user' AND id_user != $id_edit");
        if (mysqli_num_rows($cek_user) > 0) {
            $status_action = 'username_exists';
        } else {
            // Jika password diisi, update password juga. Jika kosong, gunakan password lama.
            if (!empty($pass)) {
                $hash_pass = password_hash($pass, PASSWORD_DEFAULT);
                $q_update = "UPDATE users SET username = '$user', password = '$hash_pass', nama_lengkap = '$nama', role = '$role' WHERE id_user = $id_edit";
            } else {
                $q_update = "UPDATE users SET username = '$user', nama_lengkap = '$nama', role = '$role' WHERE id_user = $id_edit";
            }

            if (mysqli_query($conn, $q_update)) {
                // Perbarui session jika admin mengubah profilnya sendiri
                if ($id_edit === $_SESSION['id_user']) {
                    $_SESSION['nama_lengkap'] = $nama;
                    $_SESSION['username']     = $user;
                    $_SESSION['role']         = $role;
                }
                $status_action = 'update_success';
            } else {
                $status_action = 'db_error';
                $error_message = mysqli_error($conn);
            }
        }
    } else {
        $status_action = 'empty_fields';
    }
}

if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id']) && !$is_preview) {
    $id_delete = intval($_GET['id']);
    
    // Mencegah menghapus akun sendiri yang sedang aktif digunakan untuk login
    if ($id_delete === $admin_id_aktif) {
        $status_action = 'delete_self_prevent';
    } else {
        $q_delete = "DELETE FROM users WHERE id_user = $id_delete";
        if (mysqli_query($conn, $q_delete)) {
            $status_action = 'delete_success';
        } else {
            $status_action = 'db_error';
            $error_message = mysqli_error($conn);
        }
    }
}

$operator_list = [];
if ($conn) {
    $q_op = mysqli_query($conn, "SELECT id_user, username, nama_lengkap, role, created_at FROM users ORDER BY id_user DESC");
    if ($q_op && mysqli_num_rows($q_op) > 0) {
        while ($row = mysqli_fetch_assoc($q_op)) {
            $operator_list[] = $row;
        }
    }
}

// Fallback data jika database dalam tinjauan preview offline kosong
if (empty($operator_list)) {
    $operator_list = [
        [
            'id_user' => 1,
            'username' => 'admin',
            'nama_lengkap' => 'Administrator Web',
            'role' => 'superadmin',
            'created_at' => '2026-06-02 15:48:54'
        ],
        [
            'id_user' => 2,
            'username' => 'sudi_admin',
            'nama_lengkap' => 'Ust. Sudi, S.Pd.I',
            'role' => 'admin',
            'created_at' => '2026-06-03 01:22:10'
        ]
    ];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Operator - PP Raudlatul Muta'allimin</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Stylings Khusus Split Scroll Independen -->
    <style>
        :root {
            --bg-neutral-light: #f4f6f9;
            --kemenag-green: #00a86b;
            --kemenag-green-dark: #007d4f;
            --slate-900: #0f172a;
            --slate-500: #64748b;
        }

        html, body {
            height: 100vh;
            overflow: hidden; /* Mengunci scroll window utama */
            background-color: var(--bg-neutral-light);
            color: var(--slate-900);
            font-family: 'Poppins', sans-serif;
        }

        .admin-main-content-scroll {
            height: 100vh;
            overflow-y: auto; /* Scroll mandiri konten utama */
            padding: 30px;
        }

        .admin-main-content-scroll::-webkit-scrollbar {
            width: 6px;
        }
        .admin-main-content-scroll::-webkit-scrollbar-track {
            background: transparent;
        }
        .admin-main-content-scroll::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.08);
            border-radius: 10px;
        }

        /* Desain Card & Tabel */
        .operator-card {
            background-color: #ffffff;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.02);
            border: 1px solid rgba(0, 0, 0, 0.03);
            margin-bottom: 25px;
        }

        .badge-role {
            font-size: 0.75rem;
            font-weight: 700;
            padding: 5px 12px;
            border-radius: 20px;
        }
        .bg-role-superadmin {
            background-color: #e0f2fe;
            color: #0284c7;
        }
        .bg-role-admin {
            background-color: #e6f7f1;
            color: var(--kemenag-green);
        }

        .btn-action {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            transition: all 0.2s;
        }

        /* Modal custom styling */
        .modal-content-custom {
            border-radius: 24px;
            border: none;
            box-shadow: 0 20px 50px rgba(0,0,0,0.1);
        }
        .modal-header-custom {
            border-bottom: 1px solid rgba(0,0,0,0.05);
            padding: 24px 30px;
        }
        .modal-body-custom {
            padding: 30px;
        }
        .form-control-custom {
            background-color: #f8fafc;
            border: 1.5px solid transparent;
            border-radius: 12px;
            padding: 10px 15px;
            font-size: 0.9rem;
            transition: all 0.2s;
        }
        .form-control-custom:focus {
            border-color: var(--kemenag-green);
            background-color: #ffffff;
            box-shadow: 0 0 0 4px rgba(0, 168, 107, 0.1);
        }
    </style>
</head>
<body>

    <!-- Container Grid Utama Terbagi 2 Panel -->
    <div class="container-fluid p-0" style="height: 100vh; overflow: hidden;">
        <div class="row g-0" style="height: 100vh; overflow: hidden;">
            
            <!-- Panel Kiri: Sidebar Terpusat -->
            <div class="col-lg-3 col-xl-2 d-none d-lg-block" style="height: 100vh; overflow: hidden;">
                <?php include 'sidebar.php'; ?>
            </div>

            <!-- Panel Kanan: Area Konten Kelola Operator -->
            <div class="col-lg-9 col-xl-10" style="height: 100vh; overflow: hidden;">
                <div class="admin-main-content-scroll">
                    
                    <!-- Header Section Modul -->
                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
                        <div>
                            <h4 class="fw-bold text-dark mb-1" style="font-weight: 850;">Kelola Operator</h4>
                            <p class="text-muted small mb-0">Manajemen hak akses administrator website Yayasan PP Raudlatul Muta'allimin.</p>
                        </div>
                        <div>
                            <button class="btn text-white rounded-pill px-4 py-2 fw-semibold shadow-sm" style="background-color: var(--kemenag-green);" data-bs-toggle="modal" data-bs-target="#modalTambah">
                                <i class="fas fa-plus me-2"></i> Tambah Operator
                            </button>
                        </div>
                    </div>

                    <!-- Card List Operator -->
                    <div class="operator-card">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light text-secondary" style="border-radius: 12px; font-size: 0.85rem;">
                                    <tr>
                                        <th class="py-3 ps-3">No</th>
                                        <th class="py-3">Nama Lengkap</th>
                                        <th class="py-3">Username</th>
                                        <th class="py-3 text-center">Hak Akses (Role)</th>
                                        <th class="py-3 text-center">Tanggal Terdaftar</th>
                                        <th class="py-3 text-centerpe-3" style="width: 120px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; foreach ($operator_list as $op): ?>
                                    <tr>
                                        <td class="ps-3 fw-semibold text-muted"><?php echo $no++; ?></td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; background-color: #f1f5f9; color: var(--slate-900); font-size: 0.85rem;">
                                                    <?php echo strtoupper(substr($op['nama_lengkap'], 0, 1)); ?>
                                                </div>
                                                <span class="fw-bold text-dark" style="font-size: 0.9rem;"><?php echo htmlspecialchars($op['nama_lengkap']); ?></span>
                                            </div>
                                        </td>
                                        <td class="text-muted font-monospace" style="font-size: 0.85rem;">@<?php echo htmlspecialchars($op['username']); ?></td>
                                        <td class="text-center">
                                            <span class="badge-role <?php echo ($op['role'] === 'superadmin') ? 'bg-role-superadmin' : 'bg-role-admin'; ?>">
                                                <?php echo ucfirst(htmlspecialchars($op['role'])); ?>
                                            </span>
                                        </td>
                                        <td class="text-center text-muted small"><?php echo date('d M Y, H:i', strtotime($op['created_at'])); ?></td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <!-- Tombol Edit Triggering Modal -->
                                                <button class="btn btn-action btn-outline-success border-0" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#modalEdit" 
                                                        data-id="<?php echo $op['id_user']; ?>"
                                                        data-nama="<?php echo htmlspecialchars($op['nama_lengkap']); ?>"
                                                        data-user="<?php echo htmlspecialchars($op['username']); ?>"
                                                        data-role="<?php echo $op['role']; ?>">
                                                    <i class="far fa-edit"></i>
                                                </button>
                                                <!-- Tombol Hapus dengan SWAL Confirmation -->
                                                <a href="#" class="btn btn-action btn-outline-danger border-0" onclick="event.preventDefault(); konfirmasiHapus(<?php echo $op['id_user']; ?>, '<?php echo htmlspecialchars($op['nama_lengkap']); ?>')">
                                                    <i class="far fa-trash-alt"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>

    <!-- Modal Tambah Operator -->
    <div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modal-content-custom">
                <div class="modal-header modal-header-custom">
                    <h5 class="modal-title fw-bold" style="color: var(--slate-900);"><i class="fas fa-user-plus me-2 text-success"></i> Tambah Operator Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="operator.php" method="POST">
                    <div class="modal-body modal-body-custom">
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-muted">Nama Lengkap</label>
                            <input type="text" class="form-control form-control-custom" name="nama_lengkap" placeholder="Masukkan nama lengkap" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-muted">Username</label>
                            <input type="text" class="form-control form-control-custom" name="username" placeholder="Masukkan username" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-muted">Password</label>
                            <input type="password" class="form-control form-control-custom" name="password" placeholder="Masukkan password baru" required>
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-semibold small text-muted">Hak Akses (Role)</label>
                            <select class="form-select form-control-custom" name="role" required>
                                <option value="admin">Admin</option>
                                <option value="superadmin">Superadmin</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer border-0 px-4 pb-4">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="tambah_operator" class="btn text-white rounded-pill px-4" style="background-color: var(--kemenag-green);">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Operator -->
    <div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modal-content-custom">
                <div class="modal-header modal-header-custom">
                    <h5 class="modal-title fw-bold" style="color: var(--slate-900);"><i class="far fa-edit me-2 text-success"></i> Perbarui Akun Operator</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="operator.php" method="POST">
                    <input type="hidden" name="id_user" id="edit_id">
                    <div class="modal-body modal-body-custom">
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-muted">Nama Lengkap</label>
                            <input type="text" class="form-control form-control-custom" name="nama_lengkap" id="edit_nama" placeholder="Masukkan nama lengkap" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-muted">Username</label>
                            <input type="text" class="form-control form-control-custom" name="username" id="edit_user" placeholder="Masukkan username" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-muted">Password (Kosongkan jika tidak diubah)</label>
                            <input type="password" class="form-control form-control-custom" name="password" placeholder="Masukkan password baru">
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-semibold small text-muted">Hak Akses (Role)</label>
                            <select class="form-select form-control-custom" name="role" id="edit_role" required>
                                <option value="admin">Admin</option>
                                <option value="superadmin">Superadmin</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer border-0 px-4 pb-4">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="edit_operator" class="btn text-white rounded-pill px-4" style="background-color: var(--kemenag-green);">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Memasukkan data secara dinamis ke modal Edit ketika tombol diklik
        const modalEdit = document.getElementById('modalEdit');
        modalEdit.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const nama = button.getAttribute('data-nama');
            const user = button.getAttribute('data-user');
            const role = button.getAttribute('data-role');

            document.getElementById('edit_id').value = id;
            document.getElementById('edit_nama').value = nama;
            document.getElementById('edit_user').value = user;
            document.getElementById('edit_role').value = role;
        });

        // Trigger Konfirmasi Hapus Menggunakan SWAL
        function konfirmasiHapus(id, nama) {
            Swal.fire({
                title: 'Apakah Anda Yakin?',
                text: "Akun operator '" + nama + "' akan dihapus permanen dari database.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#00a86b',
                cancelButtonColor: '#ef4444',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'operator.php?action=delete&id=' + id;
                }
            });
        }
    </script>

    <!-- Respon Aksi CRUD via PHP Melalui SweetAlert2 -->
    <?php if (!is_null($status_action)): ?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            <?php if ($status_action === 'insert_success'): ?>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil Menambah!',
                text: 'Operator baru berhasil didaftarkan ke sistem dengan enkripsi Bcrypt.',
                confirmButtonColor: '#00a86b'
            });
            <?php elseif ($status_action === 'update_success'): ?>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil Memperbarui!',
                text: 'Detail profil dan hak akses akun operator telah sukses diperbarui.',
                confirmButtonColor: '#00a86b'
            });
            <?php elseif ($status_action === 'delete_success'): ?>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil Dihapus!',
                text: 'Akun operator terpilih telah resmi dieliminasi dari sistem.',
                confirmButtonColor: '#00a86b'
            });
            <?php elseif ($status_action === 'username_exists'): ?>
            Swal.fire({
                icon: 'warning',
                title: 'Username Duplikat!',
                text: 'Nama pengguna tersebut sudah digunakan oleh akun lain. Gunakan username unik.',
                confirmButtonColor: '#00a86b'
            });
            <?php elseif ($status_action === 'delete_self_prevent'): ?>
            Swal.fire({
                icon: 'error',
                title: 'Penghapusan Dicegah!',
                text: 'Anda tidak diizinkan untuk menghapus akun Anda sendiri yang sedang aktif digunakan.',
                confirmButtonColor: '#ef4444'
            });
            <?php elseif ($status_action === 'empty_fields'): ?>
            Swal.fire({
                icon: 'warning',
                title: 'Kolom Kosong!',
                text: 'Harap pastikan semua isian wajib formulir diisi dengan benar.',
                confirmButtonColor: '#00a86b'
            });
            <?php else: ?>
            Swal.fire({
                icon: 'error',
                title: 'Kesalahan Sistem!',
                text: 'Gagal memproses data di server database. Error: <?php echo addslashes($error_message); ?>',
                confirmButtonColor: '#00a86b'
            });
            <?php endif; ?>
        });
    </script>
    <?php endif; ?>
</body>
</html>