<div id="filament-nav-progress" class="fixed top-0 left-0 z-[100] h-[3px] w-0 bg-rose-600 opacity-0 transition-[width] duration-300 ease-out pointer-events-none"></div>

<div id="filament-skeleton-overlay" class="fixed z-[90] hidden overflow-y-auto bg-white dark:bg-gray-900 p-6 sm:p-8">

    {{-- Resource list pages (Products, Orders, Brands, Logs, etc.): heading +
         action button, a filter bar, then a table with a header row and rows. --}}
    <div data-skeleton="list" class="hidden animate-pulse space-y-5 max-w-7xl">
        <div class="flex items-center justify-between">
            <div class="h-7 w-48 rounded-lg bg-gray-200 dark:bg-gray-700"></div>
            <div class="h-9 w-28 rounded-full bg-gray-200 dark:bg-gray-700"></div>
        </div>
        <div class="flex items-center gap-3">
            <div class="h-10 flex-1 max-w-sm rounded-lg bg-gray-100 dark:bg-gray-800"></div>
            <div class="h-10 w-24 rounded-lg bg-gray-100 dark:bg-gray-800"></div>
            <div class="h-10 w-24 rounded-lg bg-gray-100 dark:bg-gray-800"></div>
        </div>
        <div class="rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="h-11 bg-gray-100 dark:bg-gray-800"></div>
            @for ($i = 0; $i < 8; $i++)
                <div class="h-14 flex items-center gap-4 px-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/40">
                    <div class="h-8 w-8 rounded-full bg-gray-200 dark:bg-gray-700 shrink-0"></div>
                    <div class="h-3 rounded bg-gray-200 dark:bg-gray-700" style="width: {{ rand(30, 70) }}%"></div>
                </div>
            @endfor
        </div>
    </div>

    {{-- Form pages (Edit/Create, Profile, Settings): heading, then card
         sections each with a few label+input placeholder pairs. --}}
    <div data-skeleton="form" class="hidden animate-pulse space-y-6 max-w-4xl">
        <div class="h-7 w-56 rounded-lg bg-gray-200 dark:bg-gray-700"></div>
        @for ($section = 0; $section < 2; $section++)
            <div class="rounded-xl border border-gray-100 dark:border-gray-700 p-6 space-y-5">
                <div class="h-5 w-40 rounded bg-gray-200 dark:bg-gray-700"></div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    @for ($field = 0; $field < 4; $field++)
                        <div class="space-y-2">
                            <div class="h-3 w-24 rounded bg-gray-100 dark:bg-gray-800"></div>
                            <div class="h-10 rounded-lg bg-gray-100 dark:bg-gray-800"></div>
                        </div>
                    @endfor
                </div>
            </div>
        @endfor
        <div class="flex justify-end gap-3">
            <div class="h-10 w-24 rounded-full bg-gray-100 dark:bg-gray-800"></div>
            <div class="h-10 w-28 rounded-full bg-gray-200 dark:bg-gray-700"></div>
        </div>
    </div>

    {{-- Dashboard home: stat-card row (StatsOverview), two side-by-side chart
         placeholders (Revenue + Top Products), then the recent-activity table. --}}
    <div data-skeleton="dashboard" class="hidden animate-pulse space-y-6 max-w-7xl">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @for ($i = 0; $i < 4; $i++)
                <div class="h-24 rounded-xl bg-gray-200 dark:bg-gray-700"></div>
            @endfor
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <div class="h-72 rounded-xl bg-gray-200 dark:bg-gray-700"></div>
            <div class="h-72 rounded-xl bg-gray-200 dark:bg-gray-700"></div>
        </div>
        <div class="rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="h-11 bg-gray-100 dark:bg-gray-800"></div>
            @for ($i = 0; $i < 5; $i++)
                <div class="h-14 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/40"></div>
            @endfor
        </div>
    </div>

    {{-- System Status: callout banner, then a 3-column grid of small health-check
         cards (one per check in SystemStatus::getChecks()). --}}
    <div data-skeleton="status" class="hidden animate-pulse space-y-6 max-w-7xl">
        <div class="h-14 rounded-xl bg-gray-200 dark:bg-gray-700"></div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @for ($i = 0; $i < 9; $i++)
                <div class="h-16 rounded-xl bg-gray-200 dark:bg-gray-700"></div>
            @endfor
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            @for ($i = 0; $i < 3; $i++)
                <div class="h-40 rounded-xl bg-gray-100 dark:bg-gray-800"></div>
            @endfor
        </div>
    </div>
</div>

<style>
    body.is-navigating .fi-main { opacity: 0; }
</style>

<script>
    (function () {
        const bar = document.getElementById('filament-nav-progress');
        const overlay = document.getElementById('filament-skeleton-overlay');
        if (!bar || !overlay) return;

        const variants = overlay.querySelectorAll('[data-skeleton]');
        let hideTimeout = null;

        function positionSkeleton() {
            const main = document.querySelector('.fi-main');
            if (!main) return;
            const rect = main.getBoundingClientRect();
            overlay.style.top    = rect.top + 'px';
            overlay.style.left   = rect.left + 'px';
            overlay.style.width  = rect.width + 'px';
            overlay.style.height = rect.height + 'px';
        }

        // Picks a skeleton shape from the destination URL alone — at
        // livewire:navigate time the next page hasn't loaded yet, so this is
        // pattern-matched against Filament's own URL conventions rather than
        // the real layout.
        function pickVariant(url) {
            let path = '';
            try {
                path = new URL(url, window.location.origin).pathname.replace(/\/+$/, '');
            } catch (e) {
                return 'list';
            }

            if (/\/(edit|create)$/.test(path)) return 'form';
            // Filament's built-in profile page has no list view, just the form —
            // unlike /admin/settings, which is a real Resource list (a table of
            // setting rows; only /admin/settings/{id}/edit is a form).
            if (/\/profile$/.test(path)) return 'form';
            if (/\/system-status$/.test(path)) return 'status';
            if (path === '/admin') return 'dashboard';

            return 'list';
        }

        function showVariant(name) {
            variants.forEach((el) => el.classList.toggle('hidden', el.dataset.skeleton !== name));
        }

        document.addEventListener('livewire:navigate', (event) => {
            clearTimeout(hideTimeout);
            showVariant(pickVariant(event.detail?.url));
            positionSkeleton();
            overlay.classList.remove('hidden');
            document.body.classList.add('is-navigating');
            bar.style.opacity = '1';
            bar.style.width = '0%';
            requestAnimationFrame(() => { bar.style.width = '90%'; });
        });

        document.addEventListener('livewire:navigated', () => {
            document.body.classList.remove('is-navigating');
            overlay.classList.add('hidden');
            bar.style.width = '100%';
            hideTimeout = setTimeout(() => {
                bar.style.opacity = '0';
                bar.style.width = '0%';
            }, 200);
        });

        window.addEventListener('resize', positionSkeleton);
    })();
</script>
