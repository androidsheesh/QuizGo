<x-layout>
    {{--
        [ SECTION 01 ] HERO
    --}}
    <section id="hero-section" class="relative overflow-hidden bg-gradient-to-br from-[#7bfeb6] via-[#4dfc94] to-[#3ae884] pt-10 pb-44 flex flex-col">
        <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(#000 1px, transparent 1px); background-size: 20px 20px;"></div>
        <x-navbar/>

        <div class="hero-content relative z-10 flex flex-col justify-center items-center text-center px-4 mt-28">
            <span class="bg-black text-white px-4 py-1 rounded-full text-xs font-bold uppercase tracking-widest mb-6">Study Smarter, Not Harder</span>
            <h1 class="text-7xl md:text-9xl font-black text-black mb-6 leading-[0.9] tracking-tighter">
                ADDICTIVE<br>
                <span class="text-transparent" style="-webkit-text-stroke: 2px black;">LEARNING</span>
            </h1>
            <p class="text-xl md:text-2xl text-black/80 mb-10 max-w-lg font-medium">
                Gamified flashcards designed to lock information in your brain.
            </p>
            <div class="flex gap-4">
                <a href="/signup" class="px-10 py-4 rounded-2xl bg-white text-black font-black text-lg border-2 border-black shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-1 hover:translate-y-1 transition-all">
                    Start Learning
                </a>
            </div>
        </div>
    </section>

    {{--
        [ SECTION 02 ] MOCKUP
         Also this section is hardcoded
         this section is dapat mag change2 ang cards.
         D pako sure kanusa nako ni e change
    --}}
    <section id="mockup-section" class="relative bg-white z-20 pb-24">
        <div class="max-w-5xl mx-auto px-4 -mt-32">
            <div class="bg-white rounded-[2.5rem] shadow-[20px_20px_0px_0px_rgba(0,0,0,0.05)] border-4 border-black p-8 md:p-16 flex flex-col items-center justify-center relative overflow-hidden">

                <header class="flex items-center justify-between w-full max-w-md mb-16">
                    <div class="w-10 h-10 rounded-full border-2 border-black flex items-center justify-center cursor-pointer hover:bg-gray-100 transition-colors">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="black" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M19 12H5M12 19l-7-7 7-7"/>
                        </svg>
                    </div>

                    <div class="flex flex-col items-center flex-1 px-4">
                        <span class="text-xs font-black uppercase tracking-widest text-black mb-2"></span>
                        <div class="w-full h-3 bg-gray-100 border-2 border-black rounded-full overflow-hidden p-0.5">
                            <div class="h-full bg-blue-500 rounded-full border-r-2 border-black" style="width: 23.8%;"></div>
                        </div>
                    </div>

                    <div class="w-10 text-right font-black text-gray-400 text-sm">5/21</div>
                </header>

                <div class="relative w-full max-w-md aspect-[4/3] mb-16">
                    <div class="absolute inset-0 bg-black rounded-3xl translate-x-2 translate-y-2"></div>

                    <div class="absolute inset-0 bg-[#7ac2ff] border-4 border-black rounded-3xl flex items-center justify-center transform -rotate-1 transition-transform hover:rotate-0 duration-300">
                        <div class="absolute inset-0 bg-white m-3 rounded-[1.2rem] border-2 border-black flex items-center justify-center p-8 text-center shadow-inner">
                            <h3 class="text-3xl md:text-4xl font-black text-black leading-tight">What is Laravel?</h3>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row items-center justify-center gap-4 w-full">
                    <button class="group w-full sm:w-auto px-10 py-4 bg-white border-4 border-black text-black rounded-2xl font-black flex items-center justify-center space-x-3 hover:bg-[#7ac2ff] transition-all shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] active:shadow-none active:translate-x-1 active:translate-y-1">
                        <svg class="group-hover:rotate-180 transition-transform duration-500" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                            <path d="M3.51 9a9 9 0 0114.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0020.49 15"/>
                        </svg>
                        <span class="tracking-tight">FLIP CARD</span>
                    </button>

                    <button class="w-full sm:w-auto px-12 py-4 bg-[#005cff] border-4 border-black text-white rounded-2xl font-black flex items-center justify-center space-x-3 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] hover:bg-blue-600 transition-all active:shadow-none active:translate-x-1 active:translate-y-1">
                        <span class="tracking-tight text-lg">NEXT</span>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                            <path d="M5 12h14M12 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>

            </div>
        </div>
    </section>

   {{--
        [ SECTION 03 ] FEATURES
    --}}
    <section id="features" class="bg-gradient-to-b from-[#f3c4ff] to-[#ce4aff] pt-32 pb-16 px-6 md:px-16 border-t-4 border-black">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center mb-40 gap-16">

            <div class="md:w-3/5">
                <div class="inline-block bg-white border-2 border-black px-4 py-1 mb-6 font-black uppercase text-sm transform -rotate-2">
                    Level Up Your Brain
                </div>
                <h2 class="text-6xl md:text-8xl font-black text-black tracking-tighter leading-none mb-10">
                    Master any <br>subject, <span class="text-white">instantly.</span>
                </h2>

                <p class="text-black text-xl md:text-2xl mb-12 leading-tight font-bold max-w-xl italic">
                    "QuizGo transforms boring study sessions into high-octane learning sprints."
                </p>

                <a href="/signup" class="group relative inline-flex items-center justify-center px-10 py-5 text-xl font-black text-white bg-black rounded-full overflow-hidden transition-all hover:pr-14">
                    <span class="relative z-10">Start Learning Now</span>
                    <svg class="absolute right-4 opacity-0 group-hover:opacity-100 transition-all w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                        <path d="M5 12h14M12 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        <x-footer/>
</x-layout>
