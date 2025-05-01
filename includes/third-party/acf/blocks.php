<?php

/**
 * Assorted functions/hooks related to the block editor
 * * All custom blocks are powered by ACF
 *
 * @package Bluetown
 *
 * @author Fhoke <hello@fhoke.com>
 *
 * @see https://www.advancedcustomfields.com/resources/blocks/
 */

/**
 * Register custom blocks with ACF
 */
function bto_acf_register_block_types()
{
    acf_register_block_type([
        'name'            => 'boxed',
        'title'           => __('Boxed', 'bluetown'),
        'description'     => __('Add a box around some content.', 'bluetown'),
        'render_template' => BTO_INCLUDES . '/third-party/acf/blocks/boxed.php',
        'category'        => 'layout',
        'icon'            => 'visibility',
        'keywords'        => [
            'content',
        ],
        'supports'        => [
            'align' => false,
        ],
    ]);

    acf_register_block_type([
        'name'            => 'icon-content',
        'title'           => __('Icon + Text', 'bluetown'),
        'description'     => __('Show an icon next to some text.', 'bluetown'),
        'render_template' => BTO_INCLUDES . '/third-party/acf/blocks/icon-content.php',
        'category'        => 'layout',
        'icon'            => 'visibility',
        'keywords'        => [
            'icon',
            'content',
        ],
        'supports'        => [
            'align' => false,
        ],
    ]);

    acf_register_block_type([
        'name'            => 'toggle-box',
        'title'           => __('Toggle Box', 'bluetown'),
        'description'     => __("Hide content behind a 'Read More' link.", 'bluetown'),
        'render_template' => BTO_INCLUDES . '/third-party/acf/blocks/toggle-box.php',
        'category'        => 'layout',
        'icon'            => 'visibility',
        'keywords'        => [
            'toggle',
            'hidden-content',
        ],
        'supports'        => [
            'align' => false,
        ],
    ]);
}

add_action('acf/init', 'bto_acf_register_block_types');
