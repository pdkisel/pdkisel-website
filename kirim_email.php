<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

function kirimNotifikasi($email_tujuan, $nama) {
    $mail = new PHPMailer(true);

    try {
        // Konfigurasi Server SMTP Hostinger
        $mail->isSMTP();
        $mail->Host       = 'smtp.hostinger.com'; 
        $mail->SMTPAuth   = true;
        
        // PENTING: Gunakan EMAIL UTAMA (bukan alias) untuk login
        $mail->Username   = 'halo@pdkisel.id'; 
        
        // GANTI dengan password email halo@pdkisel.id Anda
        $mail->Password   = 'PASSWORD_EMAIL_HALO_ANDA'; 
        
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        // Pengirim yang akan dilihat oleh penerima email
        $mail->setFrom('no-reply@pdkisel.id', 'PDKISEL');
        
        // Email tujuan
        $mail->addAddress($email_tujuan, $nama);

        // Konten Email
        $mail->isHTML(true);
        $mail->Subject = 'Pendaftaran Anggota PDKISEL Berhasil';
        
        $mail->Body    = "
            <div style='font-family: Arial, sans-serif; color: #333;'>
                <h2 style='color: #0A1F44;'>Halo, {$nama}!</h2>
                <p>Terima kasih telah mendaftar sebagai anggota Persekutuan Doa Kisel (PDKISEL).</p>
                <p>Data Anda telah berhasil kami terima dan simpan di sistem kami.</p>
                <br>
                <p>Mari bertumbuh bersama dalam persekutuan yang saling menguatkan.</p>
                <br>
                <p>Tuhan memberkati,</p>
                <p><b>Pengurus PDKISEL</b></p>
            </div>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
?>
