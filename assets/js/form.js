document.addEventListener("DOMContentLoaded", function() {
    
    // =========================================
    // 1. LOGIKA MASA KONTRAK
    // =========================================
    const statusKaryawan = document.getElementById('status_karyawan');
    const masaKontrakGroup = document.getElementById('masa_kontrak_group');

    if (statusKaryawan && masaKontrakGroup) {
        statusKaryawan.addEventListener('change', function() {
            const value = this.value;
            if (value === 'Kontrak PKWT' || value === 'Kontrak Project') {
                masaKontrakGroup.style.display = 'grid'; // Tampilkan
            } else {
                masaKontrakGroup.style.display = 'none'; // Sembunyikan
            }
        });
    }

    // =========================================
    // 2. LOGIKA LOKASI KANTOR PENEMPATAN
    // =========================================
    const regional = document.getElementById('regional');
    const lokasiKantorGroup = document.getElementById('lokasi_kantor_group');
    const lokasiKantorInput = document.getElementById('lokasi_kantor');

    if (regional && lokasiKantorGroup && lokasiKantorInput) {
        regional.addEventListener('change', function() {
            const value = this.value;
            if (value !== "") {
                lokasiKantorGroup.style.display = 'flex'; // Tampilkan
                // Opsional: Ubah placeholder sesuai regional
                lokasiKantorInput.placeholder = `Cth: Kantor Cabang ${value.replace('Regional ', '')}`;
            } else {
                lokasiKantorGroup.style.display = 'none'; // Sembunyikan
            }
        });
    }

    // =========================================
    // 3. LOGIKA JUMLAH ANAK (STATUS PERNIKAHAN)
    // =========================================
    const statusPernikahan = document.getElementById('status_pernikahan');
    const jumlahAnakGroup = document.getElementById('jumlah_anak_group');

    if (statusPernikahan && jumlahAnakGroup) {
        statusPernikahan.addEventListener('change', function() {
            const value = this.value;
            if (value === 'Menikah') {
                jumlahAnakGroup.style.display = 'flex'; // Tampilkan
            } else {
                jumlahAnakGroup.style.display = 'none'; // Sembunyikan
            }
        });
    }

    // =========================================
    // 4. ANIMASI TOMBOL SUBMIT (LOADING STATE)
    // =========================================
    const form = document.getElementById('registrationForm');
    const btnSubmit = document.getElementById('btnSubmit');

    if (form && btnSubmit) {
        form.addEventListener('submit', function() {
            // Ubah tombol menjadi loading
            btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengirim Data...';
            btnSubmit.disabled = true;
        });
    }

});
