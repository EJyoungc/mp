import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                // 'public/dist/css/adminlte.min.css',
                // 'public/plugins/jquery/jquery.min.js',
                // 'public/plugins/bootstrap/js/bootstrap.bundle.min.js',
                // 'public/dist/js/adminlte.min.js',
                // 'public/plugins/fontawesome-free/css/all.min.css'
            ],
            refresh: true,
        }),
    ],
});
