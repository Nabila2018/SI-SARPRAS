<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use App\Models\Pasar;
use App\Models\DetailRab;
use App\Models\ProgresPerbaikan;
use App\Models\FotoProgres;
use App\Models\BuktiPembelian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class StaffLaporanController extends Controller
{
    /**
     * Export RAB ke berkas PDF.
     */
    public function exportRabPdf($id)
    {
        $laporan = Laporan::with(['lokasi.pasar', 'fasilitas', 'pelapor', 'detailRab'])->findOrFail($id);

        if ($laporan->detailRab->isEmpty()) {
            return back()->with('error', 'RAB belum memiliki rincian kebutuhan.');
        }

        $logoBase64 = '';
        if (extension_loaded('gd')) {
            $logoPath = public_path('images/Logo Dinas Perdagangan Kota Padang.png');
            if (file_exists($logoPath)) {
                $type = pathinfo($logoPath, PATHINFO_EXTENSION);
                $data = file_get_contents($logoPath);
                $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            }
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.rab', compact('laporan', 'logoBase64'));
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download("RAB_{$laporan->id_laporan}.pdf");
    }

    // Daftar semua laporan masuk (Staff)
    public function index(Request $request)
    {
        $query = $this->applyFilters(Laporan::query(), $request);

        $laporan = $query
            ->with(['lokasi.pasar', 'fasilitas', 'pelapor'])
            ->orderBy('tanggal_lapor', 'desc')
            ->paginate(5)
            ->appends($request->only(['search', 'pasar', 'status']));

        $pasarList = Pasar::orderBy('nama_pasar')->get();
        $statusList = ['Menunggu', 'Diproses', 'Selesai', 'Dikembalikan', 'Ditolak'];

        return view('staff.laporan.index', compact('laporan', 'pasarList', 'statusList'));
    }

    public function storeEvaluation(Request $request, $id)
    {
        $laporan = Laporan::findOrFail($id);

        if (auth()->user()->role->nama_role !== 'Staff Sarana dan Prasarana') {
            abort(403, 'Akses ditolak.');
        }

        if ($laporan->status_laporan !== 'Menunggu') {
            return back()->with('error', 'Evaluasi hanya dapat dilakukan saat laporan masih berstatus Menunggu.');
        }

        $data = $request->validate([
            'kategori_kerusakan' => ['required', 'in:Ringan,Sedang,Berat'],
            'catatan_pemeriksaan' => ['nullable', 'string', 'max:2000'],
        ]);

        $laporan->update([
            'kategori_kerusakan' => $data['kategori_kerusakan'],
            'catatan_pemeriksaan' => $data['catatan_pemeriksaan'],
        ]);

        return redirect()
            ->route('laporan.show', ['id' => $laporan->id_laporan, 'tab' => 'evaluasi'])
            ->with('success', 'Evaluasi berhasil disimpan.');
    }

    public function forwardToKabid($id)
    {
        $laporan = Laporan::findOrFail($id);

        if (auth()->user()->role->nama_role !== 'Staff Sarana dan Prasarana') {
            abort(403, 'Akses ditolak.');
        }

        if ($laporan->status_laporan !== 'Menunggu') {
            return back()->with('error', 'Laporan ini tidak dapat diteruskan karena status sudah berubah.');
        }

        if (empty($laporan->kategori_kerusakan) && empty($laporan->catatan_pemeriksaan)) {
            return back()->with('error', 'Evaluasi harus diisi sebelum melanjutkan ke Kabid.');
        }

        $laporan->update([
            'status_laporan' => 'Diproses',
        ]);

        return redirect()
            ->route('laporan.show', ['id' => $laporan->id_laporan, 'tab' => 'evaluasi'])
            ->with('success', 'Laporan berhasil diteruskan ke Kabid.');
    }

        /**
     * Tampilkan halaman RAB (buat baru atau edit).
     */
    public function showRab($id)
    {
        $laporan = Laporan::with(['lokasi.pasar', 'fasilitas', 'pelapor', 'detailRab'])
            ->findOrFail($id);

        // Cek role
        if (auth()->user()->role->nama_role !== 'Staff Sarana dan Prasarana') {
            abort(403, 'Akses ditolak.');
        }

        // Cek jika RAB belum dibuat dan status laporan belum disetujui
        if (!$laporan->detailRab()->exists() && in_array($laporan->status_laporan, ['Menunggu', 'Ditolak', 'Dikembalikan'])) {
            return back()->with('error', 'RAB hanya dapat diakses jika laporan sudah disetujui.');
        }

        return view('staff.laporan.rab', compact('laporan'));
    }

        /**
     * Simpan atau update RAB (detail_rab).
     */
    public function storeRab(Request $request, $id)
    {
        $laporan = Laporan::findOrFail($id);

        if (auth()->user()->role->nama_role !== 'Staff Sarana dan Prasarana') {
            abort(403, 'Akses ditolak.');
        }

        if ($laporan->status_laporan !== 'Disetujui') {
            return back()->with('error', 'RAB hanya dapat dibuat jika laporan sudah disetujui.');
        }

        // Validasi: minimal 1 baris detail
        $validated = $request->validate([
            'rincian_kebutuhan' => ['required', 'array', 'min:1'],
            'rincian_kebutuhan.*' => ['required', 'string', 'max:150'],
            'volume' => ['required', 'array', 'min:1'],
            'volume.*' => ['required', 'numeric', 'min:0.001'],
            'satuan' => ['required', 'array', 'min:1'],
            'satuan.*' => ['required', 'string', 'max:30'],
            'harga_satuan' => ['required', 'array', 'min:1'],
            'harga_satuan.*' => ['required', 'numeric', 'min:1'],
        ]);

        DB::beginTransaction();

        try {
            // Hapus detail_rab lama (kalau edit)
            $laporan->detailRab()->delete();

            // Insert detail_rab baru
            $details = [];
            $latestRab = DetailRab::orderBy('id_detail_rab', 'desc')->first();
            $startNumRab = $latestRab ? ((int) substr($latestRab->id_detail_rab, 3)) + 1 : 1;

            foreach ($validated['rincian_kebutuhan'] as $index => $rincian) {
                $details[] = [
                    'id_detail_rab' => 'RAB' . str_pad($startNumRab + $index, 3, '0', STR_PAD_LEFT),
                    'id_laporan' => $laporan->id_laporan,
                    'rincian_kebutuhan' => $rincian,
                    'volume' => $validated['volume'][$index],
                    'satuan' => $validated['satuan'][$index],
                    'harga_satuan' => $validated['harga_satuan'][$index],
                ];
            }

            DetailRab::insert($details);

            // Update tanggal input RAB & status verifikasi RAB ke Kabid
            $laporan->update([
                'status_verifikasi_rab' => 'Menunggu',
                'tanggal_input_rab' => now(),
            ]);

            DB::commit();

            return redirect()
                ->route('laporan.show', ['id' => $laporan->id_laporan, 'tab' => 'rab'])
                ->with('success', 'RAB berhasil dibuat dan diteruskan ke Kabid.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

        /**
     * Teruskan RAB ke Kabid untuk verifikasi.
     */
    public function forwardRab($id)
    {
        $laporan = Laporan::with('detailRab')->findOrFail($id);

        if (auth()->user()->role->nama_role !== 'Staff Sarana dan Prasarana') {
            abort(403, 'Akses ditolak.');
        }

        if ($laporan->status_laporan !== 'Disetujui') {
            return back()->with('error', 'RAB hanya dapat diteruskan jika laporan sudah disetujui.');
        }

        if ($laporan->detailRab->isEmpty()) {
            return back()->with('error', 'RAB tidak boleh kosong. Isi detail kebutuhan terlebih dahulu.');
        }

        $laporan->update([
            'status_verifikasi_rab' => 'Menunggu',
            'tanggal_input_rab' => now(),
        ]);

        return redirect()
            ->route('staff.laporan.rab.show', $laporan->id_laporan)
            ->with('success', 'RAB berhasil diteruskan ke Kabid.');
    }
        /**
     * Daftar RAB yang sudah dibuat oleh Staff.
     */
        /**
     * Daftar RAB yang sudah dibuat oleh Staff.
     */
    public function indexRab(Request $request)
    {
        if (auth()->user()->role->nama_role !== 'Staff Sarana dan Prasarana') {
            abort(403, 'Akses ditolak.');
        }

        $query = Laporan::with(['lokasi.pasar', 'fasilitas', 'pelapor', 'detailRab'])
            ->whereHas('detailRab');  // ← FIX: Tampilkan yang punya detail_rab

        // Filter status RAB
        if ($request->filled('status')) {
            $query->where('status_verifikasi_rab', $request->status);
        }

        // Filter search
        $search = trim((string) $request->input('search', ''));
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->whereHas('lokasi.pasar', function ($subQuery) use ($search) {
                    $subQuery->where('nama_pasar', 'like', "%{$search}%");
                })->orWhereHas('fasilitas', function ($subQuery) use ($search) {
                    $subQuery->where('nama_fasilitas', 'like', "%{$search}%");
                });
            });
        }

        $rabList = $query->orderBy('tanggal_input_rab', 'desc')->paginate(10)->appends($request->only(['search', 'status']));

        $statusList = ['Menunggu', 'Disetujui', 'Dikembalikan'];

        return view('staff.rab.index', compact('rabList', 'statusList'));
    }

    protected function applyFilters($query, Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $pasarId = $request->input('pasar');
        $status = $request->input('status');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->whereHas('lokasi.pasar', function ($subQuery) use ($search) {
                    $subQuery->where('nama_pasar', 'like', "%{$search}%");
                })->orWhereHas('fasilitas', function ($subQuery) use ($search) {
                    $subQuery->where('nama_fasilitas', 'like', "%{$search}%");
                });
            });
        }

        if ($pasarId) {
            $query->whereHas('lokasi.pasar', function ($subQuery) use ($pasarId) {
                $subQuery->where('id_pasar', $pasarId);
            });
        }

        if ($status) {
            $query->where('status_laporan', $status);
        }

        return $query;
    }

    /**
     * Simpan progres perbaikan laporan (0%, 50%, 100%).
     */
    public function storeProgres(Request $request, $id)
    {
        if (auth()->user()->role->nama_role !== 'Staff Sarana dan Prasarana') {
            abort(403, 'Akses ditolak.');
        }

        $laporan = Laporan::with('progresPerbaikan')->findOrFail($id);

        if ($laporan->status_verifikasi_rab !== 'Disetujui') {
            return back()->with('error', 'Progres perbaikan hanya dapat ditambahkan setelah RAB disetujui oleh Kabid.');
        }

        $existingStages = $laporan->progresPerbaikan->pluck('persentase_penyelesaian')->toArray();

        // Tentukan tahap selanjutnya
        $nextStage = null;
        if (!in_array('0', $existingStages)) {
            $nextStage = '0';
        } elseif (!in_array('50', $existingStages)) {
            $nextStage = '50';
        } elseif (!in_array('100', $existingStages)) {
            $nextStage = '100';
        } else {
            return back()->with('error', 'Seluruh tahap progres perbaikan (100%) sudah lengkap.');
        }

        $validated = $request->validate([
            'keterangan_perkembangan' => ['required', 'string', 'max:2000'],
            'foto_progres' => ['required', 'array', 'min:1', 'max:5'],
            'foto_progres.*' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:4096'],
        ], [
            'keterangan_perkembangan.required' => 'Keterangan perkembangan wajib diisi.',
            'foto_progres.required' => 'Minimal 1 foto progres wajib diunggah.',
            'foto_progres.min' => 'Minimal 1 foto progres wajib diunggah.',
            'foto_progres.max' => 'Maksimal 5 foto progres yang dapat diunggah sekaligus.',
            'foto_progres.*.image' => 'File foto harus berupa gambar (jpg, jpeg, png).',
            'foto_progres.*.max' => 'Ukuran file foto maksimal 4 MB per foto.',
        ]);

        DB::beginTransaction();

        try {
            $progres = ProgresPerbaikan::create([
                'id_progres' => ProgresPerbaikan::generateId(),
                'id_laporan' => $laporan->id_laporan,
                'persentase_penyelesaian' => $nextStage,
                'keterangan_perkembangan' => $validated['keterangan_perkembangan'],
                'tanggal_update' => now(),
            ]);

            // Jika progres perbaikan sudah mencapai 100%, ubah status_laporan menjadi 'Selesai'
            if ((string)$nextStage === '100') {
                $laporan->update(['status_laporan' => 'Selesai']);
            }

            if ($request->hasFile('foto_progres')) {
                foreach ($request->file('foto_progres') as $file) {
                    $path = $file->store('progres', 'public');
                    FotoProgres::create([
                        'id_foto_progres' => FotoProgres::generateId(),
                        'id_progres' => $progres->id_progres,
                        'file_foto' => $path,
                    ]);
                }
            }

            DB::commit();

            return redirect()
                ->route('laporan.show', ['id' => $laporan->id_laporan, 'tab' => 'progress'])
                ->with('success', "Progres perbaikan Tahap {$nextStage}% berhasil disimpan.");
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Unggah bukti pembelian baru (Staff).
     */
    public function storeBuktiPembelian(Request $request, $id)
    {
        if (auth()->user()->role->nama_role !== 'Staff Sarana dan Prasarana') {
            abort(403, 'Akses ditolak.');
        }

        $laporan = Laporan::findOrFail($id);

        if ($laporan->status_verifikasi_rab !== 'Disetujui') {
            return back()->with('error', 'Bukti pembelian hanya dapat diunggah setelah RAB disetujui oleh Kabid.');
        }

        $validated = $request->validate([
            'file_bukti' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'nominal' => ['required', 'numeric', 'min:1'],
            'tanggal_bukti' => ['required', 'date'],
        ], [
            'file_bukti.required' => 'Berkas bukti pembelian wajib diunggah.',
            'file_bukti.mimes' => 'Format berkas harus berupa PDF, JPG, JPEG, atau PNG.',
            'file_bukti.max' => 'Ukuran berkas maksimal 5 MB.',
            'nominal.required' => 'Nominal transaksi wajib diisi.',
            'tanggal_bukti.required' => 'Tanggal nota/bukti transaksi wajib diisi.',
        ]);

        $file = $request->file('file_bukti');
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();
        $cleanName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $originalName);
        $filename = time() . '_' . Str::random(5) . '_' . $cleanName . '.' . $extension;
        $path = $file->storeAs('bukti_pembelian', $filename, 'public');

        BuktiPembelian::create([
            'id_bukti' => BuktiPembelian::generateId(),
            'id_laporan' => $laporan->id_laporan,
            'file_bukti' => $path,
            'nominal' => $validated['nominal'],
            'tanggal_bukti' => $validated['tanggal_bukti'],
        ]);

        return redirect()
            ->route('laporan.show', ['id' => $laporan->id_laporan, 'tab' => 'bukti'])
            ->with('success', 'Bukti pembelian berhasil diunggah.');
    }

    /**
     * Hapus bukti pembelian (Staff).
     */
    public function deleteBuktiPembelian($id, $buktiId)
    {
        if (auth()->user()->role->nama_role !== 'Staff Sarana dan Prasarana') {
            abort(403, 'Akses ditolak.');
        }

        $bukti = BuktiPembelian::where('id_laporan', $id)->findOrFail($buktiId);

        if (Storage::disk('public')->exists($bukti->file_bukti)) {
            Storage::disk('public')->delete($bukti->file_bukti);
        }

        $bukti->delete();

        return redirect()
            ->route('laporan.show', ['id' => $id, 'tab' => 'bukti'])
            ->with('success', 'Bukti pembelian berhasil dihapus.');
    }

    /**
     * Unduh berkas bukti pembelian (Staff, Kabid, Kadis).
     */
    public function downloadBuktiPembelian($id, $buktiId)
    {
        $bukti = BuktiPembelian::where('id_laporan', $id)->findOrFail($buktiId);

        if (!Storage::disk('public')->exists($bukti->file_bukti)) {
            return back()->with('error', 'Berkas bukti pembelian tidak ditemukan di server.');
        }

        return Storage::disk('public')->download($bukti->file_bukti);
    }
}