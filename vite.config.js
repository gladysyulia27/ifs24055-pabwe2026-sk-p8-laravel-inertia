import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.jsx'],
            refresh: true,
        }),
        react(),
    ],
    resolve: {
        alias: {
            '@': '/resources/js',
        },
    },
    build: {
        sourcemap: true,
        cssCodeSplit: true,
        rollupOptions: {
            output: {
                manualChunks(id) {
                    if (id.includes('node_modules/react-markdown') || id.includes('node_modules/remark-') || id.includes('node_modules/micromark') || id.includes('node_modules/mdast-') || id.includes('node_modules/hast-') || id.includes('node_modules/unified')) {
                        return 'markdown';
                    }
                    if (id.includes('node_modules/lucide-react')) {
                        return 'icons';
                    }
                },
            },
        },
    },
});
