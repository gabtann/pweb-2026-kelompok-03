import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';
import path from 'path';
import fs from 'fs';

// Jika Laravel vendor belum di-install, jalankan Vite mode Standalone agar bisa melihat UI langsung di localhost:5173
const hasLaravelVendor = fs.existsSync(path.resolve(import.meta.dirname, 'vendor/autoload.php'));

export default defineConfig({
    plugins: [
        ...(hasLaravelVendor ? [
            laravel({
                input: ['resources/css/app.css', 'resources/js/app.js'],
                refresh: true,
            }),
        ] : []),
        tailwindcss(),
        vue(),
    ],
    resolve: {
        alias: {
            '@': path.resolve(import.meta.dirname, './resources/js'),
        },
    },
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
