import { createImport } from "./createImport";
import { createRoute } from "./createRoute";
import { codeGen } from "./codeGen";

import { Route, PhaseConfiguration } from "@phased/routing";

export function createRoutes(
  routes: Route[],
  config: PhaseConfiguration,
  dynamic: boolean = false,
  chunkNamePrefix: string = "pages"
) {
  // get partial import
  const preparedImport = createImport(dynamic, chunkNamePrefix);

  // generate import headers
  const imports = routes.map(preparedImport).join("\n");

  // get partial routes
  const configRoute = createRoute();

  // generate routes
  const code = routes.map(configRoute).join(",");

  return codeGen(imports, code, config);
}
