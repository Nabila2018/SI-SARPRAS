@if ($paginator->hasPages())
    <nav class="flex flex-wrap items-center justify-end gap-2" aria-label="Pagination">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-medium text-gray-400 bg-gray-100 border border-gray-200 rounded-lg cursor-not-allowed select-none">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Sebelumnya
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-medium text-[#114F72] bg-white border border-gray-200 rounded-lg hover:bg-[#114F72]/5 hover:border-[#114F72]/20 transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Sebelumnya
            </a>
        @endif

        @php
            $currentPage = $paginator->currentPage();
            $lastPage = $paginator->lastPage();

            // Batasi kotak nomor halaman maksimal 3 saja
            if ($lastPage <= 3) {
                $start = 1;
                $end = $lastPage;
            } elseif ($currentPage <= 2) {
                $start = 1;
                $end = 3;
            } elseif ($currentPage >= $lastPage - 1) {
                $start = $lastPage - 2;
                $end = $lastPage;
            } else {
                $start = $currentPage - 1;
                $end = $currentPage + 1;
            }
        @endphp

        {{-- Left Ellipsis --}}
        @if ($start > 1)
            <span class="px-1 text-xs text-gray-400 font-bold select-none">...</span>
        @endif

        {{-- Page Number Boxes (Maksimal 3 Kotak) --}}
        @for ($page = $start; $page <= $end; $page++)
            @if ($page == $currentPage)
                <span class="inline-flex items-center justify-center min-w-9 h-9 px-3 py-2 text-xs font-bold text-white bg-gradient-to-r from-[#114F72] to-[#16A394] border border-transparent rounded-lg shadow-sm">
                    {{ $page }}
                </span>
            @else
                <a href="{{ $paginator->url($page) }}" class="inline-flex items-center justify-center min-w-9 h-9 px-3 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors shadow-sm">
                    {{ $page }}
                </a>
            @endif
        @endfor

        {{-- Right Ellipsis --}}
        @if ($end < $lastPage)
            <span class="px-1 text-xs text-gray-400 font-bold select-none">...</span>
        @endif

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-medium text-[#114F72] bg-white border border-gray-200 rounded-lg hover:bg-[#114F72]/5 hover:border-[#114F72]/20 transition-colors shadow-sm">
                Berikutnya
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        @else
            <span class="inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-medium text-gray-400 bg-gray-100 border border-gray-200 rounded-lg cursor-not-allowed select-none">
                Berikutnya
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </span>
        @endif
    </nav>
@endif
