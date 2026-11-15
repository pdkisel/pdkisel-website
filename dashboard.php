<?php
// Mencegah browser menyimpan halaman di cache (Anti tombol Back)
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

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
            overflow: hidden;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #E2E8F0;
            font-size: 0.9rem;
            vertical-align: middle;
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
        .btn-detail {
            background-color: #0A1F44;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.8rem;
            transition: 0.3s;
        }
        .btn-detail:hover {
            background-color: #D32F2F;
        }
        .btn-detail.active {
            background-color: #D32F2F;
        }

        /* === STYLES UNTUK BARIS DETAIL (ACCORDION) === */
        .detail-row {
            display: none;
            background-color: #F8FAFC;
        }
        .detail-row td {
            padding: 0;
            border-bottom: 2px solid #E2E8F0;
        }
        .detail-container {
            padding: 20px 30px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            animation: fadeIn 0.3s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .detail-item {
            margin-bottom: 10px;
        }
        .detail-label {
            font-size: 0.75rem;
            color: #718096;
            font-weight: 600;
            text-transform: uppercase;
            display: block;
            margin-bottom: 2px;
        }
        .detail-value {
            font-size: 0.95rem;
            color: #2D3748;
            font-weight: 500;
        }
        .full-width {
            grid-column: 1 / -1;
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
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        $no = 1;
                        while($row = $result->fetch_assoc()) {
                            $id_row = $row['id'];
                            
                            // BARIS UTAMA TABEL
                            echo "<tr>";
                            echo "<td>" . $no++ . "</td>";
                            echo "<td><strong>" . htmlspecialchars($row['nama']) . "</strong><br><small style='color:#718096;'>" . htmlspecialchars($row['email']) . "</small></td>";
                            echo "<td>" . htmlspecialchars($row['no_hp']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['status_karyawan']) . "<br><small style='color:#718096;'>" . htmlspecialchars($row['anak_usaha']) . "</small></td>";
                            echo "<td>" . htmlspecialchars($row['regional']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['gereja']) . "</td>";
                            echo "<td><button class='btn-detail' onclick='toggleDetail(".$id_row.", this)'><i class='fas fa-chevron-down'></i> Detail</button></td>";
                            echo "</tr>";

                            // BARIS DETAIL (Tersembunyi di bawahnya)
                            echo "<tr id='detail-row-".$id_row."' class='detail-row'>";
                            echo "<td colspan='7'>"; 
                            echo "<div class='detail-container'>";
                            
                            echo "<div class='detail-item'><span class='detail-label'>Nama Lengkap</span><span class='detail-value'>" . htmlspecialchars($row['nama']) . "</span></div>";
                            echo "<div class='detail-item'><span class='detail-label'>Email</span><span class='detail-value'>" . htmlspecialchars($row['email']) . "</span></div>";
                            echo "<div class='detail-item'><span class='detail-label'>No HP / WA</span><span class='detail-value'>" . htmlspecialchars($row['no_hp']) . "</span></div>";
                            echo "<div class='detail-item'><span class='detail-label'>Jenis Kelamin</span><span class='detail-value'>" . htmlspecialchars($row['jenis_kelamin']) . "</span></div>";
                            echo "<div class='detail-item'><span class='detail-label'>Tanggal Lahir</span><span class='detail-value'>" . date('d F Y', strtotime($row['tanggal_lahir'])) . "</span></div>";
                            echo "<div class='detail-item'><span class='detail-label'>Status Pernikahan</span><span class='detail-value'>" . htmlspecialchars($row['status_pernikahan']) . "</span></div>";
                            echo "<div class='detail-item'><span class='detail-label'>Jumlah Anak</span><span class='detail-value'>" . htmlspecialchars($row['jumlah_anak']) . "</span></div>";
                            echo "<div class='detail-item'><span class='detail-label'>Gereja</span><span class='detail-value'>" . htmlspecialchars($row['gereja']) . "</span></div>";
                            echo "<div class='detail-item'><span class='detail-label'>Anak Usaha</span><span class='detail-value'>" . htmlspecialchars($row['anak_usaha']) . "</span></div>";
                            echo "<div class='detail-item'><span class='detail-label'>Lokasi Kantor</span><span class='detail-value'>" . htmlspecialchars($row['lokasi_kantor'] ?? '-') . "</span></div>";
                            
                            // Logika Masa Kontrak
                            $kontrak = "Tidak ada (Karyawan Tetap)";
                            if (!empty($row['kontrak_mulai']) && !empty($row['kontrak_selesai'])) {
                                $kontrak = date('d M Y', strtotime($row['kontrak_mulai'])) . " s/d " . date('d M Y', strtotime($row['kontrak_selesai']));
                            }
                            echo "<div class='detail-item full-width'><span class='detail-label'>Masa Kontrak</span><span class='detail-value'>" . $kontrak . "</span></div>";
                            
                            echo "<div class='detail-item full-width'><span class='detail-label'>Alamat Domisili</span><span class='detail-value'>" . htmlspecialchars($row['alamat']) . "</span></div>";
                            echo "<div class='detail-item full-width'><span class='detail-label'>Waktu Pendaftaran</span><span class='detail-value'>" . date('d F Y, H:i', strtotime($row['waktu_daftar'])) . " WIB</span></div>";

                            echo "</div>"; 
                            echo "</td>";
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

    <!-- JAVASCRIPT UNTUK TOGGLE DETAIL -->
    <script>
        function toggleDetail(id, btn) {
            var detailRow = document.getElementById('detail-row-' + id);
            
            if (detailRow.style.display === 'table-row') {
                detailRow.style.display = 'none';
                btn.innerHTML = '<i class="fas fa-chevron-down"></i> Detail';
                btn.classList.remove('active');
            } else {
                detailRow.style.display = 'table-row';
                btn.innerHTML = '<i class="fas fa-chevron-up"></i> Tutup';
                btn.classList.add('active');
            }
        }
    </script>

    <!-- SCRIPT ANTI-TOMBOL BACK (Mencegah masuk dashboard via cache) -->
    <script>
        window.history.pushState(null, "", window.location.href);
        window.onpopstate = function() {
            window.history.pushState(null, "", window.location.href);
            window.location.href = 'login.php'; // Paksa kembali ke login
        };
    </script>

</body>
</html>
