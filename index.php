<?php
// Konfigurasi Sistem
define('BYPASS_LOGIN', true); // Ubah ke false jika ingin mengaktifkan password saat scan
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Inventaris Sertifikasi Karyawan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-slate-50 text-slate-800">

<script>
    // Konfigurasi bypass dari PHP
    const IS_BYPASS_ENABLED = <?php echo BYPASS_LOGIN ? 'true' : 'false'; ?>;
    
    // ... [Bagian lain kode JS Anda tetap sama, perbarui fungsi Impor di bawah] ...

    function handleExcelImport(event) {
        const file = event.target.files[0];
        const reader = new FileReader();
        reader.onload = function(e) {
            const data = new Uint8Array(e.target.result);
            const workbook = XLSX.read(data, {type: 'array'});
            const json = XLSX.utils.sheet_to_json(workbook.Sheets[workbook.SheetNames[0]]);
            
            json.forEach(row => {
                const id = row.ID.toString().trim().toUpperCase();
                const certNames = row.Sertifikasi.split(',').map(s => s.trim());
                const rawHistory = row.Riwayat_Training ? row.Riwayat_Training.split(',').map(s => s.trim()) : [];
                
                const certs = certNames.map(name => ({
                    name: name,
                    history: rawHistory // Logika penyesuaian mapping sesuai kebutuhan
                }));

                const index = employees.findIndex(emp => emp.id === id);
                if (index >= 0) {
                    employees[index].certs = certs;
                } else {
                    employees.push({ id, name: row.Nama, department: row.Departemen, certs });
                }
            });
            saveData();
            renderTable();
        };
        reader.readAsArrayBuffer(file);
    }

    function showProfile(empId) {
        if (!IS_BYPASS_ENABLED) {
            const password = prompt("Masukkan password untuk melihat data:");
            if (password !== "admin123") {
                alert("Password salah!");
                return;
            }
        }
        // ... Lanjutkan logika showProfile asli ...
    }
</script>
<!-- ... Sisa struktur HTML Sidebar & Dashboard tetap sama ... -->
</body>
</html>