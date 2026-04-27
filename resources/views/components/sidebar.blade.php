{{--
    [ SIDEBAR ]
    Minimalist & Logic-ready
--}}
<aside class="w-20 md:w-64 bg-white border-r border-gray-200 flex flex-col z-30">
    <div class="p-8 mb-4">
        <a href="{{ route('home') }}" class="logo text-black font-black text-3xl tracking-tight flex items-center gap-2">QuizGo</a>
        <div class="md:hidden w-10 h-10 bg-slate-900 rounded-xl flex items-center justify-center mx-auto">
            <div class="w-4 h-4 bg-emerald-400 rounded-sm"></div>
        </div>
    </div>

    <nav class="flex-1 px-4 space-y-2">
        {{-- HOME LINK --}}
        <a href="{{ route('home') }}"
           class="flex items-center space-x-3 p-3 rounded-2xl transition-all
           {{ request()->routeIs('home') ? 'bg-slate-50 border border-slate-100 text-slate-900' : 'text-slate-400 hover:bg-slate-50 hover:text-slate-600' }}">
            <span class="text-xl">🖥️</span>
            <span class="hidden md:block font-semibold">Home</span>
        </a>

        {{-- MY DECKS LINK --}}
        <a href="{{ route('mydecks') }}"
           class="flex items-center space-x-3 p-3 rounded-2xl transition-all
           {{ request()->routeIs('mydecks') ? 'bg-slate-50 border border-slate-100 text-slate-900' : 'text-slate-400 hover:bg-slate-50 hover:text-slate-600' }}">
            <span class="text-xl">📂</span>
            <span class="hidden md:block font-semibold">My Decks</span>
        </a>

        {{-- PROFILE LINK --}}
        <a href="{{ route('myprofile') }}"
           class="flex items-center space-x-3 p-3 rounded-2xl transition-all
           {{ request()->routeIs('profile') ? 'bg-slate-50 border border-slate-100 text-slate-900' : 'text-slate-400 hover:bg-slate-50 hover:text-slate-600' }}">
            <span class="text-xl">👤</span>
            <span class="hidden md:block font-semibold">Profile</span>
        </a>

        {{-- DIVIDER & RECENT DECKS (Scrollable) --}}
        <div class="hidden md:block pt-6 pb-2">
            <p class="text-[10px] font-bold text-slate-300 uppercase tracking-widest px-3 mb-3">Recent Decks</p>
            
            <div class="max-h-48 overflow-y-auto space-y-1 block 
                        [&::-webkit-scrollbar]:w-1.5
                        [&::-webkit-scrollbar-track]:bg-transparent
                        [&::-webkit-scrollbar-thumb]:bg-slate-200
                        [&::-webkit-scrollbar-thumb]:rounded-full
                        hover:[&::-webkit-scrollbar-thumb]:bg-slate-300">
                
                {{-- Static Deck 1 --}}
                <a href="#" class="flex items-center space-x-3 p-2.5 rounded-xl transition-all text-slate-500 hover:bg-slate-50 hover:text-slate-700 group">
                    <span class="w-2 h-2 rounded-full bg-red-400 group-hover:scale-125 transition-transform"></span>
                    <span class="font-medium text-sm truncate">Laravel Framework</span>
                </a>
                
                {{-- Static Deck 2 --}}
                <a href="#" class="flex items-center space-x-3 p-2.5 rounded-xl transition-all text-slate-500 hover:bg-slate-50 hover:text-slate-700 group">
                    <span class="w-2 h-2 rounded-full bg-blue-400 group-hover:scale-125 transition-transform"></span>
                    <span class="font-medium text-sm truncate">UI/UX Design</span>
                </a>

                {{-- Static Deck 3 --}}
                <a href="#" class="flex items-center space-x-3 p-2.5 rounded-xl transition-all text-slate-500 hover:bg-slate-50 hover:text-slate-700 group">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 group-hover:scale-125 transition-transform"></span>
                    <span class="font-medium text-sm truncate">React Basics</span>
                </a>

                {{-- Static Deck 4 --}}
                <a href="#" class="flex items-center space-x-3 p-2.5 rounded-xl transition-all text-slate-500 hover:bg-slate-50 hover:text-slate-700 group">
                    <span class="w-2 h-2 rounded-full bg-yellow-400 group-hover:scale-125 transition-transform"></span>
                    <span class="font-medium text-sm truncate">Untitled Deck</span>
                </a>

                {{-- Static Deck 5 --}}
                <a href="#" class="flex items-center space-x-3 p-2.5 rounded-xl transition-all text-slate-500 hover:bg-slate-50 hover:text-slate-700 group">
                    <span class="w-2 h-2 rounded-full bg-purple-400 group-hover:scale-125 transition-transform"></span>
                    <span class="font-medium text-sm truncate">Advanced PHP</span>
                </a>
            </div>
        </div>
    </nav>

    {{-- SETTINGS (Fixed to bottom) --}}
    <div class="p-4 mt-auto hidden md:block border-t border-gray-100">
        <a href="#" class="flex items-center space-x-3 p-3 rounded-2xl transition-all text-slate-400 hover:bg-slate-50 hover:text-slate-600">
            <span class="text-xl">⚙️</span>
            <span class="font-semibold text-sm">Settings</span>
        </a>
    </div>


</aside>
