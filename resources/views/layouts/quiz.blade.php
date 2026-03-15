{{--
    Quiz Layout
    Layout ini khusus untuk halaman kuis.
--}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Quiz - Jtiport</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-50 text-gray-900">
    @yield('content')
    @livewireScripts
    <script src="{{ asset('js/quiz-local-storage-plain.js') }}"></script>
</body>
</html>
