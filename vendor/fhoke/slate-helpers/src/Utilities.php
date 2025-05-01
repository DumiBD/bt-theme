<?php

namespace Fhoke\Slate;

use Fhoke\Slate\ACF\ACF;

class Utilities
{
    /**
     * Get timestamp of when a file was last updated
     *
     * @param string $file_path
     * @return string
     */
    public static function fileLastUpdateTimestamp(string $file_path)
    {
        if (!\file_exists($file_path)) {
            return '';
        }

        return \filemtime($file_path);
    }

    /**
     * Get SVG file contents
     *
     * @param string $file_name
     * @param string $directory_name
     * @return string
     */
    public static function svg(string $file_name = null, string $directory_name = null)
    {
        if (!$file_name) {
            return '';
        }

        $directory_name = $directory_name ? "/$directory_name/" : '/';
        $file_path      = \get_stylesheet_directory() . '/dist/img/' . $directory_name . $file_name . '.svg';

        if (!\file_exists($file_path)) {
            return '';
        }

        return \file_get_contents($file_path);
    }

    /**
     * Get the first/primary term for a post
     *
     * @param array $args
     * @return string
     */
    public static function term($args = [])
    {
        //---- Set up args
        $args = [
            'post_id'  => ($args['post_id'] ?? get_the_id()),
            'taxonomy' => ($args['tax'] ?? 'category'),
            'classes'  => ($args['classes'] ?? null),
            'output'   => ($args['output'] ?? 'link'),
        ];

        //---- Get the terms
        $terms = \get_the_terms($args['post_id'], $args['taxonomy']);

        if ($terms) {
            // Default
            $term_id   = $terms[0]->term_id;
            $term_name = $terms[0]->name;
            $term_url  = get_term_link($term_id, $args['taxonomy']);

            // CSS classes
            $classes = ($args['classes'] ? 'class="' . trim($args['classes']) . '"' : null);

            // Yoast (Override)
            if (class_exists('WPSEO_Primary_Term')) {
                $yoast_term   = new \WPSEO_Primary_Term($args['taxonomy'], $args['post_id']);
                $primary_term = get_term($yoast_term->get_primary_term(), $args['taxonomy']);

                if ($primary_term && isset($primary_term->term_id) && isset($primary_term->name)) {
                    $term_id   = $primary_term->term_id;
                    $term_name = $primary_term->name;
                    $term_url  = get_term_link($term_id, $args['taxonomy']);
                }
            }

            // Output
            if ($args['output'] == 'link') {
                $output = "<a $classes href='$term_url'>$term_name</a>";
            } elseif ($args['output'] == 'url') {
                $output = $term_url;
            } elseif ($args['output'] == 'text') {
                $output = $term_name;
            } elseif ($args['output'] == 'id') {
                $output = $term_id;
            }

            return $output;
        }
    }

    /**
     * Merge two array into a string
     *
     * @param array $arr_1
     * @param array $arr_2
     * @param string $delimiter
     * @return string
     */
    public static function arraysMergeToString(array $arr_1, array $arr_2, $delimiter = ' ')
    {
        //---- Merge arrays
        $arr = \array_merge($arr_1, $arr_2);

        //---- Convert new array to string
        $arr = \implode($delimiter, $arr);

        //---- Sanitize string for output
        $arr = \esc_attr($arr);

        //---- Return
        return $arr;
    }

    /**
     * Display date and/or time a post was published
     *
     * @param string $type
     * @param int|\WP_Post $post
     * @return string
     */
    public static function datetime($type = '', $post = null)
    {
        $date_format = \get_option('date_format');
        $time_format = \get_option('time_format');

        if ($type == 'date') {
            return \get_the_time($date_format, $post);
        }

        if ($type == 'time') {
            return \get_the_time($time_format, $post);
        }

        return \sprintf(
            \_x(
                '%s at %s',
                'Separator for date and time',
                'slate'
            ),
            \get_the_time($date_format, $post),
            \get_the_time($time_format, $post)
        );
    }

    /**
    * Get SVG file contents
     *
     * @param array $img
     * @param string $fallback_img_size
     * @return string
     */
    public static function svgFromCms(array $img, string $fallback_img_size = null)
    {
        if (!is_array($img)) {
            return \wp_get_attachment_image($img, $fallback_img_size, false, [
                'class' => 'img-full',
            ]);
        }

        if ($img['mime_type'] != 'image/svg+xml') {
            return self::cmsImage($img, $fallback_img_size);
        }

        $img_path = self::cmsImagePath($img['ID']);

        if (!$img_path) {
            return '';
        }

        return \file_get_contents($img_path);
    }

