<?php
// Panggil file koneksi database
require 'koneksi.php';

// Panggil fungsi pengirim email
require 'kirim_email.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil data dari form
    $nama               = $_POST['nama'];
    $email              = $_POST['email'];
    $no_hp              = $_POST['no_hp'];
    $alamat             = $_POST['alamat'];
    $jenis_kelamin      = $_POST['jenis_kelamin'];
    $tanggal_lahir      = $_POST['tanggal_lahir'];
    
    $status_karyawan    = $_POST['status_karyawan'];
    $kontrak_mulai      = !empty($_POST['kontrak_mulai']) ? $_POST['kontrak_mulai'] : NULL;
    $kontrak_selesai    = !empty($_POST['kontrak_selesai']) ? $_POST['kontrak_selesai'] : NULL;
    
    $anak_usaha         = $_POST['anak_usaha'];
    $regional           = $_POST['regional'];
    $lokasi_kantor      = !empty($_POST['lokasi_kantor']) ? $_POST['lokasi_kantor'] : NULL;
    
    $status_pernikahan  = $_POST['status_pernikahan'];
    $jumlah_anak        = !empty($_POST['jumlah_anak']) ? (int)$_POST['jumlah_anak'] : 0;
    $gereja             = $_POST['gereja'];

    // ==========================================
    // PROSES UPLOAD FOTO
    // ==========================================
    $nama_file_foto = NULL;

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $file_tmp  = $_FILES['foto']['tmp_name'];
        $file_name = $_FILES['foto']['name'];
        $file_size = $_FILES['foto']['size'];
        $file_ext  = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        $allowed_ext = ['jpg', 'jpeg', 'png'];
        if (!in_array($file_ext, $allowed_ext)) {
            die("Error: Format foto harus JPG, JPEG, atau PNG.");
        }

        if ($file_size > 1048576) {
            die("Error: Ukuran foto terlalu besar. Maksimal 1MB.");
        }

        $nama_file_foto = time() . "_" . preg_replace("/[^a-zA-Z0-9.]/", "_", $file_name);
        $tujuan_upload = 'uploads/' . $nama_file_foto;

        if (!move_uploaded_file($file_tmp, $tujuan_upload)) {
            die("Error: Gagal mengunggah foto ke server.");
        }
    } else {
        die("Error: Foto profil wajib diunggah.");
    }

    // ==========================================
    // QUERY MENGGUNAKAN PREPARED STATEMENTS (AMAN DARI SQL INJECTION)
    // ==========================================
    $sql = "INSERT INTO anggota_pdkisel (
                nama, email, no_hp, alamat, jenis_kelamin, tanggal_lahir, 
                status_karyawan, kontrak_mulai, kontrak_selesai, 
                anak_usaha, regional, lokasi_kantor, 
                status_pernikahan, jumlah_anak, gereja, foto
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    
    // Bind parameter (s = string, i = integer)
    $stmt->bind_param("sssssssssssssiss", 
        $nama, $email, $no_hp, $alamat, $jenis_kelamin, $tanggal_lahir,
        $status_karyawan, $kontrak_mulai, $kontrak_selesai,
        $anak_usaha, $regional, $lokasi_kantor,
        $status_pernikahan, $jumlah_anak, $gereja, $nama_file_foto
    );

    if ($stmt->execute()) {
        
        // Panggil fungsi kirim email
        kirimNotifikasi($email, $nama);

        echo "<script>
                alert('Pendaftaran Berhasil! Silakan cek email Anda untuk konfirmasi.');
                window.location.href = '/'; 
              </script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
