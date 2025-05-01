<?php

/**
 * Assorted helper functions/hooks
 *
 * @package Bluetown
 *
 * @author Fhoke <hello@fhoke.com>
 */

use Fhoke\Bluetown\Banner;
use Fhoke\Bluetown\Validator;
use Fhoke\Slate\ACF\ACF;
use Fhoke\Slate\Utilities;

/**
 * Get field for core file
 *
 * @param string $key
 * @param array $args
 * @param bool $format
 * @param bool $get_field
 *
 * @return mixed
 */
function bto_template_part_field(string $key, array $args = [], bool $format = true, bool $get_field = true)
{
    if ($get_field) {
        return $args['fields'][$key] ?? ACF::subField($key, $format);
    }

    return $args['fields'][$key] ?? $key;
}

/**
 * Get core template file
 *
 * @param array $args
 */
function bto_template_part(array $args = [])
{
    //---- Arguments
    $args = [
        'name'        => $args['name'] ?? '',
        'fields'      => $args['fields'] ?? [],
        'css_id'      => $args['css_id'] ?? '',
        'css_classes' => isset($args['css_classes']) && is_array($args['css_classes']) ? $args['css_classes'] : [],
        'echo'        => $args['echo'] ?? true,
    ];

    if (bto_template_part_field('disable_section', $args)) {
        return;
    }

    $args['css_classes'] = implode(' ', $args['css_classes']);

    $file_path = BTO_THEME_PARTS . "/{$args['name']}.php";

    //---- Get file
    if (! file_exists($file_path)) {
        return;
    }

    if ($args['echo'] != true) {
        ob_start();
        include $file_path;
        return ob_get_clean();
    }

    include $file_path;
}

/**
 * Include core sections
 *
 * @param string $field_name
 * @param string $post_id
 */
function bto_core_sections($field_name = 'core', $post_id = null)
{
    $core_field    = ACF::field($field_name, $post_id);
    $core_sections = ($core_field ? wp_list_pluck($core_field, 'acf_fc_layout') : false);

    if (ACF::haveRows($field_name, $post_id) && $core_sections) {
        while (ACF::haveRows($field_name, $post_id)) {
            ACF::theRow();

            $core_section_num  = get_row_index();
            $core_section_name = get_row_layout();

            if (in_array($core_section_name, $core_sections)) {
                //---- Replace underscores with hyphens to get proper file name
                $core_section_file_name = str_replace('_', '-', $core_section_name);

                //---- Directory for file
                $core_section_file_dir = 'blocks';

                //---- Classes
                $classes = ['pv-large'];

                switch ($core_section_name) {
                    case 'NAME':
                        $classes[] = 'CLASS';
                        break;
                    default:
                        $classes[] = 'section-bg--white';
                        break;
                }

                if ($core_section_name == 'call_to_action') {
                    $classes = [];
                }

                //---- Include file
                bto_template_part([
                    'name'        => "{$core_section_file_dir}/{$core_section_file_name}",
                    'fields'      => [],
                    'css_id'      => "section-{$core_section_num}",
                    'css_classes' => $classes,
                ]);
            }
        }
    }
}

/**
 * HTML for social icons
 *
 * @param array $args
 *
 * @return string
 */
function bto_social_links($args = [])
{
    $social_items = ACF::settingsField('social');
    $args         = [
        'classes' => ($args['classes'] ?? []),
    ];

    if (! $social_items) {
        return;
    }

    //---- CSS Classes
    $classes = Utilities::arraysMergeToString(['social'], $args['classes']);

    //---- HTML output
    $html = '';

    foreach ($social_items as $social_item_key => $social_item_val) {
        $social_item_key = $social_item_key == 'twitter' ? 'x' : $social_item_key;

        if ($social_item_val && Utilities::svg($social_item_key, 'icons')) {
            $html .= "<li class='social__item social__item--{$social_item_key}'>";
            $html .= "<a href='{$social_item_val}' target='_blank'>";
            $html .= Utilities::svg($social_item_key, 'icons');
            $html .= '</a>';
            $html .= '</li>';
        }
    }

    return $html ? "<ul class='{$classes}'>{$html}</ul>" : '';
}

