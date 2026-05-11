<x-layout>
    <div class="flex min-h-screen bg-[#F9FAFB]">
        <x-sidebar/>

        <main class="max-w-6xl mx-auto mt-16 md:mt-20 w-full px-6" x-data="{ showDeckModal: false }">
            <x-dropdown-profile/>

            <div class="max-w-6xl mx-auto w-full">
                {{-- Header Section --}}
                <div class="flex flex-col md:flex-row md:items-center justify-between mb-10 gap-4">
                    <h2 class="text-3xl font-bold text-slate-800">My decks</h2>

                    <div class="flex flex-col md:flex-row items-center gap-4">
                        {{-- Search Bar --}}
                        <form method="GET" action="{{ route('mydecks') }}" class="flex w-full md:w-auto"
                              x-data="{ query: '{{ request('search') }}', search() {
                                  let url = new URL('{{ route('mydecks') }}');
                                  if (this.query) { url.searchParams.set('search', this.query); }
                                  fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                                      .then(res => res.text())
                                      .then(html => {
                                          let doc = new DOMParser().parseFromString(html, 'text/html');
                                          document.getElementById('decks-container').innerHTML = doc.getElementById('decks-container').innerHTML;
                                          window.history.pushState({}, '', url);
                                      });
                              } }" @submit.prevent="search">
                            <input type="text" name="search" x-model="query" @input.debounce.500ms="search" placeholder="Search decks..." class="w-full md:w-64 p-2 bg-white border border-gray-200 rounded-xl text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-400/30 focus:border-emerald-300 transition-all shadow-sm">
                            <button type="submit" class="ml-2 px-4 py-2 bg-slate-900 text-white font-bold rounded-xl shadow-sm hover:bg-slate-800 transition-colors">
                                Search
                            </button>
                        </form>

                        <button @click="showDeckModal = true"
                            class="group flex items-center space-x-2 px-6 py-2.5 bg-indigo-600 text-white rounded-2xl
                                shadow-[0_4px_0_0_#4338ca] hover:shadow-[0_2px_0_0_#4338ca] hover:translate-y-[2px]
                                active:shadow-none active:translate-y-[4px] transition-all">
                            <span class="text-xl font-black group-hover:rotate-90 transition-transform duration-300">＋</span>
                            <span class="font-bold text-sm tracking-tight">Add deck</span>
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
                <div id="decks-container">
                    @if($decks->isEmpty())
                    <div class="text-center py-20 bg-white rounded-[2rem] border border-gray-100 shadow-sm">
                        <div class="text-5xl mb-4">📭</div>
                        <h3 class="text-xl font-bold text-slate-700">No decks found</h3>
                        <p class="text-slate-400 mt-2">@if(request('search')) Try a different search term! @else Create your first deck to start studying! @endif</p>
                    </div>
                @else
                @php
                    $accentColors = [
                        ['stripe' => 'from-emerald-400 to-teal-400',    'dot' => '#6ee7b7', 'icon' => 'text-emerald-300'],
                        ['stripe' => 'from-violet-400 to-purple-500',   'dot' => '#c4b5fd', 'icon' => 'text-violet-300'],
                        ['stripe' => 'from-amber-400 to-orange-400',    'dot' => '#fcd34d', 'icon' => 'text-amber-300'],
                        ['stripe' => 'from-sky-400 to-blue-500',        'dot' => '#93c5fd', 'icon' => 'text-sky-300'],
                        ['stripe' => 'from-rose-400 to-pink-500',       'dot' => '#fca5a5', 'icon' => 'text-rose-300'],
                        ['stripe' => 'from-cyan-400 to-emerald-400',    'dot' => '#67e8f9', 'icon' => 'text-cyan-300'],
                    ];
                @endphp
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($decks as $deck)
                        @php $accent = $accentColors[$deck->id % count($accentColors)]; @endphp
                        <div class="group relative flex items-stretch bg-white border border-gray-100 rounded-[2rem] overflow-hidden shadow-sm hover:shadow-xl hover:shadow-slate-200/50 transition-all duration-300 transform hover:-translate-y-1"
                            x-data="{ editing: false, title: '{{ addslashes($deck->title) }}', original: '{{ addslashes($deck->title) }}' }">

                            {{-- Left colour stripe — gradient, always full height --}}
                            <div class="w-4 shrink-0 bg-gradient-to-b {{ $accent['stripe'] }}"></div>

                            {{-- Card body with subtle dot-grid pattern --}}
                            <div class="flex-1 min-w-0 p-8 relative"
                                style="background-image: radial-gradient(circle, {{ $accent['dot'] }}55 1px, transparent 1px); background-size: 22px 22px;">

                                <div class="flex items-start gap-2 mb-4">

                                    {{-- Title area — min-w-0 lets it shrink and truncate --}}
                                    <div class="flex-1 min-w-0">

                                        {{-- VIEW MODE --}}
                                        <a x-show="!editing" href="{{ route('decks.show', $deck) }}" class="block min-w-0">
                                            <h4 class="text-xl font-bold text-slate-800 group-hover:text-emerald-600 transition-colors truncate"
                                                x-text="title"
                                                title="{{ $deck->title }}"></h4>
                                        </a>

                                        {{-- EDIT MODE --}}
                                        <form x-show="editing" style="display:none;"
                                            action="{{ route('decks.update', $deck) }}" method="POST"
                                            @click.away="editing = false; title = original"
                                            class="flex items-center gap-2">
                                            @csrf
                                            @method('PUT')
                                            <input type="text" name="title" x-model="title"
                                                x-effect="if (editing) $nextTick(() => $el.focus())"
                                                @keydown.escape="editing = false; title = original"
                                                class="min-w-0 w-full px-3 py-1.5 text-base font-bold text-slate-700 bg-slate-50 border border-emerald-300 rounded-lg focus:outline-none focus:border-emerald-500 transition">
                                            <button type="submit"
                                                    class="shrink-0 p-1.5 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg transition"
                                                    title="Save">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            </button>
                                        </form>

                                        <p class="text-slate-400 font-medium text-sm mt-1">{{ $deck->flashcards_count }} cards</p>
                                    </div>

                                    {{-- Action buttons — shrink-0 keeps them fixed, never pushed --}}
                                    <div class="flex items-center gap-1 shrink-0">
                                        {{-- ✏️ Edit --}}
                                        <button @click="editing = true"
                                                class="opacity-0 group-hover:opacity-100 p-2 text-slate-300 hover:text-slate-600 hover:bg-slate-50 rounded-lg transition-all"
                                                title="Edit">
                                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>

                                        {{-- 🗑️ Delete --}}
                                        <form action="{{ route('decks.destroy', $deck) }}" method="POST"
                                            onsubmit="return confirm('Delete this deck?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="opacity-0 group-hover:opacity-100 p-2 text-gray-300 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all"
                                                    title="Delete">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>

                                </div>

                                <div class="w-full h-1.5 bg-slate-100 rounded-full mt-6 overflow-hidden">
                                    <div class="h-full rounded-full bg-gradient-to-r {{ $accent['stripe'] }}" style="width: {{ min(100, max(5, $deck->flashcards_count * 5)) }}%"></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    </div>
                @endif
                {{-- Pagination --}}
                @if ($decks->hasPages())
                    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-center space-x-2 mt-12 mb-20">
                        {{-- Previous Page Link --}}
                        @if ($decks->onFirstPage())
                            <span class="px-4 py-2 text-slate-300 bg-white border border-gray-100 rounded-2xl cursor-not-allowed">
                                ← <span class="hidden md:inline ml-1">Prev</span>
                            </span>
                        @else
                            <a href="{{ $decks->previousPageUrl() }}" class="px-4 py-2 text-slate-600 bg-white border border-gray-100 rounded-2xl hover:bg-slate-50 hover:border-emerald-300 transition-all shadow-sm">
                                ← <span class="hidden md:inline ml-1">Prev</span>
                            </a>
                        @endif

                        {{-- Pagination Elements --}}
                        <div class="flex items-center bg-white border border-gray-100 rounded-2xl p-1 shadow-sm">
                            @foreach ($decks->getUrlRange(max(1, $decks->currentPage() - 1), min($decks->lastPage(), $decks->currentPage() + 1)) as $page => $url)
                                @if ($page == $decks->currentPage())
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
                        @if ($decks->hasMorePages())
                            <a href="{{ $decks->nextPageUrl() }}" class="px-4 py-2 text-slate-600 bg-white border border-gray-100 rounded-2xl hover:bg-slate-50 hover:border-emerald-300 transition-all shadow-sm">
                                <span class="hidden md:inline mr-1">Next</span> →
                            </a>
                        @else
                            <span class="px-4 py-2 text-slate-300 bg-white border border-gray-100 rounded-2xl cursor-not-allowed">
                                <span class="hidden md:inline mr-1">Next</span> →
                            </span>
                        @endif
                    </nav>
                @endif
                </div>
            </div>

        </main>
    </div>
</x-layout>
