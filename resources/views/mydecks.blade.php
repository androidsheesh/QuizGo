<x-layout>
    <div class="flex min-h-screen bg-[#F9FAFB]">
        <x-sidebar/>

        <main class="max-w-6xl mx-auto mt-16 md:mt-20 w-full px-6" x-data="{ showDeckModal: false }">
            <x-dropdown-profile/>
            
            <div class="max-w-6xl mx-auto w-full">
                {{-- Header Section --}}
                <div class="flex flex-col md:flex-row md:items-center justify-between mb-10 gap-4">
                    <h2 class="text-3xl font-bold text-slate-800">My decks</h2>

                    <div class="flex items-center space-x-4">
                        <button @click="showDeckModal = true" class="flex items-center space-x-2 px-6 py-2.5 bg-slate-900 text-white rounded-2xl shadow-lg shadow-slate-200 hover:bg-slate-800 transition-all">
                            <span class="text-lg">＋</span>
                            <span class="font-semibold text-sm">Add deck</span>
                        </button>
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
                
                {{-- Add Deck Modal --}}
                <div x-show="showDeckModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center bg-slate-900/50 backdrop-blur-sm">
                    <div @click.away="showDeckModal = false" class="bg-white rounded-3xl shadow-xl w-full max-w-md p-8 transform transition-all">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-xl font-bold text-slate-800">Create New Deck</h3>
                            <button @click="showDeckModal = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                        <form action="{{ route('decks.store') }}" method="POST">
                            @csrf
                            <div class="mb-6">
                                <label class="block text-sm font-semibold text-slate-600 mb-2">Deck Title</label>
                                <input type="text" name="title" required class="w-full p-4 bg-slate-50 border border-transparent focus:border-emerald-400 focus:bg-white rounded-2xl text-slate-700 focus:outline-none transition-all">
                            </div>
                            <button type="submit" class="w-full py-4 bg-emerald-500 text-white font-bold rounded-2xl hover:bg-emerald-600 transition-colors shadow-lg shadow-emerald-200">
                                Create Deck
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Decks Grid --}}
                @if($decks->isEmpty())
                    <div class="text-center py-20 bg-white rounded-[2rem] border border-gray-100 shadow-sm">
                        <div class="text-5xl mb-4">📭</div>
                        <h3 class="text-xl font-bold text-slate-700">No decks yet</h3>
                        <p class="text-slate-400 mt-2">Create your first deck to start studying!</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        @foreach($decks as $deck)
                            <div class="group relative flex bg-white border border-gray-100 rounded-[2rem] overflow-hidden shadow-sm hover:shadow-xl hover:shadow-slate-200/50 transition-all duration-300 transform hover:-translate-y-1">
                                {{-- Fixed Accent Color instead of relying on non-existent random db column --}}
                                <div class="w-3 bg-emerald-400"></div>

                                <div class="flex-1 p-8">
                                    <div class="flex justify-between items-start mb-4">
                                        <a href="{{ route('decks.show', $deck) }}" class="block flex-1 group-hover:cursor-pointer">
                                            <h4 class="text-xl font-bold text-slate-800 group-hover:text-emerald-600 transition-colors">{{ $deck->title }}</h4>
                                            <p class="text-slate-400 font-medium text-sm mt-1">{{ $deck->flashcards_count }} cards</p>
                                        </a>
                                        
                                        {{-- Delete Button inside a form --}}
                                        <form action="{{ route('decks.destroy', $deck) }}" method="POST" onsubmit="return confirm('Delete this deck?');" class="relative z-10">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 text-gray-300 hover:text-red-500 hover:bg-red-50 rounded-lg transition" title="Delete deck">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>

                                    <div class="w-full h-1.5 bg-slate-50 rounded-full mt-6 overflow-hidden">
                                        <div class="h-full bg-slate-200 rounded-full" style="width: {{ min(100, max(5, $deck->flashcards_count * 5)) }}%"></div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </main>
    </div>
</x-layout>
