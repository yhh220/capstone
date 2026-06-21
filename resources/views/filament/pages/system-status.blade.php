<x-filament-panels::page>
    @php
        $palette = ['ok' => 'success', 'warn' => 'warning', 'fail' => 'danger'];
        $words   = ['ok' => 'Good', 'warn' => 'Heads up', 'fail' => 'Problem'];
        $icons   = ['ok' => 'heroicon-m-check-circle', 'warn' => 'heroicon-m-exclamation-triangle', 'fail' => 'heroicon-m-x-circle'];
        
        $summary = $this->getSummary();
        $summaryStatus = $summary[0];
        $summaryText = $summary[1];
        
        $calloutColor = $palette[$summaryStatus] ?? 'gray';
        $calloutIcon = $icons[$summaryStatus] ?? 'heroicon-m-information-circle';
    @endphp

    {{-- One-line verdict using Filament Callout --}}
    <x-filament::callout
        :color="$calloutColor"
        :icon="$calloutIcon"
        :heading="$summaryText"
        class="mb-2"
    >
        A quick health check of your website. Green = fine · Amber = keep an eye on it · Red = needs fixing.
    </x-filament::callout>

    {{-- Health checks grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($this->getChecks() as $check)
            @php
                $statusColor = $palette[$check['status']] ?? 'gray';
                $statusIcon = $icons[$check['status']] ?? 'heroicon-m-question-mark-circle';
                $themeClasses = match($statusColor) {
                    'success' => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400',
                    'warning' => 'bg-amber-500/10 text-amber-600 dark:text-amber-400',
                    'danger'  => 'bg-rose-500/10 text-rose-600 dark:text-rose-400',
                    default   => 'bg-gray-500/10 text-gray-600 dark:text-gray-400',
                };
            @endphp
            <x-filament::section class="fi-section-contain">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="p-2 rounded-lg shrink-0 {{ $themeClasses }}">
                            <x-filament::icon
                                :icon="$statusIcon"
                                class="h-6 w-6"
                            />
                        </div>
                        <div class="min-w-0">
                            <div class="font-bold text-sm text-gray-900 dark:text-white truncate">
                                {{ $check['name'] }}
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 leading-snug">
                                {{ $check['value'] }}
                            </div>
                        </div>
                    </div>
                    <x-filament::badge :color="$statusColor" size="sm" class="shrink-0">
                        {{ $words[$check['status']] ?? 'Problem' }}
                    </x-filament::badge>
                </div>
            </x-filament::section>
        @endforeach
    </div>

    {{-- Bottom Grid: App Info, Cache Control, Recent Errors --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- App Info --}}
        <x-filament::section heading="Application">
            <div class="divide-y divide-gray-100 dark:divide-white/10">
                @foreach($this->getAppInfo() as $key => $value)
                    <div class="flex justify-between py-2.5 text-sm">
                        <span class="text-gray-500 dark:text-gray-400 font-medium">{{ $key }}</span>
                        <span class="font-semibold font-mono text-gray-900 dark:text-white">{{ $value }}</span>
                    </div>
                @endforeach
            </div>
        </x-filament::section>

        {{-- Cache Control --}}
        <x-filament::section heading="Cache Control">
            <div class="space-y-4">
                <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                    Clear the application configuration, routes, settings, chatbot FAQs, and dashboard statistics caches if updates are not showing up.
                </p>
                <div>
                    <x-filament::button
                        wire:click="clearCache"
                        color="danger"
                        icon="heroicon-o-trash"
                        class="w-full justify-center"
                        wire:loading.attr="disabled"
                        wire:target="clearCache"
                    >
                        Clear System Cache
                    </x-filament::button>
                </div>
            </div>
        </x-filament::section>

        {{-- Recent Errors --}}
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center justify-between">
                    <span>Recent Errors</span>
                    <a href="{{ \App\Filament\Resources\Logs\LogResource::getUrl('index') }}" class="text-xs font-semibold text-rose-600 hover:text-rose-500 dark:text-rose-400">View all &rarr;</a>
                </div>
            </x-slot>
            <div class="space-y-2 max-h-[220px] overflow-y-auto pr-1">
                @forelse($this->getRecentErrors() as $err)
                    <div class="text-xs py-2 border-b border-gray-100 dark:border-white/5 last:border-0 flex items-start gap-2">
                        <span class="font-mono text-gray-400 dark:text-gray-500 shrink-0">{{ $err->logged_at->format('d M H:i') }}</span>
                        <span class="text-gray-700 dark:text-gray-300 break-words leading-tight flex-1">{{ \Illuminate\Support\Str::limit($err->message, 70) }}</span>
                    </div>
                @empty
                    <div class="text-sm text-gray-500 dark:text-gray-400 py-4 text-center flex items-center justify-center gap-1.5">
                        <svg class="w-4 h-4 text-green-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        No errors recorded
                    </div>
                @endforelse
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
