<?php

use Fhoke\Slate\Utilities;

/**
 * Register "expired" Post Status
 */
function bto_custom_post_status()
{
    register_post_status('expired', [
        'label'                     => _x('Expired', 'post'),
        'public'                    => true,
        'exclude_from_search'       => true,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop('Expired <span class="count">(%s)</span>', 'Expired <span class="count">(%s)</span>'),
    ]);
}

add_action('init', 'bto_custom_post_status');

/**
 * Use jQuery to add it to post status dropdown
 */
function bto_append_post_status_list()
{
    global $post;

    if (($post->post_type ?? '') == 'wpbb_job') {
        $complete = '';

        if ($post->post_status == 'expired') {
            $complete = "selected";
        } ?>
        <script>
            jQuery(document).ready(function($){
                $("select[name='_status']").append("<option value='expired' <?php echo $complete; ?>'>Expired</option>");
            });
        </script>
        <?php
    }
}

add_action('admin_footer', 'bto_append_post_status_list');

/**
 * Add custom columns to post types
 *
 * @param array $columns
 * @return array
 */
function bto_admin_job_cols($columns)
{
    unset($columns['date']);

    $columns['posted']  = 'Posted';
    $columns['expired'] = 'Expired';

    return $columns;
}

add_filter('manage_edit-wpbb_job_columns', 'bto_admin_job_cols');

/**
 * Add content to custom post type columns
 *
 * @param string $column
 * @param int $post_id
 * @return void
 */
function bto_admin_col_content($column, $post_id)
{
    global $post;

    switch ($column) {
        case 'posted':
            echo Utilities::datetime('date');
            break;
        case 'expired':
            echo bto_get_job_expired_date($post->ID);
            break;
    }
}

add_action('manage_wpbb_job_posts_custom_column', 'bto_admin_col_content', 11, 2);

/**
 * Set expired job meta
 *
 * @param string $new_status
 * @param string $old_status
 * @param WP_Post $post
 */
function bto_job_expired($new_status, $old_status, $post)
{
    // If the job post status is changed to expired, set the expired date post meta
    if ('expired' == $new_status && 'wpbb_job' == $post->post_type && ('publish' == $old_status || 'private' == $old_status)) {
        $days_to_advertise = get_post_meta($post->ID, '_wpbb_job_days_to_advertise', true);

        $expiry_date = new DateTime(get_the_date('Y-m-d'));
        date_add($expiry_date, date_interval_create_from_date_string($days_to_advertise . ' days'));

        update_post_meta($post->ID, 'expired_date', $expiry_date->format('Y-m-d H:i:s'));
    }
}

add_action('transition_post_status', 'bto_job_expired', 10, 3);

/**
 * Empty expired date post meta if the post is republished
 *
 * @param string $new_status
 * @param string $old_status
 * @param WP_Post $post
 */
function bto_job_republished($new_status, $old_status, $post)
{
    if ('publish' == $new_status && 'wpbb_job' == $post->post_type) {
        delete_post_meta($post->ID, 'expired_date');
    }
}

add_action('transition_post_status', 'bto_job_republished', 10, 3);

/**
 * Expire jobs
 */
function bto_expire_jobs()
{
    $jobs_query = new WP_Query([
        'post_type'      => 'wpbb_job',
        'posts_per_page' => -1,
        'post_status'    => ['publish', 'private'],
    ]);

    while ($jobs_query->have_posts()) {
        $jobs_query->the_post();

        $job_id            = get_the_ID();
        $days_to_advertise = get_post_meta($job_id, '_wpbb_job_days_to_advertise', true);

        if ($days_to_advertise) {
            $todays_date = new DateTime('now');
            $expiry_date = (new DateTime(get_the_date('Y-m-d H:i:s', $job_id)))->modify("+$days_to_advertise days");

            if ($todays_date > $expiry_date) {
                wp_update_post([
                    'ID'          => $job_id,
                    'post_status' => 'expired',
                    'meta_input'  => [
                        'expired_date' => $expiry_date->format('Y-m-d H:i:s'),
                    ],
                ]);
            } else {
                delete_post_meta($job_id, 'expired');
            }
        }
    }

    wp_reset_postdata();
}

add_action('bto_expired_jobs_action', 'bto_expire_jobs');

/**
 * Create wp-cron for expiring jobs
 */
function bto_add_expired_jobs_schedule()
{
    if (!wp_next_scheduled('bto_expired_jobs_action')) {
        wp_schedule_event(strtotime((new DateTime('now'))->format('Y-m-d H:i:s')), 'daily', 'bto_expired_jobs_action');
    }
}

add_action('init', 'bto_add_expired_jobs_schedule');

/**
 * Get expired job date
 *
 * @param bool $post_id
 * @return string
 */
function bto_get_job_expired_date($post_id = false)
{
    if (!$post_id) {
        $post_id = get_the_id();
    }

    $expired_date = get_post_meta($post_id, 'expired_date', true);

    if ($expired_date) {
        if ($expired_date instanceof DateTime) {
            $expired_date = $expired_date->format('j F, Y');
        }

        return $expired_date;
    }
}
