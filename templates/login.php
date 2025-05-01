<?php

/**
 * Template Name: Log in
 *
 * @package Bluetown
 *
 * @author Fhoke <hello@fhoke.com>
 */

use Fhoke\Slate\ACF\ACF;
use Fhoke\Slate\Templating\Views\View;
use Noticeable\Notice;

get_header();

$login_page    = ACF::settingsField('endpoint_login');
$register_page = ACF::settingsField('endpoint_register');

$notice = Notice::get();
$action = $_GET['action'] ?? 'login';

$reset_key   = $_GET['key'] ?? '';
$reset_login = $_GET['login'] ?? '';

switch ($action) {
    case 'login':
        $page_title = get_the_title();
        $page_desc  = sprintf(__("Need a Bluetown account? <a href='%s'>Create an account</a>", 'bluetown'), get_permalink($register_page));
        break;
    case 'forgot_password':
        $page_title = __('Forgot password', 'bluetown');
        $page_desc  = __("Enter your email address below and we'll send you a reset link.", 'bluetown');
        break;
    case 'reset_password':
        $page_title       = __('Reset password', 'bluetown');
        $page_desc        = __("Please enter your new password below.", 'bluetown');
        $reset_validated  = $reset_key && $reset_login;
        break;
    default:
        $page_title       = '';
        $page_desc        = '';
        $reset_validated  = false;
        break;
}
?>

<section class="section section--large pv-large">
    <?php if ($action == 'login') : ?>
        <div class="grid grid--spaced grid--v-center grid--spaced-tb1-1">
            <div class="grid__col grid__col--6">
                <div class="section section--tiny section--full tb1-txt-center">
                    <?php
                        View::present('components/form-intro.twig', [
                            'title'       => $page_title,
                            'description' => $page_desc,
                        ]);
                    ?>
                </div>

                <?php if (!$notice->isEmpty()) : ?>
                    <div class="mb-tiny section section--tiny section--full">
                        <div class="form-notice form-notice--<?php echo esc_attr($notice->type()) ?>">
                            <p>
                                <?php echo $notice->message(); ?>
                            </p>
                        </div>
                    </div>
                <?php endif; ?>

                <form method="post" action="" class="section section--tiny section--full">
                    <div class="form-row">
                        <div class="form-field">
                            <label for="email" class="form-label">
                                <?php _e('Email address', 'bluetown'); ?>
                            </label>
                            <input tabindex="1" id="email" type="email" name="email" value="<?php echo esc_attr($_REQUEST['email'] ?? ''); ?>" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-field">
                            <label for="password" class="form-label form-label--with-link">
                                <?php _e('Password', 'bluetown'); ?>
                                <a href="<?php echo esc_url(add_query_arg('action', 'forgot_password')); ?>">
                                    <?php _e('Forgot?', 'bluetown'); ?>
                                </a>
                            </label>
                            <input tabindex="2" id="password" type="password" name="password" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-field">
                            <label for="remember" class="form-label form-label--inline">
                                <input id="remember" type="checkbox" name="remember">
                                <?php _e('Remember me', 'bluetown'); ?>
                            </label>
                        </div>
                    </div>

                    <div class="form-row">
                        <button class="btn btn--full" name="submit" value="1">
                            <span>
                                <?php _e('Log in', 'bluetown'); ?>
                            </span>
                        </button>

                        <?php wp_nonce_field('bto_login', 'bto_login_nonce'); ?>
                    </div>
                </form>
            </div>

            <div class="grid__col grid__col--6 tb1-hide">
                <div class="fancy-image">
                    <img loading="lazy" width="610" height="704" src="<?php echo BTO_IMG_URL; ?>/login.jpg" alt="">
                </div>
            </div>
        </div>
    <?php elseif ($action == 'forgot_password') : ?>
        <div class="section section--tiny section--full txt-center">
            <?php
                View::present('components/form-intro.twig', [
                    'title'       => $page_title,
                    'description' => $page_desc,
                ]);
            ?>
        </div>

        <?php if (!$notice->isEmpty()) : ?>
            <div class="mb-tiny section section--tiny section--full">
                <div class="form-notice form-notice--<?php echo esc_attr($notice->type()) ?>">
                    <p>
                        <?php echo $notice->message(); ?>
                    </p>
                </div>
            </div>
        <?php endif; ?>

        <form method="post" action="" class="section section--tiny section--full">
            <div class="form-row">
                <div class="form-field">
                    <label for="email" class="form-label">
                        <?php _e('Email address', 'bluetown'); ?>
                    </label>
                    <input id="email" type="email" name="email" required>
                </div>
            </div>

            <div class="form-row">
                <button class="btn btn--full" name="submit" value="1">
                    <span>
                        <?php _e('Get reset link', 'bluetown'); ?>
                    </span>
                </button>

                <?php wp_nonce_field('bto_forgot_password', 'bto_forgot_password_nonce'); ?>
            </div>

            <?php if ($login_page) : ?>
                <div class="form-row">
                    <p class="form-note">
                        <?php echo sprintf(__('Remembered your password? <a href="%s">Log in</a>', 'bluetown'), get_permalink($login_page)); ?>
                    </p>
                </div>
            <?php endif; ?>
        </form>
    <?php elseif ($action == 'reset_password' && $reset_validated === true) : ?>
        <div class="section section--tiny section--full txt-center">
            <?php
                View::present('components/form-intro.twig', [
                    'title'       => $page_title,
                    'description' => $page_desc,
                ]);
            ?>
        </div>

        <?php if (!$notice->isEmpty()) : ?>
            <div class="mb-tiny section section--tiny section--full">
                <div class="form-notice form-notice--<?php echo esc_attr($notice->type()) ?>">
                    <p>
                        <?php echo $notice->message(); ?>
                    </p>
                </div>
            </div>
        <?php endif; ?>

        <form method="post" action="" class="section section--tiny section--full">
            <div class="form-row">
                <div class="form-field">
                    <label for="password" class="form-label">
                        <?php _e('Password', 'bluetown'); ?>
                    </label>
                    <input id="password" type="password" name="password" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-field">
                    <label for="password-confirm" class="form-label">
                        <?php _e('Password (Confirm)', 'bluetown'); ?>
                    </label>
                    <input id="password-confirm" type="password" name="password_confirm" required>
                </div>
            </div>

            <div class="form-row">
                <button class="btn btn--full" name="submit" value="1">
                    <span>
                        <?php _e('Reset password', 'bluetown'); ?>
                    </span>
                </button>

                <?php wp_nonce_field('bto_reset_password', 'bto_reset_password_nonce'); ?>
            </div>
        </form>
    <?php endif; ?>
</section>

<?php
get_footer();
