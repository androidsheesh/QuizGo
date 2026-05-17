@props(['class' => 'w-28 h-28 sm:w-32 sm:h-32 md:w-40 md:h-40 lg:w-48 lg:h-48 transition-all duration-300'])

<div class="relative {{ $class }} flex items-center justify-center mx-auto">
  <svg viewBox="0 0 280 280" class="w-full h-full overflow-visible" xmlns="http://www.w3.org/2000/svg">
    <rect x="100" y="90" width="80" height="110" rx="12" class="fill-white stroke-[#29a054] stroke-[7px]" transform="translate(-35, 20) rotate(-45, 140, 145)" />
    <rect x="100" y="90" width="80" height="110" rx="12" class="fill-white stroke-[#f58e0b] stroke-[7px]" transform="translate(-20, 10) rotate(-25, 140, 145)" />
    <rect x="100" y="90" width="80" height="110" rx="12" class="fill-white stroke-[#fab800] stroke-[7px]" transform="translate(-5, -5) rotate(-5, 140, 145)" />
    <g transform="translate(15, 5) rotate(20, 140, 145)">
      <rect x="100" y="90" width="80" height="110" rx="12" class="fill-white stroke-[#007ce4] stroke-[7px]" />
      <circle cx="140" cy="140" r="16" class="fill-none stroke-[#007ce4] stroke-[7px]" />
      <path d="M 149 150 C 155 172, 175 160, 185 145 L 195 120" class="fill-none stroke-white stroke-[16px] stroke-linecap-round" />
      <polygon points="202,115 182,122 195,138" class="fill-white stroke-white stroke-[10px] stroke-linejoin-round" />
      <path d="M 149 150 C 155 172, 175 160, 185 145 L 195 120" class="fill-none stroke-[#007ce4] stroke-[7px] stroke-linecap-round" />
      <polygon points="198,118 184,124 193,135" class="fill-[#007ce4] stroke-[#007ce4] stroke-[2px] stroke-linejoin-round" />
    </g>
    <g class="stroke-[#007ce4] stroke-[5px] stroke-linecap-round">
      <line x1="145" y1="62" x2="145" y2="46" />
      <line x1="158" y1="68" x2="170" y2="56" />
      <line x1="165" y1="80" x2="181" y2="80" />
    </g>
  </svg>
</div>
