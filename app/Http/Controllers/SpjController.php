<?php

namespace App\Http\Controllers;

use App\Models\Rab;
use App\Models\Spj;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        $query = Spj::with(['uploader', 'rab.laporan.lokasi.pasar']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_pekerjaan', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%")
                  ->orWhere('id_rab', 'like', "%{$search}%")
                  ->orWhereHas('rab.laporan.lokasi.pasar', function ($qp) use ($search) {
                      $qp->where('nama_pasar', 'like', "%{$search}%");
                  });
            });
        }

        $spjList = $query->orderBy('tanggal_upload', 'desc')
            ->paginate(10)
            ->appends($request->only('search'));

        return view('staff.spj.index', compact('spjList'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorizeStaff();

        // Ambil RAB yang belum memiliki SPJ, berstatus Disetujui, dan seluruh laporannya sudah 100% selesai
        $rabList = Rab::whereDoesntHave('spj')
            ->where('status_verifikasi_rab', 'Disetujui')
            ->with(['laporan.lokasi.pasar', 'laporan.fasilitas', 'laporan.progresPerbaikan'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->filter(function ($rab) {
                if ($rab->laporan->isEmpty()) {
                    return false;
                }
                // Pastikan seluruh laporan dalam RAB 100% selesai
                return $rab->laporan->every(function ($laporan) {
                    return $laporan->status_laporan === 'Selesai' || $laporan->latest_progress_percentage === 100;
                });
            })
            ->values();

        return view('staff.spj.create', compact('rabList'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorizeStaff();

        $request->validate([
            'id_rab' => 'required|exists:rab,id_rab',
            'nama_pekerjaan' => 'required|string|max:255',
            'periode_mulai' => 'required|date',
            'periode_selesai' => 'required|date|after_or_equal:periode_mulai',
            'keterangan' => 'nullable|string',
            'file_spj' => 'required|mimes:pdf|max:5120',
        ], [
            'id_rab.required' => 'Pilihan RAB wajib diisi.',
            'id_rab.exists' => 'RAB yang dipilih tidak ditemukan.',
            'nama_pekerjaan.required' => 'Nama pekerjaan wajib diisi.',
            'periode_mulai.required' => 'Periode mulai wajib diisi.',
            'periode_selesai.required' => 'Periode selesai wajib diisi.',
            'periode_selesai.after_or_equal' => 'Periode selesai harus setelah atau sama dengan periode mulai.',
            'file_spj.required' => 'File SPJ wajib diunggah.',
            'file_spj.mimes' => 'File SPJ harus berformat PDF.',
            'file_spj.max' => 'Ukuran file SPJ maksimal 5 MB.',
        ]);

        $rab = Rab::with(['laporan.progresPerbaikan', 'spj'])->findOrFail($request->id_rab);

        if ($rab->spj) {
            return back()->withInput()->with('error', 'RAB ini sudah memiliki dokumen SPJ.');
        }

        $allDone = $rab->laporan->isNotEmpty() && $rab->laporan->every(function ($laporan) {
            return $laporan->status_laporan === 'Selesai' || $laporan->latest_progress_percentage === 100;
        });

        if (!$allDone) {
            return back()->withInput()->with('error', 'RAB belum dapat dibuatkan SPJ karena masih ada laporan yang belum selesai 100%.');
        }

        DB::beginTransaction();

        try {
            $file = $request->file('file_spj');
            $fileName = $file->getClientOriginalName();
            $filePath = $file->storeAs('spj', $fileName, 'public');

            $spj = Spj::create([
                'id_spj' => Spj::generateId(),
                'id_rab' => $rab->id_rab,
                'nama_pekerjaan' => $request->nama_pekerjaan,
                'periode_mulai' => $request->periode_mulai,
                'periode_selesai' => $request->periode_selesai,
                'keterangan' => $request->keterangan,
                'file_spj' => $filePath,
                'uploaded_by' => Auth::id(),
                'tanggal_upload' => now(),
            ]);

            DB::commit();

            return redirect()
                ->route('staff.spj.index')
                ->with('success', "Dokumen SPJ '{$spj->nama_pekerjaan}' ({$spj->id_spj}) berhasil disimpan.");
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
            'rab.laporan.lokasi.pasar',
            'rab.laporan.fasilitas',
            'rab.laporan.pelapor',
            'rab.laporan.progresPerbaikan',
            'rab.detailRab',
        ])->findOrFail($id);

        return view('staff.spj.show', compact('spj'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $this->authorizeStaff();

        $spj = Spj::with([
            'uploader',
            'rab.laporan.lokasi.pasar',
            'rab.laporan.fasilitas',
            'rab.laporan.progresPerbaikan',
        ])->findOrFail($id);

        return view('staff.spj.edit', compact('spj'));
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
        ], [
            'nama_pekerjaan.required' => 'Nama pekerjaan wajib diisi.',
            'periode_mulai.required' => 'Periode mulai wajib diisi.',
            'periode_selesai.required' => 'Periode selesai wajib diisi.',
            'periode_selesai.after_or_equal' => 'Periode selesai harus setelah atau sama dengan periode mulai.',
            'file_spj.mimes' => 'File SPJ harus berformat PDF.',
            'file_spj.max' => 'Ukuran file SPJ maksimal 5 MB.',
        ]);

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

            DB::commit();

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
            $spj->delete();

            DB::commit();

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
