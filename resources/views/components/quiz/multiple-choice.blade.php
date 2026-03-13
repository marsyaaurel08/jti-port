@props([
    'options' => [],
    'selected' => [], // Mengharapkan array, misal: [1, 3]
    'wireModel' => 'selectedAnswers',
    'questionType' => 'multiple_choice',
    'questionId' => null,
])

<div class="space-y-3 md:space-y-4 pb-16">
    @foreach($options as $option)
        @php
            $isSelected = in_array($option['id'], (array)$selected);
        @endphp

        <label class="relative flex items-center p-3 md:p-4 rounded-lg border-2 transition-all cursor-pointer group
            {{ $isSelected ? 'border-[#ffc107] bg-yellow-50/30' : 'border-gray-200 bg-white hover:border-gray-300 hover:bg-gray-50' }}">

            <div class="relative flex items-center justify-center">
                {{-- Hidden Native Checkbox untuk Livewire --}}
                <input type="checkbox"
                    wire:model.live="{{ $wireModel }}"
                    value="{{ $option['id'] }}"
                    onchange="dispatchQuizAnswerSelected({{ $questionId ?? 'null' }}, this.value, '{{ $questionType }}')"
                    class="peer opacity-0 absolute h-full w-full cursor-pointer">

                {{-- Custom Styled Checkbox Box --}}
                <div class="w-4 h-4 md:w-5 md:h-5 rounded border-2 transition-colors flex items-center justify-center
                    {{ $isSelected ? 'bg-[#ffc107] border-[#ffc107]' : 'bg-white border-gray-300 group-hover:border-[#ffc107]' }}">

                    {{-- Checkmark Icon (Hanya muncul jika dipilih) --}}
                    @if($isSelected)
                        <svg class="w-3 h-3 md:w-3.5 md:h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    @endif
                </div>
            </div>

            <span class="ml-3 font-medium text-sm md:text-base {{ $isSelected ? 'text-gray-800' : 'text-gray-600' }}">
                {{ $option['text'] }}
            </span>
        </label>
    @endforeach
</div>