/**
 * HTML for share icons
 *
 * @param array $args
 *
 * @return string
 */
function bto_share_links($args = [])
{
    $args = [
        'show' => ($args['show'] ?? ['facebook', 'twitter', 'linkedin', 'email']),
    ];

    $links = [
        'facebook' => [
            'url' => 'https://www.facebook.com/sharer/sharer.php?u=' . urlencode(get_the_permalink()),
        ],
        'twitter' => [
            'url' => 'https://twitter.com/intent/tweet?=text=' . urlencode(get_the_title()) . '&url=' . urlencode(get_the_permalink()),
        ],
        'linkedin' => [
            'url' => 'https://www.linkedin.com/shareArticle?mini=true&url=' . urlencode(get_the_permalink()) . '&title=' . urlencode(get_the_title()) . '&summary=' . urlencode(get_the_excerpt()),
        ],
        'pinterest' => [
            'url' => 'https://pinterest.com/pin/create/button/?url=' . urlencode(get_the_post_thumbnail_url(get_the_ID())) . '&media=&description=' . urlencode(get_the_title()),
        ],
        'email' => [
            'url' => 'mailto:?subject=' . rawurlencode(sprintf('%s - %s', get_the_title(), get_bloginfo('name'))) . '&body=' . rawurlencode(get_the_title() . ' ' . get_permalink()),
        ],
    ];

    $allowed_links = array_keys($links);

    //---- Only show certain links
    $links         = array_merge(array_flip($args['show']), $links);
    $allowed_links = array_intersect(array_keys($links), $args['show']);

    //---- HTML output
    if ($links) {
        $html = "<div class='share'>";

        foreach ($links as $link_key => $link) {
            if (in_array($link_key, $allowed_links)) {
                $html .= "<a class='share__item share__item--{$link_key}' href='{$link['url']}' target='_blank'>";
                $html .= Utilities::svg($link_key, 'icons');
                $html .= '</a>';
            }
        }

        return $html . '</div>';
    }
}

/**
 * HTML for retina thumbnail
 *
 * @param array $args
 *
 * @return string
 */
function bto_retina_thumb($args = [])
{
    $args = [
        'post_id' => ($args['post_id'] ?? get_the_ID()),
        'classes' => ($args['classes'] ?? []),
        'size'    => ($args['size'] ?? null),
    ];

    //---- CSS Classes
    $img_classes = 'class=" ' . Utilities::arraysMergeToString([], $args['classes']) . '"';

    //---- Create thumbnail output
    if ($args['size']) {
        if (has_post_thumbnail($args['post_id'])) {
            //---- Post thumbnail
            $thumb_id = get_post_thumbnail_id($args['post_id']);

            if (get_post_mime_type($thumb_id) == 'image/gif') {
                // Output entire image if it's a GIF
                return get_the_post_thumbnail($args['post_id'], 'full');
            }

            $thumb_url_1x = get_the_post_thumbnail_url($args['post_id'], $args['size']);
            $thumb_url_2x = get_the_post_thumbnail_url($args['post_id'], "{$args['size']} @2x");
            $thumb_alt    = get_post_meta($thumb_id, '_wp_attachment_image_alt', true);

            return "<img {$img_classes} src='{$thumb_url_1x}' srcset='{$thumb_url_1x} 1x, {$thumb_url_2x} 2x' alt='{$thumb_alt}' " . bto_img_size_attrs($args['size']) . '>';
        }

        //---- Default thumbnail
        $thumb_default = ACF::settingsField('default_thumb');

        if ($thumb_default) {
            if ($thumb_default['mime_type'] == 'image/gif') {
                // Output entire image if it's a GIF
                return "<img {$img_classes} src='{$thumb_default['url']}' alt='{$thumb_default['alt']}'>";
            }

            $thumb_url_1x = $thumb_default['sizes'][$args['size']];
            $thumb_url_2x = $thumb_default['sizes']["{$args['size']} @2x"];

            return "<img {$img_classes} src='{$thumb_url_1x}' srcset='{$thumb_url_1x} 1x, {$thumb_url_2x} 2x' alt='{$thumb_default['alt']}' " . bto_img_size_attrs($args['size']) . '>';
        }
    }
}

