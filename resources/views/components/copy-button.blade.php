@props(['text', 'label'])
{{-- One-tap copy for reference numbers (order no., tracking no., booking ref).
     Flips to a green check for 2s as the "copied" confirmation; the sr-only
     status line announces it to screen readers. --}}
<span x-data="{ copied: false }" class="inline-flex items-center">
    <button type="button"
            @click="navigator.clipboard?.writeText(@js($text)).then(() => { copied = true; setTimeout(() => copied = false, 2000) })"
            aria-label="{{ $label }}"
            title="{{ $label }}"
            class="p-1.5 rounded-md text-gray-400 hover:text-brand-red hover:bg-brand-red/10 active:scale-90 transition-all align-middle">
        <svg x-show="!copied" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><rect width="14" height="14" x="8" y="8" rx="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>
        <svg x-show="copied" x-cloak class="w-3.5 h-3.5 text-green-500" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>
    </button>
    <span x-show="copied" x-cloak class="sr-only" role="status">{{ __('Copied!') }}</span>
</span>
