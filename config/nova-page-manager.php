<?php

return [

  /*
  |--------------------------------------------------------------------------
  | Table name
  |--------------------------------------------------------------------------
  |
  | Set a custom table for Nova Page Manager to store its data.
  |
  */

  'table' => 'nova_page_manager',


  /*
  |--------------------------------------------------------------------------
  | Max locales shown on index
  |--------------------------------------------------------------------------
  |
  | Sets the number of locales shown on index. If the number of locales
  | exceeds the defined count, the locales will be shown only on the detail
  | view.
  |
  */

  'max_locales_shown_on_index' => 4,


  /*
  |--------------------------------------------------------------------------
  | Overwrite the page resource with a custom implementation
  |--------------------------------------------------------------------------
  |
  | Add a custom implementation of the Page resource
  |
  */
  'page_resource' => null,


  /*
  |--------------------------------------------------------------------------
  | Overwrite the region resource with a custom implementation
  |--------------------------------------------------------------------------
  |
  | Add a custom implementation of the Region resource
  |
  */
  'region_resource' => null

];
