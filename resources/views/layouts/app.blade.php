<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jtiport - Learning Management System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-white text-gray-900">

    <x-layout.navbar />

    <main class="min-h-screen">
        {{ $slot }}
    </main>

    <x-layout.footer />

</body>

</html>
