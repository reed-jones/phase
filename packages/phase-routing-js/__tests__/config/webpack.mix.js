const mix = require("laravel-mix");
require("../..").PhaseMixPlugin;

mix.phase({
  // The following inputs are only needed for testing
  output: path.resolve(__dirname, "./routes.js"),

  phpConfig: {
    routes: [
      // Disabled For now. In testing, the generated output is placed in the wrong location
      // {
      //   name: 'PageController@HomePage',
      //   uri: '/',
      //   prefix: '',
      //   middleware: 'web'
      // }
    ],
    config: {
      redirects: {
        400: "Errors/PageNotFound"
      },
      assets: {
        resourceDir: '__tests__/config',
        publicDir: 'public',
        js: ["src/app.js"],
        sass: ["src/app.scss"]
      }
    }
  }
});
