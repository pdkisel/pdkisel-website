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
            min-width: 900px;
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

        /* === MODAL STYLES === */
        .modal {
            display: none; 
            position: fixed; 
            z-index: 1000; 
            left: 0;
            top: 0;
            width: 100%; 
            height: 100%; 
            background-color: rgba(10, 31, 68, 0.6); 
            backdrop-filter: blur(4px);
            align-items: center;
            justify-content: center;
        }
        .modal-content {
            background-color: #fff;
            padding: 30px;
            border-radius: 12px;
            width: 90%;
            max-width: 600px;
            position: relative;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            max-height: 90vh;
            overflow-y: auto;
        }
        .close-btn {
            position: absolute;
            top: 15px;
            right: 20px;
            font-size: 24px;
            font-weight: bold;
            color: #718096;
            cursor: pointer;
        }
        .close-btn:hover {
            color: #D32F2F;
        }
        .modal-title {
            font-family: 'Poppins', sans-serif;
            color: #0A1F44;
            margin-top: 0;
            margin-bottom: 20px;
            border-bottom: 2px solid #E2E8F0;
            padding-bottom: 10px;
        }
        .detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        .detail-item {
            margin-bottom: 10px;
        }
        .detail-label {
            font-size: 0.8rem;
            color: #718096;
            font-weight: 600;
            text-transform: uppercase;
            display: block;
        }
        .detail-value {
            font-size: 0.95rem;
            color: #2D3748;
            font-weight: 500;
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
                            // Menyiapkan data dalam format JSON untuk dilempar ke Modal
                            $jsonData = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');
                            
                            echo "<tr>";
                            echo "<td>" . $no++ . "</td>";
                            echo "<td><strong>" . htmlspecialchars($row['nama']) . "</strong><br><small style='color:#718096;'>" . htmlspecialchars($row['email']) . "</small></td>";
                            echo "<td>" . htmlspecialchars($row['no_hp']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['status_karyawan']) . "<br><small style='color:#718096;'>" . htmlspecialchars($row['anak_usaha']) . "</small></td>";
                            echo "<td>" . htmlspecialchars($row['regional']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['gereja']) . "</td>";
                            echo "<td><button class='btn-detail' onclick='bukaModal(" . $jsonData . ")'><i class='fas fa-eye'></i> Detail</button></td>";
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

    <!-- MODAL POP-UP UNTUK DETAIL -->
    <div id="detailModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="tutupModal()">&times;</span>
            <h2 class="modal-title">Detail Anggota</h2>
            <div class="detail-grid">
                <div class="detail-item">
                    <span class="detail-label">Nama Lengkap</span>
                    <span class="detail-value" id="m_nama">-</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Email</span>
                    <span class="detail-value" id="m_email">-</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">No HP / WA</span>
                    <span class="detail-value" id="m_hp">-</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Jenis Kelamin</span>
                    <span class="detail-value" id="m_jk">-</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Tanggal Lahir</span>
                    <span class="detail-value" id="m_lahir">-</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Status Pernikahan</span>
                    <span class="detail-value" id="m_nikah">-</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Jumlah Anak</span>
                    <span class="detail-value" id="m_anak">-</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Gereja</span>
                    <span class="detail-value" id="m_gereja">-</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Status Karyawan</span>
                    <span class="detail-value" id="m_status">-</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Anak Usaha</span>
                    <span class="detail-value" id="m_usaha">-</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Regional</span>
                    <span class="detail-value" id="m_regional">-</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Lokasi Kantor</span>
                    <span class="detail-value" id="m_kantor">-</span>
                </div>
                <div class="detail-item" style="grid-column: 1 / -1;">
                    <span class="detail-label">Masa Kontrak</span>
                    <span class="detail-value" id="m_kontrak">-</span>
                </div>
                <div class="detail-item" style="grid-column: 1 / -1;">
                    <span class="detail-label">Alamat Domisili</span>
                    <span class="detail-value" id="m_alamat">-</span>
                </div>
            </div>
        </div>
    </div>

    <!-- JAVASCRIPT UNTUK MODAL -->
    <script>
        function bukaModal(data) {
            // Mengisi data ke dalam elemen HTML di Modal
            document.getElementById('m_nama').innerText = data.nama || '-';
            document.getElementById('m_email').innerText = data.email || '-';
            document.getElementById('m_hp').innerText = data.no_hp || '-';
            document.getElementById('m_jk').innerText = data.jenis_kelamin || '-';
            document.getElementById('m_lahir').innerText = data.tanggal_lahir || '-';
            document.getElementById('m_nikah').innerText = data.status_pernikahan || '-';
            document.getElementById('m_anak').innerText = data.jumlah_anak || '0';
            document.getElementById('m_gereja').innerText = data.gereja || '-';
            document.getElementById('m_status').innerText = data.status_karyawan || '-';
            document.getElementById('m_usaha').innerText = data.anak_usaha || '-';
            document.getElementById('m_regional').innerText = data.regional || '-';
            document.getElementById('m_kantor').innerText = data.lokasi_kantor || '-';
            document.getElementById('m_alamat').innerText = data.alamat || '-';
            
            // Logika Masa Kontrak
            if (data.kontrak_mulai && data.kontrak_selesai) {
                document.getElementById('m_kontrak').innerText = data.kontrak_mulai + ' s/d ' + data.kontrak_selesai;
            } else {
                document.getElementById('m_kontrak').innerText = 'Tidak ada (Karyawan Tetap)';
            }

            // Tampilkan Modal
            document.getElementById('detailModal').style.display = 'flex';
        }

        function tutupModal() {
            document.getElementById('detailModal').style.display = 'none';
        }

        // Menutup modal jika area luar diklik
        window.onclick = function(event) {
            var modal = document.getElementById('detailModal');
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>

</body>
</html>
