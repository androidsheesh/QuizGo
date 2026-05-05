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
    <nav class="flex-1 px-3 md:px-4 space-y-3">
        {{-- Dashboard (Indigo) --}}
        <x-nav-link
            route="teacher.dashboard"
            icon="dashboard"
            label="Dashboard"
            color="indigo"
        />

        {{-- Assign Quiz (Emerald) --}}
        <x-nav-link
            route="teacher.quiz.index"
            icon="quiz"
            label="Assign Quiz"
            color="emerald"
        />

        {{-- Profile (Rose) --}}
        <x-nav-link
            route="teacher.profile"
            icon="account_circle"
            label="Profile"
            color="rose"
        />

        {{-- Divider for Classrooms --}}
        <div class="hidden md:block pt-8 px-3">
            <p class="text-[10px] font-black text-slate-300 uppercase tracking-widest mb-4">Your Classes</p>
            <div class="space-y-3">
                <div class="flex items-center gap-2 group cursor-pointer">
                    <div class="w-1.5 h-1.5 rounded-full bg-slate-200 group-hover:bg-emerald-400 transition-all"></div>
                    <span class="text-xs font-bold text-slate-400 group-hover:text-slate-900 transition-colors">Section A - Math</span>
                </div>
            </div>
        </div>
    </nav>
</aside>
