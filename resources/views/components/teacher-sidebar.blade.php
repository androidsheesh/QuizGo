{{--
    [ TEACHER SIDEBAR ]
--}}
<aside class="w-20 md:w-64 bg-white border-r border-gray-200 flex flex-col z-30">
    <div class="p-8 mb-4">
        <a href="{{ route('teacher.dashboard') }}" class="logo text-black font-black text-3xl tracking-tight flex items-center gap-2">QuizGo</a>
        <div class="md:hidden w-10 h-10 bg-slate-900 rounded-xl flex items-center justify-center mx-auto">
            <div class="w-4 h-4 bg-emerald-400 rounded-sm"></div>
        </div>
    </div>

    <nav class="flex-1 px-4 space-y-2">
        {{-- DASHBOARD --}}
        <a href="{{ route('teacher.dashboard') }}"
           class="flex items-center space-x-3 p-3 rounded-2xl transition-all
           {{ request()->routeIs('teacher.dashboard') || request()->routeIs('teacher.classroom.*') ? 'bg-slate-50 border border-slate-100 text-slate-900' : 'text-slate-400 hover:bg-slate-50 hover:text-slate-600' }}">
            <span class="text-xl">🖥️</span>
            <span class="hidden md:block font-semibold">Dashboard</span>
        </a>

        {{-- ASSIGN QUIZ --}}
        <a href="{{ route('teacher.quiz.index') }}"
           class="flex items-center space-x-3 p-3 rounded-2xl transition-all
           {{ request()->routeIs('teacher.quiz.*') ? 'bg-slate-50 border border-slate-100 text-slate-900' : 'text-slate-400 hover:bg-slate-50 hover:text-slate-600' }}">
            <span class="text-xl">📂</span>
            <span class="hidden md:block font-semibold">Assign Quiz</span>
        </a>

        {{-- PROFILE --}}
        <a href="{{ route('teacher.profile') }}"
           class="flex items-center space-x-3 p-3 rounded-2xl transition-all
           {{ request()->routeIs('teacher.profile') ? 'bg-slate-50 border border-slate-100 text-slate-900' : 'text-slate-400 hover:bg-slate-50 hover:text-slate-600' }}">
            <span class="text-xl">👤</span>
            <span class="hidden md:block font-semibold">Profile</span>
        </a>
    </nav>
</aside>
