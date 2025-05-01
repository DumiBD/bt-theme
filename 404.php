<?php

/**
 * 404 template
 *
 * @package Bluetown
 *
 * @author Fhoke <hello@fhoke.com>
 */

use Fhoke\Slate\Templating\Templates\CoreTemplate;

get_header();

echo (new CoreTemplate('theme_settings'))
    ->setOption('field_name', 'field_name')
    ->setSectionClasses(['pv-large'])
    ->render();

get_footer();
