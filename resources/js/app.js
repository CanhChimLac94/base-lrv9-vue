import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/inertia-vue3';
import { InertiaProgress } from '@inertiajs/progress';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';
import { createVuetify } from "vuetify";
import * as components from 'vuetify/components';
import * as directives from 'vuetify/directives';
import store from './store';
import DefaultLayout from '@/Layouts/default.vue';
// import 'vuetify/dist/vuetify.min.css'

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel';

createInertiaApp({
	title: (title) => `${title} - ${appName}`,
	resolve: (name) => {
		const page = resolvePageComponent(
			`./Pages/${name}.vue`,
			import.meta.glob('./Pages/**/*.vue')
		);
		page.layout = page.layout || DefaultLayout;
		return page;
	},
	setup({ el, app, props, plugin }) {
		const vuetify = createVuetify({ 
			components, 
			directives, 
			ssr: true,
		});

		return createApp({ render: () => h(app, props) })
			.use(plugin)
			.use(ZiggyVue, Ziggy)
			.use(store)
			.use(vuetify)
			.mount(el);
	},
});

InertiaProgress.init({ color: '#4B5563' });
