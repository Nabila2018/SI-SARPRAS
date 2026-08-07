@php
    $hasEvaluation = !is_null($laporan->kategori_kerusakan);
    $canEvaluate = $laporan->status_laporan === 'Menunggu';
    $canForward = $hasEvaluation && $laporan->status_laporan === 'Menunggu';
@endphp

<!-- Hasil Evaluasi Staff -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 space-y-6">
    <div class="flex items-center justify-between border-b border-gray-100 pb-4">
        <div>
            <h3 class="text-base font-bold text-gray-800">Hasil Evaluasi Staff</h3>
            <p class="text-xs text-gray-500 mt-0.5">Hasil pemeriksaan & analisis kerusakan fasilitas oleh Staff Sarpras.</p>
        </div>
        @if($hasEvaluation)
            @php
                $evalBadge = match($laporan->kategori_kerusakan) {
                    'Ringan' => 'bg-amber-100 text-amber-700 border-amber-200',
                    'Sedang' => 'bg-orange-100 text-orange-700 border-orange-200',
                    'Berat' => 'bg-red-100 text-red-700 border-red-200',
                    default => 'bg-gray-100 text-gray-600 border-gray-200',
                };
            @endphp
            <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold {{ $evalBadge }}">
                Kategori: {{ $laporan->kategori_kerusakan }}
            </span>
        @else
            <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold bg-gray-100 text-gray-600 border-gray-200">
                Belum Dievaluasi
            </span>
        @endif
    </div>

    @if($hasEvaluation)
        <div class="space-y-4 text-sm">
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Catatan Pemeriksaan</p>
                <p class="text-gray-700 leading-relaxed bg-gray-50 p-4 rounded-xl border border-gray-100 whitespace-pre-line">
                    {{ $laporan->catatan_pemeriksaan ?: 'Tidak ada catatan pemeriksaan.' }}
                </p>
            </div>
            @if($laporan->tanggal_evaluasi)
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Tanggal Evaluasi</p>
                    <p class="text-gray-600">{{ \Carbon\Carbon::parse($laporan->tanggal_evaluasi)->translatedFormat('d F Y H:i') }} WIB</p>
                </div>
            @endif
        </div>
    @else
        <div class="rounded-xl bg-amber-50/60 border border-amber-200/60 p-6 text-center text-sm text-amber-800">
            <p class="font-semibold">Laporan ini belum memiliki hasil evaluasi pemeriksaan.</p>
            <p class="text-xs text-amber-600 mt-1">Silakan klik tombol "Isi Evaluasi" di bawah untuk memasukkan hasil analisa kerusakan.</p>
        </div>
    @endif

    <!-- Tombol Aksi Evaluasi -->
    <div class="flex flex-wrap items-center justify-end gap-3 pt-4 border-t border-gray-100">
        <button type="button"
                onclick="openEvaluasiModal()"
                class="px-6 py-2.5 rounded-xl font-semibold shadow-sm transition text-sm {{ $canEvaluate ? 'bg-gradient-to-r from-[#114F72] to-[#16A394] text-white hover:opacity-90' : 'bg-gray-200 text-gray-500 cursor-not-allowed opacity-70' }}"
                {{ $canEvaluate ? '' : 'disabled' }}>
            {{ $hasEvaluation ? 'Edit Evaluasi' : 'Isi Evaluasi' }}
        </button>

        <button type="button"
                onclick="openForwardModal()"
                class="px-6 py-2.5 rounded-xl font-semibold shadow-sm transition text-sm {{ $canForward ? 'bg-gradient-to-r from-emerald-600 to-emerald-500 text-white hover:opacity-90' : 'bg-gray-200 text-gray-500 cursor-not-allowed opacity-70' }}"
                {{ $canForward ? '' : 'disabled' }}>
            Teruskan ke Kabid
        </button>
    </div>
</div>

<!-- Modal Input / Edit Evaluasi -->
<div id="evaluasiModal"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70 px-4"
     onclick="if(event.target === this) closeEvaluasiModal()">
    <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between mb-4 border-b pb-3">
            <h3 class="text-lg font-bold text-gray-800">{{ $hasEvaluation ? 'Edit Evaluasi Laporan' : 'Isi Evaluasi Laporan' }}</h3>
            <button type="button" onclick="closeEvaluasiModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form action="{{ route('staff.laporan.evaluasi.store', $laporan->id_laporan) }}?tab=evaluasi" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs uppercase tracking-wider font-semibold text-gray-600 mb-1">Kategori Kerusakan <span class="text-red-500">*</span></label>
                <select name="kategori_kerusakan" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#114F72] focus:ring-[#114F72] text-sm">
                    <option value="">-- Pilih Kategori Kerusakan --</option>
                    <option value="Ringan" {{ old('kategori_kerusakan', $laporan->kategori_kerusakan) === 'Ringan' ? 'selected' : '' }}>Ringan</option>
                    <option value="Sedang" {{ old('kategori_kerusakan', $laporan->kategori_kerusakan) === 'Sedang' ? 'selected' : '' }}>Sedang</option>
                    <option value="Berat" {{ old('kategori_kerusakan', $laporan->kategori_kerusakan) === 'Berat' ? 'selected' : '' }}>Berat</option>
                </select>
            </div>

            <div>
                <label class="block text-xs uppercase tracking-wider font-semibold text-gray-600 mb-1">Catatan Pemeriksaan <span class="text-red-500">*</span></label>
                <textarea name="catatan_pemeriksaan" rows="4" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#114F72] focus:ring-[#114F72] text-sm" placeholder="Tuliskan detail hasil pemeriksaan teknis...">{{ old('catatan_pemeriksaan', $laporan->catatan_pemeriksaan) }}</textarea>
            </div>

            <div class="mt-6 flex justify-end gap-3 pt-3 border-t">
                <button type="button" onclick="closeEvaluasiModal()" class="rounded-xl border border-gray-300 px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 transition">Batal</button>
                <button type="submit" class="rounded-xl bg-gradient-to-r from-[#114F72] to-[#16A394] px-5 py-2 text-sm font-semibold text-white shadow-sm hover:opacity-90 transition">Simpan Evaluasi</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Konfirmasi Teruskan ke Kabid -->
<div id="forwardModal"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70 px-4"
     onclick="if(event.target === this) closeForwardModal()">
    <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl" onclick="event.stopPropagation()">
        <h3 class="text-lg font-bold text-gray-800">Teruskan Laporan ke Kabid</h3>
        <p class="mt-2 text-sm text-gray-600">Apakah Anda yakin ingin meneruskan laporan ini beserta hasil evaluasi ke Kepala Bidang untuk disetujui?</p>

        <form action="{{ route('staff.laporan.forward', $laporan->id_laporan) }}?tab=evaluasi" method="POST" class="mt-6 flex justify-end gap-3">
            @csrf
            <button type="button" onclick="closeForwardModal()" class="rounded-xl border border-gray-300 px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 transition">Batal</button>
            <button type="submit" class="rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-500 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:opacity-90 transition">Ya, Teruskan</button>
        </form>
    </div>
</div>

<script>
    function openEvaluasiModal() {
        const modal = document.getElementById('evaluasiModal');
        if (!modal) return;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeEvaluasiModal() {
        const modal = document.getElementById('evaluasiModal');
        if (!modal) return;
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    }

    function openForwardModal() {
        const modal = document.getElementById('forwardModal');
        if (!modal) return;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeForwardModal() {
        const modal = document.getElementById('forwardModal');
        if (!modal) return;
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    }
</script>
