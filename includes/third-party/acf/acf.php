<?php

/**
 * Assorted functions/hooks related to ACF
 *
 * @package Bluetown
 *
 * @author Fhoke <hello@fhoke.com>
 *
 * @see https://www.advancedcustomfields.com/
 */

use Fhoke\Slate\ACF\ACF;
use Fhoke\Slate\Utilities;

include BTO_THIRD_PARTY . '/acf/blocks.php';

/**
 * Add a theme options page
 */
if (function_exists('acf_add_options_page')) {
    acf_add_options_page([
        'page_title' => __('Theme Settings', 'bluetown'),
        'menu_title' => __('Theme Settings', 'bluetown'),
        'menu_slug'  => 'theme-settings',
        'capability' => 'manage_options',
        'post_id'    => 'theme_settings',
    ]);
}

/**
 * Add link to theme options page in admin toolbar
 */
function bto_acf_toolbar_links(object $wp_admin_bar)
{
    if (current_user_can('manage_options') && function_exists('get_field')) {
        $wp_admin_bar->add_node([
            'id'    => 'theme_settings',
            'title' => __('Theme Settings', 'bluetown'),
            'href'  => get_admin_url() . 'admin.php?page=theme-settings',
            'meta'  => ['class' => 'theme-settings'],
        ]);
    }
}

add_action('admin_bar_menu', 'bto_acf_toolbar_links', 50);

/**
 * Only show ACF admin pages to certain users
 */
function bto_acf_hide_settings_menu()
{
    $current_user  = wp_get_current_user();
    $allowed_users = [
        'sebkay',
        'Grant',
        'fhoke',
    ];

    if (is_admin() && ! in_array($current_user->user_login, $allowed_users)) {
        add_filter('acf/settings/show_admin', '__return_false');
    }
}

add_action('init', 'bto_acf_hide_settings_menu');

/**
 * Save custom fields to JSON files
 *
 * @return string
 */
function bto_acf_json_save_point(string $path)
{
    return BTO_THIRD_PARTY . '/acf/json/';
}

add_filter('acf/settings/save_json', 'bto_acf_json_save_point');

/**
 * Load custom fields from JSON files
 *
 * @param array $paths
 *
 * @return array
 */
function bto_acf_json_load_point(array $paths)
{
    unset($paths[0]);

    $paths[] = BTO_THIRD_PARTY . '/acf/json/';

    return $paths;
}

add_filter('acf/settings/load_json', 'bto_acf_json_load_point');

/**
 * Order fields that use a WP_Query by date in admin area
 *
 * @param array $args
 * @param array $field
 * @param int $post_id
 *
 * @return array
 */
function bto_acf_query_order(array $args, array $field, $post_id)
{
    $args['orderby'] = 'date';
    $args['order']   = 'DESC';

    return $args;
}

add_filter('acf/fields/post_object/query', 'bto_acf_query_order', 10, 3);
add_filter('acf/fields/relationship/query', 'bto_acf_query_order', 10, 3);

/**
 * Add Goole Maps API key to any Google Maps fields in admin area
 */
function bto_acf_google_api_key()
{
    acf_update_setting('google_api_key', ACF::settingsField('google_api_key'));
}

add_action('acf/init', 'bto_acf_google_api_key');

/**
 * Only show certain fields to certain users in admin area
 *
 * @param array $field
 *
 * @return array|null
 */
function bto_acf_show_fields_for_users(array $field)
{
    $current_user  = wp_get_current_user();
    $allowed_users = [
        'sebkay',
        'Grant',
        'fhoke',
    ];

    if (is_admin() && in_array($current_user->user_login, $allowed_users)) {
        return $field;
    }

    return false;
}

add_action('acf/prepare_field/name=fhoke_attribution_enabled', 'bto_acf_show_fields_for_users');

/**
 * Display the recommended image size in the CMS
 *
 * @param array $field
 */
function bto_acf_display_recommended_image_size(array $field)
{
    if ($field['mime_types']) {
        echo '<p class="description">' . sprintf(__('File type: %s.', 'bluetown'), $field['mime_types']) . '</p>';
    }

    if (strpos($field['mime_types'], 'svg') !== false) {
        return;
    }

    echo bto_recommended_image_size($field['preview_size']);
}

add_action('acf/render_field/type=image', 'bto_acf_display_recommended_image_size');
add_action('acf/render_field/type=gallery', 'bto_acf_display_recommended_image_size');

/**
 * Specify fields you want Yoast SEO to read like normal content
 * * Leave commented out to include all fields
 */
/*add_filter('ysacf_exclude_fields', function(){
    return array(
        'FIELD'
    );
});*/

/**
 * Replace markdown syntax in acf field instructions
 *
 * @param array $field
 * @return array
 */
function bto_acf_parse_markdown($field)
{
    $instructions = & $field['instructions'] ?? '';
    $message      = & $field['message'] ?? '';

    if ($instructions) {
        $instructions = Utilities::parseMarkdown($instructions);
    }

    if ($message) {
        $message = Utilities::parseMarkdown($message);
    }

    return $field;
}

add_filter('acf/prepare_field', 'bto_acf_parse_markdown');

/**
 * Make certain text fields readonly
 *
 * @param array $field
 * @return array
 */
function bto_acf_readonly_fields($field)
{
    if (get_post_type() == 'job_application') {
        $field['disabled'] = true;
    }

    return $field;
}

add_filter('acf/load_field/name=user_id', 'bto_acf_readonly_fields');
add_filter('acf/load_field/name=job_id', 'bto_acf_readonly_fields');
add_filter('acf/load_field/name=first_name', 'bto_acf_readonly_fields');
add_filter('acf/load_field/name=last_name', 'bto_acf_readonly_fields');
add_filter('acf/load_field/name=email', 'bto_acf_readonly_fields');
add_filter('acf/load_field/name=phone', 'bto_acf_readonly_fields');
add_filter('acf/load_field/name=cover_message', 'bto_acf_readonly_fields');
add_filter('acf/load_field/name=linkedin_url', 'bto_acf_readonly_fields');
add_filter('acf/load_field/name=cv_url', 'bto_acf_readonly_fields');
