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
    // PROSES UPLOAD FOTO (VALIDASI KETAT)
    // ==========================================
    $nama_file_foto = NULL;

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $file_tmp  = $_FILES['foto']['tmp_name'];
        $file_name = $_FILES['foto']['name'];
        $file_size = $_FILES['foto']['size'];
        
        // 1. Validasi Ukuran (Maksimal 1MB)
        if ($file_size > 1048576) {
            die("Error: Ukuran foto terlalu besar. Maksimal 1MB.");
        }

        // 2. Validasi MIME Type (Tipe File Asli)
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $file_tmp);
        finfo_close($finfo);

        $allowed_mime = ['image/jpeg', 'image/png', 'image/jpg'];
        if (!in_array($mime_type, $allowed_mime)) {
            die("Error: File yang diunggah bukan gambar yang valid.");
        }

        // 3. Validasi Tambahan menggunakan getimagesize (Memastikan benar-benar gambar)
        $cek_gambar = getimagesize($file_tmp);
        if ($cek_gambar === false) {
            die("Error: File yang diunggah rusak atau bukan gambar.");
        }

        // 4. Tentukan Ekstensi Baru Berdasarkan MIME Type (Mencegah ekstensi palsu)
        $ext_map = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/jpg'  => 'jpg'
        ];
        $file_ext = $ext_map[$mime_type];

        // 5. Buat Nama File Unik
        $nama_file_foto = time() . "_" . bin2hex(random_bytes(4)) . "." . $file_ext;
        $tujuan_upload = 'uploads/' . $nama_file_foto;

        if (!move_uploaded_file($file_tmp, $tujuan_upload)) {
            die("Error: Gagal mengunggah foto ke server.");
        }
    } else {
        die("Error: Foto profil wajib diunggah.");
    }

    // ==========================================
    // QUERY MENGGUNAKAN PREPARED STATEMENTS
    // ==========================================
    $sql = "INSERT INTO anggota_pdkisel (
                nama, email, no_hp, alamat, jenis_kelamin, tanggal_lahir, 
                status_karyawan, kontrak_mulai, kontrak_selesai, 
                anak_usaha, regional, lokasi_kantor, 
                status_pernikahan, jumlah_anak, gereja, foto
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    
    $stmt->bind_param("sssssssssssssiss", 
        $nama, $email, $no_hp, $alamat, $jenis_kelamin, $tanggal_lahir,
        $status_karyawan, $kontrak_mulai, $kontrak_selesai,
        $anak_usaha, $regional, $lokasi_kantor,
        $status_pernikahan, $jumlah_anak, $gereja, $nama_file_foto
    );

    if ($stmt->execute()) {
        
        // PENGAMANAN XSS UNTUK EMAIL
        $nama_aman = htmlspecialchars($nama, ENT_QUOTES, 'UTF-8');
        
        // Panggil fungsi kirim email dengan nama yang sudah aman
        kirimNotifikasi($email, $nama_aman);

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
