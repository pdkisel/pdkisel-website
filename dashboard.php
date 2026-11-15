<?php
session_start();

// Proteksi Halaman: Jika belum login, tendang ke halaman login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

require 'koneksi.php';

// Ambil semua data pendaftar, urutkan dari yang terbaru
$sql = "SELECT * FROM anggota_pdkisel ORDER BY waktu_daftar DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - PDKISEL</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #F4F6F9;
            margin: 0;
            padding: 0;
        }
        .navbar {
            background-color: #0A1F44;
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navbar h1 {
            font-family: 'Poppins', sans-serif;
            font-size: 1.2rem;
            margin: 0;
        }
        .navbar a {
            color: #FED7D7;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 600;
        }
        .navbar a:hover {
            color: white;
        }
        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }
        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 800px;
        }
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #E2E8F0;
            font-size: 0.9rem;
        }
        th {
            background-color: #F8FAFC;
            color: #0A1F44;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
        }
        tr:hover {
            background-color: #F1F5F9;
        }
        .badge {
            background-color: #D32F2F;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
        }
    </style>
</head>
<body>

    <div class="navbar">
        <h1><i class="fas fa-users"></i> Data Pendaftar PDKISEL</h1>
        <div>
            <span style="margin-right: 15px;">Halo, <?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>

    <div class="container">
        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama & Email</th>
                        <th>No HP</th>
                        <th>Pekerjaan</th>
                        <th>Regional</th>
                        <th>Gereja</th>
                        <th>Waktu Daftar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        $no = 1;
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $no++ . "</td>";
                            echo "<td><strong>" . htmlspecialchars($row['nama']) . "</strong><br><small style='color:#718096;'>" . htmlspecialchars($row['email']) . "</small></td>";
                            echo "<td>" . htmlspecialchars($row['no_hp']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['status_karyawan']) . "<br><small style='color:#718096;'>" . htmlspecialchars($row['anak_usaha']) . "</small></td>";
                            echo "<td>" . htmlspecialchars($row['regional']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['gereja']) . "</td>";
                            echo "<td>" . date('d M Y, H:i', strtotime($row['waktu_daftar'])) . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7' style='text-align:center;'>Belum ada data pendaftar.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>
