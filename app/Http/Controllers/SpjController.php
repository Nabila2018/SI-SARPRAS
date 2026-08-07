<?php

namespace App\Http\Controllers;

use App\Models\Spj;
use App\Models\Laporan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SpjController extends Controller
{
    /**
     * Check if authenticated user is Staff Sarana dan Prasarana.
     */
    protected function authorizeStaff()
    {
        if (auth()->user()->role->nama_role !== 'Staff Sarana dan Prasarana') {
            abort(403, 'Akses ditolak. Hanya Staff Sarana dan Prasarana yang berhak melakukan tindakan ini.');
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Spj::with(['uploader', 'laporan']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_pekerjaan', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        $spjList = $query->orderBy('tanggal_upload', 'desc')
            ->paginate(5)
            ->appends($request->only('search'));

        return view('staff.spj.index', compact('spjList'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorizeStaff();

        // Ambil laporan yang statusnya 'Selesai' dan belum memiliki SPJ
        $laporanList = Laporan::where('status_laporan', 'Selesai')
            ->whereNull('id_spj')
            ->with(['lokasi.pasar', 'fasilitas'])
            ->orderBy('tanggal_lapor', 'desc')
            ->get();

        return view('staff.spj.create', compact('laporanList'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorizeStaff();

        $request->validate([
            'nama_pekerjaan' => 'required|string|max:255',
            'periode_mulai' => 'required|date',
            'periode_selesai' => 'required|date|after_or_equal:periode_mulai',
            'keterangan' => 'nullable|string',
            'file_spj' => 'required|mimes:pdf|max:5120',
            'laporan' => 'required|array|min:1',
            'laporan.*' => 'required|exists:laporan,id_laporan',
        ], [
            'nama_pekerjaan.required' => 'Nama pekerjaan wajib diisi.',
            'periode_mulai.required' => 'Periode mulai wajib diisi.',
            'periode_selesai.required' => 'Periode selesai wajib diisi.',
            'periode_selesai.after_or_equal' => 'Periode selesai harus setelah atau sama dengan periode mulai.',
            'file_spj.required' => 'File SPJ wajib diunggah.',
            'file_spj.mimes' => 'File SPJ harus berformat PDF.',
            'file_spj.max' => 'Ukuran file SPJ maksimal 5 MB.',
            'laporan.required' => 'Pilih minimal satu laporan.',
            'laporan.min' => 'Pilih minimal satu laporan.',
            'laporan.*.exists' => 'Laporan yang dipilih tidak valid.',
        ]);

        // Validasi Business Rule: Seluruh laporan yang dipilih harus berstatus 'Selesai' dan id_spj masih NULL
        $validLaporanCount = Laporan::whereIn('id_laporan', $request->laporan)
            ->where('status_laporan', 'Selesai')
            ->whereNull('id_spj')
            ->count();

        if ($validLaporanCount !== count($request->laporan)) {
            return back()
                ->withInput()
                ->with('error', 'Beberapa laporan yang dipilih tidak valid, belum berstatus Selesai, atau sudah terhubung dengan SPJ lain.');
        }

        DB::beginTransaction();

        try {
            // Simpan file SPJ ke storage/app/public/spj dengan nama asli file
            $file = $request->file('file_spj');
            $fileName = $file->getClientOriginalName();
            $filePath = $file->storeAs('spj', $fileName, 'public');

            // Simpan data SPJ baru
            $spj = Spj::create([
                'id_spj' => Spj::generateId(),
                'nama_pekerjaan' => $request->nama_pekerjaan,
                'periode_mulai' => $request->periode_mulai,
                'periode_selesai' => $request->periode_selesai,
                'keterangan' => $request->keterangan,
                'file_spj' => $filePath,
                'uploaded_by' => Auth::id(),
                'tanggal_upload' => now(),
            ]);

            // Update laporan terkait dengan id_spj yang baru dibuat
            Laporan::whereIn('id_laporan', $request->laporan)
                ->update(['id_spj' => $spj->id_spj]);

            DB::commit();

            return redirect()
                ->route('staff.spj.index')
                ->with('success', 'Dokumen SPJ berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();

            if (isset($filePath) && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }

            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan SPJ: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $spj = Spj::with([
            'uploader',
            'laporan',
            'laporan.lokasi.pasar',
            'laporan.fasilitas',
        ])->findOrFail($id);

        return view('staff.spj.show', compact('spj'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $this->authorizeStaff();

        $spj = Spj::with(['uploader', 'laporan'])->findOrFail($id);

        $laporanList = Laporan::where('status_laporan', 'Selesai')
            ->where(function ($query) use ($spj) {
                $query->whereNull('id_spj')
                      ->orWhere('id_spj', $spj->id_spj);
            })
            ->with(['lokasi.pasar', 'fasilitas'])
            ->orderBy('tanggal_lapor', 'desc')
            ->get();

        return view('staff.spj.edit', compact('spj', 'laporanList'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->authorizeStaff();

        $spj = Spj::findOrFail($id);

        $request->validate([
            'nama_pekerjaan' => 'required|string|max:255',
            'periode_mulai' => 'required|date',
            'periode_selesai' => 'required|date|after_or_equal:periode_mulai',
            'keterangan' => 'nullable|string',
            'file_spj' => 'nullable|mimes:pdf|max:5120',
            'laporan' => 'required|array|min:1',
            'laporan.*' => 'required|distinct|exists:laporan,id_laporan',
        ], [
            'nama_pekerjaan.required' => 'Nama pekerjaan wajib diisi.',
            'periode_mulai.required' => 'Periode mulai wajib diisi.',
            'periode_selesai.required' => 'Periode selesai wajib diisi.',
            'periode_selesai.after_or_equal' => 'Periode selesai harus setelah atau sama dengan periode mulai.',
            'file_spj.mimes' => 'File SPJ harus berformat PDF.',
            'file_spj.max' => 'Ukuran file SPJ maksimal 5 MB.',
            'laporan.required' => 'Pilih minimal satu laporan.',
            'laporan.min' => 'Pilih minimal satu laporan.',
            'laporan.*.distinct' => 'Pilihan laporan tidak boleh duplikat.',
            'laporan.*.exists' => 'Laporan yang dipilih tidak valid.',
        ]);

        // Validasi Business Rule: Seluruh laporan harus berstatus 'Selesai' dan (id_spj NULL atau milik SPJ ini)
        $validLaporanCount = Laporan::whereIn('id_laporan', $request->laporan)
            ->where('status_laporan', 'Selesai')
            ->where(function ($q) use ($spj) {
                $q->whereNull('id_spj')
                  ->orWhere('id_spj', $spj->id_spj);
            })
            ->count();

        if ($validLaporanCount !== count($request->laporan)) {
            return back()
                ->withInput()
                ->with('error', 'Beberapa laporan yang dipilih tidak valid, belum berstatus Selesai, atau sudah terhubung dengan SPJ lain.');
        }

        DB::beginTransaction();

        try {
            $oldFilePath = $spj->file_spj;
            $newFilePath = null;

            if ($request->hasFile('file_spj')) {
                $file = $request->file('file_spj');
                $fileName = $file->getClientOriginalName();
                $newFilePath = $file->storeAs('spj', $fileName, 'public');
                $spj->file_spj = $newFilePath;
            }

            $spj->nama_pekerjaan = $request->nama_pekerjaan;
            $spj->periode_mulai = $request->periode_mulai;
            $spj->periode_selesai = $request->periode_selesai;
            $spj->keterangan = $request->keterangan;
            $spj->save();

            // 1. Lepas id_spj dari laporan yang sebelumnya terikat tapi sekarang tidak dipilih lagi
            Laporan::where('id_spj', $spj->id_spj)
                ->whereNotIn('id_laporan', $request->laporan)
                ->update(['id_spj' => null]);

            // 2. Hubungkan id_spj ke laporan yang dipilih saat ini
            Laporan::whereIn('id_laporan', $request->laporan)
                ->update(['id_spj' => $spj->id_spj]);

            DB::commit();

            // Hapus file lama dari storage jika file baru berhasil disimpan
            if ($newFilePath && $oldFilePath && Storage::disk('public')->exists($oldFilePath)) {
                Storage::disk('public')->delete($oldFilePath);
            }

            return redirect()
                ->route('staff.spj.index')
                ->with('success', 'Dokumen SPJ berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($newFilePath && Storage::disk('public')->exists($newFilePath)) {
                Storage::disk('public')->delete($newFilePath);
            }

            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat memperbarui SPJ: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->authorizeStaff();

        $spj = Spj::findOrFail($id);

        DB::beginTransaction();

        try {
            $filePath = $spj->file_spj;

            // 1. Lepas keterikatan seluruh laporan terkait (set id_spj = NULL)
            Laporan::where('id_spj', $spj->id_spj)
                ->update(['id_spj' => null]);

            // 2. Hapus record SPJ dari database
            $spj->delete();

            DB::commit();

            // 3. Hapus file fisik PDF dari storage publik setelah transaksi DB sukses
            if ($filePath && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }

            return redirect()
                ->route('staff.spj.index')
                ->with('success', 'Dokumen SPJ berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->with('error', 'Terjadi kesalahan saat menghapus SPJ: ' . $e->getMessage());
        }
    }
}
