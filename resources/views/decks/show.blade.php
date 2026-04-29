<x-layout>
    <div class="flex min-h-screen bg-[#F9FAFB]">
        <x-sidebar/>

        <main class="max-w-6xl mx-auto mt-16 md:mt-20 w-full px-6">
            <x-dropdown-profile/>

            <div class="max-w-4xl mx-auto w-full flex flex-col">
                {{-- Header Section --}}
                <div class="flex flex-col md:flex-row md:items-center justify-between mb-10 gap-4">
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('mydecks') }}" class="w-10 h-10 bg-white border border-gray-200 rounded-full flex items-center justify-center text-slate-400 hover:text-slate-700 shadow-sm transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        </a>
                        <div>
                            <h2 class="text-3xl font-bold text-slate-800">{{ $deck->title }}</h2>
                            <p class="text-slate-500 mt-1">{{ $deck->flashcards->count() }} Cards</p>
                        </div>
                    </div>
                </div>

                {{-- Alert / Success Message --}}
                @if (session('success'))
                    <div class="mb-6 p-4 bg-emerald-50 text-emerald-700 border border-emerald-100 rounded-xl">
                        {{ session('success') }}
                    </div>
                @endif
                @if ($errors->any())
                    <div class="mb-6 p-4 bg-red-50 text-red-700 border border-red-100 rounded-xl">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                {{-- Study Now Section --}}
                @if($deck->flashcards->count() > 0)
                    <div class="mb-12" x-data="{
                        showStudyModal: false,
                        selectedMode: '',
                        cardCount: {{ $deck->flashcards->count() }},
                        totalCards: {{ $deck->flashcards->count() }},
                        get studyUrl() {
                            if (!this.selectedMode) return '#';
                            const routes = {
                                flipcards: '{{ route('study.flipcards', $deck) }}',
                                multiplechoice: '{{ route('study.multiplechoice', $deck) }}',
                                identification: '{{ route('study.identification', $deck) }}'
                            };
                            return routes[this.selectedMode] + '?count=' + this.cardCount;
                        }
                    }">
                        {{-- Study Now Button --}}
                        <button @click="showStudyModal = true"
                            class="w-full flex items-center justify-center gap-3 px-8 py-5 bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-bold text-lg rounded-[2rem] shadow-lg shadow-emerald-200/50 hover:shadow-xl hover:shadow-emerald-200/70 hover:-translate-y-0.5 transition-all duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Study Now
                        </button>

                        {{-- Study Mode Modal --}}
                        <div x-show="showStudyModal" style="display: none;"
                            class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center bg-slate-900/50 backdrop-blur-sm"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0"
                            x-transition:enter-end="opacity-100"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0">
                            <div @click.away="showStudyModal = false; selectedMode = ''"
                                class="bg-white rounded-[2rem] shadow-2xl w-full max-w-lg p-8 md:p-10 transform transition-all"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100">

                                {{-- Modal Header --}}
                                <div class="flex justify-between items-center mb-8">
                                    <div>
                                        <h3 class="text-2xl font-bold text-slate-800">Choose Study Mode</h3>
                                        <p class="text-slate-400 text-sm mt-1">{{ $deck->title }}</p>
                                    </div>
                                    <button @click="showStudyModal = false; selectedMode = ''"
                                        class="w-10 h-10 bg-slate-50 hover:bg-slate-100 rounded-xl flex items-center justify-center text-slate-400 hover:text-slate-600 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </div>

                                {{-- Mode Selection --}}
                                <div class="grid grid-cols-3 gap-3 mb-8">
                                    {{-- Flip Cards --}}
                                    <button @click="selectedMode = 'flipcards'"
                                        :class="selectedMode === 'flipcards'
                                            ? 'border-emerald-400 bg-emerald-50 ring-2 ring-emerald-400/30'
                                            : 'border-gray-100 bg-white hover:border-emerald-200 hover:bg-emerald-50/50'"
                                        class="flex flex-col items-center gap-3 p-5 rounded-2xl border-2 transition-all duration-200 cursor-pointer group">
                                        <div class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl"
                                            :class="selectedMode === 'flipcards' ? 'bg-emerald-400 shadow-lg shadow-emerald-200' : 'bg-slate-100 group-hover:bg-emerald-100'">
                                            🔄
                                        </div>
                                        <span class="text-sm font-bold" :class="selectedMode === 'flipcards' ? 'text-emerald-700' : 'text-slate-600'">Flip Cards</span>
                                    </button>

                                    {{-- Multiple Choice --}}
                                    <button @click="if(totalCards >= 4) selectedMode = 'multiplechoice'"
                                        :class="[
                                            totalCards < 4 ? 'opacity-50 cursor-not-allowed' : '',
                                            selectedMode === 'multiplechoice'
                                                ? 'border-violet-400 bg-violet-50 ring-2 ring-violet-400/30'
                                                : 'border-gray-100 bg-white hover:border-violet-200 hover:bg-violet-50/50'
                                        ]"
                                        class="flex flex-col items-center gap-3 p-5 rounded-2xl border-2 transition-all duration-200 cursor-pointer group relative"
                                        :title="totalCards < 4 ? 'Need at least 4 cards' : ''">
                                        <div class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl"
                                            :class="selectedMode === 'multiplechoice' ? 'bg-violet-400 shadow-lg shadow-violet-200' : 'bg-slate-100 group-hover:bg-violet-100'">
                                            📝
                                        </div>
                                        <span class="text-sm font-bold" :class="selectedMode === 'multiplechoice' ? 'text-violet-700' : 'text-slate-600'">Multiple Choice</span>
                                        <span x-show="totalCards < 4" class="absolute -bottom-1 text-[10px] text-red-400 font-semibold">Min 4 cards</span>
                                    </button>

                                    {{-- Identification --}}
                                    <button @click="selectedMode = 'identification'"
                                        :class="selectedMode === 'identification'
                                            ? 'border-amber-400 bg-amber-50 ring-2 ring-amber-400/30'
                                            : 'border-gray-100 bg-white hover:border-amber-200 hover:bg-amber-50/50'"
                                        class="flex flex-col items-center gap-3 p-5 rounded-2xl border-2 transition-all duration-200 cursor-pointer group">
                                        <div class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl"
                                            :class="selectedMode === 'identification' ? 'bg-amber-400 shadow-lg shadow-amber-200' : 'bg-slate-100 group-hover:bg-amber-100'">
                                            ⌨️
                                        </div>
                                        <span class="text-sm font-bold" :class="selectedMode === 'identification' ? 'text-amber-700' : 'text-slate-600'">Identification</span>
                                    </button>
                                </div>

                                {{-- Card Count Slider --}}
                                <div class="mb-8">
                                    <div class="flex justify-between items-center mb-3">
                                        <label class="text-sm font-bold text-slate-600">Cards to Study</label>
                                        <div class="flex items-center gap-2">
                                            <span class="text-2xl font-black text-slate-800" x-text="cardCount"></span>
                                            <span class="text-sm text-slate-400">/ <span x-text="totalCards"></span></span>
                                        </div>
                                    </div>
                                    <input type="range" min="1" :max="totalCards" x-model="cardCount"
                                        class="w-full h-2 bg-slate-100 rounded-full appearance-none cursor-pointer accent-emerald-500
                                        [&::-webkit-slider-thumb]:appearance-none [&::-webkit-slider-thumb]:w-5 [&::-webkit-slider-thumb]:h-5 [&::-webkit-slider-thumb]:rounded-full [&::-webkit-slider-thumb]:bg-emerald-500 [&::-webkit-slider-thumb]:shadow-lg [&::-webkit-slider-thumb]:shadow-emerald-200 [&::-webkit-slider-thumb]:cursor-pointer
                                        [&::-moz-range-thumb]:w-5 [&::-moz-range-thumb]:h-5 [&::-moz-range-thumb]:rounded-full [&::-moz-range-thumb]:bg-emerald-500 [&::-moz-range-thumb]:border-0 [&::-moz-range-thumb]:shadow-lg [&::-moz-range-thumb]:shadow-emerald-200 [&::-moz-range-thumb]:cursor-pointer">
                                    <div class="flex justify-between mt-2">
                                        <span class="text-xs text-slate-300 font-medium">1</span>
                                        <span class="text-xs text-slate-300 font-medium" x-text="totalCards"></span>
                                    </div>
                                </div>

                                {{-- Start Button --}}
                                <a :href="studyUrl"
                                    :class="selectedMode ? 'bg-slate-900 hover:bg-slate-800 cursor-pointer shadow-lg shadow-slate-200' : 'bg-slate-200 cursor-not-allowed text-slate-400'"
                                    class="block w-full text-center py-4 text-white font-bold rounded-2xl transition-all duration-200"
                                    @click.prevent="if(selectedMode) window.location.href = studyUrl">
                                    <span x-show="!selectedMode">Select a study mode</span>
                                    <span x-show="selectedMode">Start Studying →</span>
                                </a>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Add Flashcard Form --}}
                <div class="bg-white p-6 md:p-8 rounded-[2rem] shadow-sm border border-gray-100 mb-12">
                    <h3 class="text-lg font-bold text-slate-800 mb-4">Add Manual Card</h3>
                    <form action="{{ route('flashcards.store', $deck) }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block text-sm font-semibold text-slate-600 mb-2">Question</label>
                                <textarea name="question" required rows="3" class="w-full p-4 bg-slate-50 border border-transparent focus:border-emerald-400 focus:bg-white rounded-2xl text-slate-700 focus:outline-none transition-all placeholder:text-slate-400" placeholder="Type the question here..."></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-600 mb-2">Answer</label>
                                <textarea name="answer" required rows="3" class="w-full p-4 bg-slate-50 border border-transparent focus:border-emerald-400 focus:bg-white rounded-2xl text-slate-700 focus:outline-none transition-all placeholder:text-slate-400" placeholder="Type the answer here..."></textarea>
                            </div>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" class="px-8 py-3 bg-slate-900 text-white font-bold rounded-2xl hover:bg-slate-800 transition-colors shadow-lg shadow-slate-200">
                                Save Card
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Cards List --}}
                <div class="mb-20">
                    <h3 class="text-lg font-bold text-slate-800 mb-4">Existing Cards</h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        @forelse($deck->flashcards as $card)
                            <div class="group flex flex-col bg-white border border-gray-100 rounded-[2rem] overflow-hidden shadow-sm hover:shadow-xl hover:shadow-emerald-100/50 transition-all duration-300 relative transform hover:-translate-y-1 aspect-[3/4] min-h-[300px]">
                                <div class="h-2 w-full bg-emerald-400 transition-colors"></div>
                                <div class="flex-1 p-6 relative flex flex-col justify-between">

                                    {{-- Action Buttons (Top Right) --}}
                                    <div class="absolute right-3 top-3 flex items-center gap-1">

                                        {{-- Edit Button --}}
                                        <button
                                            type="button"
                                            onclick="document.getElementById('edit-modal-{{ $card->id }}').classList.remove('hidden')"
                                            class="p-2 text-gray-300 hover:text-emerald-500 hover:bg-emerald-50 rounded-lg transition"
                                            title="Edit Card">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>

                                        {{-- Delete Button --}}
                                        <form action="{{ route('flashcards.destroy', $card) }}" method="POST" onsubmit="return confirm('Delete this card?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 text-gray-300 hover:text-red-500 hover:bg-red-50 rounded-lg transition" title="Delete Card">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>

                                    </div>

                                    {{-- Edit Modal Overlay --}}
                                    <div
                                        id="edit-modal-{{ $card->id }}"
                                        class="hidden absolute inset-0 bg-white/95 backdrop-blur-sm rounded-[2rem] z-10 flex flex-col justify-center p-6">
                                        <form action="{{ route('flashcards.update', $card) }}" method="POST" class="flex flex-col gap-3">
                                            @csrf
                                            @method('PUT')
                                            <div>
                                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Question</label>
                                                <textarea
                                                    name="question"
                                                    rows="3"
                                                    class="w-full p-3 bg-slate-50 border border-transparent focus:border-emerald-400 focus:bg-white rounded-xl text-slate-700 text-sm focus:outline-none transition-all resize-none"
                                                >{{ $card->question }}</textarea>
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Answer</label>
                                                <textarea
                                                    name="answer"
                                                    rows="3"
                                                    class="w-full p-3 bg-slate-50 border border-transparent focus:border-emerald-400 focus:bg-white rounded-xl text-slate-700 text-sm focus:outline-none transition-all resize-none"
                                                >{{ $card->answer }}</textarea>
                                            </div>
                                            <div class="flex gap-2 mt-1">
                                                <button
                                                    type="submit"
                                                    class="flex-1 py-2 bg-slate-900 text-white text-sm font-bold rounded-xl hover:bg-slate-800 transition-colors"
                                                >
                                                    Save
                                                </button>
                                                <button
                                                    type="button"
                                                    onclick="document.getElementById('edit-modal-{{ $card->id }}').classList.add('hidden')"
                                                    class="flex-1 py-2 bg-slate-100 text-slate-600 text-sm font-bold rounded-xl hover:bg-slate-200 transition-colors"
                                                >
                                                    Cancel
                                                </button>
                                            </div>
                                        </form>
                                    </div>

                                    <div class="mb-6 pr-6">
                                        <p class="text-[10px] font-bold text-emerald-400 uppercase tracking-widest mb-2">Question</p>
                                        <p class="text-slate-800 font-bold leading-relaxed line-clamp-3">{{ $card->question }}</p>
                                    </div>
                                    <div class="mt-auto pt-4 border-t border-slate-50">
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Answer</p>
                                        <p class="text-slate-600 text-sm leading-relaxed line-clamp-4">{{ $card->answer }}</p>
                                    </div>

                                </div>
                            </div>
                        @empty
                            <div class="col-span-full text-center py-10 bg-white rounded-3xl border border-gray-100 shadow-sm">
                                <p class="text-slate-400">No cards in this deck yet. Add one above!</p>
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>
        </main>
    </div>
</x-layout>
