@props(['href' => '#', 'size' => 'btn-md'])
{{-- Unified WhatsApp button — brand green, shine sweep, lift + active scale. --}}
<a href="{{ $href }}" target="_blank" rel="noopener noreferrer"
   {{ $attributes->merge(['class' => 'btn btn-whatsapp btn-shine ' . $size]) }}>
    <x-icon.whatsapp class="icon-sm btn-ico" />
    <span class="relative z-10">{{ $slot }}</span>
</a>
