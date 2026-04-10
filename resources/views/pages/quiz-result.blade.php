
// Belum di pakai

@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <!-- Back Button -->
    @if ($attempt->quiz && $attempt->quiz->lesson && $attempt->quiz->lesson->chapter && $attempt->quiz->lesson->chapter->course)
        <a href="{{ route('student.lesson', [
            is_string($attempt->quiz->lesson->chapter->course->slug) ? $attempt->quiz->lesson->chapter->course->slug : '',
            is_string($attempt->quiz->lesson->slug) ? $attempt->quiz->lesson->slug : ''
        ]) }}"
           class="inline-flex items-center text-gray-600 hover:text-gray-900 mb-6 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Pelajaran
        </a>
    @else
        <a href="{{ route('student.my-courses') }}"
           class="inline-flex items-center text-gray-600 hover:text-gray-900 mb-6 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Kursus
        </a>
    @endif

    <!-- Result Header -->
    <div class="bg-white rounded-lg shadow-md p-8 mb-6">
        <div class="text-center mb-6">
            @if ($attempt->isPassed())
                <div class="inline-flex items-center justify-center w-20 h-20 bg-green-100 rounded-full mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Selamat! Anda Lulus</h1>
                <p class="text-gray-600">Anda telah menyelesaikan kuis ini dengan baik.</p>
            @else
                <div class="inline-flex items-center justify-center w-20 h-20 bg-red-100 rounded-full mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Maaf, Anda Belum Lulus</h1>
                <p class="text-gray-600">Jangan menyerah! Coba lagi untuk mencapai nilai minimum.</p>
            @endif
        </div>

        <!-- Score Display -->
        <div class="flex justify-center mb-6">
            <div class="text-center">
                <p class="text-sm text-gray-500 uppercase tracking-wide mb-1">Nilai Anda</p>
                <p class="text-5xl font-bold {{ $attempt->isPassed() ? 'text-green-600' : 'text-red-600' }}">
                    @if (is_numeric($attempt->score))
                        {{ number_format($attempt->score, 2) }}%
                    @else
                        {{ $attempt->score }}%
                    @endif
                </p>
                <p class="text-sm text-gray-500 mt-2">
                    @if ($attempt->quiz && isset($attempt->quiz->pass_score) && is_numeric($attempt->quiz->pass_score))
                        Nilai minimum untuk lulus: {{ $attempt->quiz->pass_score }}%
                    @else
                        Nilai minimum untuk lulus: 0%
                    @endif
                </p>
            </div>
        </div>

        <!-- Attempt Details -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 border-t border-gray-200 pt-6">
            <div class="text-center">
                <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Tanggal Pengerjaan</p>
                <p class="text-lg font-semibold text-gray-900">
                    {{ $attempt->finished_at ? $attempt->finished_at->format('d M Y') : '-' }}
                </p>
                <p class="text-sm text-gray-500">
                    {{ $attempt->finished_at ? $attempt->finished_at->format('H:i') : '-' }}
                </p>
            </div>
            <div class="text-center">
                <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Waktu Pengerjaan</p>
                @php
                    $timeTaken = $attempt->started_at && $attempt->finished_at
                        ? $attempt->started_at->diff($attempt->finished_at)
                        : null;
                @endphp
                <p class="text-lg font-semibold text-gray-900">
                    @if ($timeTaken && is_object($timeTaken))
                        @if ($timeTaken->h > 0)
                            {{ $timeTaken->h }}j {{ $timeTaken->i }}m {{ $timeTaken->s }}d
                        @elseif ($timeTaken->i > 0)
                            {{ $timeTaken->i }}m {{ $timeTaken->s }}d
                        @else
                            {{ $timeTaken->s }}d
                        @endif
                    @else
                        -
                    @endif
                </p>
            </div>
            <div class="text-center">
                <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Jumlah Pertanyaan</p>
                <p class="text-lg font-semibold text-gray-900">
                    {{ is_countable($attempt->answers) ? $attempt->answers->count() : 0 }}
                </p>
            </div>
        </div>
    </div>

    <!-- Questions Breakdown -->
    <div class="bg-white rounded-lg shadow-md p-8">
        <h2 class="text-xl font-bold text-gray-900 mb-6">Detail Jawaban</h2>

        <div class="space-y-6">
            @foreach ($attempt->answers as $index => $answer)
                <div class="border border-gray-200 rounded-lg p-6 {{ $answer->is_correct ? 'bg-green-50' : 'bg-red-50' }}">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-white border-2 {{ $answer->is_correct ? 'border-green-500 text-green-600' : 'border-red-500 text-red-600' }} font-bold text-sm mr-3">
                                @if (is_int($index))
                                    {{ $index + 1 }}
                                @else
                                    #
                                @endif
                            </span>
                            <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $answer->is_correct ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                @if (is_bool($answer->is_correct))
                                    {{ $answer->is_correct ? 'Benar' : 'Salah' }}
                                @else
                                    N/A
                                @endif
                            </span>
                        </div>
                        <span class="text-sm text-gray-500">
                            @if (is_numeric($answer->score_obtained))
                                {{ number_format($answer->score_obtained, 2) }} poin
                            @else
                                {{ $answer->score_obtained }} poin
                            @endif
                        </span>
                    </div>

                    <h3 class="text-lg font-semibold text-gray-900 mb-3">
                        @if (is_string($answer->question->question_text))
                            {{ $answer->question->question_text }}
                        @elseif (is_numeric($answer->question->question_text))
                            {{ $answer->question->question_text }}
                        @elseif (is_bool($answer->question->question_text))
                            {{ $answer->question->question_text ? 'true' : 'false' }}
                        @else
                            {{ json_encode($answer->question->question_text) }}
                        @endif
                    </h3>

                    @php
                        $questionConfig = $answer->question->question_config;
                        $config = (is_array($questionConfig) || is_object($questionConfig)) ? (array) $questionConfig : [];
                        $options = is_array($config) && isset($config['options']) && is_array($config['options']) ? $config['options'] : [];
                        $userAnswer = $answer->user_answer ?? null;
                        $correctAnswer = $answer->question->answer_key ?? null;
                    @endphp

                    @if (!empty($options) && is_array($options))
                        <div class="space-y-2">
                            @foreach ($options as $optionIndex => $option)
                                @php
                                    $isSelected = $userAnswer === $optionIndex || (is_array($userAnswer) && in_array($optionIndex, $userAnswer));
                                    $isCorrectOption = $correctAnswer === $optionIndex || (is_array($correctAnswer) && in_array($optionIndex, $correctAnswer));
                                @endphp
                                <div class="flex items-center p-3 rounded-lg border
                                    {{ $isSelected && $isCorrectOption ? 'bg-green-100 border-green-300' : '' }}
                                    {{ $isSelected && !$isCorrectOption ? 'bg-red-100 border-red-300' : '' }}
                                    {{ !$isSelected && $isCorrectOption ? 'bg-green-50 border-green-200' : '' }}
                                    {{ !$isSelected && !$isCorrectOption ? 'bg-white border-gray-200' : '' }}">
                                    <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center mr-3
                                        {{ $isSelected ? ($isCorrectOption ? 'border-green-500 bg-green-500' : 'border-red-500 bg-red-500') : 'border-gray-300' }}">
                                        @if ($isSelected)
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                        @endif
                                    </div>
                                    <span class="text-gray-700">
                                        @if (is_string($option))
                                            {{ $option }}
                                        @elseif (is_numeric($option))
                                            {{ $option }}
                                        @elseif (is_bool($option))
                                            {{ $option ? 'true' : 'false' }}
                                        @else
                                            {{ json_encode($option) }}
                                        @endif
                                    </span>
                                    @if (!$isSelected && $isCorrectOption)
                                        <span class="ml-auto text-xs text-green-600 font-semibold">
                                            Jawaban Benar
                                        </span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if ($answer->question->explanation)
                        <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                            <p class="text-sm text-blue-800">
                                @if (is_string($answer->question->explanation))
                                    <span class="font-semibold">Penjelasan:</span> {{ $answer->question->explanation }}
                                @elseif (is_numeric($answer->question->explanation))
                                    <span class="font-semibold">Penjelasan:</span> {{ $answer->question->explanation }}
                                @elseif (is_bool($answer->question->explanation))
                                    <span class="font-semibold">Penjelasan:</span> {{ $answer->question->explanation ? 'true' : 'false' }}
                                @else
                                    <span class="font-semibold">Penjelasan:</span> {{ json_encode($answer->question->explanation) }}
                                @endif
                            </p>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <!-- Retry Button -->
    @if (!$attempt->isPassed() && $attempt->quiz)
        <div class="mt-6 text-center">
            <a href="{{ route('student.quiz.attempt', $attempt->quiz->id) }}"
               class="inline-flex items-center px-6 py-3 bg-[#ffc107] hover:bg-yellow-500 text-white font-semibold rounded-lg transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Coba Lagi
            </a>
        </div>
    @endif
</div>
@endsection
