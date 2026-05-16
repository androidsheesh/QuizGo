<nav id="navbar" class="fixed top-4 left-0 right-0 mx-auto w-[calc(100%-2rem)] max-w-7xl z-50 flex justify-between items-center px-6 lg:px-12 backdrop-blur-md bg-white/10 py-4 rounded-2xl border border-white/20 transition-all duration-300">

    <a href="/" class="logo relative text-black font-black text-3xl tracking-tight flex items-center gap-2 z-[60]">
        <div class="w-8 h-8 bg-indigo-500 rounded-xl flex items-center justify-center shadow-[0_4px_0_0_#4338ca] hover:scale-105 transition-transform">
            <div class="w-4 h-4 bg-white rounded-sm rotate-45"></div>
        </div>
        QuizGo
    </a>

    <div class="flex items-center z-[60]">

        <div class="hidden lg:flex items-center space-x-8">
            <a href="#features" class="text-black font-bold text-sm uppercase tracking-widest hover:underline decoration-4 underline-offset-8">About</a>
            <a href="/signin" class="text-black font-bold text-sm uppercase tracking-widest hover:underline decoration-4 underline-offset-8">Login</a>
            <a href="/signup" class="px-6 py-3 rounded-xl bg-black text-white font-bold shadow-[4px_4px_0px_0px_rgba(255,255,255,1)] hover:translate-x-[2px] hover:translate-y-[2px] hover:shadow-none transition-all">
                Start Learning
            </a>
        </div>

        <button id="mobile-menu-btn" class="lg:hidden relative flex flex-col justify-center items-center gap-1.5 w-8 h-8 focus:outline-none">
            <span class="block w-6 h-0.5 bg-black transition-all duration-300 ease-in-out origin-center"></span>
            <span class="block w-6 h-0.5 bg-black transition-all duration-300 ease-in-out"></span>
            <span class="block w-6 h-0.5 bg-black transition-all duration-300 ease-in-out origin-center"></span>
        </button>
    </div>

    <div id="mobile-menu" class="absolute top-[calc(100%+0.5rem)] right-0 lg:right-12 w-56 bg-white rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.12)] border border-gray-100 flex flex-col p-5 space-y-4 opacity-0 pointer-events-none transition-all duration-300 ease-in-out lg:hidden origin-top-right transform scale-95 z-50">
        <a href="#features" class="mobile-link text-black font-bold text-sm uppercase tracking-widest hover:text-indigo-600 transition-colors">About</a>
        <a href="/signin" class="mobile-link text-black font-bold text-sm uppercase tracking-widest hover:text-indigo-600 transition-colors">Login</a>
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
                // Show Dropdown (Fade and scale in)
                menu.classList.remove('opacity-0', 'pointer-events-none', 'scale-95');
                menu.classList.add('opacity-100', 'pointer-events-auto', 'scale-100');

                // Animate Hamburger to 'X'
                spans[0].classList.add('translate-y-2', 'rotate-45');
                spans[1].classList.add('opacity-0');
                spans[2].classList.add('-translate-y-2', '-rotate-45');
            } else {
                // Hide Dropdown (Fade and scale out)
                menu.classList.remove('opacity-100', 'pointer-events-auto', 'scale-100');
                menu.classList.add('opacity-0', 'pointer-events-none', 'scale-95');

                // Revert 'X' to Hamburger
                spans[0].classList.remove('translate-y-2', 'rotate-45');
                spans[1].classList.remove('opacity-0');
                spans[2].classList.remove('-translate-y-2', '-rotate-45');
            }
        }

        btn.addEventListener('click', (e) => {
            e.stopPropagation(); // Prevent document click from firing immediately
            toggleMenu();
        });

        links.forEach(link => {
            link.addEventListener('click', () => {
                if (isMenuOpen) toggleMenu();
            });
        });

        // Close menu when clicking anywhere outside of it
        document.addEventListener('click', (e) => {
            if (isMenuOpen && !menu.contains(e.target)) {
                toggleMenu();
            }
        });
    });
</script>
