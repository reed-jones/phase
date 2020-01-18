import { execSync } from "child_process";
import * as fs from "fs";
import { outputFileSync } from "fs-extra";
import { componentSkeleton } from "./code-gen/componentSkeleton";
import { createRoutes } from "./code-gen/createRoutes";
import merge from "lodash.merge";
import { Route, PHPRoute, PhasePhpOptions, JSConfiguration, PhaseConfiguration, PHPConfiguration } from "phase";

export const fileEqualsCode = (to: string, code: string): boolean => {
  const isEqual = (file: string, code: string): boolean => {
    return fs.readFileSync(file, "utf8").trim() === code.trim();
  };
  return fs.existsSync(to) && isEqual(to, code);
};

export const writeCodeToFile = (to: string, code: string): void => {
  if (fileEqualsCode(to, code)) {
    return;
  }

  return fs.writeFileSync(to, code);
};

export const generateRouteFile = (route: Route): void => {
  return outputFileSync(route.file_path, componentSkeleton(route));
};

export const artisan = (cmd: string, raw: boolean = false): PhasePhpOptions => {
  return raw
    ? execSync(`php artisan ${cmd}`).toString()
    : JSON.parse(execSync(`php artisan ${cmd}`).toString());
};

export const normalizeUri = (uri: string): string => {
  let normal = uri.startsWith("/") ? uri : `/${uri}`;
  return normal.replace(/{(.*?)}/g, ":$1");
};

// TODO: generate missing files in parallel. currently all run as sync
const createMissingTemplates = (routes: Route[]): void => {
  routes.filter(r => !fs.existsSync(r.file_path)).forEach(generateRouteFile);
};

const formatForVue = ({ name, uri, prefix, middleware }: PHPRoute): Route => {
  const file_path = `resources/js/pages/${name.replace(/(@|\.|\\)/g, "/")}.vue`;

  const nameArray = name.split(/(@|\.|\/)/g);
  const componentName = nameArray[nameArray.length - 1]; // get last part of name for the component name

  return {
    uri: normalizeUri(uri), // convert {user} to :user style params
    componentName,
    prefix,
    children: null, // group all components with same prefix
    middleware,
    name: name.replace("\\", "/"),
    file_path
  };
};

export function generateRoutes(options: JSConfiguration): string {
  // get the phase:routes JSON output
  const output = options.phpConfig ?? artisan("phase:routes --json --config");

  //   merge webpack supplied options with config('phase')
  const config: PhaseConfiguration = <PhaseConfiguration>merge(output.config, options);

  // format route data
  const routes = output.routes.map(formatForVue);

  // generates missing .vue page files from template
  createMissingTemplates(routes);

  return createRoutes(routes, config);
}
