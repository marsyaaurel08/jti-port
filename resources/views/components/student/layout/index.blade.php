<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jtiport - Learning Management System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <x-layout.navbar />
    <div class="min-h-screen font-sans bg-gray-50">
        <div class="mx-auto py-14 md:px-6 -mt-20">
            <div class="flex flex-col lg:flex-row gap-8">
                <main class="flex-1 min-w-0 pt-32">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </div>

</body>

</html>
