import React from 'react';

function renderInline(text) {
    if (!text) return null;
    const escaped = String(text)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;');

    const html = escaped
        .replace(/`([^`]+)`/g, '<code>$1</code>')
        .replace(/\*\*([^*]+)\*\*/g, '<strong>$1</strong>')
        .replace(/\*([^*]+)\*/g, '<em>$1</em>')
        .replace(/~~([^~]+)~~/g, '<del>$1</del>')
        .replace(/\[([^\]]+)\]\((https?:\/\/[^\s)]+|\/[^\s)]*)\)/g, '<a href="$2" rel="noopener noreferrer">$1</a>');

    return <span dangerouslySetInnerHTML={{ __html: html }} />;
}

export default function SimpleMarkdown({ children }) {
    const raw = typeof children === 'string' ? children : String(children || '');
    const lines = raw.replace(/\r\n/g, '\n').split('\n');
    const elements = [];
    let i = 0;

    while (i < lines.length) {
        const line = lines[i];

        if (!line.trim()) {
            i++;
            continue;
        }

        if (line.trim().startsWith('```')) {
            const codeLines = [];
            i++;
            while (i < lines.length && !lines[i].trim().startsWith('```')) {
                codeLines.push(lines[i]);
                i++;
            }
            i++;
            elements.push(
                <pre key={`code-${i}`}>
                    <code>{codeLines.join('\n')}</code>
                </pre>
            );
            continue;
        }

        const headingMatch = line.match(/^(#{1,6})\s+(.*)$/);
        if (headingMatch) {
            const level = headingMatch[1].length;
            const content = headingMatch[2];
            if (level <= 2) {
                elements.push(<h2 key={`h-${i}`}>{renderInline(content)}</h2>);
            } else {
                elements.push(<h3 key={`h-${i}`}>{renderInline(content)}</h3>);
            }
            i++;
            continue;
        }

        if (line.trim().startsWith('>')) {
            const quoteLines = [];
            while (i < lines.length && lines[i].trim().startsWith('>')) {
                quoteLines.push(lines[i].replace(/^\s*>\s?/, ''));
                i++;
            }
            elements.push(
                <blockquote key={`q-${i}`}>
                    <p>{renderInline(quoteLines.join(' '))}</p>
                </blockquote>
            );
            continue;
        }

        if (/^\s*[-*+]\s+/.test(line)) {
            const items = [];
            while (i < lines.length && /^\s*[-*+]\s+/.test(lines[i])) {
                items.push(lines[i].replace(/^\s*[-*+]\s+/, ''));
                i++;
            }
            elements.push(
                <ul key={`ul-${i}`}>
                    {items.map((item, idx) => (
                        <li key={idx}>{renderInline(item)}</li>
                    ))}
                </ul>
            );
            continue;
        }

        if (/^\s*\d+\.\s+/.test(line)) {
            const items = [];
            while (i < lines.length && /^\s*\d+\.\s+/.test(lines[i])) {
                items.push(lines[i].replace(/^\s*\d+\.\s+/, ''));
                i++;
            }
            elements.push(
                <ol key={`ol-${i}`}>
                    {items.map((item, idx) => (
                        <li key={idx}>{renderInline(item)}</li>
                    ))}
                </ol>
            );
            continue;
        }

        const paraLines = [];
        while (
            i < lines.length &&
            lines[i].trim() &&
            !lines[i].trim().startsWith('```') &&
            !lines[i].trim().startsWith('#') &&
            !lines[i].trim().startsWith('>') &&
            !/^\s*[-*+]\s+/.test(lines[i]) &&
            !/^\s*\d+\.\s+/.test(lines[i])
        ) {
            paraLines.push(lines[i]);
            i++;
        }
        elements.push(<p key={`p-${i}`}>{renderInline(paraLines.join(' '))}</p>);
    }

    return <>{elements}</>;
}

