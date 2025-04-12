import '../css/app.scss';

import { createInertiaApp } from '@inertiajs/vue3';
import { Tooltip } from 'bootstrap';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import 'vue3-toastify/dist/index.css';
import { ZiggyVue } from 'ziggy-js';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

const tooltipDirective = {
    mounted: (el, binding) => {
        const options = binding.value || {};
        new Tooltip(el, options);
    },
    unmounted: (el) => {
        const tooltip = Tooltip.getInstance(el);
        if (tooltip) {
            tooltip.dispose();
        }
    },
};

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./pages/${name}.vue`, import.meta.glob('./pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });

        app.directive('tooltip', tooltipDirective);

        app.use(plugin).use(ZiggyVue).mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
