import '../css/app.css';
import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import ComponentShowcase from './Components/UI/ComponentShowcase.vue';

const appEl = document.getElementById('app');

if (appEl) {
    if (appEl.dataset && appEl.dataset.page) {
        // Standard Inertia.js application mount
        createInertiaApp({
            resolve: (name) => {
                const pages = import.meta.glob('./Pages/**/*.vue', { eager: true });
                return pages[`./Pages/${name}.vue`];
            },
            setup({ el, App, props, plugin }) {
                createApp({ render: () => h(App, props) })
                    .use(plugin)
                    .mount(el);
            },
        });
    } else {
        // Standalone preview for Jobdesk 1 Reusable Components Showcase
        createApp(ComponentShowcase).mount(appEl);
    }
}
