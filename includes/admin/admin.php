<?php

/**
 * Assorted functions/hooks for the admin area
 *
 * @package Bluetown
 *
 * @author Fhoke <hello@fhoke.com>
 */

use Fhoke\Slate\Utilities;

/**
 * Upscale images to biggest available size
 * * This filter runs when an image is uploaded to the media library.
 *
 * @param bool|array $crop
 *
 * @return array
 */
function bto_admin_thumb_upscale(?int $default = null, int $orig_w, int $orig_h, int $dest_w, int $dest_h, $crop = null)
{
    //---- If there's no crop, let WordPress handle this
    if (! $crop) {
        return null;
    }

    //---- If there is a crop, make it the biggest size
    $size_ratio = max($dest_w / $orig_w, $dest_h / $orig_h);

    $crop_w = round($dest_w / $size_ratio);
    $crop_h = round($dest_h / $size_ratio);

    $s_x = floor(($orig_w - $crop_w) / 2);
    $s_y = floor(($orig_h - $crop_h) / 2);

    return [
        0,
        0,
        (int) $s_x,
        (int) $s_y,
        (int) $dest_w,
        (int) $dest_h,
        (int) $crop_w,
        (int) $crop_h,
    ];
}

add_filter('image_resize_dimensions', 'bto_admin_thumb_upscale', 10, 6);

/**
 * Content for "Fhoke Support" dashboard widget
 */
function bto_admin_dashboard_help()
{
    $txt = sprintf(
        __("Need help? <a href='mailto:seb@fhoke.com?subject=Fhoke support for %s' target='_blank'>Email us at Fhoke</a> with any questions and we'll get back to you ASAP!", 'bluetown'),
        wp_get_theme()
    );

    echo "<p>${txt}</p>";
}

/**
 * Add "Fhoke Support" widget to dashboard
 */
function bto_admin_dashboard_widgets()
{
    add_meta_box(
        'custom_help_widget',
        __('Fhoke Support', 'bluetown'),
        'bto_admin_dashboard_help',
        'dashboard',
        'normal',
        'high'
    );
}

add_action('wp_dashboard_setup', 'bto_admin_dashboard_widgets');

/**
 * Add Fhoke attribution to admin footer
 */
function bto_admin_footer_text()
{
    $txt = sprintf(
        __("Crafted by <a href='%s' target='_blank'>%s</a>", 'bluetown'),
        'https://fhoke.com',
        'Fhoke'
    );

    echo "<span id='footer-thankyou'>${txt}</span>";
}

add_filter('admin_footer_text', 'bto_admin_footer_text');

/**
 * Load custom CSS on every admin page
 */
function bto_admin_css()
{
    $files = [
        'admin' => [
            'url'          => BTO_CSS_URL . '/admin.min.css',
            'cache_string' => Utilities::fileLastUpdateTimestamp(BTO_CSS_PATH . '/admin.min.css'),
        ],
    ];

    foreach ($files as $file) {
        echo "<link rel='stylesheet' href='{$file['url']}?ver={$file['cache_string']}'>";
    }
}

add_action('admin_head', 'bto_admin_css');

/**
 * Add links to admin toolbar
 */
function bto_admin_toolbar_links(object $wp_admin_bar)
{
    //---- Posts
    if (current_user_can('edit_posts')) {
        $args = [
            'id'    => 'all_posts',
            'title' => __('All Posts', 'bluetown'),
            'href'  => admin_url('edit.php'),
        ];

        $wp_admin_bar->add_node($args);
    }

    //---- Pages
    if (current_user_can('edit_pages')) {
        $args = [
            'id'    => 'all_pages',
            'title' => __('All Pages', 'bluetown'),
            'href'  => add_query_arg(
                'post_type',
                'page',
                admin_url('edit.php')
            ),
        ];

        $wp_admin_bar->add_node($args);
    }
}

add_action('admin_bar_menu', 'bto_admin_toolbar_links', 300);

/**
 * Load custom CSS for admin toolbar
 * * Loads in admin area and on front-end
 */
function bto_admin_enqueue_toolbar_styles()
{
    if (! is_user_logged_in()) {
        return;
    }

    wp_enqueue_style(
        'bluetown-toolbar',
        BTO_CSS_URL . '/toolbar.min.css',
        [],
        Utilities::fileLastUpdateTimestamp(BTO_CSS_PATH . '/toolbar.min.css')
    );
}

add_action('wp_enqueue_scripts', 'bto_admin_enqueue_toolbar_styles');
add_action('admin_enqueue_scripts', 'bto_admin_enqueue_toolbar_styles');

/**
 * Load custom CSS for admin login page
 */
function bto_admin_login_css()
{
    $files = [
        'login' => [
            'url'          => BTO_CSS_URL . '/admin-login.min.css',
            'cache_string' => Utilities::fileLastUpdateTimestamp(BTO_CSS_PATH . '/admin-login.min.css'),
        ],
    ];

    foreach ($files as $file) {
        echo "<link rel='stylesheet' href='{$file['url']}?ver={$file['cache_string']}'>";
    }
}

add_action('login_head', 'bto_admin_login_css');

/**
 * Change URL of login page logo to site URL
 *
 * @return string
 */
function bto_admin_login_link()
{
    return esc_url(home_url());
}

add_filter('login_headerurl', 'bto_admin_login_link');

/**
 * Change text in logo link to site name
 *
 * @return string
 */
function bto_admin_login_tooltip()
{
    return get_bloginfo('name');
}

add_filter('login_headertext', 'bto_admin_login_tooltip');

/**
 * Output recommended size on featured images (classic editor only)
 *
 * @return string
 */
function bto_admin_add_featured_image_text(string $content)
{
    $post = get_post();

    switch ($post->post_type) {
        case 'post':
            return $content . bto_recommended_image_size('Banner');
    }
}

// add_filter('admin_post_thumbnail_html', 'bto_admin_add_featured_image_text');

/**
 * Hide content editor when using certain page templates
 */
function bto_hide_content_editor()
{
    $post_id = $_GET['post'] ?? 0;

    if (!isset($post_id)) {
        return;
    }

    $template_file = get_post_meta($post_id, '_wp_page_template', true);

    $templates = [
        'templates/login.php',
        'templates/register.php',
        'templates/account.php',
        'templates/applied-jobs.php',
        'templates/saved-jobs.php',
    ];

    if (in_array($template_file, $templates)) {
        remove_post_type_support('page', 'editor');
    }
}

add_action('admin_init', 'bto_hide_content_editor');

/**
 * If there's a redirect query string, redirect there after user logs in
 *
 * @param string $user_login
 * @param WP_User $user
 */
function bto_maybe_redirect_after_login($user_login, $user)
{
    $redirect = $_GET['redirect_to'] ?? '';

    if (!$redirect) {
        return;
    }

    wp_redirect($redirect);
    exit;
}

add_action('wp_login', 'bto_maybe_redirect_after_login', 10, 2);
