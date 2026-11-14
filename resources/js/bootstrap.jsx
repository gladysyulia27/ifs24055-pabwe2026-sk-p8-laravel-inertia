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
            const savedInputs = {};
            el.querySelectorAll('input[id], textarea[id]').forEach(inp => {
                if (inp.value) {
                    savedInputs[inp.id] = inp.value;
                }
            });

            createRoot(el).render(<App {...props} />);

            if (typeof window !== 'undefined') {
                requestAnimationFrame(() => {
                    Object.entries(savedInputs).forEach(([id, val]) => {
                        const target = document.getElementById(id);
                        if (target && !target.value) {
                            const setter = Object.getOwnPropertyDescriptor(
                                window.HTMLInputElement.prototype,
                                'value'
                            )?.set;
                            if (setter) {
                                setter.call(target, val);
                                target.dispatchEvent(new Event('input', { bubbles: true }));
                            } else {
                                target.value = val;
                            }
                        }
                    });

                    if (window.__pendingButtonClick) {
                        const pending = window.__pendingButtonClick;
                        window.__pendingButtonClick = null;
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
