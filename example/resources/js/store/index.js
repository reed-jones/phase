import Vue from 'vue'
import Vuex, { Store } from 'vuex'
import { hydrate } from '@phased/state'
Vue.use(Vuex)

// console.log(axios)
export const store = new Store(hydrate({
    modules: {
        user: {
            namespaced: true,
            state: {
                profile: null,
                counter: null
            }
        },
        notices: {
            namespaced: true,
            state: {
                all: null
            }
        }
    }
}))
