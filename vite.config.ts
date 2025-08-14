import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import path from 'path';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    server: {
        host: '127.0.0.1',
        port: 5174
    },
    plugins: [
        laravel([
            'resources/css/app.css',
            'resources/js/main.ts',
        ]),
        vue(),
        tailwindcss(),
    ],
    resolve: {
        alias: {
        '@': path.resolve(__dirname, 'resources/js'),
    },
    },
});
