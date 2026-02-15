@props(['course'])

{{-- Mobile Checkout Bar (Fixed Bottom) - Hidden on Desktop --}}
<div class="fixed bottom-0 left-0 right-0 lg:hidden bg-white border-t border-gray-200 shadow-2xl shadow-black/5 z-40">
    <div class="px-4 py-4 flex items-center justify-between gap-3">
        {{-- Price and Cart Icon Column --}}
        <div class="flex items-center gap-3 flex-1">
            <div class="flex-1">
                <p class="text-xs text-gray-500 font-semibold uppercase tracking-tight">
                    Harga Sekarang
                </p>
                <p class="text-2xl font-black text-jti-primary">
                    Rp{{ number_format($course->price, 0, ',', '.') }}
                </p>
            </div>

            {{-- Cart Icon Button --}}
            <button
                class="flex-shrink-0 p-3 border-2 border-jti-primary text-jti-primary rounded-lg hover:bg-jti-light transition-all active:scale-95"
                title="Tambahkan ke Keranjang">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </button>
        </div>

        {{-- Buy Now Button --}}
        <form action="{{ route('cart.add', $course->id) }}" method="POST" class="flex-shrink-0 w-32">
            @csrf
            <button
                class="w-full py-3 bg-jti-primary text-white font-semibold rounded-lg hover:bg-jti-maroon transition-all active:scale-95 text-sm">
                Beli Sekarang
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
