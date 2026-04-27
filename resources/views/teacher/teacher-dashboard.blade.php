<x-layout>
    <div class="flex min-h-screen bg-[#F9FAFB]">

        <x-teacher-sidebar/>
        
        <x-dropdown-profile/>
        <main class="flex-1 p-6 md:p-12 overflow-y-auto relative">

            <div class="max-w-4xl mx-auto flex flex-col">

                <div class="flex flex-col mb-10">
                    <h2 class="text-3xl md:text-4xl font-bold text-slate-800">Teacher Dashboard</h2>
                    <p class="text-slate-500 mt-2">Welcome back, manage your quizzes and students here.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
                    <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 flex items-center space-x-4">
                        <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center text-emerald-600 text-xl font-bold">
                            📝
                        </div>
                        <div>
                            <p class="text-slate-400 text-sm font-medium">Active Quizzes</p>
                            <p class="text-2xl font-bold text-slate-800">12</p>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 flex items-center space-x-4">
                        <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center text-blue-600 text-xl font-bold">
                            👨‍🎓
                        </div>
                        <div>
                            <p class="text-slate-400 text-sm font-medium">Total Students</p>
                            <p class="text-2xl font-bold text-slate-800">145</p>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 flex items-center space-x-4">
                        <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center text-yellow-600 text-xl font-bold">
                            ⭐
                        </div>
                        <div>
                            <p class="text-slate-400 text-sm font-medium">Avg Score</p>
                            <p class="text-2xl font-bold text-slate-800">85%</p>
                        </div>
                    </div>
                </div>

                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-slate-700">Recent Quizzes</h3>
                    <button class="text-slate-400 text-sm font-medium hover:text-slate-600 transition-colors">View all</button>
                </div>

                <div class="space-y-4">
                    <div class="group flex items-center bg-white border border-gray-100 rounded-3xl overflow-hidden shadow-sm hover:shadow-md transition-shadow cursor-pointer">
                        <div class="flex-1 p-6 flex justify-between items-center">
                            <div>
                                <h4 class="text-lg font-bold text-slate-800">Laravel Framework Exam</h4>
                                <p class="text-slate-400 text-sm mt-1">Due in 2 days • 45 Students</p>
                            </div>
                            <button class="px-4 py-2 bg-emerald-50 text-emerald-600 font-medium rounded-xl hover:bg-emerald-100 transition-colors">
                                View Results
                            </button>
                        </div>
                        <div class="w-2 self-stretch bg-emerald-400"></div>
                    </div>

                    <div class="group flex items-center bg-white border border-gray-100 rounded-3xl overflow-hidden shadow-sm hover:shadow-md transition-shadow cursor-pointer">
                        <div class="flex-1 p-6 flex justify-between items-center">
                            <div>
                                <h4 class="text-lg font-bold text-slate-800">UI/UX Basics</h4>
                                <p class="text-slate-400 text-sm mt-1">Due in 5 days • 30 Students</p>
                            </div>
                            <button class="px-4 py-2 bg-emerald-50 text-emerald-600 font-medium rounded-xl hover:bg-emerald-100 transition-colors">
                                View Results
                            </button>
                        </div>
                        <div class="w-2 self-stretch bg-blue-400"></div>
                    </div>
                </div>

            </div>
        </main>
    </div>
</x-layout>
