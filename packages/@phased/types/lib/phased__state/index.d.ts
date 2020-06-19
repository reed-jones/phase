
declare module '@phased/state' {
    import { AxiosInstance } from "axios";

    global {
        interface Window {
            __PHASE_STATE__?: VuexStore;
            axios?: AxiosInstance;
        }
    }

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
        commit(mutation: string, data?: any): void,
        dispatch(action: string, data?: any): void
    }

    export interface VuexcellentOptions {
        generateMutations: boolean
        axios: AxiosInstance | null
        mutationPrefix: string
        logLevel: keyof ILogLevels
        logger: IPhaseLogger
    }

    export interface ILogLevels {
        debug: string
        info: string
        notice: string
        warning: string
        error: string
        critical: string
        alert: string
        emergency: string
    }
    export interface IExtendedLogLevels extends ILogLevels {
        raw: string
    }

    export type PhaseLogger = (...data: any) => void
    export interface IPhaseLogger extends PhaseLogger {
        raw: Console
        debug: (...data:any) => void
        info: (...data:any) => void
        notice: (...data:any) => void
        warning: (...data:any) => void
        error: (...data:any) => void
        critical: (...data:any) => void
        alert: (...data:any) => void
        emergency: (...data:any) => void
    }


    export const hydrate: (vuexState: VuexStore, options?: VuexcellentOptions) => VuexStore;
}
