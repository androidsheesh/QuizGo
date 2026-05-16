<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'QuizGo' }}</title>

    {{-- Prevent browser caching --}}
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded" rel="stylesheet">

    <style>[x-cloak] { display: none !important; }</style>
    @php
        $broadcastUser = auth('teacher')->user() ?? auth('web')->user();
    @endphp
    @if($broadcastUser)
        <script>
            window.QuizGo = {
                userId: @json($broadcastUser->id),
                teacherId: @json($broadcastUser->isTeacher() ? $broadcastUser->id : null),
            };
        </script>
    @endif
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white text-gray-900 font-['Outfit'] antialiased selection:bg-blue-500 selection:text-white">
    @if($broadcastUser)
        <div
            id="global-toast-container"
            class="fixed top-4 left-1/2 z-[9999] w-[calc(100%-2rem)] max-w-md -translate-x-1/2 space-y-3 pointer-events-none"
            aria-live="polite"
            aria-atomic="true"
        ></div>
    @endif

    {{ $slot }}

    {{-- Prevent back button after logout --}}
    <script>
        history.pushState(null, null, location.href);
        window.onpopstate = function () {
            history.pushState(null, null, location.href);
        };
    </script>

    {{-- Realtime logout --}}
    @if($broadcastUser)
        <script>
            window.Echo.private(`user.{{ $broadcastUser->id }}`)
                .listen('.session.hijacked', () => {
                    window.location.href = '/signin?kicked=1';
                });
        </script>
    @endif
</body>
</html>
