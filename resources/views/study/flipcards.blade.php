<x-layout>
    <div class="fixed inset-0 bg-[#F9FAFB] flex flex-col" x-data="flipcardStudy()">
        <style>
        /* 1. Create the 3D space */
        .study-flip-container {
            perspective: 1000px;
            width: 100%;
            height: 100%;
        }

        /* 2. The wrapper that actually rotates */
        .study-flip-inner {
            position: relative;
            width: 100%;
            height: 100%;
            transition: transform 0.5s ease-in-out;
            transform-style: preserve-3d;
        }

        /* 3. The class Alpine.js will toggle */
        .study-flip-container.is-flipped .study-flip-inner {
            transform: rotateY(180deg);
        }

        /* 4. Common styles for both sides */
        .study-flip-front,
        .study-flip-back {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            -webkit-backface-visibility: hidden; /* Critical for Safari */
            backface-visibility: hidden;
        }

        /* 5. Start the back face already flipped 180deg */
        .study-flip-back {
            transform: rotateY(180deg);
        }
    </style>
        {{-- Top Bar --}}
        <div class="px-6 md:px-12 pt-5 pb-3 shrink-0">
            <div class="max-w-3xl mx-auto flex items-center justify-between">
                <a href="{{ route('decks.show', $deck) }}" class="flex items-center gap-2 text-slate-400 hover:text-slate-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    <span class="font-semibold text-sm hidden md:inline">Back to Deck</span>
                </a>
                <div class="text-center">
                    <h2 class="text-lg font-bold text-slate-800">{{ $deck->title }}</h2>
                    <p class="text-xs text-slate-400 font-medium">Flip Cards</p>
                </div>
                <div class="w-20"></div>
            </div>
        </div>

        {{-- Progress Bar --}}
        <div class="px-6 md:px-12 pb-4 shrink-0">
            <div class="max-w-3xl mx-auto">
                <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                    <div class="h-full bg-emerald-400 rounded-full transition-all duration-500 ease-out"
                        :style="'width: ' + ((current + 1) / cards.length * 100) + '%'"></div>
                </div>
                <div class="flex justify-between mt-1.5">
                    <span class="text-xs text-slate-400 font-medium" x-text="(current + 1) + ' / ' + cards.length"></span>
                    <span class="text-xs text-emerald-500 font-bold" x-text="Math.round((current + 1) / cards.length * 100) + '%'"></span>
                </div>
            </div>
        </div>

{{-- Card Area --}}
        <div class="flex-1 flex items-center justify-center px-6 min-h-0 mb-6">
            <div class="w-full max-w-xl h-[min(360px,50vh)]">

                {{-- Flip Container --}}
                <div class="study-flip-container cursor-pointer"
                     @click="flipped = !flipped"
                     :class="{ 'is-flipped': flipped }">

                    <div class="study-flip-inner shadow-xl shadow-slate-200/50 rounded-[2.5rem]">

                        {{-- Front (Question) --}}
                        <div class="study-flip-front bg-white rounded-[2.5rem] border border-gray-100 flex flex-col items-center p-8 md:p-12">
                            <span class="text-[10px] font-bold text-emerald-500 uppercase tracking-widest shrink-0">Question</span>

                            {{-- Inner wrapper for long text scrolling --}}
                            <div class="flex-1 w-full flex items-center justify-center overflow-y-auto my-4">
                                <p class="text-xl md:text-2xl font-bold text-slate-800 text-center leading-relaxed" x-text="cards[current]?.question"></p>
                            </div>

                            <p class="text-xs text-slate-300 font-medium animate-pulse shrink-0">Click to reveal answer</p>
                        </div>

                        {{-- Back (Answer) --}}
                        <div class="study-flip-back bg-gradient-to-br from-emerald-500 to-teal-600 rounded-[2.5rem] flex flex-col items-center p-8 md:p-12 shadow-xl shadow-emerald-200/50">
                            <span class="text-[10px] font-bold text-emerald-100 uppercase tracking-widest shrink-0">Answer</span>

                            {{-- Inner wrapper for long text scrolling --}}
                            <div class="flex-1 w-full flex items-center justify-center overflow-y-auto my-4">
                                <p class="text-xl md:text-2xl font-bold text-white text-center leading-relaxed" x-text="cards[current]?.answer"></p>
                            </div>

                            <p class="text-xs text-emerald-100/60 font-medium shrink-0">Click to see question</p>
                        </div>

                    </div>
                </div>

            </div>
        </div>

        {{-- Bottom Navigation (fixed at bottom) --}}
        <div class="px-6 pb-5 pt-3 shrink-0">
            <div class="max-w-xl mx-auto">
                <div class="flex items-center justify-center gap-4">
                    <button @click="prev()"
                        :disabled="current === 0"
                        :class="current === 0 ? 'opacity-30 cursor-not-allowed' : 'hover:bg-slate-100 hover:text-slate-700'"
                        class="w-12 h-12 bg-white border border-gray-200 rounded-2xl flex items-center justify-center text-slate-400 shadow-sm transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    </button>

                    <div class="px-6 py-3 bg-white border border-gray-200 rounded-2xl shadow-sm">
                        <span class="text-sm font-bold text-slate-700" x-text="(current + 1) + ' of ' + cards.length"></span>
                    </div>

                    <button @click="next()"
                        :disabled="current === cards.length - 1"
                        :class="current === cards.length - 1 ? 'opacity-30 cursor-not-allowed' : 'hover:bg-slate-100 hover:text-slate-700'"
                        class="w-12 h-12 bg-white border border-gray-200 rounded-2xl flex items-center justify-center text-slate-400 shadow-sm transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </button>
                </div>
                <p class="text-center text-xs text-slate-300 mt-2">Use ← → arrow keys to navigate, Space to flip</p>
            </div>
        </div>

    </div>

    <script>
        function flipcardStudy() {
            return {
                cards: @json($cards),
                current: 0,
                flipped: false,

                prev() {
                    if (this.current > 0) {
                        this.current--;
                        this.flipped = false;
                    }
                },

                next() {
                    if (this.current < this.cards.length - 1) {
                        this.current++;
                        this.flipped = false;
                    }
                },

                init() {
                    document.addEventListener('keydown', (e) => {
                        if (e.key === 'ArrowLeft') this.prev();
                        if (e.key === 'ArrowRight') this.next();
                        if (e.key === ' ') { e.preventDefault(); this.flipped = !this.flipped; }
                    });
                }
            }
        }
    </script>
</x-layout>
