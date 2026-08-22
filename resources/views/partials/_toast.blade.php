@php
    $toasts = [];
    if (session('success')) {
        $toasts[] = ['type' => 'success', 'message' => session('success')];
    }
    if (session('error')) {
        $toasts[] = ['type' => 'error', 'message' => session('error')];
    }
    if (session('warning')) {
        $toasts[] = ['type' => 'warning', 'message' => session('warning')];
    }
    if (session('info')) {
        $toasts[] = ['type' => 'info', 'message' => session('info')];
    }
    if (session('status')) {
        $toasts[] = ['type' => 'info', 'message' => session('status')];
    }
@endphp

<!-- CONTAINER TOAST NOTIFICATION GLOBAL (Top-Center: Atas Tengah Halaman) -->
<div id="toastContainer" class="fixed top-6 left-1/2 -translate-x-1/2 z-[9999] flex flex-col gap-2.5 w-[380px] max-w-[calc(100vw-2rem)] pointer-events-none"></div>

<script>
    (function() {
        const container = document.getElementById('toastContainer');

        window.showToast = function(type, message, duration = 2500) {
            if (!container || !message) return;

            const config = {
                success: {
                    bg: 'bg-white',
                    border: 'border-emerald-200',
                    badgeBg: 'bg-emerald-50',
                    textColor: 'text-gray-800',
                    icon: `<svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`,
                    title: 'Berhasil'
                },
                error: {
                    bg: 'bg-white',
                    border: 'border-rose-200',
                    badgeBg: 'bg-rose-50',
                    textColor: 'text-gray-800',
                    icon: `<svg class="w-5 h-5 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`,
                    title: 'Gagal'
                },
                warning: {
                    bg: 'bg-white',
                    border: 'border-amber-200',
                    badgeBg: 'bg-amber-50',
                    textColor: 'text-gray-800',
                    icon: `<svg class="w-5 h-5 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>`,
                    title: 'Peringatan'
                },
                info: {
                    bg: 'bg-white',
                    border: 'border-sky-200',
                    badgeBg: 'bg-sky-50',
                    textColor: 'text-gray-800',
                    icon: `<svg class="w-5 h-5 text-sky-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`,
                    title: 'Informasi'
                }
            };

            const t = config[type] || config.info;
            const toastEl = document.createElement('div');
            toastEl.className = `pointer-events-auto relative overflow-hidden rounded-2xl ${t.bg} border ${t.border} p-4 shadow-xl transition-all duration-300 transform -translate-y-4 opacity-0 scale-95 flex items-start gap-3 w-full`;

            toastEl.innerHTML = `
                <div class="p-1.5 ${t.badgeBg} rounded-xl shrink-0">
                    ${t.icon}
                </div>
                <div class="flex-1 pr-2">
                    <p class="text-xs font-bold ${t.textColor}">${t.title}</p>
                    <p class="text-xs text-gray-600 mt-0.5 leading-relaxed">${message}</p>
                </div>
                <button type="button" class="text-gray-400 hover:text-gray-600 transition shrink-0 p-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            `;

            container.appendChild(toastEl);

            // Animate in (Slide down & Fade in)
            requestAnimationFrame(() => {
                toastEl.classList.remove('-translate-y-4', 'opacity-0', 'scale-95');
                toastEl.classList.add('translate-y-0', 'opacity-100', 'scale-100');
            });

            const dismiss = () => {
                toastEl.classList.remove('translate-y-0', 'opacity-100', 'scale-100');
                toastEl.classList.add('-translate-y-4', 'opacity-0', 'scale-95');
                setTimeout(() => {
                    if (toastEl.parentNode) {
                        toastEl.parentNode.removeChild(toastEl);
                    }
                }, 300);
            };

            // Manual close button
            const closeBtn = toastEl.querySelector('button');
            if (closeBtn) {
                closeBtn.addEventListener('click', dismiss);
            }

            // Auto dismiss after 2.5 seconds
            setTimeout(dismiss, duration);
        };

        // Render any server-side flash messages on load
        const initialToasts = @json($toasts);
        if (Array.isArray(initialToasts) && initialToasts.length > 0) {
            document.addEventListener('DOMContentLoaded', function() {
                initialToasts.forEach((item, index) => {
                    setTimeout(() => {
                        window.showToast(item.type, item.message, 2500);
                    }, index * 200);
                });
            });
        }
    })();
</script>
