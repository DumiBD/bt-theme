<?php

/**
 * Custom taxonomies
 *
 * @package Bluetown
 *
 * @author Fhoke <hello@fhoke.com>
 */

/**
 * Register custom taxonomies
 */
function bto_taxonomies()
{
    //---- Category
    /* register_taxonomy('book_category', 'book', [
        'labels'  => [
            'name'                       => __('Categories', 'bluetown'),
            'singular_name'              => __('Category', 'bluetown'),
            'all_items'                  => __('All Categories', 'bluetown'),
            'edit_item'                  => __('Edit Category', 'bluetown'),
            'view_item'                  => __('View Category', 'bluetown'),
            'update_item'                => __('Update Category', 'bluetown'),
            'add_new_item'               => __('Add New Category', 'bluetown'),
            'search_items'               => __('Search Categories', 'bluetown'),
            'separate_items_with_commas' => __('Separate categories with commas', 'bluetown'),
            'add_or_remove_items'        => __('Add or remove categories', 'bluetown'),
            'choose_from_most_used'      => __('Choose from the most used categories', 'bluetown'),
            'not_found'                  => __('No categories found.', 'bluetown'),
        ],
        'rewrite' => [
            'slug'       => 'book-category',
            'with_front' => true,
        ],
        'show_admin_column' => true,
    ]); */
}

add_action('init', 'bto_taxonomies');
