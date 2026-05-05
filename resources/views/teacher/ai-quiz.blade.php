<x-layout>
    <div class="flex min-h-screen bg-[#F9FAFB]">
        <x-teacher-sidebar/>
        <main class="flex-1 p-6 md:p-12 overflow-y-auto relative">
            <x-dropdown-profile/>
            <div class="max-w-3xl mx-auto text-center py-20">
                <div class="w-20 h-20 bg-violet-100 rounded-2xl flex items-center justify-center mx-auto mb-6 text-4xl">🤖</div>
                <h2 class="text-3xl font-bold text-slate-800 mb-3">AI Quiz Generator</h2>
                <p class="text-slate-400 mb-8">This feature is coming soon. Go to Assign Quiz to use the AI generation tab.</p>
                <a href="{{ route('teacher.quiz.index') }}" class="inline-block px-8 py-3 bg-emerald-500 text-white font-bold rounded-2xl shadow-lg hover:bg-emerald-600 transition-all">Go to Assign Quiz</a>
            </div>
        </main>
    </div>
</x-layout>
