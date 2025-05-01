<?php

/**
 * "Icon + Text" editor block
 *
 * @package Bluetown
 *
 * @author Fhoke <hello@fhoke.com>
 */

use Fhoke\Slate\ACF\ACF;
use Fhoke\Slate\Templating\Views\View;

$icon    = ACF::field('icon');
$content = ACF::field('txt');

if (is_admin()) {
    if (! $content) {
        $content = __('Your content here...', 'bluetown');
    }
}

View::present('components/icon-and-text.twig', [
    'content' => $content,
    'icon'    => $icon,
]);
