<aside class="sticky top-0 h-screen w-20 md:w-64 bg-white border-r border-slate-100 flex flex-col z-30">
    {{-- Logo Section --}}
    <div class="p-6 md:p-8 mb-4 flex items-center justify-center md:justify-start">
        <a href="{{ route('home') }}" class="flex items-center gap-3 group">
            <div class="w-10 h-10 bg-indigo-500 rounded-xl flex items-center justify-center shadow-[0_4px_0_0_#4338ca] group-hover:scale-105 transition-transform">
                <div class="w-4 h-4 bg-white rounded-sm rotate-45"></div>
            </div>
            <span class="hidden md:block text-slate-900 font-black text-2xl tracking-tighter">QuizGo</span>
        </a>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 px-3 md:px-4 space-y-3">
        <x-nav-link route="home" icon="home" label="Home" color="indigo" />
        <x-nav-link route="mydecks" icon="style" label="My Decks" color="emerald" />
        <x-nav-link route="student.assignments" icon="local_fire_department" label="Assignments" color="orange" />
        <x-nav-link route="myprofile" icon="face" label="Profile" color="rose" />

        {{-- Divider & Recent Decks --}}
        <div class="hidden md:block pt-8 px-3">
            <p class="text-[10px] font-black text-slate-900 uppercase tracking-widest mb-4 px-2">
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
