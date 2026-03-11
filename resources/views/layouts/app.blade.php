<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Jtiport - Learning Management System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-white text-gray-900 -tracking-[0.02em]">

    <x-layout.navbar />

    {{-- Flash Message Toast --}}
    <x-alert.toast />

    <main class="min-h-screen pt-20">
        @hasSection('content')
            @yield('content')
        @else
            {{ $slot }}
        @endif
    </main>

    @if (!isset($showFooter) || $showFooter)
        <x-layout.footer />
    @endif

</body>

</html>