/**
 * HTML attribute for 2x ACF image
 *
 * @param array $img
 * @param array $size
 *
 * @return string
 */
function bto_cms_image_src($img, $size = null)
{
    $img_sizes = Utilities::imageSizes();
    $size_2x   = "{$size} @2x";

    if (! $size || ! isset($img_sizes[$size])) {
        return "src='{$img['url']}'";
    }

    if (! isset($img_sizes[$size_2x])) {
        return "src='{$img['sizes'][$size]}'";
    }

    return "src='{$img['sizes'][$size]}' srcset='{$img['sizes'][$size]} 1x, {$img['sizes'][$size_2x]} 2x'";
}

/**
 * HTML for 2x ACF image
 *
 * @param array $img
 * @param array $size
 *
 * @return string
 */
function bto_cms_image($img, $size = null)
{
    if (! is_array($img)) {
        return wp_get_attachment_image($img, $size, false, ['class' => 'img-full']);
    }

    return '<img class="img-full" ' . bto_cms_image_src($img, $size) . ' alt="' . $img['alt'] . '" ' . bto_img_size_attrs($size) . '>';
}

/**
 * HTML attributes for image based on size
 *
 * @param string $img_size
 *
 * @return string
 */
function bto_img_size_attrs($img_size)
{
    $width_attr  = null;
    $height_attr = null;
    $img_size    = Utilities::imageSizes()[$img_size] ?? null;

    if (isset($img_size['width']) && $img_size['width'] > 0) {
        $width_attr = "width='{$img_size['width']}'";
    }

    if (isset($img_size['height']) && $img_size['height'] > 0) {
        $height_attr = "height='{$img_size['height']}'";
    }

    return "{$width_attr} {$height_attr}";
}

/**
 * Recommended image size note for CMS
 *
 * @param string $size
 *
 * @return string
 */
function bto_recommended_image_size($size)
{
    $img_sizes    = Utilities::imageSizes();
    $img_attrs    = ($img_sizes[$size] ?? null);
    $img_attrs_2x = ($img_sizes[$size . ' @2x'] ?? null);

    if (! $img_attrs) {
        return;
    }

    if (isset($img_attrs_2x['width']) || isset($img_attrs_2x['height'])) {
        //---- Show minimum (1x) size(s)
        if ($img_attrs['width'] && $img_attrs['height'] > 0) {
            $output = sprintf(__('Minimum size: %sx%spx.', 'bluetown'), $img_attrs['width'], $img_attrs['height']);
        } else {
            $output = sprintf(__('Minimum width: %spx.', 'bluetown'), $img_attrs['width']);
        }

        //---- Show recommended (2x) size(s)
        if (isset($img_attrs_2x['width']) && isset($img_attrs_2x['height']) && $img_attrs_2x['height'] > 0) {
            $output .= '<br>';
            $output .= sprintf(__('Recommended size: %sx%spx.', 'bluetown'), $img_attrs_2x['width'], $img_attrs_2x['height']);
        } elseif (isset($img_attrs_2x['width'])) {
            $output .= '<br>';
            $output .= sprintf(__('Recommended width: %spx.', 'bluetown'), $img_attrs_2x['width']);
        }
    } else {
        //---- Show recommended size(s)
        if ($img_attrs['width'] && $img_attrs['height'] > 0) {
            $output = sprintf(__('Recommended size: %sx%spx.', 'bluetown'), $img_attrs['width'], $img_attrs['height']);
        } else {
            $output = sprintf(__('Recommended width: %spx.', 'bluetown'), $img_attrs['width']);
        }
    }

    return "<p class='description'>{$output}</p>";
}

