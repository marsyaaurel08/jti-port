<?php

namespace App\Filament\Resources\Quizzes\Pages;

use App\Filament\Resources\Quizzes\QuizResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateQuiz extends CreateRecord
{
    protected static string $resource = QuizResource::class;

    public function mount(): void
    {
        // Prevent direct quiz creation - quizzes should only be created from lessons
        Notification::make()
            ->warning()
            ->title('Quiz Creation Restricted')
            ->body('Quizzes can only be created from the Course Materials page when editing a quiz-type lesson.')
            ->send();

        $this->redirect(QuizResource::getUrl('index'));
    }
}
