<x-layout>
    <div class="flex min-h-screen bg-[#F9FAFB]">

        <x-sidebar/>

        <main class="flex-1 p-6 md:p-12 overflow-y-auto relative">
            <x-dropdown-profile/>

            <div class="max-w-5xl mx-auto flex flex-col">

                {{-- Back Button --}}
                <a href="{{ route('student.assignments') }}" class="inline-flex items-center text-slate-400 hover:text-slate-600 transition-colors mb-6 text-sm font-medium">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                    Back to Assignments
                </a>

                {{-- Header --}}
                <div class="bg-white rounded-[2rem] border border-gray-100 p-8 shadow-sm mb-10">
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="flex items-center gap-3 mb-2">
                                <h2 class="text-3xl font-bold text-slate-800">{{ $classroom->name }}</h2>
                                <span class="font-mono bg-slate-100 px-3 py-1 rounded-lg text-sm text-slate-600 font-bold">{{ $classroom->code }}</span>
                            </div>
                            <p class="text-slate-500 font-medium">Teacher: {{ $classroom->teacher->firstname }} {{ $classroom->teacher->lastname }}</p>
                            @if($classroom->description)
                                <p class="text-slate-400 text-sm mt-3">{{ $classroom->description }}</p>
                            @endif
                        </div>
                        <div class="hidden md:flex w-16 h-16 bg-emerald-100 text-emerald-600 rounded-2xl items-center justify-center text-3xl font-bold shadow-sm">
                            🏫
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    {{-- Left Column: Quizzes --}}
                    <div class="lg:col-span-2 space-y-8">

                        {{-- Pending Quizzes --}}
                        <div>
                            <h3 class="text-xl font-bold text-slate-700 mb-4 flex items-center gap-2">
                                <span>⏳</span> Pending Quizzes
                            </h3>
                            @if($pendingQuizzes->isEmpty())
                                <div class="bg-white rounded-[2rem] border border-gray-100 p-8 text-center shadow-sm">
                                    <p class="text-slate-400 font-medium">No pending quizzes. You're all caught up! 🎉</p>
                                </div>
                            @else
                                <div class="space-y-4">
                                    @foreach($pendingQuizzes as $assignment)
                                        <div class="bg-white border border-gray-100 rounded-3xl p-6 shadow-sm hover:shadow-md transition-shadow flex flex-col md:flex-row md:items-center justify-between gap-4">
                                            <div>
                                                <h4 class="text-lg font-bold text-slate-800">{{ $assignment->quiz->title }}</h4>
                                                <div class="flex items-center gap-3 mt-1 text-sm text-slate-500">
                                                    <span>{{ $assignment->quiz->questions->count() }} Questions</span>
                                                    @if($assignment->quiz->time_limit)
                                                        <span>• {{ $assignment->quiz->time_limit }} Min Limit</span>
                                                    @endif
                                                    @if($assignment->due_at)
                                                        <span class="text-amber-500 font-medium">• Due {{ $assignment->due_at->format('M d, Y') }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <a href="{{ route('student.quiz.take', $assignment) }}" class="px-6 py-3 bg-emerald-500 text-white font-bold rounded-xl shadow-md shadow-emerald-200 hover:bg-emerald-600 transition-colors whitespace-nowrap text-center">
                                                Take Quiz
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        {{-- Completed Quizzes --}}
                        <div>
                            <h3 class="text-xl font-bold text-slate-700 mb-4 flex items-center gap-2">
                                <span>✅</span> Completed Quizzes
                            </h3>
                            @if($completedQuizzes->isEmpty())
                                <div class="bg-white rounded-[2rem] border border-gray-100 p-8 text-center shadow-sm">
                                    <p class="text-slate-400 font-medium">You haven't completed any quizzes in this class yet.</p>
                                </div>
                            @else
                                <div class="space-y-4">
                                    @foreach($completedQuizzes as $data)
                                        <div class="bg-white border border-gray-100 rounded-3xl p-6 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4 opacity-75 hover:opacity-100 transition-opacity">
                                            <div>
                                                <h4 class="text-lg font-bold text-slate-800">{{ $data['assignment']->quiz->title }}</h4>
                                                <div class="flex items-center gap-3 mt-1 text-sm text-slate-500">
                                                    <span>Completed {{ $data['attempt']->completed_at->diffForHumans() }}</span>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-4">
                                                <div class="text-right">
                                                    <p class="text-xs text-slate-400 font-bold uppercase tracking-wider">Score</p>
                                                    <p class="text-lg font-black text-slate-800">{{ $data['attempt']->score }}/{{ $data['attempt']->total_questions }}</p>
                                                </div>
                                                <a href="{{ route('student.quiz.results', $data['attempt']) }}" class="px-5 py-2.5 bg-slate-100 text-slate-600 font-bold rounded-xl hover:bg-slate-200 transition-colors whitespace-nowrap text-center">
                                                    View Results
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                    </div>

                    {{-- Right Column: Class Rankings --}}
                    <div>
                        <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden sticky top-6">
                            <div class="p-6 border-b border-gray-50">
                                <h3 class="text-lg font-bold text-slate-700">🏆 Class Rankings</h3>
                                <p class="text-slate-400 text-xs mt-1">Based on highest score & shortest time across all quizzes.</p>
                            </div>

                            @if($studentRankings->isEmpty() || $studentRankings->max('attempt_count') === 0)
                                <div class="p-8 text-center">
                                    <p class="text-slate-400 text-sm">No quiz attempts yet.</p>
                                </div>
                            @else
                                <div class="divide-y divide-gray-50">
                                    @foreach($studentRankings as $rank => $entry)
                                        <div class="p-4 flex items-center gap-4 {{ $entry['student']->id === Auth::id() ? 'bg-emerald-50/50' : '' }}">
                                            <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm shrink-0
                                                {{ $rank === 0 ? 'bg-yellow-100 text-yellow-700' : ($rank === 1 ? 'bg-gray-100 text-gray-600' : ($rank === 2 ? 'bg-amber-100 text-amber-700' : 'bg-slate-50 text-slate-500')) }}">
                                                {{ $rank === 0 ? '🥇' : ($rank === 1 ? '🥈' : ($rank === 2 ? '🥉' : $rank + 1)) }}
                                            </div>

                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-bold text-slate-800 truncate">
                                                    {{ $entry['student']->firstname }} {{ $entry['student']->lastname }}
                                                    @if($entry['student']->id === Auth::id())
                                                        <span class="text-xs text-emerald-600 font-semibold ml-1">(You)</span>
                                                    @endif
                                                </p>
                                                <p class="text-xs text-slate-400">Score: {{ $entry['best_score'] }} • Time: {{ $entry['best_time'] > 0 ? gmdate('i:s', $entry['best_time']) : '--' }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                </div>

            </div>
        </main>
    </div>
</x-layout>
