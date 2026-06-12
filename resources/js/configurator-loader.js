// Thin entry for the 3D configurator. The real bundle (three.js + logic,
// ~640 KB) is dynamic-imported only when the user actually opens the
// configurator, so the products page itself stays light.
let modulePromise = null;

document.addEventListener('click', (e) => {
    const btn = e.target.closest('#open-configurator-btn');
    if (!btn) return;

    if (!modulePromise) {
        // Brief busy state on the button while the bundle downloads.
        btn.setAttribute('aria-busy', 'true');
        btn.style.opacity = '0.7';
        btn.style.pointerEvents = 'none';

        modulePromise = import('./configurator.js')
            .then((mod) => {
                btn.removeAttribute('aria-busy');
                btn.style.opacity = '';
                btn.style.pointerEvents = '';
                // The click that triggered the download still counts — open now.
                mod.openConfigurator();
                return mod;
            })
            .catch((err) => {
                console.error('Failed to load 3D configurator:', err);
                modulePromise = null;
                btn.removeAttribute('aria-busy');
                btn.style.opacity = '';
                btn.style.pointerEvents = '';
                btn.title = 'Failed to load — check your connection and try again';
            });
    }
    // Once loaded, configurator.js's own delegated listener handles clicks.
});
