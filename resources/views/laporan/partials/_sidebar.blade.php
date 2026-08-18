<div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 space-y-4">
    <h3 class="text-base font-bold text-gray-800 border-b border-gray-100 pb-3">Ringkasan Laporan</h3>

    <div class="space-y-3 text-sm">
        <div>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">ID Laporan</p>
            <p class="font-bold text-[#114F72]">{{ $laporan->id_laporan }}</p>
        </div>

        <div>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Tanggal Lapor</p>
            <p class="font-medium text-gray-700">{{ \Carbon\Carbon::parse($laporan->tanggal_lapor)->translatedFormat('d F Y') }}</p>
        </div>

        <div>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Pasar</p>
            <p class="font-medium text-gray-700">{{ $laporan->lokasi->pasar->nama_pasar ?? '-' }}</p>
        </div>

        <div>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Lokasi</p>
            <p class="font-medium text-gray-700">{{ $laporan->lokasi->nama_lokasi ?? '-' }}</p>
            @if($laporan->lokasi_spesifik)
                <p class="text-xs text-gray-500 mt-0.5">{{ $laporan->lokasi_spesifik }}</p>
            @endif
        </div>

        <div>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Fasilitas</p>
            <p class="font-medium text-gray-700">{{ $laporan->nama_fasilitas_display }}</p>
        </div>

        <div>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Kategori Laporan</p>
            <p class="font-medium text-gray-700">{{ $laporan->kategori_laporan_display }}</p>
        </div>

        <div>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Item Kerusakan</p>
            <p class="font-medium text-gray-700">{{ $laporan->item_kerusakan ?? '-' }}</p>
        </div>

        <div>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Pelapor</p>
            <p class="font-medium text-gray-700">{{ $laporan->pelapor->nama_lengkap ?? '-' }}</p>
        </div>

        @if($laporan->evaluator)
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Evaluator Staff</p>
                <p class="font-medium text-gray-700">Dievaluasi oleh: {{ $laporan->evaluator->nama_lengkap }}</p>
            </div>
        @endif

        <div>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Kategori Kerusakan</p>
            <p class="font-medium text-gray-700">{{ $laporan->kategori_kerusakan ?? 'Belum dievaluasi' }}</p>
        </div>

        <div>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Status Laporan</p>
            @php
                $statusBadge = match($laporan->status_laporan) {
                    'Menunggu' => 'bg-amber-100 text-amber-700 border-amber-200',
                    'Diproses' => 'bg-blue-100 text-blue-700 border-blue-200',
                    'Selesai' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                    'Dikembalikan' => 'bg-red-100 text-red-700 border-red-200',
                    default => 'bg-gray-100 text-gray-600 border-gray-200',
                };
            @endphp
            <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold mt-1 {{ $statusBadge }}">
                {{ $laporan->status_laporan }}
            </span>
        </div>

        <div>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Status RAB</p>
            @php
                $rabStatus = $laporan->status_verifikasi_rab ?? ($laporan->detailRab && $laporan->detailRab->isNotEmpty() ? 'Belum Diteruskan' : 'Belum Dibuat');
                $rabBadge = match($laporan->status_verifikasi_rab) {
                    'Menunggu' => 'bg-amber-100 text-amber-700 border-amber-200',
                    'Disetujui' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                    'Dikembalikan' => 'bg-red-100 text-red-700 border-red-200',
                    default => 'bg-gray-100 text-gray-600 border-gray-200',
                };
            @endphp
            <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold mt-1 {{ $rabBadge }}">
                {{ $rabStatus }}
            </span>
        </div>
    </div>
</div>
