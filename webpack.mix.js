let mix = require('laravel-mix');

mix
  .setPublicPath('dist')
  .js('resources/page-manager-tool/js/page-manager-tool.js', 'js')
  .sass('resources/page-manager-tool/sass/page-manager-tool.scss', 'css')
  .js('resources/locale-parent-field/js/locale-parent-field.js', 'js')
  .sass('resources/locale-parent-field/sass/locale-parent-field.scss', 'css')
  .js('resources/locale-field/js/locale-field.js', 'js')
  .sass('resources/locale-field/sass/locale-field.scss', 'css')
  .js('resources/template-field/js/template-field.js', 'js')
  .sass('resources/template-field/sass/template-field.scss', 'css')
  .js('resources/parent-field/js/parent-field.js', 'js')
  .sass('resources/parent-field/sass/parent-field.scss', 'css');
