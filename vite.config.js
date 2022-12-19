import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";
import Components from "unplugin-vue-components/vite";
import { AntDesignVueResolver } from "unplugin-vue-components/resolvers";

export default defineConfig({
    css: {
        preprocessorOptions: {
            less: {
                modifyVars: {
                    "primary-color": "#0084ff",
                    "border-radius-base": "5px",
                },
                javascriptEnabled: true,
            },
        },
    },
    plugins: [
        laravel({
            input: "resources/js/app.js",
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
        Components({
            resolvers: [
                AntDesignVueResolver({
                    importStyle: "less",
                }),
            ],
        }),
    ],
});
