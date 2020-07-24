import Vue from 'vue'
import Vuex, { Store } from 'vuex'
import { hydrate } from '@phased/state'
Vue.use(Vuex)


export const store = new Store(hydrate({
    state: {
        count: 0
    },
    mutations: {
        increment (state) {
            state.count++
        }
    }
}))
