import { PhaseConfiguration } from "phase";
import VueRouterAutoloadPlugin from "./webpack-plugin";
import { artisan } from "./utils";
// require("laravel-mix")
// console.log('skjdhfksjhdf')
const PluginName = 'phase'

export default (mix: any) => {
  class PhaseMixPlugin {
    private options: PhaseConfiguration;

    /**
     * Return the name of the plugin
     */
    name(): string {
      return PluginName;
    }

    /**
     * Required Dependencies
     */
    dependencies(): string[] {
      return ["@vuexcellent/vuex"];
    }

    /**
     * Register the phase component plugin.
     *
     * @param  {PhaseConfiguration} options
     *
     * @return {void}
     */
    register(options: PhaseConfiguration = { redirects: [], assets: { js: [], sass: [] } }): void {
      // get the phase:routes JSON output
      const output = artisan("phase:routes --json --config");

      output.config.assets.js.forEach(script => {
        // mix.js(`resources/${script}`, "public/js");
      });

      output.config.assets.sass.forEach(style => {
        // mix.sass(`resources/${style}`, "public/css");
      });

      this.options = options;
    }

    /*
     * Plugins to be merged with the master webpack config.
     *
     * @return {VueRouterAutoloadPlugin}
     */
    webpackPlugins(): VueRouterAutoloadPlugin {
      return new VueRouterAutoloadPlugin(this.options);
    }
  }
// console.log(global.Mix)
  // mix.extend(PluginName, new PhaseMixPlugin());
}
// console.log(Mix)
