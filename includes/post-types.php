<?php

/**
 * Custom post types
 *
 * @package Bluetown
 *
 * @author Fhoke <hello@fhoke.com>
 */

/**
 * Register custom post types
 */
function bto_post_types()
{
    //---- Team
    register_post_type('team_member', [
        'public'        => false,
        'show_ui'       => true,
        'has_archive'   => false,
        'menu_position' => 55,
        'menu_icon'     => 'dashicons-groups',
        'labels'        => [
            'name'                     => __('Team Members', 'bluetown'),
            'singular_name'            => __('Team Member', 'bluetown'),
            'menu_name'                => _x('Team Members', 'Admin Menu Name', 'bluetown'),
            'add_new'                  => __('Add New', 'bluetown'),
            'add_new_item'             => __('Add New Team Member', 'bluetown'),
            'new_item'                 => __('New Team Member', 'bluetown'),
            'edit_item'                => __('Edit Team Member', 'bluetown'),
            'view_item'                => __('View Team Member', 'bluetown'),
            'all_items'                => __('All Team Members', 'bluetown'),
            'search_items'             => __('Search Team Members', 'bluetown'),
            'not_found'                => __('No team members found.', 'bluetown'),
            'not_found_in_trash'       => __('No team members found in bin.', 'bluetown'),
            'item_published'           => __('Team Member successfully published.', 'bluetown'),
            'item_published_privately' => __('Team Member privately published.', 'bluetown'),
            'item_reverted_to_draft'   => __('Team Member reverted to draft.', 'bluetown'),
            'item_scheduled'           => __('Team Member successfully scheduled.', 'bluetown'),
            'item_updated'             => __('Team Member successfully updated.', 'bluetown'),
        ],
        'supports' => [
            'title',
        ],
    ]);

    //---- Testimonial
    register_post_type('testimonial', [
        'public'        => false,
        'show_ui'       => true,
        'has_archive'   => false,
        'menu_position' => 55,
        'menu_icon'     => 'dashicons-format-quote',
        'labels'        => [
            'name'                     => __('Testimonials', 'bluetown'),
            'singular_name'            => __('Testimonial', 'bluetown'),
            'menu_name'                => _x('Testimonials', 'Admin Menu Name', 'bluetown'),
            'add_new'                  => __('Add New', 'bluetown'),
            'add_new_item'             => __('Add New Testimonial', 'bluetown'),
            'new_item'                 => __('New Testimonial', 'bluetown'),
            'edit_item'                => __('Edit Testimonial', 'bluetown'),
            'view_item'                => __('View Testimonial', 'bluetown'),
            'all_items'                => __('All Testimonials', 'bluetown'),
            'search_items'             => __('Search Testimonials', 'bluetown'),
            'not_found'                => __('No testimonials found.', 'bluetown'),
            'not_found_in_trash'       => __('No testimonials found in bin.', 'bluetown'),
            'item_published'           => __('Testimonial successfully published.', 'bluetown'),
            'item_published_privately' => __('Testimonial privately published.', 'bluetown'),
            'item_reverted_to_draft'   => __('Testimonial reverted to draft.', 'bluetown'),
            'item_scheduled'           => __('Testimonial successfully scheduled.', 'bluetown'),
            'item_updated'             => __('Testimonial successfully updated.', 'bluetown'),
        ],
        'supports' => [
            'title',
        ],
    ]);

    //---- Job application
    register_post_type('job_application', [
        'public'        => false,
        'show_ui'       => true,
        'has_archive'   => false,
        'menu_position' => 55,
        'menu_icon'     => 'dashicons-text-page',
        'labels'        => [
            'name'                     => __('Applications', 'bluetown'),
            'singular_name'            => __('Application', 'bluetown'),
            'menu_name'                => _x('Job Applications', 'Admin Menu Name', 'bluetown'),
            'add_new'                  => __('Add New', 'bluetown'),
            'add_new_item'             => __('Add New Application', 'bluetown'),
            'new_item'                 => __('New Application', 'bluetown'),
            'edit_item'                => __('Edit Application', 'bluetown'),
            'view_item'                => __('View Application', 'bluetown'),
            'all_items'                => __('Applications', 'bluetown'),
            'search_items'             => __('Search Applications', 'bluetown'),
            'not_found'                => __('No applications found.', 'bluetown'),
            'not_found_in_trash'       => __('No applications found in bin.', 'bluetown'),
            'item_published'           => __('Application successfully published.', 'bluetown'),
            'item_published_privately' => __('Application privately published.', 'bluetown'),
            'item_reverted_to_draft'   => __('Application reverted to draft.', 'bluetown'),
            'item_scheduled'           => __('Application successfully scheduled.', 'bluetown'),
            'item_updated'             => __('Application successfully updated.', 'bluetown'),
        ],
        'supports' => [
            'title',
        ],
    ]);

    //---- CV (Legacy)
    register_post_type(
        'cv_entry',
        [
            'public'             => true,
            'publicly_queryable' => false,
            'has_archive'        => false,
            'menu_position'      => 55,
            'menu_icon'          => 'dashicons-id-alt',
            'labels'             => [
                'name'               => _x('Sent CVs (Legacy)', 'post type general name', 'bluetown'),
                'singular_name'      => _x('Sent CV', 'post type singular name', 'bluetown'),
                'menu_name'          => _x('Sent CVs  (Legacy)', 'admin menu', 'bluetown'),
                'add_new'            => _x('Add New', 'add new on admin bar', 'bluetown'),
                'add_new_item'       => __('Add New Sent CV', 'bluetown'),
                'new_item'           => __('New Sent CV', 'bluetown'),
                'edit_item'          => __('Edit Sent CV', 'bluetown'),
                'view_item'          => __('View Sent CV', 'bluetown'),
                'all_items'          => __('All Sent CVs', 'bluetown'),
                'search_items'       => __('Search Sent CVs', 'bluetown'),
                'not_found'          => __('No CVs found.', 'bluetown'),
                'not_found_in_trash' => __('No CVs found in bin.', 'bluetown'),
            ],
            'supports'      => [
                'title',
            ],
        ]
    );
}

add_action('init', 'bto_post_types');

/**
 * Add custom post types to dashboard
 */
function bto_dashboard_glance_items()
{
    $args = [
        'public'   => true,
        '_builtin' => false,
    ];

    $output     = 'object';
    $operator   = 'and';
    $post_types = get_post_types($args, $output, $operator);

    foreach ($post_types as $post_type) {
        $num_posts = wp_count_posts($post_type->name);
        $num       = number_format_i18n($num_posts->publish);
        $text      = _n($post_type->labels->singular_name, $post_type->labels->name, intval($num_posts->publish));

        if (current_user_can('edit_posts')) {
            $css_class = $post_type->name;
            $name      = $post_type->name; ?>
            <li class='post-count <?php echo $css_class; ?>-count'>
                <a href='edit.php?post_type=<?php echo $name; ?>'>
                    <?php echo "${num} ${text}"; ?>
                </a>
            </li>
            <?php
        }
    }
}

add_action('dashboard_glance_items', 'bto_dashboard_glance_items');
