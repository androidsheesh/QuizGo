<x-layout>
    <div class="flex min-h-screen bg-[#F9FAFB]">

        <x-teacher-sidebar/>
        
        <main class="flex-1 p-6 md:p-12 overflow-y-auto relative">
            <x-dropdown-profile/>
            
            <div class="max-w-5xl mx-auto flex flex-col">

                {{-- Header Section --}}
                <div class="flex flex-col md:flex-row md:items-center justify-between mb-10 gap-4">
                    <div>
                        <h2 class="text-3xl font-bold text-slate-800">Assign Quiz</h2>
                        <p class="text-slate-500 mt-2">Select a quiz to assign to your students</p>
                    </div>

                    <div class="flex items-center space-x-4">
                        <div class="relative">
                            <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </span>
                            <input type="text" placeholder="Search quizzes..." class="pl-10 pr-4 py-2 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/10 w-48 md:w-64 transition-all">
                        </div>

                        <button class="flex items-center space-x-2 px-6 py-2.5 bg-slate-900 text-white rounded-2xl shadow-lg shadow-slate-200 hover:bg-slate-800 transition-all">
                            <span class="text-lg">＋</span>
                            <span class="font-semibold text-sm">Create New Quiz</span>
                        </button>
                    </div>
                </div>

                {{-- Quizzes Grid --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    
                    {{-- Quiz Item 1 --}}
                    <div class="group relative flex flex-col bg-white border border-gray-100 rounded-[2rem] overflow-hidden shadow-sm hover:shadow-xl hover:shadow-slate-200/50 transition-all duration-300 transform hover:-translate-y-1 cursor-pointer">
                        <div class="h-3 bg-red-400"></div>
                        <div class="flex-1 p-8">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h4 class="text-xl font-bold text-slate-800 group-hover:text-blue-600 transition-colors">Laravel Basics</h4>
                                    <p class="text-slate-400 font-medium text-sm mt-1">20 Questions • 30 Mins</p>
                                </div>
                            </div>
                            
                            <p class="text-gray-500 text-sm mt-4 mb-6">Test the foundational knowledge of Laravel framework, routing, and controllers.</p>
                            
                            <button class="w-full py-3 bg-slate-50 text-slate-700 font-semibold rounded-xl group-hover:bg-blue-50 group-hover:text-blue-600 border border-slate-100 transition-colors">
                                Assign to Class
                            </button>
                        </div>
                    </div>

                    {{-- Quiz Item 2 --}}
                    <div class="group relative flex flex-col bg-white border border-gray-100 rounded-[2rem] overflow-hidden shadow-sm hover:shadow-xl hover:shadow-slate-200/50 transition-all duration-300 transform hover:-translate-y-1 cursor-pointer">
                        <div class="h-3 bg-blue-500"></div>
                        <div class="flex-1 p-8">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h4 class="text-xl font-bold text-slate-800 group-hover:text-blue-600 transition-colors">UI/UX Principles</h4>
                                    <p class="text-slate-400 font-medium text-sm mt-1">15 Questions • 20 Mins</p>
                                </div>
                            </div>
                            
                            <p class="text-gray-500 text-sm mt-4 mb-6">A quiz measuring understanding of color theory, typography, and spacing.</p>
                            
                            <button class="w-full py-3 bg-slate-50 text-slate-700 font-semibold rounded-xl group-hover:bg-blue-50 group-hover:text-blue-600 border border-slate-100 transition-colors">
                                Assign to Class
                            </button>
                        </div>
                    </div>

                    {{-- Quiz Item 3 --}}
                    <div class="group relative flex flex-col bg-white border border-gray-100 rounded-[2rem] overflow-hidden shadow-sm hover:shadow-xl hover:shadow-slate-200/50 transition-all duration-300 transform hover:-translate-y-1 cursor-pointer">
                        <div class="h-3 bg-emerald-500"></div>
                        <div class="flex-1 p-8">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h4 class="text-xl font-bold text-slate-800 group-hover:text-blue-600 transition-colors">React Patterns</h4>
                                    <p class="text-slate-400 font-medium text-sm mt-1">25 Questions • 45 Mins</p>
                                </div>
                            </div>
                            
                            <p class="text-gray-500 text-sm mt-4 mb-6">Advanced React quiz containing hooks, state management, and side-effects.</p>
                            
                            <button class="w-full py-3 bg-slate-50 text-slate-700 font-semibold rounded-xl group-hover:bg-blue-50 group-hover:text-blue-600 border border-slate-100 transition-colors">
                                Assign to Class
                            </button>
                        </div>
                    </div>

                </div>

            </div>
        </main>
    </div>
</x-layout>
