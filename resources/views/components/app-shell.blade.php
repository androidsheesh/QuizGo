{{--
    Signed-in page frame: sidebar, a top bar, and the main content area.

    Below lg the sidebar becomes a drawer and the top bar holds the menu button and logo,
    so page content always starts below it instead of under floating buttons.
    Attributes (class, x-data…) are passed through to <main>.
--}}
@props(['title' => null])

@php
    $isTeacher = auth()->user()?->isTeacher();
    $homeRoute = $isTeacher ? route('teacher.dashboard') : route('home');
@endphp

<x-layout :title="$title">
    <div class="min-h-dvh bg-[#F9FAFB] lg:flex">
        @if($isTeacher)
            <x-teacher-sidebar/>
        @else
            <x-sidebar/>
        @endif

        <div class="flex-1 min-w-0 flex flex-col">
            {{-- No backdrop-filter here: it would trap the logout modal (position: fixed) inside the bar --}}
            <header class="sticky top-0 z-30 flex items-center gap-3 h-16 px-4 sm:px-6 bg-[#F9FAFB]/95 border-b border-slate-200/70
                           lg:static lg:h-20 lg:px-10 lg:bg-transparent lg:border-0">
                <button type="button"
                        data-sidebar-open
                        class="lg:hidden -ml-1 w-11 h-11 rounded-xl flex items-center justify-center text-slate-700 hover:bg-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-indigo-500 transition-colors"
                        aria-label="Open navigation"
                        aria-controls="app-sidebar"
                        aria-expanded="false">
                    <span class="material-symbols-rounded text-[26px]" aria-hidden="true">menu</span>
                </button>

                <a href="{{ $homeRoute }}" class="lg:hidden flex items-center gap-1.5 font-black text-xl tracking-tight text-black">
                    <x-logo class="w-9 h-9 !mx-0" />
                    <span>QuizGo</span>
                </a>

                <div class="ml-auto">
                    <x-dropdown-profile/>
                </div>
            </header>

            <main {{ $attributes->merge(['class' => 'flex-1 w-full min-w-0 px-4 sm:px-6 lg:px-10 pt-6 lg:pt-0 pb-16']) }}>
                {{ $slot }}
            </main>
        </div>
    </div>
</x-layout>
