<x-layout>
    <div class="flex min-h-screen bg-[#F9FAFB]">

        <x-sidebar/>
        {{--
            [ MAIN CONTENT ]
        --}}
        <x-dropdown-profile/>
        <main class="flex-1 p-6 md:p-12 overflow-y-auto relative">

            <div class="max-w-3xl mx-auto flex flex-col items-center">

                <div class="flex flex-col items-center text-center mb-10">
                    <div class="w-24 h-24 bg-emerald-400 rounded-2xl flex items-center justify-center shadow-xl shadow-emerald-200/50 transform rotate-3 mb-8">
                        <div class="w-12 h-12 bg-emerald-900/20 rounded-lg border-b-4 border-emerald-900/30"></div>
                    </div>
                    <h2 class="text-3xl md:text-4xl font-medium text-slate-800">What do you want to Study?</h2>
                </div>

                <div class="w-full mb-8">
                    <input type="text" placeholder="I want to study..."
                           class="w-full p-6 bg-white border border-gray-200 rounded-[2rem] text-xl shadow-sm focus:outline-none focus:ring-4 focus:ring-blue-500/5 focus:border-blue-400 transition-all placeholder:text-gray-300">
                </div>

                <div class="flex flex-wrap justify-center gap-3 mb-16">
                    <button class="flex items-center space-x-2 px-6 py-2.5 bg-white border border-gray-200 rounded-full text-slate-600 font-medium hover:bg-slate-50 transition-all">
                        <span>📤</span> <span>Upload</span>
                    </button>
                    <button class="flex items-center space-x-2 px-6 py-2.5 bg-white border border-gray-200 rounded-full text-slate-600 font-medium hover:bg-slate-50 transition-all">
                        <span>📋</span> <span>Paste</span>
                    </button>
                    <button class="flex items-center space-x-2 px-6 py-2.5 bg-white border border-gray-200 rounded-full text-slate-600 font-medium hover:bg-slate-50 transition-all">
                        <span>📄</span> <span>PDF</span>
                    </button>
                    <button class="flex items-center space-x-2 px-6 py-2.5 bg-white border border-gray-200 rounded-full text-slate-600 font-medium hover:bg-slate-50 transition-all">
                        <span>📂</span> <span>Decks</span>
                    </button>
                </div>

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
