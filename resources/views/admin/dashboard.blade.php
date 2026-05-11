<x-layout>
    <div class="min-h-screen bg-[#F9FAFB] flex flex-col">
        {{-- Admin Navbar --}}
        <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between shadow-sm sticky top-0 z-30">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-slate-900 rounded-xl flex items-center justify-center text-white font-bold text-xl">A</div>
                <div>
                    <h1 class="text-xl font-bold text-slate-800 leading-tight">Admin Dashboard</h1>
                    <p class="text-xs text-slate-500 font-medium">System Management</p>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <form method="POST" action="/logout">
                    @csrf
                    <button type="submit" class="px-5 py-2 text-sm font-bold text-slate-600 bg-slate-100 rounded-full hover:bg-slate-200 transition-colors">
                        Sign Out
                    </button>
                </form>
            </div>
        </header>

        <main class="flex-1 max-w-7xl mx-auto w-full px-6 py-10 grid grid-cols-1 lg:grid-cols-3 gap-8" x-data="{ selectedTeacher: null }">
            
            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="col-span-full bg-emerald-50 text-emerald-700 border border-emerald-200 px-6 py-4 rounded-2xl flex items-center gap-3 shadow-sm" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)">
                    <span class="text-xl">✓</span> {{ session('success') }}
                </div>
            @endif

            {{-- Left Column: Create Teacher Form --}}
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white border border-gray-200 rounded-[2rem] p-8 shadow-sm">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-xl bg-violet-100 flex items-center justify-center text-violet-500 text-xl">👨‍🏫</div>
                        <h2 class="text-xl font-bold text-slate-800">Create Teacher</h2>
                    </div>

                    <form action="{{ route('admin.teachers.store') }}" method="POST" class="space-y-5">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 ml-1">First Name</label>
                            <input type="text" name="firstname" value="{{ old('firstname') }}" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-violet-400 focus:bg-white transition text-sm">
                            @error('firstname')<p class="text-red-500 text-xs mt-1 ml-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 ml-1">Last Name</label>
                            <input type="text" name="lastname" value="{{ old('lastname') }}" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-violet-400 focus:bg-white transition text-sm">
                            @error('lastname')<p class="text-red-500 text-xs mt-1 ml-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 ml-1">Email Address</label>
                            <input type="email" name="email" value="{{ old('email') }}" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-violet-400 focus:bg-white transition text-sm">
                            @error('email')<p class="text-red-500 text-xs mt-1 ml-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 ml-1">Password</label>
                            <input type="password" name="password" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-violet-400 focus:bg-white transition text-sm">
                            @error('password')<p class="text-red-500 text-xs mt-1 ml-1">{{ $message }}</p>@enderror
                        </div>

                        <button type="submit" class="w-full py-4 mt-2 bg-slate-900 text-white font-bold text-sm rounded-xl hover:bg-slate-800 shadow-md transition">
                            Create Account
                        </button>
                    </form>
                </div>
            </div>

            {{-- Right Column: Teacher List --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white border border-gray-200 rounded-[2rem] p-8 shadow-sm min-h-[600px]">
                    <h2 class="text-xl font-bold text-slate-800 mb-6 flex items-center gap-2">
                        Registered Teachers 
                        <span class="px-2 py-0.5 bg-slate-100 text-slate-500 text-xs rounded-full">{{ $teachers->count() }}</span>
                    </h2>

                    @if($teachers->isEmpty())
                        <div class="text-center py-20 border-2 border-dashed border-gray-100 rounded-2xl">
                            <div class="text-4xl mb-3">👻</div>
                            <p class="text-slate-400 font-medium text-sm">No teachers registered yet.</p>
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach($teachers as $teacher)
                                <div class="flex items-center justify-between p-4 border border-gray-100 rounded-2xl hover:border-slate-300 hover:shadow-sm transition bg-slate-50/50 group cursor-pointer"
                                     @click="selectedTeacher = {
                                        id: {{ $teacher->id }},
                                        name: '{{ addslashes($teacher->firstname . ' ' . $teacher->lastname) }}',
                                        email: '{{ addslashes($teacher->initial_email ?? $teacher->email) }}',
                                        password: '{{ addslashes($teacher->initial_password ?? 'Not Available') }}',
                                        avatar: '{{ $teacher->profile_picture ? asset('storage/' . $teacher->profile_picture) : '' }}',
                                        created: '{{ $teacher->created_at->format('M d, Y') }}'
                                     }">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 bg-white border border-gray-200 rounded-full flex items-center justify-center text-lg shadow-sm overflow-hidden shrink-0">
                                            @if($teacher->profile_picture)
                                                <img src="{{ asset('storage/' . $teacher->profile_picture) }}" class="w-full h-full object-cover">
                                            @else
                                                👨‍🏫
                                            @endif
                                        </div>
                                        <div>
                                            <h3 class="font-bold text-slate-800">{{ $teacher->firstname }} {{ $teacher->lastname }}</h3>
                                            <p class="text-xs text-slate-500">{{ $teacher->initial_email ?? $teacher->email }} &bull; Added {{ $teacher->created_at->format('M d, Y') }}</p>
                                        </div>
                                    </div>
                                    <form action="{{ route('admin.teachers.destroy', $teacher) }}" method="POST" onsubmit="return confirm('Delete this teacher account permanently?');" class="shrink-0 ml-4">
                                        @csrf @method('DELETE')
                                        <button type="submit" @click.stop class="w-10 h-10 rounded-full flex items-center justify-center text-red-300 hover:bg-red-50 hover:text-red-500 transition opacity-0 group-hover:opacity-100" title="Delete Teacher">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- Teacher Details Modal --}}
            <div x-show="selectedTeacher" x-cloak class="fixed inset-0 z-50 flex items-center justify-center" x-transition>
                <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="selectedTeacher = null"></div>
                <div class="relative bg-white rounded-[2rem] shadow-2xl w-full max-w-lg mx-4 p-8 overflow-hidden" x-transition x-show="selectedTeacher">
                    <button @click="selectedTeacher = null" class="absolute top-6 right-6 w-8 h-8 flex items-center justify-center bg-slate-100 text-slate-500 rounded-full hover:bg-slate-200 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                    
                    <div class="flex items-center gap-6 mb-8">
                        <div class="w-24 h-24 bg-slate-100 border-4 border-white shadow-md rounded-full flex items-center justify-center text-4xl overflow-hidden shrink-0">
                            <template x-if="selectedTeacher?.avatar">
                                <img :src="selectedTeacher.avatar" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!selectedTeacher?.avatar">
                                <span>👨‍🏫</span>
                            </template>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-slate-800" x-text="selectedTeacher?.name"></h3>
                            <p class="text-emerald-600 font-bold text-sm">Teacher Account</p>
                        </div>
                    </div>

                    <div class="space-y-5">
                        <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Original Email Address</label>
                            <div class="flex items-center justify-between">
                                <p class="text-slate-700 font-mono font-bold text-lg" x-text="selectedTeacher?.email"></p>
                            </div>
                        </div>
                        <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Original Password</label>
                            <div class="flex items-center justify-between">
                                <p class="text-slate-700 font-mono font-bold text-lg" x-text="selectedTeacher?.password"></p>
                            </div>
                        </div>
                        <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Date Joined</label>
                            <p class="text-slate-700 font-medium" x-text="selectedTeacher?.created"></p>
                        </div>
                    </div>
                </div>
            </div>

        </main>
    </div>
</x-layout>
