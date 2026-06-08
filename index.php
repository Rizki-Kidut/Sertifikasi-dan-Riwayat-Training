<?php
/* =========================================================
SKELETON BACKEND PHP
Kode PHP ini disiapkan jika Anda memindahkan aplikasi ini 
ke server lokal (XAMPP/MAMP) atau hosting sungguhan.
=========================================================
*/

session_start();

$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'certitrack_db';

/* // Uncomment jika sudah terhubung ke database sungguhan
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'save_employee') {
        echo json_encode(["status" => "success", "message" => "Data tersimpan"]);
        exit;
    }
}
*/
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Inventaris Sertifikasi Karyawan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
        .fade-in { animation: fadeIn 0.3s ease-in-out; }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #93c5fd; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #3b82f6; }
        @media print {
            body * { visibility: hidden; }
            #print-area, #print-area * { visibility: visible; }
            #print-area { position: absolute; left: 0; top: 0; }
        }
    </style>
</head>
<body class="text-slate-800 overflow-hidden">

    <div class="flex h-screen bg-slate-50 w-full">
        
        <!-- SIDEBAR -->
        <aside id="sidebar" class="w-64 bg-blue-800 text-white flex flex-col transition-transform duration-300 z-50 fixed md:relative h-full transform -translate-x-full md:translate-x-0 shadow-xl md:shadow-none">
            <div class="h-16 flex items-center justify-between px-6 bg-blue-900 border-b border-blue-700/50">
                <div class="flex items-center gap-3">
                    <i data-lucide="shield-check" class="w-7 h-7 text-blue-300"></i>
                    <span class="font-bold text-xl tracking-tight">CertiTrack</span>
                </div>
                <button onclick="toggleSidebar()" class="md:hidden text-blue-300 hover:text-white">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>
            <nav class="flex-1 py-6 px-3 space-y-2 overflow-y-auto">
                <button onclick="switchTab('dashboard')" id="nav-dashboard" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg bg-blue-700 text-white font-medium transition">
                    <i data-lucide="layout-dashboard" class="w-5 h-5"></i> Dashboard
                </button>
                <button onclick="switchTab('data')" id="nav-data" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg text-blue-200 hover:bg-blue-700 hover:text-white font-medium transition">
                    <i data-lucide="users" class="w-5 h-5"></i> Data Karyawan
                </button>
                <button onclick="switchTab('scanner')" id="nav-scanner" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg text-blue-200 hover:bg-blue-700 hover:text-white font-medium transition">
                    <i data-lucide="scan-line" class="w-5 h-5"></i> Scanner QR
                </button>
            </nav>
            <div class="p-4 bg-blue-900/50 border-t border-blue-700/50">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center">
                        <i data-lucide="user" class="w-4 h-4 text-blue-200"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium">Administrator</p>
                        <p class="text-xs text-blue-300">HR Department</p>
                    </div>
                </div>
            </div>
        </aside>

        <div id="sidebar-overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-slate-900/50 z-40 hidden md:hidden transition-opacity opacity-0"></div>

        <!-- KONTEN UTAMA -->
        <div class="flex-1 flex flex-col h-full overflow-hidden">
            
            <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-4 md:hidden shadow-sm z-30">
                <div class="flex items-center gap-3">
                    <i data-lucide="shield-check" class="w-6 h-6 text-blue-600"></i>
                    <span class="font-bold text-lg text-slate-800 tracking-tight">CertiTrack</span>
                </div>
                <button onclick="toggleSidebar()" class="p-2 text-slate-500 hover:text-blue-600 hover:bg-slate-100 rounded-lg transition">
                    <i data-lucide="menu" class="w-6 h-6"></i>
                </button>
            </header>

            <main class="flex-1 overflow-y-auto p-4 md:p-8 w-full bg-slate-50">
                
                <!-- TAB: DASHBOARD -->
                <section id="tab-dashboard" class="fade-in max-w-7xl mx-auto">
                    <div class="mb-8 flex justify-between items-end">
                        <div>
                            <h1 class="text-2xl md:text-3xl font-bold text-slate-800">Dashboard Sertifikasi</h1>
                            <p class="text-slate-500 mt-1">Ringkasan jumlah karyawan yang tersertifikasi secara keseluruhan.</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6" id="dashboard-grid"></div>
                </section>

                <!-- TAB: DATA KARYAWAN -->
                <section id="tab-data" class="hidden fade-in max-w-7xl mx-auto">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                        <div>
                            <h1 class="text-2xl md:text-3xl font-bold text-slate-800">Data Karyawan</h1>
                            <p class="text-slate-500 mt-1">Kelola riwayat pelatihan dan sertifikasi.</p>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <!-- Tombol Download Template -->
                            <button onclick="downloadExcelTemplate()" class="cursor-pointer bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2.5 rounded-lg text-sm font-medium transition flex items-center gap-2 shadow-sm border border-slate-200">
                                <i data-lucide="file-down" class="w-4 h-4"></i>
                                <span class="hidden sm:inline">Template Excel</span>
                            </button>
                            <!-- Tombol Import Excel -->
                            <label class="cursor-pointer bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2.5 rounded-lg text-sm font-medium transition flex items-center gap-2 shadow-sm">
                                <i data-lucide="file-spreadsheet" class="w-4 h-4"></i>
                                <span class="hidden sm:inline">Import Excel</span>
                                <input type="file" id="excel-upload" class="hidden" accept=".xlsx, .xls, .csv" onchange="handleExcelImport(event)">
                            </label>
                            <!-- Tombol Tambah Manual -->
                            <button onclick="openModal('modal-form')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-lg text-sm font-medium transition flex items-center gap-2 shadow-sm">
                                <i data-lucide="user-plus" class="w-4 h-4"></i>
                                Tambah Data
                            </button>
                        </div>
                    </div>

                    <!-- Info Banner Format Import -->
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-5 flex gap-3 items-start">
                        <i data-lucide="info" class="w-5 h-5 text-blue-500 shrink-0 mt-0.5"></i>
                        <div class="text-sm text-blue-800">
                            <p class="font-bold mb-1">Format Kolom Excel untuk Import:</p>
                            <p><span class="font-semibold">ID</span> · <span class="font-semibold">Nama</span> · <span class="font-semibold">Departemen</span> · <span class="font-semibold">Sertifikasi</span> (dipisah koma) · <span class="font-semibold">Riwayat Training</span></p>
                            <p class="mt-1 text-blue-600 text-xs font-mono bg-blue-100 px-2 py-1 rounded inline-block">Riwayat Training: Final Checker:2024-05-10,2025-05-15;Jouho Board:2024-01-15</p>
                            <p class="mt-1 text-xs text-blue-500">Klik <strong>Template Excel</strong> untuk unduh file contoh siap pakai.</p>
                        </div>
                    </div>

                    <!-- Tabel Karyawan -->
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse min-w-[800px]">
                                <thead>
                                    <tr class="bg-slate-50 text-slate-600 border-b border-slate-200 text-sm">
                                        <th class="py-4 px-6 font-semibold w-32">ID Karyawan</th>
                                        <th class="py-4 px-6 font-semibold">Nama Lengkap</th>
                                        <th class="py-4 px-6 font-semibold">Departemen</th>
                                        <th class="py-4 px-6 font-semibold text-center w-40">Jumlah Sertifikasi</th>
                                        <th class="py-4 px-6 font-semibold text-center w-48">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="employee-table-body" class="text-sm divide-y divide-slate-100"></tbody>
                            </table>
                        </div>
                        <div id="empty-state" class="hidden text-center py-16">
                            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-slate-100">
                                <i data-lucide="users" class="w-10 h-10 text-slate-300"></i>
                            </div>
                            <p class="text-slate-600 font-medium text-lg">Belum ada data karyawan.</p>
                            <p class="text-slate-400 text-sm mt-1">Silakan tambah data secara manual atau import dari file Excel.</p>
                        </div>
                    </div>
                </section>

                <!-- TAB: SCANNER QR -->
                <section id="tab-scanner" class="hidden fade-in max-w-4xl mx-auto">
                    <div class="text-center mb-8">
                        <h1 class="text-2xl md:text-3xl font-bold text-slate-800">Scanner QR Code</h1>
                        <p class="text-slate-500 mt-2">Scan QR Code pada ID Card menggunakan kamera untuk melihat riwayat sertifikasi.</p>
                        <!-- [FIX 1] Info bahwa scan tidak butuh login -->
                        <div class="inline-flex items-center gap-2 mt-3 bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-medium px-4 py-2 rounded-full">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            Data profil tersimpan dalam QR — tidak butuh login
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden p-6 md:p-10 mb-6">
                        <div id="reader" class="w-full max-w-md mx-auto rounded-xl overflow-hidden border-2 border-dashed border-blue-300 bg-slate-50 shadow-inner"></div>
                        
                        <div class="mt-8 text-center max-w-sm mx-auto">
                            <div class="flex items-center gap-4 mb-4">
                                <div class="h-px bg-slate-200 flex-1"></div>
                                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Atau Input Manual</p>
                                <div class="h-px bg-slate-200 flex-1"></div>
                            </div>
                            <div class="flex gap-2">
                                <input type="text" id="manual-scan-id" placeholder="Masukkan ID (Contoh: EMP001)" class="flex-1 px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm font-medium">
                                <button onclick="simulateScan()" class="bg-slate-800 hover:bg-slate-900 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm">Cek</button>
                            </div>
                        </div>
                    </div>
                </section>

            </main>
        </div>
    </div>

    <!-- ================= MODALS ================= -->

    <!-- Modal Form Tambah/Edit Karyawan -->
    <div id="modal-form" class="fixed inset-0 bg-slate-900/60 hidden z-[100] flex items-center justify-center p-4 opacity-0 transition-opacity backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] flex flex-col transform scale-95 transition-transform duration-300 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-white">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center">
                        <i data-lucide="user-plus" class="w-5 h-5 text-blue-600"></i>
                    </div>
                    <h3 class="font-bold text-lg text-slate-800">Form Karyawan & Sertifikasi</h3>
                </div>
                <button onclick="closeModal('modal-form')" class="text-slate-400 hover:bg-slate-100 hover:text-red-500 p-2 rounded-lg transition"><i data-lucide="x"></i></button>
            </div>
            <div class="px-6 py-6 overflow-y-auto flex-1 bg-slate-50/50">
                <form id="employee-form" onsubmit="saveEmployee(event)">
                    <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm mb-6">
                        <h4 class="text-sm font-bold text-slate-800 mb-4 uppercase tracking-wider">Informasi Dasar</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1.5">ID Karyawan</label>
                                <input type="text" id="emp-id" required class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm uppercase transition bg-slate-50 focus:bg-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1.5">Nama Lengkap</label>
                                <input type="text" id="emp-name" required class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm transition bg-slate-50 focus:bg-white">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-slate-700 mb-1.5">Departemen / Divisi</label>
                                <input type="text" id="emp-dept" required class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm transition bg-slate-50 focus:bg-white">
                            </div>
                        </div>
                    </div>
                    <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
                        <h4 class="text-sm font-bold text-slate-800 mb-4 uppercase tracking-wider">Pilih Sertifikasi</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3" id="cert-checkboxes"></div>
                    </div>
                </form>
            </div>
            <div class="px-6 py-4 border-t border-slate-100 bg-white flex justify-end gap-3">
                <button onclick="closeModal('modal-form')" class="px-5 py-2.5 text-slate-600 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 text-sm font-medium transition">Batal</button>
                <button type="submit" form="employee-form" class="px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium transition shadow-sm flex items-center gap-2">
                    <i data-lucide="save" class="w-4 h-4"></i> Simpan Data
                </button>
            </div>
        </div>
    </div>

    <!-- Modal ID Card & QR Code -->
    <div id="modal-idcard" class="fixed inset-0 bg-slate-900/60 hidden z-[100] flex items-center justify-center p-4 opacity-0 transition-opacity backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden transform scale-95 transition-transform duration-300">
            <div class="px-5 py-4 border-b border-slate-100 flex justify-between items-center bg-white">
                <h3 class="font-bold text-slate-800 flex items-center gap-2">
                    <i data-lucide="badge-check" class="w-5 h-5 text-blue-600"></i> ID Card Preview
                </h3>
                <button onclick="closeModal('modal-idcard')" class="text-slate-400 hover:text-red-500 transition bg-slate-50 hover:bg-red-50 p-1.5 rounded-lg"><i data-lucide="x"></i></button>
            </div>
            <div class="p-8 bg-slate-50 flex justify-center">
                <div id="print-area" class="w-[260px] bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden relative">
                    <div class="bg-gradient-to-r from-blue-700 to-blue-500 h-24 w-full absolute top-0 left-0"></div>
                    <div class="relative pt-8 px-5 pb-6 flex flex-col items-center">
                        <div class="w-24 h-24 bg-white rounded-full border-[5px] border-white shadow-md flex items-center justify-center mb-4 z-10 overflow-hidden">
                            <i data-lucide="user" class="w-12 h-12 text-slate-300"></i>
                        </div>
                        <h4 id="idcard-name" class="font-bold text-xl text-slate-800 text-center leading-tight mb-1">Nama</h4>
                        <p id="idcard-dept" class="text-sm text-blue-600 font-semibold mb-1">Departemen</p>
                        <p id="idcard-id" class="text-xs text-slate-500 font-mono bg-slate-100 px-2 py-0.5 rounded-full mb-6">ID</p>
                        <div class="p-2.5 bg-white border-2 border-dashed border-slate-200 rounded-xl">
                            <div id="idcard-qr"></div>
                        </div>
                        <p class="text-[10px] text-slate-400 mt-3 text-center uppercase tracking-wider font-semibold">Scan QR untuk Verifikasi</p>
                    </div>
                </div>
            </div>
            <!-- [FIX 1] Info bahwa QR bisa di-scan tanpa login -->
            <div class="mx-6 mb-3 bg-emerald-50 border border-emerald-200 rounded-lg px-3 py-2 flex items-center gap-2">
                <i data-lucide="shield-check" class="w-4 h-4 text-emerald-600 shrink-0"></i>
                <p class="text-xs text-emerald-700 font-medium">QR ini bisa di-scan dari device manapun tanpa login.</p>
            </div>
            <div class="px-6 pb-5 flex justify-center">
                <button onclick="window.print()" class="w-full px-4 py-2.5 bg-slate-800 text-white rounded-lg hover:bg-slate-900 text-sm font-medium transition flex items-center justify-center gap-2 shadow-sm">
                    <i data-lucide="printer" class="w-4 h-4"></i> Cetak ID Card
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Profil Karyawan (Hasil Scan QR) -->
    <div id="modal-profile" class="fixed inset-0 bg-slate-900/60 hidden z-[100] flex items-center justify-center p-4 opacity-0 transition-opacity backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-xl overflow-hidden transform scale-95 transition-transform duration-300">
            <div class="bg-gradient-to-r from-blue-700 to-blue-600 px-6 py-10 text-center relative overflow-hidden">
                <div class="absolute -top-10 -left-10 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
                <div class="absolute -bottom-10 -right-10 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
                <button onclick="closeModal('modal-profile')" class="absolute top-4 right-4 text-blue-200 hover:text-white bg-black/10 hover:bg-black/20 p-2 rounded-full transition z-10"><i data-lucide="x" class="w-5 h-5"></i></button>
                <div class="w-24 h-24 bg-white rounded-full mx-auto flex items-center justify-center mb-4 shadow-lg relative z-10">
                    <i data-lucide="shield-check" class="w-12 h-12 text-blue-600"></i>
                </div>
                <h3 id="profile-name" class="font-bold text-3xl text-white relative z-10">Nama Karyawan</h3>
                <p id="profile-id-dept" class="text-blue-100 font-medium text-sm mt-2 relative z-10 bg-black/20 inline-block px-3 py-1 rounded-full">ID • Departemen</p>
            </div>
            
            <div class="p-6 bg-slate-50">
                <h4 class="font-bold text-slate-800 border-b border-slate-200 pb-3 mb-5 flex items-center gap-2 text-lg">
                    <i data-lucide="award" class="w-5 h-5 text-amber-500"></i> Riwayat Sertifikasi & Training
                </h4>
                <ul id="profile-certs" class="space-y-4 max-h-[400px] overflow-y-auto pr-2"></ul>
                <div id="profile-no-cert" class="hidden text-center py-8 bg-white rounded-xl border border-slate-200 border-dashed">
                    <i data-lucide="file-warning" class="w-12 h-12 mx-auto text-slate-300 mb-2"></i>
                    <p class="text-slate-500 font-medium">Belum ada sertifikasi.</p>
                </div>
            </div>
            
            <div class="px-6 py-4 bg-white border-t border-slate-100 flex justify-between items-center">
                <p class="text-xs text-slate-400">Data realtime tersinkronisasi</p>
                <span class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-700 bg-emerald-100 px-3 py-1.5 rounded-full border border-emerald-200">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> TERVERIFIKASI
                </span>
            </div>
        </div>
    </div>

    <!-- Modal Kelola Training -->
    <div id="modal-training" class="fixed inset-0 bg-slate-900/60 hidden z-[100] flex items-center justify-center p-4 opacity-0 transition-opacity backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] flex flex-col transform scale-95 transition-transform duration-300 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-white">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-amber-50 flex items-center justify-center border border-amber-100">
                        <i data-lucide="calendar-clock" class="w-5 h-5 text-amber-600"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg text-slate-800">Kelola Riwayat Training</h3>
                        <p id="training-emp-name" class="text-sm font-medium text-slate-500 mt-0.5">Nama Karyawan</p>
                    </div>
                </div>
                <button onclick="closeModal('modal-training')" class="text-slate-400 hover:bg-slate-100 hover:text-red-500 p-2 rounded-lg transition"><i data-lucide="x"></i></button>
            </div>
            <div class="px-6 py-6 overflow-y-auto flex-1 bg-slate-50" id="training-content"></div>
            <div class="px-6 py-4 border-t border-slate-100 bg-white flex justify-end gap-3">
                <button onclick="closeModal('modal-training')" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-bold transition shadow-sm">Selesai</button>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div id="toast" class="fixed bottom-5 right-5 bg-slate-900 text-white px-5 py-3.5 rounded-xl shadow-2xl transform translate-y-20 opacity-0 transition-all duration-300 z-[200] flex items-center gap-3 border border-slate-700">
        <div class="w-8 h-8 rounded-full bg-blue-500/20 flex items-center justify-center">
            <i data-lucide="info" class="w-4 h-4 text-blue-400"></i>
        </div>
        <span id="toast-msg" class="text-sm font-medium">Notifikasi</span>
    </div>

    <!-- ================= JAVASCRIPT ================= -->
    <script>
        lucide.createIcons();

        const TARGET_CERTS = [
            "Final Checker", 
            "Pemeriksa Produk", 
            "Pemeriksa Dalam Proses", 
            "Jouho Board", 
            "Trainer Proses Penting", 
            "Trainer Proses Khusus",
            "Shipping Approval"
        ];

        let employees = [];
        let html5QrcodeScanner = null;

        // ============================================================
        // [FIX 1] HELPER: Encode / Decode Profile Data untuk QR URL
        // Menggunakan base64 + encodeURIComponent agar aman Unicode
        // ============================================================
        function encodeProfileForQR(empData) {
            const payload = {
                id: empData.id,
                name: empData.name,
                department: empData.department,
                certs: empData.certs
            };
            // Encode: JSON -> encodeURIComponent -> btoa (aman untuk nama Indonesia)
            return btoa(encodeURIComponent(JSON.stringify(payload)));
        }

        function decodeProfileFromQR(encoded) {
            // Decode: atob -> decodeURIComponent -> JSON.parse
            return JSON.parse(decodeURIComponent(atob(encoded)));
        }

        // ============================================================
        // Inisialisasi Aplikasi
        // ============================================================
        document.addEventListener('DOMContentLoaded', () => {
            loadData();
            generateCertCheckboxes();
            renderDashboard();
            renderTable();
            
            const urlParams = new URLSearchParams(window.location.search);
            
            // [FIX 1] Cek ?profile= terlebih dahulu (QR baru dengan data embed)
            // Prioritas: profile > id
            const profileParam = urlParams.get('profile');
            const scanId = urlParams.get('id');

            if (profileParam) {
                // Data profil ada di URL → tampilkan langsung, TANPA butuh localStorage / login
                try {
                    const profileData = decodeProfileFromQR(profileParam);
                    setTimeout(() => showProfile(profileData), 300);
                } catch(e) {
                    console.error("Gagal decode data QR:", e);
                    showToast("QR Code tidak valid atau rusak.");
                }
            } else if (scanId) {
                // Backward compat: QR lama dengan ?id= (butuh data di localStorage)
                setTimeout(() => showProfile(scanId), 300);
            }
        });

        // ============================================================
        // Navigasi Tab
        // ============================================================
        function switchTab(tabId) {
            document.getElementById('tab-dashboard').classList.add('hidden');
            document.getElementById('tab-data').classList.add('hidden');
            document.getElementById('tab-scanner').classList.add('hidden');
            document.getElementById('tab-' + tabId).classList.remove('hidden');

            ['dashboard', 'data', 'scanner'].forEach(id => {
                const btn = document.getElementById('nav-' + id);
                if(btn) {
                    if(id === tabId) {
                        btn.classList.remove('text-blue-200', 'hover:bg-blue-700', 'hover:text-white');
                        btn.classList.add('bg-blue-700', 'text-white');
                    } else {
                        btn.classList.remove('bg-blue-700', 'text-white');
                        btn.classList.add('text-blue-200', 'hover:bg-blue-700', 'hover:text-white');
                    }
                }
            });

            if (tabId === 'scanner') {
                startScanner();
            } else {
                stopScanner();
            }

            renderDashboard();
            renderTable();

            if(window.innerWidth < 768) {
                toggleSidebar(false);
            }
        }

        function toggleSidebar(forceState = null) {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            let isOpening = forceState !== null ? forceState : sidebar.classList.contains('-translate-x-full');
            if (isOpening) {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
                setTimeout(() => overlay.classList.remove('opacity-0'), 10);
            } else {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('opacity-0');
                setTimeout(() => overlay.classList.add('hidden'), 300);
            }
        }

        // ============================================================
        // Data Management
        // ============================================================
        function loadData() {
            const saved = localStorage.getItem('certiTrackData');
            if (saved) {
                let parsedData = JSON.parse(saved);
                employees = parsedData.map(emp => {
                    if (emp.certs && emp.certs.length > 0 && typeof emp.certs[0] === 'string') {
                        emp.certs = emp.certs.map(certName => ({ name: certName, history: [] }));
                    }
                    return emp;
                });
            } else {
                employees = [
                    { id: "EMP001", name: "Budi Santoso", department: "Quality Control", certs: [{name: "Final Checker", history: ["2024-05-10", "2025-05-15"]}, {name: "Shipping Approval", history: ["2023-01-01"]}] },
                    { id: "EMP002", name: "Siti Aminah", department: "Produksi", certs: [{name: "Jouho Board", history: ["2024-01-15"]}, {name: "Pemeriksa Dalam Proses", history: []}] }
                ];
                saveData();
            }
        }

        function saveData() {
            localStorage.setItem('certiTrackData', JSON.stringify(employees));
        }

        // ============================================================
        // Dashboard
        // ============================================================
        function renderDashboard() {
            const grid = document.getElementById('dashboard-grid');
            grid.innerHTML = '';
            TARGET_CERTS.forEach(certName => {
                const count = employees.filter(emp => emp.certs.some(c => c.name === certName)).length;
                const total = employees.length || 1;
                const percentage = Math.round((count / total) * 100) || 0;
                const colorClass = count > 0 ? 'bg-blue-600' : 'bg-slate-300';
                const textClass = count > 0 ? 'text-blue-600' : 'text-slate-400';
                const bgIconClass = count > 0 ? 'bg-blue-50' : 'bg-slate-50';
                const cardHtml = `
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 hover:shadow-md transition-shadow group">
                        <div class="flex justify-between items-start mb-5">
                            <div class="pr-4">
                                <h3 class="font-bold text-slate-800 leading-tight group-hover:text-blue-600 transition-colors">${certName}</h3>
                            </div>
                            <div class="w-12 h-12 rounded-full ${bgIconClass} flex items-center justify-center shrink-0">
                                <i data-lucide="award" class="w-6 h-6 ${textClass}"></i>
                            </div>
                        </div>
                        <div class="flex items-end gap-2 mb-3">
                            <span class="text-4xl font-extrabold ${textClass} tracking-tight">${count}</span>
                            <span class="text-sm font-medium text-slate-500 mb-1.5">Member</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-2.5 mb-2 overflow-hidden">
                            <div class="${colorClass} h-2.5 rounded-full transition-all duration-1000 ease-out" style="width: ${percentage}%"></div>
                        </div>
                        <p class="text-xs font-semibold text-slate-400 text-right">${percentage}% Tersertifikasi</p>
                    </div>
                `;
                grid.insertAdjacentHTML('beforeend', cardHtml);
            });
            lucide.createIcons();
        }

        // ============================================================
        // Tabel & CRUD
        // ============================================================
        function renderTable() {
            const tbody = document.getElementById('employee-table-body');
            const emptyState = document.getElementById('empty-state');
            tbody.innerHTML = '';
            if (employees.length === 0) {
                emptyState.classList.remove('hidden');
            } else {
                emptyState.classList.add('hidden');
                employees.forEach(emp => {
                    const tr = document.createElement('tr');
                    tr.className = "hover:bg-blue-50/50 transition-colors group";
                    tr.innerHTML = `
                        <td class="py-4 px-6">
                            <span class="font-mono text-blue-700 bg-blue-50 px-2.5 py-1 rounded-md font-semibold text-xs border border-blue-100 group-hover:bg-white group-hover:border-blue-200">${emp.id}</span>
                        </td>
                        <td class="py-4 px-6 font-bold text-slate-800">${emp.name}</td>
                        <td class="py-4 px-6 text-slate-600 font-medium text-sm">${emp.department}</td>
                        <td class="py-4 px-6 text-center">
                            <span class="bg-slate-100 text-slate-700 py-1.5 px-3 rounded-full text-xs font-bold border border-slate-200">
                                ${emp.certs.length} Sertifikat
                            </span>
                        </td>
                        <td class="py-4 px-6 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <button onclick="openTrainingModal('${emp.id}')" title="Kelola Riwayat Training" class="p-2 text-slate-400 hover:text-amber-600 bg-white hover:bg-amber-50 border border-slate-200 rounded-lg transition shadow-sm">
                                    <i data-lucide="calendar-clock" class="w-4 h-4"></i>
                                </button>
                                <button onclick="showProfile('${emp.id}')" title="Lihat Profil" class="p-2 text-slate-400 hover:text-blue-600 bg-white hover:bg-blue-50 border border-slate-200 rounded-lg transition shadow-sm">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </button>
                                <button onclick="openIDCard('${emp.id}')" title="Generate ID Card & QR" class="p-2 text-slate-400 hover:text-emerald-600 bg-white hover:bg-emerald-50 border border-slate-200 rounded-lg transition shadow-sm">
                                    <i data-lucide="qr-code" class="w-4 h-4"></i>
                                </button>
                                <button onclick="deleteEmployee('${emp.id}')" title="Hapus" class="p-2 text-slate-400 hover:text-red-600 bg-white hover:bg-red-50 border border-slate-200 rounded-lg transition shadow-sm">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
                lucide.createIcons();
            }
        }

        function generateCertCheckboxes() {
            const container = document.getElementById('cert-checkboxes');
            container.innerHTML = '';
            TARGET_CERTS.forEach(cert => {
                container.insertAdjacentHTML('beforeend', `
                    <label class="flex items-center gap-3 p-3 border border-slate-200 rounded-xl cursor-pointer hover:bg-blue-50 hover:border-blue-200 transition bg-slate-50">
                        <input type="checkbox" value="${cert}" class="cert-input w-4 h-4 text-blue-600 focus:ring-blue-500 border-slate-300 rounded">
                        <span class="text-sm font-medium text-slate-700">${cert}</span>
                    </label>
                `);
            });
        }

        function saveEmployee(e) {
            e.preventDefault();
            const id = document.getElementById('emp-id').value.trim().toUpperCase();
            const name = document.getElementById('emp-name').value.trim();
            const dept = document.getElementById('emp-dept').value.trim();
            const existingIndex = employees.findIndex(emp => emp.id === id);
            const existingEmployee = existingIndex >= 0 ? employees[existingIndex] : null;
            const certInputs = document.querySelectorAll('.cert-input:checked');
            const certs = Array.from(certInputs).map(input => {
                const certName = input.value;
                let history = [];
                if (existingEmployee) {
                    const oldCert = existingEmployee.certs.find(c => c.name === certName);
                    if (oldCert) history = oldCert.history;
                }
                return { name: certName, history: history };
            });
            const newData = { id, name, department: dept, certs };
            if (existingIndex >= 0) {
                employees[existingIndex] = newData;
                showToast(`Data ${name} berhasil diperbarui.`);
            } else {
                employees.push(newData);
                showToast(`Karyawan ${name} berhasil ditambahkan.`);
            }
            saveData();
            renderTable();
            renderDashboard();
            closeModal('modal-form');
            e.target.reset();
        }

        function deleteEmployee(id) {
            if(confirm('Apakah Anda yakin ingin menghapus data karyawan ini?')) {
                employees = employees.filter(emp => emp.id !== id);
                saveData();
                renderTable();
                renderDashboard();
                showToast('Data berhasil dihapus.');
            }
        }

        // ============================================================
        // [FIX 2] Import Excel dengan dukungan kolom "Riwayat Training"
        // Format kolom: Final Checker:2024-05-10,2025-05-15;Jouho Board:2024-01-15
        // ============================================================
        function handleExcelImport(event) {
            const file = event.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function(e) {
                try {
                    const data = new Uint8Array(e.target.result);
                    const workbook = XLSX.read(data, {type: 'array'});
                    const firstSheetName = workbook.SheetNames[0];
                    const worksheet = workbook.Sheets[firstSheetName];
                    const json = XLSX.utils.sheet_to_json(worksheet);
                    
                    let countAdded = 0;

                    json.forEach(row => {
                        const id = (row['ID'] || row['id'] || row['Id'] || '').toString().trim().toUpperCase();
                        const name = (row['Nama'] || row['nama'] || row['Name'] || '').toString().trim();
                        const dept = (row['Departemen'] || row['departemen'] || row['Department'] || '').toString().trim();
                        
                        // Parse kolom Sertifikasi (dipisah koma)
                        const rawCerts = (row['Sertifikasi'] || row['sertifikasi'] || '').toString();
                        const certNames = rawCerts ? rawCerts.split(',').map(s => s.trim()).filter(s => s) : [];

                        // [FIX 2] Parse kolom "Riwayat Training"
                        // Format: "Final Checker:2024-05-10,2025-05-15;Jouho Board:2024-01-15"
                        const rawHistory = (
                            row['Riwayat Training'] || 
                            row['riwayat training'] || 
                            row['riwayat_training'] || 
                            row['History'] || 
                            ''
                        ).toString().trim();
                        
                        const trainingHistoryMap = {};
                        if (rawHistory) {
                            // Pisah per sertifikasi (separator: titik-koma)
                            rawHistory.split(';').forEach(entry => {
                                const colonIdx = entry.indexOf(':');
                                if (colonIdx > 0) {
                                    const certName = entry.substring(0, colonIdx).trim();
                                    const dates = entry.substring(colonIdx + 1)
                                        .split(',')
                                        .map(d => d.trim())
                                        .filter(d => d && /^\d{4}-\d{2}-\d{2}$/.test(d)); // validasi format YYYY-MM-DD
                                    if (certName && dates.length > 0) {
                                        trainingHistoryMap[certName] = dates;
                                    }
                                }
                            });
                        }

                        // Gabungkan sertifikasi dengan history-nya
                        const certs = certNames.map(certName => ({
                            name: certName,
                            history: trainingHistoryMap[certName] || []
                        }));

                        if (id && name) {
                            const existingIndex = employees.findIndex(emp => emp.id === id);
                            if (existingIndex >= 0) {
                                // Update: merge sertifikasi, pertahankan history lama jika tidak ada di file
                                const mergedCerts = certs.map(newCert => {
                                    const oldCert = employees[existingIndex].certs.find(c => c.name === newCert.name);
                                    // Jika file import punya history → pakai dari file
                                    // Jika tidak punya history di file → pertahankan history lama
                                    const finalHistory = newCert.history.length > 0
                                        ? newCert.history
                                        : (oldCert ? oldCert.history : []);
                                    return { name: newCert.name, history: finalHistory };
                                });
                                employees[existingIndex] = { id, name, department: dept, certs: mergedCerts };
                            } else {
                                employees.push({ id, name, department: dept, certs });
                            }
                            countAdded++;
                        }
                    });

                    saveData();
                    renderTable();
                    renderDashboard();
                    
                    if(countAdded > 0) {
                        showToast(`Berhasil mengimpor ${countAdded} data karyawan.`);
                    } else {
                        alert("Format Excel tidak sesuai.\n\nPastikan ada kolom: 'ID', 'Nama', 'Departemen', 'Sertifikasi'\nOpsional: 'Riwayat Training'\n\nKlik tombol 'Template Excel' untuk format yang benar.");
                    }
                    
                } catch (error) {
                    console.error("Gagal membaca Excel:", error);
                    alert("Terjadi kesalahan saat memproses file Excel.\n" + error.message);
                }
            };
            reader.readAsArrayBuffer(file);
            event.target.value = '';
        }

        // ============================================================
        // [FIX 2] Download Template Excel dengan kolom Riwayat Training
        // ============================================================
        function downloadExcelTemplate() {
            const wb = XLSX.utils.book_new();
            
            const sampleData = [
                {
                    'ID': 'EMP001',
                    'Nama': 'Budi Santoso',
                    'Departemen': 'Quality Control',
                    'Sertifikasi': 'Final Checker, Shipping Approval',
                    'Riwayat Training': 'Final Checker:2024-05-10,2025-05-15;Shipping Approval:2023-01-01'
                },
                {
                    'ID': 'EMP002',
                    'Nama': 'Siti Aminah',
                    'Departemen': 'Produksi',
                    'Sertifikasi': 'Jouho Board, Pemeriksa Dalam Proses',
                    'Riwayat Training': 'Jouho Board:2024-01-15'
                },
                {
                    'ID': 'EMP003',
                    'Nama': 'Contoh Tanpa Training',
                    'Departemen': 'Warehouse',
                    'Sertifikasi': 'Pemeriksa Produk',
                    'Riwayat Training': ''
                }
            ];

            const ws = XLSX.utils.json_to_sheet(sampleData);

            // Set lebar kolom
            ws['!cols'] = [
                { wch: 10 },  // ID
                { wch: 25 },  // Nama
                { wch: 22 },  // Departemen
                { wch: 45 },  // Sertifikasi
                { wch: 65 },  // Riwayat Training
            ];

            // Tambah sheet "Panduan" untuk penjelasan format
            const guideData = [
                { 'KOLOM': 'ID', 'FORMAT': 'Teks bebas (contoh: EMP001)', 'WAJIB': 'Ya' },
                { 'KOLOM': 'Nama', 'FORMAT': 'Nama lengkap karyawan', 'WAJIB': 'Ya' },
                { 'KOLOM': 'Departemen', 'FORMAT': 'Nama departemen/divisi', 'WAJIB': 'Ya' },
                { 'KOLOM': 'Sertifikasi', 'FORMAT': 'Nama sertifikasi dipisah koma (contoh: Final Checker, Jouho Board)', 'WAJIB': 'Tidak' },
                { 'KOLOM': 'Riwayat Training', 'FORMAT': 'NamaSertif:YYYY-MM-DD,YYYY-MM-DD;NamaSertif2:YYYY-MM-DD (pisah sertifikasi dengan titik-koma)', 'WAJIB': 'Tidak' },
            ];
            const wsGuide = XLSX.utils.json_to_sheet(guideData);
            wsGuide['!cols'] = [{ wch: 20 }, { wch: 75 }, { wch: 8 }];

            XLSX.utils.book_append_sheet(wb, ws, 'Data Karyawan');
            XLSX.utils.book_append_sheet(wb, wsGuide, 'Panduan Format');
            XLSX.writeFile(wb, 'Template_Import_CertiTrack.xlsx');
            showToast('Template Excel berhasil diunduh.');
        }

        // ============================================================
        // [FIX 1] QR Code & ID Card — embed data profil dalam URL
        // ============================================================
        let qrCodeObj = null;

        function openIDCard(empId) {
            const emp = employees.find(e => e.id === empId);
            if(!emp) return;

            document.getElementById('idcard-name').textContent = emp.name;
            document.getElementById('idcard-dept').textContent = emp.department;
            document.getElementById('idcard-id').textContent = emp.id;

            const qrContainer = document.getElementById('idcard-qr');
            qrContainer.innerHTML = '';
            
            const currentUrl = window.location.origin + window.location.pathname;
            
            // [FIX 1] Embed seluruh data profil (id, nama, dept, certs+history)
            // ke dalam URL sebagai base64 → siapapun yang scan TIDAK butuh login
            const profileEncoded = encodeProfileForQR(emp);
            const qrValue = `${currentUrl}?profile=${profileEncoded}`;
            
            qrCodeObj = new QRCode(qrContainer, {
                text: qrValue,
                width: 120,
                height: 120,
                colorDark: "#1e293b",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.M  // M (vs H sebelumnya) = kapasitas lebih besar untuk data embed
            });

            openModal('modal-idcard');
        }

        // ============================================================
        // [FIX 1] showProfile — menerima ID string ATAU objek data profil
        // Saat dipanggil dari QR scan, langsung pakai data dari URL
        // ============================================================
        function showProfile(empIdOrData) {
            let emp;
            
            // [FIX 1] Cek apakah dipanggil dengan objek profil (dari QR URL)
            // atau dengan string ID (dari klik tombol / input manual)
            if (typeof empIdOrData === 'object' && empIdOrData !== null) {
                // Data langsung dari QR URL — tidak perlu cari di localStorage
                emp = empIdOrData;
            } else {
                // Cari di data lokal (admin mode)
                emp = employees.find(e => e.id === empIdOrData.toString().toUpperCase());
            }
            
            if(!emp) {
                showToast(`Karyawan dengan ID "${empIdOrData}" tidak ditemukan.`);
                return;
            }

            document.getElementById('profile-name').textContent = emp.name;
            document.getElementById('profile-id-dept').textContent = `${emp.id} • ${emp.department}`;

            const certsContainer = document.getElementById('profile-certs');
            const noCertMsg = document.getElementById('profile-no-cert');
            certsContainer.innerHTML = '';

            if (emp.certs && emp.certs.length > 0) {
                noCertMsg.classList.add('hidden');
                const now = new Date();
                
                emp.certs.forEach(certObj => {
                    const cert = certObj.name;
                    const history = certObj.history || [];
                    let statusHtml = '';
                    let historyHtml = '<p class="text-xs text-slate-400 mt-2 italic flex items-center gap-1"><i data-lucide="info" class="w-3 h-3"></i> Belum ada riwayat training</p>';
                    
                    if (history.length > 0) {
                        const sortedHistory = [...history].sort((a, b) => new Date(b) - new Date(a));
                        const latestDate = new Date(sortedHistory[0]);
                        const diffDays = Math.ceil(Math.abs(now - latestDate) / (1000 * 60 * 60 * 24));
                        
                        if (diffDays > 365) {
                            statusHtml = `<span class="text-[10px] uppercase font-bold px-2.5 py-1 rounded bg-red-100 text-red-700 border border-red-200 flex items-center gap-1"><i data-lucide="alert-triangle" class="w-3 h-3"></i> Perlu Retraining</span>`;
                        } else {
                            statusHtml = `<span class="text-[10px] uppercase font-bold px-2.5 py-1 rounded bg-emerald-100 text-emerald-700 border border-emerald-200 flex items-center gap-1"><i data-lucide="check" class="w-3 h-3"></i> Aktif</span>`;
                        }
                        
                        historyHtml = `
                            <div class="mt-3 text-xs text-slate-600 bg-white p-3 rounded-lg border border-slate-100 shadow-sm">
                                <span class="font-bold block mb-1.5 text-slate-800">Riwayat Retraining:</span>
                                <ul class="list-none space-y-1">
                                    ${sortedHistory.map((d, i) => `
                                        <li class="flex items-center gap-2">
                                            <div class="w-1.5 h-1.5 rounded-full ${i===0 ? 'bg-blue-500' : 'bg-slate-300'}"></div>
                                            ${new Date(d).toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'})} 
                                            ${i === 0 ? '<span class="text-blue-600 text-[10px] ml-1 font-semibold bg-blue-50 px-1.5 py-0.5 rounded">(Terbaru)</span>' : ''}
                                        </li>
                                    `).join('')}
                                </ul>
                            </div>
                        `;
                    } else {
                        statusHtml = `<span class="text-[10px] uppercase font-bold px-2.5 py-1 rounded bg-amber-100 text-amber-700 border border-amber-200">Belum Training</span>`;
                    }

                    const isTarget = TARGET_CERTS.includes(cert);
                    const iconColor = isTarget ? 'text-blue-600' : 'text-slate-400';
                    const bgCertClass = isTarget ? 'bg-white border-blue-100 shadow-sm' : 'bg-slate-50 border-slate-200';
                    
                    certsContainer.insertAdjacentHTML('beforeend', `
                        <li class="p-4 border rounded-xl ${bgCertClass}">
                            <div class="flex items-start justify-between mb-1">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center shrink-0">
                                        <i data-lucide="check-circle-2" class="w-5 h-5 ${iconColor}"></i>
                                    </div>
                                    <span class="font-bold text-slate-800">${cert}</span>
                                </div>
                                ${statusHtml}
                            </div>
                            ${historyHtml}
                        </li>
                    `);
                });
            } else {
                noCertMsg.classList.remove('hidden');
            }

            lucide.createIcons();
            openModal('modal-profile');
        }

        // ============================================================
        // [FIX 1] Scanner — parse ?profile= dari URL hasil scan
        // ============================================================
        function startScanner() {
            if (html5QrcodeScanner) return;
            html5QrcodeScanner = new Html5Qrcode("reader");
            const config = { fps: 10, qrbox: { width: 250, height: 250 } };

            html5QrcodeScanner.start(
                { facingMode: "environment" },
                config,
                (decodedText) => {
                    let profileOrId = decodedText;
                    
                    try {
                        const parsedUrl = new URL(decodedText);
                        
                        // [FIX 1] Cek ?profile= (QR baru dengan data embed — tidak butuh login)
                        const profileParam = parsedUrl.searchParams.get('profile');
                        if (profileParam) {
                            profileOrId = decodeProfileFromQR(profileParam);
                        } else {
                            // Fallback: ?id= (QR lama)
                            const idParam = parsedUrl.searchParams.get('id');
                            if (idParam) profileOrId = idParam;
                        }
                    } catch (e) {
                        // Bukan URL — anggap langsung sebagai ID
                    }
                    
                    if (navigator.vibrate) navigator.vibrate(200);
                    
                    html5QrcodeScanner.stop().then(() => {
                        html5QrcodeScanner = null;
                        showProfile(profileOrId);
                    }).catch(err => console.log(err));
                },
                () => {}
            ).catch((err) => {
                console.error("Gagal memulai kamera:", err);
                document.getElementById('reader').innerHTML = `
                    <div class="p-8 text-center text-red-500">
                        <i data-lucide="camera-off" class="w-12 h-12 mx-auto mb-3 opacity-50"></i>
                        <p class="text-base font-bold">Kamera tidak dapat diakses.</p>
                        <p class="text-sm mt-1 text-slate-500">Mungkin terblokir browser/Iframe. Gunakan Input Manual di bawah.</p>
                    </div>`;
                lucide.createIcons();
            });
        }

        function stopScanner() {
            if (html5QrcodeScanner) {
                html5QrcodeScanner.stop().then(() => {
                    html5QrcodeScanner = null;
                }).catch(err => console.log(err));
            }
        }

        function simulateScan() {
            const inputVal = document.getElementById('manual-scan-id').value.trim();
            if(inputVal) {
                showProfile(inputVal);
                document.getElementById('manual-scan-id').value = '';
            } else {
                showToast("Masukkan ID terlebih dahulu.");
            }
        }

        // ============================================================
        // Kelola Riwayat Training
        // ============================================================
        let currentTrainingEmpId = null;

        function openTrainingModal(empId) {
            const emp = employees.find(e => e.id === empId);
            if (!emp) return;
            currentTrainingEmpId = emp.id;
            document.getElementById('training-emp-name').textContent = `${emp.name} - ${emp.department}`;
            renderTrainingContent();
            openModal('modal-training');
        }

        function renderTrainingContent() {
            const emp = employees.find(e => e.id === currentTrainingEmpId);
            const container = document.getElementById('training-content');
            container.innerHTML = '';

            if (!emp.certs || emp.certs.length === 0) {
                container.innerHTML = `<div class="text-center py-10 text-slate-500"><i data-lucide="alert-circle" class="w-10 h-10 mx-auto mb-3 opacity-50"></i><p class="font-medium">Karyawan ini belum memiliki sertifikasi.</p></div>`;
                lucide.createIcons();
                return;
            }

            emp.certs.forEach((certObj, certIndex) => {
                const sortedHistory = [...(certObj.history || [])].sort((a, b) => new Date(b) - new Date(a));
                
                let historyHtml = '';
                if (sortedHistory.length > 0) {
                    historyHtml = sortedHistory.map((dateStr, dateIndex) => `
                        <div class="flex items-center justify-between py-2 px-3 bg-slate-50 border border-slate-200 rounded-lg text-sm mb-2 group transition hover:bg-white hover:border-slate-300 shadow-sm">
                            <span class="font-medium text-slate-700 flex items-center gap-2">
                                <i data-lucide="calendar-check" class="w-4 h-4 text-blue-500"></i> 
                                ${new Date(dateStr).toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'})}
                            </span>
                            <button onclick="deleteTrainingDate('${emp.id}', '${certObj.name}', ${dateIndex})" class="text-slate-400 hover:text-red-600 bg-white group-hover:bg-red-50 p-1.5 rounded-md transition border border-transparent group-hover:border-red-100">
                                <i data-lucide="trash" class="w-4 h-4"></i>
                            </button>
                        </div>
                    `).join('');
                } else {
                    historyHtml = `<p class="text-sm text-slate-400 italic py-2 flex items-center gap-1"><i data-lucide="info" class="w-4 h-4"></i> Belum ada riwayat tercatat.</p>`;
                }

                container.insertAdjacentHTML('beforeend', `
                    <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 mb-5">
                        <h4 class="font-bold text-lg text-slate-800 border-b border-slate-100 pb-3 mb-4 flex items-center gap-2">
                            <i data-lucide="award" class="w-5 h-5 text-amber-500"></i> ${certObj.name}
                        </h4>
                        <div class="mb-5 pl-1">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Riwayat Training</label>
                            ${historyHtml}
                        </div>
                        <div class="flex gap-2 items-center bg-blue-50/50 p-3 rounded-xl border border-blue-100">
                            <input type="date" id="date-${certIndex}" class="flex-1 px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none font-medium">
                            <button onclick="addTrainingDate('${emp.id}', '${certObj.name}', 'date-${certIndex}')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-bold transition flex items-center gap-2 shadow-sm">
                                <i data-lucide="plus" class="w-4 h-4"></i> Tambah
                            </button>
                        </div>
                    </div>
                `);
            });
            lucide.createIcons();
        }

        function addTrainingDate(empId, certName, inputId) {
            const dateInput = document.getElementById(inputId);
            const dateVal = dateInput.value;
            if (!dateVal) { showToast("Silakan pilih tanggal terlebih dahulu."); return; }
            const emp = employees.find(e => e.id === empId);
            const cert = emp.certs.find(c => c.name === certName);
            if (!cert.history) cert.history = [];
            if (!cert.history.includes(dateVal)) {
                cert.history.push(dateVal);
                saveData();
                renderTrainingContent();
                showToast("Riwayat training berhasil ditambahkan.");
            } else {
                showToast("Tanggal training tersebut sudah tercatat.");
            }
        }

        function deleteTrainingDate(empId, certName, sortedIndex) {
            if (!confirm("Hapus riwayat training ini?")) return;
            const emp = employees.find(e => e.id === empId);
            const cert = emp.certs.find(c => c.name === certName);
            const sortedHistory = [...cert.history].sort((a, b) => new Date(b) - new Date(a));
            const dateToDelete = sortedHistory[sortedIndex];
            cert.history = cert.history.filter(d => d !== dateToDelete);
            saveData();
            renderTrainingContent();
            showToast("Riwayat berhasil dihapus.");
        }

        // ============================================================
        // Utility: Modal & Toast
        // ============================================================
        function openModal(id) {
            const modal = document.getElementById(id);
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                modal.firstElementChild.classList.remove('scale-95');
                modal.firstElementChild.classList.add('scale-100');
            }, 10);
        }

        function closeModal(id) {
            const modal = document.getElementById(id);
            modal.classList.add('opacity-0');
            modal.firstElementChild.classList.remove('scale-100');
            modal.firstElementChild.classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
                if(id === 'modal-form') document.getElementById('employee-form').reset();
                if(id === 'modal-profile' && !document.getElementById('tab-scanner').classList.contains('hidden')) {
                    startScanner();
                }
            }, 300);
        }

        function showToast(message) {
            const toast = document.getElementById('toast');
            document.getElementById('toast-msg').textContent = message;
            toast.classList.remove('translate-y-20', 'opacity-0');
            setTimeout(() => {
                toast.classList.add('translate-y-20', 'opacity-0');
            }, 3000);
        }
    </script>
</body>
</html>
