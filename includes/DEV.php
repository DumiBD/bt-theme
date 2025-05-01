<?php

/**
 * Assorted development code
 * ! This file is for testing purposes during development only.
 * ! It will only work on *.local domains.
 *
 * @package Bluetown
 *
 * @author Fhoke <hello@fhoke.com>
 */

if (BTO_ENV !== 'development') {
    return;
}

/**
 * Print code to the page
 *
 * @param mixed $var
 */
function bto_dev_print($var, bool $exit = false)
{
    echo '<pre>';
    print_r($var);
    echo '</pre>';

    if ($exit === true) {
        exit;
    }
}

/**
 * Show meta on post type(s)
 *
 * @param int|string $post_id
 */
function bto_dev_display_meta_boxes($post_id)
{
    add_meta_box(
        'display_dev_meta',
        'Meta',
        static function () use ($post_id) {
            bto_dev_print(get_post_custom($post_id));
        },
        ['page', 'wpbb_job', 'job_application'],
        'normal',
        'low'
    );
}

add_action('add_meta_boxes', 'bto_dev_display_meta_boxes');
