<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use App\Models\Pasar;
use App\Models\Proyek;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StaffProyekController extends Controller
{
    /**
     * Tampilkan daftar proyek perbaikan (Akses terbuka untuk seluruh Staff).
     */
    public function index(Request $request)
    {
        if (auth()->user()->role->nama_role !== 'Staff Sarana dan Prasarana') {
            abort(403, 'Akses ditolak.');
        }

        $query = Proyek::with(['pasar', 'pembuat'])
            ->withCount('laporan');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('id_proyek', 'LIKE', "%{$search}%")
                  ->orWhere('nama_proyek', 'LIKE', "%{$search}%")
                  ->orWhereHas('pasar', function($qp) use ($search) {
                      $qp->where('nama_pasar', 'LIKE', "%{$search}%");
                  });
            });
        }

        $proyekList = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('staff.proyek.index', compact('proyekList'));
    }

    /**
     * Tampilkan form buat proyek perbaikan baru.
     */
    public function create(Request $request)
    {
        if (auth()->user()->role->nama_role !== 'Staff Sarana dan Prasarana') {
            abort(403, 'Akses ditolak.');
        }

        $pasarList = Pasar::orderBy('nama_pasar')->get();
        $selectedPasarId = $request->query('id_pasar');

        $laporanEligible = collect();

        if ($selectedPasarId) {
            $laporanEligible = Laporan::where('status_laporan', 'Disetujui')
                ->whereNull('id_proyek')
                ->whereHas('lokasi', function ($query) use ($selectedPasarId) {
                    $query->where('id_pasar', $selectedPasarId);
                })
                ->with(['lokasi', 'fasilitas', 'pelapor'])
                ->orderBy('tanggal_lapor', 'desc')
                ->get();
        }

        return view('staff.proyek.create', compact('pasarList', 'selectedPasarId', 'laporanEligible'));
    }

    /**
     * Simpan proyek perbaikan baru ke database.
     */
    public function store(Request $request)
    {
        if (auth()->user()->role->nama_role !== 'Staff Sarana dan Prasarana') {
            abort(403, 'Akses ditolak.');
        }

        $validated = $request->validate([
            'nama_proyek' => ['required', 'string', 'max:150'],
            'deskripsi_proyek' => ['nullable', 'string', 'max:2000'],
            'id_pasar' => ['required', 'exists:pasar,id_pasar'],
            'id_laporan' => ['required', 'array', 'min:1'],
            'id_laporan.*' => ['required', 'exists:laporan,id_laporan'],
        ], [
            'nama_proyek.required' => 'Nama proyek wajib diisi.',
            'id_pasar.required' => 'Pasar wajib dipilih.',
            'id_laporan.required' => 'Minimal 1 laporan eligible wajib dipilih untuk proyek ini.',
            'id_laporan.min' => 'Minimal 1 laporan eligible wajib dipilih untuk proyek ini.',
        ]);

        // Strict Backend Validation untuk Kriteria Eligibility & Pasar
        $laporans = Laporan::whereIn('id_laporan', $validated['id_laporan'])
            ->with('lokasi')
            ->get();

        foreach ($laporans as $laporan) {
            if ($laporan->status_laporan !== 'Disetujui') {
                throw ValidationException::withMessages([
                    'id_laporan' => ["Laporan {$laporan->id_laporan} tidak dapat dimasukkan ke proyek karena status belum 'Disetujui'."],
                ]);
            }

            if (!is_null($laporan->id_proyek)) {
                throw ValidationException::withMessages([
                    'id_laporan' => ["Laporan {$laporan->id_laporan} sudah tergabung dalam proyek lain ({$laporan->id_proyek})."],
                ]);
            }

            if (!$laporan->lokasi || $laporan->lokasi->id_pasar !== $validated['id_pasar']) {
                throw ValidationException::withMessages([
                    'id_laporan' => ["Laporan {$laporan->id_laporan} tidak berasal dari pasar yang dipilih."],
                ]);
            }
        }

        DB::beginTransaction();

        try {
            $proyek = Proyek::create([
                'nama_proyek' => $validated['nama_proyek'],
                'deskripsi_proyek' => $validated['deskripsi_proyek'],
                'id_pasar' => $validated['id_pasar'],
                'id_pembuat' => auth()->user()->id_user,
            ]);

            Laporan::whereIn('id_laporan', $validated['id_laporan'])->update([
                'id_proyek' => $proyek->id_proyek,
            ]);

            DB::commit();

            return redirect()
                ->route('staff.proyek.show', $proyek->id_proyek)
                ->with('success', "Proyek perbaikan '{$proyek->nama_proyek}' ({$proyek->id_proyek}) berhasil dibuat.");
        } catch (\Exception $e) {
            DB::rollback();
            if ($e instanceof ValidationException) {
                throw $e;
            }
            return back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan proyek: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan detail informasi proyek dan daftar laporan tergabung.
     */
    public function show($id)
    {
        if (auth()->user()->role->nama_role !== 'Staff Sarana dan Prasarana') {
            abort(403, 'Akses ditolak.');
        }

        $proyek = Proyek::with([
            'pasar',
            'pembuat',
            'laporan.lokasi',
            'laporan.fasilitas',
            'laporan.pelapor',
        ])->findOrFail($id);

        return view('staff.proyek.show', compact('proyek'));
    }
}
