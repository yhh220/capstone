<button id="filament-scroll-to-top"
        aria-label="Scroll to top"
        type="button"
        style="left: calc(50% - 1.25rem);"
        class="fixed bottom-6 z-50 w-10 h-10 flex items-center justify-center rounded-full bg-rose-600 text-white shadow-2xl transition-all duration-500 translate-y-24 opacity-0 pointer-events-none hover:bg-rose-500 hover:-translate-y-1 active:scale-95 focus:outline-none focus:ring-4 focus:ring-rose-500/30 group">
    <svg class="w-5 h-5 transition-transform duration-300 group-hover:-translate-y-1" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5"/>
    </svg>
</button>

<script>
    (function() {
        const btn = document.getElementById('filament-scroll-to-top');
        if (!btn) return;

        function toggleBtn() {
            if (window.scrollY > 400) {
                btn.classList.remove('translate-y-24', 'opacity-0', 'pointer-events-none');
            } else {
                btn.classList.add('translate-y-24', 'opacity-0', 'pointer-events-none');
            }
        }

        window.addEventListener('scroll', toggleBtn);
        btn.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
        
        document.addEventListener('livewire:navigated', toggleBtn);
    })();
</script>
