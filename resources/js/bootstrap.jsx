import React from 'react';
import { createRoot } from 'react-dom/client';
import { createInertiaApp } from '@inertiajs/react';

export function bootInertiaApp() {
    createInertiaApp({
        title: title => (title ? `${title} · BlogVerse Pro` : 'BlogVerse Pro'),
        resolve: name => {
            const pages = import.meta.glob('./Pages/**/*.jsx');
            return pages[`./Pages/${name}.jsx`]();
        },
        setup({ el, App, props }) {
            createRoot(el).render(<App {...props} />);
            if (typeof window !== 'undefined' && window.__pendingButtonClick) {
                const pending = window.__pendingButtonClick;
                window.__pendingButtonClick = null;
                requestAnimationFrame(() => {
                    const buttons = el.querySelectorAll('button');
                    for (const b of buttons) {
                        if (
                            (pending.ariaLabel && b.getAttribute('aria-label') === pending.ariaLabel) ||
                            (pending.text && (b.textContent || '').trim() === pending.text)
                        ) {
                            b.click();
                            break;
                        }
                    }
                });
            }
        },
        progress: {
            color: '#4536b8',
            showSpinner: false,
        },
    });
}

bootInertiaApp();
