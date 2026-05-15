{{-- TEACHER SIDEBAR --}}
<aside class="sticky top-0 h-screen w-20 md:w-64 bg-white border-r border-slate-100 flex flex-col z-30">
    {{-- Logo Section --}}
    <div class="p-6 md:p-8 mb-4 flex items-center justify-center md:justify-start">
        <a href="{{ route('teacher.dashboard') }}" class="flex items-center gap-3 group">
            <div class="w-10 h-10 bg-indigo-500 rounded-xl flex items-center justify-center shadow-[0_4px_0_0_#4338ca] group-hover:scale-105 transition-transform">
                <div class="w-4 h-4 bg-white rounded-sm rotate-45"></div>
            </div>
            <span class="hidden md:block text-slate-900 font-black text-2xl tracking-tighter">QuizGo</span>
        </a>
    </div>

    {{-- Teacher Navigation --}}
    <nav class="flex-1 px-3 md:px-4 space-y-3 flex flex-col">
        {{-- Dashboard (Indigo) --}}
        <x-nav-link route="teacher.dashboard" icon="dashboard" label="Dashboard" color="indigo" />

        {{-- Assign Quiz (Emerald) --}}
        <x-nav-link route="teacher.quiz.index" icon="quiz" label="Assign Quiz" color="emerald" />

        {{-- Profile (Rose) --}}
        <x-nav-link route="teacher.profile" icon="account_circle" label="Profile" color="rose" />

        {{-- Divider for Classrooms --}}
        <div class="hidden md:block pt-8 px-3 mb-4">
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
