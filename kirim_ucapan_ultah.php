<?php
// Mengunci zona waktu ke Waktu Indonesia Barat (WIB)
date_default_timezone_set('Asia/Jakarta');

// Panggil file koneksi database
require 'koneksi.php';

// Panggil library PHPMailer secara langsung
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

// Fungsi khusus untuk mengirim ucapan ulang tahun
function kirimUcapanUltah($email_tujuan, $nama, $foto) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.hostinger.com'; 
        $mail->SMTPAuth   = true;
        $mail->Username   = 'halo@pdkisel.id'; 
        $mail->Password   = '@PdShi3ld2026'; // GANTI DENGAN PASSWORD ANDA
        
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        $mail->setFrom('no-reply@pdkisel.id', 'PDKISEL');
        $mail->addAddress($email_tujuan, $nama);

        $mail->CharSet = 'UTF-8'; 
        $mail->isHTML(true);
        $mail->Subject = 'Selamat Ulang Tahun, ' . $nama . '! 🎉🎂';
        
        // ==========================================
        // LOGIKA BASE64 UNTUK FOTO
        // ==========================================
        $foto_base64 = '';
        if (!empty($foto)) {
            $path_foto = 'uploads/' . $foto; // Path lokal di server Hostinger
            if (file_exists($path_foto)) {
                $file_data = file_get_contents($path_foto);
                $file_type = pathinfo($path_foto, PATHINFO_EXTENSION);
                // Format: data:image/jpeg;base64,....
                $foto_base64 = 'data:image/' . $file_type . ';base64,' . base64_encode($file_data);
            }
        }

        // Jika foto kosong atau gagal dibaca, gunakan logo PDKISEL sebagai gantinya
        if (empty($foto_base64)) {
            $logo_path = 'assets/images/logo-pdkisel.png';
            if (file_exists($logo_path)) {
                $file_data = file_get_contents($logo_path);
                $foto_base64 = 'data:image/png;base64,' . base64_encode($file_data);
            }
        }

        $mail->Body = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
        </head>
        <body style="margin: 0; padding: 0; background-color: #f4f6f9; font-family: Arial, sans-serif;">
            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f4f6f9; padding: 40px 0;">
                <tr>
                    <td align="center">
                        <table border="0" cellpadding="0" cellspacing="0" width="600" style="background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 30px rgba(10, 31, 68, 0.1);">
                            
                            <!-- Header Ulang Tahun (Warna Merah Meriah dengan Foto Base64) -->
                            <tr>
                                <td align="center" style="background: linear-gradient(135deg, #D32F2F 0%, #B71C1C 100%); padding: 40px 20px;">
                                    <img src="' . $foto_base64 . '" alt="Foto ' . htmlspecialchars($nama) . '" style="width: 120px; height: 120px; object-fit: cover; border-radius: 50%; border: 4px solid #FFFFFF; box-shadow: 0 4px 10px rgba(0,0,0,0.3); margin-bottom: 20px;">
                                    <h1 style="color: #FFFFFF; font-family: Georgia, serif; font-size: 32px; margin: 0; text-shadow: 2px 2px 4px rgba(0,0,0,0.3);">Happy Birthday! 🎂</h1>
                                </td>
                            </tr>

                            <!-- Body Email -->
                            <tr>
                                <td style="padding: 40px 40px 30px 40px;">
                                    <h2 style="color: #D32F2F; font-family: Helvetica, Arial, sans-serif; font-size: 24px; margin-top: 0;">Halo, ' . htmlspecialchars($nama) . '!</h2>
                                    
                                    <p style="color: #4a5568; font-size: 16px; line-height: 1.8; margin-bottom: 20px;">
                                        <b>Selamat ulang tahun!</b> 🎉🎈
                                    </p>
                                    
                                    <p style="color: #4a5568; font-size: 15px; line-height: 1.8; margin-bottom: 30px;">
                                        Keluarga besar Persekutuan Doa Kisel (PDKISEL) mengucapkan selamat bertambah usia. Semoga hari ini dipenuhi dengan sukacita, dan tahun yang baru ini membawa berkat, kesehatan, serta hikmat yang melimpah dari Tuhan.
                                    </p>
                                    
                                    <!-- Kotak Kutipan Ayat -->
                                    <div style="background-color: #FFF5F5; border-left: 4px solid #D32F2F; padding: 20px; margin: 30px 0; border-radius: 0 8px 8px 0;">
                                        <p style="color: #2D3748; font-size: 15px; line-height: 1.6; font-style: italic; margin: 0;">
                                            "TUHAN memberkati engkau dan melindungi engkau; TUHAN menyinari engkau dengan wajah-Nya dan memberi engkau kasih karunia; TUHAN menghadapkan wajah-Nya kepadamu dan memberi engkau damai sejahtera."
                                        </p>
                                        <p style="color: #718096; font-size: 14px; margin: 10px 0 0 0; font-weight: bold;">
                                            - Bilangan 6:24-26 -
                                        </p>
                                    </div>
                                    
                                    <p style="color: #4a5568; font-size: 15px; line-height: 1.8; margin-bottom: 0;">
                                        Doa kami menyertai langkah Anda selalu.<br>
                                        <b style="color: #0A1F44;">Pengurus PDKISEL</b>
                                    </p>
                                </td>
                            </tr>

                            <!-- Footer Navy Elegan -->
                            <tr>
                                <td align="center" style="background-color: #0A1F44; padding: 20px;">
                                    <p style="color: #a0aec0; font-size: 12px; margin: 0;">
                                        &copy; ' . date("Y") . ' Persekutuan Doa Kisel.<br>
                                        Bersatu dalam Doa, Bertumbuh dalam Kristus.
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </body>
        </html>
        ';

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

// ==========================================
// LOGIKA UTAMA PENGECEKAN ULANG TAHUN
// ==========================================
$hari_ini = date('m-d');

$sql = "SELECT nama, email, foto FROM anggota_pdkisel WHERE DATE_FORMAT(tanggal_lahir, '%m-%d') = '$hari_ini'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<h2>Mengirim Ucapan Ulang Tahun...</h2>";
    while($row = $result->fetch_assoc()) {
        $nama = $row['nama'];
        $email = $row['email'];
        $foto = $row['foto']; 
        
        echo "Mengirim ke: " . $nama . " (" . $email . ") ... ";
        
        if (kirimUcapanUltah($email, $nama, $foto)) {
            echo "<b style='color:green;'>BERHASIL</b><br>";
        } else {
            echo "<b style='color:red;'>GAGAL</b><br>";
        }
    }
} else {
    echo "Tidak ada anggota yang berulang tahun hari ini (" . date('d F Y') . ").";
}

$conn->close();
?>
