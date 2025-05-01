<?php

/**
 * Template Name: Core
 *
 * @package Bluetown
 *
 * @author Fhoke <hello@fhoke.com>
 */

use Fhoke\Slate\Templating\Templates\CoreTemplate;

get_header();

echo (new CoreTemplate())
    ->setSectionClasses(['pv-large'])
    ->render();

get_footer();
