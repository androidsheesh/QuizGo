<x-layout>
    <div class="min-h-screen bg-[#F9FAFB] flex flex-col" x-data="quizTimer({{ $quiz->time_limit ? $quiz->time_limit * 60 : 0 }})">

        {{-- Quiz Header --}}
        <header class="bg-white border-b border-gray-200 sticky top-0 z-40 shadow-sm">
            <div class="max-w-4xl mx-auto px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-emerald-100 text-emerald-600 rounded-xl flex items-center justify-center font-bold text-lg">
                        📝
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-slate-800">{{ $quiz->title }}</h1>
                        <p class="text-sm font-medium text-slate-400">{{ $assignment->classroom->name }}</p>
                    </div>
                </div>

                {{-- Timer Display --}}
                <div class="flex items-center gap-3 bg-slate-50 px-4 py-2 rounded-xl border border-slate-100" :class="{ 'bg-red-50 border-red-200 text-red-600': timeLimit > 0 && timeLeft <= 60 }">
                    <svg class="w-5 h-5 text-slate-400" :class="{ 'text-red-500': timeLimit > 0 && timeLeft <= 60 }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span class="font-mono font-bold text-lg" :class="{ 'text-red-600': timeLimit > 0 && timeLeft <= 60, 'text-slate-700': !(timeLimit > 0 && timeLeft <= 60) }" x-text="formattedTime"></span>
                </div>
            </div>
        </header>

        {{-- Quiz Content --}}
        <main class="flex-1 overflow-y-auto p-6 md:p-12">
            <div class="max-w-3xl mx-auto">

                @if($quiz->description)
                    <div class="bg-blue-50 border border-blue-100 p-6 rounded-2xl mb-8">
                        <h3 class="font-bold text-blue-800 mb-1">Instructions</h3>
                        <p class="text-blue-600 text-sm">{{ $quiz->description }}</p>
                    </div>
                @endif

                <form id="quizForm" method="POST" action="{{ route('student.quiz.submit', $assignment) }}">
                    @csrf
                    <input type="hidden" name="time_taken" x-model="timeTaken">

                    <div class="space-y-8">
                        @foreach($quiz->questions as $index => $question)
                            <div class="bg-white p-8 rounded-[2rem] border border-gray-100 shadow-sm">
                                <div class="flex gap-4 mb-6">
                                    <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-600 font-bold flex items-center justify-center shrink-0">
                                        {{ $index + 1 }}
                                    </div>
                                    <h3 class="text-lg font-bold text-slate-800 mt-1">{{ $question->question }}</h3>
                                </div>

                                <div class="ml-12">
                                    @if($question->type === 'multiple_choice')
                                        <div class="space-y-3">
                                            @foreach($question->choices as $choiceIndex => $choice)
                                                <label class="flex items-center gap-4 p-4 rounded-xl border border-slate-100 hover:border-emerald-300 hover:bg-emerald-50 cursor-pointer transition-colors group">
                                                    <input type="radio" name="answers[{{ $question->id }}]" value="{{ $choiceIndex }}" class="w-5 h-5 text-emerald-500 focus:ring-emerald-400 border-slate-300">
                                                    <span class="text-slate-700 font-medium group-hover:text-emerald-800">{{ $choice }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    @elseif($question->type === 'identification')
                                        <input type="text" name="answers[{{ $question->id }}]" placeholder="Type your answer here..." autocomplete="off"
                                               class="w-full p-4 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-400/30 focus:border-emerald-400 transition-all font-medium">
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-10 flex justify-end">
                        <button type="submit" class="px-8 py-4 bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-black text-lg rounded-2xl shadow-xl shadow-emerald-200 hover:scale-[1.02] active:scale-95 transition-transform flex items-center gap-3">
                            Submit Quiz
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                        </button>
                    </div>
                </form>

            </div>
        </main>
    </div>

    <script>
        function quizTimer(limitSeconds) {
            return {
                timeLimit: limitSeconds,
                timeLeft: limitSeconds,
                timeTaken: 0,
                timer: null,
                init() {
                    // Start counting up time taken regardless of limits
                    setInterval(() => {
                        this.timeTaken++;
                    }, 1000);

                    if (this.timeLimit > 0) {
                        this.timer = setInterval(() => {
                            this.timeLeft--;
                            if (this.timeLeft <= 0) {
                                clearInterval(this.timer);
                                // Auto submit when time runs out
                                document.getElementById('quizForm').submit();
                            }
                        }, 1000);
                    }
                },
                get formattedTime() {
                    if (this.timeLimit > 0) {
                        const m = Math.floor(this.timeLeft / 60);
                        const s = this.timeLeft % 60;
                        return `${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
                    } else {
                        const m = Math.floor(this.timeTaken / 60);
                        const s = this.timeTaken % 60;
                        return `${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
                    }
                }
            }
        }
    </script>
</x-layout>
