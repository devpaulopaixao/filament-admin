import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';

export default defineConfig({
    server: {
        host: '0.0.0.0',
        port: 5173,
        allowedHosts: 'all',
        hmr: {
            host: 'localhost',
            port: 5173,
        },
    },
    plugins: [
        react(),
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/panel-display.jsx',
                'resources/js/panel-password.js',
                'resources/js/screen-display.jsx',
            ],
            refresh: true,
        }),
    ],
});
