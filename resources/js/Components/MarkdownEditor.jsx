import React, { useState } from 'react';
import SimpleMarkdown from './SimpleMarkdown';

export default function MarkdownEditor({ id = 'post-content-input', value, onChange, height = '360px' }) {
    const [tab, setTab] = useState('write');
    const text = value || '';

    const insertFormat = (prefix, suffix = '', placeholder = 'teks') => {
        const next = `${text}${text && !text.endsWith('\n') ? '\n' : ''}${prefix}${placeholder}${suffix}`;
        onChange?.(next);
    };

    return (
        <div className="markdown-editor" style={{ border: '1px solid #cfcfe0', borderRadius: '10px', overflow: 'hidden', background: '#fff' }}>
            <div
                style={{
                    display: 'flex',
                    alignItems: 'center',
                    justifyContent: 'space-between',
                    gap: '6px',
                    padding: '8px 10px',
                    borderBottom: '1px solid #e2e2ec',
                    background: '#f8f8fc',
                    flexWrap: 'wrap',
                }}
            >
                <div style={{ display: 'flex', gap: '4px', flexWrap: 'wrap' }}>
                    <button type="button" className="btn btn-outline btn-sm" aria-label="Tambah Subjudul" onClick={() => insertFormat('## ', '', 'Subjudul')}>H</button>
                    <button type="button" className="btn btn-outline btn-sm" aria-label="Teks Tebal" onClick={() => insertFormat('**', '**', 'teks tebal')}><b>B</b></button>
                    <button type="button" className="btn btn-outline btn-sm" aria-label="Teks Miring" onClick={() => insertFormat('*', '*', 'teks miring')}><i>I</i></button>
                    <button type="button" className="btn btn-outline btn-sm" aria-label="Teks Coret" onClick={() => insertFormat('~~', '~~', 'coret')}><s>S</s></button>
                    <button type="button" className="btn btn-outline btn-sm" aria-label="Kutipan" onClick={() => insertFormat('> ', '', 'kutipan')}>“”</button>
                    <button type="button" className="btn btn-outline btn-sm" aria-label="Daftar Poin" onClick={() => insertFormat('- ', '', 'item daftar')}>• List</button>
                    <button type="button" className="btn btn-outline btn-sm" aria-label="Tautan" onClick={() => insertFormat('[', '](https://)', 'tautan')}>Link</button>
                    <button type="button" className="btn btn-outline btn-sm" aria-label="Blok Kode" onClick={() => insertFormat('```\n', '\n```', 'kode')}>{'</>'}</button>
                </div>
                <div style={{ display: 'flex', gap: '4px' }}>
                    <button
                        type="button"
                        className={`btn btn-sm ${tab === 'write' ? 'btn-primary' : 'btn-outline'}`}
                        onClick={() => setTab('write')}
                    >
                        Write
                    </button>
                    <button
                        type="button"
                        className={`btn btn-sm ${tab === 'preview' ? 'btn-primary' : 'btn-outline'}`}
                        onClick={() => setTab('preview')}
                    >
                        Preview
                    </button>
                </div>
            </div>

            {tab === 'write' ? (
                <textarea
                    id={id}
                    aria-label="Konten artikel"
                    value={text}
                    onChange={e => onChange?.(e.target.value)}
                    placeholder="Tulis konten artikel dalam format Markdown..."
                    style={{
                        width: '100%',
                        minHeight: height,
                        border: 'none',
                        padding: '14px',
                        resize: 'vertical',
                        outline: 'none',
                        fontFamily: 'inherit',
                        fontSize: '14px',
                        lineHeight: '1.6',
                    }}
                />
            ) : (
                <div
                    className="markdown-view"
                    style={{
                        minHeight: height,
                        padding: '14px',
                        overflowY: 'auto',
                    }}
                >
                    <SimpleMarkdown>
                        {text || '*Belum ada konten untuk ditampilkan.*'}
                    </SimpleMarkdown>
                </div>
            )}
        </div>
    );
}
