<x-layout>
    <div class="flex min-h-screen bg-[#F9FAFB]">
        <x-sidebar/>
        <main class="flex-1 p-6 md:p-12 overflow-y-auto relative">
            <x-dropdown-profile/>

            <div class="max-w-4xl mx-auto flex flex-col">

                {{-- Back Link --}}
                <a href="{{ route('student.classroom.show', $attempt->quizAssignment->classroom_id) }}" class="inline-flex items-center text-slate-400 hover:text-slate-600 transition-colors mb-8 text-sm font-medium">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                    Back to Class
                </a>

                @if(session('success'))
                    <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-3 rounded-2xl mb-8 font-medium text-sm">
                        🎉 {{ session('success') }}
                    </div>
                @endif
                @if(session('info'))
                    <div class="bg-blue-50 border border-blue-200 text-blue-700 px-6 py-3 rounded-2xl mb-8 font-medium text-sm">
                        ℹ️ {{ session('info') }}
                    </div>
                @endif

                {{-- Results Header Card --}}
                <div class="bg-white rounded-[2rem] border border-gray-100 p-8 shadow-sm mb-10 overflow-hidden relative">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-emerald-50 rounded-full blur-3xl -mr-20 -mt-20 z-0"></div>
                    <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-8">
                        <div>
                            <h2 class="text-3xl font-black text-slate-800 mb-2">{{ $quiz->title }}</h2>
                            <p class="text-slate-500 font-medium">Completed {{ $attempt->completed_at->format('M d, Y g:i A') }}</p>
                        </div>

                        <div class="flex items-center gap-6">
                            <div class="text-center">
                                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Time</p>
                                <p class="text-2xl font-black text-slate-700">{{ gmdate('i:s', $attempt->time_taken) }}</p>
                            </div>
                            <div class="w-px h-12 bg-gray-200"></div>
                            <div class="text-center">
                                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Score</p>
                                <div class="flex items-baseline gap-1 justify-center text-emerald-500">
                                    <span class="text-5xl font-black">{{ $attempt->score }}</span>
                                    <span class="text-xl font-bold text-slate-400">/{{ $attempt->total_questions }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tabs --}}
                <div x-data="{ tab: 'review' }">
                    <div class="flex space-x-1 bg-white rounded-2xl p-1.5 border border-gray-100 shadow-sm mb-8 w-fit">
                        <button @click="tab='review'" :class="tab==='review' ? 'bg-slate-900 text-white shadow-lg' : 'text-slate-500 hover:text-slate-700'" class="px-6 py-2.5 rounded-xl font-semibold text-sm transition-all flex items-center gap-2">
                            <span>📝</span> Review Answers
                        </button>
                        <button @click="tab='leaderboard'" :class="tab==='leaderboard' ? 'bg-slate-900 text-white shadow-lg' : 'text-slate-500 hover:text-slate-700'" class="px-6 py-2.5 rounded-xl font-semibold text-sm transition-all flex items-center gap-2">
                            <span>🏆</span> Leaderboard
                        </button>
                    </div>

                    {{-- TAB: Review Answers --}}
                    <div x-show="tab==='review'" x-transition class="space-y-6">
                        @foreach($attempt->answers as $index => $answer)
                            <div class="bg-white rounded-3xl border {{ $answer->is_correct ? 'border-emerald-100' : 'border-red-100' }} p-6 shadow-sm relative overflow-hidden">
                                <div class="absolute top-0 left-0 w-1.5 h-full {{ $answer->is_correct ? 'bg-emerald-400' : 'bg-red-400' }}"></div>

                                <div class="ml-4">
                                    <div class="flex items-start justify-between gap-4 mb-4">
                                        <div class="flex gap-4">
                                            <span class="text-slate-400 font-bold mt-0.5">{{ $index + 1 }}.</span>
                                            <h3 class="text-lg font-bold text-slate-800">{{ $answer->question->question }}</h3>
                                        </div>
                                        @if($answer->is_correct)
                                            <span class="px-3 py-1 bg-emerald-50 text-emerald-600 font-bold text-xs rounded-lg shrink-0">✓ Correct</span>
                                        @else
                                            <span class="px-3 py-1 bg-red-50 text-red-600 font-bold text-xs rounded-lg shrink-0">✕ Incorrect</span>
                                        @endif
                                    </div>

                                    <div class="ml-8 space-y-4">
                                        {{-- Student's Answer --}}
                                        <div>
                                            <p class="text-xs font-bold text-slate-400 uppercase mb-1">Your Answer</p>
                                            <div class="p-3 rounded-xl border {{ $answer->is_correct ? 'bg-emerald-50/50 border-emerald-100 text-emerald-800' : 'bg-red-50/50 border-red-100 text-red-800' }} font-medium">
                                                {{ $answer->student_answer ?: '— No answer provided —' }}
                                            </div>
                                        </div>

                                        {{-- Show correct answer if wrong --}}
                                        @if(!$answer->is_correct)
                                            <div>
                                                <p class="text-xs font-bold text-slate-400 uppercase mb-1">Correct Answer</p>
                                                <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 text-slate-700 font-bold">
                                                    {{ $answer->question->correct_answer }}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- TAB: Leaderboard --}}
                    <div x-show="tab==='leaderboard'" x-cloak x-transition>
                        <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden">
                            <div class="p-6 border-b border-gray-50 flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-bold text-slate-700 flex items-center gap-2">
                                        <span>🏆</span> Quiz Rankings
                                    </h3>
                                    <p class="text-slate-400 text-xs mt-1">Classmates who have taken this quiz</p>
                                </div>
                                <span class="bg-slate-100 text-slate-600 px-3 py-1 rounded-full text-xs font-bold">{{ $rankings->count() }} Attempts</span>
                            </div>

                            <div class="divide-y divide-gray-50">
                                @foreach($rankings as $rank => $rankAttempt)
                                    <div class="p-4 flex items-center gap-4 hover:bg-slate-50/50 transition-colors {{ $rankAttempt->id === $attempt->id ? 'bg-emerald-50/30' : '' }}">
                                        <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm shrink-0
                                            {{ $rank === 0 ? 'bg-yellow-100 text-yellow-700' : ($rank === 1 ? 'bg-gray-100 text-gray-600' : ($rank === 2 ? 'bg-amber-100 text-amber-700' : 'bg-slate-50 text-slate-500')) }}">
                                            {{ $rank === 0 ? '🥇' : ($rank === 1 ? '🥈' : ($rank === 2 ? '🥉' : $rank + 1)) }}
                                        </div>

                                        <div class="flex-1 flex flex-col md:flex-row md:items-center justify-between gap-2">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center overflow-hidden border border-emerald-200 shrink-0">
                                                    @if($rankAttempt->student->profile_picture)
                                                        <img src="{{ asset('storage/' . $rankAttempt->student->profile_picture) }}" class="w-full h-full object-cover">
                                                    @else
                                                        <span class="text-xs">👤</span>
                                                    @endif
                                                </div>
                                                <p class="font-bold text-slate-800">
                                                    {{ $rankAttempt->student->firstname }} {{ $rankAttempt->student->lastname }}
                                                    @if($rankAttempt->id === $attempt->id)
                                                        <span class="text-xs text-emerald-600 font-semibold ml-1">(You)</span>
                                                    @endif
                                                </p>
                                            </div>

                                            <div class="flex items-center gap-6 text-sm">
                                                <div class="text-right">
                                                    <span class="text-slate-400 text-xs uppercase font-bold mr-2">Score</span>
                                                    <span class="font-black text-slate-700">{{ $rankAttempt->score }}/{{ $rankAttempt->total_questions }}</span>
                                                </div>
                                                <div class="text-right w-16">
                                                    <span class="text-slate-400 text-xs uppercase font-bold mr-2">Time</span>
                                                    <span class="font-bold text-slate-600">{{ gmdate('i:s', $rankAttempt->time_taken) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>
</x-layout>