/**
 * Add extra classes before WordPress outputs them to body_class();
 *
 * @param array $classes
 *
 * @return array
 */
function bto_body_classes($classes)
{
    $classes[] = 'no-touch';
    $classes[] = 'no-js';
    $classes[] = 'site-scroll--inactive';
    $classes[] = 'transition-pages';
    $classes[] = 'transition-pages--loading';

    $banner = (new Banner())
        ->withBackgroundColor()
        ->withBackgroundImage();

    if ($banner->isBackgroundImageType() || $banner->hasBackgroundColor()) {
        $classes[] = 'site--banner-has-bg';
    }

    return $classes;
}

add_filter('body_class', 'bto_body_classes');

/**
 * Filter any built-in WP_Query call
 *
 * @param object $query
 *
 * @return object
 */
function bto_filter_query(\WP_Query $query)
{
    //---- Don't run in admin area
    if (! is_admin()) {
        $post_type = $_GET['post_type'] ?? '';

        // Limit search to posts on blog
        if ($query->is_main_query()) {
            if (is_post_type_archive('wpbb_job') || $post_type == 'wpbb_job') {
                $query->set('post_status', ['publish']);
            }
        }

        if ($query->is_main_query() && $query->is_search()) {
            if ($post_type != 'wpbb_job') {
                $query->set('post_type', ['post']);
            }
        }

        // Return query
        return $query;
    }
}

add_filter('pre_get_posts', 'bto_filter_query');

/**
 * Polyfill if server is not running PHP 8
 */
if (! function_exists('str_contains')) {
    function str_contains($haystack, $needle)
    {
        return $needle !== '' && mb_strpos($haystack, $needle) !== false;
    }
}

/**
 * Format WYSIWYG content
 *
 * @param string $content
 *
 * @return string
 */
function bto_content($content)
{
    $content = wptexturize($content);
    $content = wpautop($content);
    $content = shortcode_unautop($content);
    $content = prepend_attachment($content);
    $content = wp_filter_content_tags($content);
    $content = wp_replace_insecure_home_url($content);

    $content = bto_remove_p_tags($content);
    $content = bto_shortcodes_fix_formatting($content);

    $content = capital_P_dangit($content);
    $content = do_shortcode($content);

    return convert_smilies($content);
}

add_filter('bto_content', 'bto_content');

function bto_is_job_search_page()
{
    return is_search() && ($_GET['post_type'] ?? '') == 'wpbb_job';
}

function bto_current_url()
{
    return sprintf(
        '%s://%s/%s',
        isset($_SERVER['HTTPS']) ? 'https' : 'http',
        $_SERVER['HTTP_HOST'],
        trim($_SERVER['REQUEST_URI'], '/\\')
    );
}

function bto_can_access_login_template()
{
    return !is_user_logged_in() && get_page_template_slug() == 'templates/login.php';
}

function bto_can_access_register_template()
{
    return !is_user_logged_in() && get_page_template_slug() == 'templates/register.php';
}

function bto_can_access_account_template()
{
    return is_user_logged_in() && get_page_template_slug() == 'templates/account.php';
}

function bto_form_has_been_submitted(string $submit_field, string $nonce_name)
{
    $nonce  = $_POST["{$nonce_name}_nonce"] ?? '';
    $submit = $_POST[$submit_field] ?? '';

    return $submit && wp_verify_nonce($nonce, $nonce_name);
}

function bto_create_username_from_name(string $first_name, string $last_name)
{
    return strtolower("{$first_name}_{$last_name}_" . rand(5000, 10000));
}

function bto_request(string $key, string $fallback = null)
{
    return esc_attr($_REQUEST[$key] ?? $fallback);
}

function bto_upload_cv_file(array $file_attrs)
{
    if ($file_attrs['tmp_name'] ?? null) {
        Validator::cvFile($file_attrs);

        return \wp_upload_bits(
            $file_attrs['name'],
            null,
            file_get_contents($file_attrs['tmp_name'])
        );
    }

    return [];
}
