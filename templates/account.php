<?php

/**
 * Template Name: Account
 *
 * @package Bluetown
 *
 * @author Fhoke <hello@fhoke.com>
 */

use Fhoke\Bluetown\User;
use Noticeable\Notice;

get_header();

$notice = Notice::get();

$user_obj = new User(get_current_user_id());

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

<section class="section section--large pv-large">
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
                        <input id="first-name" type="text" name="first_name" value="<?php echo esc_attr($user_obj->firstName()); ?>" required>
                    </div>

                    <div class="form-field">
                        <label for="last-name" class="form-label">
                            <?php _e('Last name', 'bluetown'); ?>
                        </label>
                        <input id="last-name" type="text" name="last_name" value="<?php echo esc_attr($user_obj->lastName()); ?>" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-field">
                        <label for="phone" class="form-label">
                            <?php _e('Phone number', 'bluetown'); ?>
                        </label>
                        <input id="phone" type="text" name="phone" value="<?php echo esc_attr($user_obj->phone); ?>" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-field">
                        <label for="email" class="form-label">
                            <?php _e('Email address', 'bluetown'); ?>
                        </label>
                        <input id="email" type="email" name="email" value="<?php echo esc_attr($user_obj->email()); ?>" required>
                    </div>
                </div>

                <div class="form-row form-row--2">
                    <div class="form-field">
                        <label for="password" class="form-label">
                            <?php _e('Password', 'bluetown'); ?>
                        </label>
                        <input id="password" type="password" name="password">
                    </div>

                    <div class="form-field">
                        <label for="password-confirm" class="form-label">
                            <?php _e('Password (Confirm)', 'bluetown'); ?>
                        </label>
                        <input id="password-confirm" type="password" name="password_confirm">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-field">
                        <label for="linkedin-url" class="form-label">
                            <?php _e('LinkedIn URL', 'bluetown'); ?>
                        </label>
                        <input id="linkedin-url" type="url" name="linkedin_url" value="<?php echo esc_attr($user_obj->linkedinUrl); ?>">
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
                        <input id="cv" type="file" accept="application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" name="cv" data-jcf='{"placeholderText": "<?php echo esc_attr($user_obj->cvFilename); ?>"}'>
                        <p class="form-field__note">
                            <?php _e('We accept PDF or Word Documents. Maximum size for each single file is 5mb.', 'bluetown'); ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-section form-section--inline">
            <legend class="form-heading">
                <?php _e('Address', 'bluetown'); ?>
            </legend>

            <div class="form-section__inner">
                <div class="form-row">
                    <div class="form-field">
                        <label for="address" class="form-label">
                            <?php _e('Address', 'bluetown'); ?>
                        </label>
                        <input id="address" type="text" name="address" value="<?php echo esc_attr($user_obj->address); ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-field">
                        <label for="town" class="form-label">
                            <?php _e('Town', 'bluetown'); ?>
                        </label>
                        <input id="town" type="text" name="address_town" value="<?php echo esc_attr($user_obj->addressTown); ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-field">
                        <label for="county" class="form-label">
                            <?php _e('County', 'bluetown'); ?>
                        </label>
                        <input id="county" type="text" name="address_county" value="<?php echo esc_attr($user_obj->addressCounty); ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-field">
                        <label for="postcode" class="form-label">
                            <?php _e('Postcode', 'bluetown'); ?>
                        </label>
                        <input id="postcode" type="text" name="address_postcode" value="<?php echo esc_attr($user_obj->addressPostcode); ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-field">
                        <label for="country" class="form-label">
                            <?php _e('Country', 'bluetown'); ?>
                        </label>
                        <input id="country" type="text" name="address_country" value="<?php echo esc_attr($user_obj->addressCountry); ?>">
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
                                    <?php foreach ($job_sectors as $term) : ?>
                                        <option value="<?php echo esc_attr($term->term_id); ?>" <?php selected(in_array($term->term_id, $user_obj->interestedInTerms)); ?>>
                                            <?php echo $term->name; ?>
                                        </option>
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
                                    <?php foreach ($job_locations as $term) : ?>
                                        <option value="<?php echo esc_attr($term->term_id); ?>" <?php selected(in_array($term->term_id, $user_obj->interestedInTerms)); ?>>
                                            <?php echo $term->name; ?>
                                        </option>
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
                                    <?php foreach ($job_types as $term) : ?>
                                        <option value="<?php echo esc_attr($term->term_id); ?>" <?php selected(in_array($term->term_id, $user_obj->interestedInTerms)); ?>>
                                            <?php echo $term->name; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="form-row">
                        <div class="form-field">
                            <label for="email-alerts-opt-out" class="form-label form-label--inline">
                                <input id="email-alerts-opt-out" type="checkbox" name="email_alerts_opt_out" <?php checked($user_obj->emailAlertsOptOut); ?>>
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
                    <button class="btn btn--full" name="submit" value="1">
                        <span>
                            <?php _e('Update Account', 'bluetown'); ?>
                        </span>
                    </button>

                    <?php wp_nonce_field('bto_update_account', 'bto_update_account_nonce'); ?>
                </div>
            </div>
        </div>
    </form>
</section>

<?php
get_footer();
