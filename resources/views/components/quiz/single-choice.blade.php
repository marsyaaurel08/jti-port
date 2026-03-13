{{--
    Single Choice Quiz Component
--}}
@props([
    'options' => [],
    'selected' => null,
    'wireModel' => 'selectedAnswer',
    'questionId' => null,
    'questionType' => 'single_choice',
])

<div wire:key="question-{{ $questionId ?? 'default' }}" class="space-y-3 md:space-y-4 pb-16">
    @foreach($options as $option)
        <label class="relative flex items-center p-3 md:p-4 rounded-lg border-2 {{ $selected === $option['id'] ? 'border-[#ffc107] bg-yellow-50/30' : 'border-gray-200 bg-white hover:border-gray-300 hover:bg-gray-50' }} cursor-pointer transition-all">
            <div class="relative flex items-center justify-center">
                {{-- Hidden Native Radio for Livewire --}}
                <input type="radio"
                    name="answer_{{ $questionId ?? 'default' }}"
                    wire:model.live="{{ $wireModel }}"
                    value="{{ $option['id'] }}"
                    onchange="dispatchQuizAnswerSelected({{ $questionId ?? 'null' }}, {{ $option['id'] }}, '{{ $questionType }}')"
                    class="peer opacity-0 absolute h-full w-full cursor-pointer">

                {{-- Custom Styled Radio Button --}}
                <div class="w-4 h-4 md:w-5 md:h-5 rounded-full border-2 transition-colors flex items-center justify-center
                    {{ $selected === $option['id'] ? 'bg-[#ffc107] border-[#ffc107]' : 'bg-white border-gray-300 group-hover:border-[#ffc107]' }}">

                    {{-- Radio Dot (Only shown if selected) --}}
                    @if($selected === $option['id'])
                        <div class="w-2 h-2 md:w-2.5 md:h-2.5 rounded-full bg-white"></div>
                    @endif
                </div>
            </div>
            <span class="ml-3 font-medium {{ $selected === $option['id'] ? 'text-gray-800' : 'text-gray-600' }} text-sm md:text-base">{{ $option['text'] }}</span>
        </label>
    @endforeach
</div>
