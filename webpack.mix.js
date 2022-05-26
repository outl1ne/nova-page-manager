let mix = require('laravel-mix');
let path = require('path');
let postcss = require('postcss-import');
let tailwindcss = require('tailwindcss');

mix
  .setPublicPath('dist')
  .js('resources/js/entry.js', 'js')
  .vue({ version: 3 })
  .postCss('resources/css/entry.css', 'dist/css/', [postcss(), tailwindcss('tailwind.config.js')])
  .webpackConfig({
    externals: {
      vue: 'Vue',
    },
    output: {
      uniqueName: 'outl1ne/nova-page-manager',
    },
  })
  .alias({
    'laravel-nova': path.join(__dirname, 'vendor/laravel/nova/resources/js/mixins/packages.js'),
  });
