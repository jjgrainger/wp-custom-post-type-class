<?php

/*

    Custom Post Type Class
    used to help create custom post types for Wordpress
    http://github.com/jjgrainger/wp-custom-post-type-class/

    @author     jjgrainger
    @url        http://jjgrainger.co.uk
    @version    1.2.4
    @license    http://www.opensource.org/licenses/mit-license.html  MIT License

*/

class CPT {

    /*
        @var  string  $post_type_name
        Public variable that holds the name of the post type
        assigned on __contstruct()
    */

    public $post_type_name;



    /*
        @var  string  $singular
        Public variable that holds the singular name of the post type
        This is a human friendly name, capitalized with spaces
        assigned on __contstruct()
    */

    public $singular;



    /*
        @var  string  $singular
        Public variable that holds the plural name of the post type
        This is a human friendly name, capitalized with spaces
        assigned on __contstruct()
    */

    public $plural;



    /*
        @var  string  $slug
        Public variable that holds the slug name of the post type
        This is a robot friendly name, all lowercase and uses hyphens
        assigned on __contstruct()
    */

    public $slug;



    /*
        @var  array  $options
        public variable that holds the user submited options of the post type
        assigined on __construct()
    */

    public $options;



    /*
        @var  array  $taxonomies
        public variable that holds an array of the taxonomies associated with the post type
        assigined on register_taxonomy()
    */

    public $taxonomies;


    /*
        @var  array  $taxonomy_settings
        public variable that holds an array of the taxonomies associated with the post type and their options
        used when registering the taxonomies
        assigined on register_taxonomy()
    */

    public $taxonomy_settings;



    /*
        @var  array  $filters
        use to define which filters are to appear on admin edit screen
        used in add_taxonmy_filters()
    */

    public $filters;



    /*
        @var  array  $columns
        use to define which columns are to appear on admin edit screen
        used in add_admin_columns()
    */

    public $columns;



    /*
        @var  array  $custom_populate_columns
        an array of user defined functions to populate admin columns
    */

    public $custom_populate_columns;



    /*
        @var  array  $sortable
        use to define which columns are to sortable on admin edit screen
    */

    public $sortable;



    /*
        @function __contructor(@post_type_name, @options)

        @param  mixed   $post_type_names    The name(s) of the post type, accepts (post type name, slug, plural, singluar)
        @param  array   $options            User submitted options
    */

    function __construct($post_type_names, $options = array()) {

        // check if post type names is string or array
        if(is_array($post_type_names)) {

            // add names to object
            $names = array(
                'singular',
                'plural',
                'slug'
            );

            // set the post type name
            $this->post_type_name = $post_type_names['post_type_name'];

            // cycle through possible names
            foreach($names as $name) {

                // if the name has been set by user
                if(isset($post_type_names[$name])) {

                    // use the user setting
                    $this->$name = $post_type_names[$name];

                // else generate the name
                } else {

                    // define the method to be used
                    $method = 'get_'.$name;

                    // generate the name
                    $this->$name = $this->$method();

                }

            }

        // else the post type name is only supplied
        } else {

            // apply to post type name
            $this->post_type_name = $post_type_names;

            // set the slug name
            $this->slug = $this->get_slug();

            // set the plural name label
            $this->plural = $this->get_plural();

            // set the singular name label
            $this->singular = $this->get_singular();

        }

        // set the user submitted options to the object
        $this->options = $options;

        // register the post type
        $this->add_action('init', array(&$this, 'register_post_type'));

        // register taxonomies
        $this->add_action('init', array(&$this, 'register_taxonomies'));

        // add taxonomy to admin edit columns
        $this->add_filter('manage_edit-' . $this->post_type_name . '_columns', array(&$this, 'add_admin_columns'));

        // populate the taxonomy columns with the posts terms
        $this->add_action('manage_' . $this->post_type_name . '_posts_custom_column', array(&$this, 'populate_admin_columns'), 10, 2);

        // add filter select option to admin edit
        $this->add_action('restrict_manage_posts', array(&$this, 'add_taxonomy_filters'));

    }



    /*
        helper function get
        used to get an object variable

        @param  string  $var        the variable you would like to retrieve
        @return mixed               returns the value on sucess, bool (false) when fails

    */

    function get($var) {

        // if the variable exisits
        if($this->$var) {

            // on success return the value
            return $this->$var;

        } else {

            // on fail return false
            return false;

        }
    }



    /*
        helper function set
        used to set an object variable
        can overwrite exisiting variables and create new ones
        cannot overwrite reserved variables

        @param  mixed  $var         the variable you would like to create/overwrite
        @param  mixed  $value       the value you would like to set to the variable

    */

