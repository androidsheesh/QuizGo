<x-layout>
    <div class="fixed inset-0 bg-[#F9FAFB] flex flex-col" x-data="multipleChoiceStudy()">

        {{-- Top Bar --}}
        <div class="px-6 md:px-12 pt-5 pb-3 shrink-0">
            <div class="max-w-3xl mx-auto flex items-center justify-between">
                <a href="{{ route('decks.show', $deck) }}" class="flex items-center gap-2 text-slate-400 hover:text-slate-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    <span class="font-semibold text-sm hidden md:inline">Back to Deck</span>
                </a>
                <div class="text-center">
                    <h2 class="text-lg font-bold text-slate-800">{{ $deck->title }}</h2>
                    <p class="text-xs text-slate-400 font-medium">Multiple Choice</p>
                </div>
                <div class="flex items-center gap-2">
                    <div class="px-3 py-1.5 bg-emerald-50 border border-emerald-100 rounded-xl">
                        <span class="text-sm font-bold text-emerald-600" x-text="score"></span>
                        <span class="text-xs text-emerald-400">correct</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Progress Bar --}}
        <div class="px-6 md:px-12 pb-4 shrink-0" x-show="!finished">
            <div class="max-w-3xl mx-auto">
                <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                    <div class="h-full bg-violet-400 rounded-full transition-all duration-500 ease-out"
                        :style="'width: ' + ((current + 1) / cards.length * 100) + '%'"></div>
                </div>
                <div class="flex justify-between mt-1.5">
                    <span class="text-xs text-slate-400 font-medium" x-text="(current + 1) + ' / ' + cards.length"></span>
                    <span class="text-xs text-violet-500 font-bold" x-text="Math.round((current + 1) / cards.length * 100) + '%'"></span>
                </div>
            </div>
        </div>

        {{-- Quiz Area (fills remaining space) --}}
        <div class="flex-1 flex items-center justify-center px-6 min-h-0 overflow-y-auto">

            {{-- Active Quiz --}}
            <div class="w-full max-w-2xl py-4" x-show="!finished">

                {{-- Question Card --}}
                <div class="bg-white rounded-[2rem] shadow-lg shadow-slate-200/50 border border-gray-100 p-6 md:p-8 mb-5">
                    <p class="text-[10px] font-bold text-violet-400 uppercase tracking-widest mb-3">Question <span x-text="current + 1"></span></p>
                    <p class="text-lg md:text-xl font-bold text-slate-800 leading-relaxed" x-text="cards[current]?.question"></p>
                </div>

                {{-- Answer Options --}}
                <div class="grid grid-cols-1 gap-2.5">
                    <template x-for="(option, index) in currentOptions" :key="index">
                        <button @click="selectAnswer(option)"
                            :disabled="answered"
                            :class="{
                                'border-gray-100 bg-white hover:border-violet-300 hover:bg-violet-50/50 hover:-translate-y-0.5': !answered,
                                'border-emerald-400 bg-emerald-50 ring-2 ring-emerald-400/30': answered && option === cards[current]?.answer,
                                'border-red-400 bg-red-50 ring-2 ring-red-400/30': answered && selected === option && option !== cards[current]?.answer,
                                'border-gray-100 bg-white opacity-50': answered && option !== cards[current]?.answer && selected !== option,
                                'cursor-not-allowed': answered
                            }"
                            class="w-full flex items-center gap-4 p-4 rounded-2xl border-2 transition-all duration-200 text-left">
                            <div class="w-9 h-9 rounded-xl flex items-center justify-center text-sm font-bold shrink-0"
                                :class="{
                                    'bg-slate-100 text-slate-500': !answered,
                                    'bg-emerald-400 text-white': answered && option === cards[current]?.answer,
                                    'bg-red-400 text-white': answered && selected === option && option !== cards[current]?.answer,
                                    'bg-slate-100 text-slate-300': answered && option !== cards[current]?.answer && selected !== option
                                }">
                                <span x-text="['A', 'B', 'C', 'D'][index]"></span>
                            </div>
                            <span class="font-semibold text-slate-700 text-sm" x-text="option"></span>
                            <div class="ml-auto shrink-0">
                                <template x-if="answered && option === cards[current]?.answer">
                                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                </template>
                                <template x-if="answered && selected === option && option !== cards[current]?.answer">
                                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </template>
                            </div>
                        </button>
                    </template>
                </div>

                {{-- Next Button --}}
                <div x-show="answered" class="mt-4 flex justify-center">
                    <button @click="nextQuestion()"
                        class="px-8 py-3 bg-slate-900 text-white font-bold rounded-2xl hover:bg-slate-800 transition-colors shadow-lg shadow-slate-200">
                        <span x-text="current < cards.length - 1 ? 'Next Question →' : 'See Results →'"></span>
                    </button>
                </div>
            </div>

            {{-- Results Screen --}}
            <div class="w-full max-w-lg" x-show="finished" style="display: none;">
                <div class="bg-white rounded-[2rem] shadow-lg shadow-slate-200/50 border border-gray-100 p-10 text-center">
                    <div class="w-20 h-20 rounded-full mx-auto mb-6 flex items-center justify-center text-4xl"
                        :class="percentage >= 70 ? 'bg-emerald-100' : percentage >= 40 ? 'bg-amber-100' : 'bg-red-100'">
                        <span x-text="percentage >= 70 ? '🎉' : percentage >= 40 ? '💪' : '📚'"></span>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-800 mb-2">Quiz Complete!</h3>
                    <p class="text-slate-400 mb-6">{{ $deck->title }}</p>

                    <div class="flex justify-center gap-8 mb-8">
                        <div class="text-center">
                            <p class="text-3xl font-black" :class="percentage >= 70 ? 'text-emerald-500' : percentage >= 40 ? 'text-amber-500' : 'text-red-500'" x-text="percentage + '%'"></p>
                            <p class="text-xs text-slate-400 font-medium mt-1">Score</p>
                        </div>
                        <div class="text-center">
                            <p class="text-3xl font-black text-emerald-500" x-text="score"></p>
                            <p class="text-xs text-slate-400 font-medium mt-1">Correct</p>
                        </div>
                        <div class="text-center">
                            <p class="text-3xl font-black text-red-400" x-text="cards.length - score"></p>
                            <p class="text-xs text-slate-400 font-medium mt-1">Wrong</p>
                        </div>
                    </div>

                    <div class="flex flex-col gap-3">
                        <a href="{{ route('study.multiplechoice', $deck) }}?count={{ request('count', $cards->count()) }}"
                            class="w-full py-4 bg-slate-900 text-white font-bold rounded-2xl hover:bg-slate-800 transition-colors shadow-lg shadow-slate-200 text-center">
                            Try Again
                        </a>
                        <a href="{{ route('decks.show', $deck) }}"
                            class="w-full py-4 bg-slate-100 text-slate-600 font-bold rounded-2xl hover:bg-slate-200 transition-colors text-center">
                            Back to Deck
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
        function multipleChoiceStudy() {
            return {
                cards: @json($cards),
                allAnswers: @json($allAnswers),
                current: 0,
                score: 0,
                answered: false,
                selected: null,
                finished: false,
                currentOptions: [],

                get percentage() {
                    return Math.round(this.score / this.cards.length * 100);
                },

                init() {
                    this.generateOptions();
                },

                generateOptions() {
                    const correct = this.cards[this.current]?.answer;
                    if (!correct) return;

                    let wrongs = this.allAnswers.filter(a => a !== correct);
                    wrongs = wrongs.sort(() => Math.random() - 0.5).slice(0, 3);

                    while (wrongs.length < 3) {
                        wrongs.push(wrongs[wrongs.length % Math.max(wrongs.length, 1)] || 'No answer');
                    }

                    this.currentOptions = [correct, ...wrongs].sort(() => Math.random() - 0.5);
                },

                selectAnswer(option) {
                    if (this.answered) return;
                    this.selected = option;
                    this.answered = true;
                    if (option === this.cards[this.current].answer) {
                        this.score++;
                    }
                },

                nextQuestion() {
                    if (this.current < this.cards.length - 1) {
                        this.current++;
                        this.answered = false;
                        this.selected = null;
                        this.generateOptions();
                    } else {
                        this.finished = true;
                    }
                }
            }
        }
    </script>
</x-layout>
