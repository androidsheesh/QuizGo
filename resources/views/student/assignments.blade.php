<x-layout>
    <div class="flex min-h-screen bg-[#F9FAFB]">

        <x-sidebar/>

        <x-dropdown-profile/>
        <main class="flex-1 p-6 md:p-12 overflow-y-auto relative">

            <div class="max-w-5xl mx-auto flex flex-col">

                {{-- Header --}}
                <div class="flex flex-col mb-10">
                    <h2 class="text-3xl md:text-4xl font-bold text-slate-800">Assignments</h2>
                    <p class="text-slate-500 mt-2">Join classes and view your pending quizzes.</p>
                </div>

                {{-- Join Class Section --}}
                <div class="bg-white rounded-[2rem] border border-gray-100 p-6 md:p-8 mb-10 shadow-sm">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                        <div>
                            <h3 class="text-xl font-bold text-slate-700">Join a Class</h3>
                            <p class="text-slate-400 text-sm mt-1">Ask your teacher for the class code, then enter it here.</p>
                        </div>
                        <form method="POST" action="{{ route('classroom.join') }}" class="flex w-full md:w-auto gap-3">
                            @csrf
                            <input type="text" name="code" placeholder="Enter Class Code" required
                                   class="flex-1 md:w-64 p-3 bg-slate-50 border border-slate-100 rounded-xl text-slate-700 font-mono font-bold uppercase focus:outline-none focus:ring-2 focus:ring-emerald-400/30 focus:border-emerald-300 transition-all">
                            <button type="submit" class="px-6 py-3 bg-emerald-500 text-white font-bold rounded-xl shadow-md shadow-emerald-200 hover:bg-emerald-600 transition-colors whitespace-nowrap">
                                Join Class
                            </button>
                        </form>
                    </div>

                    @if(session('success'))
                        <div class="mt-4 bg-emerald-50 text-emerald-700 text-sm font-medium px-4 py-3 rounded-xl border border-emerald-100">
                            ✓ {{ session('success') }}
                        </div>
                    @endif
                    @if($errors->has('code'))
                        <div class="mt-4 bg-red-50 text-red-700 text-sm font-medium px-4 py-3 rounded-xl border border-red-100">
                            {{ $errors->first('code') }}
                        </div>
                    @endif
                </div>

                {{-- My Classes Grid --}}
                <h3 class="text-xl font-bold text-slate-700 mb-6">My Classes</h3>

                @if($classrooms->isEmpty())
                    <div class="text-center py-16 bg-white rounded-3xl border border-gray-100 shadow-sm">
                        <div class="text-5xl mb-4">🏫</div>
                        <h4 class="text-lg font-bold text-slate-700">No classes yet</h4>
                        <p class="text-slate-400 text-sm mt-2">You haven't joined any classes. Use a code to join one above!</p>
                    </div>
                @else
                    @php $colors = ['bg-emerald-400', 'bg-blue-400', 'bg-amber-400', 'bg-rose-400', 'bg-violet-400']; @endphp
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($classrooms as $index => $classroom)
                            <div class="group flex flex-col bg-white border border-gray-100 rounded-[2rem] overflow-hidden shadow-sm hover:shadow-xl hover:shadow-slate-200/50 transition-all duration-300 transform hover:-translate-y-1">
                                <div class="h-3 {{ $colors[$index % count($colors)] }}"></div>
                                <div class="flex-1 p-6 flex flex-col">
                                    <div class="flex justify-between items-start mb-4">
                                        <div>
                                            <h4 class="text-xl font-bold text-slate-800">{{ $classroom->name }}</h4>
                                            <p class="text-sm font-medium text-slate-400 mt-1">{{ $classroom->teacher->firstname }} {{ $classroom->teacher->lastname }}</p>
                                        </div>
                                        <span class="font-mono bg-slate-100 px-2 py-1 rounded-lg text-xs font-bold text-slate-500">{{ $classroom->code }}</span>
                                    </div>

                                    @if($classroom->description)
                                        <p class="text-sm text-slate-500 mb-4 line-clamp-2 flex-1">{{ $classroom->description }}</p>
                                    @else
                                        <div class="flex-1"></div>
                                    @endif

                                    <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-50">
                                        <div class="text-sm font-medium text-slate-400">
                                            {{ $classroom->quiz_assignments_count }} {{ Str::plural('Quiz', $classroom->quiz_assignments_count) }}
                                        </div>
                                        <a href="{{ route('student.classroom.show', $classroom) }}" class="px-5 py-2 bg-slate-50 hover:bg-emerald-50 text-slate-700 hover:text-emerald-700 font-semibold text-sm rounded-xl transition-colors border border-slate-100">
                                            Enter Class →
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

            </div>
        </main>
    </div>
</x-layout>