    function set($var, $value) {

        // an array of reserved variables that cannot be overwritten
        $reserved = array(
            'config',
            'post_type_name',
            'singular',
            'plural',
            'slug',
            'options',
            'taxonomies'
        );

        // if the variable is not a reserved variable
        if(!in_array($var, $reserved)) {

            // write variable and value
            $this->$var = $value;

        }

    }



    /*
        helper function add_action
        used to create add_action wordpress filter

        see Wordpress Codex
        http://codex.wordpress.org/Function_Reference/add_action

        @param  string  $action            name of the action to hook to, e.g 'init'
        @param  string  $function          function to hook that will run on @action
        @param  int     $priority          order in which to execute the function, relation to other function hooked to this action
        @param  int     $accepted_args     the number of arguements the function accepts
    */

    function add_action($action, $function, $priority = 10, $accepted_args = 1) {

        // pass variables into Wordpress add_action function
        add_action($action, $function, $priority, $accepted_args);

    }



    /*
        helper function add_filter
        used to create add_filter wordpress filter

        see Wordpress Codex
        http://codex.wordpress.org/Function_Reference/add_filter

        @param  string  $action           name of the action to hook to, e.g 'init'
        @param  string  $function         function to hook that will run on @action
        @param  int     $priority         order in which to execute the function, relation to other function hooked to this action
        @param  int     $accepted_args    the number of arguements the function accepts
    */

    function add_filter($action, $function, $priority = 10, $accepted_args = 1) {

        // pass variables into Wordpress add_action function
        add_filter($action, $function, $priority, $accepted_args);

    }



    /*
        helper function get slug
        creates url friendly slug

        @param  string  $name           name to slugify
        @return string  $name           returns the slug
    */

    function get_slug($name = null) {

        // if no name set use the post type name
        if(!isset($name)) {

            $name = $this->post_type_name;

        }

        // name to lower case
        $name = strtolower($name);

        // replace spaces with hyphen
        $name = str_replace(" ", "-", $name);

        // replace underscore with hyphen
        $name = str_replace("_", "-", $name);

        return $name;

    }



    /*
        helper function get_plural
        returns the friendly plural name

        ucwords      capitalize words
        strtolower   makes string lowercase before capitalizing
        str_replace  replace all instances of _ to space

        @param   string  $name      the slug name you want to pluralize
        @return  string             the friendly pluralized name
    */

    function get_plural($name = null) {

        // if no name is passed the post_type_name is used
        if(!isset($name)) {

            $name = $this->post_type_name;

        }

        // return the plural name
        // add 's' to the end
        return $this->get_human_friendly($name) . 's';
    }



    /*
        helper function get_singular
        returns the friendly singular name

        ucwords      capitalize words
        strtolower   makes string lowercase before capitalizing
        str_replace  replace all instances of _ to space

        @param   string  $name      the slug name you want to unpluralize
        @return  string             the friendly singular name
    */

    function get_singular($name = null) {

        // if no name is passed the post_type_name is used
        if(!isset($name)) {

            $name = $this->post_type_name;

        }

        // return the string
        return $this->get_human_friendly($name);

    }



    /*
        helper function get_human_friendly
        returns the human friendly name

        ucwords      capitalize words
        strtolower   makes string lowercase before capitalizing
        str_replace  replace all instances of hyphens and underscores to spaces

        @param   string  $name      the name you want to make friendly
        @return  string             the human friendly name
    */

    function get_human_friendly($name = null) {

        // if no name is passed the post_type_name is used
        if(!isset($name)) {

            $name = $this->post_type_name;

        }

        // return human friendly name
        return ucwords(strtolower(str_replace("-", " ", str_replace("_", " ", $name))));

    }



    /*
        register_post_type function
        object function to register the post type

        see Wordpress Codex
        http://codex.wordpress.org/Function_Reference/register_post_type
    */

    function register_post_type() {

        // friendly post type names
        $plural   = $this->plural;
        $singular = $this->singular;
        $slug     = $this->slug;

        // default labels
        $labels = array(
            'name' => __($plural),
            'singular_name' => __($singular),
            'menu_name' => __($plural),
            'all_items' => __($plural),
            'add_new' => _('Add New'),
            'add_new_item' => __('Add New ' . $singular),
            'edit_item' => __('Edit ' . $singular),
            'new_item' => __('New ' . $singular),
            'view_item' => __('View ' . $singular),
            'search_items' => __('Search ' . $plural),
            'not_found' =>  __('No ' . $plural . ' found'),
            'not_found_in_trash' => __('No ' . $plural . ' found in Trash'),
            'parent_item_colon' => __('Parent ' . $singular . ':')
        );

        // default options
        $defaults = array(
            'labels' => $labels,
            'public' => true,
            'rewrite' => array(
                'slug' => $slug,
                )
            );

        // merge user submitted options with defaults
        $options = array_replace_recursive($defaults, $this->options);

        // set the object options as full options passed
        $this->options = $options;

        // check that the post type doesn't already exist
        if(!post_type_exists($this->post_type_name)) {

            // register the post type
            register_post_type($this->post_type_name, $options);

        }

    }



