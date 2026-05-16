{{-- STUDENT SIDEBAR --}}
<div class="lg:hidden fixed top-6 left-10 z-40">
    <button
        type="button"
        id="mobile-sidebar-open"
        class="w-10 h-10 rounded-xl border border-slate-200 bg-white flex items-center justify-center text-slate-700 shadow-sm hover:bg-slate-50 transition-colors"
        aria-label="Open sidebar"
        aria-controls="mobile-sidebar"
        aria-expanded="false"
    >
        <span class="material-symbols-rounded text-2xl">menu</span>
    </button>
</div>

<div
    id="mobile-sidebar-backdrop"
    class="lg:hidden fixed inset-0 bg-slate-900/40 opacity-0 pointer-events-none transition-opacity duration-300 ease-in-out z-40"
    aria-hidden="true"
></div>

<aside
    id="mobile-sidebar"
    class="fixed lg:sticky top-0 left-0 h-screen w-72 lg:w-64 bg-white border-r border-slate-100 flex flex-col z-50 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out"
>
    {{-- Logo Section inside the sidebar --}}
    <div class="p-6 lg:p-8 mb-4 flex items-center justify-between lg:justify-start">
        <a href="{{ route('home') }}" class="flex items-center gap-3 group">
            <div class="w-10 h-10 bg-indigo-500 rounded-xl flex items-center justify-center shadow-[0_4px_0_0_#4338ca] group-hover:scale-105 transition-transform">
                <div class="w-5 h-5 bg-white rounded-sm rotate-45"></div>
            </div>
            <span class="block text-slate-900 font-black text-2xl tracking-tighter">QuizGo</span>
        </a>

        <button
            type="button"
            id="mobile-sidebar-close"
            class="lg:hidden w-10 h-10 rounded-xl border border-slate-200 flex items-center justify-center text-slate-500 hover:bg-slate-50 hover:text-slate-800 transition-colors"
            aria-label="Close sidebar"
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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const sidebar = document.getElementById('mobile-sidebar');
        const backdrop = document.getElementById('mobile-sidebar-backdrop');
        const openButton = document.getElementById('mobile-sidebar-open');
        const closeButton = document.getElementById('mobile-sidebar-close');

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
