<x-app-shell>
    <div class="max-w-5xl mx-auto flex flex-col">

        {{-- Back Button --}}
        <a href="{{ route('teacher.dashboard') }}" class="inline-flex items-center py-2 -my-2 text-slate-400 hover:text-slate-600 transition-colors mb-6 text-sm font-medium">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            Back to Dashboard
        </a>

        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-10 gap-4">
            <div class="min-w-0">
                <div class="flex flex-wrap items-center gap-x-3 gap-y-2 mb-2">
                    <h2 class="text-2xl sm:text-3xl font-bold text-slate-800 break-words min-w-0">{{ $classroom->name }}</h2>
                    <span class="font-mono bg-slate-100 px-3 py-1 rounded-lg text-sm text-slate-600 font-bold">{{ $classroom->code }}</span>
                </div>
                @if($classroom->description)
                    <p class="text-slate-500">{{ $classroom->description }}</p>
                @endif
                <p class="text-slate-400 text-sm mt-1">{{ $classroom->students->count() }} {{ Str::plural('student', $classroom->students->count()) }} enrolled • {{ $classroom->quizAssignments->count() }} {{ Str::plural('quiz', $classroom->quizAssignments->count()) }} assigned</p>
            </div>
            <div class="flex items-center gap-3">
                <form method="POST" action="{{ route('teacher.classroom.destroy', $classroom) }}" onsubmit="return confirm('Are you sure you want to delete this class?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-50 text-red-600 font-medium rounded-xl hover:bg-red-100 transition-colors text-sm">
                        Delete Class
                    </button>
                </form>
            </div>
        </div>

        {{-- Messages --}}
        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-3 rounded-2xl mb-6 font-medium text-sm"
                 x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                ✓ {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-6 py-3 rounded-2xl mb-6 font-medium text-sm">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        {{-- Add Student Form --}}
        <div class="bg-white rounded-[2rem] border border-gray-100 p-6 mb-8 shadow-sm" x-data="{ showAdd: false }">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-bold text-slate-700">Add Students</h3>
                <button @click="showAdd = !showAdd" class="py-2 px-3 -my-2 -mr-3 rounded-lg text-sm text-emerald-600 font-semibold hover:text-emerald-700 transition-colors">
                    <span x-text="showAdd ? 'Cancel' : '＋ Add Student'"></span>
                </button>
            </div>
            <div x-show="showAdd" x-cloak x-transition class="mt-4">
                <form method="POST" action="{{ route('teacher.classroom.addStudent', $classroom) }}" class="flex flex-col sm:flex-row gap-3 sm:gap-4 sm:items-end">
                    @csrf
                    <div class="flex-1 min-w-0">
                        <label class="block text-sm font-semibold text-slate-600 mb-2">Student Email</label>
                        <input type="email" name="email" placeholder="student@example.com" required
                               class="w-full p-3 bg-slate-50 border border-slate-100 rounded-xl text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-400/30 focus:border-emerald-300 transition-all">
                    </div>
                    <button type="submit" class="px-6 py-3 bg-emerald-500 text-white font-bold rounded-xl hover:bg-emerald-600 transition-all shadow-md">
                        Add
                    </button>
                </form>
                <p class="text-slate-400 text-xs mt-2">Students can also join using the class code: <span class="font-mono font-bold text-slate-600">{{ $classroom->code }}</span></p>
            </div>
        </div>

        {{-- Student Rankings Table --}}
        <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden mb-8">
            <div class="p-6 border-b border-gray-50">
                <h3 class="text-lg font-bold text-slate-700">Student Rankings</h3>
                <p class="text-slate-400 text-sm mt-1">Ranked by highest score, then shortest time</p>
            </div>

            @if($studentRankings->isEmpty())
                <div class="text-center py-14">
                    <div class="text-4xl mb-3">👨‍🎓</div>
                    <h4 class="text-base font-bold text-slate-700">No students yet</h4>
                    <p class="text-slate-400 text-sm mt-1">Add students to this class to see their rankings.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="text-left text-xs font-bold text-slate-400 uppercase tracking-wider">
                                <th class="px-2 sm:px-6 py-4">Rank</th>
                                <th class="px-2 sm:px-6 py-4">Student</th>
                                <th class="px-2 sm:px-6 py-4">Best</th>
                                <th class="px-2 sm:px-6 py-4 hidden md:table-cell lg:hidden xl:table-cell">Avg Score</th>
                                <th class="px-2 sm:px-6 py-4 hidden xl:table-cell">Best Time</th>
                                <th class="px-2 sm:px-6 py-4 hidden xl:table-cell">Attempts</th>
                                <th class="px-2 sm:px-6 py-4"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($studentRankings as $rank => $entry)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-2 sm:px-6 py-4">
                                        @if($rank === 0)
                                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-yellow-100 text-yellow-700 font-bold text-sm">🥇</span>
                                        @elseif($rank === 1)
                                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 text-gray-600 font-bold text-sm">🥈</span>
                                        @elseif($rank === 2)
                                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-amber-100 text-amber-700 font-bold text-sm">🥉</span>
                                        @else
                                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-slate-100 text-slate-600 font-bold text-sm">{{ $rank + 1 }}</span>
                                        @endif
                                    </td>
                                    <td class="px-2 sm:px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="hidden sm:flex w-10 h-10 shrink-0 rounded-full bg-emerald-100 items-center justify-center overflow-hidden border border-emerald-200">
                                                @if($entry['student']->profile_picture)
                                                    <img src="{{ asset('storage/' . $entry['student']->profile_picture) }}" class="w-full h-full object-cover" alt="">
                                                @else
                                                    <span class="text-sm">👤</span>
                                                @endif
                                            </div>
                                            <div class="min-w-0 max-w-[8rem] sm:max-w-[14rem] lg:max-w-[11rem] xl:max-w-[14rem]">
                                                <p class="font-semibold text-slate-800 truncate">{{ $entry['student']->firstname }} {{ $entry['student']->lastname }}</p>
                                                <p class="text-xs text-slate-400 truncate">{{ $entry['student']->email }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-2 sm:px-6 py-4">
                                        <span class="font-bold text-slate-800">{{ $entry['best_score'] }}</span>
                                    </td>
                                    <td class="px-2 sm:px-6 py-4 hidden md:table-cell lg:hidden xl:table-cell">
                                        <span class="text-slate-600">{{ $entry['avg_score'] }}%</span>
                                    </td>
                                    <td class="px-2 sm:px-6 py-4 hidden xl:table-cell">
                                        @if($entry['best_time'] > 0)
                                            <span class="text-slate-600">{{ gmdate('i:s', $entry['best_time']) }}</span>
                                        @else
                                            <span class="text-slate-300">—</span>
                                        @endif
                                    </td>
                                    <td class="px-2 sm:px-6 py-4 hidden xl:table-cell">
                                        <span class="text-slate-600">{{ $entry['attempt_count'] }}</span>
                                    </td>
                                    <td class="px-2 sm:px-6 py-4">
                                        <form method="POST" action="{{ route('teacher.classroom.removeStudent', [$classroom, $entry['student']]) }}"
                                              onsubmit="return confirm('Remove this student from the class?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="relative px-2.5 sm:px-3 py-2 -my-2 rounded-lg text-red-400 hover:text-red-600 hover:bg-red-50 transition-colors text-sm" title="Remove from class">
                                                <span class="sm:hidden material-symbols-rounded text-xl leading-none" aria-hidden="true">person_remove</span>
                                                <span class="sr-only sm:not-sr-only">Remove</span>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- Assigned Quizzes --}}
        <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden mb-8">
            <div class="p-6 border-b border-gray-50">
                <h3 class="text-lg font-bold text-slate-700">Assigned Quizzes</h3>
            </div>

            @if($classroom->quizAssignments->isEmpty())
                <div class="text-center py-10">
                    <p class="text-slate-400 text-sm">No quizzes assigned to this class yet.</p>
                    <a href="{{ route('teacher.quiz.index') }}" class="inline-block mt-3 text-emerald-600 text-sm font-semibold hover:text-emerald-700">Assign a Quiz →</a>
                </div>
            @else
                <div class="divide-y divide-gray-50">
                    @foreach($classroom->quizAssignments as $assignment)
                        <div class="p-6 flex justify-between items-center hover:bg-slate-50/50 transition-colors">
                            <div>
                                <h4 class="font-semibold text-slate-800">{{ $assignment->quiz->title }}</h4>
                                <p class="text-sm text-slate-400 mt-1">
                                    Assigned {{ $assignment->assigned_at->diffForHumans() }}
                                    @if($assignment->due_at)
                                        • Due {{ $assignment->due_at->format('M d, Y') }}
                                    @endif
                                    • {{ $assignment->attempts->count() }} {{ Str::plural('attempt', $assignment->attempts->count()) }}
                                </p>
                            </div>
                            <form method="POST" action="{{ route('teacher.quiz.unassign', $assignment) }}"
                                  onsubmit="return confirm('Remove this quiz assignment?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="shrink-0 px-3 py-2 -my-2 rounded-lg text-red-400 hover:text-red-600 hover:bg-red-50 transition-colors text-sm font-medium">
                                    Unassign
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>
</x-app-shell>
