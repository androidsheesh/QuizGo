@php
    $accentColors = [
        ['stripe' => 'from-emerald-400 to-teal-400',  'dot' => '#6ee7b7', 'bar' => 'from-emerald-400 to-teal-400',  'label' => 'text-emerald-500', 'icon' => 'text-emerald-500'],
        ['stripe' => 'from-violet-400 to-purple-500', 'dot' => '#c4b5fd', 'bar' => 'from-violet-400 to-purple-500', 'label' => 'text-violet-500',  'icon' => 'text-violet-500'],
        ['stripe' => 'from-amber-400 to-orange-400',  'dot' => '#fcd34d', 'bar' => 'from-amber-400 to-orange-400',  'label' => 'text-amber-500',   'icon' => 'text-amber-500'],
        ['stripe' => 'from-sky-400 to-blue-500',      'dot' => '#93c5fd', 'bar' => 'from-sky-400 to-blue-500',      'label' => 'text-sky-500',     'icon' => 'text-sky-500'],
        ['stripe' => 'from-rose-400 to-pink-500',     'dot' => '#fca5a5', 'bar' => 'from-rose-400 to-pink-500',     'label' => 'text-rose-500',    'icon' => 'text-rose-500'],
        ['stripe' => 'from-cyan-400 to-emerald-400',  'dot' => '#67e8f9', 'bar' => 'from-cyan-400 to-emerald-400',  'label' => 'text-cyan-500',    'icon' => 'text-cyan-500'],
    ];
    $accent = $accentColors[$deck->id % count($accentColors)];
@endphp

@foreach($flashcards as $card)
    <div class="group flex flex-col bg-white border border-gray-100 rounded-[2rem] overflow-hidden shadow-sm hover:shadow-xl hover:shadow-emerald-100/50 transition-all duration-300 relative transform hover:-translate-y-1 flashcard-item"
            style="height:320px; background-image:radial-gradient(circle,{{ $accent['dot'] }}33 1px,transparent 1px); background-size:18px 18px;">

        {{-- Top colour bar — matches the deck's accent colour --}}
        <div class="h-2 w-full shrink-0 bg-gradient-to-r {{ $accent['bar'] }}"></div>

        {{-- Card body fills the remaining fixed height --}}
        <div class="flex-1 p-6 relative flex flex-col overflow-hidden">

            {{-- Action Buttons (Top Right) --}}
            <div class="absolute right-3 top-3 flex items-center gap-1 z-10">
                {{-- Edit Button --}}
                <button
                    type="button"
                    onclick="document.getElementById('edit-modal-{{ $card->id }}').classList.remove('hidden')"
                    class="p-2 text-gray-300 hover:text-emerald-500 hover:bg-emerald-50 rounded-lg transition"
                    title="Edit Card">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </button>

                {{-- Delete Button --}}
                <form action="{{ route('flashcards.destroy', $card) }}" method="POST" onsubmit="return confirm('Delete this card?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="p-2 text-gray-300 hover:text-red-500 hover:bg-red-50 rounded-lg transition" title="Delete Card">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </form>
            </div>

            {{-- Edit Modal Overlay --}}
            <div
                id="edit-modal-{{ $card->id }}"
                class="hidden absolute inset-0 bg-white/95 backdrop-blur-sm rounded-[2rem] z-10 flex flex-col justify-center p-6">
                <form action="{{ route('flashcards.update', $card) }}" method="POST" class="flex flex-col gap-3">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Question</label>
                        <textarea
                            name="question"
                            rows="3"
                            class="w-full p-3 bg-slate-50 border border-transparent focus:border-emerald-400 focus:bg-white rounded-xl text-slate-700 text-sm focus:outline-none transition-all resize-none"
                        >{{ $card->question }}</textarea>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Answer</label>
                        <textarea
                            name="answer"
                            rows="3"
                            class="w-full p-3 bg-slate-50 border border-transparent focus:border-emerald-400 focus:bg-white rounded-xl text-slate-700 text-sm focus:outline-none transition-all resize-none"
                        >{{ $card->answer }}</textarea>
                    </div>
                    <div class="flex gap-2 mt-1">
                        <button
                            type="submit"
                            class="flex-1 py-2 bg-slate-900 text-white text-sm font-bold rounded-xl hover:bg-slate-800 transition-colors"
                        >
                            Save
                        </button>
                        <button
                            type="button"
                            onclick="document.getElementById('edit-modal-{{ $card->id }}').classList.add('hidden')"
                            class="flex-1 py-2 bg-slate-100 text-slate-600 text-sm font-bold rounded-xl hover:bg-slate-200 transition-colors"
                        >
                            Cancel
                        </button>
                    </div>
                </form>
            </div>

            {{-- Question — takes remaining space, clamps at 4 lines --}}
            <div class="flex-1 overflow-hidden pr-6 mb-3">
                <p class="text-[10px] font-bold {{ $accent['label'] }} uppercase tracking-widest mb-2">Question</p>
                <p class="text-slate-800 font-bold leading-relaxed line-clamp-4">{{ $card->question }}</p>
            </div>

            {{-- Answer — fixed height zone, always the same size --}}
            <div class="shrink-0 pt-4 border-t border-slate-100" style="height: 96px; overflow: hidden;">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Answer</p>
                <p class="text-slate-600 text-sm leading-relaxed line-clamp-3">{{ $card->answer }}</p>
            </div>

        </div>
    </div>
@endforeach
