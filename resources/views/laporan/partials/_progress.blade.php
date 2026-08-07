@php
    $progressItems = $laporan->progresPerbaikan ? $laporan->progresPerbaikan->sortBy('persentase_penyelesaian') : collect();
    $byStage = $progressItems->keyBy('persentase_penyelesaian');

    $has0 = $byStage->has('0');
    $has50 = $byStage->has('50');
    $has100 = $byStage->has('100');

    $activeStageKey = match(true) {
        $has100 => '100',
        $has50 => '50',
        $has0 => '0',
        default => null
    };

    $statusLabel = match(true) {
        $has100 => 'Selesai',
        ($has50 || $has0) => 'Sedang Berjalan',
        default => 'Belum berjalan'
    };

    $statusBadge = match(true) {
        $has100 => 'bg-emerald-100 text-emerald-700 border-emerald-200',
        ($has50 || $has0) => 'bg-blue-100 text-blue-700 border-blue-200',
        default => 'bg-gray-100 text-gray-600 border-gray-200'
    };

    $milestonesDef = [
        '0' => [
            'percent' => '0%',
            'title' => 'Kondisi Awal',
        ],
        '50' => [
            'percent' => '50%',
            'title' => 'Proses (50%)',
        ],
        '100' => [
            'percent' => '100%',
            'title' => 'Selesai (100%)',
        ],
    ];

    // History sorted from oldest to newest
    $historyItems = $progressItems->sortBy(function($item) {
        return $item->tanggal_update ?? $item->created_at ?? $item->persentase_penyelesaian;
    });
@endphp

