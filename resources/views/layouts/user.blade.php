<div class="bg-[#F8FAFC] min-h-screen">
    <div class="max-w-[1440px] mx-auto px-6 lg:px-24 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
            <div class="lg:col-span-3" wire:ignore>
                <div class="sticky top-26">
                    <x-user.sidebar :user="$user" />
                </div>
            </div>

            <div class="lg:col-span-9 space-y-10">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
