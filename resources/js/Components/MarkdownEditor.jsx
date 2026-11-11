import React, { useEffect, useRef } from 'react';
import Editor from '@toast-ui/editor';
import '@toast-ui/editor/dist/toastui-editor.css';

export default function MarkdownEditor({ value, onChange, height = '360px' }) {
    const containerRef = useRef(null);
    const editorRef = useRef(null);

    useEffect(() => {
        if (!containerRef.current) return;

        const instance = new Editor({
            el: containerRef.current,
            initialValue: value || '',
            previewStyle: 'vertical',
            height,
            initialEditType: 'markdown',
            useCommandShortcut: true,
            toolbarItems: [
                ['heading', 'bold', 'italic', 'strike'],
                ['hr', 'quote'],
                ['ul', 'ol', 'task'],
                ['link', 'table'],
                ['code', 'codeblock'],
            ],
            events: {
                change: () => {
                    onChange?.(instance.getMarkdown() || '');
                },
            },
        });

        editorRef.current = instance;

        return () => {
            instance.destroy();
            editorRef.current = null;
        };
    }, []);

    useEffect(() => {
        if (editorRef.current && editorRef.current.getMarkdown() !== (value || '')) {
            editorRef.current.setMarkdown(value || '');
        }
    }, [value]);

    return <div className="markdown-editor" ref={containerRef} />;
}
