<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Auth Page' }} - MyApp</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex items-center justify-center bg-gray-100">

    <div class="w-full max-w-md bg-white shadow-lg rounded-lg p-8">
        <h1 class="text-2xl font-bold text-center text-gray-800 mb-6">
            {{ $title ?? '' }}
        </h1>

        {{-- Content Section --}}
        {{ $slot }}
    </div>

</body>
</html>
