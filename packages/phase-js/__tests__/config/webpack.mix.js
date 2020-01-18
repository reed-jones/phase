const mix = require("laravel-mix");
require("../..").PhaseMixPlugin;

mix.phase({
  // The following inputs are only needed for testing
  output: path.resolve(__dirname, "./routes.js"),

  phpConfig: {
    routes: [],
    config: {
      redirects: { 400: "Errors/PageNotFound" },
      assets: {
        js: ["__tests__/config/src/app.js"],
        sass: ["__tests__/config/src/app.scss"]
      }
    }
  }
});
