import React, { useEffect, useState } from 'react';
import { Link, usePage, router } from '@inertiajs/react';
import { BookOpen, FileText, Users, Settings, LogOut, Globe, Menu, X, ChevronRight } from 'lucide-react';

export default function DashboardLayout({ children, admin = false, title = 'Dashboard' }) {
    const { auth, flash } = usePage().props;
    const [userOpen, setUserOpen] = useState(false);
    const [loading, setLoading] = useState(false);
    const user = auth?.user;

    useEffect(() => {
        const a = router.on('start', () => setLoading(true));
        const b = router.on('finish', () => setLoading(false));
        return () => {
            a();
            b();
        };
    }, []);

    const links = admin
        ? [
            ['/admin/users', 'Pengguna', Users],
            ['/admin/posts', 'Semua Artikel', FileText],
        ]
        : [
            ['/dashboard/posts', 'Artikel Saya', FileText],
            ['/profile', 'Profil', Settings],
        ];

    const currentPath = typeof window !== 'undefined' ? window.location.pathname : '';

    return (
        <div className="dash-shell">
            {loading && (
                <div role="status" aria-live="polite">
                    <div className="top-progress" />
                    <div className="loading-pill">◌ &nbsp; Memproses tindakan...</div>
                </div>
            )}
            <aside className={`sidebar ${userOpen ? 'sidebar-open' : ''}`} aria-label="Menu Samping">
                <Link href="/" className="brand side-brand">
                    <span className="brand-mark"><BookOpen size={19} aria-hidden="true" /></span>
                    <span>BlogVerse <span className="brand-light">Pro</span></span>
                </Link>
                <div className="side-label">WORKSPACE</div>
                <nav className="side-links" aria-label="Menu Workspace">
                    {links.map(([url, label, Icon]) => (
                        <Link key={url} href={url} className={`side-link ${currentPath === url ? 'active' : ''}`}>
                            <Icon size={18} aria-hidden="true" />
                            {label}
                            <ChevronRight size={14} className="side-chevron" aria-hidden="true" />
                        </Link>
                    ))}
                </nav>
                <div className="side-bottom">
                    <Link href="/articles" className="side-link">
                        <Globe size={18} aria-hidden="true" /> Lihat Blog
                    </Link>
                    <div className="side-user">
                        <div className="avatar-mini" aria-hidden="true">{user?.name?.[0] || 'U'}</div>
                        <div className="side-user-text">
                            <b>{user?.name || 'Penulis'}</b>
                            <small>{admin ? 'Administrator' : 'Penulis'}</small>
                        </div>
                        <button
                            type="button"
                            title="Keluar"
                            aria-label="Keluar dari akun"
                            onClick={() => router.post('/logout')}
                        >
                            <LogOut size={17} aria-hidden="true" />
                        </button>
                    </div>
                </div>
            </aside>
            <main className="dash-main" id="main-content">
                <header className="dash-top">
                    <button
                        type="button"
                        className="mobile-toggle dark"
                        aria-label={userOpen ? 'Tutup menu samping' : 'Buka menu samping'}
                        aria-expanded={userOpen}
                        onClick={() => setUserOpen(!userOpen)}
                    >
                        {userOpen ? <X aria-hidden="true" /> : <Menu aria-hidden="true" />}
                    </button>
                    <div>
                        <span className="crumb">WORKSPACE / </span>
                        <b>{title}</b>
                    </div>
                    <div className="dash-top-right">
                        <span className="online-dot" aria-hidden="true" /> Sistem Aktif
                        <div className="avatar-mini" aria-hidden="true">{user?.name?.[0] || 'U'}</div>
                    </div>
                </header>
                <div className="dash-content">
                    {(flash?.success || flash?.error) && (
                        <div role="alert" className={`alert ${flash.error ? 'alert-error' : ''}`}>
                            {flash.success || flash.error}
                        </div>
                    )}
                    {children}
                </div>
            </main>
        </div>
    );
}
