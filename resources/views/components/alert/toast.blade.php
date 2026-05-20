@if ($message = Session::get('success'))
    <div x-data="{ open: true }" x-show="open" x-transition class="fixed top-20 right-4 z-50 max-w-sm">
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 shadow-lg">
            <div class="flex items-start gap-3">
                <x-lucide-circle-check class="w-5 h-5 text-green-600 shrink-0 mt-0.5" />
                <div class="flex-1">
                    <p class="font-semibold text-green-800">{{ $message }}</p>
                </div>
                <button @click="open = false" class="text-green-600 hover:text-green-800">
                    <x-lucide-x class="w-5 h-5" />
                </button>
            </div>
        </div>
    </div>
@endif

@if ($message = Session::get('info'))
    <div x-data="{ open: true }" x-show="open" x-transition class="fixed top-20 right-4 z-50 max-w-sm">
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 shadow-lg">
            <div class="flex items-start gap-3">
                <x-lucide-info class="w-5 h-5 text-blue-600 shrink-0 mt-0.5" />
                <div class="flex-1">
                    <p class="font-semibold text-blue-800">{{ $message }}</p>
                </div>
                <button @click="open = false" class="text-blue-600 hover:text-blue-800">
                    <x-lucide-x class="w-5 h-5" />
                </button>
            </div>
        </div>
    </div>
@endif

@if ($message = Session::get('error'))
    <div x-data="{ open: true }" x-show="open" x-transition class="fixed top-20 right-4 z-50 max-w-sm">
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 shadow-lg">
            <div class="flex items-start gap-3">
                <x-lucide-circle-alert class="w-5 h-5 text-red-600 shrink-0 mt-0.5" />
                <div class="flex-1">
                    <p class="font-semibold text-red-800">{{ $message }}</p>
                </div>
                <button @click="open = false" class="text-red-600 hover:text-red-800">
                    <x-lucide-x class="w-5 h-5" />
                </button>
            </div>
        </div>
    </div>
@endif

{{-- Dynamic Toast Container --}}
<div id="toast-container" class="fixed top-20 right-4 z-99999 space-y-2"></div>

<script>
    window.showToast = function(message, type = 'success', duration = 4000) {
        const container = document.getElementById('toast-container');
        const toastId = 'toast-' + Date.now();

        const bgColor = type === 'success' ? 'bg-green-50 border-green-200' : type === 'info' ?
            'bg-blue-50 border-blue-200' : 'bg-red-50 border-red-200';
        const iconColor = type === 'success' ? 'text-green-600' : type === 'info' ? 'text-blue-600' :
            'text-red-600';
        const textColor = type === 'success' ? 'text-green-800' : type === 'info' ? 'text-blue-800' :
            'text-red-800';
        const buttonColor = type === 'success' ? 'text-green-600 hover:text-green-800' : type === 'info' ?
            'text-blue-600 hover:text-blue-800' : 'text-red-600 hover:text-red-800';

        // Icon Lucide berdasarkan type
        const iconName = type === 'success' ? 'circle-check' :
            type === 'info' ? 'info' :
            'circle-alert'; // default error

        const toast = document.createElement('div');
        toast.id = toastId;
        toast.className = `${bgColor} border rounded-lg p-4 shadow-lg max-w-sm`;
        toast.setAttribute('data-aos', 'fade-left');
        toast.setAttribute('data-aos-duration', '300');
        toast.setAttribute('data-aos-easing', 'ease-out');
        toast.innerHTML = `
            <div class="flex items-start gap-3">
                <x-lucide-${iconName} class="w-5 h-5 ${iconColor} shrink-0 mt-0.5" />
                <div class="flex-1">
                    <p class="font-semibold ${textColor}">${message}</p>
                </div>
                <button onclick="document.getElementById('${toastId}').remove()" class="${buttonColor}">
                    <x-lucide-x class="w-5 h-5" />
                </button>
            </div>
        `;

        container.appendChild(toast);

        // Trigger AOS animation jika pakai AOS
        if (window.AOS) {
            window.AOS.refresh();
        }

        // Auto close
        if (duration > 0) {
            setTimeout(() => {
                const el = document.getElementById(toastId);
                if (el) {
                    el.style.opacity = '0';
                    el.style.transform = 'translateX(100%)';
                    el.style.transition = 'all 300ms ease-in';
                    setTimeout(() => el.remove(), 300);
                }
            }, duration);
        }

        // Penting: panggil lucide.createIcons() setelah menambahkan elemen baru
        if (window.lucide) {
            window.lucide.createIcons();
        }
    };
</script>
