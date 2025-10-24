import path from "path";
import { defineConfig, loadEnv } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";
import Components from "unplugin-vue-components/vite";
import { AntDesignVueResolver } from "unplugin-vue-components/resolvers";

import tailwind from "tailwindcss";
import autoprefixer from "autoprefixer";

export default defineConfig(({ mode }) => {
    // Load .env file based on current mode (e.g., .env, .env.development, etc.)
    const env = loadEnv(mode, process.cwd());

    return {
        base: mode === 'production' ? 'http://admin.loganexpresscare.com.au/build/' : '/',
        css: {
            preprocessorOptions: {
                less: {
                    javascriptEnabled: true, // Enable inline JavaScript in Less
                },
            },
            postcss: {
                plugins: [tailwind(), autoprefixer()],
            },
        },
        plugins: [
            laravel({
                input: ["resources/js/app.js"],
            }),
            vue({
                template: {
                    transformAssetUrls: {
                        base: null,
                        includeAbsolute: false,
                    },
                },
            }),
            Components({
                resolvers: [
                    AntDesignVueResolver({
                        importStyle: false, // css in js
                    }),
                ],
            }),
        ],
        resolve: {
            alias: {
                "@": path.resolve(__dirname, "./resources/js"),
            },
        },
        optimizeDeps: {
            include: ['quill', '@vueup/vue-quill'],
            exclude: [],
            esbuildOptions: {
                define: {
                    global: 'globalThis'
                }
            }
        },
        server: {
            hmr: {
                host: env.VITE_HRM_HOST ?? 'localhost',
            },
        },
        build: {
            chunkSizeWarningLimit: 1000,
            sourcemap: false,
            minify: 'esbuild',
            commonjsOptions: {
                include: [/node_modules/],
                transformMixedEsModules: true,
            },
            rollupOptions: {
                output: {
                    manualChunks: (id) => {
                        if (id.includes('node_modules')) {
                            // Keep vue ecosystem together to prevent initialization issues
                            if (id.includes('ant-design-vue') || id.includes('@ant-design')) {
                                return 'ant-design-vue';
                            }
                            if (id.includes('vue') || id.includes('@vue') || id.includes('pinia')) {
                                return 'vue';
                            }
                            if (id.includes('chart')) {
                                return 'chart';
                            }
                            // Don't chunk Quill separately - include in vendor to avoid circular dependency issues
                            // if (id.includes('quill') || id.includes('@vueup/vue-quill')) {
                            //     return 'quill';
                            // }
                            return 'vendor';
                        }
                    },
                },
            },
        },
    }
});
