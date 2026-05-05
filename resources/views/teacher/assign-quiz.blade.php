<x-layout>
    <div class="flex min-h-screen bg-[#F9FAFB]">
        <x-teacher-sidebar/>
        <main class="flex-1 p-6 md:p-12 overflow-y-auto relative">
            <x-dropdown-profile/>
            <div class="max-w-5xl mx-auto flex flex-col" x-data="assignQuiz()">

                {{-- Header --}}
                <div class="flex flex-col md:flex-row md:items-center justify-between mb-10 gap-4">
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

                {{-- Tabs --}}
                <div class="flex space-x-1 bg-white rounded-2xl p-1.5 border border-gray-100 shadow-sm mb-8 w-fit">
                    <button @click="tab='quizzes'" :class="tab==='quizzes' ? 'bg-slate-900 text-white shadow-lg' : 'text-slate-500 hover:text-slate-700'" class="px-5 py-2.5 rounded-xl font-semibold text-sm transition-all">My Quizzes</button>
                    <button @click="tab='create'" :class="tab==='create' ? 'bg-slate-900 text-white shadow-lg' : 'text-slate-500 hover:text-slate-700'" class="px-5 py-2.5 rounded-xl font-semibold text-sm transition-all">Create Quiz</button>
                    <button @click="tab='ai'" :class="tab==='ai' ? 'bg-slate-900 text-white shadow-lg' : 'text-slate-500 hover:text-slate-700'" class="px-5 py-2.5 rounded-xl font-semibold text-sm transition-all">✨ AI Generate</button>
                </div>

                {{-- TAB 1: My Quizzes --}}
                <div x-show="tab==='quizzes'" x-transition>
                    @if($quizzes->isEmpty())
                        <div class="text-center py-14 bg-white rounded-3xl border border-gray-100 shadow-sm">
                            <div class="text-4xl mb-3">📝</div>
                            <h4 class="text-base font-bold text-slate-700">No quizzes yet</h4>
                            <p class="text-slate-400 text-sm mt-1">Create your first quiz to get started!</p>
                            <button @click="tab='create'" class="inline-block mt-5 px-6 py-2 bg-emerald-500 text-white text-sm font-semibold rounded-full hover:bg-emerald-600 transition-colors shadow-lg shadow-emerald-200">Create a Quiz</button>
                        </div>
                    @else
                        @php $colors = ['bg-red-400','bg-blue-500','bg-emerald-500','bg-amber-400','bg-violet-500','bg-cyan-400']; @endphp
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                            @foreach($quizzes as $i => $quiz)
                                <div class="group relative flex flex-col bg-white border border-gray-100 rounded-[2rem] overflow-hidden shadow-sm hover:shadow-xl hover:shadow-slate-200/50 transition-all duration-300 transform hover:-translate-y-1">
                                    <div class="h-3 {{ $colors[$i % count($colors)] }}"></div>
                                    <div class="flex-1 p-8">
                                        <div class="flex justify-between items-start mb-4">
                                            <div>
                                                <h4 class="text-xl font-bold text-slate-800 group-hover:text-blue-600 transition-colors">{{ $quiz->title }}</h4>
                                                <p class="text-slate-400 font-medium text-sm mt-1">{{ $quiz->questions_count }} {{ Str::plural('Question', $quiz->questions_count) }} @if($quiz->time_limit) • {{ $quiz->time_limit }} Min @endif</p>
                                            </div>
                                            <span class="px-2 py-1 text-xs font-bold rounded-full {{ $quiz->is_active ? 'bg-emerald-50 text-emerald-600' : 'bg-gray-100 text-gray-500' }}">{{ $quiz->is_active ? 'Active' : 'Off' }}</span>
                                        </div>
                                        @if($quiz->description)
                                            <p class="text-gray-500 text-sm mt-2 mb-4 line-clamp-2">{{ $quiz->description }}</p>
                                        @endif
                                        @if($quiz->assignments->isNotEmpty())
                                            <p class="text-xs text-blue-500 font-medium mb-4">Assigned to: {{ $quiz->assignments->pluck('classroom.name')->join(', ') }}</p>
                                        @endif
                                        <button @click="openAssignModal({{ $quiz->id }}, '{{ addslashes($quiz->title) }}')" class="w-full py-3 bg-slate-50 text-slate-700 font-semibold rounded-xl group-hover:bg-blue-50 group-hover:text-blue-600 border border-slate-100 transition-colors">Assign to Class</button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
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
                                                    <!-- Keep track of the selected index instead of the raw text -->
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
                    <form method="POST" action="{{ route('teacher.quiz.ai.generate') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="bg-white rounded-[2rem] border border-gray-100 p-8 shadow-sm mb-6">
                            <div class="text-center mb-8">
                                <div class="w-16 h-16 bg-violet-100 rounded-2xl flex items-center justify-center mx-auto mb-4 text-3xl">🤖</div>
                                <h3 class="text-xl font-bold text-slate-800">Generate Quiz with AI</h3>
                                <p class="text-slate-400 text-sm mt-2">Upload your notes, PDFs, or paste content and let AI create quiz questions for you</p>
                            </div>

                            {{-- File Upload Area --}}
                            <div class="mb-6" x-data="aiUpload()">
                                <label class="block text-sm font-semibold text-slate-600 mb-2">Upload Files</label>
                                <div @dragover.prevent="dragging=true" @dragleave="dragging=false" @drop.prevent="handleDrop($event)"
                                     :class="dragging ? 'border-emerald-400 bg-emerald-50' : 'border-slate-200 bg-slate-50'"
                                     class="border-2 border-dashed rounded-2xl p-10 text-center transition-all cursor-pointer"
                                     @click="$refs.fileInput.click()">
                                    <input type="file" name="files[]" multiple accept=".pdf,.doc,.docx,.txt,.pptx" class="hidden" x-ref="fileInput" @change="handleFiles($event)">
                                    <div class="text-4xl mb-3">📁</div>
                                    <p class="font-semibold text-slate-600">Drop files here or click to upload</p>
                                    <p class="text-sm text-slate-400 mt-1">PDF, DOC, TXT, PPTX — Max 10MB each</p>
                                </div>
                                <template x-if="files.length > 0">
                                    <div class="mt-4 space-y-2">
                                        <template x-for="(f, i) in files" :key="i">
                                            <div class="flex items-center justify-between bg-slate-50 px-4 py-2 rounded-xl">
                                                <span class="text-sm text-slate-600 truncate" x-text="f.name"></span>
                                                <button type="button" @click="files.splice(i,1)" class="text-red-400 hover:text-red-600 text-xs">✕</button>
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
                                    <select class="w-full p-3 bg-slate-50 border border-slate-100 rounded-xl text-slate-700 focus:outline-none">
                                        <option>5 Questions</option>
                                        <option>10 Questions</option>
                                        <option selected>15 Questions</option>
                                        <option>20 Questions</option>
                                        <option>30 Questions</option>
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

                            <button type="submit" class="w-full py-4 bg-gradient-to-r from-violet-500 to-purple-600 text-white font-bold rounded-2xl shadow-lg shadow-violet-200 hover:shadow-xl hover:from-violet-600 hover:to-purple-700 active:scale-[0.98] transition-all text-lg">
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
            dragging: false,
            files: [],
            handleDrop(e) { this.dragging = false; this.files = [...this.files, ...e.dataTransfer.files]; },
            handleFiles(e) { this.files = [...this.files, ...e.target.files]; }
        }
    }
    </script>
</x-layout>
