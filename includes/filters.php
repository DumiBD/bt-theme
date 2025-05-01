<?php

/**
 * Assorted helper filters
 *
 * @package Bluetown
 *
 * @author Fhoke <hello@fhoke.com>
 */

/**
 * Convert tokens in text to values
 *
 * @return string
 */
function bto_convert_text_tokens(string $text = '')
{
    return str_replace(
        [
            '{{ year }}',
        ],
        [
            date('Y'),
        ],
        $text
    );
}

add_filter('bto/text_tokens', 'bto_convert_text_tokens');

/**
 * Convert Mailchimp standard POST URL to ajax format
 *
 * @return string
 */
function bto_convert_mailchimp_post_url_for_ajax(string $url = '')
{
    return str_replace('/post?u', '/post-json?u', $url);
}

add_filter('bto/mailchimp_signup_url', 'bto_convert_mailchimp_post_url_for_ajax');
