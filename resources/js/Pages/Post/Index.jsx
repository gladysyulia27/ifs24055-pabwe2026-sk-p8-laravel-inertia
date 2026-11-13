import React, { useState, useEffect } from 'react';
import { Link, router, useForm, usePage } from '@inertiajs/react';
import DashboardLayout from '../../Layouts/DashboardLayout';
import Modal from '../../Components/Modal';
import MarkdownEditor from '../../Components/MarkdownEditor';
import { Plus, Download, Upload, MoreVertical, Eye, Pencil, Trash2, Globe, FileText, Sparkles } from 'lucide-react';

export default function PostIndex({ posts, stats }) {
    const [modal, setModal] = useState(false);
    const [editing, setEditing] = useState(null);
    const [menu, setMenu] = useState(null);
    const [aiOpen, setAiOpen] = useState(false);
    const [generated, setGenerated] = useState([]);

    const form = useForm({
        title: '',
        category: 'Teknologi',
        excerpt: '',
        content: '',
        is_published: false,
    });

    const ai = useForm({
        topic: '',
        category: 'Teknologi',
        count: 1,
    });

    const save = e => {
        e.preventDefault();
        if (editing) {
            form.put(`/dashboard/posts/${editing.id}`, { onSuccess: close });
        } else {
            form.post('/dashboard/posts', { onSuccess: close });
        }
    };

    const close = () => {
        setModal(false);
        setEditing(null);
        form.reset();
    };

    const edit = p => {
        setEditing(p);
        form.setData({
            title: p.title,
            category: p.category,
            excerpt: p.excerpt || '',
            content: p.content,
            is_published: p.is_published,
        });
        setModal(true);
        setMenu(null);
    };

    const create = () => {
        setEditing(null);
        form.reset();
        setModal(true);
    };

    const remove = p => {
        if (confirm(`Hapus artikel "${p.title}"? Tindakan ini tidak dapat dibatalkan.`)) {
            router.delete(`/dashboard/posts/${p.id}`);
        }
    };

    const toggle = p => router.patch(`/dashboard/posts/${p.id}/publish`);

    const generate = e => {
        e.preventDefault();
        ai.post('/dashboard/posts/ai-generate', {
            onSuccess: page => {
                const data = page.props.flash?.generatedArticles || [];
                setGenerated(data);
                setAiOpen(false);
            },
        });
    };

    const flashGenerated = usePage().props.flash?.generatedArticles;
    useEffect(() => {
        if (flashGenerated?.length) setGenerated(flashGenerated);
    }, [flashGenerated]);

    const saveAI = () => router.post('/dashboard/posts/ai-save', { articles: generated }, { onSuccess: () => setGenerated([]) });

    return (
        <DashboardLayout title="Artikel Saya">
            <div className="page-heading">
                <div>
                    <span className="eyebrow">CONTENT STUDIO</span>
                    <h1>Artikel saya<span className="heading-dot">.</span></h1>
                    <p>Kelola ide dan publikasi tulisanmu di satu tempat.</p>
                </div>
                <button type="button" className="btn btn-primary" onClick={create}>
                    <Plus size={17} aria-hidden="true" /> Tulis artikel
                </button>
            </div>

            <div className="stats-grid">
                <div className="stat-card">
                    <span>Total Artikel</span>
                    <b>{stats.total}</b>
                    <small><FileText size={14} aria-hidden="true" /> Semua tulisanmu</small>
                </div>
                <div className="stat-card stat-green">
                    <span>Total Selesai</span>
                    <b>{stats.published}</b>
                    <small><Globe size={14} aria-hidden="true" /> Sudah dipublikasikan</small>
                </div>
                <div className="stat-card stat-amber">
                    <span>Belum Selesai</span>
                    <b>{stats.draft}</b>
                    <small><Pencil size={14} aria-hidden="true" /> Masih berupa draft</small>
                </div>
            </div>

            <div className="toolbar">
                <div>
                    <h2>Daftar tulisan</h2>
                    <p>Daftar artikel terbaru yang kamu buat.</p>
                </div>
                <div className="toolbar-actions">
                    <button type="button" className="btn btn-outline btn-sm" onClick={() => setAiOpen(true)}>
                        <Sparkles size={15} aria-hidden="true" /> AI Generator
                    </button>
                    <form
                        action="/dashboard/posts/import"
                        method="post"
                        encType="multipart/form-data"
                        onSubmit={e => {
                            e.preventDefault();
                            const fd = new FormData(e.currentTarget);
                            router.post('/dashboard/posts/import', fd);
                        }}
                    >
                        <input type="hidden" name="_token" value={typeof document !== 'undefined' ? document.querySelector('meta[name=csrf-token]')?.content || '' : ''} />
                        <label className="btn btn-outline btn-sm upload-label">
                            <Upload size={15} aria-hidden="true" /> Impor Excel
                            <input
                                type="file"
                                name="file"
                                aria-label="Impor file Excel"
                                accept=".xlsx,.xls,.csv"
                                onChange={e => e.target.form.requestSubmit()}
                            />
                        </label>
                    </form>
                    <a className="btn btn-outline btn-sm" href="/dashboard/posts/export">
                        <Download size={15} aria-hidden="true" /> Ekspor
                    </a>
                </div>
            </div>

            <div className="list-table-wrap">
                <table className="list-table">
                    <thead>
                        <tr>
                            <th>JUDUL ARTIKEL</th>
                            <th>KATEGORI</th>
                            <th>STATUS</th>
                            <th>DIPERBARUI</th>
                            <th><span style={{ position: 'absolute', width: '1px', height: '1px', overflow: 'hidden', clip: 'rect(0,0,0,0)' }}>AKSI</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        {posts.data.length ? (
                            posts.data.map(p => (
                                <tr key={p.id}>
                                    <td>
                                        <div className="post-title-cell">
                                            <span className="post-file-icon"><FileText size={17} aria-hidden="true" /></span>
                                            <div>
                                                <b>{p.title}</b>
                                                <small>{p.excerpt || 'Tidak ada ringkasan'}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span className="category-soft">{p.category}</span></td>
                                    <td>
                                        <span className={`status ${p.is_published ? 'published' : 'draft'}`}>
                                            <i aria-hidden="true" />
                                            {p.is_published ? 'Published' : 'Draft'}
                                        </span>
                                    </td>
                                    <td className="date-cell">
                                        {new Date(p.updated_at).toLocaleDateString('id-ID', {
                                            day: '2-digit',
                                            month: 'short',
                                            year: 'numeric',
                                        })}
                                    </td>
                                    <td className="row-actions">
                                        <button
                                            type="button"
                                            className="icon-button"
                                            aria-label={`Menu aksi untuk ${p.title}`}
                                            aria-expanded={menu === p.id}
                                            onClick={() => setMenu(menu === p.id ? null : p.id)}
                                        >
                                            <MoreVertical size={19} aria-hidden="true" />
                                        </button>
                                        {menu === p.id && (
                                            <div className="action-menu">
                                                <Link href={`/dashboard/posts/${p.id}`}>
                                                    <Eye size={15} aria-hidden="true" /> Detail
                                                </Link>
                                                <button type="button" onClick={() => edit(p)}>
                                                    <Pencil size={15} aria-hidden="true" /> Ubah Edit
                                                </button>
                                                <button type="button" onClick={() => toggle(p)}>
                                                    <Globe size={15} aria-hidden="true" />
                                                    {p.is_published ? 'Jadikan Draf' : 'Publikasikan'}
                                                </button>
                                                <button type="button" className="danger" onClick={() => remove(p)}>
                                                    <Trash2 size={15} aria-hidden="true" /> Hapus
                                                </button>
                                            </div>
                                        )}
                                    </td>
                                </tr>
                            ))
                        ) : (
                            <tr>
                                <td colSpan="5">
                                    <div className="empty-state compact">
                                        <FileText size={28} aria-hidden="true" />
                                        <b>Belum ada artikel</b>
                                        <span>Mulai tulis artikel pertamamu.</span>
                                        <button type="button" className="btn btn-primary btn-sm" onClick={create}>
                                            <Plus size={15} aria-hidden="true" /> Tulis artikel
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        )}
                    </tbody>
                </table>
            </div>

            {posts.links && (
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

            <Modal
                open={modal}
                onClose={close}
                title={editing ? 'Ubah artikel' : 'Tulis artikel baru'}
                subtitle="Tuangkan ide terbaikmu ke dalam sebuah cerita."
                onSubmit={save}
                processing={form.processing}
                tone="purple"
            >
                <div className="form-grid">
                    <div className="form-field full">
                        <label htmlFor="post-title-input">Judul artikel</label>
                        <input
                            id="post-title-input"
                            value={form.data.title}
                            onChange={e => form.setData('title', e.target.value)}
                            placeholder="Tulis judul yang menarik..."
                            required
                            maxLength="250"
                        />
                    </div>
                    <div className="form-field">
                        <label htmlFor="post-category-select">Kategori</label>
                        <select
                            id="post-category-select"
                            value={form.data.category}
                            onChange={e => form.setData('category', e.target.value)}
                        >
                            {['Teknologi', 'Pendidikan', 'Gaya Hidup', 'Bisnis', 'Opini', 'Umum'].map(x => (
                                <option key={x} value={x}>{x}</option>
                            ))}
                        </select>
                    </div>
                    <div className="form-field">
                        <label htmlFor="post-status-select">Status awal</label>
                        <select
                            id="post-status-select"
                            value={form.data.is_published ? '1' : '0'}
                            onChange={e => form.setData('is_published', e.target.value === '1')}
                        >
                            <option value="0">Draft</option>
                            <option value="1">Publikasikan</option>
                        </select>
                    </div>
                    <div className="form-field full">
                        <label htmlFor="post-excerpt-input">Ringkasan artikel</label>
                        <textarea
                            id="post-excerpt-input"
                            rows="2"
                            value={form.data.excerpt}
                            onChange={e => form.setData('excerpt', e.target.value)}
                            placeholder="Ringkasan singkat untuk menarik pembaca..."
                        />
                    </div>
                    <div className="form-field full">
                        <label htmlFor="post-content-input">
                            Konten artikel <span>Markdown editor</span>
                        </label>
                        <MarkdownEditor
                            id="post-content-input"
                            value={form.data.content}
                            onChange={v => form.setData('content', v)}
                        />
                        {form.errors.content && <small className="field-error">{form.errors.content}</small>}
                    </div>
                </div>
            </Modal>

            <Modal
                open={aiOpen}
                onClose={() => setAiOpen(false)}
                title="AI Article Generator"
                subtitle="Buat draft artikel berkualitas dengan bantuan AI."
                onSubmit={generate}
                processing={ai.processing}
                submitText="Generate artikel"
                tone="blue"
            >
                <div className="ai-intro">
                    <Sparkles size={23} aria-hidden="true" />
                    <p>Jelaskan topik yang ingin kamu bahas. AI akan menyusun draft artikel Markdown yang bisa kamu tinjau sebelum disimpan.</p>
                </div>
                <div className="form-grid">
                    <div className="form-field full">
                        <label htmlFor="ai-topic-input">Topik / ide artikel</label>
                        <input
                            id="ai-topic-input"
                            value={ai.data.topic}
                            onChange={e => ai.setData('topic', e.target.value)}
                            placeholder="Contoh: Masa depan kecerdasan buatan di pendidikan"
                            required
                        />
                    </div>
                    <div className="form-field">
                        <label htmlFor="ai-category-select">Kategori</label>
                        <select
                            id="ai-category-select"
                            value={ai.data.category}
                            onChange={e => ai.setData('category', e.target.value)}
                        >
                            {['Teknologi', 'Pendidikan', 'Gaya Hidup', 'Bisnis', 'Opini', 'Umum'].map(x => (
                                <option key={x} value={x}>{x}</option>
                            ))}
                        </select>
                    </div>
                    <div className="form-field">
                        <label htmlFor="ai-count-input">Jumlah artikel (1–5)</label>
                        <input
                            id="ai-count-input"
                            type="number"
                            min="1"
                            max="5"
                            value={ai.data.count}
                            onChange={e => ai.setData('count', Math.max(1, Math.min(5, Number(e.target.value))))}
                        />
                    </div>
                </div>
                {ai.errors.ai && <div className="alert alert-error">{ai.errors.ai}</div>}
            </Modal>

            {generated.length > 0 && (
                <Modal
                    open
                    onClose={() => setGenerated([])}
                    title="Tinjau hasil AI"
                    subtitle={`${generated.length} draft artikel siap ditinjau sebelum disimpan.`}
                    onSubmit={e => {
                        e.preventDefault();
                        saveAI();
                    }}
                    processing={false}
                    submitText="Simpan semua draft"
                    tone="blue"
                >
                    <div className="generated-list">
                        {generated.map((a, i) => (
                            <div className="generated-item" key={i}>
                                <span className="category-soft">{a.category}</span>
                                <h3>{a.title}</h3>
                                <p>{a.excerpt}</p>
                                <details>
                                    <summary>Pratinjau konten</summary>
                                    <pre>{a.content}</pre>
                                </details>
                            </div>
                        ))}
                    </div>
                </Modal>
            )}
        </DashboardLayout>
    );
}
