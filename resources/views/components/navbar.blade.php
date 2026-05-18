<nav id="navbar" class="fixed top-4 left-0 right-0 mx-auto w-[calc(100%-2rem)] max-w-7xl z-50 flex justify-between items-center px-6 lg:px-12 backdrop-blur-md bg-white/10 py-4 rounded-2xl border border-white/20 transition-all duration-300">

    <!-- Logo Section: Much larger explicit size, heavier negative margins to prevent stretch -->
    <a href="/" class="logo relative bg-transparent text-black font-black text-2xl tracking-tight flex items-center gap-3 z-[60]">
        <x-logo class="w-24 h-24 md:w-32 md:h-32 -my-8 md:-my-12" />
        QuizGo
    </a>

    <div class="flex items-center z-[60]">

        <!-- Desktop Menu -->
        <div class="hidden lg:flex items-center space-x-8">
            <a href="#features" class="text-black font-bold text-sm uppercase tracking-widest hover:underline decoration-4 underline-offset-8 bg-transparent">About</a>
            <a href="/signin" class="text-black font-bold text-sm uppercase tracking-widest hover:underline decoration-4 underline-offset-8 bg-transparent">Login</a>
            <a href="/signup" class="px-6 py-3 rounded-xl bg-black text-white font-bold shadow-[4px_4px_0px_0px_rgba(255,255,255,1)] hover:translate-x-[2px] hover:translate-y-[2px] hover:shadow-none transition-all">
                Start Learning
            </a>
        </div>

        <!-- Mobile Menu Button -->
        <button id="mobile-menu-btn" class="lg:hidden relative flex flex-col justify-center items-center gap-1.5 w-8 h-8 focus:outline-none bg-transparent">
            <span class="block w-6 h-0.5 bg-black transition-all duration-300 ease-in-out origin-center"></span>
            <span class="block w-6 h-0.5 bg-black transition-all duration-300 ease-in-out"></span>
            <span class="block w-6 h-0.5 bg-black transition-all duration-300 ease-in-out origin-center"></span>
        </button>
    </div>

    <!-- Mobile Dropdown Menu -->
    <div id="mobile-menu" class="absolute top-[calc(100%+0.5rem)] right-0 lg:right-12 w-56 bg-white rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.12)] border border-gray-100 flex flex-col p-5 space-y-4 opacity-0 pointer-events-none transition-all duration-300 ease-in-out lg:hidden origin-top-right transform scale-95 z-50">
        <a href="#features" class="mobile-link text-black font-bold text-sm uppercase tracking-widest hover:text-indigo-600 transition-colors bg-transparent">About</a>
        <a href="/signin" class="mobile-link text-black font-bold text-sm uppercase tracking-widest hover:text-indigo-600 transition-colors bg-transparent">Login</a>
        <a href="/signup" class="mobile-link flex w-full justify-center px-4 py-3 mt-2 rounded-xl bg-black text-white font-bold text-sm shadow-[3px_3px_0px_0px_rgba(200,200,200,1)] hover:translate-x-[2px] hover:translate-y-[2px] hover:shadow-none transition-all border border-black">
            Start Learning
        </a>
    </div>
</nav>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const btn = document.getElementById('mobile-menu-btn');
        const menu = document.getElementById('mobile-menu');
        const spans = btn.querySelectorAll('span');
        const links = document.querySelectorAll('.mobile-link');
        let isMenuOpen = false;

        function toggleMenu() {
            isMenuOpen = !isMenuOpen;

            if (isMenuOpen) {
                menu.classList.remove('opacity-0', 'pointer-events-none', 'scale-95');
                menu.classList.add('opacity-100', 'pointer-events-auto', 'scale-100');

                spans[0].classList.add('translate-y-2', 'rotate-45');
                spans[1].classList.add('opacity-0');
                spans[2].classList.add('-translate-y-2', '-rotate-45');
            } else {
                menu.classList.remove('opacity-100', 'pointer-events-auto', 'scale-100');
                menu.classList.add('opacity-0', 'pointer-events-none', 'scale-95');

                spans[0].classList.remove('translate-y-2', 'rotate-45');
                spans[1].classList.remove('opacity-0');
                spans[2].classList.remove('-translate-y-2', '-rotate-45');
            }
        }

        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            toggleMenu();
        });

        links.forEach(link => {
            link.addEventListener('click', () => {
                if (isMenuOpen) toggleMenu();
            });
        });

        document.addEventListener('click', (e) => {
            if (isMenuOpen && !menu.contains(e.target)) {
                toggleMenu();
            }
        });
    });
</script>
