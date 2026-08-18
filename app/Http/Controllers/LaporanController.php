<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use App\Models\FotoLaporan;
use App\Models\Pasar;
use App\Models\Fasilitas;
use App\Models\Lokasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class LaporanController extends Controller
{
    // Tampilkan form buat laporan (UPTD)
    public function create()
    {
        $user = auth()->user();

        // UPTD hanya bisa lihat pasar miliknya
        if ($user->role->nama_role === 'Petugas UPTD') {
            $pasar = Pasar::where('id_pasar', $user->id_pasar)->get();
            $pasarTerpilih = $user->id_pasar;
        } else {
            $pasar = Pasar::all();
            $pasarTerpilih = null;
        }

        // Dropdown fasilitas diisi secara dinamis via /api/fasilitas/{id_lokasi}
        // setelah user memilih lokasi. Fasilitas::all() tidak lagi digunakan di sini.
        $kategoriLaporan = [
            'Sanitasi & Air',
            'Instalasi Listrik',
            'Prasarana Bangunan',
            'Fasilitas Umum',
            'Lainnya'
        ];

        return view(
            'laporan.create',
            compact('pasar', 'kategoriLaporan', 'pasarTerpilih')
        );
    }

    // Simpan laporan baru
    public function store(Request $request)
    {
        $selectedFasilitas = \App\Models\Fasilitas::find($request->id_fasilitas);
        $isRuangLainnya = $selectedFasilitas && $selectedFasilitas->nama_fasilitas === 'Ruang Lainnya';
        $isKategoriLainnya = $request->kategori_laporan === 'Lainnya';

        $request->validate([
            'id_pasar'           => 'required|exists:pasar,id_pasar',
            'id_lokasi'          => 'required|exists:lokasi,id_lokasi',
            'id_fasilitas'       => [
                'required',
                'exists:fasilitas,id_fasilitas',
                function ($attribute, $value, $fail) use ($request) {
                    $exists = DB::table('lokasi_fasilitas')
                        ->where('id_lokasi', $request->id_lokasi)
                        ->where('id_fasilitas', $value)
                        ->exists();

                    if (!$exists) {
                        $selected = \App\Models\Fasilitas::find($value);
                        if ($selected && $selected->nama_fasilitas === 'Ruang Lainnya') {
                            return;
                        }
                        $fail('Fasilitas yang dipilih tidak tersedia pada lokasi tersebut.');
                    }
                },
            ],
            'nama_fasilitas_lainnya' => $isRuangLainnya ? 'required|string|max:100' : 'nullable|string|max:100',
            'kategori_laporan'   => 'required|in:Sanitasi & Air,Instalasi Listrik,Prasarana Bangunan,Fasilitas Umum,Lainnya',
            'kategori_laporan_lainnya' => $isKategoriLainnya ? 'required|string|max:100' : 'nullable|string|max:100',
            'item_kerusakan'     => 'required|string|max:100',
            'lokasi_spesifik'    => 'nullable|string|max:255',
            'deskripsi_kerusakan' => 'required|string',
            'kondisi_diharapkan' => 'required|string',
            'foto_laporan'       => 'required|array|min:1',
            'foto_laporan.*'     => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'nama_fasilitas_lainnya.required' => 'Nama Ruang/Fasilitas Lainnya wajib diisi.',
            'kategori_laporan_lainnya.required' => 'Kategori Sarana Lainnya wajib diisi.',
        ]);

        $namaFasilitasLainnya = $isRuangLainnya ? trim($request->nama_fasilitas_lainnya) : null;
        $kategoriLaporanLainnya = $isKategoriLainnya ? trim($request->kategori_laporan_lainnya) : null;

        DB::beginTransaction();

        try {
            // Simpan laporan
            $laporan = Laporan::create([
                'id_laporan' => Laporan::generateId(),
                'id_lokasi' => $request->id_lokasi,
                'id_fasilitas' => $request->id_fasilitas,
                'nama_fasilitas_lainnya' => $namaFasilitasLainnya,
                'id_pelapor' => auth()->user()->id_user,
                'id_spj' => null,
                'kategori_laporan' => $request->kategori_laporan,
                'kategori_laporan_lainnya' => $kategoriLaporanLainnya,
                'item_kerusakan' => $request->item_kerusakan,
                'lokasi_spesifik' => $request->lokasi_spesifik,
                'deskripsi_kerusakan' => $request->deskripsi_kerusakan,
                'kondisi_diharapkan' => $request->kondisi_diharapkan,
                'tanggal_lapor' => now(),
                'status_laporan' => 'Menunggu',
            ]);

            // Simpan foto
            if ($request->hasFile('foto_laporan')) {
                foreach ($request->file('foto_laporan') as $foto) {
                    $path = $foto->store('laporan', 'public');

                    FotoLaporan::create([
                        'id_foto' => FotoLaporan::generateId(),
                        'id_laporan' => $laporan->id_laporan,
                        'file_foto' => $path,
                    ]);
                }
            }

            DB::commit();

            return redirect()
                ->route('laporan.index')
                ->with('success', 'Laporan berhasil dikirim!');
        } catch (\Exception $e) {
            DB::rollback();

            return back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Riwayat laporan (UPTD)
    public function index()
    {
        $user = auth()->user();

        $query = Laporan::with([
            'lokasi.pasar',
            'fasilitas',
            'pelapor'
        ]);

        // Scope laporan berdasarkan pasar (id_pasar) milik Petugas UPTD
        if ($user->role->nama_role === 'Petugas UPTD') {
            $query->whereHas('lokasi', function ($q) use ($user) {
                $q->where('id_pasar', $user->id_pasar);
            });
        }

        $laporan = $query->orderBy('tanggal_lapor', 'desc')
            ->paginate(5);

        return view('laporan.index', compact('laporan'));
    }

    // Detail laporan
    public function show($id)
    {
        $laporan = Laporan::with([
            'lokasi.pasar',
            'fasilitas',
            'fotoLaporan',
            'pelapor',
            'evaluator',
            'detailRab',
            'progresPerbaikan.fotoProgres'
        ])->findOrFail($id);

        $laporan->load(['fotoLaporan' => function ($query) use ($laporan) {
            $query->where('id_laporan', $laporan->getKey());
        }]);

        // UPTD hanya boleh lihat laporan pada pasar yang sama (id_pasar)
        if (auth()->user()->role->nama_role === 'Petugas UPTD') {
            $userPasarId = auth()->user()->id_pasar;
            $reportPasarId = $laporan->lokasi->id_pasar ?? null;

            if ($reportPasarId !== $userPasarId) {
                abort(403);
            }
        }

        // Staff pakai view berbeda
        if (auth()->user()->role->nama_role === 'Staff Sarana dan Prasarana') {
            return view('staff.laporan.show', compact('laporan'));
        }

        // Kepala Bidang pakai view kabid
        if (auth()->user()->role->nama_role === 'Kepala Bidang') {
            return view('kabid.laporan.show', compact('laporan'));
        }

        // Default: view UPTD
        return view('laporan.show', compact('laporan'));
    }

    public function authorizeUptdEdit(Laporan $laporan): void
    {
        $user = auth()->user();

        if (!$user || $user->role->nama_role !== 'Petugas UPTD') {
            abort(403, 'Akses ditolak. Hanya Petugas UPTD yang dapat mengubah laporan.');
        }

        if ($laporan->id_pelapor !== $user->id_user) {
            abort(403, 'Anda hanya dapat mengubah laporan yang Anda buat sendiri.');
        }

        if ($laporan->status_laporan !== 'Menunggu') {
            abort(403, 'Laporan yang sudah diproses tidak dapat diubah.');
        }

        if (!is_null($laporan->id_evaluator) || !is_null($laporan->kategori_kerusakan)) {
            abort(403, 'Laporan yang sudah dievaluasi tidak dapat diubah.');
        }
    }

    public function edit($id)
    {
        $laporan = Laporan::with(['lokasi.pasar', 'fasilitas', 'fotoLaporan'])->findOrFail($id);

        $this->authorizeUptdEdit($laporan);

        $user = auth()->user();
        $pasar = Pasar::where('id_pasar', $user->id_pasar)->get();
        $pasarTerpilih = $user->id_pasar;

        $kategoriLaporan = [
            'Sanitasi & Air',
            'Instalasi Listrik',
            'Prasarana Bangunan',
            'Fasilitas Umum',
            'Lainnya'
        ];

        return view('laporan.edit', compact('laporan', 'pasar', 'kategoriLaporan', 'pasarTerpilih'));
    }

    public function update(Request $request, $id)
    {
        $laporan = Laporan::findOrFail($id);

        $this->authorizeUptdEdit($laporan);

        $selectedFasilitas = \App\Models\Fasilitas::find($request->id_fasilitas);
        $isRuangLainnya = $selectedFasilitas && $selectedFasilitas->nama_fasilitas === 'Ruang Lainnya';
        $isKategoriLainnya = $request->kategori_laporan === 'Lainnya';

        $request->validate([
            'id_pasar'           => 'required|exists:pasar,id_pasar',
            'id_lokasi'          => 'required|exists:lokasi,id_lokasi',
            'id_fasilitas'       => [
                'required',
                'exists:fasilitas,id_fasilitas',
                function ($attribute, $value, $fail) use ($request) {
                    $exists = DB::table('lokasi_fasilitas')
                        ->where('id_lokasi', $request->id_lokasi)
                        ->where('id_fasilitas', $value)
                        ->exists();

                    if (!$exists) {
                        $selected = \App\Models\Fasilitas::find($value);
                        if ($selected && $selected->nama_fasilitas === 'Ruang Lainnya') {
                            return;
                        }
                        $fail('Fasilitas yang dipilih tidak tersedia pada lokasi tersebut.');
                    }
                },
            ],
            'nama_fasilitas_lainnya' => $isRuangLainnya ? 'required|string|max:100' : 'nullable|string|max:100',
            'kategori_laporan'   => 'required|in:Sanitasi & Air,Instalasi Listrik,Prasarana Bangunan,Fasilitas Umum,Lainnya',
            'kategori_laporan_lainnya' => $isKategoriLainnya ? 'required|string|max:100' : 'nullable|string|max:100',
            'item_kerusakan'     => 'required|string|max:100',
            'lokasi_spesifik'    => 'nullable|string|max:255',
            'deskripsi_kerusakan' => 'required|string',
            'kondisi_diharapkan' => 'required|string',
            'hapus_foto'         => 'nullable|array',
            'hapus_foto.*'       => 'string',
            'foto_laporan'       => 'nullable|array',
            'foto_laporan.*'     => 'image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'nama_fasilitas_lainnya.required' => 'Nama Ruang/Fasilitas Lainnya wajib diisi.',
            'kategori_laporan_lainnya.required' => 'Kategori Sarana Lainnya wajib diisi.',
        ]);

        $existingPhotosCount = $laporan->fotoLaporan()->count();
        $hapusFotoIds = (array) $request->input('hapus_foto', []);

        $validHapusFotoModels = FotoLaporan::where('id_laporan', $laporan->id_laporan)
            ->whereIn('id_foto', $hapusFotoIds)
            ->get();
        $validHapusCount = $validHapusFotoModels->count();

        $newPhotosCount = $request->hasFile('foto_laporan') ? count($request->file('foto_laporan')) : 0;
        $remainingPhotosCount = $existingPhotosCount - $validHapusCount + $newPhotosCount;

        if ($remainingPhotosCount < 1) {
            return back()->withErrors(['foto_laporan' => 'Minimal 1 foto dokumentasi laporan harus tetap ada.'])->withInput();
        }

        $namaFasilitasLainnya = $isRuangLainnya ? trim($request->nama_fasilitas_lainnya) : null;
        $kategoriLaporanLainnya = $isKategoriLainnya ? trim($request->kategori_laporan_lainnya) : null;

        DB::beginTransaction();

        try {
            $laporan->update([
                'id_lokasi' => $request->id_lokasi,
                'id_fasilitas' => $request->id_fasilitas,
                'nama_fasilitas_lainnya' => $namaFasilitasLainnya,
                'kategori_laporan' => $request->kategori_laporan,
                'kategori_laporan_lainnya' => $kategoriLaporanLainnya,
                'item_kerusakan' => $request->item_kerusakan,
                'lokasi_spesifik' => $request->lokasi_spesifik,
                'deskripsi_kerusakan' => $request->deskripsi_kerusakan,
                'kondisi_diharapkan' => $request->kondisi_diharapkan,
            ]);

            foreach ($validHapusFotoModels as $foto) {
                if ($foto->file_foto && Storage::disk('public')->exists($foto->file_foto)) {
                    Storage::disk('public')->delete($foto->file_foto);
                }
                $foto->delete();
            }

            if ($request->hasFile('foto_laporan')) {
                foreach ($request->file('foto_laporan') as $foto) {
                    $path = $foto->store('laporan', 'public');

                    FotoLaporan::create([
                        'id_foto' => FotoLaporan::generateId(),
                        'id_laporan' => $laporan->id_laporan,
                        'file_foto' => $path,
                    ]);
                }
            }

            DB::commit();

            return redirect()
                ->route('laporan.show', $laporan->id_laporan)
                ->with('success', 'Laporan berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollback();

            return back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }
}