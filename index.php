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
            #print-area, #print-area * { visibility: visible; }
            @page { size: landscape; margin: 0; }
            #print-area { 
                position: absolute; 
                left: 0; 
                top: 0; 
                width: 340px; 
                height: 214px;
                border: none !important;
                box-shadow: none !important;
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
                    <div class="mb-8">
                        <h1 class="text-2xl md:text-3xl font-bold text-slate-800">Dashboard Sistem</h1>
                        <p class="text-slate-500 mt-1">Ringkasan statistik karyawan dan status sertifikasi.</p>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-600"><i data-lucide="users" class="w-6 h-6"></i></div>
                            <div><p class="text-sm font-semibold text-slate-500">Total Karyawan</p><h3 class="text-2xl font-bold text-slate-800" id="dash-total-emp">0</h3></div>
                        </div>
                        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600"><i data-lucide="briefcase" class="w-6 h-6"></i></div>
                            <div><p class="text-sm font-semibold text-slate-500">Direct Produksi</p><h3 class="text-2xl font-bold text-slate-800" id="dash-direct-emp">0</h3></div>
                        </div>
                        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600"><i data-lucide="check-circle" class="w-6 h-6"></i></div>
                            <div><p class="text-sm font-semibold text-slate-500">Sertifikasi Aktif</p><h3 class="text-2xl font-bold text-slate-800" id="dash-active-cert">0</h3></div>
                        </div>
                        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center text-red-600"><i data-lucide="alert-triangle" class="w-6 h-6"></i></div>
                            <div><p class="text-sm font-semibold text-slate-500">Sertifikasi Expired</p><h3 class="text-2xl font-bold text-slate-800" id="dash-expired-cert">0</h3></div>
                        </div>
                    </div>
                </section>

                <section id="tab-data" class="hidden fade-in max-w-7xl mx-auto">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
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

                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse min-w-[1000px]">
                                <thead>
                                    <tr class="bg-slate-50 text-slate-600 border-b border-slate-200 text-sm">
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
                        <div id="empty-state" class="hidden text-center py-16">
                            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-slate-100">
                                <i data-lucide="users" class="w-10 h-10 text-slate-300"></i>
                            </div>
                            <p class="text-slate-600 font-medium text-lg">Belum ada data karyawan.</p>
                        </div>
                    </div>
                </section>

                <section id="tab-scanner" class="hidden fade-in max-w-4xl mx-auto">
                    <div class="text-center mb-8">
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

    <!-- Modal Form Tambah/Edit Karyawan -->
    <div id="modal-form" class="fixed inset-0 bg-slate-900/60 hidden z-[100] flex items-center justify-center p-4 opacity-0 transition-opacity backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-5xl max-h-[90vh] flex flex-col transform scale-95 transition-transform duration-300 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-white">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center">
                        <i data-lucide="file-edit" class="w-5 h-5 text-blue-600"></i>
                    </div>
                    <h3 class="font-bold text-lg text-slate-800" id="form-modal-title">Form Data Karyawan</h3>
                </div>
                <button onclick="closeModal('modal-form')" class="text-slate-400 hover:bg-slate-100 hover:text-red-500 p-2 rounded-lg transition"><i data-lucide="x"></i></button>
            </div>
            
            <div class="px-6 py-6 overflow-y-auto flex-1 bg-slate-50/50">
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
                                        <label class="block text-xs font-bold text-slate-600 mb-1">URL Foto Profil</label>
                                        <input type="url" id="emp-photo" placeholder="https://..." class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm bg-slate-50">
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
                        <div class="lg:col-span-8 bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex flex-col">
                            <div class="flex justify-between items-end border-b pb-3 mb-4">
                                <div>
                                    <h4 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Detail Sertifikasi</h4>
                                    <p class="text-xs text-slate-500 mt-1" id="cert-hint">Pilih sertifikasi yang dimiliki oleh karyawan Direct.</p>
                                </div>
                                <button type="button" id="btn-add-cert-row" onclick="addIndirectCertRow()" class="hidden bg-emerald-100 text-emerald-700 hover:bg-emerald-200 px-3 py-1.5 rounded text-xs font-bold transition flex items-center gap-1">
                                    <i data-lucide="plus-circle" class="w-3.5 h-3.5"></i> Tambah Baris
                                </button>
                            </div>

                            <!-- Area Khusus Karyawan Direct (Checkboxes + Expandable Inputs) -->
                            <div id="cert-direct-area" class="space-y-3 overflow-y-auto flex-1 pr-2 max-h-[500px]">
                                <!-- Di-generate via JS -->
                            </div>

                            <!-- Area Khusus Karyawan Semi/In-Direct (Dynamic Text Rows) -->
                            <div id="cert-indirect-area" class="hidden space-y-3 overflow-y-auto flex-1 pr-2 max-h-[500px]">
                                <!-- Di-generate via JS -->
                            </div>
                        </div>
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

    <div id="modal-idcard" class="fixed inset-0 bg-slate-900/70 hidden z-[100] flex items-center justify-center p-4 opacity-0 transition-opacity backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-xl overflow-hidden transform scale-95 transition-transform duration-300">
            <div class="px-5 py-4 border-b border-slate-100 flex justify-between items-center bg-white">
                <h3 class="font-bold text-slate-800 flex items-center gap-2">
                    <i data-lucide="badge-check" class="w-5 h-5 text-blue-600"></i> ID Card Preview
                </h3>
                <button onclick="closeModal('modal-idcard')" class="text-slate-400 hover:text-red-500 transition bg-slate-50 hover:bg-red-50 p-1.5 rounded-lg"><i data-lucide="x"></i></button>
            </div>
            
            <div class="p-8 bg-slate-100 flex justify-center items-center overflow-auto">
                <!-- Desain ID Card Landscape -->
                <div id="print-area" class="bg-white rounded-lg shadow-xl overflow-hidden relative border border-gray-200" style="width: 450px; height: 280px;">
                    <!-- Header -->
                    <div class="h-[60px] w-full bg-slate-100 flex items-center px-4 border-b-4 border-slate-300/30">
                        <div class="flex items-center justify-between w-full">
                            <div class="flex items-center">
                                <span class="text-[#f15a24] font-black text-3xl tracking-tighter" style="font-family: Arial, sans-serif; transform: scaleY(1.1); text-shadow: 1px 1px 0px rgba(0,0,0,0.1);">STANLEY</span>
                            </div>
                            <span class="text-slate-600 font-bold text-[14px] tracking-wide" style="font-family: Arial, sans-serif;">PT INDONESIA STANLEY ELECTRIC</span>
                        </div>
                    </div>
                    
                    <!-- Content Area -->
                    <div class="flex h-[220px]">
                        <!-- Left: Photo Background & Photo -->
                        <div class="w-[150px] pl-4 pt-4 pb-4">
                            <div class="w-full h-full bg-[#9e1b1b] relative overflow-hidden flex items-end justify-center rounded-sm border border-slate-200" id="idcard-photo-container">
                                <img id="idcard-photo" src="" alt="Foto" class="w-full h-full object-cover z-10" style="display:none;" onerror="this.style.display='none'; document.getElementById('idcard-silhouette').style.display='block';">
                                <i data-lucide="user" id="idcard-silhouette" class="w-24 h-24 text-white/50 absolute bottom-0"></i>
                            </div>
                        </div>
                        
                        <!-- Right: Info & QR Code -->
                        <div class="flex-1 flex flex-col justify-start pl-6 pr-4 pt-5">
                            <h4 id="idcard-name" class="font-bold text-[26px] text-slate-800 leading-none tracking-tight uppercase" style="font-family: 'Arial Narrow', Arial, sans-serif; word-wrap: break-word;">NAMA</h4>
                            <p id="idcard-id" class="text-[32px] text-slate-800 font-bold mt-1 tracking-wider" style="font-family: 'Arial Narrow', Arial, sans-serif;">80028</p>
                            
                            <div class="mt-4 flex items-center gap-3">
                                <div id="idcard-qr" class="p-1 bg-white border border-slate-300"></div>
                                <div>
                                    <span id="idcard-type" class="inline-block mb-1 text-[9px] font-bold px-1.5 py-0.5 rounded bg-blue-100 text-blue-800 uppercase tracking-wider">DIRECT</span>
                                    <p class="text-[9px] font-bold text-slate-500 leading-tight">SCAN UNTUK CEK<br>PROFIL & SERTIFIKASI</p>
                                </div>
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

    <!-- Modal Profil Karyawan (Halaman Khusus Hasil Scan) -->
    <div id="modal-profile" class="fixed inset-0 bg-slate-900/70 hidden z-[100] flex items-center justify-center p-4 opacity-0 transition-opacity backdrop-blur-sm">
        <div class="bg-slate-50 rounded-2xl shadow-2xl w-full max-w-4xl max-h-[95vh] overflow-hidden transform scale-95 transition-transform duration-300 flex flex-col">
            
            <!-- Header Profil Baru -->
            <div class="bg-blue-800 px-6 py-6 relative shrink-0">
                <button onclick="closeModal('modal-profile')" class="absolute top-4 right-4 text-blue-200 hover:text-white bg-black/10 hover:bg-black/20 p-2 rounded-full transition z-10"><i data-lucide="x" class="w-5 h-5"></i></button>
                
                <div class="flex flex-col sm:flex-row items-center sm:items-start gap-5">
                    <!-- Foto Profil -->
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
            
            <!-- Body Profil Scrollable -->
            <div class="p-6 overflow-y-auto flex-1 custom-scrollbar">
                
                <!-- Info Status Kerja & Kompetensi -->
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

                <!-- Sertifikasi Detail -->
                <div>
                    <h4 class="font-bold text-slate-800 border-b border-slate-200 pb-2 mb-4 flex items-center gap-2">
                        <i data-lucide="award" class="w-5 h-5 text-amber-500"></i> Detail Sertifikasi
                    </h4>
                    <div id="profile-certs" class="grid grid-cols-1 sm:grid-cols-2 gap-4"></div>
                    <p id="profile-no-cert" class="hidden text-sm text-slate-500 italic text-center py-6 bg-white border border-dashed rounded-xl">Belum ada sertifikasi yang tercatat.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- UI Bantuan (Toast & Dialog) -->
    <div id="toast" class="fixed bottom-5 right-5 bg-slate-800 text-white px-5 py-3.5 rounded-xl shadow-2xl transform translate-y-20 opacity-0 transition-all duration-300 z-[200] flex items-center gap-3 border border-slate-700">
        <div class="w-8 h-8 rounded-full bg-blue-500/20 flex items-center justify-center">
            <i data-lucide="info" class="w-4 h-4 text-blue-400"></i>
        </div>
        <span id="toast-msg" class="text-sm font-medium">Notifikasi</span>
    </div>

    <script>
        lucide.createIcons();

        // Konstanta untuk Karyawan Direct
        const TARGET_CERTS = [
            "Final Checker", "Pemeriksa Produk", "Pemeriksa Dalam Proses", 
            "Jouho Board", "Trainer Proses Penting", "Trainer Proses Khusus", "Shipping Approval"
        ];

        let employees = [];
        let html5QrcodeScanner = null;
        let indirectCertRowCount = 0;

        // Data Management Dasar
        document.addEventListener('DOMContentLoaded', () => {
            loadData();
            buildDirectCertCheckboxes();
            renderDashboard();
            renderTable();
            
            // Cek jika dibuka via scan QR dari HP
            const urlParams = new URLSearchParams(window.location.search);
            const profileParam = urlParams.get('profile');
            if (profileParam) {
                try {
                    const profileData = JSON.parse(decodeURIComponent(atob(profileParam)));
                    setTimeout(() => showProfile(profileData), 300);
                } catch(e) {
                    showToast("QR Code tidak valid atau rusak.");
                }
            }
        });

        function loadData() {
            const saved = localStorage.getItem('certiTrackData_v4'); // Gunakan key baru krn struktur berubah
            if (saved) {
                employees = JSON.parse(saved);
            } else {
                // Data Default (1 Direct, 1 In-Direct)
                employees = [
                    { 
                        id: "80028", name: "Rizki Hidayat", department: "Production", type: "Direct",
                        joinDate: "2020-05-10", permanentDate: "2021-05-10", photoUrl: "",
                        comps: ["Mesin Injection A", "Assembly Line 1"],
                        certs: [
                            { name: "Final Checker", date: "2024-05-10", expiry: "2025-05-10", issuer: "PT Indonesia Stanley Electric" },
                            { name: "Shipping Approval", date: "2023-01-01", expiry: "2023-12-31", issuer: "Internal" } // Expired example
                        ] 
                    },
                    { 
                        id: "IND-001", name: "Sarah Wijaya", department: "Maintenance", type: "In-Direct",
                        joinDate: "2019-02-15", permanentDate: "2020-02-15", photoUrl: "",
                        comps: ["Electrical Repair", "PLC Programming"],
                        certs: [
                            { name: "Sertifikasi K3 Listrik Umum", date: "2024-01-10", expiry: "2027-01-10", issuer: "Kemnaker RI" }
                        ] 
                    }
                ];
                saveData();
            }
        }

        function saveData() {
            localStorage.setItem('certiTrackData_v4', JSON.stringify(employees));
        }

        // ================= UI & TAB LOGIC =================
        function switchTab(tabId) {
            document.getElementById('tab-dashboard').classList.add('hidden');
            document.getElementById('tab-data').classList.add('hidden');
            document.getElementById('tab-scanner').classList.add('hidden');
            document.getElementById('tab-' + tabId).classList.remove('hidden');

            ['dashboard', 'data', 'scanner'].forEach(id => {
                const btn = document.getElementById('nav-' + id);
                if(btn) {
                    if(id === tabId) {
                        btn.classList.add('bg-blue-700', 'text-white');
                        btn.classList.remove('text-blue-200');
                    } else {
                        btn.classList.remove('bg-blue-700', 'text-white');
                        btn.classList.add('text-blue-200');
                    }
                }
            });

            if (tabId === 'scanner') startScanner(); else stopScanner();
            renderDashboard(); renderTable();
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

        // ================= STATUS CALCULATION =================
        // Menghitung status Active / Expired berdasarkan tanggal
        function getCertStatus(expiryDateStr) {
            if (!expiryDateStr) return { status: 'Aktif', class: 'bg-emerald-100 text-emerald-700 border-emerald-200' };
            const today = new Date();
            today.setHours(0,0,0,0);
            const expiry = new Date(expiryDateStr);
            if (expiry >= today) {
                return { status: 'Aktif', class: 'bg-emerald-100 text-emerald-700 border-emerald-200' };
            } else {
                return { status: 'Expired', class: 'bg-red-100 text-red-700 border-red-200' };
            }
        }

        // ================= DASHBOARD & TABLE =================
        function renderDashboard() {
            document.getElementById('dash-total-emp').textContent = employees.length;
            
            const directCount = employees.filter(e => e.type === 'Direct').length;
            document.getElementById('dash-direct-emp').textContent = directCount;

            let activeCount = 0; let expCount = 0;
            employees.forEach(emp => {
                if(emp.certs) {
                    emp.certs.forEach(c => {
                        if(getCertStatus(c.expiry).status === 'Aktif') activeCount++; else expCount++;
                    });
                }
            });
            document.getElementById('dash-active-cert').textContent = activeCount;
            document.getElementById('dash-expired-cert').textContent = expCount;
        }

        function renderTable() {
            const tbody = document.getElementById('employee-table-body');
            const emptyState = document.getElementById('empty-state');
            tbody.innerHTML = '';
            
            if (employees.length === 0) {
                emptyState.classList.remove('hidden');
            } else {
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
                // Wrapper per sertifikasi
                container.insertAdjacentHTML('beforeend', `
                    <div class="border border-slate-200 rounded-lg p-3 bg-white hover:border-blue-300 transition">
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
            if(cb.checked) {
                details.classList.remove('hidden');
            } else {
                details.classList.add('hidden');
                // Clear inputs if unchecked
                document.getElementById(`date-cert-${index}`).value = '';
                document.getElementById(`exp-cert-${index}`).value = '';
                document.getElementById(`iss-cert-${index}`).value = '';
            }
        }

        function addIndirectCertRow(data = null) {
            const container = document.getElementById('cert-indirect-area');
            const rowId = indirectCertRowCount++;
            
            const nameVal = data ? data.name : '';
            const dateVal = data ? data.date : '';
            const expVal = data ? data.expiry : '';
            const issVal = data ? data.issuer : '';

            container.insertAdjacentHTML('beforeend', `
                <div id="ind-row-${rowId}" class="flex flex-col sm:flex-row gap-2 border border-slate-200 p-3 rounded-lg bg-white relative">
                    <button type="button" onclick="document.getElementById('ind-row-${rowId}').remove()" class="absolute top-2 right-2 text-slate-400 hover:text-red-500 p-1 rounded transition"><i data-lucide="x" class="w-4 h-4"></i></button>
                    
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
            const hint = document.getElementById('cert-hint');
            const btnAdd = document.getElementById('btn-add-cert-row');

            if (type === 'Direct') {
                directArea.classList.remove('hidden');
                indirectArea.classList.add('hidden');
                btnAdd.classList.add('hidden');
                hint.textContent = "Centang dan lengkapi detail sertifikasi wajib untuk karyawan Direct Produksi.";
            } else {
                directArea.classList.add('hidden');
                indirectArea.classList.remove('hidden');
                btnAdd.classList.remove('hidden');
                hint.textContent = `Input sertifikasi secara bebas untuk karyawan ${type}. Klik Tambah Baris.`;
                // Jika kosong, tambah 1 baris default
                if(indirectArea.children.length === 0) addIndirectCertRow();
            }
        }

        // ================= CRUD LOGIC =================
        function openModalForm(empId = null) {
            document.getElementById('employee-form').reset();
            indirectCertRowCount = 0;
            document.getElementById('cert-indirect-area').innerHTML = '';
            
            // Uncheck all direct certs
            TARGET_CERTS.forEach((_, i) => {
                const cb = document.getElementById(`cb-cert-${i}`);
                if(cb) { cb.checked = false; toggleDirectDetails(i); }
            });

            if (empId) {
                // Mode Edit
                document.getElementById('form-mode').value = 'edit';
                document.getElementById('form-modal-title').innerHTML = "Edit Data Karyawan";
                document.getElementById('emp-id').readOnly = true;
                document.getElementById('emp-id').classList.add('bg-slate-100');
                
                const emp = employees.find(e => e.id === empId);
                if (emp) {
                    document.getElementById('emp-id').value = emp.id;
                    document.getElementById('emp-name').value = emp.name;
                    document.getElementById('emp-dept').value = emp.department;
                    document.getElementById('emp-type').value = emp.type || 'Direct';
                    document.getElementById('emp-join-date').value = emp.joinDate || '';
                    document.getElementById('emp-permanent-date').value = emp.permanentDate || '';
                    document.getElementById('emp-photo').value = emp.photoUrl || '';
                    document.getElementById('emp-comps').value = emp.comps ? emp.comps.join(', ') : '';

                    toggleCertMode(); // Setup UI based on type

                    if (emp.type === 'Direct') {
                        // Populate Checkboxes
                        emp.certs.forEach(c => {
                            const idx = TARGET_CERTS.indexOf(c.name);
                            if(idx !== -1) {
                                document.getElementById(`cb-cert-${idx}`).checked = true;
                                toggleDirectDetails(idx);
                                document.getElementById(`date-cert-${idx}`).value = c.date || '';
                                document.getElementById(`exp-cert-${idx}`).value = c.expiry || '';
                                document.getElementById(`iss-cert-${idx}`).value = c.issuer || '';
                            }
                        });
                    } else {
                        // Populate Custom Rows
                        if(emp.certs.length > 0) {
                            emp.certs.forEach(c => addIndirectCertRow(c));
                        } else {
                            addIndirectCertRow();
                        }
                    }
                }
            } else {
                // Mode Add
                document.getElementById('form-mode').value = 'add';
                document.getElementById('form-modal-title').innerHTML = "Tambah Data Karyawan";
                document.getElementById('emp-id').readOnly = false;
                document.getElementById('emp-id').classList.remove('bg-slate-100');
                document.getElementById('emp-type').value = 'Direct';
                toggleCertMode();
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
            const photoUrl = document.getElementById('emp-photo').value.trim();
            
            const rawComps = document.getElementById('emp-comps').value;
            const comps = rawComps ? rawComps.split(',').map(s => s.trim()).filter(s => s) : [];

            let certs = [];
            
            // Harvest Certs based on Type
            if (type === 'Direct') {
                TARGET_CERTS.forEach((certName, idx) => {
                    const cb = document.getElementById(`cb-cert-${idx}`);
                    if(cb && cb.checked) {
                        certs.push({
                            name: certName,
                            date: document.getElementById(`date-cert-${idx}`).value,
                            expiry: document.getElementById(`exp-cert-${idx}`).value,
                            issuer: document.getElementById(`iss-cert-${idx}`).value.trim()
                        });
                    }
                });
            } else {
                // Harvest Indirect rows
                const rows = document.querySelectorAll('[id^="ind-row-"]');
                rows.forEach(row => {
                    const rowId = row.id.split('-')[2];
                    const cName = document.getElementById(`ind-name-${rowId}`).value.trim();
                    if(cName) {
                        certs.push({
                            name: cName,
                            date: document.getElementById(`ind-date-${rowId}`).value,
                            expiry: document.getElementById(`ind-exp-${rowId}`).value,
                            issuer: document.getElementById(`ind-iss-${rowId}`).value.trim()
                        });
                    }
                });
            }

            const newData = { id, name, department: dept, type, joinDate, permanentDate: permDate, photoUrl, comps, certs };
            const existingIndex = employees.findIndex(emp => emp.id === id);

            if (existingIndex >= 0) {
                employees[existingIndex] = newData;
                showToast(`Data ${name} berhasil diperbarui.`);
            } else {
                employees.push(newData);
                showToast(`Karyawan ${name} berhasil ditambahkan.`);
            }
            saveData(); renderTable(); renderDashboard(); closeModal('modal-form');
        }

        function deleteEmployee(id) {
            if(confirm(`Yakin ingin menghapus karyawan dengan ID ${id}?`)) {
                employees = employees.filter(emp => emp.id !== id);
                saveData(); renderTable(); renderDashboard();
                showToast('Data berhasil dihapus.');
            }
        }

        // ================= IMPORT EXCEL =================
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
                        const dept = (row['Departemen'] || '').toString().trim();
                        const type = (row['Tipe Karyawan'] || 'Direct').toString().trim();
                        
                        let joinDate = row['Tgl Masuk'] || '';
                        let permDate = row['Tgl Tetap'] || '';
                        if(typeof joinDate === 'number') joinDate = new Date(Math.round((joinDate - 25569)*86400*1000)).toISOString().split('T')[0];
                        if(typeof permDate === 'number') permDate = new Date(Math.round((permDate - 25569)*86400*1000)).toISOString().split('T')[0];

                        const rawComps = (row['Kompetensi'] || '').toString();
                        const comps = rawComps ? rawComps.split(',').map(s => s.trim()).filter(s => s) : [];

                        // Parsing Sertifikasi
                        // Format Excel: NamaSertif|Tanggal|Expiry|Pemberi ; NamaSertif2|Tanggal|...
                        const rawCerts = (row['Data Sertifikasi'] || '').toString().trim();
                        const certs = [];
                        if (rawCerts) {
                            rawCerts.split(';').forEach(cStr => {
                                const parts = cStr.split('|').map(s => s.trim());
                                if(parts[0]) {
                                    certs.push({
                                        name: parts[0],
                                        date: parts[1] || '',
                                        expiry: parts[2] || '',
                                        issuer: parts[3] || ''
                                    });
                                }
                            });
                        }

                        if (id && name) {
                            const existingIndex = employees.findIndex(emp => emp.id === id);
                            const newData = { id, name, department: dept, type, joinDate, permanentDate: permDate, photoUrl: '', comps, certs };
                            if (existingIndex >= 0) employees[existingIndex] = newData;
                            else employees.push(newData);
                            countAdded++;
                        }
                    });

                    saveData(); renderTable(); renderDashboard();
                    if(countAdded > 0) showToast(`Berhasil mengimpor ${countAdded} data.`);
                    else showToast("Format Excel tidak sesuai.");
                } catch (error) {
                    showToast("Gagal memproses Excel.");
                }
            };
            reader.readAsArrayBuffer(file);
            event.target.value = '';
        }

        function downloadExcelTemplate() {
            const wb = XLSX.utils.book_new();
            const sampleData = [{
                'ID': '80028',
                'Nama': 'Rizki Hidayat',
                'Departemen': 'Production',
                'Tipe Karyawan': 'Direct',
                'Tgl Masuk': '2020-05-10',
                'Tgl Tetap': '2021-05-10',
                'Kompetensi': 'Mesin Injection A, Assembly Line 1',
                'Data Sertifikasi': 'Final Checker|2024-05-10|2025-05-10|PT Indonesia Stanley; Shipping Approval|2023-01-01|2024-01-01|Internal'
            }];
            const ws = XLSX.utils.json_to_sheet(sampleData);
            ws['!cols'] = [{wch: 10}, {wch: 25}, {wch: 20}, {wch: 15}, {wch: 15}, {wch: 15}, {wch: 35}, {wch: 100}];
            XLSX.utils.book_append_sheet(wb, ws, 'Template Import');
            XLSX.writeFile(wb, 'Template_CertiTrack_v4.xlsx');
        }

        // ================= ID CARD & PROFILE =================
        function openIDCard(empId) {
            const emp = employees.find(e => e.id === empId);
            if(!emp) return;

            document.getElementById('idcard-name').textContent = emp.name;
            document.getElementById('idcard-id').textContent = emp.id;
            document.getElementById('idcard-type').textContent = emp.type;
            
            const photoEl = document.getElementById('idcard-photo');
            const silhouette = document.getElementById('idcard-silhouette');
            if(emp.photoUrl) {
                photoEl.src = emp.photoUrl; photoEl.style.display = 'block'; silhouette.style.display = 'none';
            } else {
                photoEl.style.display = 'none'; silhouette.style.display = 'block';
            }

            const qrContainer = document.getElementById('idcard-qr');
            qrContainer.innerHTML = '';
            
            const currentUrl = window.location.origin + window.location.pathname;
            const payload = btoa(encodeURIComponent(JSON.stringify(emp))); // Embed all data
            
            new QRCode(qrContainer, {
                text: `${currentUrl}?profile=${payload}`,
                width: 70, height: 70, colorDark: "#1e293b", colorLight: "#ffffff", correctLevel: QRCode.CorrectLevel.L
            });

            openModal('modal-idcard');
        }

        function showProfile(empIdOrData) {
            let emp = (typeof empIdOrData === 'object') ? empIdOrData : employees.find(e => e.id === empIdOrData.toString().toUpperCase());
            if(!emp) { showToast("Karyawan tidak ditemukan."); return; }

            document.getElementById('profile-name').textContent = emp.name;
            document.getElementById('profile-id-dept').textContent = `${emp.id} • ${emp.department}`;
            document.getElementById('profile-type').textContent = emp.type || 'DIRECT';
            document.getElementById('profile-join-date').textContent = emp.joinDate ? new Date(emp.joinDate).toLocaleDateString('id-ID') : '-';
            document.getElementById('profile-perm-date').textContent = emp.permanentDate ? new Date(emp.permanentDate).toLocaleDateString('id-ID') : '-';

            const photoEl = document.getElementById('profile-photo');
            if (emp.photoUrl) {
                photoEl.src = emp.photoUrl; photoEl.style.display = 'block'; photoEl.nextElementSibling.style.display = 'none';
            } else {
                photoEl.style.display = 'none'; photoEl.nextElementSibling.style.display = 'block';
            }

            // Bind Kompetensi
            const compsContainer = document.getElementById('profile-comps');
            const noCompMsg = document.getElementById('profile-no-comp');
            compsContainer.innerHTML = '';
            if (emp.comps && emp.comps.length > 0) {
                noCompMsg.classList.add('hidden');
                emp.comps.forEach(comp => {
                    compsContainer.insertAdjacentHTML('beforeend', `<li class="text-sm font-medium text-slate-700 flex items-center gap-2"><i data-lucide="check" class="w-4 h-4 text-indigo-500"></i> ${comp}</li>`);
                });
            } else { noCompMsg.classList.remove('hidden'); }

            // Bind Sertifikasi
            const certsContainer = document.getElementById('profile-certs');
            const noCertMsg = document.getElementById('profile-no-cert');
            certsContainer.innerHTML = '';
            if (emp.certs && emp.certs.length > 0) {
                noCertMsg.classList.add('hidden');
                emp.certs.forEach(cert => {
                    const statusObj = getCertStatus(cert.expiry);
                    const isExp = statusObj.status === 'Expired';
                    const iconType = isExp ? 'alert-circle' : 'check-circle-2';
                    const expText = cert.expiry ? new Date(cert.expiry).toLocaleDateString('id-ID') : 'Seumur Hidup';

                    certsContainer.insertAdjacentHTML('beforeend', `
                        <div class="p-4 bg-white border border-slate-200 rounded-xl shadow-sm relative overflow-hidden">
                            <div class="absolute top-0 right-0 w-2 h-full ${isExp ? 'bg-red-500' : 'bg-emerald-500'}"></div>
                            <div class="flex justify-between items-start mb-2 pr-2">
                                <h5 class="font-bold text-slate-800 leading-tight">${cert.name}</h5>
                                <span class="text-[10px] uppercase font-bold px-2 py-0.5 rounded border flex items-center gap-1 ${statusObj.class}"><i data-lucide="${iconType}" class="w-3 h-3"></i> ${statusObj.status}</span>
                            </div>
                            <div class="space-y-1 mt-3">
                                <div class="flex justify-between text-xs border-b border-slate-100 pb-1">
                                    <span class="text-slate-500">Masa Berlaku:</span>
                                    <span class="font-bold ${isExp ? 'text-red-600' : 'text-slate-700'}">${expText}</span>
                                </div>
                                <div class="flex justify-between text-xs pt-1">
                                    <span class="text-slate-500">Pemberi:</span>
                                    <span class="font-bold text-slate-700 truncate max-w-[120px]" title="${cert.issuer}">${cert.issuer || '-'}</span>
                                </div>
                            </div>
                        </div>
                    `);
                });
            } else { noCertMsg.classList.remove('hidden'); }

            lucide.createIcons();
            openModal('modal-profile');
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
