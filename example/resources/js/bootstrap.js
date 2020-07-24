import Vue from 'vue';

if (typeof window !== 'undefined') {
    window.axios = require('axios')
    window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'
}

const files = require.context('./components/Icons', true, /\.vue$/i)
files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))
