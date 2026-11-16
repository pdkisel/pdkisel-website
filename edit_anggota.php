<?php
session_start();

// Proteksi Halaman
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

require 'koneksi.php';

// Ambil ID dari URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Ambil data anggota berdasarkan ID
$stmt = $conn->prepare("SELECT * FROM anggota_pdkisel WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    die("Data anggota tidak ditemukan.");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Anggota - PDKISEL</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #F4F6F9; margin: 0; padding: 40px 20px; }
        .form-container { max-width: 800px; margin: 0 auto; background: #FFF; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); overflow: hidden; }
        .form-header { background-color: #0A1F44; color: white; padding: 20px 30px; display: flex; justify-content: space-between; align-items: center; }
        .form-header h1 { margin: 0; font-family: 'Poppins', sans-serif; font-size: 1.2rem; }
        .btn-back { color: #FED7D7; text-decoration: none; font-size: 0.9rem; font-weight: 600; }
        .btn-back:hover { color: white; }
        
        .form-body { padding: 30px; }
        .form-group { margin-bottom: 20px; display: flex; flex-direction: column; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        label { font-weight: 500; font-size: 0.9rem; margin-bottom: 8px; color: #0A1F44; }
        input, select, textarea { width: 100%; padding: 10px 15px; border: 1px solid #E2E8F0; border-radius: 8px; font-family: 'Inter', sans-serif; font-size: 0.95rem; box-sizing: border-box; }
        input:focus, select:focus, textarea:focus { outline: none; border-color: #D32F2F; box-shadow: 0 0 0 3px rgba(211, 47, 47, 0.1); }
        
        .section-title { font-family: 'Poppins', sans-serif; color: #D32F2F; font-size: 1.1rem; margin: 30px 0 15px 0; border-bottom: 2px solid #E2E8F0; padding-bottom: 10px; }
        .section-title:first-child { margin-top: 0; }
        
        .btn-save { background-color: #38A169; color: white; padding: 14px 25px; border: none; border-radius: 8px; font-family: 'Poppins', sans-serif; font-weight: 600; font-size: 1rem; cursor: pointer; width: 100%; transition: 0.3s; margin-top: 20px; }
        .btn-save:hover { background-color: #2F855A; transform: translateY(-2px); }
    </style>
</head>
<body>

    <div class="form-container">
        <div class="form-header">
            <h1>Edit Data Anggota</h1>
            <a href="dashboard.php" class="btn-back"><i class="fas fa-arrow-left"></i> Kembali ke Dashboard</a>
        </div>

        <div class="form-body">
            <form action="proses_edit.php" method="POST" enctype="multipart/form-data">
                <!-- Kirim ID tersembunyi -->
                <input type="hidden" name="id" value="<?php echo $data['id']; ?>">

                <div class="section-title">Identitas Diri</div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <input type="text" name="nama" value="<?php echo htmlspecialchars($data['nama']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($data['email']); ?>" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>No HP / WA</label>
                        <input type="text" name="no_hp" value="<?php echo htmlspecialchars($data['no_hp']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Jenis Kelamin</label>
                        <select name="jenis_kelamin" required>
                            <option value="Pria" <?php if($data['jenis_kelamin'] == 'Pria') echo 'selected'; ?>>Pria</option>
                            <option value="Wanita" <?php if($data['jenis_kelamin'] == 'Wanita') echo 'selected'; ?>>Wanita</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Alamat Domisili</label>
                    <textarea name="alamat" rows="2" required><?php echo htmlspecialchars($data['alamat']); ?></textarea>
                </div>

                <div class="section-title">Data Pekerjaan</div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Status Karyawan</label>
                        <select name="status_karyawan" required>
                            <option value="Tetap" <?php if($data['status_karyawan'] == 'Tetap') echo 'selected'; ?>>Tetap</option>
                            <option value="Kontrak PKWT" <?php if($data['status_karyawan'] == 'Kontrak PKWT') echo 'selected'; ?>>Kontrak PKWT</option>
                            <option value="Kontrak Project" <?php if($data['status_karyawan'] == 'Kontrak Project') echo 'selected'; ?>>Kontrak Project</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Anak Usaha</label>
                        <select name="anak_usaha" required>
                            <?php 
                            $usaha_list = ['Holding Kisel', 'PT. KSP', 'PT. KUT', 'PT. KAM', 'PT. KSS'];
                            foreach($usaha_list as $u) {
                                $sel = ($data['anak_usaha'] == $u) ? 'selected' : '';
                                echo "<option value='$u' $sel>$u</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Regional</label>
                        <select name="regional" required>
                            <?php 
                            $reg_list = ['Kantor Pusat', 'Regional Sumbagut', 'Regional Sumbagteng', 'Regional Sumbagsel', 'Jabotabek & Jabar', 'Jawa Tengah', 'Jawa Timur', 'Balinusra', 'Kalimantan', 'Sulawesi', 'Puma'];
                            foreach($reg_list as $r) {
                                $sel = ($data['regional'] == $r) ? 'selected' : '';
                                echo "<option value='$r' $sel>$r</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Lokasi Kantor</label>
                        <input type="text" name="lokasi_kantor" value="<?php echo htmlspecialchars($data['lokasi_kantor'] ?? ''); ?>">
                    </div>
                </div>

                <div class="section-title">Keluarga & Gereja</div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Status Pernikahan</label>
                        <select name="status_pernikahan" required>
                            <option value="Belum Menikah" <?php if($data['status_pernikahan'] == 'Belum Menikah') echo 'selected'; ?>>Belum Menikah</option>
                            <option value="Menikah" <?php if($data['status_pernikahan'] == 'Menikah') echo 'selected'; ?>>Menikah</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Jumlah Anak</label>
                        <input type="number" name="jumlah_anak" value="<?php echo htmlspecialchars($data['jumlah_anak']); ?>" min="0">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Denominasi Kristen</label>
                        <select name="kristen" required>
                            <option value="Protestan" <?php if($data['kristen'] == 'Protestan') echo 'selected'; ?>>Protestan</option>
                            <option value="Katolik" <?php if($data['kristen'] == 'Katolik') echo 'selected'; ?>>Katolik</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Nama Gereja</label>
                        <input type="text" name="gereja" value="<?php echo htmlspecialchars($data['gereja']); ?>" required>
                    </div>
                </div>

                <button type="submit" class="btn-save"><i class="fas fa-save"></i> Simpan Perubahan</button>
            </form>
        </div>
    </div>

</body>
</html>
