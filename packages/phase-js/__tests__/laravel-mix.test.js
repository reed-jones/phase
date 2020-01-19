require("laravel-mix");
Mix.paths.setRootPath(path.resolve(__dirname, "./config")); // oh boy, globals

// kick it off by starting webpack
const webpack = require("webpack");
const options = require("laravel-mix/setup/webpack.config");
options.output.path = path.resolve(__dirname, "./config");

let files;
beforeAll(done => {
  webpack(options, (err, stats) => {
    if (err || stats.hasErrors()) {
      throw "Failed to initialize webpack";
    }

    // // 3. Map asset objects to output filenames
    files = stats.toJson().assets.map(x => x.name);

    done();
  });
});
describe("It compiles with laravel-mix properly", () => {
  it("Compiles routes nested at one level", () => {
    expect(files.indexOf("/public/js/app.js")).not.toBe(-1);
    expect(files.indexOf("public/css/app.css")).not.toBe(-1);
  });

  it("can import the outputted routes file", done => {
    import("./config/routes.js").then(routes => {
      expect(routes).toEqual({ default: [] });
      done();
    });
  });
});
