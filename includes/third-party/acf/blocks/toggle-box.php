<?php

/**
 * "Toggle Box" editor block
 *
 * @package Bluetown
 *
 * @author Fhoke <hello@fhoke.com>
 */

use Fhoke\Slate\ACF\ACF;
use Fhoke\Slate\Templating\Views\View;

$content = ACF::field('txt');

if (is_admin()) {
    if (! $content) {
        $content = __('Your content here...', 'bluetown');
    }
}

View::present('components/toggle-box.twig', [
    'content' => $content,
]);
