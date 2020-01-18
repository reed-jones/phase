import { JSConfiguration } from "phase";
import VueRouterAutoloadPlugin from "./webpack-plugin";
import { artisan } from "./utils";
import mix from "laravel-mix";

const PluginName = "phase";

export default class PhaseMixPlugin {
  private options: JSConfiguration = {
    phpConfig: {
      config: {
        assets: { js: [], sass: [] },
        redirects: {},
        entry: ''
      },
      routes: []
    }
  };

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
   * @param  {JSConfiguration} options
   *
   * @return {void}
   */
  register(
    options: JSConfiguration = {
      redirects: {},
      output: ""
    }
  ): void {
    if (!options.phpConfig) {
      options.phpConfig = artisan("phase:routes --json --config")
    }
    this.options = options;

    options.phpConfig.config.assets.js.forEach(script => {
      mix.js(`${script}`, path.resolve(Mix.paths.root(), "public/js/app.js"));
    });

    options.phpConfig.config.assets.sass.forEach(style => {
      mix.sass(`${style}`, path.resolve(Mix.paths.root(), "public/css/app.css"));
    });
  }

  /*
   * Plugins to be merged with the master webpack config.
   *
   * @return {VueRouterAutoloadPlugin}
   */
  webpackPlugins() {
    return new VueRouterAutoloadPlugin(this.options);
  }
}

mix.extend(PluginName, new PhaseMixPlugin());
