<?php

namespace App\Http\Controllers;

use App\Models\LaporanRealisasiTahunan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LaporanRealisasiTahunanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = LaporanRealisasiTahunan::with('uploader');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('tahun_anggaran', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        $realisasiList = $query->orderBy('tahun_anggaran', 'desc')
            ->paginate(5)
            ->appends($request->only('search'));

        return view('staff.realisasi-tahunan.index', compact('realisasiList'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('staff.realisasi-tahunan.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $currentYear = now()->year;

        $request->validate([
            'tahun_anggaran' => 'required|digits:4|integer|min:2000|max:' . $currentYear . '|unique:laporan_realisasi_tahunan,tahun_anggaran',
            'keterangan' => 'nullable|string',
            'file_realisasi' => 'required|mimes:pdf|max:5120',
        ], [
            'tahun_anggaran.required' => 'Tahun anggaran wajib diisi.',
            'tahun_anggaran.digits' => 'Tahun anggaran harus berupa 4 digit angka.',
            'tahun_anggaran.integer' => 'Tahun anggaran harus berupa angka.',
            'tahun_anggaran.min' => 'Tahun anggaran tidak valid.',
            'tahun_anggaran.max' => 'Tahun anggaran tidak boleh melebihi tahun saat ini (' . $currentYear . ').',
            'tahun_anggaran.unique' => 'Laporan realisasi untuk tahun anggaran ini sudah ada.',
            'file_realisasi.required' => 'File laporan realisasi wajib diunggah.',
            'file_realisasi.mimes' => 'File laporan realisasi harus berformat PDF.',
            'file_realisasi.max' => 'Ukuran file laporan realisasi maksimal 5 MB.',
        ]);

        DB::beginTransaction();

        try {
            // Simpan file ke storage/app/public/realisasi-tahunan
            $filePath = $request->file('file_realisasi')->store('realisasi-tahunan', 'public');

            LaporanRealisasiTahunan::create([
                'tahun_anggaran' => $request->tahun_anggaran,
                'keterangan' => $request->keterangan,
                'file_realisasi' => $filePath,
                'uploaded_by' => Auth::id(),
                'tanggal_upload' => now(),
            ]);

            DB::commit();

            return redirect()
                ->route('staff.realisasi-tahunan.index')
                ->with('success', 'Laporan Realisasi Tahunan berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();

            if (isset($filePath) && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }

            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan Laporan Realisasi Tahunan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $realisasiTahunan = LaporanRealisasiTahunan::with('uploader')->findOrFail($id);

        return view('staff.realisasi-tahunan.show', compact('realisasiTahunan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $realisasiTahunan = LaporanRealisasiTahunan::with('uploader')->findOrFail($id);

        return view('staff.realisasi-tahunan.edit', compact('realisasiTahunan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $realisasiTahunan = LaporanRealisasiTahunan::findOrFail($id);

        $currentYear = now()->year;

        $request->validate([
            'tahun_anggaran' => 'required|digits:4|integer|min:2000|max:' . $currentYear . '|unique:laporan_realisasi_tahunan,tahun_anggaran,' . $id . ',id_realisasi',
            'keterangan' => 'nullable|string',
            'file_realisasi' => 'nullable|mimes:pdf|max:5120',
        ], [
            'tahun_anggaran.required' => 'Tahun anggaran wajib diisi.',
            'tahun_anggaran.digits' => 'Tahun anggaran harus berupa 4 digit angka.',
            'tahun_anggaran.integer' => 'Tahun anggaran harus berupa angka.',
            'tahun_anggaran.min' => 'Tahun anggaran tidak valid.',
            'tahun_anggaran.max' => 'Tahun anggaran tidak boleh melebihi tahun saat ini (' . $currentYear . ').',
            'tahun_anggaran.unique' => 'Laporan realisasi untuk tahun anggaran ini sudah ada.',
            'file_realisasi.mimes' => 'File laporan realisasi harus berformat PDF.',
            'file_realisasi.max' => 'Ukuran file laporan realisasi maksimal 5 MB.',
        ]);

        DB::beginTransaction();

        try {
            $oldFilePath = $realisasiTahunan->file_realisasi;
            $newFilePath = null;

            if ($request->hasFile('file_realisasi')) {
                $newFilePath = $request->file('file_realisasi')->store('realisasi-tahunan', 'public');
                $realisasiTahunan->file_realisasi = $newFilePath;
            }

            $realisasiTahunan->tahun_anggaran = $request->tahun_anggaran;
            $realisasiTahunan->keterangan = $request->keterangan;
            $realisasiTahunan->save();

            DB::commit();

            if ($newFilePath && $oldFilePath && Storage::disk('public')->exists($oldFilePath)) {
                Storage::disk('public')->delete($oldFilePath);
            }

            return redirect()
                ->route('staff.realisasi-tahunan.index')
                ->with('success', 'Laporan Realisasi Tahunan berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($newFilePath && Storage::disk('public')->exists($newFilePath)) {
                Storage::disk('public')->delete($newFilePath);
            }

            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat memperbarui Laporan Realisasi Tahunan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $realisasiTahunan = LaporanRealisasiTahunan::findOrFail($id);

        DB::beginTransaction();

        try {
            $filePath = $realisasiTahunan->file_realisasi;

            $realisasiTahunan->delete();

            DB::commit();

            if ($filePath && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }

            return redirect()
                ->route('staff.realisasi-tahunan.index')
                ->with('success', 'Laporan Realisasi Tahunan berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->with('error', 'Terjadi kesalahan saat menghapus Laporan Realisasi Tahunan: ' . $e->getMessage());
        }
    }
}
