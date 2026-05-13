<?php

namespace App\Filament\Resources\Courses\Tables;

use App\Models\QuizAttempt;
use Filament\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CourseProgressTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-m-user-circle'),

                TextColumn::make('enrolled_at')
                    ->label('Tgl Terdaftar')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->icon('heroicon-m-calendar-days'),

                TextColumn::make('actual_completed_lesson_count')
                    ->label('Materi Selesai')
                    ->alignCenter()
                    ->formatStateUsing(fn($record) => $record->actual_completed_lesson_count . ' / ' . $record->course->total_lessons)
                    ->icon('heroicon-m-check-badge'),

                TextColumn::make('progress_percentage')
                    ->label('Progress')
                    ->formatStateUsing(fn($state) => number_format($state, 0) . '%')
                    ->color(fn($state) => match (true) {
                        $state >= 75 => 'success',
                        $state >= 50 => 'info',
                        $state >= 25 => 'warning',
                        default => 'danger',
                    })
                    ->badge(),

                IconColumn::make('is_completed')
                    ->label('Lulus')
                    ->boolean()
                    ->alignCenter(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'active' => 'success',
                        'completed' => 'info',
                        'expired' => 'danger',
                    }),

                TextColumn::make('lastAccessedLesson.title')
                    ->label('Terakhir Diakses')
                    ->placeholder('Belum dimulai')
                    ->limit(30)
                    ->tooltip(fn($record) => $record->lastAccessedLesson?->title)
                    ->icon('heroicon-m-play-pause'),
            ])
            ->actions([
                Action::make('view_quiz_scores')
                    ->label('Detail Nilai')
                    ->icon('heroicon-m-eye')
                    ->color('primary')
                    ->modalHeading('Detail Nilai Kuis')
                    ->modalContent(function ($record) {
                        return view('filament.resources.courses.modals.quiz-scores', [
                            'studentName' => $record->user->name,
                            'quizScores' => self::getQuizScores($record),
                        ]);
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->visible(fn($record) => $record->course->lessons()->whereHas('quiz')->exists()),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'active' => 'Aktif',
                        'completed' => 'Lulus',
                        'expired' => 'Kedaluwarsa',
                    ])
                    ->label('Filter Status'),

                SelectFilter::make('progress_range')
                    ->options([
                        '0-25' => '0-25%',
                        '25-50' => '25-50%',
                        '50-75' => '50-75%',
                        '75-100' => '75-100%',
                    ])
                    ->query(function ($query, $data) {
                        if (isset($data['value'])) {
                            [$min, $max] = explode('-', $data['value']);
                            return $query->whereHas('course', function ($q) use ($min, $max) {
                                $q->whereRaw('(
                                    SELECT COUNT(*)
                                    FROM course_progress
                                    WHERE course_progress.enrollment_id = enrollments.id
                                    AND course_progress.is_completed = 1
                                ) / total_lessons * 100 BETWEEN ? AND ?', [$min, $max]);
                            });
                        }
                        return $query;
                    })
                    ->label('Filter Progress'),
            ])
            ->defaultSort('enrolled_at', 'desc');
    }

    public static function getQuizScores($enrollment): array
    {
        $quizzes = $enrollment->course->lessons()
            ->with('quiz')
            ->whereHas('quiz')
            ->orderBy('sort_order')
            ->get();

        $scores = [];

        foreach ($quizzes as $lesson) {
            $attempts = QuizAttempt::where('quiz_id', $lesson->quiz->id)
                ->where('user_id', $enrollment->user_id)
                ->orderBy('created_at')
                ->get();

            $scoreList = $attempts->pluck('score')
                ->map(fn($score) => number_format((float) $score, 2, ',', ''))
                ->implode(' - ');

            $scores[] = [
                'quiz_name' => $lesson->quiz->title ?? $lesson->title,
                'scores' => $scoreList,
            ];
        }

        return $scores;
    }
}
