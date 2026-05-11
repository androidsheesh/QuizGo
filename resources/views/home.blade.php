<x-layout title="Quizgo | Home">
    <div class="flex min-h-screen bg-[#F9FAFB]">
        <x-sidebar/>

        <main class="flex-1 p-6 md:p-12 relative">
            <x-dropdown-profile/>

            <div class="max-w-3xl mx-auto flex flex-col items-center">

                {{-- Header --}}
                <div class="flex flex-col items-center text-center mb-10">
                    <div class="w-24 h-24 bg-indigo-500 rounded-xl flex items-center justify-center shadow-[0_4px_0_0_#4338ca]">
                        <div class="w-12 h-12 bg-white rounded-sm rotate-45"></div>
                    </div>
                    <h2 class="text-3xl md:text-4xl font-medium text-slate-800 mt-4">What do you want to Study?</h2>
                </div>

                {{-- AI errors --}}
                @if ($errors->any())
                    <div class="w-full mb-4 p-4 bg-red-50 border border-red-200 rounded-2xl text-red-600 text-sm">
                        {{ $errors->first() }}
                    </div>
                @endif

                {{-- Success message --}}
                @if (session('success'))
                    <div class="w-full mb-4 p-4 bg-green-50 border border-green-200 rounded-2xl text-green-600 text-sm">
                        ✅ {{ session('success') }}
                    </div>
                @endif

                {{-- Action buttons + collapsible panels --}}
                <div class="w-full flex flex-col items-center gap-3 mb-10" x-data="{ active: 'topic', isLoading: false }">

                    {{-- Full-screen Loading Overlay --}}
                    <div x-show="isLoading" style="display: none;" class="fixed inset-0 z-50 flex flex-col items-center justify-center bg-white/80 backdrop-blur-sm">
                        <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-b-4 border-indigo-600 mb-4"></div>
                        <h2 class="text-2xl font-semibold text-slate-800">Flashcards are on the making...</h2>
                        <p class="text-slate-500 mt-2">This may take a few moments. Please don't close this page.</p>
                    </div>

                    <div class="flex flex-wrap justify-center gap-3 mb-2">
                        <button @click="active = 'topic'" class="flex items-center space-x-2 px-6 py-2.5 bg-white border border-gray-200 rounded-full text-slate-600 font-medium hover:bg-slate-50 transition-all" :class="{ 'border-indigo-400 bg-indigo-50 text-indigo-600': active === 'topic' }">
                            <span>💡</span><span>Topic</span>
                        </button>
                        <button @click="active = 'paste'" class="flex items-center space-x-2 px-6 py-2.5 bg-white border border-gray-200 rounded-full text-slate-600 font-medium hover:bg-slate-50 transition-all" :class="{ 'border-indigo-400 bg-indigo-50 text-indigo-600': active === 'paste' }">
                            <span>📋</span><span>Paste</span>
                        </button>
                        <button @click="active = 'pdf'" class="flex items-center space-x-2 px-6 py-2.5 bg-white border border-gray-200 rounded-full text-slate-600 font-medium hover:bg-slate-50 transition-all" :class="{ 'border-indigo-400 bg-indigo-50 text-indigo-600': active === 'pdf' }">
                            <span>📄</span><span>PDF</span>
                        </button>
                    </div>

                    {{-- Topic Panel --}}
                    <div x-show="active === 'topic'" x-transition class="w-full">
                        <form method="POST" action="{{ route('generate.topic') }}" @submit.prevent="isLoading = true; setTimeout(() => $event.target.submit(), 50)">
                            @csrf
                            <div class="flex items-center space-x-3 mb-3 pl-2">
                                <label class="text-sm font-semibold text-slate-600">Flashcard Count (Max 20):</label>
                                <input type="number" name="count" min="1" max="20" value="10" class="w-20 p-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-400">
                            </div>
                            <div class="relative">
                                <input type="text" name="topic" placeholder="I want to study..." value="{{ old('topic') }}" required class="w-full p-6 pr-16 bg-white border border-gray-200 rounded-[2rem] text-xl shadow-sm focus:outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-400 transition-all placeholder:text-gray-300">
                                <button type="submit" class="absolute right-4 top-1/2 -translate-y-1/2 w-10 h-10 bg-indigo-500 hover:bg-indigo-600 rounded-full flex items-center justify-center transition-colors shadow-md shadow-indigo-200" title="Generate">
                                    <svg class="text-white w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 5l7 7-7 7M5 5l7 7-7 7"/></svg>
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- Paste Panel --}}
                    <div x-show="active === 'paste'" style="display: none;" x-transition class="w-full">
                        <form method="POST" action="{{ route('generate.text') }}" @submit.prevent="isLoading = true; setTimeout(() => $event.target.submit(), 50)">
                            @csrf
                            <div class="flex items-center space-x-3 mb-3 pl-2">
                                <label class="text-sm font-semibold text-slate-600">Flashcard Count (Max 20):</label>
                                <input type="number" name="count" min="1" max="20" value="10" class="w-20 p-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-400">
                            </div>
                            <textarea name="text" rows="6" placeholder="Paste your notes, article, or any text here..." required class="w-full p-5 bg-white border border-gray-200 rounded-3xl text-base shadow-sm focus:outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-400 placeholder:text-gray-300 resize-none">{{ old('text') }}</textarea>
                            <div class="flex justify-end mt-2">
                                <button type="submit" class="px-6 py-2.5 bg-indigo-500 text-white rounded-full font-semibold hover:bg-indigo-600 transition-colors shadow-md shadow-indigo-200">
                                    Generate Flashcards ✨
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- PDF Panel --}}
                    <div x-show="active === 'pdf'" style="display: none;" x-transition class="w-full">
                        <form method="POST" action="{{ route('generate.pdf') }}" enctype="multipart/form-data" @submit.prevent="isLoading = true; setTimeout(() => $event.target.submit(), 50)">
                            @csrf
                            <div class="flex items-center space-x-3 mb-3 pl-2">
                                <label class="text-sm font-semibold text-slate-600">Flashcard Count (Max 20):</label>
                                <input type="number" name="count" min="1" max="20" value="10" class="w-20 p-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-400">
                            </div>
                            <div x-data="{ fileName: '' }">
                                <label
                                    class="flex flex-col items-center justify-center w-full h-36 bg-white border-2 border-dashed rounded-3xl cursor-pointer transition-all"
                                    :class="fileName ? 'border-indigo-400 bg-indigo-50/40' : 'border-gray-200 hover:border-indigo-400 hover:bg-indigo-50/30'"
                                >
                                    <template x-if="!fileName">
                                        <div class="flex flex-col items-center">
                                            <span class="text-3xl mb-2">📄</span>
                                            <span class="text-slate-500 text-sm font-medium">Click to upload a PDF</span>
                                            <span class="text-slate-300 text-xs mt-1">Max 10MB</span>
                                        </div>
                                    </template>
                                    <template x-if="fileName">
                                        <div class="flex flex-col items-center gap-1 px-4 text-center">
                                            <span class="text-3xl">✅</span>
                                            <span class="text-indigo-600 text-sm font-semibold mt-1 break-all" x-text="fileName"></span>
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
                                <button type="submit" class="px-6 py-2.5 bg-indigo-500 text-white rounded-full font-semibold hover:bg-indigo-600 transition-colors shadow-md shadow-indigo-200">
                                    Generate from PDF ✨
                                </button>
                            </div>
                        </form>
                    </div>
                </div> {{-- end x-data --}}

                <div class="w-full max-w-2xl">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold text-slate-700">My decks</h3>
                        <a href="{{ route('mydecks') }}" class="text-2xl text-slate-300 hover:text-slate-600 transition-colors" title="Add deck">+</a>
                    </div>

                    @if($decks->isEmpty())
                        {{-- Empty state --}}
                        <div class="text-center py-14 bg-white rounded-3xl border border-gray-100 shadow-sm">
                            <div class="text-4xl mb-3">📭</div>
                            <h4 class="text-base font-bold text-slate-700">No decks yet</h4>
                            <p class="text-slate-400 text-sm mt-1">Create your first deck to start studying!</p>
                            <a href="{{ route('mydecks') }}" class="inline-block mt-5 px-6 py-2 bg-emerald-500 text-white text-sm font-semibold rounded-full hover:bg-emerald-600 transition-colors shadow-lg shadow-emerald-200">
                                Create a deck
                            </a>
                        </div>
                    @else
                        @php
                            $accentColors = ['bg-emerald-400', 'bg-blue-400', 'bg-amber-400', 'bg-rose-400', 'bg-violet-400'];
                        @endphp

                        <div class="space-y-4">
                            @foreach($decks as $index => $deck)
                                <a href="{{ route('decks.show', $deck) }}" class="group flex items-center bg-white border border-gray-100 rounded-3xl overflow-hidden shadow-sm hover:shadow-md transition-all duration-200 cursor-pointer">
                                    <div class="flex-1 p-6 flex justify-between items-center">
                                        <div>
                                            <h4 class="text-lg font-bold text-slate-800 group-hover:text-emerald-600 transition-colors">{{ $deck->title }}</h4>
                                            <p class="text-slate-400 text-sm">{{ $deck->flashcards_count }} {{ Str::plural('card', $deck->flashcards_count) }}</p>
                                        </div>
                                        <span class="opacity-0 group-hover:opacity-100 text-slate-300 transition-all">
                                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                                        </span>
                                    </div>
                                    <div class="w-3 self-stretch {{ $accentColors[$index % count($accentColors)] }}"></div>
                                </a>
                            @endforeach
                        </div>
                    @endif

                    <a href="{{ route('mydecks') }}" class="block w-full mt-8 py-2 text-center text-slate-400 text-sm font-medium hover:text-emerald-600 transition-colors">
                        See all →
                    </a>
                </div>
            </div>
        </main>
    </div>
</x-layout>
