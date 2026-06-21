<div id="filament-nav-progress" class="fixed top-0 left-0 z-[100] h-[3px] w-0 bg-rose-600 opacity-0 transition-[width] duration-300 ease-out pointer-events-none"></div>

<div id="filament-skeleton-overlay" class="fixed z-[90] hidden overflow-hidden bg-white dark:bg-gray-900 p-6 sm:p-8">
    <div class="animate-pulse space-y-6 max-w-7xl">
        {{-- Page heading --}}
        <div class="h-7 w-56 rounded-lg bg-gray-200 dark:bg-gray-700"></div>

        {{-- Stat-card row --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @for ($i = 0; $i < 3; $i++)
                <div class="h-24 rounded-xl bg-gray-200 dark:bg-gray-700"></div>
            @endfor
        </div>

        {{-- Table-like block --}}
        <div class="rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="h-11 bg-gray-100 dark:bg-gray-800"></div>
            @for ($i = 0; $i < 6; $i++)
                <div class="h-14 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/40"></div>
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
        const skeleton = document.getElementById('filament-skeleton-overlay');
        if (!bar || !skeleton) return;

        let hideTimeout = null;

        function positionSkeleton() {
            const main = document.querySelector('.fi-main');
            if (!main) return;
            const rect = main.getBoundingClientRect();
            skeleton.style.top    = rect.top + 'px';
            skeleton.style.left   = rect.left + 'px';
            skeleton.style.width  = rect.width + 'px';
            skeleton.style.height = rect.height + 'px';
        }

        document.addEventListener('livewire:navigate', () => {
            clearTimeout(hideTimeout);
            positionSkeleton();
            skeleton.classList.remove('hidden');
            document.body.classList.add('is-navigating');
            bar.style.opacity = '1';
            bar.style.width = '0%';
            requestAnimationFrame(() => { bar.style.width = '90%'; });
        });

        document.addEventListener('livewire:navigated', () => {
            document.body.classList.remove('is-navigating');
            skeleton.classList.add('hidden');
            bar.style.width = '100%';
            hideTimeout = setTimeout(() => {
                bar.style.opacity = '0';
                bar.style.width = '0%';
            }, 200);
        });

        window.addEventListener('resize', positionSkeleton);
    })();
</script>
