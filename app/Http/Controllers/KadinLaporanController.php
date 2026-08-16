<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use App\Models\Pasar;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class KadinLaporanController extends Controller
{
    /**
     * Tampilkan daftar laporan dari semua pasar (Read-Only untuk Kepala Dinas).
     */
    public function index(Request $request)
    {
        $query = $this->applyFilters(Laporan::query(), $request);

        $laporanList = $query
            ->with(['lokasi.pasar', 'fasilitas', 'pelapor'])
            ->orderBy('tanggal_lapor', 'desc')
            ->orderBy('id_laporan', 'desc')
            ->paginate(10)
            ->withQueryString();

        $pasarList = Pasar::orderBy('nama_pasar')->get();
        $statusList = ['Menunggu', 'Diproses', 'Disetujui', 'Selesai', 'Dikembalikan', 'Ditolak'];

        return view('kadin.laporan.index', compact(
            'laporanList',
            'pasarList',
            'statusList'
        ));
    }

    /**
     * Endpoint AJAX untuk menghitung jumlah data yang akan dicetak pada modal.
     */
    public function countCetak(Request $request)
    {
        $query = $this->buildQueryByTipe($request);
        $count = $query->count();

        return response()->json([
            'status' => 'success',
            'count'  => $count,
        ]);
    }

    /**
     * Cetak PDF laporan berdasarkan kriteria pilihan modal.
     */
    public function printPdf(Request $request)
    {
        $query = $this->buildQueryByTipe($request);

        $laporanList = $query
            ->with(['lokasi.pasar', 'fasilitas', 'pelapor'])
            ->orderBy('tanggal_lapor', 'desc')
            ->orderBy('id_laporan', 'desc')
            ->get();

        // Calculate summary for footer/statistics
        $summary = [
            'total' => $laporanList->count(),
            'menunggu' => $laporanList->where('status_laporan', 'Menunggu')->count(),
            'diproses' => $laporanList->where('status_laporan', 'Diproses')->count(),
            'disetujui' => $laporanList->where('status_laporan', 'Disetujui')->count(),
            'selesai' => $laporanList->where('status_laporan', 'Selesai')->count(),
            'dikembalikan' => $laporanList->whereIn('status_laporan', ['Dikembalikan', 'Ditolak'])->count(),
        ];

        $tipeCetak = $request->input('tipe_cetak', 'filter');
        $filterDescription = $this->getFilterDescription($tipeCetak, $request);

        // Logo image base64
        $logoBase64 = '';
        if (extension_loaded('gd')) {
            $logoPath = public_path('images/Logo Dinas Perdagangan Kota Padang.png');
            if (file_exists($logoPath)) {
                $type = pathinfo($logoPath, PATHINFO_EXTENSION);
                $data = file_get_contents($logoPath);
                $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            }
        }

        $printDate = Carbon::now('Asia/Jakarta')->locale('id')->isoFormat('D MMMM YYYY');

        $pdf = Pdf::loadView('pdf.laporan_kadin', compact(
            'laporanList',
            'summary',
            'filterDescription',
            'logoBase64',
            'printDate'
        ));

        $pdf->setPaper('a4', 'landscape');

        return $pdf->stream("Laporan_Kerusakan_Sarpras_" . date('Ymd_His') . ".pdf");
    }

    /**
     * Helper untuk membangun Query berdasarkan tipe cetak.
     */
    protected function buildQueryByTipe(Request $request)
    {
        $tipeCetak = $request->input('tipe_cetak', 'filter');
        $query = Laporan::query();

        if ($tipeCetak === 'semua') {
            return $query;
        }

        if ($tipeCetak === 'periode') {
            $tahun = $request->input('tahun', date('Y'));
            $bulan = $request->input('bulan');

            $query->whereYear('tanggal_lapor', $tahun);

            if (!empty($bulan) && is_numeric($bulan) && (int)$bulan >= 1 && (int)$bulan <= 12) {
                $query->whereMonth('tanggal_lapor', (int)$bulan);
            }

            return $query;
        }

        // Default: 'filter'
        return $this->applyFilters($query, $request);
    }

    /**
     * Helper deskripsi filter untuk header PDF.
     */
    protected function getFilterDescription($tipeCetak, Request $request)
    {
        if ($tipeCetak === 'semua') {
            return "Kriteria: Semua Laporan Kerusakan";
        }

        if ($tipeCetak === 'periode') {
            $tahun = $request->input('tahun', date('Y'));
            $bulan = $request->input('bulan');
            $namaBulan = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
            ];

            if (!empty($bulan) && isset($namaBulan[(int)$bulan])) {
                return "Periode: Bulan " . $namaBulan[(int)$bulan] . " " . $tahun;
            } else {
                return "Periode: Laporan Tahunan " . $tahun . " (Semua Bulan)";
            }
        }

        // Tipe Filter Saat Ini
        $filterTexts = [];

        if ($request->filled('search')) {
            $filterTexts[] = 'Pencarian: "' . $request->search . '"';
        }

        if ($request->filled('pasar')) {
            $pasar = Pasar::find($request->pasar);
            $filterTexts[] = "Pasar: " . ($pasar->nama_pasar ?? 'Terpilih');
        } else {
            $filterTexts[] = "Pasar: Semua Pasar";
        }

        if ($request->filled('status')) {
            $filterTexts[] = "Status: " . $request->status;
        } else {
            $filterTexts[] = "Status: Semua Status";
        }

        return count($filterTexts) > 0 ? implode(' | ', $filterTexts) : "Kriteria: Filter Aktif";
    }

    /**
     * Helper untuk menerapkan filter pencarian dan parameter URL.
     */
    protected function applyFilters($query, Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $pasarId = $request->input('pasar');
        $status = $request->input('status');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('id_laporan', 'like', "%{$search}%")
                  ->orWhere('item_kerusakan', 'like', "%{$search}%")
                  ->orWhereHas('lokasi.pasar', function ($subQuery) use ($search) {
                      $subQuery->where('nama_pasar', 'like', "%{$search}%");
                  })
                  ->orWhereHas('lokasi', function ($subQuery) use ($search) {
                      $subQuery->where('nama_lokasi', 'like', "%{$search}%");
                  })
                  ->orWhereHas('fasilitas', function ($subQuery) use ($search) {
                      $subQuery->where('nama_fasilitas', 'like', "%{$search}%");
                  })
                  ->orWhereHas('pelapor', function ($subQuery) use ($search) {
                      $subQuery->where('nama_lengkap', 'like', "%{$search}%");
                  });
            });
        }

        if (!empty($pasarId)) {
            $query->whereHas('lokasi.pasar', function ($subQuery) use ($pasarId) {
                $subQuery->where('id_pasar', $pasarId);
            });
        }

        if (!empty($status)) {
            $query->where('status_laporan', $status);
        }

        return $query;
    }
}
