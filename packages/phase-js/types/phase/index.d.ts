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
    children: Array<Route>;
  }

  interface PhaseAssets {
    js: string[],
    sass: string[]
  }

  export interface PhaseConfiguration {
    redirects: object,
    output?: string,
    assets: PhaseAssets
  }

  export interface PhaseOptions {
    config: PhaseConfiguration;
    routes: PHPRoute[];
  }
}
