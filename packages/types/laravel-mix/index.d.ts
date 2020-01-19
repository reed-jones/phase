declare module "laravel-mix" {
  export function js(src: string | string[], output: string): void;
  export function sass(src: string, output: string, options?: object): void;
  export function less(src: string, output: string, options?: object): void;
  export function stylus(src: string, output: string, options?: object): void;
  export function postCss(src: string, output: string, options?: object): void;

  export function extend(extension: string, callback: any): void;
}

declare const path: {
  resolve(src: string, dest: string): string;
};
declare const Mix: {
  paths: { root(append?: string): string };
};
