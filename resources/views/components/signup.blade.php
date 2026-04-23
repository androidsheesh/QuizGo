<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'QuizGo' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-[#f8f8f8] min-h-screen min-w-screen flex items-center justify-center p-6">
    {{ $slot }}
</body>
</html>

