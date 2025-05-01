<?php

/**
 * Assorted functions/hooks related to the TinyMCE editor
 *
 * @package Bluetown
 *
 * @author Fhoke <hello@fhoke.com>
 */

use Fhoke\Slate\Utilities;

/**
 * Load custom CSS in classic editor
 */
function bto_admin_editor_css()
{
    $css_files = [
        'editor' => [
            'url'          => BTO_CSS_URL . '/tinymce.min.css',
            'cache_string' => Utilities::fileLastUpdateTimestamp(BTO_CSS_PATH . '/tinymce.min.css'),
        ],
    ];

    foreach ($css_files as $css_file) {
        add_editor_style("{$css_file['url']}?ver={$css_file['cache_string']}");
    }
}

add_action('after_setup_theme', 'bto_admin_editor_css');

/**
 * Enqueue TinyMCE plugin script
 *
 * @param array $plugin_array
 *
 * @return array
 *
 * @see https://codex.wordpress.org/Plugin_API/Filter_Reference/mce_external_plugins
 */
function bto_tinymce_enqueue_js(array $plugin_array)
{
    $plugin_array['bto_toolbar_buttons'] = BTO_JS_URL . '/tinymce.min.js';

    return $plugin_array;
}

add_filter('mce_external_plugins', 'bto_tinymce_enqueue_js');

/**
 * Add custom buttons to TinyMCE
 *
 * @param array $buttons
 *
 * @return array
 *
 * @see https://developer.wordpress.org/reference/hooks/mce_buttons/
 */
function bto_tinymce_buttons(array $buttons)
{
    array_push($buttons, 'bto_shortcodes');

    return $buttons;
}

add_filter('mce_buttons', 'bto_tinymce_buttons');

/**
 * Add formatting options
 *
 * @param array $init_array
 *
 * @return array
 *
 * @see https://codex.wordpress.org/Plugin_API/Filter_Reference/tiny_mce_before_init
 */
function bto_tinymce_style_formats(array $init_array)
{
    $style_formats = [
        [
            'title'   => __('Heading 1', 'bluetown'),
            'inline'  => 'span',
            'classes' => 'h1',
            'wrapper' => false,
        ],
        [
            'title'   => __('Heading 2', 'bluetown'),
            'inline'  => 'span',
            'classes' => 'h2',
            'wrapper' => false,
        ],
        [
            'title'   => __('Heading 3', 'bluetown'),
            'inline'  => 'span',
            'classes' => 'h3',
            'wrapper' => false,
        ],
        [
            'title'   => __('Heading 4', 'bluetown'),
            'inline'  => 'span',
            'classes' => 'h4',
            'wrapper' => false,
        ],
        [
            'title'   => __('Heading 5', 'bluetown'),
            'inline'  => 'span',
            'classes' => 'h5',
            'wrapper' => false,
        ],
        [
            'title'   => __('Heading 6', 'bluetown'),
            'inline'  => 'span',
            'classes' => 'h6',
            'wrapper' => false,
        ],
        [
            'title'   => __('Subtitle', 'bluetown'),
            'block'   => 'p',
            'classes' => 'subtitle',
            'wrapper' => false,
        ],
        [
            'title'   => __('Large', 'bluetown'),
            'block'   => 'p',
            'classes' => 'txt-large',
            'wrapper' => false,
        ],
        [
            'title'   => __('Small', 'bluetown'),
            'block'   => 'p',
            'classes' => 'txt-small',
            'wrapper' => false,
        ],
        [
            'title'   => __('Highlight', 'bluetown'),
            'inline'  => 'span',
            'classes' => 'txt-highlight',
            'wrapper' => false,
        ],
    ];

    $init_array['style_formats'] = json_encode($style_formats);

    return $init_array;
}

add_filter('tiny_mce_before_init', 'bto_tinymce_style_formats');

/**
 * Set defaults for tinyMCE
 *
 * @param array $init_array
 *
 * @return array
 */
function bto_tinymce_defaults(array $init_array)
{
    $init_array['wordpress_adv_hidden'] = false;

    return $init_array;
}

add_filter('tiny_mce_before_init', 'bto_tinymce_defaults');

/**
 * Add "Formats" dropdown to toolbar
 *
 * @param array $buttons
 *
 * @return array
 *
 * @see https://developer.wordpress.org/reference/hooks/mce_buttons_2/
 */
function bto_tinymce_display_formats(array $buttons)
{
    array_unshift($buttons, 'styleselect');

    return $buttons;
}

add_filter('mce_buttons_2', 'bto_tinymce_display_formats');
