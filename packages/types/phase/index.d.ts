declare module 'phase' {
  interface PHPRoute {
    name: string;
    uri: string;
    prefix: string;
    middleware: string;
  }

  interface Route {
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

  interface PhasePhpOptions {
    config: PHPConfiguration;
    routes: PHPRoute[];
  }

  interface PHPConfiguration {
    entry: string, //'phase::app' unused in js
    redirects?: object,
    assets: PhaseAssets,
  }
  interface JSConfiguration {
    redirects?: object, // { 404: 'Errors/PageNotFound' }
    output?: string, // path to routes.js
    phpConfig?: PhasePhpOptions, //
  }

  interface PhaseConfiguration extends JSConfiguration, PHPConfiguration { }
}
