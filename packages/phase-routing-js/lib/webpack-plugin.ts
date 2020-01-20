import * as path from "path";
import { writeCodeToFile, generateRoutes } from "./utils";
import { JSConfiguration } from "phase";
import { Compiler } from "webpack";

const pluginName = "VueRouterAutoloadPlugin";

export default class VueRouterAutoloadPlugin {
  private options: JSConfiguration;

  constructor(options: JSConfiguration) {
    this.options = options;
  }

  apply(compiler: Compiler) {
    const generate = () => {
      const to =
        this.options?.output ?? path.resolve(__dirname, "../routes.js");
      const code = generateRoutes(this.options);
      writeCodeToFile(to, code);
    };

    compiler.hooks.run.tap(pluginName, generate);
    compiler.hooks.watchRun.tap(pluginName, generate);
  }
}
