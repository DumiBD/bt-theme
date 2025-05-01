<?php

/**
 * Assorted functions/hooks related to Yoast SEO
 *
 * @package Bluetown
 *
 * @author Fhoke <hello@fhoke.com>
 *
 * @see https://yoast.com/
 */

/**
 * Give Yoast meta box lowest priority
 *
 * @return string
 */
add_filter('wpseo_metabox_prio', static function () {
    return 'low';
});
