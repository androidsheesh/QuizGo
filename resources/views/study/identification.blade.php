<x-layout>
    <div class="fixed inset-0 bg-[#F9FAFB] flex flex-col" x-data="identificationStudy()">

        {{-- Top Bar --}}
        <div class="px-6 md:px-12 pt-5 pb-3 shrink-0">
            <div class="max-w-3xl mx-auto flex items-center justify-between">
                <a href="{{ route('decks.show', $deck) }}" class="flex items-center gap-2 text-slate-400 hover:text-slate-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    <span class="font-semibold text-sm hidden md:inline">Back to Deck</span>
                </a>
                <div class="text-center">
                    <h2 class="text-lg font-bold text-slate-800">{{ $deck->title }}</h2>
                    <p class="text-xs text-slate-400 font-medium">Identification</p>
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
                    <div class="h-full bg-amber-400 rounded-full transition-all duration-500 ease-out"
                        :style="'width: ' + ((current + 1) / cards.length * 100) + '%'"></div>
                </div>
                <div class="flex justify-between mt-1.5">
                    <span class="text-xs text-slate-400 font-medium" x-text="(current + 1) + ' / ' + cards.length"></span>
                    <span class="text-xs text-amber-500 font-bold" x-text="Math.round((current + 1) / cards.length * 100) + '%'"></span>
                </div>
            </div>
        </div>

        {{-- Quiz Area (fills remaining space) --}}
        <div class="flex-1 flex items-center justify-center px-6 min-h-0 overflow-y-auto">

            {{-- Active Quiz --}}
            <div class="w-full max-w-2xl py-4" x-show="!finished">

                {{-- Question Card --}}
                <div class="bg-white rounded-[2rem] shadow-lg shadow-slate-200/50 border border-gray-100 p-6 md:p-8 mb-5">
                    <p class="text-[10px] font-bold text-amber-400 uppercase tracking-widest mb-3">Question <span x-text="current + 1"></span></p>
                    <p class="text-lg md:text-xl font-bold text-slate-800 leading-relaxed" x-text="cards[current]?.question"></p>
                </div>

                {{-- Answer Input --}}
                <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-5 md:p-6 mb-4">
                    <label class="block text-sm font-bold text-slate-600 mb-2">Your Answer</label>
                    <div class="flex gap-3">
                        <input type="text"
                            x-model="userAnswer"
                            x-ref="answerInput"
                            @keydown.enter="if(!answered) checkAnswer()"
                            :disabled="answered"
                            :class="answered ? 'bg-slate-50 cursor-not-allowed' : 'bg-slate-50 focus:bg-white focus:border-amber-400'"
                            class="flex-1 p-3.5 border border-transparent rounded-2xl text-slate-700 font-medium focus:outline-none transition-all placeholder:text-slate-300"
                            placeholder="Type your answer here...">
                        <button @click="checkAnswer()"
                            x-show="!answered"
                            :disabled="!userAnswer.trim()"
                            :class="userAnswer.trim() ? 'bg-slate-900 hover:bg-slate-800 shadow-lg shadow-slate-200' : 'bg-slate-200 cursor-not-allowed'"
                            class="px-6 py-3.5 text-white font-bold rounded-2xl transition-all shrink-0">
                            Check
                        </button>
                    </div>
                </div>

                {{-- Feedback --}}
                <div x-show="answered" style="display: none;" class="mb-4">
                    {{-- Correct --}}
                    <div x-show="isCorrect"
                        class="bg-emerald-50 border-2 border-emerald-200 rounded-2xl p-5 flex items-center gap-4">
                        <div class="w-10 h-10 bg-emerald-400 rounded-xl flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <div>
                            <p class="font-bold text-emerald-700 text-sm">Correct!</p>
                            <p class="text-xs text-emerald-600 mt-0.5">The answer is: <span class="font-bold" x-text="cards[current]?.answer"></span></p>
                        </div>
                    </div>

                    {{-- Wrong --}}
                    <div x-show="!isCorrect"
                        class="bg-red-50 border-2 border-red-200 rounded-2xl p-5 flex items-center gap-4">
                        <div class="w-10 h-10 bg-red-400 rounded-xl flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </div>
                        <div>
                            <p class="font-bold text-red-700 text-sm">Incorrect</p>
                            <p class="text-xs text-red-600 mt-0.5">Correct answer: <span class="font-bold" x-text="cards[current]?.answer"></span></p>
                            <p class="text-xs text-red-400 mt-0.5">Your answer: <span class="font-medium" x-text="userAnswer"></span></p>
                        </div>
                    </div>
                </div>

                {{-- Next Button --}}
                <div x-show="answered" style="display: none;" class="flex justify-center">
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
                        <a href="{{ route('study.identification', $deck) }}?count={{ request('count', $cards->count()) }}"
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
        function identificationStudy() {
            return {
                cards: @json($cards),
                current: 0,
                score: 0,
                answered: false,
                userAnswer: '',
                isCorrect: false,
                finished: false,

                get percentage() {
                    return Math.round(this.score / this.cards.length * 100);
                },

                init() {
                    this.$nextTick(() => {
                        if (this.$refs.answerInput) this.$refs.answerInput.focus();
                    });
                },

                checkAnswer() {
                    if (this.answered || !this.userAnswer.trim()) return;
                    this.answered = true;

                    const correct = this.cards[this.current].answer.trim().toLowerCase();
                    const user = this.userAnswer.trim().toLowerCase();
                    this.isCorrect = user === correct;

                    if (this.isCorrect) {
                        this.score++;
                    }
                },

                nextQuestion() {
                    if (this.current < this.cards.length - 1) {
                        this.current++;
                        this.answered = false;
                        this.userAnswer = '';
                        this.isCorrect = false;
                        this.$nextTick(() => {
                            if (this.$refs.answerInput) this.$refs.answerInput.focus();
                        });
                    } else {
                        this.finished = true;
                    }
                }
            }
        }
    </script>
</x-layout>
