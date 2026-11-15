<?php
session_start();

// Pengaturan Batas Percobaan Login
$max_attempts = 5; // Maksimal 5 kali gagal
$lockout_time = 900; // Waktu blokir dalam detik (15 menit)

// Cek apakah IP sedang diblokir
if (isset($_SESSION['lockout_time']) && (time() - $_SESSION['lockout_time']) < $lockout_time) {
    $sisa_waktu = ceil(($lockout_time - (time() - $_SESSION['lockout_time'])) / 60);
    $error = "Terlalu banyak percobaan. Silakan coba lagi dalam " . $sisa_waktu . " menit.";
} else {
    // Reset session jika waktu blokir sudah habis
    if (isset($_SESSION['lockout_time'])) {
        unset($_SESSION['login_attempts']);
        unset($_SESSION['lockout_time']);
    }

    // Jika admin sudah login, langsung arahkan ke dashboard
    if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
        header("Location: dashboard.php");
        exit;
    }

    require 'koneksi.php';

    $error = '';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Hitung percobaan gagal
        if (!isset($_SESSION['login_attempts'])) {
            $_SESSION['login_attempts'] = 0;
        }

        // ==========================================
        // MENGGUNAKAN PREPARED STATEMENTS UNTUK LOGIN
        // ==========================================
        $stmt = $conn->prepare("SELECT * FROM admin_pdkisel WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            // Verifikasi password menggunakan password_verify (Bcrypt)
            if (password_verify($password, $row['password'])) {
                // Login Berhasil
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_username'] = $username;
                
                // Reset percobaan gagal
                $_SESSION['login_attempts'] = 0;
                
                header("Location: dashboard.php");
                exit;
            } else {
                // Password Salah
                $_SESSION['login_attempts']++;
                $error = "Password salah. Silakan coba lagi. (Percobaan: " . $_SESSION['login_attempts'] . "/" . $max_attempts . ")";
            }
        } else {
            // Username Tidak Ditemukan
            $_SESSION['login_attempts']++;
            $error = "Username tidak ditemukan. Silakan coba lagi. (Percobaan: " . $_SESSION['login_attempts'] . "/" . $max_attempts . ")";
        }

        $stmt->close();

        // Cek jika sudah mencapai batas maksimal
        if ($_SESSION['login_attempts'] >= $max_attempts) {
            $_SESSION['lockout_time'] = time();
            $error = "Terlalu banyak percobaan gagal. Akun diblokir selama 15 menit.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - PDKISEL</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #F4F6F9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-card {
            background: #FFFFFF;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(10, 31, 68, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .login-card img {
            height: 80px;
            margin-bottom: 20px;
        }
        .login-card h1 {
            font-family: 'Poppins', sans-serif;
            color: #0A1F44;
            font-size: 1.5rem;
            margin-bottom: 30px;
        }
        .form-group {
            text-align: left;
            margin-bottom: 20px;
        }
        .form-group label {
            font-size: 0.9rem;
            color: #2D3748;
            font-weight: 500;
            display: block;
            margin-bottom: 8px;
        }
        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #E2E8F0;
            border-radius: 8px;
            font-size: 1rem;
            box-sizing: border-box;
            transition: all 0.3s;
        }
        .form-group input:focus {
            outline: none;
            border-color: #D32F2F;
            box-shadow: 0 0 0 3px rgba(211, 47, 47, 0.1);
        }
        .btn-login {
            background-color: #D32F2F;
            color: #FFFFFF;
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-top: 10px;
        }
        .btn-login:hover {
            background-color: #B71C1C;
        }
        .alert {
            background-color: #FED7D7;
            color: #C53030;
            padding: 12px;
            border-radius: 8px;
            font-size: 0.9rem;
            margin-bottom: 20px;
            text-align: left;
            display: flex;
            align-items: center;
            gap: 10px;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <img src="assets/images/logo-pdkisel.png" alt="Logo PDKISEL">
        <h1>Admin Dashboard</h1>

        <?php if ($error): ?>
            <div class="alert"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Masukkan username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Masukkan password" required>
            </div>
            <button type="submit" class="btn-login">Masuk <i class="fas fa-sign-in-alt"></i></button>
        </form>
    </div>

</body>
</html>
