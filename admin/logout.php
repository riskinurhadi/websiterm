<?php
/**
 * File: admin/logout.php
 * Deskripsi: Skrip untuk mengakhiri sesi administrator dan mengembalikannya ke halaman login.
 */

session_start();

// Menghapus semua variabel sesi yang ada
session_unset();

// Menghancurkan sesi secara total dari server
session_destroy();

// Mengarahkan kembali ke halaman login
header("Location: login.php");
exit;
?>