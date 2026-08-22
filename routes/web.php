<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\StaffLaporanController;
use App\Http\Controllers\VerifikasiLaporanController;
use App\Http\Controllers\PengelolaanAkunController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\KabidRabController;
use App\Http\Controllers\SpjController;
use App\Http\Controllers\LaporanRealisasiTahunanController;
use App\Http\Controllers\KadinLaporanController;
use App\Http\Controllers\StaffProyekController;
use App\Http\Controllers\StaffRabController;
use App\Http\Controllers\StaffSabController;
use App\Http\Controllers\StaffMasterController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\HomeController;

// =======================
// PUBLIC (Tanpa Login)
// =======================

Route::get('/login', [AuthController::class, 'showLoginForm'])
    ->name('login');

Route::post('/login', [AuthController::class, 'login']);

// =======================
// LUPA PASSWORD
// =======================

Route::get('/lupa-password', [ForgotPasswordController::class, 'showForgotForm'])
    ->name('password.request');

Route::post('/lupa-password', [ForgotPasswordController::class, 'sendResetLink'])
    ->name('password.email');

Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])
    ->name('password.reset');

Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword'])
    ->name('password.update');
    
// =======================
// PROTECTED (Harus Login)
// =======================

Route::middleware(['auth', 'account.active'])->group(function () {
    // =======================
    // NOTIFIKASI IN-APP
    // =======================
    Route::get('/notifikasi/api', [NotifikasiController::class, 'getNotifications'])->name('notifikasi.api');
    Route::get('/notifikasi/{id}/read', [NotifikasiController::class, 'read'])->name('notifikasi.read');
    Route::post('/notifikasi/mark-all-read', [NotifikasiController::class, 'markAllAsRead'])->name('notifikasi.mark-all-read');

    // =======================
    // AUTH
    // =======================

    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');

    // =======================
    // PROFIL
    // =======================

    Route::get('/profil', [ProfileController::class, 'show'])
        ->name('profil.show');

    Route::patch('/profil', [ProfileController::class, 'update'])
        ->name('profil.update');

    Route::patch('/profil/password', [ProfileController::class, 'updatePassword'])
        ->name('profil.password.update');
    // =======================
    // HOME
    // =======================

    Route::get('/home', [HomeController::class, 'index'])
        ->name('home');

    Route::get('/panduan', function () {
        return view('panduan');
    })->name('panduan');


    // =======================
    // LAPORAN UPTD
    // =======================

    Route::middleware(['role:Petugas UPTD'])->group(function () {
        Route::get('/laporan/create', [LaporanController::class, 'create'])
            ->name('laporan.create');

        Route::post('/laporan', [LaporanController::class, 'store'])
            ->name('laporan.store');

        Route::get('/laporan', [LaporanController::class, 'index'])
            ->name('laporan.index');

        Route::get('/laporan/{id}/edit', [LaporanController::class, 'edit'])
            ->name('laporan.edit');

        Route::put('/laporan/{id}', [LaporanController::class, 'update'])
            ->name('laporan.update');
    });

    Route::get('/laporan/{id}', [LaporanController::class, 'show'])
        ->name('laporan.show');

    Route::get('/laporan/{id}/rab/pdf', [StaffLaporanController::class, 'exportRabPdf'])
        ->name('laporan.rab.pdf');


    // =======================
    // LAPORAN STAFF
    // =======================

    Route::middleware(['role:Staff Sarana dan Prasarana'])->group(function () {
        Route::get('/staff/laporan', [StaffLaporanController::class, 'index'])
            ->name('staff.laporan.index');

        Route::post('/staff/laporan/{id}/evaluasi', [StaffLaporanController::class, 'storeEvaluation'])
            ->name('staff.laporan.evaluasi.store');

        Route::post('/staff/laporan/{id}/forward', [StaffLaporanController::class, 'forwardToKabid'])
            ->name('staff.laporan.forward');

        // ===== PROGRES PERBAIKAN =====
        Route::post('/staff/laporan/{id}/progres', [StaffLaporanController::class, 'storeProgres'])
            ->name('staff.laporan.progres.store');
        Route::post('/staff/laporan/{id}/progres/{id_progres}', [StaffLaporanController::class, 'updateProgres'])
            ->name('staff.laporan.progres.update');

        // ===== RENCANA ANGGARAN BIAYA (RAB) =====
        Route::get('/staff/rab', [StaffRabController::class, 'index'])
            ->name('staff.rab.index');
        Route::get('/staff/rab/create', [StaffRabController::class, 'create'])
            ->name('staff.rab.create');
        Route::post('/staff/rab', [StaffRabController::class, 'store'])
            ->name('staff.rab.store');
        Route::get('/staff/rab/{id}', [StaffRabController::class, 'show'])
            ->name('staff.rab.show');
        Route::get('/staff/rab/{id}/edit', [StaffRabController::class, 'edit'])
            ->name('staff.rab.edit');
        Route::put('/staff/rab/{id}', [StaffRabController::class, 'update'])
            ->name('staff.rab.update');
        Route::post('/staff/rab/{id}/submit', [StaffRabController::class, 'submitToKabid'])
            ->name('staff.rab.submit');
        Route::get('/staff/rab/{id}/pdf', [StaffRabController::class, 'exportPdf'])
            ->name('staff.rab.pdf');

        // ===== MASTER DATA TERPUSAT =====
        Route::get('/staff/master', [StaffMasterController::class, 'index'])->name('staff.master.index');
        
        // Pasar
        Route::post('/staff/master/pasar', [StaffMasterController::class, 'storePasar'])->name('staff.master.pasar.store');
        Route::put('/staff/master/pasar/{id}', [StaffMasterController::class, 'updatePasar'])->name('staff.master.pasar.update');
        Route::patch('/staff/master/pasar/{id}/status', [StaffMasterController::class, 'toggleStatusPasar'])->name('staff.master.pasar.toggle-status');

        // Lokasi
        Route::post('/staff/master/lokasi', [StaffMasterController::class, 'storeLokasi'])->name('staff.master.lokasi.store');
        Route::put('/staff/master/lokasi/{id}', [StaffMasterController::class, 'updateLokasi'])->name('staff.master.lokasi.update');
        Route::patch('/staff/master/lokasi/{id}/status', [StaffMasterController::class, 'toggleStatusLokasi'])->name('staff.master.lokasi.toggle-status');

        // Fasilitas
        Route::post('/staff/master/fasilitas', [StaffMasterController::class, 'storeFasilitas'])->name('staff.master.fasilitas.store');
        Route::put('/staff/master/fasilitas/{id}', [StaffMasterController::class, 'updateFasilitas'])->name('staff.master.fasilitas.update');
        Route::patch('/staff/master/fasilitas/{id}/status', [StaffMasterController::class, 'toggleStatusFasilitas'])->name('staff.master.fasilitas.toggle-status');

        // Kategori
        Route::post('/staff/master/kategori', [StaffMasterController::class, 'storeKategori'])->name('staff.master.kategori.store');
        Route::put('/staff/master/kategori/{id}', [StaffMasterController::class, 'updateKategori'])->name('staff.master.kategori.update');
        Route::patch('/staff/master/kategori/{id}/status', [StaffMasterController::class, 'toggleStatusKategori'])->name('staff.master.kategori.toggle-status');

        // SAB
        Route::post('/staff/master/sab', [StaffMasterController::class, 'storeSab'])->name('staff.master.sab.store');
        Route::put('/staff/master/sab/{id}', [StaffMasterController::class, 'updateSab'])->name('staff.master.sab.update');
        Route::patch('/staff/master/sab/{id}/status', [StaffMasterController::class, 'toggleStatusSab'])->name('staff.master.sab.toggle-status');

        // ===== MASTER SAB (REDIRECT) =====
        Route::get('/staff/sab', [StaffSabController::class, 'index'])
            ->name('staff.sab.index');
        Route::post('/staff/sab', [StaffSabController::class, 'store'])
            ->name('staff.sab.store');
        Route::put('/staff/sab/{id}', [StaffSabController::class, 'update'])
            ->name('staff.sab.update');
        Route::patch('/staff/sab/{id}/status', [StaffSabController::class, 'toggleStatus'])
            ->name('staff.sab.toggle-status');

        // PENGELOLAAN AKUN
        Route::get('/staff/akun', [PengelolaanAkunController::class, 'index'])
        ->name('staff.akun.index');
            
        Route::patch('/staff/akun/{id}/status', [PengelolaanAkunController::class, 'toggleStatus'])
        ->name('staff.akun.toggle-status');

        Route::post('/staff/akun', [PengelolaanAkunController::class, 'store'])
        ->name('staff.akun.store');

        // ===== REALISASI TAHUNAN =====
        Route::resource('/staff/realisasi-tahunan', LaporanRealisasiTahunanController::class)
            ->names('staff.realisasi-tahunan');

        // ===== SPJ WRITE ACTIONS (STAFF ONLY) =====
        Route::get('/staff/spj/create', [SpjController::class, 'create'])->name('staff.spj.create');
        Route::post('/staff/spj', [SpjController::class, 'store'])->name('staff.spj.store');
        Route::get('/staff/spj/{spj}/edit', [SpjController::class, 'edit'])->name('staff.spj.edit');
        Route::put('/staff/spj/{spj}', [SpjController::class, 'update'])->name('staff.spj.update');
        Route::delete('/staff/spj/{spj}', [SpjController::class, 'destroy'])->name('staff.spj.destroy');

    });

    // ===== SPJ READ-ONLY (STAFF, KABID, KADIN) =====
    Route::middleware(['role:Staff Sarana dan Prasarana,Kepala Bidang,Kepala Dinas'])->group(function () {
        Route::get('/staff/spj', [SpjController::class, 'index'])->name('staff.spj.index');
        Route::get('/staff/spj/{spj}', [SpjController::class, 'show'])->name('staff.spj.show');
    });

    // =======================
    // VERIFIKASI LAPORAN - KABID
    // =======================


    Route::middleware(['role:Kepala Bidang'])->group(function () {

        Route::get('/kabid/laporan', [VerifikasiLaporanController::class, 'index'])
             ->name('kabid.laporan.index');

        Route::get('/kabid/laporan/{id}', [VerifikasiLaporanController::class, 'show'])
            ->name('kabid.laporan.show');

        Route::post('/kabid/laporan/{id}/setujui', [VerifikasiLaporanController::class, 'setujui'])
            ->name('kabid.laporan.setujui');
        
        Route::post('/kabid/laporan/{id}/kembalikan', [VerifikasiLaporanController::class, 'kembalikan'])
            ->name('kabid.laporan.kembalikan');

        // ===== VERIFIKASI RAB - KABID =====
        Route::get('/kabid/rab', [KabidRabController::class, 'index'])
            ->name('kabid.rab.index');

        Route::get('/kabid/rab/{id}', [KabidRabController::class, 'show'])
            ->name('kabid.rab.show');

        Route::post('/kabid/rab/{id}/setujui', [KabidRabController::class, 'setujui'])
            ->name('kabid.rab.setujui');

        Route::post('/kabid/rab/{id}/kembalikan', [KabidRabController::class, 'kembalikan'])
            ->name('kabid.rab.kembalikan');
    }); 

    // =======================
    // MONITORING LAPORAN - KEPALA DINAS
    // =======================
    Route::middleware(['role:Kepala Dinas'])->group(function () {
        Route::get('/kadin/laporan', [KadinLaporanController::class, 'index'])
            ->name('kadin.laporan.index');

        Route::get('/kadin/laporan/count', [KadinLaporanController::class, 'countCetak'])
            ->name('kadin.laporan.count');

        Route::get('/kadin/laporan/print', [KadinLaporanController::class, 'printPdf'])
            ->name('kadin.laporan.print');
    });


    // =======================
    // API LOKASI
    // =======================

    // Load lokasi berdasarkan pasar dengan hierarki nama lengkap
    Route::get('/api/lokasi/{pasar}', function ($pasarId) {

        $lokasi = \App\Models\Lokasi::where('id_pasar', $pasarId)
            ->where('status_aktif', 'Aktif')
            ->orderBy('id_induk')
            ->orderBy('nama_lokasi')
            ->get([
                'id_lokasi',
                'nama_lokasi',
                'id_induk'
            ]);

        // Build nama lengkap dengan hierarki
        $lokasiMap = $lokasi->keyBy('id_lokasi');

        return $lokasi->map(function ($item) use ($lokasiMap) {

            $namaLengkap = $item->nama_lokasi;

            $parent = $item->id_induk
                ? ($lokasiMap[$item->id_induk] ?? null)
                : null;

            // Jika punya parent, tampilkan "Parent > Child"
            if ($parent) {

                $grandparent = $parent->id_induk
                    ? ($lokasiMap[$parent->id_induk] ?? null)
                    : null;

                if ($grandparent) {
                    $namaLengkap =
                        $grandparent->nama_lokasi
                        . ' > '
                        . $parent->nama_lokasi
                        . ' > '
                        . $item->nama_lokasi;
                } else {
                    $namaLengkap =
                        $parent->nama_lokasi
                        . ' > '
                        . $item->nama_lokasi;
                }
            }

            return [
                'id_lokasi' => $item->id_lokasi,
                'nama_lokasi' => $item->nama_lokasi,
                'nama_lengkap' => $namaLengkap,
                'id_induk' => $item->id_induk,
            ];
        });
    })->name('api.lokasi');

    // =======================
    // API FASILITAS
    // =======================

    // Load fasilitas yang tersedia pada suatu lokasi via tabel lokasi_fasilitas.
    // Hanya fasilitas yang memiliki mapping (id_lokasi + id_fasilitas) yang dikembalikan.
    // Tidak ada fallback ke parent lokasi. Nilai jumlah tidak diekspos.
    Route::get('/api/fasilitas/{lokasi}', function ($lokasiId) {

        $fasilitas = \App\Models\Fasilitas::whereHas('lokasiFasilitas', function ($q) use ($lokasiId) {
            $q->where('id_lokasi', $lokasiId);
        })->where('status_aktif', 'Aktif')->orderBy('nama_fasilitas')->get(['id_fasilitas', 'nama_fasilitas']);

        // Pastikan 'Ruang Lainnya' selalu tersedia sebagai opsi fallback di semua lokasi
        $ruangLainnya = \App\Models\Fasilitas::where('nama_fasilitas', 'Ruang Lainnya')->where('status_aktif', 'Aktif')->first(['id_fasilitas', 'nama_fasilitas']);
        if ($ruangLainnya && !$fasilitas->contains('id_fasilitas', $ruangLainnya->id_fasilitas)) {
            $fasilitas->push($ruangLainnya);
        }

        return response()->json($fasilitas);
    })->name('api.fasilitas');
});



// =======================
// ROOT
// =======================

// Redirect root ke home
// Middleware akan mengarahkan ke login jika user belum login
Route::get('/', function () {
    return redirect()->route('home');
});