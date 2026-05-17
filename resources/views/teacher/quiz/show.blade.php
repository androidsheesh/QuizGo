<x-layout>
    <div class="flex min-h-screen bg-[#F9FAFB]">
        <x-teacher-sidebar/>
        <main class="flex-1 p-6 md:p-12 overflow-y-auto relative">
            <x-dropdown-profile/>
            
            <div class="max-w-4xl mx-auto flex flex-col">
                {{-- Header --}}
                <div class="flex flex-col md:flex-row items-start md:items-center justify-between mb-8 gap-4">
                    <div>
                        <a href="{{ route('teacher.quiz.index') }}" class="text-emerald-500 font-semibold text-sm hover:text-emerald-600 mb-2 inline-block">← Back to Quizzes</a>
                        <h2 class="text-3xl font-bold text-slate-800">{{ $quiz->title }}</h2>
                        <p class="text-slate-500 mt-1">{{ $quiz->description ?? 'No description' }} • {{ $quiz->questions->count() }} Questions</p>
                    </div>
                    
                    {{-- Alert Messages --}}
                    @if(session('success'))
                        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-3 rounded-2xl font-medium text-sm" x-data="{s:true}" x-show="s" x-init="setTimeout(()=>s=false,4000)" x-transition>
                            ✓ {{ session('success') }}
                        </div>
                    @endif
                </div>

                {{-- Questions List --}}
                <div class="bg-white rounded-[2rem] border border-gray-100 p-8 shadow-sm mb-8">
                    <h3 class="text-xl font-bold text-slate-700 mb-6">Quiz Questions</h3>
                    
                    <div class="space-y-6">
                        @foreach($quiz->questions as $index => $question)
                            <div class="p-6 bg-slate-50 border border-slate-100 rounded-2xl relative">
                                <div class="flex justify-between items-start mb-4">
                                    <span class="text-sm font-bold text-slate-500">Question {{ $index + 1 }}</span>
                                    <span class="px-3 py-1 bg-white text-slate-600 border border-slate-200 rounded-lg text-xs font-bold uppercase tracking-wide">{{ str_replace('_', ' ', $question->type) }}</span>
                                </div>
                                <p class="text-lg font-medium text-slate-800 mb-4">{{ $question->question }}</p>
                                
                                @if($question->type === 'multiple_choice' && is_array($question->choices))
                                    <div class="space-y-2">
                                        @foreach($question->choices as $choice)
                                            <div class="flex items-center p-3 rounded-xl border {{ $choice === $question->correct_answer ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-white border-slate-200 text-slate-600' }}">
                                                <div class="w-5 h-5 rounded-full border-2 mr-3 flex items-center justify-center {{ $choice === $question->correct_answer ? 'border-emerald-500 bg-emerald-500 text-white font-bold text-xs' : 'border-slate-300' }}">
                                                    @if($choice === $question->correct_answer) ✓ @endif
                                                </div>
                                                {{ $choice }}
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="mt-2">
                                        <p class="text-sm font-semibold text-slate-500 mb-1">Correct Answer:</p>
                                        <div class="p-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl font-medium">
                                            {{ $question->correct_answer }}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                        
                        @if($quiz->questions->isEmpty())
                            <div class="text-center py-10">
                                <p class="text-slate-400">This quiz doesn't have any questions yet.</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- ─── Student Attempts with Proctoring Flags ─── --}}
                @php
                    $allAttempts = $quiz->assignments->flatMap(fn($a) => $a->attempts)->sortByDesc('score');
                    $flaggedCount = $allAttempts->where('violations', '>', 0)->count();
                @endphp

                @if($allAttempts->isNotEmpty())
                <div class="bg-white rounded-[2rem] border border-gray-100 p-8 shadow-sm">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-xl font-bold text-slate-700 flex items-center gap-2">
                                🏆 Student Attempts
                                <span class="text-sm font-semibold text-slate-400 ml-1">({{ $allAttempts->count() }} total)</span>
                            </h3>
                        </div>
                        @if($flaggedCount > 0)
                            <span class="px-4 py-2 bg-red-50 border border-red-200 text-red-700 text-sm font-bold rounded-xl flex items-center gap-2">
                                ⚠️ {{ $flaggedCount }} flagged attempt{{ $flaggedCount > 1 ? 's' : '' }}
                            </span>
                        @endif
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-xs font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100">
                                    <th class="pb-3 text-left">#</th>
                                    <th class="pb-3 text-left">Student</th>
                                    <th class="pb-3 text-left">Class</th>
                                    <th class="pb-3 text-center">Score</th>
                                    <th class="pb-3 text-center">Time</th>
                                    <th class="pb-3 text-center">Proctoring</th>
                                    <th class="pb-3 text-right">Submitted</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @foreach($allAttempts as $rank => $attempt)
                                    <tr class="hover:bg-slate-50/50 transition-colors {{ $attempt->violations > 0 ? 'bg-red-50/30' : '' }}">
                                        <td class="py-4 pr-4">
                                            <span class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm
                                                {{ $rank === 0 ? 'bg-yellow-100 text-yellow-700' : ($rank === 1 ? 'bg-gray-100 text-gray-600' : ($rank === 2 ? 'bg-amber-100 text-amber-700' : 'bg-slate-50 text-slate-500')) }}">
                                                {{ $rank === 0 ? '🥇' : ($rank === 1 ? '🥈' : ($rank === 2 ? '🥉' : $rank + 1)) }}
                                            </span>
                                        </td>
                                        <td class="py-4 pr-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center overflow-hidden border border-emerald-200 shrink-0">
                                                    @if($attempt->student->profile_picture)
                                                        <img src="{{ asset('storage/' . $attempt->student->profile_picture) }}" class="w-full h-full object-cover">
                                                    @else
                                                        <span class="text-xs">👤</span>
                                                    @endif
                                                </div>
                                                <span class="font-semibold text-slate-800">
                                                    {{ $attempt->student->firstname }} {{ $attempt->student->lastname }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="py-4 pr-4">
                                            <span class="text-slate-500">{{ $attempt->quizAssignment->classroom->name ?? '—' }}</span>
                                        </td>
                                        <td class="py-4 text-center">
                                            <span class="font-black text-slate-800">{{ $attempt->score }}/{{ $attempt->total_questions }}</span>
                                            <span class="text-xs text-slate-400 ml-1">({{ $attempt->score_percentage }}%)</span>
                                        </td>
                                        <td class="py-4 text-center">
                                            <span class="font-mono text-slate-600">{{ gmdate('i:s', $attempt->time_taken) }}</span>
                                        </td>
                                        <td class="py-4 text-center">
                                            @if($attempt->violations > 0)
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-red-100 border border-red-300 text-red-700 rounded-lg text-xs font-black">
                                                    ⚠️ {{ $attempt->violations }} violation{{ $attempt->violations > 1 ? 's' : '' }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-3 py-1 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-lg text-xs font-bold">
                                                    ✓ Clean
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-4 text-right text-slate-400 text-xs">
                                            {{ $attempt->completed_at->format('M d, g:i A') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

            </div>
        </main>
    </div>
</x-layout>
