<?php
session_start();

// Proteksi: Hanya admin yang login yang bisa mengunduh
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

require 'koneksi.php';

// Nama file yang akan diunduh
$filename = "Data_Anggota_PDKISEL_" . date('Y-m-d') . ".csv";

// Header untuk memaksa browser mengunduh file
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// Membuka output stream (php://output mengirim langsung ke browser)
$output = fopen('php://output', 'w');

// Menambahkan BOM (Byte Order Mark) agar Excel bisa membaca karakter UTF-8 dengan benar
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Header Kolom CSV
$header = array(
    'ID', 'Nama Lengkap', 'Email', 'No HP / WA', 'Jenis Kelamin', 'Tanggal Lahir', 
    'Alamat Domisili', 'Status Karyawan', 'Anak Usaha', 'Regional', 'Lokasi Kantor', 
    'Masa Kontrak Mulai', 'Masa Kontrak Selesai', 'Status Pernikahan', 'Jumlah Anak', 
    'Gereja', 'Waktu Daftar'
);
fputcsv($output, $header);

// Ambil data dari database
$sql = "SELECT * FROM anggota_pdkisel ORDER BY waktu_daftar DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // Susun baris data sesuai urutan header
        $baris = array(
            $row['id'],
            $row['nama'],
            $row['email'],
            $row['no_hp'],
            $row['jenis_kelamin'],
            date('d-m-Y', strtotime($row['tanggal_lahir'])),
            $row['alamat'],
            $row['status_karyawan'],
            $row['anak_usaha'],
            $row['regional'],
            $row['lokasi_kantor'] ?? '-',
            $row['kontrak_mulai'] ?? '-',
            $row['kontrak_selesai'] ?? '-',
            $row['status_pernikahan'],
            $row['jumlah_anak'],
            $row['gereja'],
            date('d-m-Y H:i', strtotime($row['waktu_daftar']))
        );
        
        fputcsv($output, $baris);
    }
}

fclose($output);
exit;
?>
