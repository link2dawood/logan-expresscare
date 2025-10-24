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
        server: {
            hmr: {
                host: env.VITE_HRM_HOST ?? 'localhost',
            },
        },
    }
});
