<div class="fixed top-6 right-6 md:right-12 z-50" x-data="{ open: false }">

    <div @click="open = !open"
         class="flex items-center space-x-2 cursor-pointer group bg-white/80 backdrop-blur-md p-1.5 pr-3 rounded-full shadow-sm border border-gray-100 hover:shadow-md transition-all">
        <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center border border-emerald-200 group-hover:border-emerald-400 transition-colors">
            <span class="text-xs">👤</span>
        </div>
        <span class="text-gray-400 text-xs transition-transform duration-200" :class="open ? 'rotate-180' : ''">⌄</span>
    </div>

    <div x-show="open"
         @click.away="open = false"
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         class="absolute right-0 mt-3 w-48 bg-white rounded-2xl shadow-xl py-2 border border-gray-100"
         style="display: none;">

        <div class="px-4 py-2 border-b border-gray-50 mb-1">
            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Account</p>
        </div>

        <a href="/teacher-profile" class="block px-4 py-2 text-sm text-slate-700 hover:bg-emerald-50 hover:text-emerald-700 transition-colors">Your Profile</a>
        <a href="#" class="block px-4 py-2 text-sm text-slate-700 hover:bg-emerald-50 hover:text-emerald-700 transition-colors">Settings</a>

        <div class="border-t border-gray-50 mt-1 pt-1">
            <form method="POST" action="logout">
                @csrf
                <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-red-600 hover:bg-red-50 font-semibold transition-colors">
                    Logout
                </button>
            </form>
        </div>
    </div>
</div>
