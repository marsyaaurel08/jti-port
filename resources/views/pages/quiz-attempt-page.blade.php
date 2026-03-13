{{--
    Quiz Attempt Page View

    File ini berfungsi sebagai bridge antara route dan Livewire component.
    Component View: resources/views/livewire/quiz-attempt-view.blade.php
--}}
@extends('layouts.quiz')

@section('content')
<livewire:quiz-attempt :id="$quizId" />
@endsection
