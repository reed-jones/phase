declare module 'phase' {
  export interface PHPRoute {
    name: string;
    uri: string;
    prefix: string;
    middleware: string;
  }

  export interface Route {
    name: string;
    uri: string;
    prefix: string;
    middleware: string;
    componentName: string;
    file_path: string;
    children?: Route[] | null;
  }

  interface PhaseAssets {
    js: string[],
    sass: string[]
  }

  export interface PhasePhpOptions {
    config: PHPConfiguration;
    routes: PHPRoute[];
  }

  export interface PHPConfiguration {
    entry: string, //'phase::app' unused in js
    redirects?: object,
    assets: PhaseAssets,
  }
  export interface JSConfiguration {
    redirects?: object, // { 404: 'Errors/PageNotFound' }
    output?: string, // path to routes.js
    phpConfig?: PhasePhpOptions, //
  }

  export interface PhaseConfiguration extends JSConfiguration, PHPConfiguration { }
}
