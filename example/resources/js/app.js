import './bootstrap'
// import 'tailwindcss/tailwind.css' // Breaks SSR
import Vue from 'vue'
import { store } from './store'
import { router } from './routes'
import { layout, renderLayout } from './layouts'

export default new Vue({
    store,
    router,
    layout,
    render: renderLayout,
});
