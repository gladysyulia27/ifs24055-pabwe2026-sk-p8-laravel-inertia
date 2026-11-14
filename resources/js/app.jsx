let booted = false;

function loadApp(evt) {
    if (evt && evt.target && typeof evt.target.closest === 'function') {
        const nativeForm = evt.target.closest('form[action]');
        if (nativeForm && !evt.target.closest('button[type="button"]')) {
            return;
        }
        if (evt.type === 'click') {
            const btn = evt.target.closest('button[type="button"]');
            if (btn) {
                window.__pendingButtonClick = {
                    text: (btn.textContent || '').trim(),
                    ariaLabel: btn.getAttribute('aria-label') || '',
                };
            }
        }
    }
    if (booted) return;
    booted = true;
    import('./bootstrap.jsx');
}

if (typeof window !== 'undefined') {
    ['pointerdown', 'mousedown', 'touchstart', 'click'].forEach(evt => {
        window.addEventListener(evt, loadApp, { passive: true });
    });
}
