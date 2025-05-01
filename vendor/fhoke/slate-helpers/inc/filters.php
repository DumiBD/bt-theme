<?php
/**
 * Remove wrapping <p></p> on images in the_content()
 *
 * @return string
 */
function slate_remove_p_tags(string $content)
{
    return preg_replace('/<p>\s*(<a .*>)?\s*(<img .* \/>)\s*(<\/a>)?\s*<\/p>/iU', '\1\2\3', $content);
}

/**
 * Remove HTML tags around shortcodes
 *
 * @return string
 */
function slate_shortcodes_fix_formatting(string $content = '')
{
    return strtr($content, [
        '<p>['    => '[',
        ']</p>'   => ']',
        ']<br />' => ']',
    ]);
}

/**
 * Format WYSIWYG content
 *
 * @param string $content
 *
 * @return string
 */
function slate_content($content)
{
    $content = wptexturize($content);
    $content = wpautop($content);
    $content = shortcode_unautop($content);
    $content = prepend_attachment($content);
    $content = wp_filter_content_tags($content);
    $content = wp_replace_insecure_home_url($content);

    $content = slate_remove_p_tags($content);
    $content = slate_shortcodes_fix_formatting($content);

    $content = capital_P_dangit($content);
    $content = do_shortcode($content);

    return convert_smilies($content);
}

add_filter('slate/format/content', 'slate_content');
