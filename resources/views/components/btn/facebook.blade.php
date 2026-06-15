@props(['href' => '#', 'size' => 'btn-md'])
{{-- Unified Facebook button — brand blue, shine sweep, lift + active scale. --}}
<a href="{{ $href }}" target="_blank" rel="noopener noreferrer"
   {{ $attributes->merge(['class' => 'btn btn-facebook btn-shine ' . $size]) }}>
    <x-icon.facebook class="icon-sm btn-ico" />
    <span class="relative z-10">{{ $slot }}</span>
</a>
