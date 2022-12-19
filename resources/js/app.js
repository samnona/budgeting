import "./bootstrap";
import "../css/app.css";

import { createApp, h } from "vue";
import { createInertiaApp } from "@inertiajs/inertia-vue3";
import { InertiaProgress } from "@inertiajs/progress";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { ZiggyVue } from "../../vendor/tightenco/ziggy/dist/vue.m";
import { Inertia } from "@inertiajs/inertia";
import "ant-design-vue/lib/notification/style/css";

const appName =
    window.document.getElementsByTagName("title")[0]?.innerText || "Laravel";

window.back = () => window.history.back(); // use to do back
window.addEventListener("popstate", (event) => {
    event.stopImmediatePropagation();

    Inertia.reload({
        preserveState: false,
        preserveScroll: false,
        replace: true,
        onSuccess: (page) => Inertia.setPage(page),
        onError: () => (window.location.href = event.state.url),
    });
});

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob("./Pages/**/*.vue")
        ),
    setup({ el, app, props, plugin }) {
        return createApp({ render: () => h(app, props) })
            .use(plugin)
            .use(ZiggyVue, Ziggy)
            .mount(el);
    },
});

InertiaProgress.init({ color: "#4B5563" });
