import Vuex, { Store } from '@phased/phase/state'
import Vue from 'vue'

Vue.use(Vuex)

// modules
import app from './modules/app'

export default new Store({
    modules: {
        app
    }
})
