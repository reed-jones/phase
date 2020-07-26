import Vue from 'vue';
import dayjs from 'dayjs'
Vue.prototype.dayjs = Vue.dayjs = dayjs
globalThis.__BROWSER__ = typeof window !== 'undefined'
globalThis.axios = require('axios')
globalThis.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'

const files = require.context('./components/Icons', true, /\.vue$/i)
files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))
