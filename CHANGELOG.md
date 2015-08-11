# Changelog

##### v1.4
* fix error with taxonomy arrays when generating columns
* run `custom_populate_columns` callbacks using `call_user_func_array()`
* add post updated messages
* add flush method

##### v1.3.3
* add check if exisiting_taxonomies is an array

##### v1.3.2
* fix register taxonomies exisiting taxonomies after post type is regitered
* fix `add_admin_columns` to work with 3rd party plugins
* capital P dangit

##### v1.3.1
* register taxonomies before post type to fix issues with taxonomy permalinks

##### v1.3.0
* fix translation issues
* new method to set custom textdomain with `set_textdomain()`

##### v1.2.4
* add check if `$filter` array is empty

##### v1.2.3
* add array check for `$this->taxonomy_settings`

##### v1.2.2
* fix issues when registering taxonomy across multiple post type
* remove wrapper function `options_merge`

##### v1.2.1
* reduce the defaults within the class
* replace contents of `options_merge` function with `array_replace_recursive`

##### v1.2.0
* allow taxonomies to be sorted with the `sortable()` method
* use of `.gitattributes` to make package lighter when deploying for production.

##### v1.1.0
* make repository a composer package

##### v1.0.2
* ability to use dashicons with `menu_icon()` method
* removed old custom icon functions

##### v1.0.1
* fixed issue with registering taxonomies
* fixed issue with options merge, now accepts boolean
* register_taxonomy method can now register exisiting taxonomies to post type
