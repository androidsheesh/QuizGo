{{-- TEACHER SIDEBAR --}}
<div class="lg:hidden fixed top-6 left-10 z-40">
    <button
        type="button"
        id="mobile-teacher-sidebar-open"
        class="w-10 h-10 rounded-xl border border-slate-200 bg-white flex items-center justify-center text-slate-700 shadow-sm hover:bg-slate-50 transition-colors"
        aria-label="Open sidebar"
        aria-controls="mobile-sidebar"
        aria-expanded="false"
    >
        <span class="material-symbols-rounded text-2xl">menu</span>
    </button>
</div>

<div
    id="mobile-teacher-sidebar-backdrop"
    class="lg:hidden fixed inset-0 bg-slate-900/40 opacity-0 pointer-events-none transition-opacity duration-300 ease-in-out z-40"
    aria-hidden="true"
></div>

<aside
    id="mobile-teacher-sidebar"
    class="fixed lg:sticky top-0 left-0 h-screen w-72 lg:w-64 bg-white border-r border-slate-100 flex flex-col z-50 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out"
>
{{-- Logo Section --}}
    <div class="p-4 lg:p-5 mb-4 flex items-center justify-between lg:justify-start">
        <a href="{{ route('teacher.dashboard') }}" class="logo relative bg-transparent text-black font-black text-2xl tracking-tight flex items-center z-[60]">
            <x-logo class="w-20 h-20 md:w-30 md:h-30 -my-6 md:-my-12" />
            <h1 class="-ml-2 md:-ml-4">QuizGo</h1>
        </a>

        <button
            type="button"
            id="mobile-teacher-sidebar-close"
            class="lg:hidden w-10 h-10 rounded-xl border border-slate-200 flex items-center justify-center text-slate-500 hover:bg-slate-50 hover:text-slate-800 transition-colors"
            aria-label="Close sidebar"
        >
            <span class="material-symbols-rounded text-2xl">close</span>
        </button>
    </div>

    {{-- Teacher Navigation --}}
    <nav class="flex-1 px-4 space-y-3 flex flex-col overflow-y-auto overflow-x-hidden pb-6 custom-sidebar-scroll">
        {{-- Dashboard (Indigo) --}}
        <x-nav-link route="teacher.dashboard" icon="dashboard" label="Dashboard" color="indigo" />

        {{-- Assign Quiz (Emerald) --}}
        <x-nav-link route="teacher.quiz.index" icon="quiz" label="Assign Quiz" color="emerald" />

        {{-- Profile (Rose) --}}
        <x-nav-link route="teacher.profile" icon="account_circle" label="Profile" color="rose" />

        {{-- Divider for Classrooms --}}
        <div class="pt-8 px-3 mb-4">
            <p class="text-[10px] font-black text-slate-900 uppercase tracking-widest mb-4 px-2">
                Your Classes
            </p>
            <div class="space-y-1 h-[200px] overflow-y-auto overflow-x-hidden px-2 custom-sidebar-scroll">

                @php
                    $sidebarClassrooms = \App\Models\Classroom::where('teacher_id', auth()->id())->get();
                @endphp

                @forelse($sidebarClassrooms as $classroom)
                    <a href="{{ route('teacher.classroom.show', $classroom->id) }}"
                       class="flex items-center gap-3 p-2 rounded-lg hover:bg-slate-50 group transition-all duration-200">
                        <div class="w-1.5 h-1.5 rounded-full bg-slate-300 group-hover:bg-emerald-500 transition-colors flex-shrink-0"></div>
                        <span class="text-sm font-medium text-slate-600 group-hover:text-slate-900 truncate">
                            {{ $classroom->name }}
                        </span>
                    </a>
                @empty
                    <p class="text-xs text-slate-400 italic px-2">No classes yet...</p>
                @endforelse

            </div>
        </div>
    </nav>
</aside>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const sidebar = document.getElementById('mobile-teacher-sidebar');
        const backdrop = document.getElementById('mobile-teacher-sidebar-backdrop');
        const openButton = document.getElementById('mobile-teacher-sidebar-open');
        const closeButton = document.getElementById('mobile-teacher-sidebar-close');

        if (!sidebar || !backdrop || !openButton || !closeButton) {
            return;
        }

        const openSidebar = function () {
            sidebar.classList.remove('-translate-x-full');
            backdrop.classList.remove('opacity-0', 'pointer-events-none');
            backdrop.classList.add('opacity-100');
            openButton.setAttribute('aria-expanded', 'true');
            document.body.classList.add('overflow-hidden', 'lg:overflow-auto');
        };

        const closeSidebar = function () {
            sidebar.classList.add('-translate-x-full');
            backdrop.classList.add('opacity-0', 'pointer-events-none');
            backdrop.classList.remove('opacity-100');
            openButton.setAttribute('aria-expanded', 'false');
            document.body.classList.remove('overflow-hidden', 'lg:overflow-auto');
        };

        openButton.addEventListener('click', openSidebar);
        closeButton.addEventListener('click', closeSidebar);
        backdrop.addEventListener('click', closeSidebar);

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeSidebar();
            }
        });

        window.addEventListener('resize', function () {
            if (window.innerWidth >= 1024) {
                closeSidebar();
            }
        });
    });
</script>
