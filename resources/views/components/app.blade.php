<x-layout>
    <div class="flex h-screen overflow-hidden">
        {{-- Include your Sidebar here --}}
        @include('partials.sidebar')

        <main class="flex-1 overflow-y-auto p-12 relative">
            {{-- User Profile / Top Right Info --}}
            <div class="absolute top-8 right-12 flex items-center space-x-2">
                <div class="w-10 h-10 bg-emerald-200 rounded-full flex items-center justify-center overflow-hidden">
                     <span class="text-xs">🟩</span>
                </div>
                <span class="text-gray-600 font-bold">v</span>
            </div>

            {{-- Page Content Goes Here --}}
            <h1 class="text-2xl font-bold">Dashboard</h1>
            <p>Your main content starts here...</p>
        </main>
    </div>
</x-layout>
