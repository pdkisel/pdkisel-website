<?php
session_start();

// Proteksi Halaman
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

require 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $id                 = (int)$_POST['id'];
    $nama               = $_POST['nama'];
    $email              = $_POST['email'];
    $no_hp              = $_POST['no_hp'];
    $jenis_kelamin      = $_POST['jenis_kelamin'];
    $alamat             = $_POST['alamat'];
    
    $status_karyawan    = $_POST['status_karyawan'];
    $anak_usaha         = $_POST['anak_usaha'];
    $regional           = $_POST['regional'];
    $lokasi_kantor      = !empty($_POST['lokasi_kantor']) ? $_POST['lokasi_kantor'] : NULL;
    
    $status_pernikahan  = $_POST['status_pernikahan'];
    $jumlah_anak        = !empty($_POST['jumlah_anak']) ? (int)$_POST['jumlah_anak'] : 0;
    $kristen            = $_POST['kristen'];
    $gereja             = $_POST['gereja'];

    // Query Update Data
    $sql = "UPDATE anggota_pdkisel SET 
                nama = ?, email = ?, no_hp = ?, jenis_kelamin = ?, alamat = ?, 
                status_karyawan = ?, anak_usaha = ?, regional = ?, lokasi_kantor = ?, 
                status_pernikahan = ?, jumlah_anak = ?, kristen = ?, gereja = ?
            WHERE id = ?";

    $stmt = $conn->prepare($sql);
    
    // Binding parameter (s=string, i=integer)
    $stmt->bind_param("ssssssssssissi", 
        $nama, $email, $no_hp, $jenis_kelamin, $alamat, 
        $status_karyawan, $anak_usaha, $regional, $lokasi_kantor, 
        $status_pernikahan, $jumlah_anak, $kristen, $gereja, $id
    );

    try {
        if ($stmt->execute()) {
            echo "<script>
                    alert('Data anggota berhasil diperbarui!');
                    window.location.href = 'dashboard.php'; 
                  </script>";
        }
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1062) {
            echo "<script>
                    alert('Gagal Update: Email atau Nomor HP sudah digunakan oleh anggota lain.');
                    window.history.back();
                  </script>";
        } else {
            echo "Error: " . $stmt->error;
        }
    }

    $stmt->close();
    $conn->close();
}
?>
