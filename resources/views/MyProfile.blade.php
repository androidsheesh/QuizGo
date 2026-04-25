<x-layout>
    <div class="flex min-h-screen bg-[#F9FAFB]">
        <x-sidebar/>

        <main class="flex-1 flex justify-center py-12 overflow-y-auto">
            <div class="w-full max-w-3xl px-6">

                {{-- Header Section: Centered items --}}
                <div class="flex flex-col items-center md:flex-row md:space-x-8 mb-12 text-center md:text-left">
                    <div class="relative mb-4 md:mb-0">
                        <div class="w-32 h-32 bg-emerald-100 rounded-full flex items-center justify-center overflow-hidden border-4 border-white shadow-md">
                            {{-- Placeholder for actual avatar --}}
                            <span class="text-6xl">👤</span>
                        </div>
                        <button class="absolute bottom-0 right-0 bg-white border border-gray-200 p-2 rounded-full shadow-sm hover:bg-gray-50">
                            📷
                        </button>
                    </div>
                    <div>
                        <h2 class="text-4xl font-bold text-slate-800">Chrishian Degaom</h2>
                        <p class="text-slate-400 text-lg mt-1">c.degaom@gmail.com</p>
                    </div>
                </div>

                <hr class="border-gray-100 mb-10">

                <div class="space-y-10">
                    {{-- Account Details Card --}}
                    <section>
                        <h3 class="text-xl font-bold text-slate-700 mb-6 flex items-center gap-2">
                            <span>⚙️</span> Account Details
                        </h3>

                        <div class="bg-white border border-gray-200 rounded-[2.5rem] p-8 md:p-10 shadow-sm">
                            <div class="space-y-8">
                                {{-- Email --}}
                                <div>
                                    <label class="block text-sm font-bold text-slate-500 uppercase tracking-wider mb-2">Email address</label>
                                    <div class="relative group">
                                        <input type="text" value="c.degaom@gmail.com" readonly
                                               class="w-full p-4 bg-slate-50 border border-slate-100 rounded-2xl text-slate-600 font-medium focus:outline-none">
                                        <button class="absolute right-4 top-1/2 -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-opacity text-gray-500 font-bold text-sm">
                                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>
                                    </div>
                                </div>

                                {{-- Display Name --}}
                                <div>
                                    <label class="block text-sm font-bold text-slate-500 uppercase tracking-wider mb-2">Display Name</label>
                                    <div class="relative group">
                                        <input type="text" value="Chrishian Degaom" readonly
                                               class="w-full p-4 bg-slate-50 border border-slate-100 rounded-2xl text-slate-600 font-medium focus:outline-none">
                                        <button class="absolute right-4 top-1/2 -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-opacity text-gray-500 font-bold text-sm">
                                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>
                                    </div>
                                </div>

                                {{-- Bio --}}
                                <div>
                                    <label class="block text-sm font-bold text-slate-500 uppercase tracking-wider mb-2">Bio</label>
                                    <div class="relative group">
                                        <textarea rows="3" readonly placeholder="Tell us about yourself..."
                                                  class="w-full p-4 bg-slate-50 border border-slate-100 rounded-2xl text-slate-600 font-medium focus:outline-none resize-none"></textarea>
                                        <button class="absolute right-4 top-4 opacity-0 group-hover:opacity-100 transition-opacity text-gray-500 font-bold text-sm">
                                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    {{-- Danger Zone --}}
                    <button class="w-full flex justify-between items-center bg-white border border-red-100 p-6 rounded-[2rem] shadow-sm hover:bg-red-50 transition-all group">
                        <div class="flex items-center space-x-4">
                            <div class="bg-red-50 border border-red-100 p-3 rounded-xl">
                                <span class="text-red-500">🗑️</span>
                            </div>
                            <div class="text-left">
                                <span class="block text-lg font-bold text-red-600">Delete Account</span>
                                <span class="text-sm text-red-400">This action is permanent and cannot be undone.</span>
                            </div>
                        </div>
                        <span class="text-2xl text-red-300 group-hover:translate-x-1 group-hover:text-red-500 transition-all">»</span>
                    </button>
                </div>
            </div>
        </main>
    </div>
</x-layout>
