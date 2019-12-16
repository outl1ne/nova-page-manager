let mix = require('laravel-mix');

mix
  .setPublicPath('dist')
  .js('resources/nova-page-manager-resources', 'js');
