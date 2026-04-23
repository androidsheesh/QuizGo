import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    // Add this server block to ignore massive vendor folders
    server: {
        watch: {
            ignored: [
                '**/vendor/**',
                '**/node_modules/**'
            ],
        },
    },
});
