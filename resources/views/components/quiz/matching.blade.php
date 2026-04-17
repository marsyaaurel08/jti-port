@props([
    'items' => [],
    'matches' => [],
    'selected' => [],
    'wireModel' => 'matchingAnswers',
    'questionId' => null,
])

@php
    $selectedAnswers = is_array($selected) ? $selected : [];
    if (is_array($selectedAnswers) && array_is_list($selectedAnswers)) {
        $selectedAnswers = [];
    }
@endphp

<div
    x-data="matchingComponent({
items: @js($items ?? []),
        matches: @js($matches),
        selected: @js($selectedAnswers),
        wireModel: @js($wireModel),
        questionId: @js($questionId),
    })"
    x-init="init()"
    class="space-y-4 pb-20"
>
    <!-- Instructions -->
    <p class="text-sm text-gray-600 text-center mb-4">
        Drag items from the left column to match them with the correct items on the right
    </p>

    <!-- Two-column layout -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Left Column: Draggable Items -->
        <div class="space-y-3">
            <h3 class="text-sm font-semibold text-gray-700 mb-2">Items</h3>
            <div class="space-y-2">
                <template x-for="(item, index) in items" :key="item.id">
                    <div
                        class="p-3 md:p-4 rounded-lg border-2 border-gray-200 bg-white hover:border-[#ffc107] hover:bg-yellow-50/30 cursor-grab active:cursor-grabbing transition-all shadow-sm hover:shadow-md"
                        draggable="true"
                        :data-item-id="item?.id"
                        @dragstart="dragStart($event)"
                        @dragend="dragEnd($event)"
                        :class="draggedItemId === item?.id ? 'opacity-50' : ''"
                        x-show="!isItemMatched(item?.id)"
                        x-transition
                    >
                        <div class="flex items-center">
                            <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 text-sm font-semibold mr-3">
                                <span x-text="getItemNumber(item?.id)"></span>
                            </div>
                            <span class="font-medium text-gray-700 text-sm md:text-base" x-text="item?.text"></span>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Right Column: Match Drop Zones -->
        <div class="space-y-3">
            <h3 class="text-sm font-semibold text-gray-700 mb-2">Matches</h3>
            <div class="space-y-2">
                <template x-for="match in matches" :key="match.id">
                    <div
                        class="p-3 md:p-4 rounded-lg border-2 transition-all min-h-[60px] flex items-center"
                        :class="getMatchZoneClass(match.id)"
                        @dragover.prevent="dragOver($event, match.id)"
                        @dragleave="dragLeave($event, match.id)"
                        @drop="drop($event, match.id)"
                    >
                        <!-- Drop zone content -->
                        <div class="w-full flex items-center">
                            <!-- Match label -->
                            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 text-sm font-semibold mr-3">
                                <span x-text="getMatchLetter(match.id)"></span>
                            </div>

                            <!-- Dropped item or empty state -->
                            <div class="flex-1">
                                <template x-if="getMatchedItem(match.id)">
                                    <div class="flex items-center justify-between bg-[#ffc107] text-white rounded-md px-3 py-2">
                                        <span class="font-medium text-sm" x-text="getMatchedItem(match.id)?.text"></span>
                                        <button
                                            @click.stop="removeMatch(match.id)"
                                            class="ml-2 hover:bg-white/20 rounded-full p-1 transition-colors"
                                            title="Remove match"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                </template>
                                <template x-if="!getMatchedItem(match.id)">
                                    <div class="text-gray-400 text-sm" :class="{'text-[#ffc107]': hoveredMatchId === match.id}">
                                        <span x-text="hoveredMatchId === match.id ? 'Drop here' : match.text"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- Reset button -->
    <div class="text-center mt-4">
        <button
            @click="resetAll()"
            class="text-sm text-gray-500 hover:text-gray-700 underline transition-colors"
        >
            Reset all matches
        </button>
    </div>
</div>
