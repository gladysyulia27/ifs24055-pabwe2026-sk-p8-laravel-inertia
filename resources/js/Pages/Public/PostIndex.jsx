import React from 'react';
import { Link, useForm } from '@inertiajs/react';
import PublicLayout from '../../Layouts/PublicLayout';
import ArticleCard from '../../Components/ArticleCard';
import { ArrowRight, Search, PenLine, Sparkles, BookOpen, MoveUpRight } from 'lucide-react';

export default function PostIndex({ posts, filters = {}, featured = false }) {
    const form = useForm({
        search: filters.search || '',
        category: filters.category || '',
    });

    const submit = e => {
        e.preventDefault();
        form.get('/articles', { preserveState: true, replace: true });
    };

    const cardHeadingTag = featured ? 'h3' : 'h2';

    return (
        <PublicLayout>
            {featured && (
                <section className="hero">
                    <div className="hero-orb orb-one" aria-hidden="true" />
                    <div className="hero-orb orb-two" aria-hidden="true" />
                    <div className="hero-content">
                        <div className="eyebrow-pill">
                            <Sparkles size={14} aria-hidden="true" /> IDEAS THAT MATTER
                        </div>
                        <h1>
                            Setiap ide punya<br />
                            <em>cerita.</em> Bagikan di sini.
                        </h1>
                        <p>
                            Temukan perspektif baru, pelajari hal menarik, dan bagikan pemikiranmu bersama komunitas penulis yang terus berkembang.
                        </p>
                        <div className="hero-actions">
                            <Link href="/articles" className="btn btn-primary btn-lg">
                                Jelajahi Artikel <ArrowRight size={17} aria-hidden="true" />
                            </Link>
                            <Link href="/register" className="btn btn-outline btn-lg">
                                Mulai Menulis <PenLine size={16} aria-hidden="true" />
                            </Link>
                        </div>
                        <div className="hero-proof">
                            <div className="proof-avatars" aria-hidden="true">
                                <i>R</i><i>A</i><i>M</i><i>+</i>
                            </div>
                            <span>Ruang terbuka untuk semua cerita.</span>
                        </div>
                    </div>
                    <div className="hero-visual" aria-hidden="true">
                        <div className="hero-paper">
                            <div className="paper-top">
                                <span>✳ BLOGVERSE JOURNAL</span>
                                <span>VOL. 01</span>
                            </div>
                            <div className="paper-art">
                                <div className="art-sun" />
                                <div className="art-mountain m1" />
                                <div className="art-mountain m2" />
                            </div>
                            <div className="paper-caption">
                                <small>THE ART OF THINKING</small>
                                <b>Ideas grow when<br />they are shared.</b>
                                <span>Stories · Knowledge · People</span>
                            </div>
                        </div>
                        <div className="floating-note">
                            <BookOpen size={17} />
                            <span>
                                <b>Explore ideas</b>
                                <small>Read something new today</small>
                            </span>
                        </div>
                    </div>
                </section>
            )}

            <section className="section-wrap">
                <div className="section-heading">
                    <div>
                        <span className="eyebrow">THE JOURNAL</span>
                        {featured ? <h2>Cerita pilihan</h2> : <h1>Jelajahi artikel</h1>}
                        <p>Wawasan dan inspirasi dari komunitas BlogVerse.</p>
                    </div>
                    {featured && (
                        <Link href="/articles" className="text-link">
                            Lihat semua artikel <ArrowRight size={16} aria-hidden="true" />
                        </Link>
                    )}
                </div>

                {!featured && (
                    <form className="filter-bar" onSubmit={submit} role="search">
                        <div className="search-box">
                            <Search size={18} aria-hidden="true" />
                            <input
                                type="search"
                                aria-label="Cari judul atau isi artikel"
                                placeholder="Cari judul atau isi artikel..."
                                value={form.data.search}
                                onChange={e => form.setData('search', e.target.value)}
                            />
                        </div>
                        <select
                            aria-label="Filter kategori artikel"
                            value={form.data.category}
                            onChange={e => {
                                form.setData('category', e.target.value);
                                form.get('/articles', { preserveState: true, replace: true });
                            }}
                        >
                            <option value="">Semua kategori</option>
                            {['Teknologi', 'Pendidikan', 'Gaya Hidup', 'Bisnis', 'Opini', 'Umum'].map(c => (
                                <option key={c} value={c}>{c}</option>
                            ))}
                        </select>
                        <button type="submit" className="btn btn-primary">Cari</button>
                    </form>
                )}

                <div className="article-grid">
                    {posts?.data?.length ? (
                        posts.data.map(p => <ArticleCard key={p.id} post={p} HeadingTag={cardHeadingTag} />)
                    ) : (
                        <div className="empty-state">
                            <BookOpen size={30} aria-hidden="true" />
                            {featured ? <h3>Belum ada artikel</h3> : <h2>Belum ada artikel</h2>}
                            <p>Coba kata kunci atau kategori lainnya.</p>
                        </div>
                    )}
                </div>

                {posts?.links && (
                    <nav className="pagination" aria-label="Navigasi Halaman">
                        {posts.links.map((l, i) => (
                            <Link
                                key={i}
                                href={l.url || '#'}
                                className={`${l.active ? 'active' : ''} ${!l.url ? 'disabled' : ''}`}
                                dangerouslySetInnerHTML={{ __html: l.label }}
                            />
                        ))}
                    </nav>
                )}
            </section>

            {featured && (
                <section className="join-banner">
                    <div>
                        <span className="eyebrow">YOUR VOICE MATTERS</span>
                        <h2>Punya cerita untuk dibagikan?</h2>
                        <p>Jadikan ide dan pengalamanmu sesuatu yang berarti bagi orang lain.</p>
                    </div>
                    <Link href="/register" className="btn btn-white">
                        Bergabung sekarang <MoveUpRight size={16} aria-hidden="true" />
                    </Link>
                </section>
            )}
        </PublicLayout>
    );
}
