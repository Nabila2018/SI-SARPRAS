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

    Route::get('/home', function () {
        return view('home');
    })->name('home');


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
    });

    Route::get('/laporan/{id}', [LaporanController::class, 'show'])
        ->name('laporan.show');


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

                // ===== RAB =====
        Route::get('/staff/laporan/{id}/rab', [StaffLaporanController::class, 'showRab'])
            ->name('staff.laporan.rab.show');

        Route::post('/staff/laporan/{id}/rab', [StaffLaporanController::class, 'storeRab'])
            ->name('staff.laporan.rab.store');

        Route::post('/staff/laporan/{id}/rab/forward', [StaffLaporanController::class, 'forwardRab'])
            ->name('staff.laporan.rab.forward');

                // ===== DAFTAR RAB =====
        Route::get('/staff/rab', [StaffLaporanController::class, 'indexRab'])
            ->name('staff.rab.index');

        // ===== PROGRES PERBAIKAN =====
        Route::post('/staff/laporan/{id}/progres', [StaffLaporanController::class, 'storeProgres'])
            ->name('staff.laporan.progres.store');

        // PENGELOLAAN AKUN
        Route::get('/staff/akun', [PengelolaanAkunController::class, 'index'])
        ->name('staff.akun.index');
            
        Route::patch('/staff/akun/{id}/status', [PengelolaanAkunController::class, 'toggleStatus'])
        ->name('staff.akun.toggle-status');

        Route::patch('/staff/akun/{id}', [PengelolaanAkunController::class, 'update'])
        ->name('staff.akun.update');

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
    // API LOKASI
    // =======================

    // Load lokasi berdasarkan pasar dengan hierarki nama lengkap
    Route::get('/api/lokasi/{pasar}', function ($pasarId) {

        $lokasi = \App\Models\Lokasi::where('id_pasar', $pasarId)
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
});


// =======================
// ROOT
// =======================

// Redirect root ke home
// Middleware akan mengarahkan ke login jika user belum login
Route::get('/', function () {
    return redirect()->route('home');
});