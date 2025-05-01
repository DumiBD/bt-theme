<?php

/**
 * Assorted functions/hooks related to Gravity Forms
 *
 * @package Bluetown
 *
 * @author Fhoke <hello@fhoke.com>
 *
 * @see https://www.gravityforms.com/
 */

/**
 * HTML for custom submit button
 *
 * @return string
 */
function bto_gf_form_button(string $button, array $form)
{
    return "<button class='gform_button' id='gform_submit_button_{$form['id']}' data-gf-id='{$form['id']}'><span>{$form['button']['text']}</span></button>";
}

add_filter('gform_submit_button', 'bto_gf_form_button', 10, 2);

/**
 * Remove Ajax auto-scroll
 */
add_filter('gform_confirmation_anchor', '__return_false');
