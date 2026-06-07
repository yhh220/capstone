@props(['text', 'position' => 'top'])
@php
$posClass = match($position) {
    'bottom' => 'top-full mt-2 left-1/2 -translate-x-1/2',
    'left'   => 'right-full mr-2 top-1/2 -translate-y-1/2',
    'right'  => 'left-full ml-2 top-1/2 -translate-y-1/2',
    default  => 'bottom-full mb-2 left-1/2 -translate-x-1/2',
};
@endphp
<div x-data="{ show: false }"
     class="relative inline-flex"
     @mouseenter="show = true"
     @mouseleave="show = false"
     @focusin="show = true"
     @focusout="show = false">
    {{ $slot }}
    <div x-show="show"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute {{ $posClass }} px-2 py-1 text-xs font-medium text-white bg-gray-900 dark:bg-gray-700 rounded whitespace-nowrap pointer-events-none z-50"
         role="tooltip">
        {{ $text }}
    </div>
</div>
