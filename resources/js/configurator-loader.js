// Thin entry for the 3D configurator. The real bundle (three.js + logic,
// ~640 KB) is dynamic-imported only when the user actually opens the
// configurator, so the products page itself stays light.
let modulePromise = null;

function loadConfigurator(btn) {
    if (modulePromise) {
        // Already loaded/loading — just (re)open via the module.
        modulePromise.then((mod) => mod?.openConfigurator?.());
        return;
    }

    if (btn) {
        // Brief busy state on the button while the bundle downloads.
        btn.setAttribute('aria-busy', 'true');
        btn.style.opacity = '0.7';
        btn.style.pointerEvents = 'none';
    }

    const clearBusy = () => {
        if (!btn) return;
        btn.removeAttribute('aria-busy');
        btn.style.opacity = '';
        btn.style.pointerEvents = '';
    };

    modulePromise = import('./configurator.js')
        .then((mod) => {
            clearBusy();
            // The click/refresh that triggered the download still counts — open now.
            mod.openConfigurator();
            return mod;
        })
        .catch((err) => {
            console.error('Failed to load 3D configurator:', err);
            modulePromise = null;
            clearBusy();
            if (btn) btn.title = 'Failed to load — check your connection and try again';
        });
}

document.addEventListener('click', (e) => {
    const btn = e.target.closest('#open-configurator-btn');
    if (btn) loadConfigurator(btn);
    // Once loaded, configurator.js's own delegated listener handles clicks.
});

// If the page was refreshed while the configurator was open (#car-configurator
// in the URL), re-open it automatically so the user stays in the 3D view
// instead of landing back on the plain products page.
function maybeAutoOpen() {
    if (window.location.hash === '#car-configurator' && document.getElementById('configurator-modal')) {
        loadConfigurator(document.getElementById('open-configurator-btn'));
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', maybeAutoOpen);
} else {
    maybeAutoOpen();
}
document.addEventListener('livewire:navigated', maybeAutoOpen);
