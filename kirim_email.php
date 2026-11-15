<?php
// PENTING: Anda harus mengunduh PHPMailer dan mengunggahnya ke Hostinger
// Kita asumsikan PHPMailer diletakkan di folder 'PHPMailer/src/'
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

function kirimNotifikasi($email_tujuan, $nama) {
    $mail = new PHPMailer(true);

    try {
        // Konfigurasi Server SMTP Hostinger
        // GANTI 'email_anda@pdkisel.id' dan 'password_email_anda' dengan akun email Hostinger Anda
        $mail->isSMTP();
        $mail->Host       = 'smtp.hostinger.com'; 
        $mail->SMTPAuth   = true;
        $mail->Username   = 'halo@pdkisel.id'; // Email yang dibuat di Hostinger
        $mail->Password   = '@PdShi3ld2026'; // Password email tersebut
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        // Pengirim
        $mail->setFrom('no-reply@pdkisel.id', 'PDKISEL');
        $mail->addAddress($email_tujuan, $nama);

        // Konten Email
        $mail->isHTML(true);
        $mail->Subject = 'Pendaftaran Anggota PDKISEL Berhasil';
        
        $mail->Body    = "
            <h2>Halo, {$nama}!</h2>
            <p>Terima kasih telah mendaftar sebagai anggota Persekutuan Doa Kisel (PDKISEL).</p>
            <p>Data Anda telah berhasil kami terima dan simpan di sistem kami.</p>
            <br>
            <p>Mari bertumbuh bersama dalam persekutuan yang saling menguatkan.</p>
            <br>
            <p>Tuhan memberkati,</p>
            <p><b>Pengurus PDKISEL</b></p>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
?>