    /*
        function register_taxonomy
        register a taxonomy to a post type

        @param  string          $taxonomy_name      the slug for the taxonomy
        @param  array           $options            taxonomy options

        see Wordpress codex
        http://codex.wordpress.org/Function_Reference/register_taxonomy
    */

    function register_taxonomy($taxonomy_names, $options = array()) {

        // post type defaults to $this post type if unspecified
        $post_type = $this->post_type_name;

        // an array of the names required excluding taxonomy_name
        $names = array(
            'singular',
            'plural',
            'slug'
        );

        // if an array of names are passed
        if(is_array($taxonomy_names)) {

            // set the taxonomy name
            $taxonomy_name = $taxonomy_names['taxonomy_name'];

            // cycle through possible names
            foreach($names as $name) {

                // if the user has set the name
                if(isset($taxonomy_names[$name])) {

                    // use user submitted name
                    $$name = $taxonomy_names[$name];

                // else generate the name
                } else {

                    // define the fnction to be used
                    $method = 'get_'.$name;

                    // generate the name
                    $$name = $this->$method($taxonomy_name);

                }

            }

        // else if only the taxonomy_name has been supplied
        } else  {

            // create user friendly names
            $taxonomy_name = $taxonomy_names;
            $singular = $this->get_singular($taxonomy_name);
            $plural   = $this->get_plural($taxonomy_name);
            $slug     = $this->get_slug($taxonomy_name);

        }

        // default labels
        $labels = array(
            'name' => _($plural),
            'singular_name' => _($singular),
            'menu_name' => __($plural),
            'all_items' => __('All ' . $plural),
            'edit_item' => __('Edit ' . $singular),
            'view_item' => __('View ' . $singular),
            'update_item' => __('Update ' . $singular),
            'add_new_item' => __('Add New ' . $singular),
            'new_item_name' => __('New ' . $singular . ' Name'),
            'parent_item' => __('Parent ' . $plural),
            'parent_item_colon' => __('Parent ' . $plural .':'),
            'search_items' =>  __('Search ' . $plural),
            'popular_items' => __('Popular ' . $plural),
            'separate_items_with_commas' => __('Seperate ' . $plural . ' with commas'),
            'add_or_remove_items' => __('Add or remove ' . $plural),
            'choose_from_most_used' => __('Choose from most used ' . $plural),
            'not_found' => __('No ' . $plural  . ' found'),
        );

        // default options
        $defaults = array(
            'labels' => $labels,
            'hierarchical' => true,
            'rewrite' => array(
                'slug' => $slug
            )
        );

        // merge default options with user submitted options
        $options = array_replace_recursive($defaults, $options);

        // add the taxonomy to the object array
        // this is used to add columns and filters to admin pannel
        $this->taxonomies[] = $taxonomy_name;

        // create array used when registering taxonomies
        $this->taxonomy_settings[$taxonomy_name] = $options;

    }



    /*
        function register_taxonomies
        cycles through taxonomies added with the class and registers them

        function is used with add_action
    */
    function register_taxonomies() {

        if(is_array($this->taxonomy_settings)) {
            // foreach taxonomy registered with the post type
            foreach($this->taxonomy_settings as $taxonomy_name => $options) {

                // register the taxonomy if it doesn't exist
                if(!taxonomy_exists($taxonomy_name)) {

                    // register the taxonomy with Wordpress
                    register_taxonomy($taxonomy_name, $this->post_type_name, $options);


                } else {

                    // if taxonomy exists, attach exisiting taxonomy to post type
                    register_taxonomy_for_object_type($taxonomy_name, $this->post_type_name);

                }
            }
        }

    }



    /*
        function add_admin_columns
        adds columns to the admin edit screen

        function is used with add_action
    */

