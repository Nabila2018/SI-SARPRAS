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
            'Fasilitas Umum'
        ];

        return view(
            'laporan.create',
            compact('pasar', 'kategoriLaporan', 'pasarTerpilih')
        );
    }

    // Simpan laporan baru
    public function store(Request $request)
    {
        $request->validate([
            'id_pasar'           => 'required|exists:pasar,id_pasar',
            'id_lokasi'          => 'required|exists:lokasi,id_lokasi',
            'id_fasilitas'       => [
                'required',
                'exists:fasilitas,id_fasilitas',
                // Validasi server-side: kombinasi id_lokasi + id_fasilitas harus ada
                // di tabel lokasi_fasilitas. Mencegah user mengirim fasilitas yang
                // tidak tersedia pada lokasi yang dipilih, meski bypass JS di frontend.
                function ($attribute, $value, $fail) use ($request) {
                    $exists = DB::table('lokasi_fasilitas')
                        ->where('id_lokasi', $request->id_lokasi)
                        ->where('id_fasilitas', $value)
                        ->exists();

                    if (!$exists) {
                        $fail('Fasilitas yang dipilih tidak tersedia pada lokasi tersebut.');
                    }
                },
            ],
            'kategori_laporan'   => 'required|in:Sanitasi & Air,Instalasi Listrik,Prasarana Bangunan,Fasilitas Umum',
            'item_kerusakan'     => 'required|string|max:100',
            'lokasi_spesifik'    => 'nullable|string|max:255',
            'deskripsi_kerusakan' => 'required|string',
            'kondisi_diharapkan' => 'required|string',
            'foto_laporan'       => 'required|array|min:1',
            'foto_laporan.*'     => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        DB::beginTransaction();

        try {
            // Simpan laporan
            $laporan = Laporan::create([
                'id_laporan' => Laporan::generateId(),
                'id_lokasi' => $request->id_lokasi,
                'id_fasilitas' => $request->id_fasilitas,
                'id_pelapor' => auth()->user()->id_user,
                'id_spj' => null,
                'kategori_laporan' => $request->kategori_laporan,
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
}