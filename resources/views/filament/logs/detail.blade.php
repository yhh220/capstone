@php
    $ctx = $log->context ?? [];
    $breadcrumbs = $ctx['breadcrumbs'] ?? null;
    $detail = collect($ctx)->except('breadcrumbs');
@endphp
<div class="space-y-4 text-sm">
    {{-- Meta --}}
    <div class="grid grid-cols-2 gap-x-4 gap-y-1">
        <div><span class="text-gray-500 dark:text-gray-400">Channel:</span> <span class="font-medium">{{ $log->channel ?: '—' }}</span></div>
        <div><span class="text-gray-500 dark:text-gray-400">Trace:</span> <span class="font-mono text-xs">{{ $log->trace_id ?: '—' }}</span></div>
        <div><span class="text-gray-500 dark:text-gray-400">User:</span> <span class="font-medium">{{ $log->user_id ?: '—' }}</span></div>
        <div><span class="text-gray-500 dark:text-gray-400">IP:</span> <span class="font-medium">{{ $log->ip ?: '—' }}</span></div>
        <div class="col-span-2"><span class="text-gray-500 dark:text-gray-400">Request:</span> <span class="font-mono text-xs">{{ $log->method }} {{ $log->path ?: '—' }}</span></div>
    </div>

    {{-- Message --}}
    <div>
        <div class="text-xs font-bold uppercase tracking-wide text-gray-400 mb-1">Message</div>
        <div class="rounded-lg bg-gray-50 dark:bg-white/5 p-3 font-mono text-xs whitespace-pre-wrap break-words">{{ $log->message }}</div>
    </div>

    {{-- Breadcrumbs (trail before the error) --}}
    @if(is_array($breadcrumbs) && count($breadcrumbs))
    <div>
        <div class="text-xs font-bold uppercase tracking-wide text-gray-400 mb-1">Breadcrumbs ({{ count($breadcrumbs) }})</div>
        <ol class="rounded-lg border border-gray-100 dark:border-white/10 divide-y divide-gray-100 dark:divide-white/10">
            @foreach($breadcrumbs as $crumb)
            <li class="flex items-start gap-2 px-3 py-1.5">
                <span class="font-mono text-[11px] text-gray-400 shrink-0">{{ $crumb['ts'] ?? '' }}</span>
                <span class="text-[11px] font-bold uppercase text-brand-red shrink-0">{{ $crumb['category'] ?? '' }}</span>
                <span class="text-xs">{{ $crumb['message'] ?? '' }}</span>
                @if(!empty($crumb['data']))
                <span class="font-mono text-[11px] text-gray-400 ml-auto">{{ \Illuminate\Support\Str::limit(json_encode($crumb['data']), 60) }}</span>
                @endif
            </li>
            @endforeach
        </ol>
    </div>
    @endif

    {{-- Context / detail --}}
    @if($detail->isNotEmpty())
    <div>
        <div class="text-xs font-bold uppercase tracking-wide text-gray-400 mb-1">Context</div>
        <pre class="rounded-lg bg-gray-50 dark:bg-white/5 p-3 text-xs overflow-x-auto whitespace-pre-wrap break-words">{{ json_encode($detail, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</pre>
    </div>
    @endif
</div>
