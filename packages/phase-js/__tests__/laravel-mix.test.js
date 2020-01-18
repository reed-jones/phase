require("laravel-mix");
Mix.paths.setRootPath(path.resolve(__dirname, "./config")); // oh boy, globals
const webpack = require("webpack");
const options = require("laravel-mix/setup/webpack.config");
options.output.path = path.resolve(__dirname, "./config");

describe("It compiles with laravel-mix properly", () => {
  it("Compiles routes nested at one level", done => {
    expect.assertions(2);
    webpack(options, function(err, stats) {
      if (err || stats.hasErrors()) {
        throw "Failed to initialize webpack";
      }

      // // 3. Map asset objects to output filenames
      const files = stats.toJson().assets.map(x => x.name);

      expect(files.indexOf("/public/js/app.js")).not.toBe(-1);
      expect(files.indexOf("public/css/app.css")).not.toBe(-1);

      done();
    });
  });
});
