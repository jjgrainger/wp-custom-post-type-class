<?php

// include the custom post type class
include_once('CPT.php');


// create a book custom post type
$books = new CPT('book');


// create a genre taxonomy
$books->register_taxonomy('genre');


// define the columns to appear on the admin edit screen
$books->columns(array(
    'cb' => '<input type="checkbox" />',
    'title' => __('Title'),
    'genre' => __('Genres'),
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


// use "pages" icon for post type
$books->menu_icon("dashicons-book-alt");
