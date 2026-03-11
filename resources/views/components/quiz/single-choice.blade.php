{{--
    Single Choice Quiz Component
--}}
@props([
    'options' => [],
    'selected' => null,
    'wireModel' => 'selectedAnswer',
])

<div class="space-y-3 md:space-y-4 pb-16">
    @foreach($options as $option)
        <label class="relative flex items-center p-3 md:p-4 rounded-lg border-2 {{ $selected === $option['id'] ? 'border-[#ffc107] bg-yellow-50/30' : 'border-gray-200 bg-white hover:border-gray-300 hover:bg-gray-50' }} cursor-pointer transition-all">
            <input type="radio" name="answer" wire:model.live="{{ $wireModel }}" value="{{ $option['id'] }}" class="w-4 h-4 md:w-5 md:h-5 accent-[#ffc107]">
            <span class="ml-3 font-medium {{ $selected === $option['id'] ? 'text-gray-800' : 'text-gray-600' }} text-sm md:text-base">{{ $option['text'] }}</span>
        </label>
    @endforeach
</div>
