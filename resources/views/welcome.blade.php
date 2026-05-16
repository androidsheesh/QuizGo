<!-- Add these to your layout <head> if they aren't already included -->
<!-- <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" /> -->
<!-- <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script> -->
<!-- <script src="https://unpkg.com/aos@next/dist/aos.js"></script> -->
<!-- <script>document.addEventListener('DOMContentLoaded', function() { AOS.init({ once: true }); });</script> -->

<x-layout>
    {{--
        [ SECTION 01 ] HERO : SOLID BLUE BLOCK
    --}}
    <section id="hero-section" class="relative overflow-hidden bg-blue-600 pt-10 pb-32 md:pb-44 flex flex-col">
        <!-- Floating Geometric Background Decoration -->
        <div class="absolute top-0 right-0 -mr-32 -mt-32 w-64 h-64 sm:w-96 sm:h-96 rounded-full bg-white/10 pointer-events-none animate-[pulse_4s_ease-in-out_infinite]"></div>
        <div class="absolute bottom-0 left-10 -mb-20 w-48 h-48 sm:w-64 sm:h-64 bg-white/5 transform rotate-45 pointer-events-none animate-[bounce_6s_ease-in-out_infinite]"></div>

        <x-navbar class="relative z-10 text-white" />

        <div class="hero-content relative z-10 flex flex-col justify-center items-center text-center px-4 sm:px-6 mt-16 md:mt-28">
            <span data-aos="fade-down" class="bg-amber-400 text-gray-900 px-4 py-1.5 sm:px-5 sm:py-2 rounded-full text-xs sm:text-sm font-bold uppercase tracking-wider mb-6 sm:mb-8 inline-block shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] border-2 border-black animate-bounce">
                🎓 Study Smarter, Not Harder
            </span>

            <h1 data-aos="zoom-in" data-aos-delay="100" class="text-5xl sm:text-7xl md:text-8xl lg:text-9xl font-black text-white mb-6 leading-none tracking-tight break-words max-w-full drop-shadow-[0_5px_0_rgba(29,78,216,1)]">
                ADDICTIVE<br>
                LEARNING
            </h1>

            <p data-aos="fade-up" data-aos-delay="200" class="text-lg sm:text-xl md:text-2xl text-blue-100 mb-8 sm:mb-10 max-w-md md:max-w-lg font-medium">
                Gamified flashcards designed to lock information in your brain. 🧠⚡
            </p>

            <div data-aos="fade-up" data-aos-delay="300" class="flex gap-4 w-full sm:w-auto px-4 sm:px-0 justify-center">
                <a href="/signup" class="flex items-center justify-center w-full sm:w-auto h-14 sm:h-16 px-8 sm:px-10 rounded-xl bg-amber-400 text-gray-900 font-black text-base sm:text-lg border-4 border-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-1 hover:translate-y-1 transition-all duration-150">
                    Start Learning
                </a>
            </div>
        </div>
    </section>

    {{--
        [ SECTION 02 ] MOCKUP : INTERACTIVE FLASHCARD
    --}}
    <section id="mockup-section" class="relative bg-amber-50 z-20 pb-16 md:pb-32 pt-8 md:pt-16 px-4" x-data="{ isFlipped: false }">
        <!-- Main Mockup Container -->
        <div data-aos="fade-up" data-aos-delay="400" class="max-w-5xl mx-auto -mt-20 md:-mt-32 relative z-20 w-full">
            <div class="bg-white rounded-2xl p-6 sm:p-8 md:p-16 flex flex-col items-center justify-center relative overflow-hidden border-4 border-black shadow-[8px_8px_0px_0px_rgba(0,0,0,1)]">

                <!-- Header Trackers -->
                <header class="flex items-center justify-between w-full max-w-md mb-8 sm:mb-12 gap-3">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-gray-100 border-2 border-black flex items-center justify-center flex-shrink-0 cursor-pointer hover:bg-gray-200 active:scale-95 transition-all shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-0.5 hover:translate-y-0.5">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="text-gray-900">
                            <path d="M19 12H5M12 19l-7-7 7-7"/>
                        </svg>
                    </div>

                    <div class="flex flex-col items-center flex-1 min-w-0">
                        <div class="w-full h-4 bg-gray-100 rounded-full border-2 border-black overflow-hidden p-0.5">
                            <div class="h-full bg-blue-500 rounded-full transition-all duration-500 ease-out" style="width: 35%;"></div>
                        </div>
                    </div>

                    <div class="w-12 text-right font-black text-gray-700 text-xs sm:text-sm tracking-wider flex-shrink-0">5/21</div>
                </header>

                <!-- 3D Flashcard Wrapper -->
                <div class="w-full max-w-md aspect-[4/3] mb-8 sm:mb-12 perspective-1000 group cursor-pointer" @click="isFlipped = !isFlipped">
                    <div class="relative w-full h-full duration-500 transform-style-3d select-none" :class="isFlipped ? 'rotate-y-180' : ''">

                        <!-- Card Front -->
                        <div class="absolute inset-0 backface-hidden bg-blue-50 border-4 border-black rounded-2xl flex flex-col items-center justify-center p-6 sm:p-8 text-center shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] group-hover:shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] group-hover:-translate-y-1 transition-all duration-200">
                            <span class="text-xs font-bold text-blue-600 uppercase tracking-widest mb-2 bg-blue-100 px-2.5 py-1 rounded-md border border-blue-300">Question</span>
                            <h3 class="text-2xl sm:text-3xl md:text-4xl font-black text-gray-900 leading-tight">What is Laravel?</h3>
                            <p class="text-xs font-bold text-gray-400 mt-6 animate-pulse">👉 Tap to reveal answer</p>
                        </div>

                        <!-- Card Back -->
                        <div class="absolute inset-0 backface-hidden rotate-y-180 bg-emerald-50 border-4 border-black rounded-2xl flex flex-col items-center justify-center p-6 sm:p-8 text-center shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
                            <span class="text-xs font-bold text-emerald-700 uppercase tracking-widest mb-2 bg-emerald-100 px-2.5 py-1 rounded-md border border-emerald-300">Answer</span>
                            <h3 class="text-xl sm:text-2xl font-bold text-gray-900 leading-snug">A playful and elegant PHP web framework built for artisan web developers.</h3>
                        </div>

                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4 w-full max-w-md">
                    <!-- Flip Trigger Button -->
                    <button @click="isFlipped = !isFlipped" class="group w-full sm:w-1/2 h-14 px-6 bg-white border-2 border-black text-gray-900 rounded-xl font-bold flex items-center justify-center space-x-3 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-1 hover:translate-y-1 transition-all duration-150">
                        <svg class="group-hover:rotate-180 transition-transform duration-500 flex-shrink-0" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                            <path d="M3.51 9a9 9 0 0114.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0020.49 15"/>
                        </svg>
                        <span class="tracking-wide text-sm sm:text-base font-black">FLIP CARD</span>
                    </button>

                    <!-- Next Button -->
                    <button @click="isFlipped = false" class="group w-full sm:w-1/2 h-14 px-6 bg-blue-500 border-2 border-black text-white rounded-xl font-bold flex items-center justify-center space-x-3 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-1 hover:translate-y-1 transition-all duration-150">
                        <span class="tracking-wide text-sm sm:text-base font-black">NEXT CARD</span>
                        <svg class="group-hover:translate-x-1 transition-transform duration-200 flex-shrink-0" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
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
    <section id="features" class="bg-emerald-600 py-20 md:py-32 px-4 sm:px-8 md:px-16 relative overflow-hidden">
        <div class="absolute bottom-0 left-0 w-full h-1/2 bg-gradient-to-t from-black/10 to-transparent pointer-events-none"></div>

        <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-start md:items-center relative z-10 gap-12 md:gap-16">
            <div class="w-full md:w-3/5">
                <div data-aos="fade-right" class="inline-block bg-amber-400 text-gray-900 border-2 border-black px-4 py-1.5 rounded-md mb-6 md:mb-8 font-black uppercase text-xs sm:text-sm tracking-wider shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                    ⚡ Level Up Your Brain
                </div>

                <h2 data-aos="fade-right" data-aos-delay="100" class="text-4xl sm:text-6xl md:text-7xl lg:text-8xl font-black text-white tracking-tight leading-none mb-6 md:mb-10 break-words drop-shadow-[0_4px_0_rgba(16,185,129,1)]">
                    Master any <br class="hidden sm:inline">subject, <span class="relative inline-block text-amber-300 underline decoration-wavy decoration-black">instantly.</span>
                </h2>

                <p data-aos="fade-right" data-aos-delay="200" class="text-white/90 text-lg sm:text-xl md:text-2xl mb-8 md:mb-12 leading-snug font-medium max-w-xl">
                    "QuizGo transforms boring study sessions into high-octane learning sprints." 🔥
                </p>

                <a data-aos="zoom-in" data-aos-delay="300" href="/signup" class="group inline-flex items-center justify-center w-full sm:w-auto h-14 sm:h-16 px-8 sm:px-10 text-base sm:text-lg font-black text-white bg-gray-900 border-4 border-white rounded-xl transition-all duration-150 shadow-[4px_4px_0px_0px_rgba(255,255,255,1)] hover:shadow-none hover:translate-x-1 hover:translate-y-1">
                    <span>Start Learning Now</span>
                    <svg class="ml-3 w-5 h-5 group-hover:translate-x-1 transition-transform duration-200 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                        <path d="M5 12h14M12 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>

        <div class="mt-16 md:mt-24 border-t-2 border-white/20 pt-8">
            <x-footer/>
        </div>
    </section>
</x-layout>

<!-- Custom Tailwind CSS styles for smooth 3D Flashcard rotations -->
<style>
    .perspective-1000 {
        perspective: 1000px;
    }
    .transform-style-3d {
        transform-style: preserve-3d;
    }
    .backface-hidden {
        backface-visibility: hidden;
        -webkit-backface-visibility: hidden;
    }
    .rotate-y-180 {
        transform: rotateY(180deg);
    }
</style>
