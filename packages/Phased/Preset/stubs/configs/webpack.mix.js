const mix = require("laravel-mix");
const tailwindcss = require("tailwindcss");
require("@phased/phase/mix"); // generates phase/phase/routing

mix
  .webpackConfig({
    resolve: {
      // aliases '@' to the base js folder to avoid
      // annoying relative imports '../../../../SomeFile.vue'
      alias: { "@": path.resolve(__dirname, "resources", "js") }
    }
  })
  // Setup TailwindCss
  .options({
    processCssUrls: false,
    postCss: [tailwindcss("./tailwind.config.js")]
  })
  // Generate Phase Routes
  .phase();
