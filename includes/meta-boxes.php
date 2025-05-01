<?php

/**
 * Add meta boxes
 *
 * @param string post_type
 * @param WP_Post $post
 */
function bto_display_meta_boxes($post_type, $post)
{
    add_meta_box('bto_cv_meta', 'Information', 'bto_cv_meta_box', 'cv_entry', 'normal', 'low');
}

add_action('add_meta_boxes', 'bto_display_meta_boxes', 10, 2);

/**
 * CV details meta box
 *
 * @param W_Post $post
 */
function bto_cv_meta_box($post)
{
    $cv_id  = get_post_meta($post->ID, 'cv', true);
    $cv_url = wp_get_attachment_url($cv_id);

    $job_type = get_term_by('slug', get_post_meta($post->ID, 'job_type', true), 'wpbb_job_type');

    $cover_message = get_post_meta($post->ID, 'cover_message', true);
    $first_name    = get_post_meta($post->ID, 'first_name', true);
    $last_name     = get_post_meta($post->ID, 'last_name', true);
    $email         = get_post_meta($post->ID, 'email', true);
    $phone         = get_post_meta($post->ID, 'phone', true);

    $saved_job_sector = get_post_meta($post->ID, 'saved_job_sector', true);
    $saved_job_sector = get_term_by('slug', $saved_job_sector, 'wpbb_job_industry');

    $saved_job_location = get_post_meta($post->ID, 'saved_job_location', true);
    $saved_job_location = get_term_by('slug', $saved_job_location, 'wpbb_job_location');

    $saved_job_type = get_post_meta($post->ID, 'saved_job_type', true);
    $saved_job_type = get_term_by('slug', $saved_job_type, 'wpbb_job_type'); ?>
    <table class="form-table widefat">
        <thead>
            <tr>
                <td colspan="2">
                    <strong>
                        <?php _e('CV', 'bluetown'); ?>
                    </strong>
                </td>
            </tr>
        </thead>
        <tbody>
            <?php if ($cv_url) : ?>
                <tr valign="top" class="">
                    <td width="50%"><?php echo wp_basename($cv_url); ?></td>
                    <td>
                        <a href="<?php echo $cv_url; ?>" target="_blank">
                            <?php _e('View', 'bluetown'); ?> &rarr;
                        </a>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <table class="form-table widefat">
        <thead>
            <tr>
                <td colspan="2">
                    <strong>
                        <?php _e("What They're Looking For", 'bluetown'); ?>
                    </strong>
                </td>
            </tr>
        </thead>
        <tbody>
            <?php if ($job_type) : ?>
                <tr valign="top" class="alternate">
                    <td width="50%">
                        <?php _e('Job Type', 'bluetown'); ?>
                    </td>
                    <td>
                        <?php echo $job_type->name; ?>
                    </td>
                </tr>
            <?php endif; ?>
            <?php if ($cover_message) : ?>
                <tr valign="top" class="">
                    <td width="50%">
                        <?php _e('Cover Message', 'bluetown'); ?>
                    </td>
                    <td>
                        <?php echo apply_filters('the_content', $cover_message); ?>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <table class="form-table widefat">
        <thead>
            <tr>
                <td colspan="2">
                    <strong>
                        <?php _e('Personal Details', 'bluetown'); ?>
                    </strong>
                </td>
            </tr>
        </thead>
        <tbody>
            <?php if ($first_name) : ?>
                <tr valign="top" class="alternate">
                    <td width="50%">
                        <?php _e('First name', 'bluetown'); ?>
                    </td>
                    <td>
                        <?php echo $first_name; ?>
                    </td>
                </tr>
            <?php endif; ?>

            <?php if ($last_name) : ?>
                <tr valign="top" class="">
                    <td width="50%">
                        <?php _e('Last name', 'bluetown'); ?>
                    </td>
                    <td>
                        <?php echo $last_name; ?>
                    </td>
                </tr>
            <?php endif; ?>

            <?php if ($email) : ?>
                <tr valign="top" class="alternate">
                    <td width="50%">
                        <?php _e('Email', 'bluetown'); ?>
                    </td>
                    <td>
                        <?php echo $email; ?>
                        |
                        <a href="mailto:<?php echo $email; ?>" target="_blank">
                            <?php _e('Contact', 'bluetown'); ?> &rarr;
                        </a>
                    </td>
                </tr>
            <?php endif; ?>

            <?php if ($phone) : ?>
                <tr valign="top" class="">
                    <td width="50%">
                        <?php _e('Phone', 'bluetown'); ?>
                    </td>
                    <td>
                        <?php echo $phone; ?>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <?php
}
