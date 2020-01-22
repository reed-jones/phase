declare module '@phased/webpack-plugin' {
    import { JSConfiguration } from "@phased/routing";
    import { Compiler } from "webpack";
    export default class VueRouterAutoloadPlugin {
        private options;
        constructor(options: JSConfiguration);
        apply(compiler: Compiler): void;
    }

    export const fileEqualsCode: (to: string, code: string) => boolean;
    export const writeCodeToFile: (to: string, code: string) => void;

}
