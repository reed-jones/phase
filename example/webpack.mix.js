const mix = require('laravel-mix');
const tailwindcss = require('tailwindcss')
require("@phased/phase")

mix.phase()
    .options({
        postCss: [tailwindcss('./tailwind.config.js')]
    });
