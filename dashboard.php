<?php
// Mencegah browser menyimpan halaman di cache (Anti tombol Back)
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

require 'koneksi.php';

// ==========================================
// LOGIKA INFOGRAFIS (HITUNG DATA)
// ==========================================
$q_total = $conn->query("SELECT COUNT(*) as total FROM anggota_pdkisel");
$total_anggota = $q_total->fetch_assoc()['total'];

$status_counts = [];
$q_status = $conn->query("SELECT status_karyawan, COUNT(*) as jumlah FROM anggota_pdkisel GROUP BY status_karyawan");
while($row = $q_status->fetch_assoc()) {
    $status_counts[$row['status_karyawan']] = $row['jumlah'];
}

$usaha_counts = [];
$q_usaha = $conn->query("SELECT anak_usaha, COUNT(*) as jumlah FROM anggota_pdkisel GROUP BY anak_usaha");
while($row = $q_usaha->fetch_assoc()) {
    $usaha_counts[$row['anak_usaha']] = $row['jumlah'];
}

$regional_counts = [];
$q_regional = $conn->query("SELECT regional, COUNT(*) as jumlah FROM anggota_pdkisel GROUP BY regional");
while($row = $q_regional->fetch_assoc()) {
    $regional_counts[$row['regional']] = $row['jumlah'];
}

// Ulang Tahun Bulan Ini
$bulan_ini = date('m');
$q_ultah = $conn->query("SELECT nama, tanggal_lahir FROM anggota_pdkisel WHERE DATE_FORMAT(tanggal_lahir, '%m') = '$bulan_ini' ORDER BY DAY(tanggal_lahir) ASC");
$list_ultah = [];
while($row = $q_ultah->fetch_assoc()) {
    $list_ultah[] = $row;
}
$total_ultah = count($list_ultah);

