const mix = require("laravel-mix")
// require("..").PhaseMixPlugin(mix);

describe("It compiles with laravel-mix properly", () => {
  it("Compiles routes nested at one level", done => {
    mix.js('/asdasd', 'asdasd');
    console.log(mix)
    // mix.phase().then(_ => {
    //   expect(files.indexOf("routes.js")).not.toBe(-1);
    //   expect(files.indexOf("app.js")).not.toBe(-1);
    //   expect(files.indexOf("app.css")).not.toBe(-1);
    // });
  });
});
