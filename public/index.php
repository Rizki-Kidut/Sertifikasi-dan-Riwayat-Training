<?php
/* =========================================================
SKELETON BACKEND PHP
Kode PHP ini disiapkan jika Anda memindahkan aplikasi ini 
ke server lokal (XAMPP/MAMP) atau hosting sungguhan.
=========================================================
*/

session_start();

// Simulasi Koneksi Database (MySQL)
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
        /* Mengatur base font menjadi 12px (75% dari default 16px) untuk mengecilkan skala aplikasi */
        html { font-size: 12px; }
        
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
        
        /* Print Styles ID Card Landscape */
        @media print {
            body * { visibility: hidden; }
            
            /* Paksa browser mencetak warna background */
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            #modal-idcard, #print-area, #print-area * { visibility: visible; }
            
            /* Reset struktur modal agar tidak ada yang memotong (clipping) area print */
            #modal-idcard {
                position: absolute !important;
                left: 0 !important;
                top: 0 !important;
                width: 100% !important;
                height: 100% !important;
                background: transparent !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            /* Hapus sifat transform dan overflow-hidden dari parent modal yang jadi penyebab gambar terpotong */
            #modal-idcard * {
                overflow: visible !important;
                transform: none !important;
            }

            @page { size: landscape; margin: 0; }
            
            #print-area { 
                position: fixed !important; 
                left: 50% !important; 
                top: 50% !important; 
                /* Mengembalikan transform secara spesifik HANYA untuk print-area agar posisinya tepat di tengah kertas */
                transform: translate(-50%, -50%) !important; 
                width: 450px !important; 
                height: 280px !important;
                border: none !important;
                box-shadow: none !important;
                margin: 0 !important;
                padding: 0 !important;
            }
        }
        .img-placeholder {
            background-color: #e2e8f0;
            display: flex; align-items: center; justify-content: center; color: #94a3b8;
        }
    </style>