// Ambil semua data untuk tabel
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
        body { font-family: 'Inter', sans-serif; background-color: #F4F6F9; margin: 0; padding: 0; }
        .navbar { background-color: #0A1F44; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .navbar h1 { font-family: 'Poppins', sans-serif; font-size: 1.2rem; margin: 0; }
        .navbar a { color: #FED7D7; text-decoration: none; font-size: 0.9rem; font-weight: 600; }
        .navbar a:hover { color: white; }
        .container { max-width: 1200px; margin: 30px auto; padding: 0 20px; }
        
        .card { background: white; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); overflow: hidden; margin-bottom: 20px; }
        
        /* === TOOLBAR FILTER === */
        .filter-toolbar {
            display: flex;
            gap: 15px;
            padding: 20px;
            background-color: #FFFFFF;
            border-bottom: 1px solid #E2E8F0;
            flex-wrap: wrap;
            align-items: center;
        }
        .filter-group {
            display: flex;
            align-items: center;
            gap: 8px;
            background-color: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 8px;
            padding: 8px 15px;
            flex: 1;
            min-width: 200px;
        }
        .filter-group i { color: #A0AEC0; }
        .filter-group input, .filter-group select {
            border: none;
            background: transparent;
            outline: none;
            font-family: 'Inter', sans-serif;
            font-size: 0.9rem;
            color: #2D3748;
            width: 100%;
        }
        .filter-group select { cursor: pointer; }

        /* Tombol Aksi Filter */
        .btn-filter-action {
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            border: none;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .btn-terapkan { background-color: #0A1F44; color: #FFFFFF; }
        .btn-terapkan:hover { background-color: #D32F2F; }
        .btn-reset { background-color: #E2E8F0; color: #4A5568; }
        .btn-reset:hover { background-color: #CBD5E0; }

        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #E2E8F0; font-size: 0.9rem; vertical-align: middle; }
        th { background-color: #F8FAFC; color: #0A1F44; font-weight: 600; font-family: 'Poppins', sans-serif; }
        tr:hover { background-color: #F1F5F9; }
        
        .btn-detail { background-color: #0A1F44; color: white; border: none; padding: 8px 12px; border-radius: 6px; cursor: pointer; font-size: 0.8rem; transition: 0.3s; }
        .btn-detail:hover { background-color: #D32F2F; }
        .btn-detail.active { background-color: #D32F2F; }

        .detail-row { display: none; background-color: #FFFFFF; }
        .detail-row td { padding: 0; border-bottom: 2px solid #0A1F44; }
        .detail-wrapper { padding: 30px; background-color: #F8FAFC; }
        .detail-section-title { font-family: 'Poppins', sans-serif; color: #0A1F44; font-size: 1rem; margin-top: 20px; margin-bottom: 15px; padding-bottom: 8px; border-bottom: 2px solid #E2E8F0; display: flex; align-items: center; gap: 10px; }
        .detail-section-title:first-child { margin-top: 0; }
        .detail-section-title i { color: #D32F2F; }
        .detail-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 20px; }
        .detail-text-area { display: grid; grid-template-columns: 1fr 1fr; gap: 15px 30px; }
        .detail-item { margin-bottom: 5px; }
        .detail-label { font-size: 0.75rem; color: #718096; font-weight: 600; text-transform: uppercase; display: block; margin-bottom: 4px; }
        .detail-value { font-size: 0.95rem; color: #2D3748; font-weight: 500; }
        .full-width { grid-column: 1 / -1; }
        .profile-photo-container { display: flex; justify-content: center; align-items: flex-start; padding-top: 10px; }
        .profile-photo { width: 150px; height: 150px; object-fit: cover; border-radius: 50%; border: 4px solid #FFFFFF; box-shadow: 0 4px 15px rgba(10, 31, 44, 0.15); }

        /* === STYLES UNTUK INFOGRAFIS === */
        .infografis-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-bottom: 20px; }
        .info-card { background: #FFFFFF; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); padding: 25px; border-top: 5px solid #D32F2F; transition: transform 0.3s ease; }
        .info-card-title { font-family: 'Poppins', sans-serif; color: #0A1F44; font-size: 1.1rem; margin-top: 0; margin-bottom: 15px; display: flex; align-items: center; gap: 10px; }
        .info-card-title i { color: #D32F2F; }
        
        .info-list { list-style: none; padding: 0; margin: 0; }
        .info-list li { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px dashed #E2E8F0; font-size: 0.9rem; color: #4A5568; cursor: pointer; transition: all 0.2s; }
        .info-list li:hover { background-color: #F8FAFC; padding-left: 5px; border-radius: 4px; }
        .info-list li:last-child { border-bottom: none; }
        .info-number { font-weight: 700; color: #0A1F44; background: #F4F6F9; padding: 2px 8px; border-radius: 12px; font-size: 0.85rem; }
        
        /* Card Ulang Tahun */
        .info-highlight { background: #FFF5F5; border-color: #FED7D7; border-top-color: #D4AF37; margin-bottom: 30px;}
        .info-highlight .info-card-title i { color: #D4AF37; }
        .ultah-list { list-style: none; padding: 0; margin: 0; max-height: 150px; overflow-y: auto; }
        .ultah-list li { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px dashed #FED7D7; font-size: 0.9rem; color: #2D3748; }
        .ultah-list li:last-child { border-bottom: none; }
        .ultah-tgl { font-weight: 600; color: #D32F2F; }

        /* Efek Sembunyi/Tampil Baris Tabel */
        .hidden-row { display: none !important; }

        /* Responsif Mobile */
        @media (max-width: 768px) {
            .filter-toolbar { flex-direction: column; align-items: stretch; }
            .filter-group { width: 100%; }
            .btn-filter-action { justify-content: center; width: 100%; }
        }
    </style>
</head>
<body>

    <div class="navbar">
        <h1><i class="fas fa-chart-pie"></i> Dashboard PDKISEL</h1>
        <div>
            <span style="margin-right: 15px;">Halo, <?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>

    <div class="container">
        
        <!-- INFOGRAFIS -->
        <div class="infografis-grid">
            <!-- Card 1 -->
            <div class="info-card">
                <h3 class="info-card-title"><i class="fas fa-users"></i> Ringkasan Anggota</h3>
                <ul class="info-list">
                    <li onclick="filterInfografis('all', null)"><span>Total Anggota Terdaftar</span> <span class="info-number" style="background:#0A1F44; color:white;"><?php echo $total_anggota; ?></span></li>
                    <li onclick="filterInfografis('status_karyawan', 'Tetap')"><span>Karyawan Tetap</span> <span class="info-number"><?php echo isset($status_counts['Tetap']) ? $status_counts['Tetap'] : 0; ?></span></li>
                    <li onclick="filterInfografis('status_karyawan', 'Kontrak PKWT')"><span>Kontrak PKWT</span> <span class="info-number"><?php echo isset($status_counts['Kontrak PKWT']) ? $status_counts['Kontrak PKWT'] : 0; ?></span></li>
                    <li onclick="filterInfografis('status_karyawan', 'Kontrak Project')"><span>Kontrak Project</span> <span class="info-number"><?php echo isset($status_counts['Kontrak Project']) ? $status_counts['Kontrak Project'] : 0; ?></span></li>
                </ul>
            </div>

            <!-- Card 2 -->
            <div class="info-card">
                <h3 class="info-card-title"><i class="fas fa-building"></i> Sebaran Anak Usaha</h3>
                <ul class="info-list">
                    <?php 
                    $daftar_usaha = ['Holding Kisel', 'PT. KSP', 'PT. KUT', 'PT. KAM', 'PT. KSS'];
                    foreach($daftar_usaha as $usaha) {
                        $jml = isset($usaha_counts[$usaha]) ? $usaha_counts[$usaha] : 0;
                        echo "<li onclick=\"filterInfografis('anak_usaha', '{$usaha}')\"><span>{$usaha}</span> <span class='info-number'>{$jml}</span></li>";
                    }
                    ?>
                </ul>
            </div>

            <!-- Card 3 -->
            <div class="info-card">
                <h3 class="info-card-title"><i class="fas fa-map-marker-alt"></i> Sebaran Regional</h3>
                <ul class="info-list" style="max-height: 200px; overflow-y: auto;">
                    <?php 
                    $daftar_regional = ['Kantor Pusat', 'Regional Sumbagut', 'Regional Sumbagteng', 'Regional Sumbagsel', 'Jabotabek & Jabar', 'Jawa Tengah', 'Jawa Timur', 'Balinusra', 'Kalimantan', 'Sulawesi', 'Puma'];
                    foreach($daftar_regional as $reg) {
                        $jml = isset($regional_counts[$reg]) ? $regional_counts[$reg] : 0;
                        echo "<li onclick=\"filterInfografis('regional', '{$reg}')\"><span>{$reg}</span> <span class='info-number'>{$jml}</span></li>";
                    }
                    ?>
                </ul>
            </div>
        </div>

        <!-- CARD ULANG TAHUN -->
        <div class="info-card info-highlight">
            <h3 class="info-card-title"><i class="fas fa-birthday-cake"></i> Ulang Tahun Bulan Ini (<?php echo date('F Y'); ?>) - <?php echo $total_ultah; ?> Anggota</h3>
            <?php if($total_ultah > 0): ?>
                <ul class="ultah-list">
                    <?php foreach($list_ultah as $u): ?>
                        <li>
                            <span><i class="fas fa-user" style="color:#D4AF37; margin-right: 5px;"></i> <?php echo htmlspecialchars($u['nama']); ?></span>
                            <span class="ultah-tgl"><?php echo date('d M', strtotime($u['tanggal_lahir'])); ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p style="color: #718096; font-size: 0.9rem; margin:0;">Tidak ada anggota yang berulang tahun bulan ini.</p>
            <?php endif; ?>
        </div>

        <!-- TABEL DATA DENGAN FILTER TOOLBAR -->
        <div class="card">
            <!-- Toolbar Pencarian & Filter -->
            <div class="filter-toolbar">
                <div class="filter-group">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchInput" placeholder="Cari nama atau email..." onkeyup="if(event.key === 'Enter') terapkanFilter()">
                </div>
                <div class="filter-group">
                    <i class="fas fa-briefcase"></i>
                    <select id="filterStatus">
                        <option value="">Semua Status Karyawan</option>
                        <option value="Tetap">Tetap</option>
                        <option value="Kontrak PKWT">Kontrak PKWT</option>
                        <option value="Kontrak Project">Kontrak Project</option>
                    </select>
                </div>
                <div class="filter-group">
                    <i class="fas fa-map-marker-alt"></i>
                    <select id="filterRegional">
                        <option value="">Semua Regional</option>
                        <option value="Kantor Pusat">Kantor Pusat</option>
                        <option value="Regional Sumbagut">Regional Sumbagut</option>
                        <option value="Regional Sumbagteng">Regional Sumbagteng</option>
                        <option value="Regional Sumbagsel">Regional Sumbagsel</option>
                        <option value="Jabotabek & Jabar">Jabotabek & Jabar</option>
                        <option value="Jawa Tengah">Jawa Tengah</option>
                        <option value="Jawa Timur">Jawa Timur</option>
                        <option value="Balinusra">Balinusra</option>
                        <option value="Kalimantan">Kalimantan</option>
                        <option value="Sulawesi">Sulawesi</option>
                        <option value="Puma">Puma</option>
                    </select>
                </div>
                
                <!-- Tombol Aksi -->
                <button class="btn-filter-action btn-terapkan" onclick="terapkanFilter()"><i class="fas fa-filter"></i> Terapkan</button>
                <button class="btn-filter-action btn-reset" onclick="resetFilter()"><i class="fas fa-sync-alt"></i> Reset</button>
            </div>

            <table id="dataTable">
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
                            $status = htmlspecialchars($row['status_karyawan']);
                            $usaha = htmlspecialchars($row['anak_usaha']);
                            $reg = htmlspecialchars($row['regional']);
                            $nama_email = htmlspecialchars($row['nama']) . ' ' . htmlspecialchars($row['email']);
                            
                            echo "<tr class='data-row' data-status='{$status}' data-usaha='{$usaha}' data-regional='{$reg}' data-search='" . strtolower($nama_email) . "'>";
                            echo "<td>" . $no++ . "</td>";
                            echo "<td><strong>" . htmlspecialchars($row['nama']) . "</strong><br><small style='color:#718096;'>" . htmlspecialchars($row['email']) . "</small></td>";
                            echo "<td>" . htmlspecialchars($row['no_hp']) . "</td>";
                            echo "<td>" . $status . "<br><small style='color:#718096;'>" . $usaha . "</small></td>";
                            echo "<td>" . $reg . "</td>";
                            echo "<td>" . htmlspecialchars($row['gereja']) . "</td>";
                            echo "<td><button class='btn-detail' onclick='toggleDetail(".$id_row.", this)'><i class='fas fa-chevron-down'></i> Detail</button></td>";
                            echo "</tr>";

                            echo "<tr id='detail-row-".$id_row."' class='detail-row'>";
                            echo "<td colspan='7'>"; 
                            echo "<div class='detail-wrapper'>";
                            
                            // SEKSI 1: IDENTITAS
                            echo "<div class='detail-section-title'><i class='fas fa-user-circle'></i> Identitas Diri</div>";
                            echo "<div class='detail-grid'>";
                            echo "<div class='detail-text-area'>";
                            echo "<div class='detail-item'><span class='detail-label'>Nama Lengkap</span><span class='detail-value'>" . htmlspecialchars($row['nama']) . "</span></div>";
                            echo "<div class='detail-item'><span class='detail-label'>Email</span><span class='detail-value'>" . htmlspecialchars($row['email']) . "</span></div>";
                            echo "<div class='detail-item'><span class='detail-label'>No HP / WA</span><span class='detail-value'>" . htmlspecialchars($row['no_hp']) . "</span></div>";
                            echo "<div class='detail-item'><span class='detail-label'>Jenis Kelamin</span><span class='detail-value'>" . htmlspecialchars($row['jenis_kelamin']) . "</span></div>";
                            echo "<div class='detail-item'><span class='detail-label'>Tanggal Lahir</span><span class='detail-value'>" . date('d F Y', strtotime($row['tanggal_lahir'])) . "</span></div>";
                            echo "<div class='detail-item full-width'><span class='detail-label'>Alamat Domisili</span><span class='detail-value'>" . htmlspecialchars($row['alamat']) . "</span></div>";
                            echo "</div>";
                            echo "<div class='profile-photo-container'>";
                            if (!empty($row['foto']) && file_exists('uploads/' . $row['foto'])) {
                                echo "<img src='uploads/" . htmlspecialchars($row['foto']) . "' alt='Foto Profil' class='profile-photo'>";
                            } else {
                                echo "<div style='width:150px; height:150px; border-radius:50%; background:#E2E8F0; display:flex; justify-content:center; align-items:center; color:#A0AEC0; border: 4px solid #FFF;'><i class='fas fa-user fa-3x'></i></div>";
                            }
                            echo "</div>";
                            echo "</div>";

                            // SEKSI 2: PEKERJAAN
                            echo "<div class='detail-section-title'><i class='fas fa-briefcase'></i> Data Pekerjaan</div>";
                            echo "<div class='detail-text-area' style='grid-template-columns: 1fr 1fr;'>";
                            echo "<div class='detail-item'><span class='detail-label'>Status Karyawan</span><span class='detail-value'>" . $status . "</span></div>";
                            echo "<div class='detail-item'><span class='detail-label'>Anak Usaha</span><span class='detail-value'>" . $usaha . "</span></div>";
                            echo "<div class='detail-item'><span class='detail-label'>Regional</span><span class='detail-value'>" . $reg . "</span></div>";
                            echo "<div class='detail-item'><span class='detail-label'>Lokasi Kantor</span><span class='detail-value'>" . htmlspecialchars($row['lokasi_kantor'] ?? '-') . "</span></div>";
                            $kontrak = "Tidak ada (Karyawan Tetap)";
                            if (!empty($row['kontrak_mulai']) && !empty($row['kontrak_selesai'])) {
                                $kontrak = date('d M Y', strtotime($row['kontrak_mulai'])) . " s/d " . date('d M Y', strtotime($row['kontrak_selesai']));
                            }
                            echo "<div class='detail-item full-width'><span class='detail-label'>Masa Kontrak</span><span class='detail-value'>" . $kontrak . "</span></div>";
                            echo "</div>";

                            // SEKSI 3: KELUARGA
                            echo "<div class='detail-section-title'><i class='fas fa-heart'></i> Keluarga & Gereja</div>";
                            echo "<div class='detail-text-area' style='grid-template-columns: 1fr 1fr;'>";
                            echo "<div class='detail-item'><span class='detail-label'>Status Pernikahan</span><span class='detail-value'>" . htmlspecialchars($row['status_pernikahan']) . "</span></div>";
                            echo "<div class='detail-item'><span class='detail-label'>Jumlah Anak</span><span class='detail-value'>" . htmlspecialchars($row['jumlah_anak']) . "</span></div>";
                            echo "<div class='detail-item full-width'><span class='detail-label'>Gereja / Jemaat</span><span class='detail-value'>" . htmlspecialchars($row['gereja']) . "</span></div>";
                            echo "</div>";

                            echo "<div style='margin-top: 20px; text-align: right; font-size: 0.8rem; color: #A0AEC0;'>";
                            echo "Terdaftar pada: " . date('d F Y, H:i', strtotime($row['waktu_daftar'])) . " WIB";
                            echo "</div>";

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

    <!-- JAVASCRIPT -->
    <script>
        // 1. Fungsi Toggle Detail
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

        // 2. Fungsi Filter Terpadu
        function terapkanFilter() {
            var searchText = document.getElementById('searchInput').value.toLowerCase();
            var filterStatus = document.getElementById('filterStatus').value;
            var filterRegional = document.getElementById('filterRegional').value;
            
            // Ambil nilai filter dari Infografis jika ada
            var infoKategori = window.currentInfoKategori || 'all';
            var infoNilai = window.currentInfoNilai || '';

            var rows = document.querySelectorAll('.data-row');
            
            rows.forEach(function(row) {
                var rowStatus = row.getAttribute('data-status');
                var rowUsaha = row.getAttribute('data-usaha');
                var rowRegional = row.getAttribute('data-regional');
                var rowSearch = row.getAttribute('data-search');
                
                var match = true;

                // A. Cek Filter Infografis
                if (infoKategori === 'status_karyawan' && rowStatus !== infoNilai) match = false;
                if (infoKategori === 'anak_usaha' && rowUsaha !== infoNilai) match = false;
                if (infoKategori === 'regional' && rowRegional !== infoNilai) match = false;

                // B. Cek Filter Toolbar (Pencarian)
                if (searchText && !rowSearch.includes(searchText)) match = false;

                // C. Cek Filter Toolbar (Dropdown Status)
                if (filterStatus && rowStatus !== filterStatus) match = false;

                // D. Cek Filter Toolbar (Dropdown Regional)
                if (filterRegional && rowRegional !== filterRegional) match = false;

                // Tampilkan/Sembunyikan
                if (match) {
                    row.classList.remove('hidden-row');
                } else {
                    row.classList.add('hidden-row');
                }
            });
        }

        // 3. Fungsi Klik Infografis
        function filterInfografis(kategori, nilai) {
            // Simpan state filter infografis
            window.currentInfoKategori = kategori;
            window.currentInfoNilai = nilai;

            // Sinkronkan Dropdown Toolbar
            if (kategori === 'status_karyawan') {
                document.getElementById('filterStatus').value = nilai;
                document.getElementById('filterRegional').value = '';
            } else if (kategori === 'regional') {
                document.getElementById('filterRegional').value = nilai;
                document.getElementById('filterStatus').value = '';
            } else {
                // Jika klik 'all' (total)
                document.getElementById('filterStatus').value = '';
                document.getElementById('filterRegional').value = '';
            }

            // Kosongkan pencarian saat klik infografis
            document.getElementById('searchInput').value = '';

            // Panggil fungsi filter terpadu
            terapkanFilter();
        }

        // 4. Fungsi Reset Filter
        function resetFilter() {
            // Kosongkan input
            document.getElementById('searchInput').value = '';
            document.getElementById('filterStatus').value = '';
            document.getElementById('filterRegional').value = '';
            
            // Reset state infografis
            window.currentInfoKategori = 'all';
            window.currentInfoNilai = '';

            // Tampilkan semua baris
            var rows = document.querySelectorAll('.data-row');
            rows.forEach(function(row) {
                row.classList.remove('hidden-row');
            });
        }
    </script>
</body>
</html>
