import React, { useEffect, useState } from 'react';
import { Link, usePage, router } from '@inertiajs/react';
import { BookOpen, Menu, X, LogOut, LayoutDashboard, Shield, UserCircle } from 'lucide-react';

export default function PublicLayout({ children }) {
    const { auth, flash } = usePage().props;
    const [userMenu, setUserMenu] = useState(false);
    const [userOpen, setUserOpen] = useState(false);
    const [loading, setLoading] = useState(false);
    const user = auth?.user;

    useEffect(() => {
        const removeStart = router.on('start', () => setLoading(true));
        const removeFinish = router.on('finish', () => setLoading(false));
        return () => {
            removeStart();
            removeFinish();
        };
    }, []);

    return (
        <div className="public-shell">
            {loading && (
                <div role="status" aria-live="polite">
                    <div className="top-progress" />
                    <div className="loading-pill">◌ &nbsp; Memproses tindakan...</div>
                </div>
            )}
            <header className="topbar">
                <div className="topbar-inner">
                    <Link href="/" className="brand">
                        <span className="brand-mark"><BookOpen size={19} aria-hidden="true" /></span>
                        <span>BlogVerse <span className="brand-light">Pro</span></span>
                    </Link>
                    <nav className={`top-links ${userOpen ? 'open' : ''}`} aria-label="Navigasi Utama">
                        <Link href="/">Beranda</Link>
                        <Link href="/articles">Jelajahi Artikel</Link>
                    </nav>
                    <div className="top-actions">
                        {user ? (
                            <div className="user-menu-wrap">
                                <button
                                    type="button"
                                    className="user-chip"
                                    aria-label={`Menu akun ${user.name}`}
                                    aria-expanded={userMenu}
                                    onClick={() => setUserMenu(!userMenu)}
                                >
                                    <span className="avatar-mini">{user.name?.[0]}</span>
                                    <span>{user.name}</span>
                                    <span aria-hidden="true">⌄</span>
                                </button>
                                {userMenu && (
                                    <div className="user-dropdown">
                                        {user.role === 'admin' ? (
                                            <Link href="/admin/users"><Shield size={16} aria-hidden="true" /> Panel Admin</Link>
                                        ) : (
                                            <Link href="/dashboard/posts"><LayoutDashboard size={16} aria-hidden="true" /> Dashboard Penulis</Link>
                                        )}
                                        <Link href="/profile"><UserCircle size={16} aria-hidden="true" /> Profil</Link>
                                        <button type="button" onClick={() => router.post('/logout')}>
                                            <LogOut size={16} aria-hidden="true" /> Keluar
                                        </button>
                                    </div>
                                )}
                            </div>
                        ) : (
                            <>
                                <Link href="/login" className="nav-login">Masuk</Link>
                                <Link href="/register" className="btn btn-primary btn-sm">
                                    Mulai Menulis <span aria-hidden="true">↗</span>
                                </Link>
                            </>
                        )}
                        <button
                            type="button"
                            className="mobile-toggle"
                            aria-label={userOpen ? 'Tutup menu navigasi' : 'Buka menu navigasi'}
                            aria-expanded={userOpen}
                            onClick={() => setUserOpen(!userOpen)}
                        >
                            {userOpen ? <X aria-hidden="true" /> : <Menu aria-hidden="true" />}
                        </button>
                    </div>
                </div>
            </header>
            <main className="public-main" id="main-content">
                {(flash?.success || flash?.error) && (
                    <div role="alert" className={`toast ${flash.error ? 'toast-error' : ''}`}>
                        {flash.success || flash.error}
                    </div>
                )}
                {children}
            </main>
            <footer className="footer">
                <div className="footer-inner">
                    <Link href="/" className="brand">
                        <span className="brand-mark"><BookOpen size={18} aria-hidden="true" /></span>
                        <span>BlogVerse <span className="brand-light">Pro</span></span>
                    </Link>
                    <p>Ruang untuk ide, cerita, dan pengetahuan.</p>
                    <nav aria-label="Navigasi Footer" style={{ display: 'flex', gap: '20px' }}>
                        <Link href="/">Beranda</Link>
                        <Link href="/articles">Artikel</Link>
                        <Link href="/login">Login</Link>
                        <Link href="/register">Register</Link>
                    </nav>
                    <small>© {new Date().getFullYear()} BlogVerse Pro. Dibuat untuk berbagi inspirasi.</small>
                </div>
            </footer>
        </div>
    );
}
