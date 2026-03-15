import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp, router } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import { notify } from './Utils/alert'; 


import 'vuetify/styles'
import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'
import '@mdi/font/css/materialdesignicons.css'

const vuetify = createVuetify({
  components,
  directives,
  theme: {
    defaultTheme: 'light' 
  }
})

import { usePage } from '@inertiajs/vue3'; 

createInertiaApp({
    title: (title) => `${title} InvenTrack`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });

        router.on('finish', () => {
            const flash = usePage().props.flash;
            
            if (flash && flash.success) {
                notify(flash.success, 'success');
                flash.success = null; 
            }
            
            if (flash && flash.error) {
                notify(flash.error, 'error');
                flash.error = null;
            }
        });

        return app
            .use(plugin)
            .use(ZiggyVue)
            .use(vuetify) 
            .mount(el);
    },
});