</head>
<body class="text-slate-800 overflow-hidden">

    <div class="flex h-screen bg-slate-50 w-full">
        
        <!-- SIDEBAR -->
        <aside id="sidebar" class="w-64 bg-blue-800 text-white flex flex-col transition-transform duration-300 z-50 fixed md:relative h-full transform -translate-x-full md:translate-x-0 shadow-xl md:shadow-none shrink-0">
            <div class="h-16 flex items-center justify-between px-6 bg-blue-900 border-b border-blue-700/50">
                <div class="flex items-center gap-3">
                    <i data-lucide="shield-check" class="w-7 h-7 text-blue-300"></i>
                    <span class="font-bold text-xl tracking-tight">CertiTrack</span>
                </div>
                <button onclick="toggleSidebar()" class="md:hidden text-blue-300 hover:text-white">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>
            <nav class="flex-1 py-6 px-3 space-y-2 overflow-y-auto custom-scrollbar">
                <button onclick="switchTab('dashboard')" id="nav-dashboard" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg bg-blue-700 text-white font-medium transition">
                    <i data-lucide="layout-dashboard" class="w-5 h-5"></i> Dashboard
                </button>
                <button onclick="switchTab('data')" id="nav-data" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg text-blue-200 hover:bg-blue-700 hover:text-white font-medium transition">
                    <i data-lucide="users" class="w-5 h-5"></i> Data Karyawan
                </button>
                <div class="text-[10px] font-bold text-blue-400 uppercase tracking-wider px-4 mt-4 mb-2">Manajemen & Workflow</div>
                <button onclick="switchTab('pengajuan')" id="nav-pengajuan" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg text-blue-200 hover:bg-blue-700 hover:text-white font-medium transition">
                    <i data-lucide="file-plus" class="w-5 h-5"></i> Pengajuan Sertif
                </button>
                <button onclick="switchTab('penghapusan')" id="nav-penghapusan" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg text-blue-200 hover:bg-blue-700 hover:text-white font-medium transition">
                    <i data-lucide="file-minus" class="w-5 h-5"></i> Pengajuan Hapus
                </button>
                <button onclick="switchTab('approval')" id="nav-approval" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg text-amber-200 hover:bg-amber-600 hover:text-white font-medium transition">
                    <i data-lucide="check-square" class="w-5 h-5"></i> Approval QAS
                </button>
                <div class="text-[10px] font-bold text-blue-400 uppercase tracking-wider px-4 mt-4 mb-2">Utilitas</div>
                <button onclick="switchTab('history')" id="nav-history" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg text-blue-200 hover:bg-blue-700 hover:text-white font-medium transition">
                    <i data-lucide="history" class="w-5 h-5"></i> History Sertifikasi
                </button>
                <button onclick="switchTab('scanner')" id="nav-scanner" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg text-blue-200 hover:bg-blue-700 hover:text-white font-medium transition">
                    <i data-lucide="scan-line" class="w-5 h-5"></i> Scanner QR
                </button>
                <div class="text-[10px] font-bold text-blue-400 uppercase tracking-wider px-4 mt-4 mb-2">Admin</div>
                <button onclick="switchTab('admin-history')" id="nav-admin-history" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg text-blue-200 hover:bg-blue-700 hover:text-white font-medium transition">
                    <i data-lucide="database" class="w-5 h-5"></i> Tambah History
                </button>
            </nav>
            <div class="p-4 bg-blue-900/50 border-t border-blue-700/50">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center">
                        <i data-lucide="user" class="w-4 h-4 text-blue-200"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium">Administrator</p>
                        <p class="text-xs text-blue-300">HR / QAS Dept</p>
                    </div>
                </div>
            </div>
        </aside>

        <div id="sidebar-overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-slate-900/50 z-40 hidden md:hidden transition-opacity opacity-0"></div>

        <div class="flex-1 flex flex-col h-full overflow-hidden">
            
            <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-4 md:hidden shadow-sm z-30 shrink-0">
                <div class="flex items-center gap-3">
                    <i data-lucide="shield-check" class="w-6 h-6 text-blue-600"></i>
                    <span class="font-bold text-lg text-slate-800 tracking-tight">CertiTrack</span>
                </div>
                <button onclick="toggleSidebar()" class="p-2 text-slate-500 hover:text-blue-600 hover:bg-slate-100 rounded-lg transition">
                    <i data-lucide="menu" class="w-6 h-6"></i>
                </button>
            </header>

            <main class="flex-1 overflow-hidden p-4 md:p-8 w-full bg-slate-50 flex flex-col relative">
                
                <!-- TAB: DASHBOARD -->
                <section id="tab-dashboard" class="fade-in max-w-7xl mx-auto w-full flex flex-col h-full overflow-hidden">
                    <div class="mb-6 shrink-0">
                        <h1 class="text-2xl md:text-3xl font-bold text-slate-800">Dashboard Sistem</h1>
                        <p class="text-slate-500 mt-1">Ringkasan status sertifikasi aktif, expired, dan alert perpanjangan.</p>
                    </div>

                    <!-- Container 1: Tabel Alert Statis -->
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mb-8 shrink-0 flex flex-col max-h-[50%]">
                        <div class="bg-red-50 px-6 py-4 border-b border-red-100 flex items-center gap-2 shrink-0">
                            <i data-lucide="bell-ring" class="w-5 h-5 text-red-600"></i>
                            <h3 class="font-bold text-red-800">Alert: Sertifikasi Expired & Mendekati Expired (30 Hari)</h3>
                        </div>
                        <div class="overflow-x-auto overflow-y-auto custom-scrollbar flex-1">
                            <table class="w-full text-left border-collapse relative">
                                <thead class="sticky top-0 z-10">
                                    <tr class="bg-slate-50 text-slate-600 border-b border-slate-200 text-sm shadow-sm">
                                        <th class="py-3 px-6 font-semibold w-32">Nomor Induk</th>
                                        <th class="py-3 px-6 font-semibold">Nama</th>
                                        <th class="py-3 px-6 font-semibold">Sertifikasi</th>
                                        <th class="py-3 px-6 font-semibold w-48">Masa Berlaku</th>
                                        <th class="py-3 px-6 font-semibold w-40">Status</th>
                                    </tr>
                                </thead>
                                <tbody id="dash-alert-table" class="text-sm divide-y divide-slate-100">
                                </tbody>
                            </table>
                        </div>
                        <!-- Container Pagination Alert -->
                        <div id="dash-alert-pagination" class="px-6 py-3 border-t border-slate-100 bg-slate-50 flex justify-between items-center hidden shrink-0">
                        </div>
                    </div>
                    
                    <div class="mb-4 shrink-0 flex items-center gap-2">
                        <i data-lucide="bar-chart-2" class="w-5 h-5 text-blue-600"></i>
                        <h3 class="font-bold text-lg text-slate-800">Statistik per Sertifikasi</h3>
                    </div>

                    <!-- Container 2: Kartu Sertifikasi Vertical Scroll -->
                    <div id="dashboard-certs-container" class="flex-1 overflow-y-auto custom-scrollbar flex flex-col gap-6 pb-8 pr-2 min-h-0">
                    </div>
                </section>

                <!-- TAB: DATA KARYAWAN -->
                <section id="tab-data" class="hidden fade-in max-w-7xl mx-auto w-full flex flex-col h-full overflow-hidden">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4 shrink-0">
                        <div>
                            <h1 class="text-2xl md:text-3xl font-bold text-slate-800">Data Karyawan</h1>
                            <p class="text-slate-500 mt-1">Kelola data kompetensi, pelatihan, dan masa berlaku sertifikasi.</p>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <button onclick="downloadExcelTemplate()" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2.5 rounded-lg text-sm font-medium transition flex items-center gap-2 shadow-sm border border-slate-200">
                                <i data-lucide="file-down" class="w-4 h-4"></i> Template Excel
                            </button>
                            <label class="cursor-pointer bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2.5 rounded-lg text-sm font-medium transition flex items-center gap-2 shadow-sm">
                                <i data-lucide="file-spreadsheet" class="w-4 h-4"></i> Import Excel
                                <input type="file" id="excel-upload" class="hidden" accept=".xlsx, .xls, .csv" onchange="handleExcelImport(event)">
                            </label>
                            <button onclick="openModalForm()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-lg text-sm font-medium transition flex items-center gap-2 shadow-sm">
                                <i data-lucide="user-plus" class="w-4 h-4"></i> Tambah Data
                            </button>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden flex-1 flex flex-col min-h-0 relative">
                        <div class="overflow-x-auto overflow-y-auto custom-scrollbar flex-1">
                            <table class="w-full text-left border-collapse min-w-[1000px] relative">
                                <thead class="sticky top-0 z-10">
                                    <tr class="bg-slate-50 text-slate-600 border-b border-slate-200 text-sm shadow-sm">
                                        <th class="py-4 px-6 font-semibold w-24">ID/NIK</th>
                                        <th class="py-4 px-6 font-semibold">Nama Lengkap</th>
                                        <th class="py-4 px-6 font-semibold text-center w-32">Tipe Karyawan</th>
                                        <th class="py-4 px-6 font-semibold text-center w-32">Kompetensi</th>
                                        <th class="py-4 px-6 font-semibold text-center w-32">Sertifikasi</th>
                                        <th class="py-4 px-6 font-semibold text-center w-48">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="employee-table-body" class="text-sm divide-y divide-slate-100"></tbody>
                            </table>
                        </div>
                        <div id="empty-state" class="hidden text-center py-16 absolute inset-0 flex flex-col items-center justify-center bg-white z-0 pointer-events-none mt-12">
                            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-slate-100">
                                <i data-lucide="users" class="w-10 h-10 text-slate-300"></i>
                            </div>
                            <p class="text-slate-600 font-medium text-lg">Belum ada data karyawan.</p>
                        </div>
                    </div>
                </section>

                <!-- TAB: PENGAJUAN SERTIFIKASI -->
                <section id="tab-pengajuan" class="hidden fade-in max-w-7xl mx-auto w-full flex flex-col h-full overflow-y-auto custom-scrollbar pb-8 pr-2">
                    <div class="mb-6 shrink-0 flex justify-between items-end">
                        <div>
                            <h1 class="text-2xl md:text-3xl font-bold text-slate-800">Pengajuan Sertifikasi</h1>
                            <p class="text-slate-500 mt-1">Buat draft pengajuan training dan sertifikasi baru untuk diteruskan ke QAS.</p>
                        </div>
                        <div class="flex gap-2">
                            <button onclick="downloadExcelPengajuan()" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2.5 rounded-lg text-sm font-medium transition flex items-center gap-2 shadow-sm border border-slate-200">
                                <i data-lucide="file-down" class="w-4 h-4"></i> Template Draft
                            </button>
                            <label class="cursor-pointer bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2.5 rounded-lg text-sm font-medium transition flex items-center gap-2 shadow-sm">
                                <i data-lucide="file-spreadsheet" class="w-4 h-4"></i> Import Draft
                                <input type="file" class="hidden" accept=".xlsx, .xls, .csv" onchange="handleExcelPengajuan(event)">
                            </label>
                        </div>
                    </div>

                    <!-- Form Pengajuan Manual -->
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 mb-6 shrink-0">
                        <h3 class="font-bold text-slate-800 mb-4 border-b pb-2">Form Tambah Draft Pengajuan Baru</h3>
                        <form id="form-pengajuan" onsubmit="addDraftPengajuan(event)">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-600 mb-1">NIK</label>
                                    <input type="text" id="peng-nik" required placeholder="Masukkan NIK..." class="w-full px-3 py-2 border border-slate-300 rounded focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-600 mb-1">Nama Lengkap</label>
                                    <input type="text" id="peng-nama" required placeholder="Masukkan nama lengkap..." class="w-full px-3 py-2 border border-slate-300 rounded bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-600 mb-1">Sertifikasi</label>
                                    <select id="peng-cert" required class="w-full px-3 py-2 border border-slate-300 rounded focus:ring-2 focus:ring-blue-500 focus:outline-none" onchange="checkPengajuanCert()">
                                        <option value="">-- Pilih Sertifikasi --</option>
                                        <option value="Final Checker">Final Checker</option>
                                        <option value="Pemeriksa Produk">Pemeriksa Produk</option>
                                        <option value="Pemeriksa Dalam Proses">Pemeriksa Dalam Proses</option>
                                        <option value="Jouho Board">Jouho Board</option>
                                        <option value="Trainer Proses Penting">Trainer Proses Penting</option>
                                        <option value="Trainer Proses Khusus">Trainer Proses Khusus</option>
                                        <option value="Shipping Approval">Shipping Approval</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-600 mb-1">Tanggal Training</label>
                                    <input type="date" id="peng-date" required class="w-full px-3 py-2 border border-slate-300 rounded focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                </div>
                            </div>

                            <div class="bg-slate-50 p-4 rounded border border-slate-200 mb-4">
                                <label class="block text-xs font-bold text-slate-600 mb-3 uppercase tracking-wide">Upload Dokumen Bukti Training</label>
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                    <div>
                                        <label class="block text-[10px] text-slate-500 mb-1">1. Daftar Hadir</label>
                                        <input type="file" id="peng-doc-hadir" required class="w-full text-xs">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] text-slate-500 mb-1">2. Nilai Test Tulis</label>
                                        <input type="file" id="peng-doc-tulis" required class="w-full text-xs">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] text-slate-500 mb-1">3. Nilai Test Praktek</label>
                                        <input type="file" id="peng-doc-praktek" required class="w-full text-xs">
                                    </div>
                                    <div id="peng-doc-eye-container" class="hidden">
                                        <label class="block text-[10px] text-red-500 font-bold mb-1">4. Nilai Test Eye Check</label>
                                        <input type="file" id="peng-doc-eye" class="w-full text-xs">
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end">
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-bold transition shadow-sm flex items-center gap-2">
                                    <i data-lucide="plus-circle" class="w-4 h-4"></i> Tambah ke Draft
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Tabel Draft Pengajuan -->
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mb-6 flex-1 flex flex-col min-h-[300px]">
                        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50 shrink-0">
                            <h3 class="font-bold text-slate-800">Daftar Draft Pengajuan Baru (Belum Disubmit)</h3>
                        </div>
                        <div class="overflow-x-auto flex-1 overflow-y-auto custom-scrollbar">
                            <table class="w-full text-left border-collapse relative">
                                <thead class="sticky top-0 z-10">
                                    <tr class="bg-slate-100 text-slate-600 border-b border-slate-200 text-xs uppercase tracking-wide shadow-sm">
                                        <th class="py-3 px-4 w-20">NIK</th>
                                        <th class="py-3 px-4">Nama</th>
                                        <th class="py-3 px-4">Sertifikasi</th>
                                        <th class="py-3 px-4">Tgl Training</th>
                                        <th class="py-3 px-4 text-center">Dokumen</th>
                                        <th class="py-3 px-4 text-center w-24">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="draft-table-body" class="text-sm divide-y divide-slate-100">
                                </tbody>
                            </table>
                        </div>
                        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 flex justify-between items-center shrink-0">
                            <p class="text-xs text-slate-500" id="draft-count">Total Draft: 0</p>
                            <div class="flex gap-2">
                                <button onclick="clearDrafts()" class="px-4 py-2 text-slate-600 bg-white border border-slate-300 rounded hover:bg-slate-50 text-sm font-medium transition">Cancel / Hapus Draft</button>
                                <button onclick="submitDraftsToQAS()" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded text-sm font-bold transition shadow flex items-center gap-2">
                                    <i data-lucide="send" class="w-4 h-4"></i> Submit ke QAS
                                </button>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- TAB: PENGAJUAN PENGHAPUSAN -->
                <section id="tab-penghapusan" class="hidden fade-in max-w-7xl mx-auto w-full flex flex-col h-full overflow-y-auto custom-scrollbar pb-8 pr-2">
                    <div class="mb-6 shrink-0 flex justify-between items-end">
                        <div>
                            <h1 class="text-2xl md:text-3xl font-bold text-slate-800">Pengajuan Penghapusan</h1>
                            <p class="text-slate-500 mt-1">Buat draft pengajuan untuk mencabut sertifikasi/menghapus data karyawan ke QAS.</p>
                        </div>
                        <div class="flex gap-2">
                            <button onclick="downloadExcelHapus()" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2.5 rounded-lg text-sm font-medium transition flex items-center gap-2 shadow-sm border border-slate-200">
                                <i data-lucide="file-down" class="w-4 h-4"></i> Template Draft
                            </button>
                            <label class="cursor-pointer bg-red-600 hover:bg-red-700 text-white px-4 py-2.5 rounded-lg text-sm font-medium transition flex items-center gap-2 shadow-sm">
                                <i data-lucide="file-spreadsheet" class="w-4 h-4"></i> Import Draft
                                <input type="file" class="hidden" accept=".xlsx, .xls, .csv" onchange="handleExcelHapus(event)">
                            </label>
                        </div>
                    </div>

                    <!-- Form Pengajuan Penghapusan Manual -->
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 mb-6 shrink-0 border-t-4 border-t-red-500">
                        <h3 class="font-bold text-slate-800 mb-4 border-b pb-2">Form Tambah Draft Penghapusan</h3>
                        <form id="form-hapus" onsubmit="addDraftHapus(event)">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-600 mb-1">NIK</label>
                                    <datalist id="nik-list-hapus"></datalist>
                                    <input type="text" list="nik-list-hapus" id="hapus-nik" required placeholder="Cari NIK..." class="w-full px-3 py-2 border border-slate-300 rounded focus:ring-2 focus:ring-red-500 focus:outline-none uppercase" onchange="autoFillHapusName()">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-600 mb-1">Nama Lengkap</label>
                                    <input type="text" id="hapus-nama" required readonly class="w-full px-3 py-2 border border-slate-300 rounded bg-slate-100 focus:outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-600 mb-1">Reason Penghapusan</label>
                                    <input type="text" id="hapus-reason" required placeholder="Contoh: Resign, Mutasi, dsb." class="w-full px-3 py-2 border border-slate-300 rounded focus:ring-2 focus:ring-red-500 focus:outline-none">
                                </div>
                            </div>
                            <div class="flex justify-end">
                                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-bold transition shadow-sm flex items-center gap-2">
                                    <i data-lucide="plus-circle" class="w-4 h-4"></i> Tambah ke Draft
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Tabel Draft Penghapusan -->
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mb-6 flex-1 flex flex-col min-h-[300px]">
                        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50 shrink-0">
                            <h3 class="font-bold text-slate-800">Daftar Draft Penghapusan (Belum Disubmit)</h3>
                        </div>
                        <div class="overflow-x-auto flex-1 overflow-y-auto custom-scrollbar">
                            <table class="w-full text-left border-collapse relative">
                                <thead class="sticky top-0 z-10">
                                    <tr class="bg-slate-100 text-slate-600 border-b border-slate-200 text-xs uppercase tracking-wide shadow-sm">
                                        <th class="py-3 px-4 w-20">NIK</th>
                                        <th class="py-3 px-4">Nama</th>
                                        <th class="py-3 px-4">Alasan Penghapusan</th>
                                        <th class="py-3 px-4 text-center w-24">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="draft-hapus-table-body" class="text-sm divide-y divide-slate-100">
                                </tbody>
                            </table>
                        </div>
                        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 flex justify-between items-center shrink-0">
                            <p class="text-xs text-slate-500" id="draft-hapus-count">Total Draft: 0</p>
                            <div class="flex gap-2">
                                <button onclick="clearDraftsHapus()" class="px-4 py-2 text-slate-600 bg-white border border-slate-300 rounded hover:bg-slate-50 text-sm font-medium transition">Cancel / Hapus Draft</button>
                                <button onclick="submitDraftsHapusToQAS()" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded text-sm font-bold transition shadow flex items-center gap-2">
                                    <i data-lucide="send" class="w-4 h-4"></i> Submit ke QAS
                                </button>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- TAB: APPROVAL QAS -->
                <section id="tab-approval" class="hidden fade-in max-w-7xl mx-auto w-full flex flex-col h-full overflow-y-auto custom-scrollbar pb-8 pr-2">
                    <div class="mb-6 shrink-0">
                        <h1 class="text-2xl md:text-3xl font-bold text-slate-800">Approval QAS</h1>
                        <p class="text-slate-500 mt-1">Review, Setujui, atau Tolak pengajuan sertifikasi baru dan penghapusan data karyawan.</p>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mb-8">
                        <div class="px-6 py-4 border-b border-slate-100 bg-amber-50 flex justify-between items-center">
                            <div class="flex items-center gap-2 text-amber-800">
                                <i data-lucide="clock" class="w-5 h-5"></i>
                                <h3 class="font-bold">Menunggu Approval (Pending)</h3>
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-slate-50 text-slate-600 border-b border-slate-200 text-xs uppercase tracking-wide">
                                        <th class="py-3 px-4 w-20">NIK</th>
                                        <th class="py-3 px-4">Nama</th>
                                        <th class="py-3 px-4">Jenis Pengajuan</th>
                                        <th class="py-3 px-4">Detail Pengajuan</th>
                                        <th class="py-3 px-4 text-center">Dokumen</th>
                                        <th class="py-3 px-4 text-center w-48">Keputusan</th>
                                    </tr>
                                </thead>
                                <tbody id="approval-pending-table" class="text-sm divide-y divide-slate-100">
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- History Approval -->
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                        <div class="px-6 py-3 border-b border-slate-100 bg-slate-50">
                            <h3 class="font-bold text-slate-700 text-sm">Riwayat Keputusan QAS (Terbaru)</h3>
                        </div>
                        <div class="overflow-x-auto max-h-64">
                            <table class="w-full text-left border-collapse">
                                <tbody id="approval-history-table" class="text-sm divide-y divide-slate-100">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>

                <!-- TAB: HISTORY SERTIFIKASI (KARYAWAN TERHAPUS) -->
                <section id="tab-history" class="hidden fade-in max-w-7xl mx-auto w-full flex flex-col h-full overflow-hidden">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4 shrink-0">
                        <div>
                            <h1 class="text-2xl md:text-3xl font-bold text-slate-800">History Sertifikasi</h1>
                            <p class="text-slate-500 mt-1">Daftar arsip karyawan yang telah dihapus / dicabut sertifikasinya.</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <label class="text-sm font-bold text-slate-600">Cari NIK / Nama:</label>
                            <input type="text" id="search-history" placeholder="Ketik NIK/Nama..." onkeyup="renderHistoryTable()" class="px-3 py-2 border border-slate-300 rounded focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm">
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden flex-1 flex flex-col min-h-0 relative">
                        <div class="overflow-x-auto overflow-y-auto custom-scrollbar flex-1">
                            <table class="w-full text-left border-collapse relative">
                                <thead class="sticky top-0 z-10">
                                    <tr class="bg-slate-200 text-slate-700 border-b border-slate-300 text-sm shadow-sm">
                                        <th class="py-4 px-6 font-semibold w-24">NIK</th>
                                        <th class="py-4 px-6 font-semibold">Nama Lengkap</th>
                                        <th class="py-4 px-6 font-semibold">Departemen</th>
                                        <th class="py-4 px-6 font-semibold">Sertifikasi Terakhir</th>
                                        <th class="py-4 px-6 font-semibold">Tanggal Dihapus</th>
                                        <th class="py-4 px-6 font-semibold text-red-600">Alasan Penghapusan</th>
                                    </tr>
                                </thead>
                                <tbody id="history-table-body" class="text-sm divide-y divide-slate-100"></tbody>
                            </table>
                        </div>
                        <div id="history-empty-state" class="hidden text-center py-16 absolute inset-0 flex flex-col items-center justify-center bg-white z-0 pointer-events-none mt-12">
                            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-slate-100">
                                <i data-lucide="archive" class="w-8 h-8 text-slate-300"></i>
                            </div>
                            <p class="text-slate-600 font-medium">Belum ada riwayat penghapusan data.</p>
                        </div>
                    </div>
                </section>

                <!-- TAB: ADMIN - TAMBAH HISTORY -->
                <section id="tab-admin-history" class="hidden fade-in max-w-7xl mx-auto w-full flex flex-col h-full overflow-y-auto custom-scrollbar pb-8 pr-2">
                    <div class="mb-6 shrink-0">
                        <h1 class="text-2xl md:text-3xl font-bold text-slate-800">Tambah History Sertifikasi</h1>
                        <p class="text-slate-500 mt-1">Import data karyawan yang sudah terhapus atau keluar secara massal via Excel.</p>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-8 flex flex-col items-center justify-center text-center">
                        <div class="w-20 h-20 bg-blue-50 rounded-full flex items-center justify-center mb-6 border border-blue-100">
                            <i data-lucide="file-spreadsheet" class="w-10 h-10 text-blue-600"></i>
                        </div>
                        <h3 class="text-xl font-bold text-slate-800 mb-2">Import Data History</h3>
                        <p class="text-slate-500 max-w-md mb-8">Data history sertifikasi hanya dapat ditambahkan menggunakan format Excel yang telah ditentukan agar terstruktur dengan baik.</p>
                        
                        <div class="flex gap-4">
                            <button onclick="downloadExcelHistoryTemplate()" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-6 py-3 rounded-lg text-sm font-bold transition flex items-center gap-2 border border-slate-300 shadow-sm">
                                <i data-lucide="download" class="w-5 h-5"></i> Download Template
                            </button>
                            <label class="cursor-pointer bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg text-sm font-bold transition shadow-md flex items-center gap-2">
                                <i data-lucide="upload" class="w-5 h-5"></i> Import Excel
                                <input type="file" class="hidden" accept=".xlsx, .xls, .csv" onchange="handleExcelHistoryImport(event)">
                            </label>
                        </div>
                    </div>
                </section>

                <!-- TAB: SCANNER QR -->
                <section id="tab-scanner" class="hidden fade-in max-w-4xl mx-auto w-full flex flex-col h-full overflow-y-auto custom-scrollbar pb-8 pr-2">
                    <div class="text-center mb-8 shrink-0">
                        <h1 class="text-2xl md:text-3xl font-bold text-slate-800">Scanner QR Code</h1>
                        <p class="text-slate-500 mt-2">Scan QR Code pada ID Card untuk melihat profil terverifikasi.</p>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden p-6 md:p-10 mb-6">
                        <div id="reader" class="w-full max-w-md mx-auto rounded-xl overflow-hidden border-2 border-dashed border-blue-300 bg-slate-50 shadow-inner"></div>
                        
                        <div class="mt-8 text-center max-w-sm mx-auto">
                            <div class="flex items-center gap-4 mb-4">
                                <div class="h-px bg-slate-200 flex-1"></div>
                                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Atau Input Manual ID</p>
                                <div class="h-px bg-slate-200 flex-1"></div>
                            </div>
                            <div class="flex gap-2">
                                <input type="text" id="manual-scan-id" placeholder="Contoh: 80028" class="flex-1 px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm font-medium">
                                <button onclick="simulateScan()" class="bg-slate-800 hover:bg-slate-900 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm">Cek Profil</button>
                            </div>
                        </div>
                    </div>
                </section>

            </main>
        </div>
    </div>

    <!-- SEMUA MODALS -->
    <!-- Modal Form Tambah/Edit Karyawan -->
    <div id="modal-form" class="fixed inset-0 bg-slate-900/60 hidden z-[100] flex items-center justify-center p-4 opacity-0 transition-opacity backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-5xl max-h-[95vh] flex flex-col transform scale-95 transition-transform duration-300 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-white shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center">
                        <i data-lucide="file-edit" class="w-5 h-5 text-blue-600"></i>
                    </div>
                    <h3 class="font-bold text-lg text-slate-800" id="form-modal-title">Form Data Karyawan</h3>
                </div>
                <button onclick="closeModal('modal-form')" class="text-slate-400 hover:bg-slate-100 hover:text-red-500 p-2 rounded-lg transition"><i data-lucide="x"></i></button>
            </div>
            
            <div class="px-6 py-6 overflow-y-auto flex-1 bg-slate-50/50 custom-scrollbar">
                <form id="employee-form" onsubmit="saveEmployee(event)">
                    <input type="hidden" id="form-mode" value="add"> <!-- add / edit -->

                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                        
                        <!-- Kolom Kiri: Profil & Masa Kerja (Lebar: 4) -->
                        <div class="lg:col-span-4 space-y-6">
                            <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
                                <h4 class="text-sm font-bold text-slate-800 mb-4 uppercase tracking-wider border-b pb-2">Profil Karyawan</h4>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-600 mb-1">Tipe Karyawan</label>
                                        <select id="emp-type" onchange="toggleCertMode()" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm bg-blue-50 font-semibold text-blue-800">
                                            <option value="Direct">Direct Produksi</option>
                                            <option value="Semi-Direct">Semi-Direct</option>
                                            <option value="In-Direct">In-Direct</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-600 mb-1">ID / NIK</label>
                                        <input type="text" id="emp-id" required class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm uppercase bg-slate-50">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-600 mb-1">Nama Lengkap</label>
                                        <input type="text" id="emp-name" required class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm bg-slate-50">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-600 mb-1">Departemen / Seksi</label>
                                        <input type="text" id="emp-dept" required class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm bg-slate-50">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-600 mb-1">Upload Foto Profil</label>
                                        <input type="file" id="emp-photo-file" accept="image/*" class="w-full px-2 py-1.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm bg-slate-50 cursor-pointer" onchange="handlePhotoUpload(event)">
                                        <input type="hidden" id="emp-photo-url">
                                        <p id="photo-upload-status" class="text-[10px] text-slate-500 mt-1"></p>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
                                <h4 class="text-sm font-bold text-slate-800 mb-4 uppercase tracking-wider border-b pb-2">Masa Kerja & Kompetensi</h4>
                                <div class="space-y-4">
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-xs font-bold text-slate-600 mb-1">Tanggal Masuk</label>
                                            <input type="date" id="emp-join-date" class="w-full px-2 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none text-xs bg-slate-50">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-slate-600 mb-1">Tanggal Tetap</label>
                                            <input type="date" id="emp-permanent-date" class="w-full px-2 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none text-xs bg-slate-50">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-600 mb-1">Kompetensi (Dipisah koma)</label>
                                        <textarea id="emp-comps" rows="2" placeholder="Contoh: Mesin Injection A, Assembly Line" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none text-sm bg-slate-50 resize-none"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Kolom Kanan: Manajemen Sertifikasi (Lebar: 8) -->
                        <div class="lg:col-span-8 space-y-6">
                            
                            <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex flex-col">
                                <div class="flex justify-between items-end border-b pb-3 mb-4">
                                    <div>
                                        <h4 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Detail Sertifikasi</h4>
                                        <p class="text-xs text-slate-500 mt-1" id="cert-hint">Pilih sertifikasi yang dimiliki oleh karyawan Direct.</p>
                                    </div>
                                    <button type="button" id="btn-add-cert-row" onclick="addIndirectCertRow()" class="hidden bg-emerald-100 text-emerald-700 hover:bg-emerald-200 px-3 py-1.5 rounded text-xs font-bold transition flex items-center gap-1">
                                        <i data-lucide="plus-circle" class="w-3.5 h-3.5"></i> Tambah Baris
                                    </button>
                                </div>

                                <div id="cert-direct-area" class="space-y-3 flex-1 pr-2"></div>
                                <div id="cert-indirect-area" class="hidden space-y-3 flex-1 pr-2"></div>
                            </div>

                            <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex flex-col">
                                <div class="flex justify-between items-end border-b pb-3 mb-4">
                                    <div>
                                        <h4 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Riwayat Training</h4>
                                        <p class="text-xs text-slate-500 mt-1" id="train-hint">Pilih riwayat training untuk karyawan Direct.</p>
                                    </div>
                                    <button type="button" id="btn-add-train-row" onclick="addIndirectTrainRow()" class="hidden bg-blue-100 text-blue-700 hover:bg-blue-200 px-3 py-1.5 rounded text-xs font-bold transition flex items-center gap-1">
                                        <i data-lucide="plus-circle" class="w-3.5 h-3.5"></i> Tambah Baris
                                    </button>
                                </div>
                                <div id="train-direct-area" class="space-y-3 pr-2"></div>
                                <div id="train-indirect-area" class="hidden space-y-3 pr-2"></div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            
            <div class="px-6 py-4 border-t border-slate-100 bg-white flex justify-end gap-3 shrink-0">
                <button onclick="closeModal('modal-form')" class="px-5 py-2.5 text-slate-600 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 text-sm font-medium transition">Batal</button>
                <button type="submit" form="employee-form" class="px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium transition shadow-sm flex items-center gap-2">
                    <i data-lucide="save" class="w-4 h-4"></i> Simpan Data
                </button>
            </div>
        </div>
    </div>

    <!-- Modal ID Card & Profile -->
    <div id="modal-idcard" class="fixed inset-0 bg-slate-900/70 hidden z-[100] flex items-center justify-center p-4 opacity-0 transition-opacity backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-xl overflow-hidden transform scale-95 transition-transform duration-300">
            <div class="px-5 py-4 border-b border-slate-100 flex justify-between items-center bg-white">
                <h3 class="font-bold text-slate-800 flex items-center gap-2">
                    <i data-lucide="badge-check" class="w-5 h-5 text-blue-600"></i> ID Card Preview
                </h3>
                <button onclick="closeModal('modal-idcard')" class="text-slate-400 hover:text-red-500 transition bg-slate-50 hover:bg-red-50 p-1.5 rounded-lg"><i data-lucide="x"></i></button>
            </div>
            
            <div class="p-8 bg-slate-100 flex justify-center items-center overflow-auto">
                <div id="print-area" class="bg-white rounded-lg shadow-xl overflow-hidden relative border border-gray-200" style="width: 450px; height: 260px;">
                    <div class="h-[60px] w-full bg-slate-100 flex items-center px-4 border-b-4 border-slate-300/30">
                        <div class="flex items-center justify-between w-full">
                            <div class="flex items-center">
                                <span class="text-[#f15a24] font-black text-3xl tracking-tighter" style="font-family: Arial, sans-serif; transform: scaleY(1.1); text-shadow: 1px 1px 0px rgba(0,0,0,0.1);">STANLEY</span>
                            </div>
                            <span class="text-slate-600 font-bold text-[13px] tracking-wide" style="font-family: Arial, sans-serif;">PT INDONESIA STANLEY ELECTRIC</span>
                        </div>
                    </div>
                    <div class="flex h-[200px]">
                        <div class="w-[150px] pl-4 pt-4 pb-4">
                            <div class="w-full h-full bg-[#9e1b1b] relative overflow-hidden flex items-end justify-center rounded-sm border border-slate-200" id="idcard-photo-container">
                                <img id="idcard-photo" src="" alt="Foto" class="w-full h-full object-cover z-10" style="display:none;" onerror="this.style.display='none'; document.getElementById('idcard-silhouette').style.display='block';">
                                <i data-lucide="user" id="idcard-silhouette" class="w-24 h-24 text-white/50 absolute bottom-0"></i>
                            </div>
                        </div>
                        <div class="flex-1 flex flex-col justify-start pl-6 pr-4 pt-5">
                            <h4 id="idcard-name" class="font-bold text-[26px] text-slate-800 leading-none tracking-tight uppercase" style="font-family: 'Arial Narrow', Arial, sans-serif; word-wrap: break-word; line-height: 1.1;">NAMA</h4>
                            <p id="idcard-id" class="text-[24px] text-slate-800 font-bold mt-1 tracking-wider leading-none" style="font-family: 'Arial Narrow', Arial, sans-serif;">80028</p>
                            <div class="mt-3 flex justify-start">
                                <div id="idcard-qr" class="p-1 bg-white border border-slate-300 inline-block shadow-sm w-[100px] h-[100px] overflow-hidden flex items-center justify-center"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="px-6 py-4 border-t border-slate-100 bg-white flex justify-center gap-3">
                <button onclick="window.print()" class="w-full px-4 py-2.5 bg-slate-800 text-white rounded-lg hover:bg-slate-900 text-sm font-medium transition flex items-center justify-center gap-2 shadow-sm">
                    <i data-lucide="printer" class="w-4 h-4"></i> Cetak ID Card
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Profil Karyawan -->
    <div id="modal-profile" class="fixed inset-0 bg-slate-900/70 hidden z-[100] flex items-center justify-center p-4 opacity-0 transition-opacity backdrop-blur-sm">
        <div class="bg-slate-50 rounded-2xl shadow-2xl w-full max-w-4xl max-h-[95vh] overflow-hidden transform scale-95 transition-transform duration-300 flex flex-col">
            <div class="bg-blue-800 px-6 py-6 relative shrink-0">
                <button onclick="closeModal('modal-profile')" class="absolute top-4 right-4 text-blue-200 hover:text-white bg-black/10 hover:bg-black/20 p-2 rounded-full transition z-10"><i data-lucide="x" class="w-5 h-5"></i></button>
                <div class="flex flex-col sm:flex-row items-center sm:items-start gap-5">
                    <div class="w-28 h-32 bg-slate-200 rounded-lg border-2 border-white shadow-md overflow-hidden relative shrink-0 img-placeholder">
                        <img id="profile-photo" src="" alt="Foto" class="w-full h-full object-cover" style="display:none;" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                        <i data-lucide="user" class="w-16 h-16 text-slate-400 absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2"></i>
                    </div>
                    <div class="text-white text-center sm:text-left mt-2 sm:mt-0">
                        <div class="flex items-center justify-center sm:justify-start gap-2 mb-1">
                            <span id="profile-type" class="text-[10px] font-bold bg-amber-400 text-amber-900 px-2 py-0.5 rounded uppercase tracking-wider shadow-sm">DIRECT</span>
                            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-blue-900 bg-blue-100 px-2 py-0.5 rounded shadow-sm">
                                <i data-lucide="shield-check" class="w-3 h-3"></i> TERVERIFIKASI
                            </span>
                        </div>
                        <h3 id="profile-name" class="font-bold text-3xl mb-1">Nama Karyawan</h3>
                        <p id="profile-id-dept" class="text-blue-200 font-medium text-sm">ID • Departemen</p>
                    </div>
                </div>
            </div>
            <div class="p-6 overflow-y-auto flex-1 custom-scrollbar">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-start gap-3">
                        <div class="bg-emerald-50 p-2 rounded-lg text-emerald-600 shrink-0"><i data-lucide="calendar-arrow-down" class="w-5 h-5"></i></div>
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-0.5">Tanggal Masuk</p>
                            <p id="profile-join-date" class="font-bold text-slate-800 text-sm">-</p>
                        </div>
                    </div>
                    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-start gap-3">
                        <div class="bg-blue-50 p-2 rounded-lg text-blue-600 shrink-0"><i data-lucide="calendar-check" class="w-5 h-5"></i></div>
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-0.5">Tanggal Tetap</p>
                            <p id="profile-perm-date" class="font-bold text-slate-800 text-sm">-</p>
                        </div>
                    </div>
                    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-start gap-3 md:row-span-2">
                        <div class="bg-indigo-50 p-2 rounded-lg text-indigo-600 shrink-0"><i data-lucide="cpu" class="w-5 h-5"></i></div>
                        <div class="w-full">
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Daftar Kompetensi</p>
                            <ul id="profile-comps" class="space-y-1.5"></ul>
                            <p id="profile-no-comp" class="hidden text-xs text-slate-500 italic">Belum ada kompetensi.</p>
                        </div>
                    </div>
                </div>
                <div class="mb-6">
                    <h4 class="font-bold text-slate-800 border-b border-slate-200 pb-2 mb-4 flex items-center gap-2">
                        <i data-lucide="award" class="w-5 h-5 text-amber-500"></i> Detail Sertifikasi
                    </h4>
                    <div id="profile-certs" class="grid grid-cols-1 sm:grid-cols-2 gap-4"></div>
                    <p id="profile-no-cert" class="hidden text-sm text-slate-500 italic text-center py-6 bg-white border border-dashed rounded-xl">Belum ada sertifikasi yang tercatat.</p>
                </div>
                <div>
                    <h4 class="font-bold text-slate-800 border-b border-slate-200 pb-2 mb-4 flex items-center gap-2">
                        <i data-lucide="calendar-check" class="w-5 h-5 text-blue-500"></i> Riwayat Training
                    </h4>
                    <div id="profile-trains" class="grid grid-cols-1 sm:grid-cols-2 gap-4"></div>
                    <p id="profile-no-train" class="hidden text-sm text-slate-500 italic text-center py-6 bg-white border border-dashed rounded-xl">Belum ada riwayat training yang tercatat.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- UI Bantuan (Toast) -->
    <div id="toast" class="fixed bottom-5 right-5 bg-slate-800 text-white px-5 py-3.5 rounded-xl shadow-2xl transform translate-y-20 opacity-0 transition-all duration-300 z-[200] flex items-center gap-3 border border-slate-700">
        <div class="w-8 h-8 rounded-full bg-blue-500/20 flex items-center justify-center">
            <i data-lucide="info" class="w-4 h-4 text-blue-400"></i>
        </div>
        <span id="toast-msg" class="text-sm font-medium">Notifikasi</span>
    </div>

    <script>
        lucide.createIcons();

        // Konstanta Default
        const TARGET_CERTS = [
            "Final Checker", "Pemeriksa Produk", "Pemeriksa Dalam Proses", 
            "Jouho Board", "Trainer Proses Penting", "Trainer Proses Khusus", "Shipping Approval"
        ];
        const TARGET_TRAININGS = TARGET_CERTS; 

        // State Management
        let employees = [];
        let draftPengajuan = [];
        let draftPenghapusan = [];
        let submissionQAS = []; 
        let historyKaryawan = [];
        let currentAlertPage = 1; // Variabel Halaman untuk Tabel Alert
        let html5QrcodeScanner = null;
        let indirectCertRowCount = 0;
        let indirectTrainRowCount = 0;

        document.addEventListener('DOMContentLoaded', () => {
            loadData();
            buildDirectCertCheckboxes();
            buildDirectTrainCheckboxes();
            renderDashboard();
            renderTable();
            renderDraftTable();
            renderDraftHapusTable();
            renderApprovalTable();
            renderHistoryTable();
            populateNIKList();
            
            const urlParams = new URLSearchParams(window.location.search);
            const profileParam = urlParams.get('profile');
            if (profileParam) {
                try {
                    const profileData = JSON.parse(decodeURIComponent(atob(profileParam)));
                    setTimeout(() => showProfile(profileData), 300);
                } catch(e) { showToast("QR Code tidak valid atau rusak."); }
            }
        });

        function loadData() {
            const savedEmp = localStorage.getItem('certiTrackData_v5');
            const savedSub = localStorage.getItem('certiTrackSub_v1');
            const savedHist = localStorage.getItem('certiTrackHist_v1');
            if (savedEmp) {
                employees = JSON.parse(savedEmp);
            } else {
                employees = [
                    { 
                        id: "80028", name: "Rizki Hidayat", department: "Production", type: "Direct",
                        joinDate: "2020-05-10", permanentDate: "2021-05-10", photoUrl: "", comps: ["Mesin Injection A"],
                        certs: [{ name: "Final Checker", date: "2024-05-10", expiry: "2025-05-10", issuer: "PT Indonesia Stanley Electric" }],
                        trainings: [{ name: "Final Checker", date: "2024-04-10" }]
                    }
                ];
                saveData();
            }
            if (savedSub) submissionQAS = JSON.parse(savedSub);
            if (savedHist) historyKaryawan = JSON.parse(savedHist);
        }

        function saveData() {
            localStorage.setItem('certiTrackData_v5', JSON.stringify(employees));
            localStorage.setItem('certiTrackSub_v1', JSON.stringify(submissionQAS));
            localStorage.setItem('certiTrackHist_v1', JSON.stringify(historyKaryawan));
            populateNIKList();
        }

        // ================= TAB NAVIGATION =================
        function switchTab(tabId) {
            document.querySelectorAll('main > section').forEach(sec => sec.classList.add('hidden'));
            document.getElementById('tab-' + tabId).classList.remove('hidden');

            ['dashboard', 'data', 'pengajuan', 'penghapusan', 'approval', 'history', 'scanner', 'admin-history'].forEach(id => {
                const btn = document.getElementById('nav-' + id);
                if(btn) {
                    if(id === tabId) {
                        if(id === 'approval') { btn.classList.add('bg-amber-600', 'text-white'); btn.classList.remove('text-amber-200'); }
                        else { btn.classList.add('bg-blue-700', 'text-white'); btn.classList.remove('text-blue-200'); }
                    } else {
                        if(id === 'approval') { btn.classList.remove('bg-amber-600', 'text-white'); btn.classList.add('text-amber-200'); }
                        else { btn.classList.remove('bg-blue-700', 'text-white'); btn.classList.add('text-blue-200'); }
                    }
                }
            });

            if (tabId === 'scanner') startScanner(); else stopScanner();
            if (tabId === 'dashboard') { currentAlertPage = 1; renderDashboard(); } 
            if (tabId === 'data') { renderTable(); populateNIKList(); }
            if (tabId === 'pengajuan') populateNIKList();
            if (tabId === 'penghapusan') populateNIKList();
            if (tabId === 'approval') renderApprovalTable();
            if (tabId === 'history') renderHistoryTable();
            
            if(window.innerWidth < 768) toggleSidebar(false);
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

        function populateNIKList() {
            const listSertif = document.getElementById('nik-list-sertif');
            const listHapus = document.getElementById('nik-list-hapus');
            if (listSertif) listSertif.innerHTML = '';
            if (listHapus) listHapus.innerHTML = '';
            
            employees.forEach(e => {
                const opt = `<option value="${e.id}">${e.name}</option>`;
                if(listSertif) listSertif.insertAdjacentHTML('beforeend', opt);
                if(listHapus) listHapus.insertAdjacentHTML('beforeend', opt);
            });
        }

        // ================= PENGAJUAN SERTIFIKASI LOGIC =================
        function autoFillName() {
            const nik = document.getElementById('peng-nik').value.trim().toUpperCase();
            if(nik) {
                const emp = employees.find(e => e.id === nik);
                if(emp) document.getElementById('peng-nama').value = emp.name;
                else document.getElementById('peng-nama').value = '';
            }
        }

        function checkPengajuanCert() {
            const cert = document.getElementById('peng-cert').value;
            const eyeContainer = document.getElementById('peng-doc-eye-container');
            if(cert === "Final Checker") {
                eyeContainer.classList.remove('hidden');
                document.getElementById('peng-doc-eye').required = true;
            } else {
                eyeContainer.classList.add('hidden');
                document.getElementById('peng-doc-eye').required = false;
            }
        }

        function addDraftPengajuan(e) {
            e.preventDefault();
            const nik = document.getElementById('peng-nik').value.trim().toUpperCase();
            const name = document.getElementById('peng-nama').value.trim();
            const cert = document.getElementById('peng-cert').value;
            const date = document.getElementById('peng-date').value;
            
            const fHadir = document.getElementById('peng-doc-hadir').files[0];
            const fTulis = document.getElementById('peng-doc-tulis').files[0];
            const fPraktek = document.getElementById('peng-doc-praktek').files[0];
            const fEye = document.getElementById('peng-doc-eye').files[0];

            draftPengajuan.push({
                nik, name, cert, date,
                docs: { hadir: !!fHadir, tulis: !!fTulis, praktek: !!fPraktek, eye: cert === "Final Checker" ? !!fEye : false }
            });

            document.getElementById('form-pengajuan').reset();
            checkPengajuanCert();
            renderDraftTable();
            showToast("Berhasil ditambahkan ke Draft!");
        }

        function removeDraft(index) { draftPengajuan.splice(index, 1); renderDraftTable(); }
        function clearDrafts() { if(confirm("Yakin ingin menghapus semua draft pengajuan?")) { draftPengajuan = []; renderDraftTable(); } }

        function renderDraftTable() {
            const tbody = document.getElementById('draft-table-body');
            document.getElementById('draft-count').textContent = `Total Draft: ${draftPengajuan.length}`;
            tbody.innerHTML = '';
            
            if(draftPengajuan.length === 0) {
                tbody.innerHTML = `<tr><td colspan="6" class="py-6 text-center text-slate-500 italic">Belum ada draft pengajuan.</td></tr>`;
                return;
            }

            draftPengajuan.forEach((d, idx) => {
                let docBadge = `<span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-700">Lengkap</span>`;
                tbody.insertAdjacentHTML('beforeend', `
                    <tr class="hover:bg-slate-50 transition">
                        <td class="py-2 px-4 font-mono font-bold">${d.nik}</td>
                        <td class="py-2 px-4 font-medium">${d.name}</td>
                        <td class="py-2 px-4">${d.cert}</td>
                        <td class="py-2 px-4">${d.date ? new Date(d.date).toLocaleDateString('id-ID') : '-'}</td>
                        <td class="py-2 px-4 text-center">${docBadge}</td>
                        <td class="py-2 px-4 text-center">
                            <button onclick="removeDraft(${idx})" class="text-slate-400 hover:text-red-600 bg-white border border-slate-200 hover:border-red-200 p-1 rounded shadow-sm transition"><i data-lucide="trash-2" class="w-4 h-4"></i></button>
                        </td>
                    </tr>
                `);
            });
            lucide.createIcons();
        }

        function submitDraftsToQAS() {
            if(draftPengajuan.length === 0) { showToast("Draft masih kosong."); return; }
            
            const submitDate = new Date().toISOString().split('T')[0];
            draftPengajuan.forEach(d => {
                submissionQAS.push({
                    id: 'SUB-' + Math.floor(Math.random() * 1000000), reqType: 'Sertifikasi Baru',
                    nik: d.nik, name: d.name, cert: d.cert, trainDate: d.date, 
                    submitDate: submitDate, status: 'Pending', reason: ''
                });
            });

            draftPengajuan = []; saveData(); renderDraftTable(); renderApprovalTable();
            showToast(`Berhasil mengirim ${submissionQAS.length} pengajuan ke Manajer QAS.`);
        }

        function downloadExcelPengajuan() {
            const wb = XLSX.utils.book_new();
            const sampleData = [{ 'NIK': '80028', 'Nama': 'Rizki Hidayat', 'Sertifikasi': 'Final Checker', 'Tanggal Training': '2024-05-10' }];
            const ws = XLSX.utils.json_to_sheet(sampleData);
            ws['!cols'] = [{wch: 10}, {wch: 25}, {wch: 25}, {wch: 15}];
            XLSX.utils.book_append_sheet(wb, ws, 'Draft Pengajuan'); XLSX.writeFile(wb, 'Template_Pengajuan_Sertif.xlsx');
        }

        function handleExcelPengajuan(event) {
            const file = event.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function(e) {
                try {
                    const data = new Uint8Array(e.target.result);
                    const workbook = XLSX.read(data, {type: 'array'});
                    const json = XLSX.utils.sheet_to_json(workbook.Sheets[workbook.SheetNames[0]]);
                    let count = 0;
                    json.forEach(row => {
                        const nik = (row['NIK'] || '').toString().trim().toUpperCase();
                        if(!nik) return;
                        let tgl = row['Tanggal Training'] || '';
                        if(typeof tgl === 'number') tgl = new Date(Math.round((tgl - 25569)*86400*1000)).toISOString().split('T')[0];
                        
                        draftPengajuan.push({ nik: nik, name: row['Nama'] || '', cert: row['Sertifikasi'] || '', date: tgl, docs: { hadir: true, tulis: true, praktek: true, eye: true } });
                        count++;
                    });
                    renderDraftTable();
                    if(count > 0) showToast(`Berhasil mengimpor ${count} draft pengajuan.`);
                } catch(err) { showToast("Format Excel tidak sesuai."); }
            };
            reader.readAsArrayBuffer(file); event.target.value = '';
        }

        // ================= PENGAJUAN PENGHAPUSAN LOGIC =================
        function autoFillHapusName() {
            const nik = document.getElementById('hapus-nik').value.trim().toUpperCase();
            if(nik) {
                const emp = employees.find(e => e.id === nik);
                if(emp) document.getElementById('hapus-nama').value = emp.name;
                else document.getElementById('hapus-nama').value = '';
            }
        }

        function addDraftHapus(e) {
            e.preventDefault();
            const nik = document.getElementById('hapus-nik').value.trim().toUpperCase();
            const name = document.getElementById('hapus-nama').value.trim();
            const reason = document.getElementById('hapus-reason').value.trim();
            
            draftPenghapusan.push({ nik, name, reason });
            document.getElementById('form-hapus').reset();
            renderDraftHapusTable();
            showToast("Berhasil ditambahkan ke Draft Penghapusan!");
        }

        function removeDraftHapus(index) { draftPenghapusan.splice(index, 1); renderDraftHapusTable(); }
        function clearDraftsHapus() { if(confirm("Yakin ingin menghapus semua draft penghapusan?")) { draftPenghapusan = []; renderDraftHapusTable(); } }

        function renderDraftHapusTable() {
            const tbody = document.getElementById('draft-hapus-table-body');
            document.getElementById('draft-hapus-count').textContent = `Total Draft: ${draftPenghapusan.length}`;
            tbody.innerHTML = '';
            
            if(draftPenghapusan.length === 0) {
                tbody.innerHTML = `<tr><td colspan="4" class="py-6 text-center text-slate-500 italic">Belum ada draft pengajuan penghapusan.</td></tr>`;
                return;
            }

            draftPenghapusan.forEach((d, idx) => {
                tbody.insertAdjacentHTML('beforeend', `
                    <tr class="hover:bg-slate-50 transition">
                        <td class="py-2 px-4 font-mono font-bold">${d.nik}</td>
                        <td class="py-2 px-4 font-medium">${d.name}</td>
                        <td class="py-2 px-4 text-red-600">${d.reason}</td>
                        <td class="py-2 px-4 text-center">
                            <button onclick="removeDraftHapus(${idx})" class="text-slate-400 hover:text-red-600 bg-white border border-slate-200 hover:border-red-200 p-1 rounded shadow-sm transition"><i data-lucide="trash-2" class="w-4 h-4"></i></button>
                        </td>
                    </tr>
                `);
            });
            lucide.createIcons();
        }

        function submitDraftsHapusToQAS() {
            if(draftPenghapusan.length === 0) { showToast("Draft masih kosong."); return; }
            const submitDate = new Date().toISOString().split('T')[0];
            draftPenghapusan.forEach(d => {
                submissionQAS.push({
                    id: 'DEL-' + Math.floor(Math.random() * 1000000), reqType: 'Penghapusan',
                    nik: d.nik, name: d.name, submitDate: submitDate, status: 'Pending', delReason: d.reason, reason: ''
                });
            });

            draftPenghapusan = []; saveData(); renderDraftHapusTable(); renderApprovalTable();
            showToast(`Berhasil mengirim pengajuan penghapusan ke Manajer QAS.`);
        }

        function downloadExcelHapus() {
            const wb = XLSX.utils.book_new();
            const sampleData = [{ 'NIK': '80028', 'Alasan Penghapusan': 'Resign per 1 Jan 2025' }];
            const ws = XLSX.utils.json_to_sheet(sampleData);
            ws['!cols'] = [{wch: 15}, {wch: 50}];
            XLSX.utils.book_append_sheet(wb, ws, 'Draft Penghapusan'); XLSX.writeFile(wb, 'Template_Pengajuan_Penghapusan.xlsx');
        }

        function handleExcelHapus(event) {
            const file = event.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function(e) {
                try {
                    const data = new Uint8Array(e.target.result);
                    const workbook = XLSX.read(data, {type: 'array'});
                    const json = XLSX.utils.sheet_to_json(workbook.Sheets[workbook.SheetNames[0]]);
                    let count = 0;
                    json.forEach(row => {
                        const nik = (row['NIK'] || '').toString().trim().toUpperCase();
                        if(!nik) return;
                        const emp = employees.find(e => e.id === nik);
                        const empName = emp ? emp.name : "Tidak ditemukan di Data";
                        
                        draftPenghapusan.push({ nik: nik, name: empName, reason: row['Alasan Penghapusan'] || 'Data tidak relevan' });
                        count++;
                    });
                    renderDraftHapusTable();
                    if(count > 0) showToast(`Berhasil mengimpor ${count} draft penghapusan.`);
                } catch(err) { showToast("Format Excel tidak sesuai."); }
            };
            reader.readAsArrayBuffer(file); event.target.value = '';
        }

        // ================= APPROVAL QAS LOGIC =================
        function renderApprovalTable() {
            const tbodyPending = document.getElementById('approval-pending-table');
            const tbodyHistory = document.getElementById('approval-history-table');
            tbodyPending.innerHTML = ''; tbodyHistory.innerHTML = '';

            const pending = submissionQAS.filter(s => s.status === 'Pending');
            const history = submissionQAS.filter(s => s.status !== 'Pending').sort((a,b) => new Date(b.submitDate) - new Date(a.submitDate)).slice(0, 10);

            if(pending.length === 0) {
                tbodyPending.innerHTML = `<tr><td colspan="6" class="py-6 text-center text-slate-500 italic">Tidak ada pengajuan yang menunggu approval.</td></tr>`;
            } else {
                pending.forEach(s => {
                    const isHapus = s.reqType === 'Penghapusan';
                    const typeBadge = isHapus ? '<span class="text-xs font-bold text-red-600 bg-red-100 px-2 py-0.5 rounded uppercase">Hapus Karyawan</span>' : '<span class="text-xs font-bold text-blue-600 bg-blue-100 px-2 py-0.5 rounded uppercase">Sertifikasi Baru</span>';
                    const detail = isHapus ? `<span class="text-red-500 italic">Alasan: ${s.delReason}</span>` : `<span class="font-bold text-slate-700">${s.cert}</span><br><span class="text-[10px] text-slate-500">Tgl: ${s.trainDate ? new Date(s.trainDate).toLocaleDateString('id-ID') : '-'}</span>`;
                    const docs = isHapus ? '-' : '<span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-700"><i data-lucide="check" class="w-3 h-3 inline"></i> Lengkap</span>';

                    tbodyPending.insertAdjacentHTML('beforeend', `
                        <tr class="hover:bg-amber-50/30 transition">
                            <td class="py-3 px-4 font-mono font-bold">${s.nik}</td>
                            <td class="py-3 px-4 font-medium">${s.name}</td>
                            <td class="py-3 px-4">${typeBadge}</td>
                            <td class="py-3 px-4 leading-tight">${detail}</td>
                            <td class="py-3 px-4 text-center">${docs}</td>
                            <td class="py-3 px-4 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button onclick="approveSub('${s.id}')" class="bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded text-xs font-bold transition shadow-sm">Approve</button>
                                    <button onclick="declineSub('${s.id}')" class="bg-white border border-red-300 text-red-600 hover:bg-red-50 px-3 py-1.5 rounded text-xs font-bold transition shadow-sm">Decline</button>
                                </div>
                            </td>
                        </tr>
                    `);
                });
            }

            if(history.length === 0) {
                tbodyHistory.innerHTML = `<tr><td class="py-4 px-6 text-center text-slate-500 italic">Belum ada riwayat approval.</td></tr>`;
            } else {
                history.forEach(s => {
                    const badge = s.status === 'Approved' ? `<span class="text-emerald-600 font-bold"><i data-lucide="check-circle" class="w-4 h-4 inline"></i> Approved</span>` : `<span class="text-red-600 font-bold"><i data-lucide="x-circle" class="w-4 h-4 inline"></i> Declined</span>`;
                    const isHapus = s.reqType === 'Penghapusan';
                    const detail = isHapus ? `Penghapusan: ${s.delReason}` : `Sertif: ${s.cert}`;
                    
                    tbodyHistory.insertAdjacentHTML('beforeend', `
                        <tr class="border-b border-slate-100">
                            <td class="py-2 px-6">
                                <div class="flex items-center justify-between">
                                    <div><span class="font-mono font-bold">${s.nik}</span> - <span class="font-medium">${s.name}</span> <span class="text-slate-400 mx-1">|</span> <span class="font-bold ${isHapus ? 'text-red-500' : 'text-slate-600'}">${detail}</span></div>
                                    <div class="flex items-center gap-4">
                                        ${s.reason ? `<span class="text-xs text-red-500 italic">"${s.reason}"</span>` : ''}
                                        ${badge}
                                    </div>
                                </div>
                            </td>
                        </tr>
                    `);
                });
            }
            lucide.createIcons();
        }

        function approveSub(id) {
            const sub = submissionQAS.find(s => s.id === id);
            if(!sub) return;

            if(sub.reqType === 'Penghapusan') {
                if(!confirm(`Yakin ingin menyetujui penghapusan data ${sub.name} (${sub.nik})? Data akan dipindahkan ke History Sertifikasi.`)) return;
                
                const empIdx = employees.findIndex(e => e.id === sub.nik);
                if(empIdx > -1) {
                    const empToMove = employees[empIdx];
                    empToMove.deletedDate = new Date().toISOString();
                    empToMove.deleteReason = sub.delReason;
                    historyKaryawan.push(empToMove);
                    employees.splice(empIdx, 1);
                } else {
                    historyKaryawan.push({ id: sub.nik, name: sub.name, department: "-", deletedDate: new Date().toISOString(), deleteReason: sub.delReason });
                }
                sub.status = 'Approved';
                saveData(); renderApprovalTable(); renderTable(); renderDashboard(); renderHistoryTable();
                showToast("Pengajuan Penghapusan Disetujui. Data telah dipindahkan ke arsip History.");

            } else {
                // Proses Sertifikasi Baru
                if(!confirm("Yakin ingin meng-Approve pengajuan ini? Masa berlaku akan dihitung 1 tahun dari hari ini.")) return;
                
                let emp = employees.find(e => e.id === sub.nik);
                if(!emp) {
                    emp = { id: sub.nik, name: sub.name, department: "Belum Ditentukan", type: "Direct", joinDate: "", permanentDate: "", photoUrl: "", comps: [], certs: [], trainings: [] };
                    employees.push(emp);
                }

                const approveDate = new Date();
                const expiryDate = new Date();
                expiryDate.setFullYear(approveDate.getFullYear() + 1);
                const dateStr = approveDate.toISOString().split('T')[0];
                const expStr = expiryDate.toISOString().split('T')[0];

                if(!emp.trainings) emp.trainings = [];
                emp.trainings.push({ name: sub.cert, date: sub.trainDate });

                if(!emp.certs) emp.certs = [];
                const exCert = emp.certs.find(c => c.name === sub.cert);
                if(exCert) { exCert.date = dateStr; exCert.expiry = expStr; exCert.issuer = "Manajer QAS"; } 
                else { emp.certs.push({ name: sub.cert, date: dateStr, expiry: expStr, issuer: "Manajer QAS" }); }

                sub.status = 'Approved';
                saveData(); renderApprovalTable(); renderTable(); renderDashboard();
                showToast("Pengajuan disetujui. Masa berlaku Sertifikasi telah diupdate otomatis.");
            }
        }

        function declineSub(id) {
            const reason = prompt("Masukkan alasan penolakan:");
            if(reason === null) return;
            const sub = submissionQAS.find(s => s.id === id);
            if(sub) { sub.status = 'Declined'; sub.reason = reason; saveData(); renderApprovalTable(); showToast("Pengajuan ditolak."); }
        }

        // ================= ADMIN: TAMBAH HISTORY VIA EXCEL =================
        function downloadExcelHistoryTemplate() {
            const wb = XLSX.utils.book_new();
            const sampleData = [{ 
                'NIK': '90011', 
                'Nama Lengkap': 'Budi Santoso', 
                'Departemen': 'Quality Control', 
                'Sertifikasi': 'Final Checker, Jouho Board',
                'Tanggal Dihapus': '2023-12-01', 
                'Alasan Penghapusan': 'Resign' 
            }];
            const ws = XLSX.utils.json_to_sheet(sampleData);
            ws['!cols'] = [{wch: 15}, {wch: 30}, {wch: 25}, {wch: 30}, {wch: 20}, {wch: 35}];
            XLSX.utils.book_append_sheet(wb, ws, 'Data History'); 
            XLSX.writeFile(wb, 'Template_Tambah_History.xlsx');
        }

        function handleExcelHistoryImport(event) {
            const file = event.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function(e) {
                try {
                    const data = new Uint8Array(e.target.result);
                    const workbook = XLSX.read(data, {type: 'array'});
                    const json = XLSX.utils.sheet_to_json(workbook.Sheets[workbook.SheetNames[0]]);
                    let count = 0;
                    
                    json.forEach(row => {
                        const nik = (row['NIK'] || '').toString().trim().toUpperCase();
                        if(!nik) return;
                        
                        let tglHapus = row['Tanggal Dihapus'] || '';
                        // Konversi format tanggal excel ke ISO string
                        if(typeof tglHapus === 'number') {
                            tglHapus = new Date(Math.round((tglHapus - 25569)*86400*1000)).toISOString();
                        } else if(tglHapus) {
                            tglHapus = new Date(tglHapus).toISOString();
                        } else {
                            tglHapus = new Date().toISOString(); 
                        }

                        // Parse Sertifikasi 
                        const rawCerts = (row['Sertifikasi'] || '').toString();
                        const certs = rawCerts ? rawCerts.split(',').map(s => ({ name: s.trim() })) : [];

                        historyKaryawan.push({ 
                            id: nik, 
                            name: row['Nama Lengkap'] || 'Tanpa Nama', 
                            department: row['Departemen'] || '-', 
                            certs: certs,
                            deletedDate: tglHapus, 
                            deleteReason: row['Alasan Penghapusan'] || 'Diimpor via Excel Admin' 
                        });
                        count++;
                    });
                    
                    saveData();
                    if(count > 0) {
                        showToast(`Berhasil mengimpor ${count} data history.`);
                        switchTab('history'); // Pindah tab untuk melihat hasil
                    } else {
                        showToast("Tidak ada data valid yang ditemukan di Excel.");
                    }
                } catch(err) { showToast("Format Excel tidak sesuai."); }
            };
            reader.readAsArrayBuffer(file); 
            event.target.value = ''; // Reset input file
        }

        // ================= HISTORY SERTIFIKASI LOGIC =================
        function renderHistoryTable() {
            const tbody = document.getElementById('history-table-body');
            const emptyState = document.getElementById('history-empty-state');
            const searchVal = document.getElementById('search-history').value.trim().toLowerCase();
            tbody.innerHTML = '';

            // Clone array & urutkan dari tanggal dihapus paling baru (descending)
            let filtered = [...historyKaryawan];
            filtered.sort((a, b) => new Date(b.deletedDate || 0) - new Date(a.deletedDate || 0));

            // Terapkan filter pencarian jika ada
            if(searchVal) {
                filtered = filtered.filter(h => h.id.toLowerCase().includes(searchVal) || h.name.toLowerCase().includes(searchVal));
            }

            if (filtered.length === 0) { emptyState.classList.remove('hidden'); } 
            else {
                emptyState.classList.add('hidden');
                filtered.forEach(h => {
                    const delDate = h.deletedDate ? new Date(h.deletedDate).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' }) : '-';
                    const certsStr = (h.certs && h.certs.length > 0) ? h.certs.map(c => c.name).join(', ') : '-';
                    
                    tbody.insertAdjacentHTML('beforeend', `
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-4 px-6 font-mono font-bold text-slate-500">${h.id}</td>
                            <td class="py-4 px-6 font-bold text-slate-600">${h.name}</td>
                            <td class="py-4 px-6 text-slate-500">${h.department || '-'}</td>
                            <td class="py-4 px-6 text-slate-700 font-medium">${certsStr}</td>
                            <td class="py-4 px-6 text-slate-600 font-medium">${delDate}</td>
                            <td class="py-4 px-6 text-red-600 italic font-medium">${h.deleteReason || '-'}</td>
                        </tr>
                    `);
                });
            }
        }

        // ================= DASHBOARD & STATUS CALC =================
        function getCertStatus(expiryDateStr) {
            if (!expiryDateStr) return { status: 'Aktif', class: 'bg-emerald-100 text-emerald-700 border-emerald-200', text: 'Aktif' };
            
            const today = new Date(); 
            today.setHours(0,0,0,0);
            
            const expiry = new Date(expiryDateStr);
            expiry.setHours(0,0,0,0); // Normalisasi jam agar hitungan hari presisi

            // Hitung selisih hari
            const diffTime = expiry - today;
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)); 

            if (diffDays < 0) {
                return { status: 'Expired', class: 'bg-red-100 text-red-700 border-red-200', text: 'Expired' };
            } else if (diffDays <= 30) {
                return { status: 'Warning', class: 'bg-amber-100 text-amber-700 border-amber-200', text: `Akan Expired dlm ${diffDays} hari` };
            } else {
                return { status: 'Aktif', class: 'bg-emerald-100 text-emerald-700 border-emerald-200', text: 'Aktif' };
            }
        }

        // Fungsi Global Baru untuk Pagination Alert
        function changeAlertPage(page) {
            currentAlertPage = page;
            renderDashboard();
        }

        function renderDashboard() {
            let alertList = [];
            const today = new Date(); today.setHours(0,0,0,0);
            const thirtyDaysLater = new Date(today); thirtyDaysLater.setDate(today.getDate() + 30);

            const dashContainer = document.getElementById('dashboard-certs-container');
            dashContainer.innerHTML = '';

            TARGET_CERTS.forEach(certName => {
                let certEmpCount = 0; let certActive = 0; let certExp = 0;

                employees.forEach(emp => {
                    if (emp.certs) {
                        const hasThisCert = emp.certs.find(c => c.name === certName);
                        if (hasThisCert) {
                            certEmpCount++;
                            const statusObj = getCertStatus(hasThisCert.expiry);
                            
                            // Hitung statistik (Sertifikasi yg Warning tetap dihitung 'Aktif' di card dashboard)
                            if (statusObj.status === 'Aktif' || statusObj.status === 'Warning') certActive++; 
                            else certExp++; 

                            // Masukkan ke Alert List jika statusnya Expired ATAU Warning (<= 30 hari)
                            if (statusObj.status === 'Warning' || statusObj.status === 'Expired') {
                                alertList.push({ 
                                    id: emp.id, 
                                    name: emp.name, 
                                    cert: hasThisCert.name, 
                                    expiry: hasThisCert.expiry, 
                                    status: statusObj.status, 
                                    text: statusObj.text,           // Menyimpan teks "Expired dlm X hari"
                                    statusClass: statusObj.class,   // Menyimpan warna (Merah/Kuning)
                                    dateVal: new Date(hasThisCert.expiry) 
                                });
                            }
                        }
                    }
                });

                dashContainer.insertAdjacentHTML('beforeend', `
                    <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
                        <h3 class="font-bold text-lg text-slate-800 mb-4 border-b border-slate-100 pb-2">${certName}</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div class="bg-slate-50 p-4 rounded-xl border border-slate-100 flex items-center gap-3">
                                <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 shrink-0"><i data-lucide="users" class="w-6 h-6"></i></div>
                                <div><p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Tersertifikasi</p><h3 class="text-2xl font-bold text-slate-800 leading-none mt-1">${certEmpCount}</h3></div>
                            </div>
                            <div class="bg-slate-50 p-4 rounded-xl border border-slate-100 flex items-center gap-3">
                                <div class="w-12 h-12 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 shrink-0"><i data-lucide="check-circle" class="w-6 h-6"></i></div>
                                <div><p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Aktif</p><h3 class="text-2xl font-bold text-slate-800 leading-none mt-1">${certActive}</h3></div>
                            </div>
                            <div class="bg-slate-50 p-4 rounded-xl border border-slate-100 flex items-center gap-3">
                                <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center text-red-600 shrink-0"><i data-lucide="alert-triangle" class="w-6 h-6"></i></div>
                                <div><p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Expired</p><h3 class="text-2xl font-bold text-slate-800 leading-none mt-1">${certExp}</h3></div>
                            </div>
                        </div>
                    </div>
                `);
            });

            // Urutkan Alert dari yang paling dekat expire
            alertList.sort((a, b) => a.dateVal - b.dateVal);
            const alertTable = document.getElementById('dash-alert-table');
            const paginationContainer = document.getElementById('dash-alert-pagination');
            
            alertTable.innerHTML = '';
            paginationContainer.innerHTML = '';
            paginationContainer.classList.add('hidden');
            
            if (alertList.length === 0) {
                alertTable.innerHTML = `<tr><td colspan="5" class="py-5 px-6 text-center text-slate-500 italic font-medium">Wah! Semua sertifikasi masih aman (tidak ada yang mendekati expired).</td></tr>`;
            } else {
                const itemsPerPage = 5;
                const totalPages = Math.ceil(alertList.length / itemsPerPage);
                
                // Pastikan current page selalu berada dalam batas valid
                if (currentAlertPage > totalPages) currentAlertPage = totalPages;
                if (currentAlertPage < 1) currentAlertPage = 1;

                const startIndex = (currentAlertPage - 1) * itemsPerPage;
                const endIndex = startIndex + itemsPerPage;
                const currentItems = alertList.slice(startIndex, endIndex);

                currentItems.forEach(alert => {
                    alertTable.insertAdjacentHTML('beforeend', `
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3 px-6"><span class="font-mono font-bold text-slate-700">${alert.id}</span></td>
                            <td class="py-3 px-6 font-medium text-slate-800">${alert.name}</td>
                            <td class="py-3 px-6 text-slate-600">${alert.cert}</td>
                            <td class="py-3 px-6 text-slate-600 font-semibold">${new Date(alert.expiry).toLocaleDateString('id-ID')}</td>
                            <td class="py-3 px-6"><span class="px-2.5 py-1 rounded-full text-[11px] font-bold ${alert.statusClass}">${alert.text}</span></td>
                        </tr>
                    `);
                });

                // Tampilkan Pagination jika lebih dari 1 halaman
                if (totalPages > 1) {
                    paginationContainer.classList.remove('hidden');
                    
                    let prevBtnDisabled = currentAlertPage === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-slate-200 cursor-pointer';
                    let nextBtnDisabled = currentAlertPage === totalPages ? 'opacity-50 cursor-not-allowed' : 'hover:bg-slate-200 cursor-pointer';

                    paginationContainer.innerHTML = `
                        <p class="text-xs text-slate-500 font-medium">Menampilkan ${startIndex + 1} - ${Math.min(endIndex, alertList.length)} dari total ${alertList.length} alert</p>
                        <div class="flex items-center gap-2">
                            <button onclick="changeAlertPage(${currentAlertPage - 1})" class="p-1.5 rounded bg-white text-slate-600 border border-slate-300 transition ${prevBtnDisabled}" ${currentAlertPage === 1 ? 'disabled' : ''}>
                                <i data-lucide="chevron-left" class="w-4 h-4"></i>
                            </button>
                            <span class="text-xs font-bold text-slate-700">Halaman ${currentAlertPage} dari ${totalPages}</span>
                            <button onclick="changeAlertPage(${currentAlertPage + 1})" class="p-1.5 rounded bg-white text-slate-600 border border-slate-300 transition ${nextBtnDisabled}" ${currentAlertPage === totalPages ? 'disabled' : ''}>
                                <i data-lucide="chevron-right" class="w-4 h-4"></i>
                            </button>
                        </div>
                    `;
                    lucide.createIcons(); // Refresh ikon di dalam tombol pagination
                }
            }
        }

        // ================= LOGIKA TABEL UTAMA (Data Karyawan) =================
        function renderTable() {
            const tbody = document.getElementById('employee-table-body');
            const emptyState = document.getElementById('empty-state');
            tbody.innerHTML = '';
            
            if (employees.length === 0) { emptyState.classList.remove('hidden'); } 
            else {
                emptyState.classList.add('hidden');
                employees.forEach(emp => {
                    const compCount = emp.comps ? emp.comps.length : 0;
                    const certCount = emp.certs ? emp.certs.length : 0;
                    
                    let typeBadge = '';
                    if (emp.type === 'Direct') typeBadge = '<span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-[10px] font-bold uppercase">Direct</span>';
                    else if (emp.type === 'Semi-Direct') typeBadge = '<span class="bg-amber-100 text-amber-700 px-2 py-0.5 rounded text-[10px] font-bold uppercase">Semi-Direct</span>';
                    else typeBadge = '<span class="bg-slate-100 text-slate-700 px-2 py-0.5 rounded text-[10px] font-bold uppercase">In-Direct</span>';

                    const tr = document.createElement('tr');
                    tr.className = "hover:bg-blue-50/50 transition-colors group";
                    tr.innerHTML = `
                        <td class="py-4 px-6"><span class="font-mono text-blue-700 font-bold">${emp.id}</span></td>
                        <td class="py-4 px-6"><div class="font-bold text-slate-800">${emp.name}</div><div class="text-xs text-slate-500">${emp.department}</div></td>
                        <td class="py-4 px-6 text-center">${typeBadge}</td>
                        <td class="py-4 px-6 text-center"><span class="text-xs font-bold text-slate-600">${compCount} Skill</span></td>
                        <td class="py-4 px-6 text-center"><span class="text-xs font-bold text-slate-600">${certCount} Sertif</span></td>
                        <td class="py-4 px-6 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <button onclick="openModalForm('${emp.id}')" title="Edit Data" class="p-1.5 text-slate-400 hover:text-amber-600 bg-white border border-slate-200 rounded transition shadow-sm"><i data-lucide="edit-3" class="w-4 h-4"></i></button>
                                <button onclick="showProfile('${emp.id}')" title="Lihat Profil" class="p-1.5 text-slate-400 hover:text-blue-600 bg-white border border-slate-200 rounded transition shadow-sm"><i data-lucide="eye" class="w-4 h-4"></i></button>
                                <button onclick="openIDCard('${emp.id}')" title="Generate ID Card" class="p-1.5 text-slate-400 hover:text-emerald-600 bg-white border border-slate-200 rounded transition shadow-sm"><i data-lucide="qr-code" class="w-4 h-4"></i></button>
                                <button onclick="deleteEmployee('${emp.id}')" title="Hapus" class="p-1.5 text-slate-400 hover:text-red-600 bg-white border border-slate-200 rounded transition shadow-sm"><i data-lucide="trash-2" class="w-4 h-4"></i></button>
                            </div>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
                lucide.createIcons();
            }
        }

        // ================= FORM & CERT LOGIC =================
        function buildDirectCertCheckboxes() {
            const container = document.getElementById('cert-direct-area');
            container.innerHTML = '';
            TARGET_CERTS.forEach((cert, index) => {
                container.insertAdjacentHTML('beforeend', `
                    <div class="border border-slate-200 rounded-lg p-3 bg-white hover:border-blue-300 transition mb-3">
                        <label class="flex items-center gap-2 cursor-pointer mb-2">
                            <input type="checkbox" id="cb-cert-${index}" value="${cert}" class="w-4 h-4 text-blue-600 focus:ring-blue-500 rounded" onchange="toggleDirectDetails(${index})">
                            <span class="text-sm font-bold text-slate-700">${cert}</span>
                        </label>
                        <div id="details-cert-${index}" class="hidden grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2 border-t border-slate-100 mt-2">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 mb-1 uppercase">Tanggal Sertifikasi</label>
                                <input type="date" id="date-cert-${index}" class="w-full px-2 py-1.5 border border-slate-300 rounded text-xs">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 mb-1 uppercase">Masa Berlaku (Expiry)</label>
                                <input type="date" id="exp-cert-${index}" class="w-full px-2 py-1.5 border border-slate-300 rounded text-xs">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 mb-1 uppercase">Pemberi / Issuer</label>
                                <input type="text" id="iss-cert-${index}" placeholder="Contoh: PT ABC" class="w-full px-2 py-1.5 border border-slate-300 rounded text-xs">
                            </div>
                        </div>
                    </div>
                `);
            });
        }

        function toggleDirectDetails(index) {
            const cb = document.getElementById(`cb-cert-${index}`);
            const details = document.getElementById(`details-cert-${index}`);
            if(cb.checked) { details.classList.remove('hidden'); } 
            else {
                details.classList.add('hidden');
                document.getElementById(`date-cert-${index}`).value = '';
                document.getElementById(`exp-cert-${index}`).value = '';
                document.getElementById(`iss-cert-${index}`).value = '';
            }
        }

        function buildDirectTrainCheckboxes() {
            const container = document.getElementById('train-direct-area');
            container.innerHTML = '';
            TARGET_TRAININGS.forEach((train, index) => {
                container.insertAdjacentHTML('beforeend', `
                    <div class="border border-slate-200 rounded-lg p-3 bg-white hover:border-blue-300 transition mb-3">
                        <label class="flex items-center gap-2 cursor-pointer mb-2">
                            <input type="checkbox" id="cb-train-${index}" value="${train}" class="w-4 h-4 text-blue-600 focus:ring-blue-500 rounded" onchange="toggleDirectTrainDetails(${index})">
                            <span class="text-sm font-bold text-slate-700">${train}</span>
                        </label>
                        <div id="details-train-${index}" class="hidden pt-2 border-t border-slate-100 mt-2">
                            <label class="block text-[10px] font-bold text-slate-500 mb-1 uppercase">Tanggal Pelaksanaan Training</label>
                            <div id="train-dates-container-${index}"></div>
                            <button type="button" onclick="addDirectTrainDate(${index})" class="mt-2 text-[10px] font-bold bg-blue-50 hover:bg-blue-100 text-blue-700 px-2.5 py-1.5 rounded transition flex items-center gap-1 border border-blue-200 shadow-sm"><i data-lucide="plus" class="w-3.5 h-3.5"></i> Tambah Tanggal Training</button>
                        </div>
                    </div>
                `);
            });
        }

        function addDirectTrainDate(index, dateVal = '') {
            const container = document.getElementById(`train-dates-container-${index}`);
            const uniqueId = `dt-${index}-${Math.floor(Math.random() * 10000)}`;
            container.insertAdjacentHTML('beforeend', `
                <div class="flex items-center gap-2 mb-2" id="wrap-${uniqueId}">
                    <input type="date" value="${dateVal}" class="direct-train-date-${index} flex-1 px-2 py-1.5 border border-slate-300 rounded text-xs bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <button type="button" onclick="document.getElementById('wrap-${uniqueId}').remove()" class="text-slate-400 hover:bg-red-50 hover:text-red-600 p-1.5 rounded transition border border-transparent hover:border-red-200"><i data-lucide="trash" class="w-3.5 h-3.5"></i></button>
                </div>
            `);
            lucide.createIcons();
        }

        function toggleDirectTrainDetails(index) {
            const cb = document.getElementById(`cb-train-${index}`);
            const details = document.getElementById(`details-train-${index}`);
            const container = document.getElementById(`train-dates-container-${index}`);
            if(cb.checked) {
                details.classList.remove('hidden');
                if(container.children.length === 0) addDirectTrainDate(index);
            } else {
                details.classList.add('hidden');
                container.innerHTML = '';
            }
        }

        function addIndirectTrainRow(data = null) {
            const container = document.getElementById('train-indirect-area');
            const rowId = indirectTrainRowCount++;
            const nameVal = data ? data.name : ''; const dateVal = data ? data.date : '';
            container.insertAdjacentHTML('beforeend', `
                <div id="ind-train-row-${rowId}" class="flex flex-col sm:flex-row gap-2 border border-slate-200 p-3 rounded-lg bg-white relative mb-2">
                    <button type="button" onclick="document.getElementById('ind-train-row-${rowId}').remove()" class="absolute top-2 right-2 text-slate-400 hover:bg-red-50 hover:text-red-500 p-1 rounded transition"><i data-lucide="x" class="w-4 h-4"></i></button>
                    <div class="flex-1">
                        <label class="block text-[10px] font-bold text-slate-500 mb-1 uppercase">Nama Training</label>
                        <input type="text" id="ind-train-name-${rowId}" value="${nameVal}" required placeholder="Contoh: Pelatihan K3" class="w-full px-2 py-1.5 border border-slate-300 rounded text-xs">
                    </div>
                    <div class="w-full sm:w-48">
                        <label class="block text-[10px] font-bold text-slate-500 mb-1 uppercase">Tanggal Pelaksanaan</label>
                        <input type="date" id="ind-train-date-${rowId}" value="${dateVal}" class="w-full px-2 py-1.5 border border-slate-300 rounded text-xs">
                    </div>
                </div>
            `);
            lucide.createIcons();
        }

        function addIndirectCertRow(data = null) {
            const container = document.getElementById('cert-indirect-area');
            const rowId = indirectCertRowCount++;
            const nameVal = data ? data.name : ''; const dateVal = data ? data.date : '';
            const expVal = data ? data.expiry : ''; const issVal = data ? data.issuer : '';
            container.insertAdjacentHTML('beforeend', `
                <div id="ind-row-${rowId}" class="flex flex-col sm:flex-row gap-2 border border-slate-200 p-3 rounded-lg bg-white relative mb-2">
                    <button type="button" onclick="document.getElementById('ind-row-${rowId}').remove()" class="absolute top-2 right-2 text-slate-400 hover:bg-red-50 hover:text-red-500 p-1 rounded transition"><i data-lucide="x" class="w-4 h-4"></i></button>
                    <div class="flex-1">
                        <label class="block text-[10px] font-bold text-slate-500 mb-1 uppercase">Nama Sertifikasi</label>
                        <input type="text" id="ind-name-${rowId}" value="${nameVal}" required placeholder="Contoh: K3 Umum" class="w-full px-2 py-1.5 border border-slate-300 rounded text-xs">
                    </div>
                    <div class="w-full sm:w-32">
                        <label class="block text-[10px] font-bold text-slate-500 mb-1 uppercase">Tanggal</label>
                        <input type="date" id="ind-date-${rowId}" value="${dateVal}" class="w-full px-2 py-1.5 border border-slate-300 rounded text-xs">
                    </div>
                    <div class="w-full sm:w-32">
                        <label class="block text-[10px] font-bold text-slate-500 mb-1 uppercase">Masa Berlaku</label>
                        <input type="date" id="ind-exp-${rowId}" value="${expVal}" class="w-full px-2 py-1.5 border border-slate-300 rounded text-xs">
                    </div>
                    <div class="flex-1">
                        <label class="block text-[10px] font-bold text-slate-500 mb-1 uppercase">Pemberi / Issuer</label>
                        <input type="text" id="ind-iss-${rowId}" value="${issVal}" placeholder="Pihak Sertifikasi" class="w-full px-2 py-1.5 border border-slate-300 rounded text-xs">
                    </div>
                </div>
            `);
            lucide.createIcons();
        }

        function toggleCertMode() {
            const type = document.getElementById('emp-type').value;
            const directArea = document.getElementById('cert-direct-area');
            const indirectArea = document.getElementById('cert-indirect-area');
            const btnAdd = document.getElementById('btn-add-cert-row');

            const trainDirectArea = document.getElementById('train-direct-area');
            const trainIndirectArea = document.getElementById('train-indirect-area');
            const btnAddTrain = document.getElementById('btn-add-train-row');

            if (type === 'Direct') {
                directArea.classList.remove('hidden'); indirectArea.classList.add('hidden'); btnAdd.classList.add('hidden');
                trainDirectArea.classList.remove('hidden'); trainIndirectArea.classList.add('hidden'); btnAddTrain.classList.add('hidden');
            } else {
                directArea.classList.add('hidden'); indirectArea.classList.remove('hidden'); btnAdd.classList.remove('hidden');
                if(indirectArea.children.length === 0) addIndirectCertRow();
                trainDirectArea.classList.add('hidden'); trainIndirectArea.classList.remove('hidden'); btnAddTrain.classList.remove('hidden');
                if(trainIndirectArea.children.length === 0) addIndirectTrainRow();
            }
        }

        function handlePhotoUpload(event) {
            const file = event.target.files[0];
            const statusText = document.getElementById('photo-upload-status');
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = new Image();
                    img.onload = function() {
                        // Fitur kompresi gambar otomatis (Mencegah localStorage cepat penuh)
                        const canvas = document.createElement('canvas');
                        const MAX_WIDTH = 300;
                        const MAX_HEIGHT = 400;
                        let width = img.width;
                        let height = img.height;

                        if (width > height) {
                            if (width > MAX_WIDTH) { height *= MAX_WIDTH / width; width = MAX_WIDTH; }
                        } else {
                            if (height > MAX_HEIGHT) { width *= MAX_HEIGHT / height; height = MAX_HEIGHT; }
                        }
                        
                        canvas.width = width;
                        canvas.height = height;
                        const ctx = canvas.getContext('2d');
                        ctx.drawImage(img, 0, 0, width, height);
                        
                        // Konversi ke Base64 (Kualitas 80%)
                        document.getElementById('emp-photo-url').value = canvas.toDataURL('image/jpeg', 0.8);
                        statusText.textContent = "✓ Foto berhasil dimuat siap disimpan.";
                        statusText.className = 'text-[10px] font-bold text-emerald-600 mt-1';
                    };
                    img.src = e.target.result;
                };
                reader.readAsDataURL(file);
            } else {
                document.getElementById('emp-photo-url').value = '';
                statusText.textContent = "";
                statusText.className = 'text-[10px] text-slate-500 mt-1';
            }
        }

        function openModalForm(empId = null) {
            document.getElementById('employee-form').reset();
            // --- Reset bagian Upload Foto ---
            document.getElementById('emp-photo-file').value = '';
            document.getElementById('emp-photo-url').value = '';
            document.getElementById('photo-upload-status').textContent = '';
            document.getElementById('photo-upload-status').className = 'text-[10px] text-slate-500 mt-1';
            // --------------------------------
            
            indirectCertRowCount = 0; indirectTrainRowCount = 0;
            document.getElementById('cert-indirect-area').innerHTML = ''; document.getElementById('train-indirect-area').innerHTML = '';
            
            TARGET_CERTS.forEach((_, i) => { const cb = document.getElementById(`cb-cert-${i}`); if(cb) { cb.checked = false; toggleDirectDetails(i); } });
            TARGET_TRAININGS.forEach((_, i) => { const cb = document.getElementById(`cb-train-${i}`); if(cb) { cb.checked = false; toggleDirectTrainDetails(i); } });

            if (empId) {
                document.getElementById('form-mode').value = 'edit'; document.getElementById('form-modal-title').innerHTML = "Edit Data Karyawan";
                document.getElementById('emp-id').readOnly = true; document.getElementById('emp-id').classList.add('bg-slate-100');
                const emp = employees.find(e => e.id === empId);
                if (emp) {
                    document.getElementById('emp-id').value = emp.id; document.getElementById('emp-name').value = emp.name;
                    document.getElementById('emp-dept').value = emp.department; document.getElementById('emp-type').value = emp.type || 'Direct';
                    document.getElementById('emp-join-date').value = emp.joinDate || ''; document.getElementById('emp-permanent-date').value = emp.permanentDate || '';
                    
                    // --- Edit Data Bagian Upload Foto ---
                    document.getElementById('emp-photo-url').value = emp.photoUrl || ''; 
                    if(emp.photoUrl) {
                        document.getElementById('photo-upload-status').textContent = "✓ Foto saat ini sudah ada. Upload foto baru untuk mengganti.";
                        document.getElementById('photo-upload-status').className = 'text-[10px] font-bold text-blue-600 mt-1';
                    }
                    // ------------------------------------
                    
                    document.getElementById('emp-comps').value = emp.comps ? emp.comps.join(', ') : '';
                    toggleCertMode();
                    if (emp.type === 'Direct') {
                        if (emp.certs) {
                            emp.certs.forEach(c => {
                                const idx = TARGET_CERTS.indexOf(c.name);
                                if(idx !== -1) { document.getElementById(`cb-cert-${idx}`).checked = true; toggleDirectDetails(idx); document.getElementById(`date-cert-${idx}`).value = c.date || ''; document.getElementById(`exp-cert-${idx}`).value = c.expiry || ''; document.getElementById(`iss-cert-${idx}`).value = c.issuer || ''; }
                            });
                        }
                        if (emp.trainings) {
                            TARGET_TRAININGS.forEach((trainName, idx) => {
                                const matchedTrains = emp.trainings.filter(t => t.name === trainName);
                                if(matchedTrains.length > 0) {
                                    document.getElementById(`cb-train-${idx}`).checked = true;
                                    document.getElementById(`details-train-${idx}`).classList.remove('hidden');
                                    document.getElementById(`train-dates-container-${idx}`).innerHTML = '';
                                    matchedTrains.forEach(t => addDirectTrainDate(idx, t.date || ''));
                                }
                            });
                        }
                    } else {
                        if(emp.certs && emp.certs.length > 0) emp.certs.forEach(c => addIndirectCertRow(c)); else addIndirectCertRow();
                        if(emp.trainings && emp.trainings.length > 0) emp.trainings.forEach(t => addIndirectTrainRow(t)); else addIndirectTrainRow();
                    }
                }
            } else {
                document.getElementById('form-mode').value = 'add'; document.getElementById('form-modal-title').innerHTML = "Tambah Data Karyawan";
                document.getElementById('emp-id').readOnly = false; document.getElementById('emp-id').classList.remove('bg-slate-100');
                document.getElementById('emp-type').value = 'Direct'; toggleCertMode();
            }
            openModal('modal-form');
        }

        function saveEmployee(e) {
            e.preventDefault();
            const id = document.getElementById('emp-id').value.trim().toUpperCase();
            const name = document.getElementById('emp-name').value.trim();
            const dept = document.getElementById('emp-dept').value.trim();
            const type = document.getElementById('emp-type').value;
            const joinDate = document.getElementById('emp-join-date').value;
            const permDate = document.getElementById('emp-permanent-date').value;
            
            // --- Mengambil nilai URL dari Data gambar file bukan input manual ---
            const photoUrl = document.getElementById('emp-photo-url').value; 
            // ------------------------------------------------------------------
            
            const comps = document.getElementById('emp-comps').value ? document.getElementById('emp-comps').value.split(',').map(s => s.trim()).filter(s => s) : [];

            let certs = []; let trainings = [];
            if (type === 'Direct') {
                TARGET_CERTS.forEach((certName, idx) => {
                    if(document.getElementById(`cb-cert-${idx}`) && document.getElementById(`cb-cert-${idx}`).checked) {
                        certs.push({ name: certName, date: document.getElementById(`date-cert-${idx}`).value, expiry: document.getElementById(`exp-cert-${idx}`).value, issuer: document.getElementById(`iss-cert-${idx}`).value.trim() });
                    }
                });
                TARGET_TRAININGS.forEach((trainName, idx) => {
                    if(document.getElementById(`cb-train-${idx}`) && document.getElementById(`cb-train-${idx}`).checked) {
                        document.querySelectorAll(`.direct-train-date-${idx}`).forEach(inp => { if(inp.value) trainings.push({ name: trainName, date: inp.value }); });
                    }
                });
            } else {
                document.querySelectorAll('[id^="ind-row-"]').forEach(row => {
                    const rowId = row.id.split('-')[2]; const cName = document.getElementById(`ind-name-${rowId}`).value.trim();
                    if(cName) certs.push({ name: cName, date: document.getElementById(`ind-date-${rowId}`).value, expiry: document.getElementById(`ind-exp-${rowId}`).value, issuer: document.getElementById(`ind-iss-${rowId}`).value.trim() });
                });
                document.querySelectorAll('[id^="ind-train-row-"]').forEach(row => {
                    const rowId = row.id.split('-')[3]; const tName = document.getElementById(`ind-train-name-${rowId}`).value.trim();
                    if(tName) trainings.push({ name: tName, date: document.getElementById(`ind-train-date-${rowId}`).value });
                });
            }

            const newData = { id, name, department: dept, type, joinDate, permanentDate: permDate, photoUrl, comps, certs, trainings };
            const existingIndex = employees.findIndex(emp => emp.id === id);

            if (existingIndex >= 0) { employees[existingIndex] = newData; showToast(`Data ${name} berhasil diperbarui.`); } 
            else { employees.push(newData); showToast(`Karyawan ${name} berhasil ditambahkan.`); }
            
            saveData(); renderTable(); renderDashboard(); closeModal('modal-form');
        }

        function deleteEmployee(id) {
            if(confirm(`Yakin ingin menghapus karyawan dengan ID ${id} secara manual (tanpa melalui QAS)?`)) {
                const empIdx = employees.findIndex(emp => emp.id === id);
                if(empIdx > -1) {
                    const emp = employees[empIdx];
                    emp.deletedDate = new Date().toISOString();
                    emp.deleteReason = "Dihapus manual oleh HR";
                    historyKaryawan.push(emp);
                    employees.splice(empIdx, 1);
                }
                saveData(); renderTable(); renderDashboard(); renderHistoryTable();
                showToast('Data berhasil dihapus dan dipindahkan ke History.');
            }
        }

        // ================= IMPORT EXCEL KARYAWAN =================
        function handleExcelImport(event) {
            const file = event.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function(e) {
                try {
                    const data = new Uint8Array(e.target.result);
                    const workbook = XLSX.read(data, {type: 'array'});
                    const ws = workbook.Sheets[workbook.SheetNames[0]];
                    const json = XLSX.utils.sheet_to_json(ws);
                    let countAdded = 0;

                    json.forEach(row => {
                        const id = (row['ID'] || '').toString().trim().toUpperCase();
                        const name = (row['Nama'] || '').toString().trim();
                        if (!id || !name) return;

                        const dept = (row['Departemen'] || '').toString().trim();
                        const type = (row['Tipe Karyawan'] || 'Direct').toString().trim();
                        
                        let joinDate = row['Tgl Masuk'] || ''; let permDate = row['Tgl Tetap'] || '';
                        if(typeof joinDate === 'number') joinDate = new Date(Math.round((joinDate - 25569)*86400*1000)).toISOString().split('T')[0];
                        if(typeof permDate === 'number') permDate = new Date(Math.round((permDate - 25569)*86400*1000)).toISOString().split('T')[0];

                        const comps = (row['Kompetensi'] || '').toString() ? (row['Kompetensi'] || '').toString().split(',').map(s => s.trim()).filter(s => s) : [];

                        const rawCerts = (row['Data Sertifikasi'] || '').toString().trim();
                        const certs = [];
                        if (rawCerts) {
                            rawCerts.split(';').forEach(cStr => {
                                const parts = cStr.split('|').map(s => s.trim());
                                if(parts[0]) certs.push({ name: parts[0], date: parts[1] || '', expiry: parts[2] || '', issuer: parts[3] || '' });
                            });
                        }

                        // Parse Excel "Riwayat Training" (Format: "Final Checker|2023-01-01,2024-01-01; Produk|2023-05-05")
                        const rawTrains = (row['Riwayat Training'] || '').toString().trim();
                        const trainings = [];
                        if (rawTrains) {
                            rawTrains.split(';').forEach(tStr => {
                                const parts = tStr.split('|').map(s => s.trim());
                                if(parts[0] && parts[1]) {
                                    parts[1].split(',').forEach(d => { trainings.push({ name: parts[0], date: d.trim() }); });
                                }
                            });
                        }

                        const existingIndex = employees.findIndex(emp => emp.id === id);
                        const newData = { id, name, department: dept, type, joinDate, permanentDate: permDate, photoUrl: '', comps, certs, trainings: trainings };
                        if (existingIndex >= 0) employees[existingIndex] = newData; else employees.push(newData);
                        countAdded++;
                    });

                    saveData(); renderTable(); renderDashboard();
                    if(countAdded > 0) showToast(`Berhasil mengimpor ${countAdded} data.`); else showToast("Format Excel tidak sesuai.");
                } catch (error) { showToast("Gagal memproses Excel."); }
            };
            reader.readAsArrayBuffer(file);
            event.target.value = '';
        }

        function downloadExcelTemplate() {
            const wb = XLSX.utils.book_new();
            const sampleData = [{ 
                'ID': '80028', 'Nama': 'Rizki Hidayat', 'Departemen': 'Production', 'Tipe Karyawan': 'Direct', 
                'Tgl Masuk': '2020-05-10', 'Tgl Tetap': '2021-05-10', 'Kompetensi': 'Mesin Injection A, Assembly Line 1', 
                'Data Sertifikasi': 'Final Checker|2024-05-10|2025-05-10|PT Indonesia Stanley; Shipping Approval|2023-01-01|2024-01-01|Internal',
                'Riwayat Training': 'Final Checker|2023-04-10,2024-04-10; Shipping Approval|2022-12-15'
            }];
            const ws = XLSX.utils.json_to_sheet(sampleData);
            ws['!cols'] = [{wch: 10}, {wch: 25}, {wch: 20}, {wch: 15}, {wch: 15}, {wch: 15}, {wch: 35}, {wch: 80}, {wch: 70}];
            XLSX.utils.book_append_sheet(wb, ws, 'Template Import'); XLSX.writeFile(wb, 'Template_CertiTrack_v5.xlsx');
        }

        // ================= ID CARD & PROFILE =================
        function openIDCard(empId) {
            const emp = employees.find(e => String(e.id) === String(empId));
            if(!emp) return;

            document.getElementById('idcard-name').textContent = emp.name;
            document.getElementById('idcard-id').textContent = emp.id;
            
            const photoEl = document.getElementById('idcard-photo');
            const silhouette = document.getElementById('idcard-silhouette');

            // Reset tampilan ke default (Siluet)
            photoEl.style.display = 'none';
            if(silhouette) silhouette.style.display = 'block';

            // Menampilkan Foto di ID Card (Jika ada)
            if(emp.photoUrl && emp.photoUrl.trim() !== "") {
                // Pasang onload SEBELUM memasukkan src untuk menghindari race condition
                photoEl.onload = () => {
                    photoEl.style.display = 'block';
                    if(silhouette) silhouette.style.display = 'none';
                };
                photoEl.onerror = () => {
                    photoEl.style.display = 'none';
                    if(silhouette) silhouette.style.display = 'block';
                };
                photoEl.src = emp.photoUrl;
            }

            const qrContainer = document.getElementById('idcard-qr');
            qrContainer.innerHTML = '';
            
            // --- PERBAIKAN UTAMA ---
            // Buat salinan data karyawan dan hapus fotonya khusus untuk payload QR Code
            // Agar ukuran data tidak melebihi batas maksimal kapasitas QR Code.
            const empForQR = { ...emp };
            delete empForQR.photoUrl; 
            // -----------------------

            try {
                const payload = btoa(encodeURIComponent(JSON.stringify(empForQR))); 
                new QRCode(qrContainer, { 
                    text: `${window.location.origin + window.location.pathname}?profile=${payload}`, 
                    width: 400, height: 400, colorDark: "#1e293b", colorLight: "#ffffff", correctLevel: QRCode.CorrectLevel.M 
                });
            } catch(e) {
                console.error("Gagal membuat QR Code:", e);
            }

            setTimeout(() => {
                const qrCanvas = qrContainer.querySelector('canvas');
                const qrImg = qrContainer.querySelector('img');
                if (qrCanvas) { qrCanvas.style.width = '100%'; qrCanvas.style.height = '100%'; }
                if (qrImg) { qrImg.style.width = '100%'; qrImg.style.height = '100%'; }
            }, 10);

            openModal('modal-idcard');
        }

        function showProfile(empIdOrData) {
            let emp = (typeof empIdOrData === 'object') ? empIdOrData : employees.find(e => e.id === empIdOrData.toString().toUpperCase());
            if(!emp) { showToast("Karyawan tidak ditemukan."); return; }

            if (typeof empIdOrData === 'object') {
                const localEmp = employees.find(e => e.id === emp.id);
                if (localEmp && localEmp.photoUrl) {
                    emp.photoUrl = localEmp.photoUrl;
                }
            }

            document.getElementById('profile-name').textContent = emp.name;
            document.getElementById('profile-id-dept').textContent = `${emp.id} • ${emp.department}`;
            document.getElementById('profile-type').textContent = emp.type || 'DIRECT';
            document.getElementById('profile-join-date').textContent = emp.joinDate ? new Date(emp.joinDate).toLocaleDateString('id-ID') : '-';
            document.getElementById('profile-perm-date').textContent = emp.permanentDate ? new Date(emp.permanentDate).toLocaleDateString('id-ID') : '-';

            const photoEl = document.getElementById('profile-photo');
            if (emp.photoUrl) { photoEl.src = emp.photoUrl; photoEl.style.display = 'block'; photoEl.nextElementSibling.style.display = 'none'; } 
            else { photoEl.style.display = 'none'; photoEl.nextElementSibling.style.display = 'block'; }

            const compsContainer = document.getElementById('profile-comps');
            const noCompMsg = document.getElementById('profile-no-comp');
            compsContainer.innerHTML = '';
            if (emp.comps && emp.comps.length > 0) {
                noCompMsg.classList.add('hidden');
                emp.comps.forEach(comp => compsContainer.insertAdjacentHTML('beforeend', `<li class="text-sm font-medium text-slate-700 flex items-center gap-2"><i data-lucide="check" class="w-4 h-4 text-indigo-500"></i> ${comp}</li>`));
            } else { noCompMsg.classList.remove('hidden'); }

            const certsContainer = document.getElementById('profile-certs');
            const noCertMsg = document.getElementById('profile-no-cert');
            certsContainer.innerHTML = '';
            if (emp.certs && emp.certs.length > 0) {
                noCertMsg.classList.add('hidden');
                emp.certs.forEach(cert => {
                    const statusObj = getCertStatus(cert.expiry);
                    const isExp = statusObj.status === 'Expired';
                    const isWarning = statusObj.status === 'Warning';
                    
                    // Tentukan Warna Garis Samping dan Ikon
                    let sideColor = 'bg-emerald-500';
                    let iconName = 'check-circle-2';
                    if (isExp) { sideColor = 'bg-red-500'; iconName = 'alert-circle'; }
                    else if (isWarning) { sideColor = 'bg-amber-500'; iconName = 'clock'; }

                    certsContainer.insertAdjacentHTML('beforeend', `
                        <div class="p-4 bg-white border border-slate-200 rounded-xl shadow-sm relative overflow-hidden">
                            <div class="absolute top-0 right-0 w-2 h-full ${sideColor}"></div>
                            <div class="flex justify-between items-start mb-2 pr-2">
                                <h5 class="font-bold text-slate-800 leading-tight">${cert.name}</h5>
                                <span class="text-[10px] uppercase font-bold px-2 py-0.5 rounded border flex items-center gap-1 ${statusObj.class}"><i data-lucide="${iconName}" class="w-3 h-3"></i> ${statusObj.text}</span>
                            </div>
                            <div class="space-y-1 mt-3 text-xs">
                                <div class="flex justify-between border-b border-slate-100 pb-1"><span class="text-slate-500">Masa Berlaku:</span><span class="font-bold ${isExp ? 'text-red-600' : (isWarning ? 'text-amber-600' : 'text-slate-700')}">${cert.expiry ? new Date(cert.expiry).toLocaleDateString('id-ID') : 'Seumur Hidup'}</span></div>
                                <div class="flex justify-between pt-1"><span class="text-slate-500">Pemberi:</span><span class="font-bold text-slate-700 truncate max-w-[120px]">${cert.issuer || '-'}</span></div>
                            </div>
                        </div>
                    `);
                });
            } else { noCertMsg.classList.remove('hidden'); }

            const trainsContainer = document.getElementById('profile-trains');
            const noTrainMsg = document.getElementById('profile-no-train');
            trainsContainer.innerHTML = '';
            if (emp.trainings && emp.trainings.length > 0) {
                noTrainMsg.classList.add('hidden');
                const sortedTrains = [...emp.trainings].sort((a,b) => new Date(b.date) - new Date(a.date));
                sortedTrains.forEach(train => {
                    trainsContainer.insertAdjacentHTML('beforeend', `
                        <div class="p-3 bg-white border border-slate-200 rounded-lg shadow-sm flex justify-between items-center">
                            <h5 class="font-bold text-sm text-slate-800">${train.name}</h5>
                            <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-2 py-1 rounded border border-blue-100">${train.date ? new Date(train.date).toLocaleDateString('id-ID') : '-'}</span>
                        </div>
                    `);
                });
            } else { noTrainMsg.classList.remove('hidden'); }

            lucide.createIcons(); openModal('modal-profile');
        }

        // ================= SCANNER =================
        function startScanner() {
            if (html5QrcodeScanner) return;
            html5QrcodeScanner = new Html5Qrcode("reader");
            html5QrcodeScanner.start({ facingMode: "environment" }, { fps: 10, qrbox: { width: 250, height: 250 } }, (decodedText) => {
                let profileData = decodedText;
                try {
                    const parsedUrl = new URL(decodedText);
                    const profileParam = parsedUrl.searchParams.get('profile');
                    if (profileParam) profileData = JSON.parse(decodeURIComponent(atob(profileParam)));
                } catch(e) {}
                if (navigator.vibrate) navigator.vibrate(200);
                html5QrcodeScanner.stop().then(() => { html5QrcodeScanner = null; showProfile(profileData); }).catch(e => console.log(e));
            }, () => {}).catch(() => {
                document.getElementById('reader').innerHTML = `<div class="p-8 text-center text-red-500"><p class="font-bold">Kamera tidak dapat diakses.</p></div>`;
            });
        }
        function stopScanner() { if (html5QrcodeScanner) html5QrcodeScanner.stop().then(() => { html5QrcodeScanner = null; }); }
        function simulateScan() {
            const inputVal = document.getElementById('manual-scan-id').value.trim();
            if(inputVal) { showProfile(inputVal); document.getElementById('manual-scan-id').value = ''; } 
            else showToast("Masukkan ID terlebih dahulu.");
        }

        // ================= UTILS =================
        function openModal(id) {
            const modal = document.getElementById(id);
            modal.classList.remove('hidden');
            setTimeout(() => { modal.classList.remove('opacity-0'); modal.firstElementChild.classList.remove('scale-95'); modal.firstElementChild.classList.add('scale-100'); }, 10);
        }
        function closeModal(id) {
            const modal = document.getElementById(id);
            modal.classList.add('opacity-0');
            modal.firstElementChild.classList.remove('scale-100'); modal.firstElementChild.classList.add('scale-95');
            setTimeout(() => { modal.classList.add('hidden'); if(id === 'modal-profile' && !document.getElementById('tab-scanner').classList.contains('hidden')) startScanner(); }, 300);
        }
        function showToast(message) {
            const toast = document.getElementById('toast');
            document.getElementById('toast-msg').textContent = message;
            toast.classList.remove('translate-y-20', 'opacity-0');
            setTimeout(() => toast.classList.add('translate-y-20', 'opacity-0'), 3000);
        }
    </script>
</body>
</html>
