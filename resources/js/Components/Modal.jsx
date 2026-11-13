import React from 'react';
import { X } from 'lucide-react';

export default function Modal({
    open,
    onClose,
    title,
    subtitle,
    children,
    onSubmit,
    processing,
    submitText = 'Simpan',
    tone = 'purple',
}) {
    if (!open) return null;
    return (
        <div className="modal-overlay" role="dialog" aria-modal="true" aria-label={title}>
            <form className="fullscreen-modal" onSubmit={onSubmit}>
                <header className={`modal-head ${tone}`}>
                    <div>
                        <span className="eyebrow">BLOGVERSE EDITOR</span>
                        <h2>{title}</h2>
                        <p>{subtitle}</p>
                    </div>
                    <button
                        type="button"
                        className="icon-button light"
                        aria-label="Tutup modal"
                        onClick={onClose}
                    >
                        <X aria-hidden="true" />
                    </button>
                </header>
                <section className="modal-body">{children}</section>
                <footer className={`modal-foot ${tone}`}>
                    <button type="button" className="btn btn-ghost" onClick={onClose}>
                        Batal
                    </button>
                    <button type="submit" className="btn btn-primary" disabled={processing}>
                        {processing && <span className="spinner" aria-hidden="true" />}
                        {submitText}
                    </button>
                </footer>
            </form>
        </div>
    );
}
