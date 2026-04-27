<x-layout>
    <div class="flex min-h-screen bg-[#F9FAFB]">

        <x-teacher-sidebar/>
        
        <main class="flex-1 p-6 md:p-12 overflow-y-auto relative">
            <x-dropdown-profile/>
            
            <div class="max-w-3xl mx-auto flex flex-col">

                {{-- Header Section --}}
                <div class="mb-10">
                    <h2 class="text-3xl font-bold text-slate-800">Teacher Profile</h2>
                    <p class="text-slate-500 mt-2">Manage your personal information and settings</p>
                </div>

                {{-- Profile Card --}}
                <div class="bg-white rounded-[2rem] border border-gray-100 p-8 shadow-sm">
                    <div class="flex flex-col md:flex-row items-center gap-8 mb-8 pb-8 border-b border-gray-50">
                        
                        <div class="w-32 h-32 rounded-[2rem] bg-emerald-100 border-4 border-white shadow-lg flex items-center justify-center text-5xl">
                            👨‍🏫
                        </div>

                        <div class="flex-1 text-center md:text-left">
                            <h3 class="text-2xl font-bold text-slate-800">John Doe</h3>
                            <p class="text-emerald-600 font-medium mt-1">Computer Science Teacher</p>
                            <p class="text-slate-500 text-sm mt-2">Joined September 2025</p>
                        </div>

                        <div>
                            <button class="px-6 py-2.5 bg-slate-100 text-slate-700 font-semibold rounded-xl hover:bg-slate-200 transition-colors">
                                Edit Profile
                            </button>
                        </div>
                    </div>

                    {{-- Form Fields (Static) --}}
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-slate-600 mb-2">First Name</label>
                                <input type="text" value="John" disabled class="w-full p-4 bg-slate-50 border border-transparent rounded-2xl text-slate-700 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-600 mb-2">Last Name</label>
                                <input type="text" value="Doe" disabled class="w-full p-4 bg-slate-50 border border-transparent rounded-2xl text-slate-700 focus:outline-none">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-600 mb-2">Email Address</label>
                            <input type="email" value="john.doe@university.edu" disabled class="w-full p-4 bg-slate-50 border border-transparent rounded-2xl text-slate-700 focus:outline-none">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-600 mb-2">Department</label>
                            <select disabled class="w-full p-4 bg-slate-50 border border-transparent rounded-2xl text-slate-700 focus:outline-none appearance-none">
                                <option>Computer Science</option>
                                <option>Mathematics</option>
                                <option>Physics</option>
                            </select>
                        </div>
                    </div>

                </div>

            </div>
        </main>
    </div>
</x-layout>
