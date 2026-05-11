<x-signup title="QuizGo | Sign In">
    <div class="h-screen bg-[#f8f8f8] flex items-center justify-center p-6">

        <div class="w-full max-w-3xl min-h-[800px] bg-white border border-gray-200 rounded-2xl shadow-md p-14">

            <div class="text-center mb-6">
                <h1 class="text-5xl font-extrabold text-black tracking-tight mb-1">QuizGo</h1>
                <p class="text-xl text-gray-800 font-light">Welcome!</p>
            </div>

            <div class="flex justify-center mb-8">
                <div class="bg-[#f5f4f4] p-1 rounded-full flex w-72">
                    <a href="/signup"
                        class="flex-1 py-2 text-center text-sm font-medium text-gray-500 hover:text-gray-900 transition">
                        Sign up
                    </a>
                    <button type="button"
                        class="flex-1 py-2 bg-white rounded-full text-sm font-medium text-gray-900 shadow-sm">
                        Sign in
                    </button>
                </div>
            </div>

            <form action="/signin" method="POST" class="space-y-6">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-800 mb-1 ml-1">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required autocomplete="email"
                        class="w-full px-4 py-3 bg-[#f5f4f4] border border-gray-100 rounded-full focus:outline-none focus:ring-2 focus:ring-gray-300 focus:bg-white transition">
                    @error('email')
                        <p class="text-red-500 text-sm mt-1 ml-2">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-800 mb-1 ml-1">Password</label>
                    <input type="password" name="password" id="password" required autocomplete="current-password"
                        class="w-full px-4 py-3 bg-[#f5f4f4] border border-gray-100 rounded-full focus:outline-none focus:ring-2 focus:ring-gray-300 focus:bg-white transition">
                    @error('password')
                        <p class="text-red-500 text-sm mt-1 ml-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-4">
                    <button type="submit"
                        class="w-full py-3 rounded-full text-sm font-medium text-white bg-black hover:bg-gray-800 transition">
                        Sign In
                    </button>
                </div>

            </form>

            <div class="mt-12 text-center border-t border-gray-100 pt-8">
                <p class="text-sm text-gray-500 font-medium mb-3">Are you an educator?</p>
                <a href="{{ route('teacher.signin') }}" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-emerald-50 text-emerald-600 text-sm font-bold rounded-full hover:bg-emerald-100 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    Login as Teacher
                </a>
            </div>

        </div>

    </div>
</x-signup>
