import React from 'react';
import { Link, router } from '@inertiajs/react';
import DashboardLayout from '../../Layouts/DashboardLayout';
import SimpleMarkdown from '../../Components/SimpleMarkdown';
import { ArrowLeft, Globe, Trash2, ImagePlus } from 'lucide-react';

export default function Show({ post }) {
    const cover = e => {
        const fd = new FormData();
        fd.append('cover', e.target.files[0]);
        router.post(`/dashboard/posts/${post.id}/cover`, fd);
    };

    return (
        <DashboardLayout title="Detail Artikel">
            <div className="page-heading">
                <div>
                    <Link href="/dashboard/posts" className="back-link">
                        <ArrowLeft size={15} aria-hidden="true" /> Kembali ke daftar
                    </Link>
                    <span className="eyebrow">ARTICLE PREVIEW</span>
                    <h1>{post.title}</h1>
                    <p>{post.category} · {post.is_published ? 'Published' : 'Draft'}</p>
                </div>
                <div className="toolbar-actions">
                    <Link href="/dashboard/posts" className="btn btn-outline">Daftar artikel</Link>
                    <button
                        type="button"
                        className="btn btn-primary"
                        onClick={() => router.patch(`/dashboard/posts/${post.id}/publish`)}
                    >
                        <Globe size={16} aria-hidden="true" />
                        {post.is_published ? 'Jadikan Draft' : 'Publikasikan'}
                    </button>
                </div>
            </div>

            <div className="detail-tools">
                <label className="btn btn-outline btn-sm">
                    <ImagePlus size={15} aria-hidden="true" /> Unggah cover
                    <input type="file" aria-label="Unggah gambar cover" accept="image/*" onChange={cover} hidden />
                </label>
                {post.cover_image_path && (
                    <button
                        type="button"
                        className="btn btn-outline btn-sm"
                        onClick={() => router.delete(`/dashboard/posts/${post.id}/cover`)}
                    >
                        Hapus cover
                    </button>
                )}
                <button
                    type="button"
                    className="btn btn-danger btn-sm"
                    onClick={() => {
                        if (confirm('Hapus artikel ini?')) router.delete(`/dashboard/posts/${post.id}`);
                    }}
                >
                    <Trash2 size={15} aria-hidden="true" /> Hapus artikel
                </button>
            </div>

            {post.cover_image_path && (
                <img
                    className="detail-cover"
                    src={`/storage/${post.cover_image_path}`}
                    alt={post.title}
                />
            )}

            <div className="detail-paper">
                <div className="detail-excerpt">{post.excerpt}</div>
                <div className="markdown-view">
                    <SimpleMarkdown>{post.content}</SimpleMarkdown>
                </div>
            </div>
        </DashboardLayout>
    );
}
