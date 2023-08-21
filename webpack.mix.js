let mix = require("laravel-mix");
const webpack = require("webpack");
mix
  .js("resources/js/cp.js", "resources/dist/js")
  .vue({ version: 2 })
  .setPublicPath("resources/dist")
  .copy("resources/img", "../../../public/vendor/pinpoint-image/img/");
// .copy('resources/dist/js', '../../../public/vendor/pinpoint-image/js/')
