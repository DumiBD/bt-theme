<?php

/**
 * Extending core WordPress functionality
 *
 * @package Bluetown
 *
 * @author Fhoke <hello@fhoke.com>
 */

/**
 * Remove WordPress version from outputting in wp_head();
 */
add_filter('the_generator', '__return_false');

/**
 * Remove WordPress version from query string after JS/CSS
 * * Only removes the query string if it matches the current WordPress version
 *
 * @return string
 */
function bto_remove_wp_version_file_src(string $src)
{
    return strpos($src, 'ver=' . get_bloginfo('version')) ? remove_query_arg('ver', $src) : $src;
}

add_filter('style_loader_src', 'bto_remove_wp_version_file_src');
add_filter('script_loader_src', 'bto_remove_wp_version_file_src');

/**
 * Show post thumbnail in RSS feed
 *
 * @return string
 */
function bto_rss_content(string $content)
{
    $post_id = get_the_ID();

    if (has_post_thumbnail($post_id)) {
        $content = '<p>' . get_the_post_thumbnail($post_id) . '</p>' . $content;
    }

    return $content;
}

add_filter('the_excerpt_rss', 'bto_rss_content');
add_filter('the_content_feed', 'bto_rss_content');

/**
 * Enable shortcodes in RSS feed
 */
add_filter('the_excerpt_rss', 'do_shortcode');
add_filter('the_content_rss', 'do_shortcode');
add_filter('the_content_feed', 'do_shortcode');

/**
 * Change length of excerpts
 *
 * @param int $length
 *
 * @return int
 */
function bto_excerpt_length()
{
    return 40;
}

add_filter('excerpt_length', 'bto_excerpt_length');

/**
 * Add ellipsis at the end of an excerpt
 *
 * @param string $more
 *
 * @return string
 */
function bto_excerpt_more()
{
    return '&hellip;';
}

add_filter('excerpt_more', 'bto_excerpt_more');

/**
 * Remove wrapping <p></p> on images in the_content()
 *
 * @return string
 */
function bto_remove_p_tags(string $content)
{
    return preg_replace('/<p>\s*(<a .*>)?\s*(<img .* \/>)\s*(<\/a>)?\s*<\/p>/iU', '\1\2\3', $content);
}

add_filter('the_content', 'bto_remove_p_tags');

/**
 * Stop links autoinserting around images
 */
function bto_remove_image_auto_links()
{
    if (get_option('image_default_link_type') !== 'none') {
        update_option('image_default_link_type', 'none');
    }
}

add_action('admin_init', 'bto_remove_image_auto_links');

/**
 * Remove CSS for WordPress gallery
 */
add_filter('use_default_gallery_style', '__return_false');
