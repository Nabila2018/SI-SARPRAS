<?php

namespace App\Http\Controllers;

use App\Models\DetailRab;
use App\Models\Laporan;
use App\Models\Rab;
use App\Models\Sab;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StaffRabController extends Controller
{
    protected function authorizeStaff()
    {
        if (auth()->user()->role->nama_role !== 'Staff Sarana dan Prasarana') {
            abort(403, 'Akses ditolak. Hanya Staff Sarana dan Prasarana yang berhak melakukan tindakan ini.');
        }
    }

    /**
     * Tampilkan daftar RAB yang telah dibuat.
     */
    public function index(Request $request)
    {
        $this->authorizeStaff();

        $query = Rab::with(['laporan.lokasi.pasar', 'detailRab', 'spj']);

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('id_rab', 'LIKE', "%{$search}%")
                  ->orWhereHas('laporan.lokasi.pasar', function ($qp) use ($search) {
                      $qp->where('nama_pasar', 'LIKE', "%{$search}%");
                  })
                  ->orWhereHas('detailRab', function ($qd) use ($search) {
                      $qd->where('rincian_kebutuhan', 'LIKE', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status_verifikasi_rab', $request->status);
        }

        $rabList = $query->orderBy('created_at', 'desc')->paginate(10)->appends($request->only(['search', 'status']));
        $statusList = ['Draft', 'Menunggu', 'Disetujui', 'Dikembalikan'];

        return view('staff.rab.index', compact('rabList', 'statusList'));
    }

    /**
     * Tampilkan form pembuatan RAB baru.
     */
    public function create()
    {
        $this->authorizeStaff();

        // Laporan eligible: Evaluasi disetujui Kabid, kategori Ringan / Sedang, dan belum punya RAB
        $laporanEligible = Laporan::with(['lokasi.pasar', 'fasilitas'])
            ->whereNull('id_rab')
            ->whereIn('status_laporan', ['Disetujui', 'Diproses'])
            ->whereIn('kategori_kerusakan', ['Ringan', 'Sedang'])
            ->orderBy('tanggal_lapor', 'desc')
            ->get();

        $pasarList = $laporanEligible
            ->map(function ($lap) {
                return $lap->lokasi->pasar ?? null;
            })
            ->filter()
            ->unique('id_pasar')
            ->values();

        $sabList = Sab::where('status_aktif', true)
            ->orderBy('nama_kebutuhan', 'asc')
            ->get();

        return view('staff.rab.create', compact('laporanEligible', 'sabList', 'pasarList'));
    }

    /**
     * Simpan RAB baru ke database.
     */
    public function store(Request $request)
    {
        $this->authorizeStaff();

        $validated = $request->validate([
            'laporan_ids' => ['required', 'array', 'min:1'],
            'laporan_ids.*' => ['required', 'exists:laporan,id_laporan'],
            'rincian_kebutuhan' => ['required', 'array', 'min:1'],
            'rincian_kebutuhan.*' => ['required', 'string', 'max:150'],
            'volume' => ['required', 'array', 'min:1'],
            'volume.*' => ['required', 'numeric', 'min:0.001'],
            'satuan' => ['required', 'array', 'min:1'],
            'satuan.*' => ['required', 'string', 'max:30'],
            'harga_satuan' => ['required', 'array', 'min:1'],
            'harga_satuan.*' => ['required', 'numeric', 'min:1'],
            'id_sab' => ['nullable', 'array'],
            'id_sab.*' => ['nullable', 'exists:sab,id_sab'],
            'action' => ['nullable', 'string'],
        ], [
            'laporan_ids.required' => 'Minimal 1 laporan wajib dipilih untuk RAB.',
            'laporan_ids.min' => 'Minimal 1 laporan wajib dipilih untuk RAB.',
            'rincian_kebutuhan.required' => 'Minimal 1 rincian kebutuhan wajib diisi.',
            'rincian_kebutuhan.*.required' => 'Rincian kebutuhan wajib diisi.',
            'volume.*.required' => 'Volume wajib diisi.',
            'satuan.*.required' => 'Satuan wajib diisi.',
            'harga_satuan.*.required' => 'Harga satuan wajib diisi.',
        ]);

        // Verifikasi bahwa seluruh laporan yang dipilih belum terikat RAB lain
        $alreadyAssigned = Laporan::whereIn('id_laporan', $validated['laporan_ids'])
            ->whereNotNull('id_rab')
            ->count();

        if ($alreadyAssigned > 0) {
            return back()->withInput()->with('error', 'Beberapa laporan yang dipilih sudah terikat pada RAB lain.');
        }

        // Verifikasi bahwa seluruh laporan yang dipilih berasal dari lokasi pasar yang sama
        $selectedLaporan = Laporan::with('lokasi.pasar')->whereIn('id_laporan', $validated['laporan_ids'])->get();
        $pasarIds = $selectedLaporan->map(function ($lap) {
            return $lap->lokasi->id_pasar ?? null;
        })->filter()->unique();

        if ($pasarIds->count() > 1) {
            return back()->withInput()->with('error', 'Seluruh laporan yang dipilih untuk 1 RAB harus berasal dari lokasi pasar yang sama.');
        }

        DB::beginTransaction();

        try {
            $isSubmit = ($request->input('action') === 'submit');
            $statusRab = $isSubmit ? 'Menunggu' : 'Draft';

            $rab = Rab::create([
                'id_rab' => Rab::generateId(),
                'status_verifikasi_rab' => $statusRab,
                'catatan_revisi_rab' => null,
            ]);

            // Linkkan laporan yang dipilih ke RAB ini
            Laporan::whereIn('id_laporan', $validated['laporan_ids'])
                ->update(['id_rab' => $rab->id_rab]);

            // Save detail_rab
            $details = [];
            $latestDetail = DetailRab::orderBy('id_detail_rab', 'desc')->first();
            $startNum = $latestDetail ? ((int) substr($latestDetail->id_detail_rab, 3)) + 1 : 1;

            foreach ($validated['rincian_kebutuhan'] as $index => $rincian) {
                $details[] = [
                    'id_detail_rab' => 'RAB' . str_pad($startNum + $index, 3, '0', STR_PAD_LEFT),
                    'id_rab' => $rab->id_rab,
                    'id_sab' => $validated['id_sab'][$index] ?? null,
                    'rincian_kebutuhan' => $rincian,
                    'volume' => $validated['volume'][$index],
                    'satuan' => $validated['satuan'][$index],
                    'harga_satuan' => $validated['harga_satuan'][$index],
                ];
            }

            DetailRab::insert($details);

            if ($isSubmit) {
                $firstLap = $rab->laporan()->first()?->id_laporan;
                \App\Services\NotificationService::sendToRole(
                    'Kepala Bidang',
                    'Pengajuan RAB Baru',
                    "Staff mengajukan RAB baru {$rab->id_rab} untuk diverifikasi Kabid.",
                    route('kabid.rab.show', $rab->id_rab),
                    $firstLap
                );
            }

            DB::commit();

            $msg = $isSubmit ? 'RAB berhasil dibuat dan diajukan ke Kabid.' : 'Draft RAB berhasil disimpan.';
            return redirect()->route('staff.rab.show', $rab->id_rab)->with('success', $msg);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan detail RAB.
     */
    public function show($id)
    {
        $this->authorizeStaff();

        $rab = Rab::with(['laporan.lokasi.pasar', 'laporan.fasilitas', 'detailRab.sab', 'spj'])
            ->where('id_rab', $id)
            ->firstOrFail();

        return view('staff.rab.show', compact('rab'));
    }

    /**
     * Form edit RAB.
     */
    public function edit($id)
    {
        $this->authorizeStaff();

        $rab = Rab::with(['laporan', 'detailRab'])->where('id_rab', $id)->firstOrFail();

        $isLocked = !is_null($rab->tanggal_persetujuan_awal);

        $laporanEligible = Laporan::with(['lokasi.pasar', 'fasilitas'])
            ->where(function ($q) use ($rab) {
                $q->whereNull('id_rab')->orWhere('id_rab', $rab->id_rab);
            })
            ->whereIn('status_laporan', ['Disetujui', 'Diproses'])
            ->whereIn('kategori_kerusakan', ['Ringan', 'Sedang'])
            ->orderBy('tanggal_lapor', 'desc')
            ->get();

        $pasarList = $laporanEligible
            ->map(function ($lap) {
                return $lap->lokasi->pasar ?? null;
            })
            ->filter()
            ->unique('id_pasar')
            ->values();

        $sabList = Sab::where('status_aktif', true)
            ->orderBy('nama_kebutuhan', 'asc')
            ->get();

        return view('staff.rab.edit', compact('rab', 'laporanEligible', 'sabList', 'pasarList', 'isLocked'));
    }

    /**
     * Update RAB.
     */
    public function update(Request $request, $id)
    {
        $this->authorizeStaff();

        $rab = Rab::where('id_rab', $id)->firstOrFail();

        $isLocked = !is_null($rab->tanggal_persetujuan_awal);

        $rules = [
            'rincian_kebutuhan' => ['required', 'array', 'min:1'],
            'rincian_kebutuhan.*' => ['required', 'string', 'max:150'],
            'volume' => ['required', 'array', 'min:1'],
            'volume.*' => ['required', 'numeric', 'min:0.001'],
            'satuan' => ['required', 'array', 'min:1'],
            'satuan.*' => ['required', 'string', 'max:30'],
            'harga_satuan' => ['required', 'array', 'min:1'],
            'harga_satuan.*' => ['required', 'numeric', 'min:1'],
            'id_sab' => ['nullable', 'array'],
            'id_sab.*' => ['nullable', 'exists:sab,id_sab'],
            'action' => ['nullable', 'string'],
        ];

        if (!$isLocked) {
            $rules['laporan_ids'] = ['required', 'array', 'min:1'];
            $rules['laporan_ids.*'] = ['required', 'exists:laporan,id_laporan'];
        }

        $validated = $request->validate($rules);

        if (!$isLocked) {
            $selectedLaporan = Laporan::with('lokasi.pasar')->whereIn('id_laporan', $validated['laporan_ids'])->get();
            $pasarIds = $selectedLaporan->map(function ($lap) {
                return $lap->lokasi->id_pasar ?? null;
            })->filter()->unique();

            if ($pasarIds->count() > 1) {
                return back()->withInput()->with('error', 'Seluruh laporan yang dipilih untuk 1 RAB harus berasal dari lokasi pasar yang sama.');
            }
        }

        DB::beginTransaction();

        try {
            // Update komposisi laporan jika belum dikunci
            if (!$isLocked) {
                // Lepaskan laporan lama yang tidak terpilih
                Laporan::where('id_rab', $rab->id_rab)
                    ->whereNotIn('id_laporan', $validated['laporan_ids'])
                    ->update(['id_rab' => null]);

                // Pasang id_rab pada laporan baru
                Laporan::whereIn('id_laporan', $validated['laporan_ids'])
                    ->update(['id_rab' => $rab->id_rab]);
            }

            // Hapus detail_rab lama dan re-insert
            $rab->detailRab()->delete();

            $details = [];
            $latestDetail = DetailRab::orderBy('id_detail_rab', 'desc')->first();
            $startNum = $latestDetail ? ((int) substr($latestDetail->id_detail_rab, 3)) + 1 : 1;

            foreach ($validated['rincian_kebutuhan'] as $index => $rincian) {
                $details[] = [
                    'id_detail_rab' => 'RAB' . str_pad($startNum + $index, 3, '0', STR_PAD_LEFT),
                    'id_rab' => $rab->id_rab,
                    'id_sab' => $validated['id_sab'][$index] ?? null,
                    'rincian_kebutuhan' => $rincian,
                    'volume' => $validated['volume'][$index],
                    'satuan' => $validated['satuan'][$index],
                    'harga_satuan' => $validated['harga_satuan'][$index],
                ];
            }

            DetailRab::insert($details);

            $isSubmit = ($request->input('action') === 'submit');

            // Jika disubmit/diedit, status verifikasi kembali ke Menunggu
            $rab->update([
                'status_verifikasi_rab' => $isSubmit ? 'Menunggu' : 'Draft',
                'catatan_revisi_rab' => null,
            ]);

            if ($isSubmit) {
                $firstLap = $rab->laporan()->first()?->id_laporan;
                \App\Services\NotificationService::sendToRole(
                    'Kepala Bidang',
                    'Pengajuan RAB Baru',
                    "Staff mengajukan/memperbarui RAB {$rab->id_rab} untuk diverifikasi Kabid.",
                    route('kabid.rab.show', $rab->id_rab),
                    $firstLap
                );
            }

            DB::commit();

            $msg = $isSubmit ? 'Perubahan RAB berhasil disimpan dan dikirim ke Kabid untuk diverifikasi.' : 'Perubahan draft RAB berhasil disimpan.';
            return redirect()->route('staff.rab.show', $rab->id_rab)->with('success', $msg);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Kirim RAB (Draft / Dikembalikan) langsung ke Kabid dari halaman Detail.
     */
    public function submitToKabid($id)
    {
        $this->authorizeStaff();

        $rab = Rab::with(['laporan', 'detailRab'])->where('id_rab', $id)->firstOrFail();

        if (!in_array($rab->status_verifikasi_rab, ['Draft', 'Dikembalikan'])) {
            return back()->with('error', 'Hanya RAB berstatus Draft atau Dikembalikan yang dapat dikirim ke Kabid.');
        }

        if ($rab->detailRab->isEmpty()) {
            return back()->with('error', 'RAB belum memiliki rincian kebutuhan material.');
        }

        $rab->update([
            'status_verifikasi_rab' => 'Menunggu',
            'catatan_revisi_rab' => null,
        ]);

        $firstLap = $rab->laporan()->first()?->id_laporan;
        \App\Services\NotificationService::sendToRole(
            'Kepala Bidang',
            'Pengajuan RAB Baru',
            "Staff mengajukan RAB {$rab->id_rab} untuk diverifikasi Kabid.",
            route('kabid.rab.show', $rab->id_rab),
            $firstLap
        );

        return redirect()->route('staff.rab.show', $rab->id_rab)
            ->with('success', 'RAB ' . $rab->id_rab . ' berhasil dikirim ke Kepala Bidang untuk diverifikasi.');
    }

    /**
     * Preview / Unduh PDF RAB.
     */
    public function exportPdf($id)
    {
        $this->authorizeStaff();

        $rab = Rab::with(['laporan.lokasi.pasar', 'laporan.fasilitas', 'detailRab'])
            ->where('id_rab', $id)
            ->firstOrFail();

        if ($rab->detailRab->isEmpty()) {
            return back()->with('error', 'RAB belum memiliki rincian kebutuhan material.');
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

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.rab', compact('rab', 'logoBase64'));
        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream("RAB_{$rab->id_rab}.pdf");
    }
}
