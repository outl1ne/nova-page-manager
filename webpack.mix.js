let mix = require('laravel-mix');

mix
  .setPublicPath('dist')
  .js('resources/page-manager-tool/page-manager-tool.js', 'js')
  .js('resources/template-field/template-field.js', 'js')
  .js('resources/parent-field/parent-field.js', 'js')
  .js('resources/region-field/region-field.js', 'js')
  .js('resources/published-field/published-field.js', 'js')
  .js('resources/prefix-field/prefix-field.js', 'js')
  .js('resources/draft-button/draft-button.js', 'js');