<div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 sm:p-8 space-y-8">
    <!-- Header -->
    <div class="flex flex-wrap items-center justify-between gap-4 border-b border-gray-100 pb-4">
        <div>
            <h3 class="text-base sm:text-lg font-bold text-gray-800">Progress Perbaikan</h3>
            <p class="text-xs text-gray-500 mt-0.5">Tahapan fisik perbaikan fasilitas publik.</p>
        </div>

        <span class="inline-flex items-center rounded-full border px-3.5 py-1 text-xs font-semibold {{ $statusBadge }}">
            Status: {{ $statusLabel }}
        </span>
    </div>

    <!-- 1. Progress Timeline (Horizontal Indicator Bar) -->
    <div class="bg-gray-50/70 border border-gray-100 rounded-2xl p-6">
        <div class="relative py-2 px-2 sm:px-8">
            <!-- Background & Active Track Line -->
            <div class="absolute top-5 left-10 right-10 h-1 bg-gray-200 rounded-full z-0">
                @php
                    $lineWidth = match(true) {
                        $has100 => '100%',
                        $has50 => '50%',
                        $has0 => '0%',
                        default => '0%'
                    };
                @endphp
                <div class="h-full bg-gradient-to-r from-[#114F72] to-[#16A394] rounded-full transition-all duration-500"
                     style="width: {{ $lineWidth }};">
                </div>
            </div>

            <!-- Milestone Step Nodes -->
            <div class="relative z-10 flex justify-between items-start">
                <!-- Step 0% -->
                <div class="flex flex-col items-center">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-xs shadow-sm transition-all duration-200 border-2 {{ $has0 ? ($activeStageKey === '0' ? 'bg-[#114F72] text-white border-[#114F72] ring-4 ring-blue-100' : 'bg-emerald-500 text-white border-emerald-500') : 'bg-white text-gray-400 border-gray-300' }}">
                        @if($has50 || $has100)
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        @else
                            0%
                        @endif
                    </div>
                    <span class="text-xs font-bold mt-2.5 {{ $has0 ? 'text-gray-800' : 'text-gray-400' }}">0%</span>
                    <span class="text-[11px] text-gray-500 mt-0.5">Kondisi Awal</span>
                </div>

                <!-- Step 50% -->
                <div class="flex flex-col items-center">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-xs shadow-sm transition-all duration-200 border-2 {{ $has50 ? ($activeStageKey === '50' ? 'bg-[#114F72] text-white border-[#114F72] ring-4 ring-blue-100' : 'bg-emerald-500 text-white border-emerald-500') : 'bg-white text-gray-400 border-gray-300' }}">
                        @if($has100)
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        @else
                            50%
                        @endif
                    </div>
                    <span class="text-xs font-bold mt-2.5 {{ $has50 ? 'text-gray-800' : 'text-gray-400' }}">50%</span>
                    <span class="text-[11px] text-gray-500 mt-0.5">Proses (50%)</span>
                </div>

                <!-- Step 100% -->
                <div class="flex flex-col items-center">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-xs shadow-sm transition-all duration-200 border-2 {{ $has100 ? 'bg-emerald-500 text-white border-emerald-500 ring-4 ring-emerald-100' : 'bg-white text-gray-400 border-gray-300' }}">
                        @if($has100)
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        @else
                            100%
                        @endif
                    </div>
                    <span class="text-xs font-bold mt-2.5 {{ $has100 ? 'text-gray-800' : 'text-gray-400' }}">100%</span>
                    <span class="text-[11px] text-gray-500 mt-0.5">Selesai (100%)</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Progress Milestone Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        @foreach($milestonesDef as $key => $mDef)
            @php
                $item = $byStage->get($key);
                $isUploaded = !is_null($item);
                $firstFoto = $isUploaded && $item->fotoProgres && $item->fotoProgres->count() > 0 ? $item->fotoProgres->first() : null;

                $isCompleted = false;
                $isActive = false;

                if ($isUploaded) {
                    if ($key === $activeStageKey && $key !== '100') {
                        $isActive = true;
                    } else {
                        $isCompleted = true;
                    }
                }

                $cardBorderClass = match(true) {
                    $isActive => 'border-2 border-[#114F72] shadow-md bg-blue-50/20 ring-2 ring-[#114F72]/10',
                    $isCompleted => 'border-2 border-emerald-500 shadow-sm bg-white',
                    default => 'border border-gray-200 bg-gray-50/70 opacity-60'
                };
            @endphp

            <div class="rounded-2xl {{ $cardBorderClass }} p-4 flex flex-col justify-between space-y-4 transition duration-200">
                <!-- Header: Badge & Status Icon -->
                <div class="flex items-center justify-between">
                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold {{ $isCompleted ? 'bg-emerald-100 text-emerald-800' : ($isActive ? 'bg-[#114F72] text-white' : 'bg-gray-200 text-gray-500') }}">
                        🔧 {{ $mDef['percent'] }}
                    </span>

                    @if($isCompleted)
                        <span class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-600 bg-emerald-50 px-2.5 py-0.5 rounded-full border border-emerald-200">
                            <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                            </svg>
                            Selesai
                        </span>
                    @elseif($isActive)
                        <span class="inline-flex items-center gap-1 text-xs font-semibold text-[#114F72] bg-blue-50 px-2.5 py-0.5 rounded-full border border-blue-200">
                            Sedang Berjalan
                        </span>
                    @else
                        <span class="text-xs text-gray-400 font-medium">
                            Belum tersedia
                        </span>
                    @endif
                </div>

                <!-- Photo or Disabled Placeholder Card -->
                @if($isUploaded && $firstFoto)
                    <button type="button"
                            onclick="openUptdProgresFotoModal('{{ asset('storage/' . $firstFoto->file_foto) }}')"
                            class="group relative aspect-[16/10] w-full overflow-hidden rounded-xl bg-gray-100 border border-gray-200 focus:outline-none">
                        <img src="{{ asset('storage/' . $firstFoto->file_foto) }}" alt="{{ $mDef['title'] }}" class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
                        <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white text-xs font-semibold">
                            Lihat Foto
                        </div>
                    </button>
                @else
                    <div class="aspect-[16/10] w-full rounded-xl bg-gray-100/80 border border-dashed border-gray-300 flex flex-col items-center justify-center text-gray-400 text-xs p-3">
                        <svg class="w-7 h-7 mb-1 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span class="font-medium">Belum tersedia</span>
                    </div>
                @endif

                <!-- Title -->
                <div class="pt-1">
                    <h4 class="text-sm font-bold text-gray-800">{{ $mDef['title'] }}</h4>
                </div>
            </div>
        @endforeach
    </div>

    <!-- 3. Progress History -->
    <div class="border-t border-gray-100 pt-6 space-y-4">
        <div class="flex items-center justify-between">
            <h4 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                <svg class="w-4 h-4 text-[#114F72]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Riwayat Progres
            </h4>
        </div>

        @if($historyItems->isEmpty())
            <div class="rounded-xl bg-gray-50 border border-dashed border-gray-200 p-6 text-center text-xs text-gray-400">
                Belum ada data riwayat progres perbaikan yang dicatat.
            </div>
        @else
            <div class="space-y-3">
                @foreach($historyItems as $history)
                    <div class="p-4 rounded-xl border border-gray-200 bg-gray-50/50 hover:bg-white transition flex flex-col sm:flex-row sm:items-start justify-between gap-3">
                        <div class="space-y-1.5 flex-1">
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-bold bg-gray-200 text-gray-800">
                                    🔧 {{ $history->persentase_penyelesaian }}%
                                </span>
                            </div>
                            <p class="text-xs sm:text-sm text-gray-700 leading-relaxed font-normal">
                                {{ $history->keterangan_perkembangan }}
                            </p>
                        </div>

                        @if($history->tanggal_update)
                            <div class="text-left sm:text-right flex-shrink-0">
                                <span class="text-xs text-gray-500 font-medium">
                                    {{ \Carbon\Carbon::parse($history->tanggal_update)->translatedFormat('d F Y') }}
                                </span>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<!-- Modal Lightbox Photo Preview for UPTD -->
<div id="uptdProgresFotoModal"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/80 px-4"
     onclick="if(event.target === this) closeUptdProgresFotoModal()">
    <button type="button" onclick="closeUptdProgresFotoModal()" class="absolute top-4 right-4 text-white/80 hover:text-white transition">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </button>
    <img id="uptdProgresFotoModalImg" src="" alt="Pratinjau Foto Progres" class="max-h-[85vh] max-w-full rounded-lg shadow-2xl">
</div>

<script>
    function openUptdProgresFotoModal(src) {
        document.getElementById('uptdProgresFotoModalImg').src = src;
        const modal = document.getElementById('uptdProgresFotoModal');
        if (!modal) return;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeUptdProgresFotoModal() {
        const modal = document.getElementById('uptdProgresFotoModal');
        if (!modal) return;
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    }
</script>
