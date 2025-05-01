<?php

/**
 * General theme set up
 *
 * @package Bluetown
 *
 * @author Fhoke <hello@fhoke.com>
 */

use Fhoke\Slate\ACF\ACF;
use Fhoke\Slate\Utilities;

/**
 * Assorted set up after activation
 */
function bto_theme_setup()
{
    //---- Translation Ready
    load_theme_textdomain('bluetown', BTO_THEME_PATH . '/languages');

    //---- Set content width
    $GLOBALS['content_width'] = 900;

    //---- Custom image sizes
    set_post_thumbnail_size($GLOBALS['content_width']);

    add_image_size('Banner', 2000);
    add_image_size('Banner - Image', 610, 705, true);

    add_image_size('Product Gallery', 720, 720, true);

    add_image_size('Logo', 80, 80, true);
    add_image_size('Logo @2x', 160, 160, true);

    add_image_size('Alternating Content', 610, 460, true);

    add_image_size('Card Image', 490, 290, true);
    add_image_size('Card Icon', 80, 80, true);

    add_image_size('Media + Content', 960, 540, true);

    add_image_size('Screenshot', 925, 826, true);
    add_image_size('Screenshot @2x', 1850, 1652, true);

    add_image_size('Headshot', 312, 360, true);
    add_image_size('Headshot @2x', 624, 720, true);

    add_image_size('Hexagon 2', 110, 127, true);

    add_image_size('Jump Link Icon', 26, 26, true);
    add_image_size('Jump Link Icon @2x', 52, 52, true);

    add_image_size('Pricing Plan Icon', 60, 60, true);
    add_image_size('Pricing Plan Icon @2x', 120, 120, true);

    //---- Title tag support
    add_theme_support('title-tag');

    //---- RSS links in header
    add_theme_support('automatic-feed-links');

    //---- Thumbnail Support
    add_theme_support('post-thumbnails', [
        'post',
    ]);

    //---- Enable HTML5
    add_theme_support('html5', [
        'comment-list',
        'comment-form',
        'search-form',
        'gallery',
        'caption',
    ]);

    //---- Menus
    register_nav_menus([
        'nav-1' => __('Header (Site)', 'bluetown'),
        'nav-2' => __('Header (User - Logged Out)', 'bluetown'),
        'nav-4' => __('Header (User - Logged In)', 'bluetown'),
        'nav-5' => __('Mobile Menu', 'bluetown'),
        'nav-6' => __('Mobile Menu 2', 'bluetown'),
        'nav-3' => __('Footer', 'bluetown'),
    ]);

    //---- Flush URL cache on theme activation
    add_action('after_switch_theme', 'flush_rewrite_rules');
}

add_action('after_setup_theme', 'bto_theme_setup');

/**
 * Load theme styles
 */
function bto_enqueue_css()
{
    //---- Fonts
    wp_enqueue_style(
        'google-fonts-headings',
        'https://fonts.googleapis.com/css2?family=Rubik:wght@400;500&display=swap'
    );

    wp_enqueue_style(
        'google-fonts-body',
        'https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap'
    );

    //---- Theme
    wp_enqueue_style(
        'bluetown',
        BTO_CSS_URL . '/app.min.css',
        [],
        Utilities::fileLastUpdateTimestamp(BTO_CSS_PATH . '/app.min.css')
    );

    //---- Child theme
    if (is_child_theme()) {
        wp_enqueue_style(
            'bluetown-child',
            get_stylesheet_directory_uri() . '/style.css',
            [],
            Utilities::fileLastUpdateTimestamp(get_stylesheet_directory() . '/style.css')
        );
    }

    //--- Load WP block styles on enabled post types
    if (!in_array(get_post_type(), bto_gutenberg_post_types())) {
        wp_dequeue_style('wp-block-library');
        wp_dequeue_style('wc-block-vendors-style');
        wp_dequeue_style('wc-block-style');
    }
}

add_action('wp_enqueue_scripts', 'bto_enqueue_css');

/**
 * Load theme scripts
 */
function bto_enqueue_js()
{
    //---- Theme
    wp_enqueue_script(
        'bluetown',
        BTO_JS_URL . '/app.min.js',
        ['jquery'],
        Utilities::fileLastUpdateTimestamp(BTO_JS_PATH . '/app.min.js'),
        true
    );

    //---- Maps
    wp_register_script(
        'google-maps',
        'https://maps.googleapis.com/maps/api/js?v=3.45&key=' . ACF::settingsField('google_api_key'),
        ['bluetown'],
        3.45,
        true
    );
}

add_action('wp_enqueue_scripts', 'bto_enqueue_js');

/**
 * Remove scripts and styles for emojis
 */
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');

/**
 * Defer loading of JS
 *
 * @return string
 */
function bto_defer_js(string $url, string $handle)
{
    if (is_user_logged_in()) {
        return $url;
    }

    if (strpos($url, '.js') === false) {
        return $url;
    }

    if (in_array($handle, ['jquery-core', 'wp-dom-ready', 'wp-hooks', 'wp-i18n'])) {
        return $url;
    }

    return str_replace(' src', ' defer src', $url);
}

add_filter('script_loader_tag', 'bto_defer_js', 10, 2);
