let booted = false;

function loadApp(evt) {
    if (evt && evt.type === 'click' && evt.target && typeof evt.target.closest === 'function') {
        const btn = evt.target.closest('button[type="button"]');
        if (btn) {
            window.__pendingButtonClick = {
                text: (btn.textContent || '').trim(),
                ariaLabel: btn.getAttribute('aria-label') || '',
            };
        }
    }
    if (booted) return;
    booted = true;
    import('./bootstrap.jsx');
}

if (typeof window !== 'undefined') {
    ['pointerdown', 'mousedown', 'mousemove', 'touchstart', 'keydown', 'focusin', 'click'].forEach(evt => {
        window.addEventListener(evt, loadApp, { once: true, passive: true });
    });
}
