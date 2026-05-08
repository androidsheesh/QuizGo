@props(['route', 'icon', 'label', 'color' => 'indigo'])

@php
    $isActive = request()->routeIs($route) ||
               ($label == 'Assignments' && (request()->routeIs('student.classroom.*') || request()->routeIs('student.quiz.*')));

    $themes = [
        'indigo'  => [
            'bg'    => 'bg-indigo-50',
            'text'  => 'text-indigo-600',
            'icon'  => 'text-indigo-500',
            'glow'  => 'shadow-[0_0_15px_-2px_rgba(99,102,241,0.5)]'
        ],
        'emerald' => [
            'bg'    => 'bg-emerald-50',
            'text'  => 'text-emerald-600',
            'icon'  => 'text-emerald-500',
            'glow'  => 'shadow-[0_0_15px_-2px_rgba(16,185,129,0.5)]'
        ],
        'orange'  => [
            'bg'    => 'bg-orange-50',
            'text'  => 'text-orange-600',
            'icon'  => 'text-orange-500',
            'glow'  => 'shadow-[0_0_15px_-2px_rgba(249,115,22,0.5)]'
        ],
        'rose'    => [
            'bg'    => 'bg-rose-50',
            'text'  => 'text-rose-600',
            'icon'  => 'text-rose-500',
            'glow'  => 'shadow-[0_0_15px_-2px_rgba(244,63,94,0.5)]'
        ],
    ][$color] ?? [
        'bg'    => 'bg-slate-50',
        'text'  => 'text-slate-600',
        'icon'  => 'text-slate-500',
        'glow'  => 'shadow-none'
    ];
@endphp

<a href="{{ route($route) }}"
   {{ $attributes->merge(['class' => "flex items-center space-x-3 p-3 rounded-2xl transition-all duration-200 group " .
   ($isActive
        ? "{$themes['bg']} {$themes['text']} shadow-[0_4px_0_0_rgba(0,0,0,0.05)] translate-y-[-2px]"
        : "text-slate-500 hover:bg-white hover:text-slate-900")]) }}>

    {{-- Icon Wrapper: Always glowing with the theme color --}}
    <div class="flex items-center justify-center w-10 h-10 rounded-xl transition-all duration-300 bg-white
        {{ $themes['glow'] }}
        {{ $isActive ? 'rotate-0' : 'group-hover:rotate-12 group-hover:scale-110' }}">

        <span class="material-symbols-rounded text-[24px] transition-all
            {{-- Icon is always colored now, but gets a "FILL" effect when active --}}
            {{ $themes['icon'] }}
            {{ $isActive ? "[font-variation-settings:'FILL'_1]" : "[font-variation-settings:'FILL'_0]" }}">
            {{ $icon }}
        </span>
    </div>

    {{-- Text Label --}}
    <span class="hidden md:block {{ $isActive ? 'font-black tracking-tight' : 'font-bold' }}">
        {{ $label }}
    </span>
</a>
