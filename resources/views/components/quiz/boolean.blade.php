<div wire:key="question-{{ $questionId ?? 'default' }}" class="space-y-3 md:space-y-4 pb-16">

    @php
        $options = [
            ['id' => 1, 'text' => 'True'],
            ['id' => 0, 'text' => 'False'],
        ];
    @endphp

    @foreach($options as $option)
        <label class="relative flex items-center p-3 md:p-4 rounded-lg border-2
            {{ $selected !== null && (int)$selected === (int)$option['id']
                ? 'border-jti-yellow bg-yellow-50/30'
                : 'border-gray-200 bg-white hover:border-gray-300 hover:bg-gray-50' }}
            cursor-pointer transition-all">

            <input
                type="radio"
                name="answer_{{ $questionId }}"
                wire:model="{{ $wireModel }}"
                value="{{ $option['id'] }}"
                class="absolute inset-0 opacity-0 cursor-pointer quiz-input">

            <div class="relative flex items-center justify-center pointer-events-none">
                <div class="w-4 h-4 md:w-5 md:h-5 rounded-full border-2 flex items-center justify-center
                    {{ $selected !== null && (int)$selected === (int)$option['id']
                        ? 'bg-jti-yellow border-jti-yellow'
                        : 'bg-white border-gray-300' }}">

                    @if($selected !== null && (int)$selected === (int)$option['id'])
                        <div class="w-2 h-2 md:w-2.5 md:h-2.5 rounded-full bg-white"></div>
                    @endif
                </div>
            </div>

            <span class="ml-3 font-medium
                {{ $selected !== null && (int)$selected === (int)$option['id']
                    ? 'text-gray-800'
                    : 'text-gray-600' }}
                text-sm md:text-base">
                {{ $option['text'] }}
            </span>
        </label>
    @endforeach

</div>
