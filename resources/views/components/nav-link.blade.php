@props(['route', 'icon', 'label', 'color' => 'indigo'])

@php
    // Logic for active state (includes sub-routes for Assignments)
    $isActive = request()->routeIs($route) ||
               ($label == 'Assignments' && (request()->routeIs('student.classroom.*') || request()->routeIs('student.quiz.*')));

    // Minimalist Palette Mapping
    $themes = [
        'indigo'  => ['bg' => 'bg-indigo-50',  'text' => 'text-indigo-600', 'icon' => 'text-indigo-500'],
        'emerald' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-600', 'icon' => 'text-emerald-500'],
        'orange'  => ['bg' => 'bg-orange-50',  'text' => 'text-orange-600', 'icon' => 'text-orange-500'],
        'rose'    => ['bg' => 'bg-rose-50',    'text' => 'text-rose-600',   'icon' => 'text-rose-500'],
    ][$color] ?? ['bg' => 'bg-slate-50', 'text' => 'text-slate-600', 'icon' => 'text-slate-500'];
@endphp

<a href="{{ route($route) }}"
   {{ $attributes->merge(['class' => "flex items-center space-x-3 p-3 rounded-2xl transition-all duration-200 group " .
   ($isActive
        ? "{$themes['bg']} {$themes['text']} shadow-[0_4px_0_0_rgba(0,0,0,0.05)] translate-y-[-2px]"
        : "text-slate-400 hover:bg-slate-50 hover:text-slate-600 active:translate-y-0")]) }}>

    {{-- Icon Wrapper: Pop-out effect on hover --}}
    <div class="flex items-center justify-center w-10 h-10 rounded-xl transition-all duration-300
        {{ $isActive ? 'bg-white shadow-sm rotate-0' : 'group-hover:bg-white group-hover:rotate-12 group-hover:shadow-md' }}">
        <span class="material-symbols-rounded text-[24px] transition-all
            {{ $isActive ? "{$themes['icon']} [font-variation-settings:'FILL'_1]" : "text-slate-300 group-hover:{$themes['icon']}" }}">
            {{ $icon }}
        </span>
    </div>

    {{-- Text Label --}}
    <span class="hidden md:block {{ $isActive ? 'font-black tracking-tight' : 'font-bold' }}">
        {{ $label }}
    </span>
</a>
