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
                <div class="bg-white rounded-[2rem] border border-gray-100 p-8 shadow-sm">
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
                
            </div>
        </main>
    </div>
</x-layout>
