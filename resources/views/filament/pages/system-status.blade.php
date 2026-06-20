<x-filament-panels::page>
    @php
        $palette = ['ok' => '#22c55e', 'warn' => '#f59e0b', 'fail' => '#ef4444'];
        $words   = ['ok' => 'Good', 'warn' => 'Heads up', 'fail' => 'Problem'];
        $icons   = ['ok' => '✅', 'warn' => '⚠️', 'fail' => '🔴'];
        $summary = $this->getSummary();
        $sc = $palette[$summary[0]];
    @endphp

    {{-- One-line verdict anyone can understand --}}
    <div style="display:flex;align-items:center;gap:14px;padding:16px 20px;border-radius:14px;background:{{ $sc }}14;border:1px solid {{ $sc }}55;margin-bottom:4px;">
        <span style="font-size:26px;line-height:1;">{{ $icons[$summary[0]] }}</span>
        <div>
            <div style="font-weight:800;font-size:16px;">{{ $summary[1] }}</div>
            <div style="font-size:12px;opacity:0.7;">A quick health check of your website. Green = fine · Amber = keep an eye on it · Red = needs fixing.</div>
        </div>
    </div>

    {{-- Health checks --}}
    <div style="display:flex;flex-wrap:wrap;gap:12px;">
        @foreach($this->getChecks() as $check)
            @php $c = $palette[$check['status']] ?? '#ef4444'; @endphp
            <div style="flex:1 1 240px;min-width:220px;display:flex;align-items:center;gap:12px;padding:14px 16px;border:1px solid rgba(128,128,128,0.2);border-radius:12px;">
                <span style="width:10px;height:10px;border-radius:9999px;background:{{ $c }};flex-shrink:0;box-shadow:0 0 0 3px {{ $c }}22;"></span>
                <div style="min-width:0;">
                    <div style="font-weight:700;font-size:13px;">{{ $check['name'] }}</div>
                    <div style="font-size:12px;opacity:0.7;">{{ $check['value'] }}</div>
                </div>
                <span style="margin-left:auto;font-size:11px;font-weight:800;text-transform:uppercase;color:{{ $c }};white-space:nowrap;">{{ $words[$check['status']] ?? 'Problem' }}</span>
            </div>
        @endforeach
    </div>

    {{-- App info + recent errors --}}
    <div style="display:flex;flex-wrap:wrap;gap:12px;margin-top:8px;">
        <div style="flex:1 1 300px;padding:16px;border:1px solid rgba(128,128,128,0.2);border-radius:12px;">
            <div style="font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:0.5px;opacity:0.5;margin-bottom:8px;">Application</div>
            @foreach($this->getAppInfo() as $key => $value)
                <div style="display:flex;justify-content:space-between;font-size:13px;padding:4px 0;">
                    <span style="opacity:0.6;">{{ $key }}</span>
                    <span style="font-weight:600;font-family:ui-monospace,monospace;">{{ $value }}</span>
                </div>
            @endforeach
        </div>

        <div style="flex:1 1 300px;padding:16px;border:1px solid rgba(128,128,128,0.2);border-radius:12px;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
                <span style="font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:0.5px;opacity:0.5;">Recent errors</span>
                <a href="{{ \App\Filament\Resources\Logs\LogResource::getUrl('index') }}" style="font-size:12px;color:#C8413D;font-weight:600;">View all &rarr;</a>
            </div>
            @forelse($this->getRecentErrors() as $err)
                <div style="font-size:12px;padding:5px 0;border-bottom:1px solid rgba(128,128,128,0.12);">
                    <span style="font-family:ui-monospace,monospace;opacity:0.5;">{{ $err->logged_at->format('d M H:i') }}</span>
                    {{ \Illuminate\Support\Str::limit($err->message, 56) }}
                </div>
            @empty
                <div style="font-size:13px;opacity:0.5;padding:8px 0;">No errors recorded 🎉</div>
            @endforelse
        </div>
    </div>
</x-filament-panels::page>
