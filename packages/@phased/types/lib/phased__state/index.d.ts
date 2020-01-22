
declare module '@phased/state' {
    import { AxiosInstance } from "axios";

    export interface VuexModule {
        namespaced?: boolean,
        state?: object | Function,
        mutations?: object,
        actions?: object,
        getters?: object,
        modules?: { [key: string]: VuexModule },
    }

    export interface VuexStore extends VuexModule {
        plugins?: Function[],
        strict?: boolean,
        devtools?: boolean
    }

    export interface InitializedVuexStore {
        commit(mutation: string, data?: any): void
    }

    export interface VuexcellentOptions {
        generateMutations: boolean,
        axios: AxiosInstance | null,
        mutationPrefix: string
    }
}
