import Vue from 'vue'
import VueRouter from 'vue-router'
import PhaseRoutes from '@phased/phase/routes'

Vue.use(VueRouter)

export const router = new VueRouter({
    mode: 'history',
    routes: PhaseRoutes
})
