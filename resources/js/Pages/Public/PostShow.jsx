import React from 'react';
import { Link } from '@inertiajs/react';
import ReactMarkdown from 'react-markdown';
import remarkGfm from 'remark-gfm';
import PublicLayout from '../../Layouts/PublicLayout';
import { ArrowLeft, ArrowUpRight, Clock } from 'lucide-react';

export default function PostShow({ post }) {
    return (
        <PublicLayout>
            <article className="reading-layout">
                <Link href="/articles" className="back-link">
                    <ArrowLeft size={16} aria-hidden="true" /> Kembali ke artikel
                </Link>
                <div className="reading-head">
                    <span className="category-tag static">{post.category}</span>
                    <h1>{post.title}</h1>
                    <p className="reading-excerpt">{post.excerpt}</p>
                    <div className="reading-byline">
                        <div className="avatar-mini" aria-hidden="true">{post.user?.name?.[0]}</div>
                        <span>
                            <b>{post.user?.name}</b>
                            <small>
                                {post.published_at
                                    ? new Date(post.published_at).toLocaleDateString('id-ID', {
                                        day: 'numeric',
                                        month: 'long',
                                        year: 'numeric',
                                    })
                                    : ''}
                            </small>
                        </span>
                        <span className="reading-time">
                            <Clock size={15} aria-hidden="true" /> Artikel BlogVerse
                        </span>
                    </div>
                </div>
                {post.cover_image_path && (
                    <img
                        className="reading-cover"
                        src={`/storage/${post.cover_image_path}`}
                        alt={post.title}
                    />
                )}
                <div className="markdown-view">
                    <ReactMarkdown remarkPlugins={[remarkGfm]}>{post.content}</ReactMarkdown>
                </div>
                <div className="reading-community">
                    <div className="community-icon" aria-hidden="true">✳</div>
                    <div>
                        <h2>Jadilah bagian dari cerita.</h2>
                        <p>Bergabung dengan komunitas BlogVerse dan bagikan perspektifmu.</p>
                    </div>
                    <div className="community-actions">
                        <Link href="/login" className="text-link">Masuk</Link>
                        <Link href="/register" className="btn btn-primary">
                            Daftar <ArrowUpRight size={15} aria-hidden="true" />
                        </Link>
                    </div>
                </div>
            </article>
        </PublicLayout>
    );
}
