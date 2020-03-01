const mix = require("laravel-mix");
const path = require('path')
const tailwindcss = require("tailwindcss");
require('laravel-mix-purgecss');
require('@phased/phase')

mix
  .webpackConfig({
    // aliases '@' to the base js folder
    resolve: { alias: { "@": path.resolve(__dirname, "resources", "js") } }
  })

  // Setup TailwindCss
  .options({
    processCssUrls: false,
    postCss: [tailwindcss("./tailwind.config.js")]
  })

  // Remove unused css in production
  .purgeCss()

  // Generate Phase Routes
  .phase();
