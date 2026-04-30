@props([
    'items' => [],
    'selected' => [],
    'wireModel' => 'dragDropAnswers',
    'questionId' => null,
    'shuffleItems' => false,
    'savedOrder' => null,
])

@php
    $originalItems = collect($items)->values()->toArray();
@endphp

<div
    x-data="dragDropComponent({
        initialItems: @js($originalItems),
        shuffledItems: @js($shuffleItems ? collect($items)->shuffle()->values()->toArray() : $originalItems),
        wireModel: @js($wireModel),
        questionId: @js($questionId),
        shuffleOnInit: @js($shuffleItems),
        hasSavedAnswer: @js(!empty($savedOrder)),
    })"
    x-init="init()"
    class="space-y-4 pb-20"
    data-question-id="{{ $questionId }}"
>

    <!-- Drop Area (sorted result) -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <template x-for="(item, index) in items" :key="item.id">
            <div
                class="p-4 rounded-lg border-2 border-dashed border-yellow-400 bg-yellow-100/30 text-center font-semibold cursor-move"
                draggable="true"
                @dragstart="dragStart(index)"
                @dragover.prevent
                @drop="drop(index)"
            >
                <div x-text="item.text"></div>
            </div>
        </template>
    </div>

    <!-- Hint -->
    <p class="text-xs text-gray-500 text-center">
        Drag items to reorder them
    </p>
</div>