    /**
     * SRC attribute for image tags
     *
     * @param array $img
     * @param string $size
     * @return string
     */
    public static function imageSrcAttribute(array $img, string $size = '')
    {
        $img_sizes = self::imageSizes();
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
     * Attributes for image size
     *
     * @param string $img_size
     * @return string
     */
    public static function imageSizeAttributes(string $img_size)
    {
        $width_attr  = null;
        $height_attr = null;
        $img_size    = self::imageSizes()[$img_size] ?? null;

        if (isset($img_size['width']) && $img_size['width'] > 0) {
            $width_attr = "width='{$img_size['width']}'";
        }

        if (isset($img_size['height']) && $img_size['height'] > 0) {
            $height_attr = "height='{$img_size['height']}'";
        }

        return "{$width_attr} {$height_attr}";
    }

    /**
     * Image tag for CMS image
     *
     * @param array $img
     * @param string $size
     * @param bool $lazy_load
     * @return string
     */
    public static function cmsImage(array $img, string $size = '', bool $lazy_load = true)
    {
        if (! is_array($img)) {
            return \wp_get_attachment_image($img, $size, false, ['class' => 'img-full']);
        }

        $src_attr     = self::imageSrcAttribute($img, $size);
        $size_attrs   = self::imageSizeAttributes($size);
        $loading_attr = $lazy_load ? 'loading="lazy"' : '';

        return "<img $loading_attr class='img-full' {$size_attrs} {$src_attr} alt='{$img['alt']}'>";
    }

    /**
     * Path to image from media library
     *
     * @param int $id
     * @return string
     */
    public static function cmsImagePath(int $id)
    {
        $uploads = \wp_upload_dir();
        $path    = \str_replace($uploads['baseurl'], $uploads['basedir'], \wp_get_attachment_url($id));

        if (!\file_exists($path)) {
            return '';
        }

        return $path;
    }

    /**
     * Get all available image sizes
     *
     * @return array
     */
    public static function imageSizes()
    {
        global $_wp_additional_image_sizes;

        $image_sizes = [];

        \array_map(function (string $size) use (&$image_sizes) {
            $image_sizes[$size]['width']  = intval(get_option("{$size}_size_w", ''));
            $image_sizes[$size]['height'] = intval(get_option("{$size}_size_h", ''));
            $image_sizes[$size]['crop']   = get_option("{$size}_crop", false);
        }, get_intermediate_image_sizes());

        if ($_wp_additional_image_sizes && \count($_wp_additional_image_sizes)) {
            $image_sizes = \array_merge($image_sizes, $_wp_additional_image_sizes);
        }

        return $image_sizes;
    }

    /**
     * Get the current post ID
     *
     * @return int
     */
    public static function currentPostID()
    {
        if (\is_home()) {
            return \intval(\get_option('page_for_posts'));
        }

        return \get_queried_object()->term_id ?? \get_the_ID();
    }

    /**
     * Fhoke attribution link
     *
     * @return string
     */
    public static function devAttrLink()
    {
        if (!ACF::settingsField('fhoke_attribution_enabled')) {
            return '';
        }

        $txt = \sprintf(
            \__('Crafted by %s', 'slate'),
            'Fhoke'
        );

        return "<a href='https://www.fhoke.com' target='_blank'>$txt</a>";
    }

    /**
     * Check if a plugin is active
     *
     * @param string $plugin_name
     * @return bool
     */
    public static function pluginIsActive(string $plugin_name)
    {
        switch ($plugin_name) {
            case 'woocommerce':
                return \class_exists('WooCommerce');
        }
    }

    /**
     * Check if WooCommerce is active
     *
     * @return bool
     */
    public static function wcIsActive()
    {
        return self::pluginIsActive('woocommerce');
    }

    /**
     * Format a comma separated address
     *
     * @param string $address
     * @return string
     */
    public static function formatAddress(string $address = null)
    {
        return \implode('<br>', \explode(', ', $address));
    }

    /**
     * Output a Google Map with one or more markers
     *
     * @param array $maps
     * @param bool $display_address
     * @return string
     */
    public static function googleMap(array $maps = [], $display_address = true)
    {
        \wp_enqueue_script('google-maps');

        $marker_url = \esc_url(\get_template_directory_uri() . '/dist/img/icons/map-marker.svg');

        $html = "<div class='google-map'>";

        $maps = \array_map(function ($map) use (&$html, $marker_url, $display_address) {
            $html .= "<div class='marker' data-lat='{$map['lat']}' data-lng='{$map['lng']}' data-marker='$marker_url'>";

            if ($display_address) {
                $html .= '<p class="google-map__desc">' . self::formatAddress($map['address']) . "<br>{$map['post_code']}</p>";
            }

            $html .= "</div>";
        }, $maps);

        $html .= '</div>';

        return $html;
    }

    /**
     * Parse markdown syntax
     *
     * @param string $content
     * @return string
     */
    public static function parseMarkdown(string $content = '')
    {
        $patterns = [
            "/\*\*(.*?)\*\*/", // '**strong**' to '<strong>strong</strong>'
            "/\*(.*?)\*/", // '*emhasis*' to '<em>emphasis</em>'
            "/\_(.*?)\_/", // '_emhasis_' to '<em>emphasis</em>'
            "/\[([^\]]+)\]\(([^\)]+)\)/", // '[Link Title](/link-url)' to  '<a href="/link-url" target="_blank">Link Title</a>'

        ];
        $replacements = [
            '<strong>$1</strong>',
            '<em>$1</em>',
            '<em>$1</em>',
            '<a href="$2" target="_blank">$1</a>',
        ];

        $content = str_replace('\\', '<br>', $content); // '\' to '<br>'
        $content = preg_replace($patterns, $replacements, $content);

        return $content;
    }
}
