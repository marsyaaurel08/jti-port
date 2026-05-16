<div class="space-y-4">
    <div>
        <span class="font-semibold text-gray-700">Nama:</span>
        <span class="ml-2">{{ $studentName }}</span>
    </div>

    @if(!empty($quizScores))
        @foreach($quizScores as $quiz)
            <div class="border-b pb-3">
                <div>
                    <span class="font-semibold text-gray-700">Kuis:</span>
                    <span class="ml-2">{{ $quiz['quiz_name'] }}</span>
                </div>
                <div>
                    <span class="font-semibold text-gray-700">Nilai:</span>
                    <span class="ml-2">{{ $quiz['scores'] }}</span>
                </div>
            </div>
        @endforeach
    @else
        <div class="text-gray-500 italic">
            Dat nilai kuis tidak tersedia.
        </div>
    @endif
</div>
