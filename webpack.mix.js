let mix = require('laravel-mix');

mix
  .setPublicPath('dist')
  .js('resources/page-manager-tool/page-manager-tool.js', 'js')
  .js('resources/locale-parent-field/locale-parent-field.js', 'js')
  .js('resources/locale-field/locale-field.js', 'js')
  .js('resources/template-field/template-field.js', 'js')
  .js('resources/parent-field/parent-field.js', 'js')
  .js('resources/region-field/region-field.js', 'js');
