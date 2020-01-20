import { JSConfiguration } from "phase";
import VueRouterAutoloadPlugin from "./webpack-plugin";
import { artisan } from "./utils";
import mix from "laravel-mix";
import path from 'path'

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
    const { resourceDir = 'resources', publicDir = 'public', js:scripts, sass:styles } = options.phpConfig.config.assets

    scripts.forEach(script => {
      const out = path.basename(script, path.extname(script))
      mix.js(`${resourceDir}/${script}`, path.resolve(Mix.paths.root(), `${publicDir}/js/${out}.js`));
    });

    styles.forEach(style => {
      const out = path.basename(style, path.extname(style))
      mix.sass(`${resourceDir}/${style}`, path.resolve(Mix.paths.root(), `${publicDir}/css/${out}.css`));
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
