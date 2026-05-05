<x-layout>
    {{--
        [ SECTION 01 ] HERO : SOLID BLUE BLOCK
    --}}
    <section id="hero-section" class="relative overflow-hidden bg-blue-500 pt-10 pb-44 flex flex-col">
        <!-- Flat Geometric Background Decoration (No Blur, No Gradients) -->
        <div class="absolute top-0 right-0 -mr-32 -mt-32 w-96 h-96 rounded-full bg-white/10 pointer-events-none"></div>
        <div class="absolute bottom-0 left-10 -mb-20 w-64 h-64 bg-white/5 transform rotate-45 pointer-events-none"></div>

        <x-navbar class="relative z-10 text-white" />

        <div class="hero-content relative z-10 flex flex-col justify-center items-center text-center px-4 mt-28">
            <span class="bg-amber-500 text-gray-900 px-5 py-2 rounded-full text-sm font-semibold uppercase tracking-wider mb-8">
                Study Smarter, Not Harder
            </span>

            <h1 class="text-7xl md:text-9xl font-extrabold text-white mb-6 leading-none tracking-tight">
                ADDICTIVE<br>
                LEARNING
            </h1>

            <p class="text-xl md:text-2xl text-blue-50 mb-10 max-w-lg font-normal">
                Gamified flashcards designed to lock information in your brain.
            </p>

            <div class="flex gap-4">
                <a href="/signup" class="flex items-center justify-center h-16 px-10 rounded-lg bg-white text-blue-600 font-bold text-lg hover:bg-gray-50 hover:scale-105 transition-all duration-200">
                    Start Learning
                </a>
            </div>
        </div>
    </section>

    {{--
        [ SECTION 02 ] MOCKUP : GRAY BLOCK WITH WHITE CONTAINER
    --}}
    <section id="mockup-section" class="relative bg-gray-100 z-20 pb-32 pt-16">
        <!-- Pulled up into the hero section using negative margin, establishing hierarchy without shadows -->
        <div class="max-w-5xl mx-auto px-4 -mt-32 relative z-20">
            <div class="bg-white rounded-lg p-8 md:p-16 flex flex-col items-center justify-center relative overflow-hidden">

                <!-- Header -->
                <header class="flex items-center justify-between w-full max-w-md mb-16">
                    <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center cursor-pointer hover:bg-gray-200 hover:scale-105 transition-all duration-200">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-gray-900">
                            <path d="M19 12H5M12 19l-7-7 7-7"/>
                        </svg>
                    </div>

                    <div class="flex flex-col items-center flex-1 px-6">
                        <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                            <div class="h-full bg-blue-500 rounded-full" style="width: 23.8%;"></div>
                        </div>
                    </div>

                    <div class="w-12 text-right font-bold text-gray-400 text-sm tracking-wider">5/21</div>
                </header>

                <!-- Flashcard -->
                <div class="relative w-full max-w-md aspect-[4/3] mb-16 group cursor-pointer transition-all duration-300 hover:scale-[1.02]">
                    <!-- Single flat colored background representing the card -->
                    <div class="absolute inset-0 bg-blue-50 rounded-lg flex items-center justify-center p-8 text-center transition-colors duration-200 group-hover:bg-blue-100">
                        <h3 class="text-3xl md:text-4xl font-bold text-gray-900 leading-tight">What is Laravel?</h3>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4 w-full max-w-md">
                    <!-- Secondary Muted Button -->
                    <button class="group w-full sm:w-auto h-14 px-10 bg-gray-100 text-gray-900 rounded-lg font-semibold flex items-center justify-center space-x-3 hover:bg-gray-200 hover:scale-105 transition-all duration-200">
                        <svg class="group-hover:rotate-180 transition-transform duration-300" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path d="M3.51 9a9 9 0 0114.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0020.49 15"/>
                        </svg>
                        <span class="tracking-wide">FLIP CARD</span>
                    </button>

                    <!-- Primary Action Button -->
                    <button class="group w-full sm:w-auto h-14 px-12 bg-blue-500 text-white rounded-lg font-semibold flex items-center justify-center space-x-3 hover:bg-blue-600 hover:scale-105 transition-all duration-200">
                        <span class="tracking-wide">NEXT</span>
                        <svg class="group-hover:translate-x-1 transition-transform duration-200" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path d="M5 12h14M12 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>

            </div>
        </div>
    </section>

   {{--
        [ SECTION 03 ] FEATURES : SOLID EMERALD BLOCK
    --}}
    <section id="features" class="bg-emerald-500 py-32 px-6 md:px-16 relative overflow-hidden">
        <!-- Subtle flat directional background overlay -->
        <div class="absolute bottom-0 left-0 w-full h-1/2 bg-gradient-to-t from-black/5 to-transparent pointer-events-none"></div>

        <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center relative z-10 gap-16">
            <div class="md:w-3/5">
                <div class="inline-block bg-white text-emerald-600 px-4 py-1.5 rounded-md mb-8 font-bold uppercase text-sm tracking-wider">
                    Level Up Your Brain
                </div>

                <h2 class="text-6xl md:text-8xl font-extrabold text-white tracking-tight leading-none mb-10">
                    Master any <br>subject, <span class="text-emerald-900">instantly.</span>
                </h2>

                <p class="text-white/90 text-xl md:text-2xl mb-12 leading-snug font-medium max-w-xl">
                    "QuizGo transforms boring study sessions into high-octane learning sprints."
                </p>

                <a href="/signup" class="group inline-flex items-center justify-center h-16 px-10 text-lg font-bold text-white bg-gray-900 rounded-lg transition-all duration-200 hover:scale-105 hover:bg-black">
                    <span>Start Learning Now</span>
                    <svg class="ml-3 w-5 h-5 group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path d="M5 12h14M12 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>

        <x-footer/>
    </section>
</x-layout>
