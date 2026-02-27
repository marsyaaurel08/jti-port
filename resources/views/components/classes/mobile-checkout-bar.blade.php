@props(['class'])

{{-- Mobile Checkout Bar (Fixed Bottom) - Hidden on Desktop --}}
<div class="fixed bottom-0 left-0 right-0 lg:hidden bg-white border-t border-gray-200 shadow-2xl shadow-black/5 z-40">
    <div class="px-4 py-4 flex items-center justify-between gap-3">
        {{-- Price and Info Column --}}
        <div class="flex items-center gap-3 flex-1">
            <div class="flex-1">
                <p class="text-xs text-gray-500 font-semibold uppercase tracking-tight">
                    Harga Kelas
                </p>
                <p class="text-2xl font-black text-jti-primary">
                    Rp{{ number_format($class->price, 0, ',', '.') }}
                </p>
            </div>

            {{-- Availability Badge --}}
            <div class="flex-shrink-0 text-center">
                <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Sisa</p>
                <p class="text-xl font-black text-jti-yellow">
                    {{ $class->max_capacity - $class->current_enrolled }}
                </p>
            </div>
        </div>

        {{-- Buy Now Button --}}
        <form action="{{ route('student.cart.add-class', $class->id) }}" method="POST" class="flex-shrink-0 w-32">
            @csrf
            <button
                class="w-full py-3 bg-jti-primary text-white font-semibold rounded-lg hover:bg-jti-maroon transition-all active:scale-95 text-sm">
                Daftar
            </button>
        </form>
    </div>
</div>

{{-- Add padding to main content on mobile so it doesn't get hidden under fixed bar --}}
<style>
    @media (max-width: 1024px) {
        main {
            padding-bottom: 88px;
        }
    }
</style>
