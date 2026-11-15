<?php
// Panggil file koneksi database
require 'koneksi.php';

// Panggil fungsi pengirim email
require 'kirim_email.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil data dari form dan amankan
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

    // ==========================================
    // PROSES UPLOAD FOTO
    // ==========================================
    $nama_file_foto = NULL; // Default kosong

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $file_tmp  = $_FILES['foto']['tmp_name'];
        $file_name = $_FILES['foto']['name'];
        $file_size = $_FILES['foto']['size'];
        $file_ext  = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        // Validasi Ekstensi
        $allowed_ext = ['jpg', 'jpeg', 'png'];
        if (!in_array($file_ext, $allowed_ext)) {
            die("Error: Format foto harus JPG, JPEG, atau PNG.");
        }

        // Validasi Ukuran (Maksimal 1MB = 1048576 bytes)
        if ($file_size > 1048576) {
            die("Error: Ukuran foto terlalu besar. Maksimal 1MB.");
        }

        // Buat nama file unik agar tidak bentrok (contoh: 1699999999_foto.jpg)
        $nama_file_foto = time() . "_" . preg_replace("/[^a-zA-Z0-9.]/", "_", $file_name);
        $tujuan_upload = 'uploads/' . $nama_file_foto;

        // Pindahkan file dari temporary ke folder uploads
        if (!move_uploaded_file($file_tmp, $tujuan_upload)) {
            die("Error: Gagal mengunggah foto ke server.");
        }
    } else {
        die("Error: Foto profil wajib diunggah.");
    }

    // Query untuk memasukkan data ke tabel (Termasuk kolom foto)
    $sql = "INSERT INTO anggota_pdkisel (
                nama, email, no_hp, alamat, jenis_kelamin, tanggal_lahir, 
                status_karyawan, kontrak_mulai, kontrak_selesai, 
                anak_usaha, regional, lokasi_kantor, 
                status_pernikahan, jumlah_anak, gereja, foto
            ) VALUES (
                '$nama', '$email', '$no_hp', '$alamat', '$jenis_kelamin', '$tanggal_lahir', 
                '$status_karyawan', " . ($kontrak_mulai ? "'$kontrak_mulai'" : "NULL") . ", " . ($kontrak_selesai ? "'$kontrak_selesai'" : "NULL") . ", 
                '$anak_usaha', '$regional', " . ($lokasi_kantor ? "'$lokasi_kantor'" : "NULL") . ", 
                '$status_pernikahan', $jumlah_anak, '$gereja', '$nama_file_foto'
            )";

    if ($conn->query($sql) === TRUE) {
        
        // Panggil fungsi kirim email
        kirimNotifikasi($email, $nama);

        // Tampilkan pesan sukses dan redirect ke beranda utama
        echo "<script>
                alert('Pendaftaran Berhasil! Silakan cek email Anda untuk konfirmasi.');
                window.location.href = '/'; 
              </script>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>
