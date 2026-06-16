import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/configurator.css',
                'resources/js/configurator-loader.js'
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    build: {
        // The configurator bundle (three.js, ~650KB) is intentionally large and
        // lazy-loaded only when a user opens the 3D configurator, so it never
        // affects initial page load. Raise the threshold so the build doesn't
        // warn about this deliberate, code-split chunk.
        chunkSizeWarningLimit: 900,
    },
    server: {
        host: '127.0.0.1',
        port: 5173,
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
