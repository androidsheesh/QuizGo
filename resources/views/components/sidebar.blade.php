{{-- STUDENT SIDEBAR --}}
<div
    id="app-sidebar-backdrop"
    class="lg:hidden fixed inset-0 bg-slate-900/40 opacity-0 pointer-events-none transition-opacity duration-300 ease-in-out z-40"
    aria-hidden="true"
></div>

<aside
    id="app-sidebar"
    aria-label="Main navigation"
    class="fixed lg:sticky top-0 left-0 h-dvh w-[min(18rem,85vw)] lg:w-64 shrink-0 bg-white border-r border-slate-100 flex flex-col z-50 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out"
>
    {{-- Logo Section inside the sidebar --}}
    <div class="h-16 lg:h-20 px-4 lg:px-5 mb-2 flex items-center justify-between lg:justify-start shrink-0">

        <a href="{{ route('home') }}" class="logo flex items-center gap-1.5 text-black font-black text-2xl tracking-tight">
            <x-logo class="w-10 h-10 !mx-0" />
            <span>QuizGo</span>
        </a>

        <button
            type="button"
            data-sidebar-close
            class="lg:hidden w-11 h-11 rounded-xl border border-slate-200 flex items-center justify-center text-slate-500 hover:bg-slate-50 hover:text-slate-800 transition-colors"
            aria-label="Close navigation"
        >
            <span class="material-symbols-rounded text-2xl">close</span>
        </button>
    </div>

    {{-- Navigation links --}}
    <nav class="flex-1 px-4 space-y-3 overflow-y-auto overflow-x-hidden pb-6 custom-sidebar-scroll">
        <x-nav-link route="home" icon="home" label="Home" color="indigo" />
        <x-nav-link route="mydecks" icon="style" label="My Decks" color="emerald" />
        <x-nav-link route="student.assignments" icon="local_fire_department" label="Assignments" color="orange" />
        <x-nav-link route="myprofile" icon="face" label="Profile" color="rose" />

        {{-- Divider & Recent Decks --}}
        <div class="pt-8 px-3">
            <p class="text-[11px] font-black text-slate-900 uppercase tracking-widest mb-4 px-2">
                Recent Decks
            </p>
            <div class="space-y-1 h-[200px] overflow-y-auto overflow-x-hidden px-2 custom-sidebar-scroll">
                @forelse($recentDecks as $deck)
                    <a href="{{ route('decks.show', $deck->id) }}"
                    class="flex items-center gap-3 p-2 rounded-lg hover:bg-slate-50 group transition-all duration-200">
                        <div class="w-1.5 h-1.5 rounded-full bg-slate-300 group-hover:bg-indigo-500 transition-colors flex-shrink-0"></div>
                        <span class="text-sm font-medium text-slate-600 group-hover:text-slate-900 truncate">
                            {{ $deck->title }}
                        </span>
                    </a>
                @empty
                    <p class="text-xs text-slate-400 italic px-2">No decks yet...</p>
                @endforelse
            </div>
        </div>
    </nav>
</aside>

