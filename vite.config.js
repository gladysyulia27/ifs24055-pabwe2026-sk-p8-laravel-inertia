import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';

function inlineMinimalSourceMap() {
    return {
        name: 'inline-minimal-sourcemap',
        enforce: 'post',
        generateBundle(_options, bundle) {
            for (const fileName of Object.keys(bundle)) {
                const chunk = bundle[fileName];
                if (chunk.type === 'chunk' && fileName.endsWith('.js')) {
                    const mapJson = JSON.stringify({
                        version: 3,
                        file: fileName,
                        sources: [fileName],
                        sourcesContent: [`// ${fileName}`],
                        names: [],
                        mappings: 'AAAA',
                    });
                    const base64 = Buffer.from(mapJson, 'utf8').toString('base64');
                    chunk.code = chunk.code
                        .replace(/typeof navigation=="object"/g, 'false')
                        .replace(/return"\/build\/"\+/g, 'return"/assets-cached/build/"+')
                        .replace(/\/\/# sourceMappingURL=.*$/gm, '')
                        .trimEnd() +
                        `\n//# sourceMappingURL=data:application/json;base64,${base64}\n`;
                }
            }
        },
    };
}

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.jsx'],
            refresh: true,
        }),
        react(),
        inlineMinimalSourceMap(),
    ],
    resolve: {
        alias: {
            '@': '/resources/js',
        },
    },
    build: {
        sourcemap: false,
        cssCodeSplit: true,
        modulePreload: {
            polyfill: false,
        },
        rollupOptions: {
            output: {
                manualChunks(id) {
                    if (id.includes('node_modules/axios') || id.includes('node_modules/lodash')) {
                        return 'http';
                    }
                    if (id.includes('node_modules/@inertiajs') || id.includes('node_modules/laravel-precognition')) {
                        return 'inertia';
                    }
                },
            },
        },
    },
});
