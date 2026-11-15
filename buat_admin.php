<?php
require 'koneksi.php';

// ==========================================
// SILAKAN UBAH USERNAME DAN PASSWORD DI SINI
// ==========================================
$username = "admin";
$password = "Pdkisel2026!"; // Buat password yang kuat

// Membuat hash password yang aman
$hashed_password = password_hash($password, PASSWORD_BCRYPT);

// Hapus admin lama jika ada (agar tidak duplikat)
$conn->query("TRUNCATE TABLE admin_pdkisel");

// Simpan ke database
$sql = "INSERT INTO admin_pdkisel (username, password) VALUES ('$username', '$hashed_password')";

if ($conn->query($sql) === TRUE) {
    echo "<h1>Akun Admin Berhasil Dibuat!</h1>";
    echo "<p>Username: <b>" . $username . "</b></p>";
    echo "<p>Password: <b>" . $password . "</b></p>";
    echo "<p style='color:red;'><b>PENTING: Segera hapus file buat_admin.php ini dari Hostinger setelah Anda berhasil login!</b></p>";
    echo "<a href='login.php'>Klik di sini untuk Login</a>";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
