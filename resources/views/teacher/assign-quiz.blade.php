<x-layout>
    <div class="flex min-h-screen bg-[#F9FAFB]">
        <x-teacher-sidebar/>
        <main class="flex-1 px-6 pb-6 pt-20 md:p-12 overflow-y-auto relative">
            <x-dropdown-profile/>
            <div class="max-w-5xl mx-auto flex flex-col" x-data="assignQuiz()">

                {{-- Header --}}
                <div class="flex flex-col mt-16 lg:mt-0 mb-10">
                    <div>
                        <h2 class="text-3xl font-bold text-slate-800">Assign Quiz</h2>
                        <p class="text-slate-500 mt-2">Create, manage, and assign quizzes to your classes</p>
                    </div>
                </div>

                {{-- Messages --}}
                @if(session('success'))
                    <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-3 rounded-2xl mb-6 font-medium text-sm" x-data="{s:true}" x-show="s" x-init="setTimeout(()=>s=false,4000)" x-transition>✓ {{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="bg-red-50 border border-red-200 text-red-700 px-6 py-3 rounded-2xl mb-6 font-medium text-sm">{{ session('error') }}</div>
                @endif
                @if(session('info'))
                    <div class="bg-blue-50 border border-blue-200 text-blue-700 px-6 py-3 rounded-2xl mb-6 font-medium text-sm">{{ session('info') }}</div>
                @endif
                @if ($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-700 px-6 py-3 rounded-2xl mb-6 font-medium text-sm">
                        {{ $errors->first() }}
                    </div>
                @endif

                @if(session('waiting_for_quiz'))
                    <div id="quiz-processing-notification"
                        class="mb-8 p-6 bg-violet-50 border-2 border-dashed border-violet-200 rounded-[2rem] flex items-center justify-between animate-pulse">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 flex items-center justify-center bg-violet-500 rounded-full text-white">
                                <svg class="animate-spin h-5 w-5" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-violet-900">AI is processing your quiz...</h3>
                                <p class="text-violet-600 text-sm">The queue is running in the background. You can continue using the app.</p>
                            </div>
                        </div>
                        <span class="px-4 py-1.5 bg-white/50 text-violet-700 text-xs font-bold uppercase tracking-wider rounded-full border border-violet-100">
                            Background Process
                        </span>
                    </div>

                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const oldId = "{{ session('waiting_for_quiz') }}";

                            const checkInterval = setInterval(() => {
                                fetch(`/api/check-new-quiz/${oldId}`)
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.is_ready) {
                                            clearInterval(checkInterval);
                                            document.querySelector('#quiz-processing-notification h3').innerText = "Redirecting...";
                                            window.location.href = `/teacher/quizzes/${data.quiz_id}`;
                                        }
                                    })
                                    .catch(error => console.error('Error checking quiz status:', error));
                            }, 2000);
                        });
                    </script>
                @endif

                {{-- Tabs --}}
                <div class="flex space-x-1 bg-white rounded-2xl p-1.5 border border-gray-100 shadow-sm mb-8 w-fit">
                    <button @click="tab='quizzes'" :class="tab==='quizzes' ? 'bg-slate-900 text-white shadow-lg' : 'text-slate-500 hover:text-slate-700'" class="px-5 py-2.5 rounded-xl font-semibold text-sm transition-all">My Quizzes</button>
                    <button @click="tab='create'" :class="tab==='create' ? 'bg-slate-900 text-white shadow-lg' : 'text-slate-500 hover:text-slate-700'" class="px-5 py-2.5 rounded-xl font-semibold text-sm transition-all">Create Quiz</button>
                    <button @click="tab='ai'" :class="tab==='ai' ? 'bg-slate-900 text-white shadow-lg' : 'text-slate-500 hover:text-slate-700'" class="px-5 py-2.5 rounded-xl font-semibold text-sm transition-all">✨ AI Generate</button>
                </div>

                {{-- TAB 1: My Quizzes --}}
                <div x-show="tab==='quizzes'" x-transition>
                    <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
                        <h3 class="text-xl font-bold text-slate-700">My Quizzes</h3>

                        {{-- Search Bar --}}
                        <form method="GET" action="{{ route('teacher.quiz.index') }}" class="flex w-full md:w-auto"
                              x-data="{ query: '{{ request('search') }}', search() {
                                  let url = new URL('{{ route('teacher.quiz.index') }}');
                                  if (this.query) { url.searchParams.set('search', this.query); }
                                  fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                                      .then(res => res.text())
                                      .then(html => {
                                          let doc = new DOMParser().parseFromString(html, 'text/html');
                                          document.getElementById('assign-quiz-container').innerHTML = doc.getElementById('assign-quiz-container').innerHTML;
                                          window.history.pushState({}, '', url);
                                      });
                              } }" @submit.prevent="search">
                            <input type="text" name="search" x-model="query" @input.debounce.500ms="search" placeholder="Search quizzes..." class="w-full md:w-64 p-2 bg-white border border-gray-200 rounded-xl text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-400/30 focus:border-emerald-300 transition-all shadow-sm">
                            <button type="submit" class="ml-2 px-4 py-2 bg-emerald-500 text-white font-bold rounded-xl shadow-sm hover:bg-emerald-600 transition-colors">
                                Search
                            </button>
                        </form>
                    </div>

                    <div id="assign-quiz-container">
                    @if($quizzes->isEmpty())
                        <div class="text-center py-14 bg-white rounded-3xl border border-gray-100 shadow-sm">
                            <div class="text-4xl mb-3">📝</div>
                            <h4 class="text-base font-bold text-slate-700">No quizzes found</h4>
                            <p class="text-slate-400 text-sm mt-1">@if(request('search')) Try a different search term! @else Create your first quiz to get started! @endif</p>
                            @if(!request('search'))
                            <button @click="tab='create'" class="inline-block mt-5 px-6 py-2 bg-emerald-500 text-white text-sm font-semibold rounded-full hover:bg-emerald-600 transition-colors shadow-lg shadow-emerald-200">Create a Quiz</button>
                            @endif
                        </div>
                    @else
                        @php $colors = ['bg-red-400','bg-blue-500','bg-emerald-500','bg-amber-400','bg-violet-500','bg-cyan-400']; @endphp
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                            @foreach($quizzes as $i => $quiz)
                                <div class="group relative flex flex-col bg-white border border-gray-100 rounded-[2rem] overflow-hidden shadow-sm hover:shadow-xl hover:shadow-slate-200/50 transition-all duration-300 transform hover:-translate-y-1">
                                    <div class="h-3 {{ $colors[$i % count($colors)] }}"></div>
                                    <div class="flex-1 flex flex-col">
                                        <div class="flex-1 p-8 pb-5">
                                            <div class="flex justify-between items-start gap-3 mb-4">
                                                <div class="min-w-0">
                                                    <a href="{{ route('teacher.quiz.show', $quiz) }}" class="text-xl font-bold text-slate-800 hover:text-blue-600 transition-colors block truncate" title="{{ $quiz->title }}">{{ $quiz->title }}</a>
                                                    <p class="text-slate-400 font-medium text-sm mt-1">{{ $quiz->questions_count }} {{ Str::plural('Question', $quiz->questions_count) }} @if($quiz->time_limit) • {{ $quiz->time_limit }} Min @endif</p>
                                                </div>
                                                <span class="shrink-0 px-2 py-1 text-xs font-bold rounded-full {{ $quiz->is_active ? 'bg-emerald-50 text-emerald-600' : 'bg-gray-100 text-gray-500' }}">{{ $quiz->is_active ? 'Active' : 'Off' }}</span>
                                            </div>
                                            @if($quiz->description)
                                                <p class="text-gray-500 text-sm mt-2 line-clamp-2">{{ $quiz->description }}</p>
                                            @endif
                                            @if($quiz->assignments->isNotEmpty())
                                                <p class="text-xs text-blue-500 font-medium mt-4 line-clamp-2">Assigned to: {{ $quiz->assignments->pluck('classroom.name')->join(', ') }}</p>
                                            @endif
                                        </div>
                                        <div class="p-8 pt-0 mt-auto">
                                            <button @click="openAssignModal({{ $quiz->id }}, '{{ addslashes($quiz->title) }}')" class="w-full py-3 bg-slate-50 text-slate-700 font-semibold rounded-xl group-hover:bg-blue-50 group-hover:text-blue-600 border border-slate-100 transition-colors">Assign to Class</button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Inlined Pagination --}}
                        @if ($quizzes->hasPages())
                            <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-center space-x-2 mt-12 mb-20">
                                {{-- Previous Page Link --}}
                                @if ($quizzes->onFirstPage())
                                    <span class="px-4 py-2 text-slate-300 bg-white border border-gray-100 rounded-2xl cursor-not-allowed">
                                        ← <span class="hidden md:inline ml-1">Prev</span>
                                    </span>
                                @else
                                    <a href="{{ $quizzes->previousPageUrl() }}" class="px-4 py-2 text-slate-600 bg-white border border-gray-100 rounded-2xl hover:bg-slate-50 hover:border-emerald-300 transition-all shadow-sm">
                                        ← <span class="hidden md:inline ml-1">Prev</span>
                                    </a>
                                @endif

                                {{-- Pagination Elements --}}
                                <div class="flex items-center bg-white border border-gray-100 rounded-2xl p-1 shadow-sm">
                                    @foreach ($quizzes->getUrlRange(max(1, $quizzes->currentPage() - 1), min($quizzes->lastPage(), $quizzes->currentPage() + 1)) as $page => $url)
                                        @if ($page == $quizzes->currentPage())
                                            <span class="w-10 h-10 flex items-center justify-center bg-emerald-500 text-white font-bold rounded-xl shadow-md shadow-emerald-200">
                                                {{ $page }}
                                            </span>
                                        @else
                                            <a href="{{ $url }}" class="w-10 h-10 flex items-center justify-center text-slate-500 font-semibold rounded-xl hover:bg-slate-50 hover:text-emerald-600 transition-all">
                                                {{ $page }}
                                            </a>
                                        @endif
                                    @endforeach
                                </div>

                                {{-- Next Page Link --}}
                                @if ($quizzes->hasMorePages())
                                    <a href="{{ $quizzes->nextPageUrl() }}" class="px-4 py-2 text-slate-600 bg-white border border-gray-100 rounded-2xl hover:bg-slate-50 hover:border-emerald-300 transition-all shadow-sm">
                                        <span class="hidden md:inline mr-1">Next</span> →
                                    </a>
                                @else
                                    <span class="px-4 py-2 text-slate-300 bg-white border border-gray-100 rounded-2xl cursor-not-allowed">
                                        <span class="hidden md:inline mr-1">Next</span> →
                                    </span>
                                @endif
                            </nav>
                        @endif
                    @endif
                    </div>
                </div>

                {{-- TAB 2: Create Quiz (Manual) --}}
                <div x-show="tab==='create'" x-transition x-cloak>
                    <form method="POST" action="{{ route('teacher.quiz.store') }}">
                        @csrf
                        <div class="bg-white rounded-[2rem] border border-gray-100 p-8 shadow-sm mb-6">
                            <h3 class="text-lg font-bold text-slate-700 mb-6">Quiz Details</h3>
                            <div class="space-y-5">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-600 mb-2">Quiz Title</label>
                                    <input type="text" name="title" required placeholder="e.g. Laravel Framework Exam" class="w-full p-4 bg-slate-50 border border-slate-100 rounded-2xl text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-400/30 focus:border-emerald-300 transition-all">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-600 mb-2">Description (optional)</label>
                                    <textarea name="description" rows="2" placeholder="What is this quiz about?" class="w-full p-4 bg-slate-50 border border-slate-100 rounded-2xl text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-400/30 focus:border-emerald-300 transition-all resize-none"></textarea>
                                </div>
                                <div class="w-48">
                                    <label class="block text-sm font-semibold text-slate-600 mb-2">Time Limit (min)</label>
                                    <input type="number" name="time_limit" min="1" max="180" placeholder="30" class="w-full p-4 bg-slate-50 border border-slate-100 rounded-2xl text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-400/30 focus:border-emerald-300 transition-all">
                                </div>
                            </div>
                        </div>

                        {{-- Questions Builder --}}
                        <div class="bg-white rounded-[2rem] border border-gray-100 p-8 shadow-sm mb-6">
                            <div class="flex justify-between items-center mb-6">
                                <h3 class="text-lg font-bold text-slate-700">Questions</h3>
                                <span class="text-sm text-slate-400" x-text="questions.length + ' question(s)'"></span>
                            </div>

                            <template x-for="(q, idx) in questions" :key="idx">
                                <div class="border border-gray-100 rounded-2xl p-6 mb-4 bg-slate-50/50 relative">
                                    <div class="flex justify-between items-center mb-4">
                                        <span class="text-sm font-bold text-slate-500" x-text="'Question ' + (idx+1)"></span>
                                        <div class="flex items-center gap-3">
                                            <select x-model="q.type" class="text-sm bg-white border border-slate-200 rounded-xl px-3 py-1.5 text-slate-600 focus:outline-none focus:ring-2 focus:ring-emerald-400/30">
                                                <option value="multiple_choice">Multiple Choice</option>
                                                <option value="identification">Identification</option>
                                            </select>
                                            <button type="button" @click="removeQuestion(idx)" x-show="questions.length > 1" class="text-red-400 hover:text-red-600 text-sm font-medium transition-colors">✕ Remove</button>
                                        </div>
                                    </div>

                                    <input type="hidden" :name="'questions['+idx+'][type]'" :value="q.type">

                                    <div class="mb-4">
                                        <label class="block text-sm font-semibold text-slate-600 mb-2">Question</label>
                                        <input type="text" :name="'questions['+idx+'][question]'" x-model="q.question" required placeholder="Enter your question..." class="w-full p-3 bg-white border border-slate-100 rounded-xl text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-400/30 transition-all">
                                    </div>

                                    {{-- MC Choices --}}
                                    <template x-if="q.type === 'multiple_choice'">
                                        <div class="space-y-3">
                                            <label class="block text-sm font-semibold text-slate-600">Choices</label>
                                            <template x-for="(c, ci) in q.choices" :key="ci">
                                                <div class="flex items-center gap-3">
                                                    <input type="radio" :name="'questions['+idx+'][correct_choice_index]'" :value="ci" required class="text-emerald-500 focus:ring-emerald-400">
                                                    <input type="text" :name="'questions['+idx+'][choices]['+ci+']'" x-model="q.choices[ci]" required :placeholder="'Choice ' + (ci+1)" class="flex-1 p-3 bg-white border border-slate-100 rounded-xl text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-400/30 transition-all">
                                                </div>
                                            </template>
                                            <p class="text-xs text-slate-400">Select the radio button next to the correct answer</p>
                                        </div>
                                    </template>

                                    {{-- Identification --}}
                                    <template x-if="q.type === 'identification'">
                                        <div>
                                            <label class="block text-sm font-semibold text-slate-600 mb-2">Correct Answer</label>
                                            <input type="text" :name="'questions['+idx+'][correct_answer]'" x-model="q.correct_answer" required placeholder="Type the correct answer..." class="w-full p-3 bg-white border border-slate-100 rounded-xl text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-400/30 transition-all">
                                        </div>
                                    </template>
                                </div>
                            </template>

                            <button type="button" @click="addQuestion()" class="w-full py-3 border-2 border-dashed border-slate-200 rounded-2xl text-slate-400 font-semibold hover:border-emerald-300 hover:text-emerald-600 transition-all">＋ Add Question</button>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="px-8 py-3 bg-emerald-500 text-white font-bold rounded-2xl shadow-lg shadow-emerald-200 hover:bg-emerald-600 hover:shadow-xl active:scale-95 transition-all">Save Quiz</button>
                        </div>
                    </form>
                </div>

                {{-- TAB 3: AI Generate --}}
                <div x-show="tab==='ai'" x-transition x-cloak>
                    <form method="POST" action="{{ route('teacher.quiz.ai.generate') }}" enctype="multipart/form-data"
                          x-data="aiUpload()"
                          @submit.prevent="if (!tooLarge) $event.target.submit()">
                        @csrf
                        <div class="bg-white rounded-[2rem] border border-gray-100 p-8 shadow-sm mb-6">
                            <div class="text-center mb-8">
                                <div class="w-16 h-16 bg-violet-100 rounded-2xl flex items-center justify-center mx-auto mb-4 text-3xl">🤖</div>
                                <h3 class="text-xl font-bold text-slate-800">Generate Quiz with AI</h3>
                                <p class="text-slate-400 text-sm mt-2">Upload your notes, PDFs, or paste content and let AI create quiz questions for you</p>
                            </div>

                            {{-- File Upload Area --}}
                            <div class="mb-6">
                                <label class="block text-sm font-semibold text-slate-600 mb-2">Upload Files</label>
                                <div @dragover.prevent="dragging=true" @dragleave="dragging=false" @drop.prevent="handleDrop($event)"
                                     :class="tooLarge ? 'border-red-400 bg-red-50/40' : (dragging ? 'border-emerald-400 bg-emerald-50' : 'border-slate-200 bg-slate-50')"
                                     class="border-2 border-dashed rounded-2xl p-10 text-center transition-all cursor-pointer"
                                     @click="$refs.fileInput.click()">
                                    <input type="file" name="files[]" accept=".pdf,.txt" class="hidden" x-ref="fileInput" @change="handleFiles($event)">
                                    <div class="text-4xl mb-3">📁</div>
                                    <p class="font-semibold text-slate-600">Drop files here or click to upload</p>
                                    <p class="text-sm text-slate-400 mt-1">PDF or TXT - Max 10MB</p>
                                </div>
                                <template x-if="files.length > 0">
                                    <div class="mt-4 space-y-2">
                                        <template x-for="(f, i) in files" :key="i">
                                            <div class="flex items-center justify-between bg-slate-50 px-4 py-2 rounded-xl">
                                                <div class="min-w-0">
                                                    <span class="text-sm truncate block" :class="f.size > maxSize ? 'text-red-600' : 'text-slate-600'" x-text="f.name"></span>
                                                    <span class="text-xs" :class="f.size > maxSize ? 'text-red-400' : 'text-slate-400'" x-text="f.size > maxSize ? 'File is over 10MB limit' : 'Ready to upload'"></span>
                                                </div>
                                                <button type="button" @click="removeFile(i)" class="text-red-400 hover:text-red-600 text-xs">✕</button>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>

                            {{-- Paste Content --}}
                            <div class="mb-6">
                                <label class="block text-sm font-semibold text-slate-600 mb-2">Or Paste Content</label>
                                <textarea name="content" rows="5" placeholder="Paste your notes, text, or study material here..." class="w-full p-4 bg-slate-50 border border-slate-100 rounded-2xl text-slate-700 focus:outline-none focus:ring-2 focus:ring-violet-400/30 focus:border-violet-300 transition-all resize-none"></textarea>
                            </div>

                            {{-- Settings --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-600 mb-2">Number of Questions</label>
                                    <select name="count" class="w-full p-3 bg-slate-50 border border-slate-100 rounded-xl text-slate-700 focus:outline-none">
                                        <option value="5">5 Questions</option>
                                        <option value="10" selected>10 Questions</option>
                                        <option value="15" selected>15 Questions</option>
                                        <option value="20" selected>20 Questions</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-600 mb-2">Question Type</label>
                                    <select class="w-full p-3 bg-slate-50 border border-slate-100 rounded-xl text-slate-700 focus:outline-none">
                                        <option>Multiple Choice Only</option>
                                        <option>Identification Only</option>
                                        <option selected>Mixed (Both)</option>
                                    </select>
                                </div>
                            </div>

                            <button type="submit"
                                    :disabled="tooLarge"
                                    :class="tooLarge ? 'bg-gray-300 cursor-not-allowed shadow-none' : 'bg-gradient-to-r from-violet-500 to-purple-600 shadow-violet-200 hover:shadow-xl hover:from-violet-600 hover:to-purple-700 active:scale-[0.98]'"
                                    class="w-full py-4 text-white font-bold rounded-2xl shadow-lg transition-all text-lg">
                                ✨ Generate Quiz
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Assign Modal --}}
                <div x-show="showAssignModal" x-cloak class="fixed inset-0 z-[70] flex items-center justify-center"
                     x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="showAssignModal=false"></div>
                    <div class="relative bg-white rounded-[2rem] shadow-2xl w-full max-w-md mx-4 p-8">
                        <h3 class="text-xl font-bold text-slate-800 mb-2">Assign Quiz</h3>
                        <p class="text-slate-400 text-sm mb-6">Assign "<span x-text="assignQuizTitle" class="font-semibold text-slate-600"></span>" to a class</p>
                        <form method="POST" action="{{ route('teacher.quiz.assign') }}">
                            @csrf
                            <input type="hidden" name="quiz_id" :value="assignQuizId">
                            <div class="mb-4">
                                <label class="block text-sm font-semibold text-slate-600 mb-2">Select Class</label>
                                <select name="classroom_id" required class="w-full p-3 bg-slate-50 border border-slate-100 rounded-xl text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-400/30">
                                    <option value="">Choose a class...</option>
                                    @foreach($classrooms as $c)
                                        <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->code }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-6">
                                <label class="block text-sm font-semibold text-slate-600 mb-2">Due Date (optional)</label>
                                <input type="datetime-local" name="due_at" class="w-full p-3 bg-slate-50 border border-slate-100 rounded-xl text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-400/30">
                            </div>
                            <div class="flex gap-3">
                                <button type="button" @click="showAssignModal=false" class="flex-1 py-3 bg-slate-100 text-slate-600 font-bold rounded-2xl hover:bg-slate-200 transition-all">Cancel</button>
                                <button type="submit" class="flex-1 py-3 bg-emerald-500 text-white font-bold rounded-2xl hover:bg-emerald-600 shadow-md transition-all">Assign</button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <script>
    function assignQuiz() {
        return {
            tab: 'quizzes',
            showAssignModal: false,
            assignQuizId: null,
            assignQuizTitle: '',
            questions: [{ type: 'multiple_choice', question: '', correct_answer: '', choices: ['', '', '', ''] }],
            addQuestion() {
                this.questions.push({ type: 'multiple_choice', question: '', correct_answer: '', choices: ['', '', '', ''] });
            },
            removeQuestion(idx) { this.questions.splice(idx, 1); },
            openAssignModal(id, title) {
                this.assignQuizId = id;
                this.assignQuizTitle = title;
                this.showAssignModal = true;
            }
        }
    }
    function aiUpload() {
        return {
            maxSize: 10 * 1024 * 1024,
            dragging: false,
            files: [],
            tooLarge: false,
            handleDrop(e) {
                this.dragging = false;
                this.$refs.fileInput.files = e.dataTransfer.files;
                this.handleFiles({ target: this.$refs.fileInput });
            },
            handleFiles(e) {
                this.files = [...e.target.files];
                this.tooLarge = this.files.some(file => file.size > this.maxSize);
            },
            removeFile(index) {
                const transfer = new DataTransfer();
                this.files.filter((file, i) => i !== index).forEach(file => transfer.items.add(file));
                this.$refs.fileInput.files = transfer.files;
                this.handleFiles({ target: this.$refs.fileInput });
            }
        }
    }
    </script>
</x-layout>
