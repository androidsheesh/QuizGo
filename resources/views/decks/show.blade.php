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
                                    
                                    {{-- Delete Form Button (Top Right) --}}
                                    <form action="{{ route('flashcards.destroy', $card) }}" method="POST" onsubmit="return confirm('Delete this card?');" class="absolute right-3 top-3">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-gray-300 hover:text-red-500 hover:bg-red-50 rounded-lg transition" title="Delete Card">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>

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
