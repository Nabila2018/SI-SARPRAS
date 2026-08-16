<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $now = Carbon::now('Asia/Jakarta');
        $currentYear = (int) $now->format('Y');
        $currentMonth = (int) $now->format('m');

        $selectedYear = (int) $request->input('tahun', $currentYear);
        if ($selectedYear < 2000 || $selectedYear > 2100) {
            $selectedYear = $currentYear;
        }

        $bulanInput = $request->input('bulan');
        if ($bulanInput === 'semua' || $bulanInput === 'all') {
            $selectedMonth = null;
        } elseif (!is_null($bulanInput) && is_numeric($bulanInput)) {
            $monthInt = (int) $bulanInput;
            $selectedMonth = ($monthInt >= 1 && $monthInt <= 12) ? $monthInt : null;
        } else {
            // Default to current month if no parameter passed
            $selectedMonth = $currentMonth;
        }

        $monthNamesIndo = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        if (is_null($selectedMonth)) {
            $startDate = Carbon::create($selectedYear, 1, 1)->startOfDay();
            $endDate = Carbon::create($selectedYear, 12, 31)->endOfDay();
            $periodTitle = "Ringkasan Laporan Tahun {$selectedYear}";
            $chartSubtitle = "Periode Tahun {$selectedYear}";
        } else {
            $startDate = Carbon::create($selectedYear, $selectedMonth, 1)->startOfDay();
            $endDate = Carbon::create($selectedYear, $selectedMonth, 1)->endOfMonth()->endOfDay();
            $namaBulan = $monthNamesIndo[$selectedMonth] ?? '';
            $periodTitle = "Ringkasan Laporan {$namaBulan} {$selectedYear}";
            $chartSubtitle = "Periode {$namaBulan} {$selectedYear}";
        }

        // Years range from earliest report or currentYear - 2 to currentYear + 1
        $minDateStr = Laporan::min('tanggal_lapor');
        $minYear = $minDateStr ? (int) Carbon::parse($minDateStr)->format('Y') : ($currentYear - 2);
        $minYear = min($minYear, $currentYear - 1);
        $maxYear = max($currentYear, $selectedYear);

        $availableYears = range($minYear, $maxYear);
        rsort($availableYears);

        return view('home', compact(
            'selectedMonth',
            'selectedYear',
            'startDate',
            'endDate',
            'periodTitle',
            'chartSubtitle',
            'monthNamesIndo',
            'availableYears'
        ));
    }
}
