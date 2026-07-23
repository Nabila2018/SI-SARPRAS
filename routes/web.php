<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\StaffLaporanController;

// =======================
// PUBLIC (Tanpa Login)
// =======================

Route::get('/login', [AuthController::class, 'showLoginForm'])
    ->name('login');

Route::post('/login', [AuthController::class, 'login']);


// =======================
// PROTECTED (Harus Login)
// =======================

Route::middleware('auth')->group(function () {

    // =======================
    // AUTH
    // =======================

    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');


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