    function add_admin_columns($columns) {


        // if no user columns have been specified use following defaults
        if(!isset($this->columns)) {

            // default columns
            $columns = array(
                'cb' => '<input type="checkbox" />',
                'title' => __('Title')
            );

            // if there are taxonomies registered to the post type
            if(is_array($this->taxonomies)) {

                // create a column for each taxonomy
                foreach($this->taxonomies as $tax) {

                    // get the taxonomy object for labels
                    $taxonomy_object = get_taxonomy($tax);

                    // column key is the slug, value is friendly name
                    $columns[$tax] = __($taxonomy_object->labels->name);

                }

            }

            // if post type supports comments
            if(post_type_supports($this->post_type_name, 'comments')) {

                $columns['comments'] = '<img alt="Comments" src="'. site_url() .'/wp-admin/images/comment-grey-bubble.png">';

            }

            // add date of post to end of columns
            $columns['date'] = __('Date');

        } else {

            // use user submitted columns
            // these are defined using the object columns() method
            $columns = $this->columns;

        }

        return $columns;

    }



    /*
        function populate_admin_columns
        populates custom columns on the admin edit screen

        function is used with add_action
    */

    function populate_admin_columns($column, $post_id) {

        // get wordpress $post object
        global $post;

        // determine the column
        switch($column) {

            // if column is a taxonomy associated with the post type
            case (taxonomy_exists($column)) :

                // Get the taxonomy for the post
                $terms = get_the_terms($post_id, $column);

                // if we have terms
                if (!empty($terms)) {

                    $output = array();

                    // Loop through each term, linking to the 'edit posts' page for the specific term.
                    foreach($terms as $term) {

                        // output is an array of terms associated with the post
                        $output[] = sprintf(

                            // define link
                            '<a href="%s">%s</a>',

                            // create filter url
                            esc_url(add_query_arg(array('post_type' => $post->post_type, $column => $term->slug), 'edit.php')),

                            // create friendly term name
                            esc_html(sanitize_term_field('name', $term->name, $term->term_id, $column, 'display'))

                        );

                    }

                    // Join the terms, separating them with a comma
                    echo join(', ', $output);

                // if no terms found
                } else {

                    // get the taxonomy object for labels
                    $taxonomy_object = get_taxonomy($column);

                    // echo no terms
                    _e('No ' . $taxonomy_object->labels->name);

                }


            break;

            // if column is for the post ID
            case 'post_id' :

                echo $post->ID;

            break;

            // if the column is prepended with 'meta_'
            // this will automagically retrieve the meta values and display them
            case (preg_match('/^meta_/', $column) ? true : false) :

                // meta_book_author (meta key = book_author)
                $x = substr($column, 5);

                $meta = get_post_meta($post->ID, $x);

                echo join(", ", $meta);

            break;

            // if the column is post thumbnail
            case 'icon' :

                // create the edit link
                $link = esc_url(add_query_arg(array('post' => $post->ID, 'action' => 'edit'), 'post.php'));

                // if it post has a featured image
                if(has_post_thumbnail()) {

                    // display post featured image with edit link
                    echo '<a href="'. $link .'">';
                        the_post_thumbnail(array(60, 60));
                    echo '</a>';

                } else {

                    // display default media image with link
                    echo '<a href="'.$link.'"><img src="'. site_url('/wp-includes/images/crystal/default.png') .'" alt="'. $post->post_title .'" /></a>';

                }

            break;

            // default case checks if the column has a user function
            // this is most commonly used for custom fields
            default :

                // if there are user custom columns to populate
                if(isset($this->custom_populate_columns) && is_array($this->custom_populate_columns)) {

                    // if this column has a user submitted function to run
                    if(isset($this->custom_populate_columns[$column]) && is_callable($this->custom_populate_columns[$column])) {

                        // run the function
                        $this->custom_populate_columns[$column]($column, $post);

                    }

                }

            break;

        } // end switch($column)

    }



    /*
        function filters
        user function to define which taxonomy filters to display on the admin page

        @param  array  $filters         an array of taxonomy filters to display

    */

    function filters($filters = array()) {

        $this->filters = $filters;

    }




    /*
        function add_taxtonomy_filters
        creates select fields for filtering posts by taxonomies on admin edit screen

    */

