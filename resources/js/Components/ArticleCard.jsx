import React from 'react';
import { Link } from '@inertiajs/react';
import { ArrowUpRight } from 'lucide-react';

export default function ArticleCard({ post, HeadingTag = 'h3' }) {
    return (
        <article className="article-card">
            <Link
                href={`/articles/${post.slug}`}
                className="article-cover"
            >
                {post.cover_image_path ? (
                    <img src={`/storage/${post.cover_image_path}`} alt={post.title} loading="lazy" />
                ) : (
                    <div className="cover-placeholder" aria-hidden="true">
                        <span>{post.category}</span>
                        <b>{post.title.slice(0, 1)}</b>
                    </div>
                )}
                <span style={{ position: 'absolute', width: '1px', height: '1px', overflow: 'hidden', clip: 'rect(0,0,0,0)' }}>
                    Baca artikel {post.title} -{' '}
                </span>
                <span className="category-tag">{post.category}</span>
            </Link>
            <div className="article-card-body">
                <div className="article-meta">
                    {post.user?.name || 'BlogVerse Writer'} <span>·</span>{' '}
                    {post.published_at
                        ? new Date(post.published_at).toLocaleDateString('id-ID', {
                            day: 'numeric',
                            month: 'short',
                            year: 'numeric',
                        })
                        : 'Draft'}
                </div>
                <HeadingTag>
                    <Link href={`/articles/${post.slug}`}>{post.title}</Link>
                </HeadingTag>
                <p>{post.excerpt}</p>
                <Link href={`/articles/${post.slug}`} className="read-link">
                    Baca artikel <ArrowUpRight size={15} aria-hidden="true" />
                </Link>
            </div>
        </article>
    );
}
