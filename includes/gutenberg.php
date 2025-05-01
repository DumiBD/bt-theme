<?php

/**
 * Assorted functions/hooks related the block editor
 *
 * @package Bluetown
 *
 * @author Fhoke <hello@fhoke.com>
 */

use Fhoke\Slate\Utilities;

/**
 * Array of post types Gutenberg is supported on
 *
 * @return array
 */
function bto_gutenberg_post_types()
{
    return ['post'];
}

/**
 * Show block editor if post type is allowed
 *
 * @return bool
 */
function bto_gutenberg_enable(bool $show_block_editor, string $post_type)
{
    if (in_array($post_type, bto_gutenberg_post_types())) {
        return true;
    }

    return false;
}

add_filter('gutenberg_can_edit_post_type', 'bto_gutenberg_enable', 10, 2);
add_filter('use_block_editor_for_post_type', 'bto_gutenberg_enable', 10, 2);

/**
 * Load CSS and JS into Gutenberg editor
 */
function bto_gutenberg_enqueue_assets()
{
    wp_enqueue_style(
        'bluetown-gutenberg',
        BTO_CSS_URL . '/gutenberg.min.css',
        [],
        Utilities::fileLastUpdateTimestamp(BTO_CSS_PATH . '/gutenberg.min.css')
    );

    wp_enqueue_script(
        'bluetown-gutenberg',
        BTO_JS_URL . '/gutenberg.min.js',
        ['jquery'],
        Utilities::fileLastUpdateTimestamp(BTO_JS_PATH . '/gutenberg.min.js')
    );
}

add_action('enqueue_block_editor_assets', 'bto_gutenberg_enqueue_assets');
