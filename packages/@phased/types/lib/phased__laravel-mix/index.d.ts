declare module '@phased/laravel-mix' {
    import { JSConfiguration } from "@phased/routing";
    import VueRouterAutoloadPlugin from "@phased/webpack-plugin";
    export default class PhaseMixPlugin {
        private options;
        name(): string;
        dependencies(): string[];
        register(options?: JSConfiguration): void;
        webpackPlugins(): VueRouterAutoloadPlugin;
    }
}
