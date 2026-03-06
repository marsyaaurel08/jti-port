<div class="bg-[#F8FAFC] min-h-screen">
    <div class="max-w-[1440px] mx-auto px-6 lg:px-24 py-12 md:py-20">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">

            <div class="lg:col-span-3" wire:ignore>
                <div class="sticky top-26">
                    <x-user.sidebar :user="$this->user" />
                </div>
            </div>

            <div class="lg:col-span-9 space-y-10">
                @if (empty($cartItems))
                    <div class="bg-white rounded-[2rem] border border-slate-200 p-16 text-center shadow-sm">
                        <div class="w-16 h-16 bg-slate-50 rounded-2xl mx-auto flex items-center justify-center text-slate-400 mb-6">
                            <i data-lucide="shopping-basket" class="w-8 h-8" style="stroke-width: 1.5px;"></i>
                        </div>
                        <h3 class="text-xl font-medium text-slate-900 mb-2">keranjang masih kosong</h3>
                        <p class="text-slate-600 max-w-xs mx-auto mb-8 text-sm leading-relaxed">
                            temukan materi sertifikasi yang sesuai dengan rencana karier anda.
                        </p>
                        <a href="{{ route('courses.index') }}"
                            class="inline-flex items-center gap-2 px-6 py-3 bg-slate-900 text-white text-sm font-medium rounded-xl hover:bg-jti-primary transition-all active:scale-95 group">
                            jelajahi kursus
                            <i data-lucide="chevron-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
                        </a>
                    </div>
                @else
                    <div class="space-y-6">
                        <div class="bg-white rounded-[2rem] border border-slate-200 overflow-hidden shadow-sm shadow-slate-200/40">

                            <div class="p-8 pb-0 flex items-center gap-4">
                                <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                                    <i data-lucide="shopping-cart" class="w-6 h-6 text-slate-600"></i>
                                </div>
                                <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Keranjang Anda</h2>
                            </div>

                            <div class="divide-y divide-slate-100 mt-8">
                                @foreach ($cartItems as $item)
                                    @php
                                        $course = $item['course'] ?? $item['class'];
                                        $thumbnail = $course['thumbnail_url'] ?? null;
                                        $isClass = isset($item['class_id']) && $item['class_id'] !== null;
                                    @endphp
                                    <div wire:key="{{ $item['id'] }}"
                                        class="group p-8 flex flex-col sm:flex-row items-center gap-8 hover:bg-slate-50 transition-colors duration-500">

                                        <div class="relative w-32 h-20 shrink-0 overflow-hidden rounded-2xl bg-slate-100 border border-slate-100">
                                            <img src="{{ $thumbnail ?? 'https://via.placeholder.com/150' }}"
                                                class="w-full h-full object-cover group-hover:scale-105 transition-all duration-700">
                                        </div>

                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 text-[10px] text-slate-500 font-semibold mb-1 uppercase tracking-wider">
                                                <span class="w-1.5 h-1.5 rounded-full {{ $isClass ? 'bg-amber-500' : 'bg-jti-primary' }}"></span>
                                                {{ $isClass ? 'live class' : 'kursus digital' }}
                                            </div>

                                            <h3 class="text-lg font-medium text-slate-900 leading-snug group-hover:text-jti-primary transition-colors">
                                                {{ $course['title'] }}
                                            </h3>

                                            <div class="flex items-baseline gap-1 mt-2">
                                                <span class="text-[10px] font-bold text-slate-500">Rp</span>
                                                <span class="text-slate-900 font-bold text-lg tracking-tight">
                                                    {{ number_format($course['price'], 0, ',', '.') }}
                                                </span>
                                            </div>
                                        </div>

                                        <div class="shrink-0">
                                            <button wire:click="removeItem({{ $item['id'] }})"
                                                wire:loading.attr="disabled"
                                                class="flex items-center justify-center w-11 h-11 rounded-2xl bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white transition-all duration-300 active:scale-90 border border-rose-100 group/del shadow-sm"
                                                title="hapus">

                                                <div wire:loading.remove wire:target="removeItem({{ $item['id'] }})">
                                                    <i data-lucide="trash-2" class="w-5 h-5" style="stroke-width: 1.8px;"></i>
                                                </div>

                                                <div wire:loading wire:target="removeItem({{ $item['id'] }})">
                                                    <i data-lucide="loader-2" class="w-5 h-5 animate-spin"></i>
                                                </div>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
