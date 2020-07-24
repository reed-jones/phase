import './bootstrap'
import 'tailwindcss/tailwind.css'
import Vue from 'vue'
import { store } from './store'
import { router } from './routes'
import App from './App.vue'

export default new Vue({
    store,
    router,
    render: h => h(App)
});
