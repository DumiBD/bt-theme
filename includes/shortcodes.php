<?php

/**
 * Custom shortcodes
 *
 * @package Bluetown
 *
 * @author Fhoke <hello@fhoke.com>
 */

use Fhoke\Slate\ACF\Button;
use Fhoke\Slate\Templating\Views\View;
use Fhoke\Slate\Utilities;

/**
 * Remove HTML tags around shortcodes
 *
 * @return string
 */
function bto_shortcodes_fix_formatting(string $content = '')
{
    $array = [
        '<p>['    => '[',
        ']</p>'   => ']',
        ']<br />' => ']',
    ];

    return strtr($content, $array);
}

add_filter('the_content', 'bto_shortcodes_fix_formatting');
add_filter('acf_the_content', 'bto_shortcodes_fix_formatting');

/**
 * Button shortcode
 *
 * @param array<string>|string $atts
 *
 * @return string
 */
function bto_shortcode_btn($atts, string $content = '')
{
    if (! $content) {
        return;
    }

    extract(shortcode_atts([
        'url'      => '',
        'external' => false,
        'size'     => '',
        'style'    => '',
    ], $atts));

    $url = esc_url($url);

    //---- CSS Classes
    $classes = [];

    if ($size === 'full') {
        $classes[] = 'btn--full';
    } elseif ($size === 'small') {
        $classes[] = 'btn--small';
    }

    //---- Options
    $options = [];

    if ($external === 'true') {
        $options[] = 'external';
    }

    if ($style === 'secondary') {
        $icon = Utilities::svg('arrow-right', 'icons');

        return <<< EOT
            <a class="fancy-link" href="{$url}">
                <span class="fancy-icon">
                    {$icon}
                </span>

                <span class="fancy-link__txt">
                    {$content}
                </span>
            </a>
        EOT;
    }

    //---- Button
    $button = new Button([
        'is_enabled' => true,
        'url'        => $url,
        'txt'        => $content,
        'options'    => $options,
    ], [
        'classes' => $classes,
    ]);

    return $button->render();
}

add_shortcode('button', 'bto_shortcode_btn');

/**
 * Boxed shortcode
 *
 * @param array<string>|string $atts
 *
 * @return string
 */
function bto_shortcode_boxed($atts, string $content = '')
{
    if (! $content) {
        return '';
    }

    return View::render('components/boxed.twig', [
        'content' => $content,
    ]);
}

add_shortcode('boxed', 'bto_shortcode_boxed');

/**
 * Icon followed by content shortcode
 *
 * @param array<string>|string $atts
 *
 * @return string
 */
function bto_shortcode_icon_content($atts, string $content = '')
{
    extract(shortcode_atts([
        'icon'  => false,
        'text'  => false,
        'style' => 'primary',
    ], $atts));

    if (! $content) {
        return;
    }

    return View::render('components/icon-and-text.twig', [
        'content' => $content,
        'style'   => $style,
        'icon'    => $icon,
        'text'    => $text,
    ]);
}

add_shortcode('icon_content', 'bto_shortcode_icon_content');

/**
 * Quote shortcode
 *
 * @param array<string>|string $atts
 *
 * @return string
 */
function bto_shortcode_quote($atts, string $content = '')
{
    extract(shortcode_atts([
        'name'    => false,
        'company' => false,
    ], $atts));

    $quote_footer = false;

    if ($name && $company) {
        $quote_footer = "<cite><strong>${name}</strong><br>${company}</cite>";
    } elseif ($name && ! $company) {
        $quote_footer = "<cite><strong>${name}</strong></cite>";
    } elseif ($company && ! $name) {
        $quote_footer = "<cite>${company}</cite>";
    }

    if (! $content) {
        return;
    }

    $output = '<blockquote>';
    $output .= "<p>${content}</p>";

    if ($quote_footer) {
        $output .= '<footer>';
        $output .= $quote_footer;
        $output .= '</footer>';
    }

    $output .= '</blockquote>';

    return $output;
}

add_shortcode('quote', 'bto_shortcode_quote');

/**
 * Toggle box shortcode
 *
 * @param array<string>|string $atts
 *
 * @return string
 */
function bto_shortcode_toggle_box($atts, string $content = '')
{
    if (! $content) {
        return '';
    }

    return View::render('components/toggle-box.twig', [
        'content' => $content,
    ]);
}

add_shortcode('toggle_box', 'bto_shortcode_toggle_box');

/**
 * Grid shortcode
 *
 * @param array<string>|string $atts
 *
 * @return string
 */
function bto_shortcode_row($atts, string $content = '')
{
    if (! $content) {
        return;
    }

    return '<div class="grid grid--spaced-equal">' . do_shortcode($content) . '</div>';
}

add_shortcode('row', 'bto_shortcode_row');

/**
 * Grid column shortcode
 *
 * @param array<string>|string $atts
 *
 * @return string
 */
function bto_shortcode_col($atts, string $content = '')
{
    if (! $content) {
        return;
    }

    return '<div class="grid__col">' . wpautop(do_shortcode($content)) . '</div>';
}

add_shortcode('column', 'bto_shortcode_col');
