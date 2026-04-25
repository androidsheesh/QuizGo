<x-layout>
    <div class="flex min-h-screen bg-[#F9FAFB]">
        <x-sidebar/>


        <main class="max-w-6xl mx-auto mt-16 md:mt-20">
            <x-dropdown-profile/>
            <div class="max-w-6xl mx-auto">
                {{-- Header Section --}}
                <div class="flex flex-col md:flex-row md:items-center justify-between mb-10 gap-4">
                    <h2 class="text-3xl font-bold text-slate-800">My decks</h2>

                    <div class="flex items-center space-x-4">
                        <div class="relative">
                            <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </span>
                            <input type="text" placeholder="Search decks..." class="pl-10 pr-4 py-2 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/10 w-48 md:w-64 transition-all">
                        </div>

                        <button class="flex items-center space-x-2 px-6 py-2.5 bg-slate-900 text-white rounded-2xl shadow-lg shadow-slate-200 hover:bg-slate-800 transition-all">
                            <span class="text-lg">＋</span>
                            <span class="font-semibold text-sm">Add deck</span>
                        </button>
                    </div>
                </div>

                {{-- Decks Grid --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($decks as $deck)
                        <div class="group relative flex bg-white border border-gray-100 rounded-[2rem] overflow-hidden shadow-sm hover:shadow-xl hover:shadow-slate-200/50 transition-all duration-300 transform hover:-translate-y-1 cursor-pointer">
                            {{-- Side Accent Color --}}
                            <div class="w-3 {{ $deck->color_class ?? 'bg-blue-500' }}"></div>

                            <div class="flex-1 p-8">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <h4 class="text-xl font-bold text-slate-800 group-hover:text-blue-600 transition-colors">{{ $deck->name }}</h4>
                                        <p class="text-slate-400 font-medium text-sm mt-1">{{ $deck->cards_count }} cards</p>
                                    </div>
                                    <button class="p-2 text-gray-300 hover:text-slate-600 hover:bg-slate-50 rounded-lg transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                </div>

                                {{-- Progress indicator (Visual fluff for the minimal look) --}}
                                <div class="w-full h-1.5 bg-slate-50 rounded-full mt-6 overflow-hidden">
                                    <div class="h-full bg-slate-200 rounded-full" style="width: 40%"></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

            </div>
        </main>
    </div>
</x-layout>
