<?php
// Panggil file koneksi
require 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil data dari form dan amankan (mencegah SQL Injection)
    $nama               = $conn->real_escape_string($_POST['nama']);
    $email              = $conn->real_escape_string($_POST['email']);
    $no_hp              = $conn->real_escape_string($_POST['no_hp']);
    $alamat             = $conn->real_escape_string($_POST['alamat']);
    $jenis_kelamin      = $conn->real_escape_string($_POST['jenis_kelamin']);
    $tanggal_lahir      = $conn->real_escape_string($_POST['tanggal_lahir']);
    
    $status_karyawan    = $conn->real_escape_string($_POST['status_karyawan']);
    $kontrak_mulai      = !empty($_POST['kontrak_mulai']) ? $conn->real_escape_string($_POST['kontrak_mulai']) : NULL;
    $kontrak_selesai    = !empty($_POST['kontrak_selesai']) ? $conn->real_escape_string($_POST['kontrak_selesai']) : NULL;
    
    $anak_usaha         = $conn->real_escape_string($_POST['anak_usaha']);
    $regional           = $conn->real_escape_string($_POST['regional']);
    $lokasi_kantor      = !empty($_POST['lokasi_kantor']) ? $conn->real_escape_string($_POST['lokasi_kantor']) : NULL;
    
    $status_pernikahan  = $conn->real_escape_string($_POST['status_pernikahan']);
    $jumlah_anak        = !empty($_POST['jumlah_anak']) ? (int)$_POST['jumlah_anak'] : 0;
    $gereja             = $conn->real_escape_string($_POST['gereja']);

    // Query untuk memasukkan data ke tabel
    $sql = "INSERT INTO anggota_pdkisel (
                nama, email, no_hp, alamat, jenis_kelamin, tanggal_lahir, 
                status_karyawan, kontrak_mulai, kontrak_selesai, 
                anak_usaha, regional, lokasi_kantor, 
                status_pernikahan, jumlah_anak, gereja
            ) VALUES (
                '$nama', '$email', '$no_hp', '$alamat', '$jenis_kelamin', '$tanggal_lahir', 
                '$status_karyawan', " . ($kontrak_mulai ? "'$kontrak_mulai'" : "NULL") . ", " . ($kontrak_selesai ? "'$kontrak_selesai'" : "NULL") . ", 
                '$anak_usaha', '$regional', " . ($lokasi_kantor ? "'$lokasi_kantor'" : "NULL") . ", 
                '$status_pernikahan', $jumlah_anak, '$gereja'
            )";

    if ($conn->query($sql) === TRUE) {
        
        // --- FITUR EMAIL NOTIFIKASI ---
        $to = $email;
        $subject = "Pendaftaran Anggota PDKISEL Berhasil";
        
        $message = "Halo " . $nama . ",\n\n";
        $message .= "Terima kasih telah mendaftar sebagai anggota Persekutuan Doa Kisel (PDKISEL).\n";
        $message .= "Data Anda telah berhasil kami terima dan simpan.\n\n";
        $message .= "Mari bertumbuh bersama dalam persekutuan yang saling menguatkan.\n\n";
        $message .= "Tuhan memberkati,\nPengurus PDKISEL";

        $headers = "From: no-reply@pdkisel.id" . "\r\n" .
                   "Reply-To: halo@pdkisel.id" . "\r\n" .
                   "X-Mailer: PHP/" . phpversion();

        // Kirim email (Fungsi mail bawaan Hostinger)
        mail($to, $subject, $message, $headers);

        // --- SELESAI EMAIL ---

        // Tampilkan pesan sukses dan redirect
        echo "<script>
                alert('Pendaftaran Berhasil! Silakan cek email Anda untuk konfirmasi.');
                window.location.href = 'index.html';
              </script>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>
