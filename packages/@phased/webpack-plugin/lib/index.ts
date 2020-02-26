import * as path from "path";
import { writeCodeToFile } from "./utils";
import { JSConfiguration, generateRoutes } from "@phased/routing";
import { Compiler } from "webpack";

const pluginName = "VueRouterAutoloadPlugin";

export default class VueRouterAutoloadPlugin {
  private options: JSConfiguration;

  constructor(options: JSConfiguration) {
    this.options = options;
  }

  apply(compiler: Compiler) {
    const generate = () => {
      const to = this.options?.output ?? path.resolve(__dirname, "../routes.js");
      const code = generateRoutes(this.options);
      writeCodeToFile(to, code);
    };

    // Run Route Generation
    compiler.hooks.run.tap(pluginName, generate);
    compiler.hooks.watchRun.tap(pluginName, generate);

    // Recompile for web.php changes
    compiler.hooks.afterCompile.tap(pluginName, compilation => {
      const file = path.resolve('./routes/web.php')

      if (Array.isArray(compilation.fileDependencies)) {
        compilation.fileDependencies.push(file)
      } else {
        compilation.fileDependencies.add(file)
      }
    })
  }
}
