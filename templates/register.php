<?php

/**
 * Template Name: Register
 *
 * @package Bluetown
 *
 * @author Fhoke <hello@fhoke.com>
 */

use Noticeable\Notice;

get_header();

$notice = Notice::get();

$job_sectors = get_terms([
    'taxonomy'   => 'wpbb_job_industry',
    'hide_empty' => false,
]);

$job_locations = get_terms([
    'taxonomy'   => 'wpbb_job_location',
    'hide_empty' => false,
]);

$job_types = get_terms([
    'taxonomy'   => 'wpbb_job_type',
    'hide_empty' => false,
]);
?>

<section class="core-section--shapes-top-left">
    <div class="section section--large pv-large">
        <?php if (!$notice->isEmpty()) : ?>
            <div class="mb-tiny txt-center">
                <div class="form-notice form-notice--<?php echo esc_attr($notice->type()) ?>">
                    <p>
                        <?php echo $notice->message(); ?>
                    </p>
                </div>
            </div>
        <?php endif; ?>

        <form method="post" action="" class="" enctype="multipart/form-data">
            <div class="form-section form-section--inline">
                <legend class="form-heading">
                    <?php _e('Personal Details', 'bluetown'); ?>
                </legend>

                <div class="form-section__inner">
                    <div class="form-row form-row--2">
                        <div class="form-field">
                            <label for="first-name" class="form-label">
                                <?php _e('First name', 'bluetown'); ?>
                            </label>
                            <input id="first-name" type="text" name="first_name" value="<?php echo bto_request('first_name'); ?>" required>
                        </div>

                        <div class="form-field">
                            <label for="last-name" class="form-label">
                                <?php _e('Last name', 'bluetown'); ?>
                            </label>
                            <input id="last-name" type="text" name="last_name" value="<?php echo bto_request('last_name'); ?>" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-field">
                            <label for="phone" class="form-label">
                                <?php _e('Phone number', 'bluetown'); ?>
                            </label>
                            <input id="phone" type="text" name="phone" value="<?php echo bto_request('phone'); ?>" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-field">
                            <label for="email" class="form-label">
                                <?php _e('Email address', 'bluetown'); ?>
                            </label>
                            <input id="email" type="email" name="email" value="<?php echo bto_request('email'); ?>" required>
                        </div>
                    </div>

                    <div class="form-row form-row--2">
                        <div class="form-field">
                            <label for="password" class="form-label">
                                <?php _e('Password', 'bluetown'); ?>
                            </label>
                            <input id="password" type="password" name="password" value="<?php echo bto_request('password'); ?>" required>
                        </div>

                        <div class="form-field">
                            <label for="password-confirm" class="form-label">
                                <?php _e('Password (Confirm)', 'bluetown'); ?>
                            </label>
                            <input id="password-confirm" type="password" name="password_confirm" value="<?php echo bto_request('password_confirm'); ?>" required>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-section form-section--inline">
                <legend class="form-heading">
                    <?php _e('CV', 'bluetown'); ?>
                </legend>

                <div class="form-section__inner">
                    <div class="form-row">
                        <div class="form-field">
                            <input id="cv" type="file" accept="application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" name="cv">
                            <p class="form-field__note">
                                <?php _e('We accept PDF or Word Documents. Maximum size for each single file is 5mb.', 'bluetown'); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <?php if ($job_sectors) : ?>
                <div class="form-section form-section--inline">
                    <legend class="form-heading">
                        <?php _e('Interested in', 'bluetown'); ?>
                    </legend>

                    <div class="form-section__inner">
                        <?php if ($job_sectors) : ?>
                            <div class="form-row">
                                <div class="form-field">
                                    <label for="job-sectors" class="form-label">
                                        <?php _e('Sectors', 'bluetown'); ?>
                                    </label>
                                    <select id="job-sectors" name="interested_in_terms[]" multiple>
                                        <?php foreach ($job_sectors as $job_sector) : ?>
                                            <option value="<?php echo esc_attr($job_sector->term_id); ?>"><?php echo $job_sector->name; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($job_locations) : ?>
                            <div class="form-row">
                                <div class="form-field">
                                    <label for="job-sectors" class="form-label">
                                        <?php _e('Locations', 'bluetown'); ?>
                                    </label>
                                    <select id="job-sectors" name="interested_in_terms[]" multiple>
                                        <?php foreach ($job_locations as $job_location) : ?>
                                            <option value="<?php echo esc_attr($job_location->term_id); ?>"><?php echo $job_location->name; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($job_types) : ?>
                            <div class="form-row">
                                <div class="form-field">
                                    <label for="job-sectors" class="form-label">
                                        <?php _e('Types', 'bluetown'); ?>
                                    </label>
                                    <select id="job-sectors" name="interested_in_terms[]" multiple>
                                        <?php foreach ($job_types as $job_type) : ?>
                                            <option value="<?php echo esc_attr($job_type->term_id); ?>"><?php echo $job_type->name; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="form-row">
                            <div class="form-field">
                                <label for="email-alerts-opt-out" class="form-label form-label--inline">
                                    <input id="email-alerts-opt-out" type="checkbox" name="email_alerts_opt_out">
                                    <?php _e("I don't want email updates", 'bluetown'); ?>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="form-section form-section--inline">
                <div class="form-section__inner">
                    <div class="form-row">
                        <button class="btn--full" name="submit" value="1">
                            <span>
                                <?php _e('Register', 'bluetown'); ?>
                            </span>
                        </button>

                        <?php wp_nonce_field('bto_register', 'bto_register_nonce'); ?>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

<?php
get_footer();
