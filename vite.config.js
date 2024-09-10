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
            outDir: 'public',
            scope: '/',
            base: '/',
            injectRegister: 'auto',
            workbox: {
                globPatterns: ['**/*.{js,css,html,png}'],
                runtimeCaching: [{
                    urlPattern: ({ url }) => {
                        return url.pathname.startsWith("/local-api");
                    },
                    handler: "CacheFirst",
                    options: {
                        cacheName: "api-cache",
                        cacheableResponse: {
                            statuses: [0, 200],
                        }
                    }
                }]
            },
            strategies: 'generateSW',
            includeAssets: ['favicon.ico', '/images/favicon_black.png', '/images/favicon_white.png'],
            manifest: {
                id: '/',
                scope: '/',
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
