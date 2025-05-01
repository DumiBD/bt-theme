<?php

/**
 * Assorted functions/hooks related to WP Broadbean
 *
 * @package Bluetown
 *
 * @author Fhoke <hello@fhoke.com>
 *
 * @see https://highrise.digital/products/wpbroadbean-wordpress-plugin/
 */

/**
 * Remove job meta
 */
remove_filter('the_content', 'wpbb_ouput_job_meta_data', 10, 1);

/**
 * Remove application form
 */
remove_filter('the_content', 'wpbb_job_application_form_output', 20, 1);

/**
 * Re-add application form after content
 */
// add_filter('bto_after_the_content', 'wpbb_job_application_form_output', 20, 1);

/**
 * Remove application form title
 */
remove_action('wpbb_before_application_form', 'wpbb_application_form_title', 20);
