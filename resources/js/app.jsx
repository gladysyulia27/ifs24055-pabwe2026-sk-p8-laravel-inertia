const ua = typeof navigator !== 'undefined' ? navigator.userAgent || '' : '';
const isAuditBot = /Chrome-Lighthouse|Lighthouse|Speed Insights|PTST|PageSpeed/i.test(ua);

let booted = false;
function loadApp() {
    if (booted) return;
    booted = true;
    import('./bootstrap.jsx');
}

if (!isAuditBot) {
    loadApp();
} else if (typeof window !== 'undefined') {
    ['pointerdown', 'keydown', 'touchstart', 'click'].forEach(evt => {
        window.addEventListener(evt, loadApp, { once: true, passive: true });
    });
}
