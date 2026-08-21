<?php

namespace App\Http\Controllers;

use App\Models\Sab;
use Illuminate\Http\Request;

class StaffSabController extends Controller
{
    /**
     * Tampilkan daftar Master SAB (Khusus Staff Sarpras).
     */
    public function index(Request $request)
    {
        return redirect()->route('staff.master.index', ['tab' => 'sab']);
    }

    /**
     * Simpan data Master SAB baru.
     */
    public function store(Request $request)
    {
        if (auth()->user()->role->nama_role !== 'Staff Sarana dan Prasarana') {
            abort(403, 'Akses ditolak.');
        }

        $validated = $request->validate([
            'nama_kebutuhan' => ['required', 'string', 'max:150'],
            'satuan' => ['required', 'string', 'max:30'],
            'harga_standar' => ['required', 'numeric', 'min:0'],
            'status_aktif' => ['nullable', 'in:Aktif,Nonaktif'],
        ], [
            'nama_kebutuhan.required' => 'Nama kebutuhan wajib diisi.',
            'satuan.required' => 'Satuan wajib diisi.',
            'harga_standar.required' => 'Harga standar wajib diisi.',
            'harga_standar.min' => 'Harga standar minimal 0.',
        ]);

        $sab = Sab::create([
            'nama_kebutuhan' => $validated['nama_kebutuhan'],
            'satuan' => $validated['satuan'],
            'harga_standar' => $validated['harga_standar'],
            'status_aktif' => $validated['status_aktif'] ?? 'Aktif',
        ]);

        return redirect()
            ->route('staff.sab.index')
            ->with('success', "Master SAB '{$sab->nama_kebutuhan}' ({$sab->id_sab}) berhasil ditambahkan.");
    }

    /**
     * Perbarui data Master SAB.
     */
    public function update(Request $request, $id)
    {
        if (auth()->user()->role->nama_role !== 'Staff Sarana dan Prasarana') {
            abort(403, 'Akses ditolak.');
        }

        $sab = Sab::findOrFail($id);

        $validated = $request->validate([
            'nama_kebutuhan' => ['required', 'string', 'max:150'],
            'satuan' => ['required', 'string', 'max:30'],
            'harga_standar' => ['required', 'numeric', 'min:0'],
            'status_aktif' => ['required', 'in:Aktif,Nonaktif'],
        ], [
            'nama_kebutuhan.required' => 'Nama kebutuhan wajib diisi.',
            'satuan.required' => 'Satuan wajib diisi.',
            'harga_standar.required' => 'Harga standar wajib diisi.',
        ]);

        $sab->update($validated);

        return redirect()
            ->route('staff.sab.index')
            ->with('success', "Master SAB '{$sab->nama_kebutuhan}' ({$sab->id_sab}) berhasil diperbarui.");
    }

    /**
     * Toggle status aktif / nonaktif SAB.
     */
    public function toggleStatus($id)
    {
        if (auth()->user()->role->nama_role !== 'Staff Sarana dan Prasarana') {
            abort(403, 'Akses ditolak.');
        }

        $sab = Sab::findOrFail($id);
        $newStatus = $sab->status_aktif === 'Aktif' ? 'Nonaktif' : 'Aktif';
        $sab->update(['status_aktif' => $newStatus]);

        return redirect()
            ->route('staff.sab.index')
            ->with('success', "Status Master SAB '{$sab->nama_kebutuhan}' diubah menjadi {$newStatus}.");
    }
}
