<?php

/**
 * Default post template
 *
 * @package Bluetown
 *
 * @author Fhoke <hello@fhoke.com>
 */

use Fhoke\Bluetown\PostTypes\Job;
use Fhoke\Bluetown\Queries;
use Fhoke\Bluetown\User;
use Fhoke\Slate\ACF\ACF;
use Fhoke\Slate\ACF\Button;
use Fhoke\Slate\ACF\Link;
use Fhoke\Slate\Templating\Views\View;
use Fhoke\Slate\Utilities;
use Noticeable\Notice;

get_header();

$notice = Notice::get();

$user_obj = new User(get_current_user_id());
$job      = new Job(get_the_ID());

$sidebar_help_box = ACF::settingsField('job_sidebar_help_box');
$related_query    = Queries::related(get_the_id());
?>

<section class="pv-large core-section--shapes-bottom-right">
    <div class="content-alt">
        <div class="content-alt__main">
            <?php while (have_posts()) : ?>
                <?php the_post(); ?>
                <article class="txt-styles">
                    <?php the_content(); ?>
                </article>
            <?php endwhile; ?>

            <?php if ($job->applyUrl) : ?>
                <div class="mt-tiny">
                    <a href="<?php echo esc_attr($job->applyUrl); ?>" class="btn btn--large" target="_blank">
                        <span>
                            <?php _e('Apply for Job', 'bluetown'); ?>
                        </span>
                    </a>
                </div>
            <?php endif; ?>

            <div class="mt-small">
                <h6 class="txt-small mb-mini txt-center txt-uppercase">
                    <?php _e('Share', 'bluetown'); ?>
                </h6>
                <div class="share-inline">
                    <?php echo bto_share_links(); ?>
                </div>
            </div>

            <?php echo apply_filters('bto_after_the_content', ''); ?>

            <?php if (!$job->applyUrl && !$job->isExpired) : ?>
                <div id="apply-form-<?php echo esc_attr(get_the_ID()); ?>" class="pt-large">
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
                        <div class="form-section">
                            <legend class="form-heading">
                                <?php _e('Personal Details', 'bluetown'); ?>
                            </legend>

                            <div class="form-section__inner">
                                <div class="form-row form-row--2">
                                    <div class="form-field">
                                        <label for="first-name" class="form-label">
                                            <?php _e('First name', 'bluetown'); ?>
                                        </label>
                                        <input id="first-name" type="text" name="first_name" value="<?php echo bto_request('first_name', $user_obj->firstName()); ?>" required>
                                    </div>

                                    <div class="form-field">
                                        <label for="last-name" class="form-label">
                                            <?php _e('Last name', 'bluetown'); ?>
                                        </label>
                                        <input id="last-name" type="text" name="last_name" value="<?php echo bto_request('last_name', $user_obj->lastName()); ?>" required>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-field">
                                        <label for="phone" class="form-label">
                                            <?php _e('Phone number', 'bluetown'); ?>
                                        </label>
                                        <input id="phone" type="text" name="phone" value="<?php echo bto_request('phone', $user_obj->phone); ?>" required>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-field">
                                        <label for="email" class="form-label">
                                            <?php _e('Email address', 'bluetown'); ?>
                                        </label>
                                        <input id="email" type="email" name="email" value="<?php echo bto_request('email', $user_obj->email()); ?>" required>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-field">
                                        <label for="cover-message" class="form-label">
                                            <?php _e('Covering message', 'bluetown'); ?>
                                        </label>
                                        <textarea id="cover-message" type="email" name="cover_message" required><?php echo bto_request('cover_message'); ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <legend class="form-heading">
                                <?php _e('CV', 'bluetown'); ?>
                            </legend>

                            <div class="form-section__inner">
                                <div class="form-row">
                                    <div class="form-field">
                                        <input
                                            id="cv"
                                            type="file"
                                            accept="application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                                            name="cv"
                                            <?php echo $user_obj->cvFilename ? "data-jcf='{\"placeholderText\": \"{$user_obj->cvFilename}\"}'" : ""; ?>
                                            <?php echo !$user_obj->cvFilename ? 'required' : ''; ?>>
                                        <p class="form-field__note">
                                            <?php _e('We accept PDF or Word Documents. Maximum size for each single file is 5mb.', 'bluetown'); ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php if (!is_user_logged_in()) : ?>
                            <div class="form-section" data-toggle="create-account">
                                <legend class="form-heading">
                                    <?php _e('Account details', 'bluetown'); ?>
                                </legend>

                                <div class="form-section__inner">
                                    <div class="form-row form-row--2">
                                        <div class="form-field">
                                            <label for="password" class="form-label">
                                                <?php _e('Password', 'bluetown'); ?>
                                            </label>
                                            <input id="password" type="password" name="password"<?php echo bto_request('password'); ?>>
                                        </div>

                                        <div class="form-field">
                                            <label for="password-confirm" class="form-label">
                                                <?php _e('Password (Confirm)', 'bluetown'); ?>
                                            </label>
                                            <input id="password-confirm" type="password" name="password_confirm"<?php echo bto_request('password_confirm'); ?>>
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-field">
                                            <label for="linkedin-url" class="form-label">
                                                <?php _e('LinkedIn URL', 'bluetown'); ?>
                                            </label>
                                            <input id="linkedin-url" type="url" name="linkedin_url" value="<?php echo bto_request('linkedin_url'); ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="form-section">
                            <div class="form-section__inner">
                                <?php if (!is_user_logged_in()) : ?>
                                    <div class="form-row">
                                        <div class="form-field">
                                            <label for="create-account" class="form-label form-label--inline">
                                                <input id="create-account" type="checkbox" name="create_account" data-toggle-target="create-account">
                                                <?php _e('Create an account to save jobs and apply quicker.', 'bluetown'); ?>
                                            </label>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <div class="form-row">
                                    <div class="form-field">
                                        <label for="terms" class="form-label form-label--inline">
                                            <input id="terms" type="checkbox" name="accept_terms" required>
                                            <span>
                                                <?php echo sprintf(__("Have you read and understood our <a href='%s' target='_blank'>GDPR data policy</a>?", 'bluetown'), get_permalink(get_option('wp_page_for_privacy_policy'))); ?>
                                            </span>
                                        </label>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <button class="btn--full" name="submit" value="1">
                                        <span>
                                            <?php _e('Apply now', 'bluetown'); ?>
                                        </span>
                                    </button>

                                    <?php wp_nonce_field('bto_apply_for_job', 'bto_apply_for_job_nonce'); ?>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            <?php endif; ?>
        </div>
        <div class="content-alt__side">
            <div class="sticky">
                <?php if ($sidebar_help_box) : ?>
                    <div class="mb-tiny">
                        <div class="card card--dark">
                            <div class="card__media card__media--alt">
                                <?php echo Utilities::cmsImage($sidebar_help_box['img'], 'Hexagon 2'); ?>
                            </div>

                            <div class="card__content">
                                <div class="txt-styles">
                                    <?php echo apply_filters('slate_the_content', $sidebar_help_box['txt']) ?>
                                </div>

                                <?php if ($sidebar_help_box['btns']) : ?>
                                    <div class="mt-tiny">
                                        <div class="btn-group btn-group--small">
                                            <?php foreach ($sidebar_help_box['btns'] as $btn) : ?>
                                                <?php
                                                    $button = new Button($btn['btn'], ['classes' => ['btn--small', 'btn--full']]);
                                                ?>
                                                <?php echo $button->render(); ?>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php while (have_rows('job_sidebar_slides', 'theme_settings')) : ?>
                    <?php the_row(); ?>
                    <div class="mb-tiny">
                        <?php
                            $card = [
                                'icon' => bto_cms_image(get_sub_field('icon'), 'Card Icon'),
                                'txt'  => get_sub_field('txt'),
                                'link' => new Link(get_sub_field('link'), ['classes' => ['txt-uppercase txt-tiny txt-link txt-link--alt']]),
                            ];
                            View::present('components/card.twig', [
                                'image' => $card['icon'],
                                'txt'   => $card['txt'],
                                'link'  => $card['link'],
                            ]);
                        ?>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</section>

<?php if ($related_query->have_posts() || 1 == 2) : ?>
    <section class="pv-large bg-dark core-section--shapes-top-right">
        <div class="section section--large">
            <div class="mb-medium  txt-center txt-light">
                <h2>
                    <?php _e("Related jobs", 'bluetown'); ?>
                </h2>
            </div>

            jobs

            <div class="mt-medium txt-center">
                <a class="btn" href="<?php echo get_post_type_archive_link('wpbb_job'); ?>">
                    <?php _e('All Jobs', 'bluetown'); ?>
                </a>
            </div>
        </div>
    </section>
<?php endif; ?>

<?php get_footer(); ?>
