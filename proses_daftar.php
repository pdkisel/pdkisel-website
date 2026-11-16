<?php
// Panggil file koneksi database
require 'koneksi.php';

// Panggil fungsi pengirim email
require 'kirim_email.php';

// Fungsi untuk menampilkan halaman error yang elegan
function tampilkanErrorElegan($pesan) {
    echo '
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Pendaftaran Gagal - PDKISEL</title>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <style>
            body {
                font-family: "Inter", sans-serif;
                background-color: #F4F6F9;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                margin: 0;
                background-image: linear-gradient(rgba(10, 31, 68, 0.85), rgba(10, 31, 68, 0.85)), url("https://images.unsplash.com/photo-1511632765486-a01980e01a18?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80");
                background-size: cover;
                background-position: center;
            }
            .error-card {
                background: #FFFFFF;
                padding: 50px 40px;
                border-radius: 16px;
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
                text-align: center;
                max-width: 500px;
                width: 90%;
                border-top: 6px solid #D32F2F;
            }
            .error-icon {
                font-size: 4rem;
                color: #D32F2F;
                margin-bottom: 20px;
            }
            .error-card h1 {
                font-family: "Poppins", sans-serif;
                color: #0A1F44;
                font-size: 1.8rem;
                margin-bottom: 15px;
            }
            .error-card p {
                color: #718096;
                font-size: 1rem;
                line-height: 1.6;
                margin-bottom: 30px;
            }
            .btn-back {
                background-color: #0A1F44;
                color: #FFFFFF;
                text-decoration: none;
                padding: 12px 25px;
                border-radius: 8px;
                font-weight: 600;
                font-family: "Poppins", sans-serif;
                transition: 0.3s;
                display: inline-block;
            }
            .btn-back:hover {
                background-color: #D32F2F;
                transform: translateY(-2px);
            }
        </style>
    </head>
    <body>
        <div class="error-card">
            <i class="fas fa-exclamation-triangle error-icon"></i>
            <h1>Pendaftaran Gagal</h1>
            <p>' . $pesan . '</p>
            <a href="daftar.html" class="btn-back"><i class="fas fa-arrow-left"></i> Kembali ke Form</a>
        </div>
    </body>
    </html>
    ';
    exit;
}

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
    // VALIDASI ANTI-DUPLIKAT (EMAIL & NO HP)
    // ==========================================
    $cek_duplikat = $conn->prepare("SELECT id FROM anggota_pdkisel WHERE email = ? OR no_hp = ?");
    $cek_duplikat->bind_param("ss", $email, $no_hp);
    $cek_duplikat->execute();
    $hasil_cek = $cek_duplikat->get_result();

    if ($hasil_cek->num_rows > 0) {
        // Tampilkan pesan error elegan
        tampilkanErrorElegan("Email atau Nomor HP yang Anda masukkan sudah terdaftar sebagai anggota PDKISEL. Jika ini kesalahan, silakan hubungi pengurus.");
    }
    $cek_duplikat->close();

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
            tampilkanErrorElegan("Ukuran foto terlalu besar. Maksimal 1MB.");
        }

        // 2. Validasi MIME Type (Tipe File Asli)
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $file_tmp);
        finfo_close($finfo);

        $allowed_mime = ['image/jpeg', 'image/png', 'image/jpg'];
        if (!in_array($mime_type, $allowed_mime)) {
            tampilkanErrorElegan("File yang diunggah bukan gambar yang valid (JPG/PNG).");
        }

        // 3. Validasi Tambahan menggunakan getimagesize
        $cek_gambar = getimagesize($file_tmp);
        if ($cek_gambar === false) {
            tampilkanErrorElegan("File yang diunggah rusak atau bukan gambar.");
        }

        // 4. Tentukan Ekstensi Baru Berdasarkan MIME Type
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
            tampilkanErrorElegan("Gagal mengunggah foto ke server. Silakan coba lagi.");
        }
    } else {
        tampilkanErrorElegan("Foto profil wajib diunggah.");
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

    // ==========================================
    // ERROR HANDLING (TANGKAP ERROR DUPLIKAT DARI DATABASE)
    // ==========================================
    try {
        if ($stmt->execute()) {
            
            // PENGAMANAN XSS UNTUK EMAIL
            $nama_aman = htmlspecialchars($nama, ENT_QUOTES, 'UTF-8');
            
            // Panggil fungsi kirim email dengan nama yang sudah aman
            kirimNotifikasi($email, $nama_aman);

            echo "<script>
                    alert('Pendaftaran Berhasil! Silakan cek email Anda untuk konfirmasi.');
                    window.location.href = '/'; 
                  </script>";
        }
    } catch (mysqli_sql_exception $e) {
        // Jika database menolak karena duplikat (kode error 1062)
        if ($e->getCode() == 1062) {
            tampilkanErrorElegan("Data dengan Email atau Nomor HP tersebut sudah terdaftar. Silakan gunakan data lain.");
        } else {
            tampilkanErrorElegan("Terjadi kesalahan sistem. Silakan coba beberapa saat lagi.");
        }
    }

    $stmt->close();
    $conn->close();
}
?>
