<?php

namespace App\Http\Controllers;

use App\Models\Fasilitas;
use App\Models\KategoriLaporan;
use App\Models\Lokasi;
use App\Models\LokasiFasilitas;
use App\Models\Pasar;
use App\Models\Sab;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StaffMasterController extends Controller
{
    protected function authorizeStaff(): void
    {
        if (auth()->user()->role->nama_role !== 'Staff Sarana dan Prasarana') {
            abort(403, 'Akses ditolak. Hanya Staff Sarana dan Prasarana yang berhak mengelola Master Data.');
        }
    }

    public function index(Request $request)
    {
        $this->authorizeStaff();

        $activeTab = $request->query('tab', 'pasar');
        $allowedTabs = ['pasar', 'lokasi', 'fasilitas', 'kategori', 'sab'];
        if (!in_array($activeTab, $allowedTabs)) {
            $activeTab = 'pasar';
        }

        $pasarList = Pasar::withCount('lokasi')->orderBy('id_pasar')->get();
        $lokasiList = Lokasi::with(['pasar', 'induk', 'anak'])->orderBy('id_pasar')->get();
        $fasilitasList = Fasilitas::with(['lokasiFasilitas.lokasi.pasar'])->orderBy('id_fasilitas')->get();
        $kategoriList = KategoriLaporan::orderBy('id_kategori')->get();
        $sabList = Sab::orderBy('id_sab')->get();

        return view('staff.master.index', compact(
            'activeTab',
            'pasarList',
            'lokasiList',
            'fasilitasList',
            'kategoriList',
            'sabList'
        ));
    }

    // ==================== PASAR ====================
    public function storePasar(Request $request)
    {
        $this->authorizeStaff();

        $validated = $request->validate([
            'nama_pasar' => 'required|string|max:255',
            'alamat' => 'nullable|string|max:500',
        ], [
            'nama_pasar.required' => 'Nama pasar wajib diisi.',
        ]);

        Pasar::create([
            'id_pasar' => Pasar::generateId(),
            'nama_pasar' => trim($validated['nama_pasar']),
            'alamat' => $validated['alamat'] ? trim($validated['alamat']) : null,
            'status_aktif' => 'Aktif',
        ]);

        return redirect()
            ->route('staff.master.index', ['tab' => 'pasar'])
            ->with('success', 'Data Pasar berhasil ditambahkan.');
    }

    public function updatePasar(Request $request, $id)
    {
        $this->authorizeStaff();

        $pasar = Pasar::findOrFail($id);

        $validated = $request->validate([
            'nama_pasar' => 'required|string|max:255',
            'alamat' => 'nullable|string|max:500',
        ], [
            'nama_pasar.required' => 'Nama pasar wajib diisi.',
        ]);

        $pasar->update([
            'nama_pasar' => trim($validated['nama_pasar']),
            'alamat' => $validated['alamat'] ? trim($validated['alamat']) : null,
        ]);

        return redirect()
            ->route('staff.master.index', ['tab' => 'pasar'])
            ->with('success', "Data Pasar '{$pasar->nama_pasar}' berhasil diperbarui.");
    }

    public function toggleStatusPasar($id)
    {
        $this->authorizeStaff();

        $pasar = Pasar::findOrFail($id);
        $newStatus = $pasar->status_aktif === 'Aktif' ? 'Nonaktif' : 'Aktif';

        $pasar->update(['status_aktif' => $newStatus]);

        return redirect()
            ->route('staff.master.index', ['tab' => 'pasar'])
            ->with('success', "Status Pasar '{$pasar->nama_pasar}' berhasil diubah menjadi {$newStatus}.");
    }

    // ==================== LOKASI ====================
    public function storeLokasi(Request $request)
    {
        $this->authorizeStaff();

        $validated = $request->validate([
            'id_pasar' => 'required|exists:pasar,id_pasar',
            'id_induk' => 'nullable|exists:lokasi,id_lokasi',
            'nama_lokasi' => 'required|string|max:255',
            'tipe_lokasi' => 'required|string|max:50',
            'tahun_mulai_dibangun' => 'nullable|integer|min:1900|max:2100',
            'tahun_selesai_dibangun' => 'nullable|integer|min:1900|max:2100',
            'luas_tanah' => 'nullable|numeric|min:0',
            'luas_bangunan' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string',
        ], [
            'id_pasar.required' => 'Pasar wajib dipilih.',
            'nama_lokasi.required' => 'Nama lokasi wajib diisi.',
            'tipe_lokasi.required' => 'Tipe lokasi wajib diisi.',
        ]);

        Lokasi::create([
            'id_lokasi' => Lokasi::generateId(),
            'id_pasar' => $validated['id_pasar'],
            'id_induk' => ($validated['id_induk'] ?? null) ?: null,
            'nama_lokasi' => trim($validated['nama_lokasi']),
            'tipe_lokasi' => trim($validated['tipe_lokasi']),
            'tahun_mulai_dibangun' => ($validated['tahun_mulai_dibangun'] ?? null) ?: null,
            'tahun_selesai_dibangun' => ($validated['tahun_selesai_dibangun'] ?? null) ?: null,
            'luas_tanah' => ($validated['luas_tanah'] ?? null) ?: null,
            'luas_bangunan' => ($validated['luas_bangunan'] ?? null) ?: null,
            'keterangan' => !empty($validated['keterangan']) ? trim($validated['keterangan']) : null,
            'status_aktif' => 'Aktif',
        ]);

        return redirect()
            ->route('staff.master.index', ['tab' => 'lokasi'])
            ->with('success', 'Data Lokasi berhasil ditambahkan.');
    }

    public function updateLokasi(Request $request, $id)
    {
        $this->authorizeStaff();

        $lokasi = Lokasi::findOrFail($id);

        $validated = $request->validate([
            'id_pasar' => 'required|exists:pasar,id_pasar',
            'id_induk' => 'nullable|exists:lokasi,id_lokasi',
            'nama_lokasi' => 'required|string|max:255',
            'tipe_lokasi' => 'required|string|max:50',
            'tahun_mulai_dibangun' => 'nullable|integer|min:1900|max:2100',
            'tahun_selesai_dibangun' => 'nullable|integer|min:1900|max:2100',
            'luas_tanah' => 'nullable|numeric|min:0',
            'luas_bangunan' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string',
        ], [
            'id_pasar.required' => 'Pasar wajib dipilih.',
            'nama_lokasi.required' => 'Nama lokasi wajib diisi.',
            'tipe_lokasi.required' => 'Tipe lokasi wajib diisi.',
        ]);

        $idInduk = $validated['id_induk'] ?? null;
        if ($idInduk === $lokasi->id_lokasi) {
            return back()->withInput()->with('error', 'Lokasi induk tidak boleh sama dengan lokasi itu sendiri.');
        }

        $lokasi->update([
            'id_pasar' => $validated['id_pasar'],
            'id_induk' => $idInduk ?: null,
            'nama_lokasi' => trim($validated['nama_lokasi']),
            'tipe_lokasi' => trim($validated['tipe_lokasi']),
            'tahun_mulai_dibangun' => ($validated['tahun_mulai_dibangun'] ?? null) ?: null,
            'tahun_selesai_dibangun' => ($validated['tahun_selesai_dibangun'] ?? null) ?: null,
            'luas_tanah' => ($validated['luas_tanah'] ?? null) ?: null,
            'luas_bangunan' => ($validated['luas_bangunan'] ?? null) ?: null,
            'keterangan' => !empty($validated['keterangan']) ? trim($validated['keterangan']) : null,
        ]);

        return redirect()
            ->route('staff.master.index', ['tab' => 'lokasi'])
            ->with('success', "Data Lokasi '{$lokasi->nama_lokasi}' berhasil diperbarui.");
    }

    public function toggleStatusLokasi($id)
    {
        $this->authorizeStaff();

        $lokasi = Lokasi::findOrFail($id);
        $newStatus = $lokasi->status_aktif === 'Aktif' ? 'Nonaktif' : 'Aktif';

        $lokasi->update(['status_aktif' => $newStatus]);

        return redirect()
            ->route('staff.master.index', ['tab' => 'lokasi'])
            ->with('success', "Status Lokasi '{$lokasi->nama_lokasi}' berhasil diubah menjadi {$newStatus}.");
    }

    // ==================== FASILITAS ====================
    public function storeFasilitas(Request $request)
    {
        $this->authorizeStaff();

        $validated = $request->validate([
            'nama_fasilitas' => 'required|string|max:255',
            'lokasi_ids' => 'nullable|array',
            'lokasi_ids.*' => 'exists:lokasi,id_lokasi',
        ], [
            'nama_fasilitas.required' => 'Nama fasilitas wajib diisi.',
        ]);

        DB::beginTransaction();
        try {
            $fasilitas = Fasilitas::create([
                'id_fasilitas' => Fasilitas::generateId(),
                'nama_fasilitas' => trim($validated['nama_fasilitas']),
                'status_aktif' => 'Aktif',
            ]);

            if (!empty($validated['lokasi_ids'])) {
                foreach ($validated['lokasi_ids'] as $idLokasi) {
                    LokasiFasilitas::create([
                        'id_lokasi' => $idLokasi,
                        'id_fasilitas' => $fasilitas->id_fasilitas,
                        'jumlah' => 1,
                    ]);
                }
            }

            DB::commit();

            return redirect()
                ->route('staff.master.index', ['tab' => 'fasilitas'])
                ->with('success', 'Data Fasilitas berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function updateFasilitas(Request $request, $id)
    {
        $this->authorizeStaff();

        $fasilitas = Fasilitas::findOrFail($id);

        $validated = $request->validate([
            'nama_fasilitas' => 'required|string|max:255',
            'lokasi_ids' => 'nullable|array',
            'lokasi_ids.*' => 'exists:lokasi,id_lokasi',
        ], [
            'nama_fasilitas.required' => 'Nama fasilitas wajib diisi.',
        ]);

        DB::beginTransaction();
        try {
            $fasilitas->update([
                'nama_fasilitas' => trim($validated['nama_fasilitas']),
            ]);

            LokasiFasilitas::where('id_fasilitas', $fasilitas->id_fasilitas)->delete();

            if (!empty($validated['lokasi_ids'])) {
                foreach ($validated['lokasi_ids'] as $idLokasi) {
                    LokasiFasilitas::create([
                        'id_lokasi' => $idLokasi,
                        'id_fasilitas' => $fasilitas->id_fasilitas,
                        'jumlah' => 1,
                    ]);
                }
            }

            DB::commit();

            return redirect()
                ->route('staff.master.index', ['tab' => 'fasilitas'])
                ->with('success', "Data Fasilitas '{$fasilitas->nama_fasilitas}' berhasil diperbarui.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function toggleStatusFasilitas($id)
    {
        $this->authorizeStaff();

        $fasilitas = Fasilitas::findOrFail($id);

        if (in_array(trim(strtolower($fasilitas->nama_fasilitas)), ['ruang lainnya', 'lainnya'])) {
            return back()->with('error', 'Fasilitas fallback "Lainnya" tidak dapat dinonaktifkan.');
        }

        $newStatus = $fasilitas->status_aktif === 'Aktif' ? 'Nonaktif' : 'Aktif';
        $fasilitas->update(['status_aktif' => $newStatus]);

        return redirect()
            ->route('staff.master.index', ['tab' => 'fasilitas'])
            ->with('success', "Status Fasilitas '{$fasilitas->nama_fasilitas}' berhasil diubah menjadi {$newStatus}.");
    }

    // ==================== KATEGORI LAPORAN ====================
    public function storeKategori(Request $request)
    {
        $this->authorizeStaff();

        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:kategori_laporan,nama_kategori',
        ], [
            'nama_kategori.required' => 'Nama kategori wajib diisi.',
            'nama_kategori.unique' => 'Nama kategori tersebut sudah ada.',
        ]);

        KategoriLaporan::create([
            'id_kategori' => KategoriLaporan::generateId(),
            'nama_kategori' => trim($validated['nama_kategori']),
            'status_aktif' => 'Aktif',
        ]);

        return redirect()
            ->route('staff.master.index', ['tab' => 'kategori'])
            ->with('success', 'Kategori Laporan baru berhasil ditambahkan.');
    }

    public function updateKategori(Request $request, $id)
    {
        $this->authorizeStaff();

        $kategori = KategoriLaporan::findOrFail($id);

        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:kategori_laporan,nama_kategori,' . $id . ',id_kategori',
        ], [
            'nama_kategori.required' => 'Nama kategori wajib diisi.',
            'nama_kategori.unique' => 'Nama kategori tersebut sudah ada.',
        ]);

        $kategori->update([
            'nama_kategori' => trim($validated['nama_kategori']),
        ]);

        return redirect()
            ->route('staff.master.index', ['tab' => 'kategori'])
            ->with('success', "Kategori Laporan '{$kategori->nama_kategori}' berhasil diperbarui.");
    }

    public function toggleStatusKategori($id)
    {
        $this->authorizeStaff();

        $kategori = KategoriLaporan::findOrFail($id);

        if (trim(strtolower($kategori->nama_kategori)) === 'lainnya') {
            return back()->with('error', 'Kategori fallback "Lainnya" tidak dapat dinonaktifkan.');
        }

        $newStatus = $kategori->status_aktif === 'Aktif' ? 'Nonaktif' : 'Aktif';
        $kategori->update(['status_aktif' => $newStatus]);

        return redirect()
            ->route('staff.master.index', ['tab' => 'kategori'])
            ->with('success', "Status Kategori '{$kategori->nama_kategori}' berhasil diubah menjadi {$newStatus}.");
    }

    // ==================== SAB ====================
    public function storeSab(Request $request)
    {
        $this->authorizeStaff();

        $validated = $request->validate([
            'nama_kebutuhan' => 'required|string|max:255',
            'satuan' => 'required|string|max:50',
            'harga_standar' => 'required|numeric|min:0',
        ], [
            'nama_kebutuhan.required' => 'Nama kebutuhan SAB wajib diisi.',
            'satuan.required' => 'Satuan wajib diisi.',
            'harga_standar.required' => 'Harga standar wajib diisi.',
        ]);

        Sab::create([
            'id_sab' => Sab::generateId(),
            'nama_kebutuhan' => trim($validated['nama_kebutuhan']),
            'satuan' => trim($validated['satuan']),
            'harga_standar' => $validated['harga_standar'],
            'status_aktif' => 'Aktif',
        ]);

        return redirect()
            ->route('staff.master.index', ['tab' => 'sab'])
            ->with('success', 'Data Master SAB berhasil ditambahkan.');
    }

    public function updateSab(Request $request, $id)
    {
        $this->authorizeStaff();

        $sab = Sab::findOrFail($id);

        $validated = $request->validate([
            'nama_kebutuhan' => 'required|string|max:255',
            'satuan' => 'required|string|max:50',
            'harga_standar' => 'required|numeric|min:0',
        ], [
            'nama_kebutuhan.required' => 'Nama kebutuhan SAB wajib diisi.',
            'satuan.required' => 'Satuan wajib diisi.',
            'harga_standar.required' => 'Harga standar wajib diisi.',
        ]);

        $sab->update([
            'nama_kebutuhan' => trim($validated['nama_kebutuhan']),
            'satuan' => trim($validated['satuan']),
            'harga_standar' => $validated['harga_standar'],
        ]);

        return redirect()
            ->route('staff.master.index', ['tab' => 'sab'])
            ->with('success', "Data Master SAB '{$sab->nama_kebutuhan}' berhasil diperbarui.");
    }

    public function toggleStatusSab($id)
    {
        $this->authorizeStaff();

        $sab = Sab::findOrFail($id);

        $newStatus = $sab->status_aktif === 'Aktif' ? 'Nonaktif' : 'Aktif';
        $sab->update(['status_aktif' => $newStatus]);

        return redirect()
            ->route('staff.master.index', ['tab' => 'sab'])
            ->with('success', "Status SAB '{$sab->nama_kebutuhan}' berhasil diubah menjadi {$newStatus}.");
    }
}
