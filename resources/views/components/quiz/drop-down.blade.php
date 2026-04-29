@props([
    'template' => '',
    'blanks' => [],
    'selected' => [],
    'wireModel' => 'dropDownAnswers',
    'questionId' => null,
])

@php
    $selectedAnswers = is_array($selected) ? $selected : [];
    if (is_array($selectedAnswers) && array_is_list($selectedAnswers)) {
        $selectedAnswers = [];
    }

    $blanksMap = [];
    foreach ($blanks as $blank) {
        $blanksMap[$blank['id']] = $blank['options'] ?? [];
    }
@endphp

<div
    x-data="dropDownComponent({
        template: @js($template),
        blanksMap: @js($blanksMap),
        selected: @js($selectedAnswers),
        wireModel: @js($wireModel),
        questionId: @js($questionId),
    })"
    x-init="init()"
    class="space-y-4"
>
    <!-- Code Template Display with Dropdowns -->
    <div class="bg-gray-900 rounded-lg p-4 md:p-6 overflow-x-auto">
        <pre class="text-sm md:text-base font-mono text-gray-100 whitespace-pre-wrap" x-html="renderedTemplate"></pre>
    </div>

    <!-- Instructions -->
    <p class="text-sm text-gray-600">
        Select the correct options from the dropdowns above to fill in the blanks.
    </p>
</div>