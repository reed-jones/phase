interface VuexModule {
    namespaced?: boolean,
    state?: object|Function,
    mutations?: object,
    actions?: object,
    getters?: object,
    modules?: { [key: string]: VuexModule },
}

interface VuexStore extends VuexModule {
    plugins?: Function[],
    strict?: boolean,
    devtools?: boolean
}

interface InitializedVuexStore {
    commit(mutation: string, data?: any): void
}

interface VuexcellentOptions {
    generateMutations: boolean,
    axios: object | null,
    mutationPrefix: string
}

// window.MyNamespace = window.MyNamespace || {};
