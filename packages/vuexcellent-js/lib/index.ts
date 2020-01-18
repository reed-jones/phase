interface VuexStore {
    state?: object|Function,
    mutations?: object,
    actions?: object,
    getters?: object,
    modules?: object,
    plugins?: Function[],
    strict?: boolean,
    devtools?: boolean
}

export const hydrate = (options: VuexStore) => {
    // merge incoming (store) options with window.__VUEXCELLENT_STATE__

    // generate mutations

    // inject VuexcellentInterceptorPlugin into store.plugins
}
