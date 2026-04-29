<x-layout>
    <div class="fixed inset-0 bg-[#F9FAFB] flex flex-col" x-data="flipcardStudy()">

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

        {{-- Card Area (fills remaining space) --}}
        <div class="flex-1 flex items-center justify-center px-6 min-h-0">
            <div class="w-full max-w-xl">

                {{-- Flip Card --}}
                <div class="cursor-pointer" @click="flipped = !flipped" style="perspective: 1000px;">
                    <div class="relative w-full"
                        style="transform-style: preserve-3d; height: min(360px, 50vh); transition: transform 0.6s ease-in-out;"
                        :style="flipped ? 'transform: rotateY(180deg)' : 'transform: rotateY(0deg)'">

                        {{-- Front (Question) --}}
                        <div class="absolute inset-0 bg-white rounded-[2rem] shadow-lg shadow-slate-200/50 border border-gray-100 flex flex-col items-center justify-center p-8 md:p-10"
                            style="backface-visibility: hidden;">
                            <p class="text-[10px] font-bold text-emerald-400 uppercase tracking-widest mb-4">Question</p>
                            <p class="text-xl md:text-2xl font-bold text-slate-800 text-center leading-relaxed" x-text="cards[current]?.question"></p>
                            <p class="mt-6 text-xs text-slate-300 font-medium">Click to reveal answer</p>
                        </div>

                        {{-- Back (Answer) --}}
                        <div class="absolute inset-0 bg-gradient-to-br from-emerald-500 to-teal-500 rounded-[2rem] shadow-lg shadow-emerald-200/50 flex flex-col items-center justify-center p-8 md:p-10"
                            style="backface-visibility: hidden; transform: rotateY(180deg);">
                            <p class="text-[10px] font-bold text-emerald-100 uppercase tracking-widest mb-4">Answer</p>
                            <p class="text-xl md:text-2xl font-bold text-white text-center leading-relaxed" x-text="cards[current]?.answer"></p>
                            <p class="mt-6 text-xs text-emerald-200 font-medium">Click to see question</p>
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
