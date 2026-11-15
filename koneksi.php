<?php
// Konfigurasi Database Hostinger
$host = "srv1761.hstgr.io";
$user = "u613848645_pdkiselgroup"; // Username database Anda
$pass = "WamnqfL8GK5&"; // GANTI DENGAN PASSWORD DATABASE ANDA
$db   = "u613848645_pdkisel"; // Nama database Anda

// Membuat koneksi
$conn = new mysqli($host, $user, $pass, $db);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}
?>
