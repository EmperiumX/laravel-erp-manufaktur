@props(['active', 'icon'])

@php
$classes = ($active ?? false)
            ? 'flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold shadow-md shadow-blue-900/35 transition-all duration-200 transform scale-[1.02]'
            : 'flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl text-slate-400 hover:bg-slate-800/60 hover:text-slate-100 font-medium transition-all duration-200 hover:translate-x-1';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    @if(isset($icon))
    <i class="{{ $icon }} text-lg {{ ($active ?? false) ? 'text-white' : 'text-slate-400 hover:text-slate-100 transition-colors' }}"></i>
    @endif
    <span>{{ $slot }}</span>
</a>
