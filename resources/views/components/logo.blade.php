<!-- resources/views/components/logo.blade.php -->
@props(['class' => 'w-28 h-28 sm:w-32 sm:h-32 md:w-40 md:h-40 lg:w-48 lg:h-48 transition-all duration-300'])

<div class="relative {{ $class }} flex items-center justify-center mx-auto">
    <!-- Notice we are passing your utility classes down to the SVG using $attributes -->
    <x-logo-svg class="w-full h-full overflow-visible" />
</div>
