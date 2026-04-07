@props([
    'items' => [],
    'selected' => [],
    'wireModel' => 'dragDropAnswers',
    'questionId' => null,
])

@php
    $orderedItems = collect($items)
        ->sortBy(fn($item) => array_search($item['id'], $selected ?: array_column($items, 'id')))
        ->values();
@endphp

<div
    x-data="dragDropComponent({
        initialItems: @js($orderedItems),
        wireModel: @js($wireModel),
        questionId: @js($questionId),
    })"
    x-init="init()"
    class="space-y-4 pb-20"
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
                <div class="text-xs text-gray-400 mb-1">#<span x-text="index + 1"></span></div>
                <div x-text="item.text"></div>
            </div>
        </template>
    </div>

    <!-- Hint -->
    <p class="text-xs text-gray-500 text-center">
        Drag items to reorder them
    </p>
</div>
