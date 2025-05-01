<?php

/**
 * Session management
 *
 * @package Bluetown
 *
 * @author Fhoke <hello@fhoke.com>
 */

/**
 * Start session
 */
function bto_start_theme_session()
{
    if (session_id()) {
        return;
    }

    session_start();
}

add_action('template_redirect', 'bto_start_theme_session', 1);
