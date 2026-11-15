<?php
// Panggil file koneksi database
require 'koneksi.php';

// Panggil fungsi pengirim email (menggunakan PHPMailer yang sudah ada)
require 'kirim_email.php';

// Fungsi khusus untuk mengirim ucapan ulang tahun
function kirimUcapanUltah($email_tujuan, $nama) {
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

        $mail->isHTML(true);
        $mail->Subject = 'Selamat Ulang Tahun, ' . $nama . '! 🎉';
        
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
                            
                            <!-- Header Ulang Tahun -->
                            <tr>
                                <td align="center" style="background: linear-gradient(135deg, #0A1F44 0%, #1B3A6B 100%); padding: 40px 20px;">
                                    <img src="https://pdkisel.id/assets/images/logo-pdkisel.png" alt="Logo PDKISEL" style="height: 80px; margin-bottom: 15px;">
                                    <h1 style="color: #D4AF37; font-family: Georgia, serif; font-size: 28px; margin: 0; font-style: italic;">Happy Birthday!</h1>
                                </td>
                            </tr>

                            <!-- Body Email -->
                            <tr>
                                <td style="padding: 40px 40px 30px 40px;">
                                    <h2 style="color: #0A1F44; font-family: Helvetica, Arial, sans-serif; font-size: 22px; margin-top: 0;">Halo, ' . htmlspecialchars($nama) . '!</h2>
                                    
                                    <p style="color: #4a5568; font-size: 15px; line-height: 1.8; margin-bottom: 20px;">
                                        Selamat ulang tahun! 🎂
                                    </p>
                                    
                                    <p style="color: #4a5568; font-size: 15px; line-height: 1.8; margin-bottom: 30px;">
                                        Keluarga besar Persekutuan Doa Kisel (PDKISEL) mengucapkan selamat bertambah usia. Semoga Tuhan senantiasa melimpahkan berkat, kesehatan, dan hikmat dalam setiap langkah pelayanan dan pekerjaan Anda.
                                    </p>
                                    
                                    <blockquote style="border-left: 4px solid #D32F2F; padding-left: 15px; margin: 20px 0; color: #718096; font-style: italic;">
                                        "TUHAN memberkati engkau dan melindungi engkau; TUHAN menyinari engkau dengan wajah-Nya dan memberi engkau kasih karunia;" <br><b>- Bilangan 6:24-25 -</b>
                                    </blockquote>

                                    <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 30px 0;">
                                    
                                    <p style="color: #4a5568; font-size: 15px; line-height: 1.8; margin-bottom: 0;">
                                        Dengan kasih,<br>
                                        <b style="color: #0A1F44;">Pengurus PDKISEL</b>
                                    </p>
                                </td>
                            </tr>

                            <!-- Footer -->
                            <tr>
                                <td align="center" style="background-color: #0A1F44; padding: 20px;">
                                    <p style="color: #a0aec0; font-size: 12px; margin: 0;">
                                        &copy; ' . date("Y") . ' Persekutuan Doa Kisel.
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

// Mendapatkan tanggal dan bulan hari ini (format: MM-DD)
$hari_ini = date('m-d');

// Cari anggota yang tanggal lahirnya sama dengan hari ini
$sql = "SELECT nama, email FROM anggota_pdkisel WHERE DATE_FORMAT(tanggal_lahir, '%m-%d') = '$hari_ini'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<h2>Mengirim Ucapan Ulang Tahun...</h2>";
    while($row = $result->fetch_assoc()) {
        $nama = $row['nama'];
        $email = $row['email'];
        
        echo "Mengirim ke: " . $nama . " (" . $email . ") ... ";
        
        if (kirimUcapanUltah($email, $nama)) {
            echo "<b style='color:green;'>BERHASIL</b><br>";
        } else {
            echo "<b style='color:red;'>GAGAL</b><br>";
        }
    }
} else {
    echo "Tidak ada anggota yang berulang tahun hari ini (" . date('d F') . ").";
}

$conn->close();
?>
