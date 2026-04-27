<x-layout>
    {{-- Optional: Keep the Breeze header slot if your custom layout renders it --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="flex min-h-screen bg-[#F9FAFB]">

        {{-- Custom Navigation / Sidebar --}}
        <x-sidebar/>

        {{-- Custom Profile Dropdown --}}
        <x-dropdown-profile/>

        {{-- [ MAIN CONTENT ] --}}
        <main class="flex-1 p-6 md:p-12 overflow-y-auto relative">

            <div class="max-w-3xl mx-auto flex flex-col items-center">

                {{-- Hero Section --}}
                <div class="flex flex-col items-center text-center mb-10">
                    <div class="w-24 h-24 bg-emerald-400 rounded-2xl flex items-center justify-center shadow-xl shadow-emerald-200/50 transform rotate-3 mb-8">
                        <div class="w-12 h-12 bg-emerald-900/20 rounded-lg border-b-4 border-emerald-900/30"></div>
                    </div>
                    <h2 class="text-3xl md:text-4xl font-medium text-slate-800">What do you want to Study?</h2>
                </div>

                {{-- Search / Input --}}
                <div class="w-full mb-8">
                    <input type="text" placeholder="I want to study..."
                           class="w-full p-6 bg-white border border-gray-200 rounded-[2rem] text-xl shadow-sm focus:outline-none focus:ring-4 focus:ring-blue-500/5 focus:border-blue-400 transition-all placeholder:text-gray-300">
                </div>

                {{-- Action Buttons --}}
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

                {{-- Decks List --}}
                <div class="w-full max-w-2xl">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold text-slate-700">My decks</h3>
                        <button class="text-2xl text-slate-300 hover:text-slate-600 transition-colors">+</button>
                    </div>

                    <div class="space-y-4">
                        {{-- Deck Item 1 --}}
                        <div class="group flex items-center bg-white border border-gray-100 rounded-3xl overflow-hidden shadow-sm hover:shadow-md transition-shadow cursor-pointer">
                            <div class="flex-1 p-6 flex justify-between items-center">
                                <div>
                                    <h4 class="text-lg font-bold text-slate-800">Laravel</h4>
                                    <p class="text-slate-400 text-sm">121 cards</p>
                                </div>
                                <button class="opacity-0 group-hover:opacity-100 text-slate-300 hover:text-slate-600 transition-all">
                                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                            </div>
                            <div class="w-8 self-stretch bg-red-500"></div>
                        </div>

                        {{-- Deck Item 2 --}}
                        <div class="group flex items-center bg-white border border-gray-100 rounded-3xl overflow-hidden shadow-sm hover:shadow-md transition-shadow cursor-pointer">
                            <div class="flex-1 p-6 flex justify-between items-center">
                                <div>
                                    <h4 class="text-lg font-bold text-slate-800">Untitled</h4>
                                    <p class="text-slate-400 text-sm">12 cards</p>
                                </div>
                                <button class="opacity-0 group-hover:opacity-100 text-slate-300 hover:text-slate-600 transition-all">
                                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                            </div>
                            <div class="w-8 self-stretch bg-yellow-400"></div>
                        </div>
                    </div>

                    <button class="w-full mt-8 py-2 text-slate-400 text-sm font-medium hover:text-slate-600 transition-colors">
                        View all
                    </button>
                </div>
            </div>
        </main>
    </div>
</x-layout>
