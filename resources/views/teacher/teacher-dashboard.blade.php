<x-layout>
    <div class="flex min-h-screen bg-[#F9FAFB]">

        <x-teacher-sidebar/>

        <x-dropdown-profile/>
        <main class="flex-1 p-6 md:p-12 overflow-y-auto relative">

            <div class="max-w-5xl mx-auto flex flex-col">

                {{-- Header --}}
                <div class="flex flex-col mb-10">
                    <h2 class="text-3xl md:text-4xl font-bold text-slate-800">Teacher Dashboard</h2>
                    <p class="text-slate-500 mt-2">Welcome back, {{ Auth::user()->firstname }}! Manage your quizzes and students here.</p>
                </div>

                {{-- Stats Cards --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
                    <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 flex items-center space-x-4 hover:shadow-md transition-shadow">
                        <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center text-emerald-600 text-xl font-bold">
                            📝
                        </div>
                        <div>
                            <p class="text-slate-400 text-sm font-medium">Active Quizzes</p>
                            <p class="text-2xl font-bold text-slate-800">{{ $activeQuizzes }}</p>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 flex items-center space-x-4 hover:shadow-md transition-shadow">
                        <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center text-blue-600 text-xl font-bold">
                            👨‍🎓
                        </div>
                        <div>
                            <p class="text-slate-400 text-sm font-medium">Total Students</p>
                            <p class="text-2xl font-bold text-slate-800">{{ $totalStudents }}</p>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 flex items-center space-x-4 hover:shadow-md transition-shadow">
                        <div class="w-12 h-12 bg-violet-100 rounded-xl flex items-center justify-center text-violet-600 text-xl font-bold">
                            🏫
                        </div>
                        <div>
                            <p class="text-slate-400 text-sm font-medium">Total Classes</p>
                            <p class="text-2xl font-bold text-slate-800">{{ $totalClasses }}</p>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 flex items-center space-x-4 hover:shadow-md transition-shadow">
                        <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center text-yellow-600 text-xl font-bold">
                            ⭐
                        </div>
                        <div>
                            <p class="text-slate-400 text-sm font-medium">Avg Score</p>
                            <p class="text-2xl font-bold text-slate-800">{{ $avgScore }}%</p>
                        </div>
                    </div>
                </div>

                {{-- My Classes Section --}}
                <div class="mb-12" x-data="{ showCreateClass: false }">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-bold text-slate-700">My Classes</h3>
                        <button @click="showCreateClass = !showCreateClass" class="flex items-center space-x-2 px-5 py-2 bg-slate-900 text-white rounded-2xl shadow-lg shadow-slate-200 hover:bg-slate-800 transition-all text-sm font-semibold">
                            <span class="text-lg">＋</span>
                            <span>New Class</span>
                        </button>
                    </div>

                    {{-- Create Class Form --}}
                    <div x-show="showCreateClass" x-cloak
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="bg-white rounded-[2rem] border border-gray-100 p-6 mb-6 shadow-sm">
                        <form method="POST" action="{{ route('teacher.classroom.store') }}" class="flex flex-col md:flex-row gap-4 items-end">
                            @csrf
                            <div class="flex-1">
                                <label class="block text-sm font-semibold text-slate-600 mb-2">Class Name</label>
                                <input type="text" name="name" placeholder="e.g. IT9a" required
                                       class="w-full p-3 bg-slate-50 border border-slate-100 rounded-xl text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-400/30 focus:border-emerald-300 transition-all">
                            </div>
                            <div class="flex-1">
                                <label class="block text-sm font-semibold text-slate-600 mb-2">Description (optional)</label>
                                <input type="text" name="description" placeholder="e.g. Information Technology Section A"
                                       class="w-full p-3 bg-slate-50 border border-slate-100 rounded-xl text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-400/30 focus:border-emerald-300 transition-all">
                            </div>
                            <button type="submit" class="px-6 py-3 bg-emerald-500 text-white font-bold rounded-xl hover:bg-emerald-600 transition-all shadow-md whitespace-nowrap">
                                Create Class
                            </button>
                        </form>
                    </div>

                    {{-- Success / Error Messages --}}
                    @if(session('success'))
                        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-3 rounded-2xl mb-6 font-medium text-sm"
                             x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                             x-transition:leave="transition ease-in duration-200"
                             x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                            ✓ {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="bg-red-50 border border-red-200 text-red-700 px-6 py-3 rounded-2xl mb-6 font-medium text-sm">
                            {{ session('error') }}
                        </div>
                    @endif

                    {{-- Class Cards --}}
                    @if($classrooms->isEmpty())
                        <div class="text-center py-14 bg-white rounded-3xl border border-gray-100 shadow-sm">
                            <div class="text-4xl mb-3">🏫</div>
                            <h4 class="text-base font-bold text-slate-700">No classes yet</h4>
                            <p class="text-slate-400 text-sm mt-1">Create your first class to start managing students!</p>
                        </div>
                    @else
                        @php
                            $accentColors = ['bg-emerald-400', 'bg-blue-400', 'bg-amber-400', 'bg-rose-400', 'bg-violet-400', 'bg-cyan-400'];
                        @endphp
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($classrooms as $index => $classroom)
                                <a href="{{ route('teacher.classroom.show', $classroom) }}"
                                   class="group relative flex flex-col bg-white border border-gray-100 rounded-[2rem] overflow-hidden shadow-sm hover:shadow-xl hover:shadow-slate-200/50 transition-all duration-300 transform hover:-translate-y-1 cursor-pointer">
                                    <div class="h-3 {{ $accentColors[$index % count($accentColors)] }}"></div>
                                    <div class="flex-1 p-6">
                                        <div class="flex justify-between items-start mb-3">
                                            <div>
                                                <h4 class="text-lg font-bold text-slate-800 group-hover:text-emerald-600 transition-colors">{{ $classroom->name }}</h4>
                                                <p class="text-slate-400 text-sm font-medium mt-1">Code: <span class="font-mono bg-slate-100 px-2 py-0.5 rounded-md text-slate-600">{{ $classroom->code }}</span></p>
                                            </div>
                                            <div class="bg-blue-50 text-blue-600 text-xs font-bold px-3 py-1 rounded-full">
                                                {{ $classroom->students_count }} {{ Str::plural('student', $classroom->students_count) }}
                                            </div>
                                        </div>
                                        @if($classroom->description)
                                            <p class="text-gray-400 text-sm mt-2 line-clamp-2">{{ $classroom->description }}</p>
                                        @endif
                                        <div class="mt-4 flex items-center text-sm text-slate-400">
                                            <span>{{ $classroom->quizAssignments->count() }} {{ Str::plural('quiz', $classroom->quizAssignments->count()) }} assigned</span>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Recent Quizzes Section --}}
                <div class="mb-8">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-bold text-slate-700">Recent Quizzes</h3>
                        <a href="{{ route('teacher.quiz.index') }}" class="text-slate-400 text-sm font-medium hover:text-slate-600 transition-colors">View all →</a>
                    </div>

                    @if($recentQuizzes->isEmpty())
                        <div class="text-center py-14 bg-white rounded-3xl border border-gray-100 shadow-sm">
                            <div class="text-4xl mb-3">📝</div>
                            <h4 class="text-base font-bold text-slate-700">No quizzes yet</h4>
                            <p class="text-slate-400 text-sm mt-1">Create your first quiz to get started!</p>
                            <a href="{{ route('teacher.quiz.create') }}" class="inline-block mt-5 px-6 py-2 bg-emerald-500 text-white text-sm font-semibold rounded-full hover:bg-emerald-600 transition-colors shadow-lg shadow-emerald-200">
                                Create a Quiz
                            </a>
                        </div>
                    @else
                        @php
                            $quizColors = ['bg-emerald-400', 'bg-blue-400', 'bg-amber-400', 'bg-rose-400', 'bg-violet-400'];
                        @endphp
                        <div class="space-y-4">
                            @foreach($recentQuizzes as $index => $quiz)
                                <div class="group flex items-center bg-white border border-gray-100 rounded-3xl overflow-hidden shadow-sm hover:shadow-md transition-shadow cursor-pointer">
                                    <div class="flex-1 p-6 flex justify-between items-center">
                                        <div>
                                            <h4 class="text-lg font-bold text-slate-800">{{ $quiz->title }}</h4>
                                            <p class="text-slate-400 text-sm mt-1">
                                                {{ $quiz->questions_count }} {{ Str::plural('question', $quiz->questions_count) }}
                                                @if($quiz->time_limit)
                                                    • {{ $quiz->time_limit }} min
                                                @endif
                                                @if($quiz->assignments->isNotEmpty())
                                                    • Assigned to {{ $quiz->assignments->pluck('classroom.name')->join(', ') }}
                                                @endif
                                            </p>
                                        </div>
                                        <div class="flex items-center space-x-3">
                                            <span class="px-3 py-1 text-xs font-bold rounded-full {{ $quiz->is_active ? 'bg-emerald-50 text-emerald-600' : 'bg-gray-100 text-gray-500' }}">
                                                {{ $quiz->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="w-2 self-stretch {{ $quizColors[$index % count($quizColors)] }}"></div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

            </div>
        </main>
    </div>
</x-layout>
