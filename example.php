<?php

// include the Custom Post Type Class
include_once('CPT.php');


// creare a books custom post type
$books = new CPT('books');


// create a genres taxonomy
$books->register_taxonomy('genres');


// define the columns to appear on the admin edit screen
$books->columns(array(
    'cb' => '<input type="checkbox" />',
    'title' => __('Title'),
    'genres' => __('Genres'),
    'price' => __('Price'),
    'rating' => __('Rating'),
    'date' => __('Date')
));


// populate the price column
$books->populate_column('price', function($column, $post) {

    echo "£" . get_field('price'); // ACF get_field() function

}); 


// populate the ratings column
$books->populate_column('rating', function($column, $post) {

    echo get_field('rating') . '/5'; // ACF get_field() function

});


// make rating and price columns sortable
$books->sortable(array(
    'price' => array('price', true),
    'rating' => array('rating', true)
));