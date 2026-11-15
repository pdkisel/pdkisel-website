<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

function kirimNotifikasi($email_tujuan, $nama) {
    $mail = new PHPMailer(true);

    try {
        // Konfigurasi Server SMTP Hostinger
        $mail->isSMTP();
        $mail->Host       = 'smtp.hostinger.com'; 
        $mail->SMTPAuth   = true;
        
        $mail->Username   = 'halo@pdkisel.id'; 
        $mail->Password   = '@PdShi3ld2026'; // GANTI DENGAN PASSWORD ANDA
        
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        // Pengirim
        $mail->setFrom('no-reply@pdkisel.id', 'PDKISEL');
        
        // Email tujuan
        $mail->addAddress($email_tujuan, $nama);

        // Konten Email
        $mail->isHTML(true);
        $mail->Subject = 'Pendaftaran Anggota PDKISEL Berhasil';
        
        // DESAIN EMAIL ELEGAN (Menggunakan Tabel agar aman di semua email client)
        $mail->Body = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                @import url("https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=Poppins:wght@600&display=swap");
            </style>
        </head>
        <body style="margin: 0; padding: 0; background-color: #f4f6f9; font-family: Arial, sans-serif;">
            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f4f6f9; padding: 40px 0;">
                <tr>
                    <td align="center">
                        <!-- Kotak Utama Email -->
                        <table border="0" cellpadding="0" cellspacing="0" width="600" style="background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 30px rgba(10, 31, 68, 0.1);">
                            
                            <!-- Header (Navy Gradient) -->
                            <tr>
                                <td align="center" style="background: linear-gradient(135deg, #0A1F44 0%, #1B3A6B 100%); padding: 40px 20px;">
                                    <img src="https://pdkisel.id/assets/images/logo-pdkisel.png" alt="Logo PDKISEL" style="height: 80px; margin-bottom: 15px;">
                                    <h1 style="color: #ffffff; font-family: Poppins, Arial, sans-serif; font-size: 24px; margin: 0;">Pendaftaran Berhasil</h1>
                                </td>
                            </tr>

                            <!-- Body Email -->
                            <tr>
                                <td style="padding: 40px 40px 30px 40px;">
                                    <h2 style="color: #0A1F44; font-family: Poppins, Arial, sans-serif; font-size: 20px; margin-top: 0;">Halo, ' . htmlspecialchars($nama) . '!</h2>
                                    
                                    <p style="color: #4a5568; font-size: 15px; line-height: 1.8; margin-bottom: 20px;">
                                        Terima kasih telah mendaftar sebagai anggota <b>Persekutuan Doa Kisel (PDKISEL)</b>.
                                    </p>
                                    
                                    <p style="color: #4a5568; font-size: 15px; line-height: 1.8; margin-bottom: 30px;">
                                        Data Anda telah berhasil kami terima dan simpan di sistem kami. Mari bertumbuh bersama dalam persekutuan yang saling menguatkan.
                                    </p>
                                    
                                    <!-- Tombol CTA -->
                                    <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                        <tr>
                                            <td align="center" style="padding: 20px 0;">
                                                <a href="https://pdkisel.id" style="background-color: #D32F2F; color: #ffffff; text-decoration: none; padding: 14px 30px; border-radius: 8px; font-weight: bold; font-size: 15px; display: inline-block;">Kunjungi Website PDKISEL</a>
                                            </td>
                                        </tr>
                                    </table>

                                    <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 30px 0;">
                                    
                                    <p style="color: #4a5568; font-size: 15px; line-height: 1.8; margin-bottom: 0;">
                                        Tuhan memberkati,<br>
                                        <b style="color: #0A1F44;">Pengurus PDKISEL</b>
                                    </p>
                                </td>
                            </tr>

                            <!-- Footer -->
                            <tr>
                                <td align="center" style="background-color: #0A1F44; padding: 20px;">
                                    <p style="color: #a0aec0; font-size: 12px; margin: 0;">
                                        &copy; ' . date("Y") . ' Persekutuan Doa Kisel. All rights reserved.<br>
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
?>
