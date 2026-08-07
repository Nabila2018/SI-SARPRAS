@php
    $stage1Done = true;
    $stage2Done = !is_null($laporan->kategori_kerusakan);
    $stage3Done = !is_null($laporan->status_verifikasi_rab) && $laporan->status_verifikasi_rab !== 'Dikembalikan';
    $stage4Done = $laporan->progresPerbaikan && $laporan->progresPerbaikan->count() > 0;
    $stage5Done = $laporan->status_laporan === 'Selesai';

    if ($stage5Done) {
        $currentStage = 5;
    } elseif ($stage4Done) {
        $currentStage = 4;
    } elseif ($stage3Done) {
        $currentStage = 3;
    } elseif ($stage2Done) {
        $currentStage = 2;
    } else {
        $currentStage = 1;
    }

    $stages = [
        1 => ['label' => 'Laporan', 'sub' => 'Dibuat', 'done' => $stage1Done],
        2 => ['label' => 'Evaluasi', 'sub' => 'Pemeriksaan', 'done' => $stage2Done],
        3 => ['label' => 'RAB', 'sub' => 'Anggaran', 'done' => $stage3Done],
        4 => ['label' => 'Progress', 'sub' => 'Perbaikan', 'done' => $stage4Done],
        5 => ['label' => 'Selesai', 'sub' => 'Tuntas', 'done' => $stage5Done],
    ];
@endphp

<div class="mb-6 bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
    <div class="flex items-center justify-between mb-4 border-b border-gray-100 pb-3">
        <h3 class="text-xs uppercase tracking-wider font-bold text-gray-500 flex items-center gap-2">
            <svg class="w-4 h-4 text-[#114F72]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            Alur Tahapan Laporan (Workflow Timeline)
        </h3>
        <span class="text-xs font-semibold text-gray-500 bg-gray-100 px-2.5 py-0.5 rounded-full">
            Tahap {{ $currentStage }} dari 5
        </span>
    </div>

    <!-- Responsive Timeline Grid -->
    <div class="grid grid-cols-5 gap-2 relative">
        @foreach($stages as $num => $stg)
            @php
                $isDone = $stg['done'];
                $isCurrent = ($num === $currentStage && !$stage5Done);
                $isFuture = (!$isDone && !$isCurrent);

                $badgeClass = match(true) {
                    $isDone => 'bg-emerald-500 text-white ring-4 ring-emerald-100',
                    $isCurrent => 'bg-[#114F72] text-white ring-4 ring-blue-100 animate-pulse',
                    default => 'bg-gray-100 text-gray-400 border border-gray-300'
                };

                $textClass = match(true) {
                    $isDone => 'text-gray-800 font-bold',
                    $isCurrent => 'text-[#114F72] font-bold',
                    default => 'text-gray-400 font-medium'
                };
            @endphp

            <div class="flex flex-col items-center text-center relative z-10">
                <div class="w-9 h-9 rounded-full flex items-center justify-center text-xs transition duration-200 {{ $badgeClass }}">
                    @if($isDone)
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                    @else
                        <span>{{ $num }}</span>
                    @endif
                </div>

                <div class="mt-2">
                    <p class="text-xs sm:text-sm {{ $textClass }}">{{ $stg['label'] }}</p>
                    <p class="text-[10px] text-gray-400 hidden sm:block">{{ $stg['sub'] }}</p>
                </div>
            </div>
        @endforeach
    </div>
</div>
