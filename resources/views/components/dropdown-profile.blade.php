{{-- Profile Dropdown + Logout Confirmation Modal --}}
<div class="fixed top-6 right-6 md:right-12 z-50"
     x-data="{ open: false, showLogout: false }">

    {{-- ── Avatar / trigger pill ── --}}
    <div @click="open = !open"
         class="flex items-center space-x-2 cursor-pointer group bg-white/80 backdrop-blur-md p-1.5 pr-3 rounded-full shadow-sm border border-gray-100 hover:shadow-md transition-all">
        <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center border border-emerald-200 group-hover:border-emerald-400 transition-colors overflow-hidden">
            @if(Auth::check() && Auth::user()->profile_picture)
                <img src="{{ asset('storage/' . Auth::user()->profile_picture) }}" alt="Avatar" class="w-full h-full object-cover">
            @else
                <span class="text-xs">👤</span>
            @endif
        </div>
        @if(Auth::check())
            <span class="hidden md:block text-sm font-semibold text-slate-700">{{ Auth::user()->firstname }}</span>
        @endif
        <span class="text-gray-400 text-xs transition-transform duration-200" :class="open ? 'rotate-180' : ''">⌄</span>
    </div>

    {{-- ── Dropdown panel ── --}}
    <div x-show="open"
         @click.away="open = false"
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         class="absolute right-0 mt-3 w-52 bg-white rounded-2xl shadow-xl py-2 border border-gray-100"
         style="display: none;">

        <div class="px-4 py-2 border-b border-gray-50 mb-1">
            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Account</p>
        </div>

        <a href="{{ Auth::check() && Auth::user()->isTeacher() ? route('teacher.profile') : '/myprofile' }}"
           class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-slate-700 hover:bg-emerald-50 hover:text-emerald-700 transition-colors rounded-xl mx-1">
            <span class="text-base">👤</span> Your Profile
        </a>

        {{-- Logout trigger — opens the modal, does NOT submit yet --}}
        <button type="button"
                @click="open = false; showLogout = true"
                class="w-full flex items-center gap-2.5 px-4 py-2.5 text-sm text-red-500 hover:bg-red-50 hover:text-red-600 transition-colors rounded-xl mx-1">
            <span class="text-base">🚪</span> Logout
        </button>
    </div>

    {{-- ══════════════════════════════════════════════════════
         LOGOUT CONFIRMATION MODAL
    ══════════════════════════════════════════════════════ --}}
    <div x-show="showLogout"
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[9999] flex items-center justify-center p-4"
         style="display: none;"
         aria-modal="true"
         role="dialog"
         aria-labelledby="logout-title">

        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" @click="showLogout = false"></div>

        {{-- Card --}}
        <div x-show="showLogout"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 translate-y-2"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-2"
             class="relative bg-white rounded-3xl shadow-2xl w-full max-w-sm mx-auto p-6 sm:p-8 text-center">

            {{-- Icon --}}
            <div class="w-16 h-16 bg-red-50 rounded-2xl flex items-center justify-center text-3xl mx-auto mb-5">
                🚪
            </div>

            {{-- Copy --}}
            <h2 id="logout-title" class="text-xl sm:text-2xl font-black text-slate-800 mb-2">Log out?</h2>
            <p class="text-slate-500 text-sm sm:text-base leading-relaxed mb-6 sm:mb-8">
                Are you sure you want to log out of your account?
            </p>

            {{-- Actions --}}
            <div class="flex flex-col sm:flex-row items-center gap-3">
                {{-- Cancel --}}
                <button type="button"
                        @click="showLogout = false"
                        class="w-full sm:flex-1 py-3 px-5 rounded-2xl border border-slate-200 text-slate-700 font-bold text-sm hover:bg-slate-50 transition-colors">
                    Cancel
                </button>

                {{-- Confirm logout (real form submit) --}}
                <form method="POST" action="{{ route('logout') }}" class="w-full sm:flex-1">
                    @csrf
                    <button type="submit"
                            class="w-full py-3 px-5 rounded-2xl bg-red-500 hover:bg-red-600 text-white font-black text-sm shadow-lg shadow-red-200 hover:shadow-red-300 transition-all active:scale-95">
                        Yes, Log Out
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
