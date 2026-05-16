<x-signup title="QuizGo | Teacher Sign In">
    <div class="h-screen bg-slate-50 flex items-center justify-center p-6">

        <div class="w-full max-w-3xl min-h-[800px] bg-white border border-gray-200 rounded-2xl shadow-lg p-14 relative overflow-hidden">
            
            {{-- Decorative Accent --}}
            <div class="absolute top-0 left-0 w-full h-2 bg-emerald-500"></div>

            <div class="text-center mb-10">
                <h1 class="text-5xl font-extrabold text-slate-800 tracking-tight mb-2">QuizGo</h1>
                <p class="text-xl text-emerald-600 font-bold">Educator Portal</p>
                <p class="text-sm text-slate-400 mt-2">Sign in to manage your classrooms and quizzes.</p>
            </div>

            @if (session('confirm_other_device'))
                <div class="mb-6 max-w-md mx-auto rounded-xl border border-amber-200 bg-amber-50 p-5 text-center">
                    <p class="text-sm font-bold text-amber-900">Are you sure you wanted to login to other device?</p>
                    <p class="mt-1 text-sm text-amber-800">Choosing yes will log out your other device and continue here.</p>
                    <div class="mt-4 flex justify-center gap-3">
                        <form action="{{ route('teacher.signin.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="email" value="{{ old('email') }}">
                            <input type="hidden" name="confirm_other_device" value="1">
                            <button type="submit" class="rounded-xl bg-emerald-500 px-5 py-2 text-sm font-bold text-white hover:bg-emerald-600 transition">
                                Yes
                            </button>
                        </form>
                        <a href="{{ route('teacher.signin') }}" class="rounded-xl border border-amber-300 px-5 py-2 text-sm font-bold text-amber-900 hover:bg-amber-100 transition">
                            No
                        </a>
                    </div>
                </div>
            @endif

            <form action="{{ route('teacher.signin.store') }}" method="POST" class="space-y-6 max-w-md mx-auto">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-bold text-slate-700 mb-2 ml-1 uppercase tracking-wider">Email Address</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required autocomplete="email"
                        class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:bg-white transition font-medium">
                    @error('email')
                        <p class="text-red-500 text-sm mt-2 ml-2 font-medium flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-bold text-slate-700 mb-2 ml-1 uppercase tracking-wider">Password</label>
                    <input type="password" name="password" id="password" required autocomplete="current-password"
                        class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:bg-white transition font-medium">
                    @error('password')
                        <p class="text-red-500 text-sm mt-2 ml-2 font-medium flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="pt-6">
                    <button type="submit"
                        class="w-full py-4 rounded-xl text-base font-bold text-white bg-emerald-500 hover:bg-emerald-600 shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all">
                        Sign In as Teacher
                    </button>
                </div>
            </form>

            <div class="mt-12 text-center">
                <a href="/signin" class="text-sm font-semibold text-slate-400 hover:text-slate-600 transition flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Back to Student Login
                </a>
            </div>

        </div>

    </div>
</x-signup>
