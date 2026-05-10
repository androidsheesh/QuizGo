<x-layout title="Quizgo | AI Quiz Generator">
    <div class="flex min-h-screen bg-[#F9FAFB]">
        <x-teacher-sidebar/>
        <main class="flex-1 p-6 md:p-12 overflow-y-auto relative">
            <x-dropdown-profile/>
            
            <div class="max-w-3xl mx-auto flex flex-col items-center">
                
                <div class="flex flex-col items-center text-center mb-10">
                    <div class="w-24 h-24 bg-violet-500 rounded-xl flex items-center justify-center shadow-[0_4px_0_0_#6d28d9]">
                        <span class="text-5xl text-white">🤖</span>
                    </div>
                    <h2 class="text-3xl md:text-4xl font-bold text-slate-800 mt-6">AI Quiz Generator</h2>
                    <p class="text-slate-500 mt-2">Generate multiple-choice quizzes automatically</p>
                </div>

                {{-- AI errors --}}
                @if ($errors->any())
                    <div class="w-full mb-4 p-4 bg-red-50 border border-red-200 rounded-2xl text-red-600 text-sm">
                        {{ $errors->first() }}
                    </div>
                @endif

                {{-- Success message --}}
                @if (session('success'))
                    <div class="w-full mb-4 p-4 bg-emerald-50 border border-emerald-200 rounded-2xl text-emerald-600 text-sm">
                        ✅ {{ session('success') }}
                    </div>
                @endif

                <div class="w-full flex flex-col items-center gap-3 mb-10" x-data="{ active: 'topic' }">

                    <div class="flex flex-wrap justify-center gap-3 mb-4">
                        <button @click="active = 'topic'" class="flex items-center space-x-2 px-6 py-2.5 bg-white border border-gray-200 rounded-full text-slate-600 font-medium hover:bg-slate-50 transition-all" :class="{ 'border-violet-400 bg-violet-50 text-violet-600': active === 'topic' }">
                            <span>💡</span><span>Topic</span>
                        </button>
                        <button @click="active = 'paste'" class="flex items-center space-x-2 px-6 py-2.5 bg-white border border-gray-200 rounded-full text-slate-600 font-medium hover:bg-slate-50 transition-all" :class="{ 'border-violet-400 bg-violet-50 text-violet-600': active === 'paste' }">
                            <span>📋</span><span>Paste</span>
                        </button>
                        <button @click="active = 'pdf'" class="flex items-center space-x-2 px-6 py-2.5 bg-white border border-gray-200 rounded-full text-slate-600 font-medium hover:bg-slate-50 transition-all" :class="{ 'border-violet-400 bg-violet-50 text-violet-600': active === 'pdf' }">
                            <span>📄</span><span>PDF</span>
                        </button>
                    </div>

                    {{-- Topic Panel --}}
                    <div x-show="active === 'topic'" x-transition class="w-full">
                        <form method="POST" action="{{ route('teacher.quiz.ai.topic') }}">
                            @csrf
                            <div class="flex items-center space-x-3 mb-3 pl-2">
                                <label class="text-sm font-semibold text-slate-600">Question Count (Max 20):</label>
                                <input type="number" name="count" min="1" max="20" value="10" class="w-20 p-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-violet-400">
                            </div>
                            <div class="relative">
                                <input type="text" name="topic" placeholder="Generate a quiz about..." value="{{ old('topic') }}" required class="w-full p-6 pr-16 bg-white border border-gray-200 rounded-[2rem] text-xl shadow-sm focus:outline-none focus:ring-4 focus:ring-violet-500/10 focus:border-violet-400 transition-all placeholder:text-gray-300">
                                <button type="submit" class="absolute right-4 top-1/2 -translate-y-1/2 w-10 h-10 bg-violet-500 hover:bg-violet-600 rounded-full flex items-center justify-center transition-colors shadow-md shadow-violet-200" title="Generate">
                                    <svg class="text-white w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 5l7 7-7 7M5 5l7 7-7 7"/></svg>
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- Paste Panel --}}
                    <div x-show="active === 'paste'" style="display: none;" x-transition class="w-full">
                        <form method="POST" action="{{ route('teacher.quiz.ai.text') }}">
                            @csrf
                            <div class="flex items-center space-x-3 mb-3 pl-2">
                                <label class="text-sm font-semibold text-slate-600">Question Count (Max 20):</label>
                                <input type="number" name="count" min="1" max="20" value="10" class="w-20 p-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-violet-400">
                            </div>
                            <textarea name="text" rows="6" placeholder="Paste lesson notes, article, or any text here..." required class="w-full p-5 bg-white border border-gray-200 rounded-3xl text-base shadow-sm focus:outline-none focus:ring-4 focus:ring-violet-500/10 focus:border-violet-400 placeholder:text-gray-300 resize-none">{{ old('text') }}</textarea>
                            <div class="flex justify-end mt-2">
                                <button type="submit" class="px-6 py-2.5 bg-violet-500 text-white rounded-full font-semibold hover:bg-violet-600 transition-colors shadow-md shadow-violet-200">
                                    Generate Quiz ✨
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- PDF Panel --}}
                    <div x-show="active === 'pdf'" style="display: none;" x-transition class="w-full">
                        <form method="POST" action="{{ route('teacher.quiz.ai.pdf') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="flex items-center space-x-3 mb-3 pl-2">
                                <label class="text-sm font-semibold text-slate-600">Question Count (Max 20):</label>
                                <input type="number" name="count" min="1" max="20" value="10" class="w-20 p-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-violet-400">
                            </div>
                            <div x-data="{ fileName: '' }">
                                <label
                                    class="flex flex-col items-center justify-center w-full h-36 bg-white border-2 border-dashed rounded-3xl cursor-pointer transition-all"
                                    :class="fileName ? 'border-violet-400 bg-violet-50/40' : 'border-gray-200 hover:border-violet-400 hover:bg-violet-50/30'"
                                >
                                    <template x-if="!fileName">
                                        <div class="flex flex-col items-center">
                                            <span class="text-3xl mb-2">📄</span>
                                            <span class="text-slate-500 text-sm font-medium">Click to upload a lesson PDF</span>
                                            <span class="text-slate-300 text-xs mt-1">Max 10MB</span>
                                        </div>
                                    </template>
                                    <template x-if="fileName">
                                        <div class="flex flex-col items-center gap-1 px-4 text-center">
                                            <span class="text-3xl">✅</span>
                                            <span class="text-violet-600 text-sm font-semibold mt-1 break-all" x-text="fileName"></span>
                                            <span class="text-slate-400 text-xs">Click to change file</span>
                                        </div>
                                    </template>
                                    <input
                                        type="file" name="pdf" accept=".pdf" class="hidden" required
                                        @change="fileName = $event.target.files[0] ? $event.target.files[0].name : ''"
                                    >
                                </label>
                            </div>
                            <div class="flex justify-end mt-2">
                                <button type="submit" class="px-6 py-2.5 bg-violet-500 text-white rounded-full font-semibold hover:bg-violet-600 transition-colors shadow-md shadow-violet-200">
                                    Generate from PDF ✨
                                </button>
                            </div>
                        </form>
                    </div>
                </div> {{-- end x-data --}}

                <div class="mt-8 text-center">
                    <a href="{{ route('teacher.quiz.index') }}" class="text-slate-400 hover:text-violet-600 font-medium transition-colors">← Back to Quizzes</a>
                </div>

            </div>
        </main>
    </div>
</x-layout>