    function add_taxonomy_filters() {

        global $typenow;
        global $wp_query;

        // must set this to the post type you want the filter(s) displayed on
        if($typenow == $this->post_type_name){

            // if custom filters are defined use those
            if(is_array($this->filters)) {

                $filters = $this->filters;

            // else default to use all taxonomies associated with the post
            } else {

                $filters = $this->taxonomies;

            }

            if(!empty($filters)) {

                // foreach of the taxonomies we want to create filters for
                foreach($filters as $tax_slug) {

                    // object for taxonomy, doesn't contain the terms
                    $tax = get_taxonomy($tax_slug);

                    // get taxonomy terms and order by name
                    $args = array(
                        'orderby' => 'name',
                        'hide_empty' => false
                    );

                    // get taxonomy terms
                    $terms = get_terms($tax_slug, $args);

                    // if we have terms
                    if($terms) {

                        // set up select box
                        printf(' &nbsp;<select name="%s" class="postform">', $tax_slug);

                        // default show all
                        printf('<option value="0">%s</option>', 'Show all ' . $tax->label);

                        // foreach term create an option field
                        foreach ($terms as $term) {

                            // if filtered by this term make it selected
                            if(isset($_GET[$tax_slug]) && $_GET[$tax_slug] === $term->slug) {

                                printf('<option value="%s" selected="selected">%s (%s)</option>', $term->slug, $term->name, $term->count);

                            // create option for taxonomy
                            } else {

                                printf('<option value="%s">%s (%s)</option>', $term->slug, $term->name, $term->count);

                            }

                        }

                        // end the select field
                        print('</select>&nbsp;');

                    }

                }
            }
        }

    }



    /*
        function columns
        user function to choose columns to be displayed on the admin edit screen

        @param  array  $columns         an array of columns to be displayed

    */

    function columns($columns) {

        // if columns is set
        if(isset($columns)) {

            // assign user submitted columns to object
            $this->columns = $columns;

        }

    }



    /*
        function populate_column
        user function to define what and how to populate a specific admin column

        @param  string  $column_name        the name of the column to populate
        @param  func    $function           an anonymous function to run when populating the column
    */

    function populate_column($column_name, $function) {

        $this->custom_populate_columns[$column_name] = $function;

    }



    /*
        function sortable
        user function define what columns are sortable in admin edit screen

        @param  array  $columns         an array of the columns that are sortable
    */

    function sortable($columns = array()) {

        // assign user defined sortable columns to object variable
        $this->sortable = $columns;

        // run filter to make columns sortable
        $this->add_filter('manage_edit-' . $this->post_type_name . '_sortable_columns', array(&$this, 'make_columns_sortable'));

        // run action that sorts columns on request
        $this->add_action('load-edit.php', array(&$this, 'load_edit'));

    }



    /*
        function make_columns_sortable
        internal function that adds any user defined sortable columns to wordpress default columns

    */

    function make_columns_sortable($columns) {

        // for each sortable column
        foreach($this->sortable as $column => $values) {

            // make an array to merege into wordpress sortable columns
            $sortable_columns[$column] = $values[0];

        }


        // merge sortable columns array into wordpress sortable columns
        $columns = array_merge($sortable_columns, $columns);

        return $columns;

    }



    /*
        function load_edit
        only sort columns on the edit.php page when requested

    */

    function load_edit() {

        // run filter to sort columns when requested
        $this->add_filter( 'request', array(&$this, 'sort_columns') );

    }



    /*
        function sort columns
        internal function that sorts columns on request

        run by load_edit() filter

        @param  array  $vars        the query vars submitted by user

    */

    function sort_columns($vars) {

        // cycle through all sortable columns submitted by the user
        foreach($this->sortable as $column => $values) {

            // retrieve the meta key from the user submitted array of sortable columns
            $meta_key = $values[0];

            // if the meta_key is a taxonomy
            if(taxonomy_exists($meta_key)) {

                // sort by taxonomy
                $key = "taxonomy";

            } else {

                // else by meta key
                $key = "meta_key";

            }

            // if the optional parameter is set and is set to true
            if(isset($values[1]) && true === $values[1]) {

                // vaules needed to be ordered by integer value
                $orderby = 'meta_value_num';

            } else {

                // values are to be order by string value
                $orderby = 'meta_value';

            }

            // Check if we're viewing this post type
            if (isset($vars['post_type']) && $this->post_type_name == $vars['post_type']) {

                // find the meta key we want to order posts by
                if (isset($vars['orderby']) && $meta_key == $vars['orderby']) {

                    // merge the query vars with our custom variables
                    $vars = array_merge($vars,
                        array(
                            'meta_key' => $meta_key,
                            'orderby' => $orderby
                        )
                    );
                }

            }

        }

        return $vars;
    }



    /*
        function menu icon
        used to change the menu icon in the admin dashboard
        pass name of dashicon, list found here http://melchoyce.github.io/dashicons/

        @param  mixed  $icon        a string of the name of the icon to use
    */

    function menu_icon($icon = "dashicons-admin-page") {

        // WP 3.8 changed the icon system to use an icon font.
        // http://melchoyce.github.io/dashicons/

        if(is_string($icon) && stripos($icon, "dashicons") !== FALSE) {

            $this->options["menu_icon"] = $icon;

        } else {
            // set a default
            $this->options["menu_icon"] = "dashicons-admin-page";

        }

    }

}
