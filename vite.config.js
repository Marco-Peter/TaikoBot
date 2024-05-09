import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import { VitePWA } from 'vite-plugin-pwa';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.js'],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        VitePWA({
            registerType: 'autoUpdate',
            injectRegister: 'auto',
            workbox: {
                globPatterns: ['**/*.{js,css,html,png}'],
            },
            strategies: 'generateSW',
            includeAssets: ['favicon.ico', '/images/favicon_black.png', '/images/favicon_white.png'],
            manifest: {
                name: 'TaikoBot',
                short_name: 'TaikoBot',
                description: 'Manage your Taiko experience',
                theme_color: '#94ab67',
                icons: [
                    {
                        src: '/images/favicon_white_192.png',
                        sizes: '192x192',
                        type: 'image/png',
                        purpose: 'any maskable',
                    },
                    {
                        src: '/images/favicon_white_512.png',
                        sizes: '512x512',
                        type: 'image/png',
                    },
                    {
                        src: '/images/favicon_white_512.png',
                        sizes: '512x512',
                        type: 'image/png',
                        purpose: 'any',
                    },
                    {
                        src: '/images/favicon_white_512.png',
                        sizes: '512x512',
                        type: 'image/png',
                        purpose: 'maskable',
                    },
                ],
            },
            devOptions: {
                enabled: true,
            },
        }),
    ],
    resolve: {
        alias: {
            vue: 'vue/dist/vue.esm-bundler.js',
        },
    },
});
