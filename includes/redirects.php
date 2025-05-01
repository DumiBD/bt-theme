<?php

/**
 * Assorted redirects
 *
 * @package Bluetown
 *
 * @author Fhoke <hello@fhoke.com>
 */

use Fhoke\Bluetown\Auth;
use Fhoke\Bluetown\JobApplicationManager;
use Fhoke\Bluetown\Mail\Mail;
use Fhoke\Bluetown\Mail\Mailer;
use Fhoke\Bluetown\Mail\MailgunSender;
use Fhoke\Bluetown\PostTypes\Job;
use Fhoke\Bluetown\PostTypes\JobApplication;
use Fhoke\Bluetown\UserManager;
use Noticeable\Notice;
use Noticeable\NoticeContent;

/**
 * Redirect users away from pages if logged in
 */
function bto_redirects_logged_in()
{
    $templates = [
        'templates/login.php',
        'templates/register.php',
    ];

    if (is_user_logged_in() && in_array(get_page_template_slug(), $templates)) {
        wp_redirect(home_url('account'));
        exit;
    }
}

add_action('template_redirect', 'bto_redirects_logged_in');

/**
 * Redirect users away from pages if logged out
 */
function bto_redirects_logged_out()
{
    $templates = [
        'templates/account.php',
        'templates/applied-jobs.php',
        'templates/saved-jobs.php',
    ];

    if (!is_user_logged_in() && in_array(get_page_template_slug(), $templates)) {
        wp_redirect(home_url('login'));
        exit;
    }
}

add_action('template_redirect', 'bto_redirects_logged_out');

/**
 * Log user in
 */
function bto_redirects_log_user_in()
{
    if (!bto_can_access_login_template()) {
        return;
    }

    if (!bto_form_has_been_submitted('submit', 'bto_login')) {
        return;
    }

    try {
        Auth::login(
            $_POST['email'] ?? '',
            $_POST['password'] ?? '',
            $_POST['remember'] ?? false,
        );

        wp_redirect(home_url('account'));
        exit;
    } catch (\Exception $e) {
        Notice::set(
            new NoticeContent($e->getMessage(), 'error')
        );

        wp_redirect(bto_current_url());
        exit;
    }
}

add_action('template_redirect', 'bto_redirects_log_user_in');

/**
 * Send password reset
 */
function bto_redirects_send_password_reset_email()
{
    $action = $_GET['action'] ?? '';

    if (!bto_can_access_login_template() || $action != 'forgot_password') {
        return;
    }

    if (!bto_form_has_been_submitted('submit', 'bto_forgot_password')) {
        return;
    }

    $email  = $_POST['email'] ?? '';

    if (!$email) {
        Notice::set(
            new NoticeContent(__("Please provide a valid email address."), 'error')
        );

        wp_redirect(bto_current_url());
        exit;
    }

    try {
        $user = get_user_by('email', $email);
        $key  = Auth::generatePasswordResetKey($email);

        Mail::setUp()->sendPasswordResetEmail('', $email, $user->user_login, $key);

        Notice::set(
            new NoticeContent(__("An email has been sent to you with a link to reset your password."), 'success')
        );

        wp_redirect(home_url('login'));
        exit;
    } catch (\Exception $e) {
        Notice::set(
            new NoticeContent($e->getMessage(), 'error')
        );

        wp_redirect(bto_current_url());
        exit;
    }
}

add_action('template_redirect', 'bto_redirects_send_password_reset_email');

/**
 * Send password reset
 */
function bto_redirects_reset_password()
{
    $action = $_GET['action'] ?? '';

    if (!bto_can_access_login_template() || $action != 'reset_password') {
        return;
    }

    if (!bto_form_has_been_submitted('submit', 'bto_reset_password')) {
        return;
    }

    $password         = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    if (!$password || !$password_confirm) {
        Notice::set(
            new NoticeContent(__("Please provide a new password."), 'error')
        );

        wp_redirect(bto_current_url());
        exit;
    }

    if ($password !== $password_confirm) {
        Notice::set(
            new NoticeContent(__("The passwords must match."), 'error')
        );

        wp_redirect(bto_current_url());
        exit;
    }

    $user = get_user_by('login', $_GET['login'] ?? '');
    $key  = $_GET['key'] ?? '';

    try {
        if (!Auth::checkPasswordResetKey($user->user_login, $key)) {
            throw new \Exception("There's an error with the link. Please try again.");
        }

        Auth::setPassword($user->ID, $password);

        Notice::set(
            new NoticeContent(__("Your password has been succesfully reset."), 'success')
        );

        wp_redirect(home_url('login'));
        exit;
    } catch (\Exception $e) {
        Notice::set(
            new NoticeContent($e->getMessage(), 'error')
        );

        wp_redirect(bto_current_url());
        exit;
    }
}

add_action('template_redirect', 'bto_redirects_reset_password');

/**
 * Register user account
 */
function bto_redirects_register_user()
{
    if (!bto_can_access_register_template()) {
        return;
    }

    if (!bto_form_has_been_submitted('submit', 'bto_register')) {
        return;
    }

    try {
        (new UserManager())->create([
            'email'            => sanitize_email($_POST['email'] ?? ''),
            'password'         => $_POST['password'] ?? '',
            'password_confirm' => $_POST['password_confirm'] ?? '',
            'first_name'       => sanitize_text_field($_POST['first_name'] ?? ''),
            'last_name'        => sanitize_text_field($_POST['last_name'] ?? ''),
            'meta'             => [
                'phone'                => sanitize_text_field($_POST['phone'] ?? ''),
                'interested_in_terms'  => $_POST['interested_in_terms'] ?? [],
                'email_alerts_opt_out' => sanitize_text_field($_POST['email_alerts_opt_out'] ?? ''),
            ],
            'files' => [
                'cv' => $_FILES['cv'] ?? [],
            ],
        ]);

        Notice::set(
            new NoticeContent(__("Your account has been succesfully created. Please login to continue."), 'success')
        );

        wp_redirect(home_url('login'));
        exit;
    } catch (\Exception $e) {
        Notice::set(
            new NoticeContent($e->getMessage(), 'error')
        );
    }
}

add_action('template_redirect', 'bto_redirects_register_user');

/**
 * Update user account
 */
function bto_redirects_update_user_account()
{
    if (!bto_can_access_account_template()) {
        return;
    }

    if (!bto_form_has_been_submitted('submit', 'bto_update_account')) {
        return;
    }

    try {
        (new UserManager())->update([
            'id'                   => \get_current_user_id(),
            'email'                => sanitize_email($_POST['email'] ?? ''),
            'password'             => $_POST['password'] ?? '',
            'password_confirm'     => $_POST['password_confirm'] ?? '',
            'first_name'           => sanitize_text_field($_POST['first_name'] ?? ''),
            'last_name'            => sanitize_text_field($_POST['last_name'] ?? ''),
            'meta'                 => [
                'phone'                => sanitize_text_field($_POST['phone'] ?? ''),
                'interested_in_terms'  => $_POST['interested_in_terms'] ?? [],
                'email_alerts_opt_out' => sanitize_text_field($_POST['email_alerts_opt_out'] ?? ''),
                'address'              => sanitize_text_field($_POST['address'] ?? ''),
                'address_town'         => sanitize_text_field($_POST['address_town'] ?? ''),
                'address_county'       => sanitize_text_field($_POST['address_county'] ?? ''),
                'address_postcode'     => sanitize_text_field($_POST['address_postcode'] ?? ''),
                'address_country'      => sanitize_text_field($_POST['address_country'] ?? ''),
                'linkedin_url'         => sanitize_url($_POST['linkedin_url'] ?? ''),
            ],
            'files'                => [
                'cv' => $_FILES['cv'] ?? [],
            ],
        ]);

        Notice::set(
            new NoticeContent(__("Your account has been succesfully updated."), 'success')
        );

        wp_redirect(bto_current_url());
        exit;
    } catch (\Exception $e) {
        Notice::set(
            new NoticeContent($e->getMessage(), 'error')
        );
    }
}

add_action('template_redirect', 'bto_redirects_update_user_account');

/**
 * Submit job application
 */
function bto_redirects_apply_for_job()
{
    if (!bto_form_has_been_submitted('submit', 'bto_apply_for_job')) {
        return;
    }

    $job = new Job(get_the_ID());

    try {
        $first_name = sanitize_text_field($_POST['first_name'] ?? '');
        $last_name  = sanitize_text_field($_POST['last_name'] ?? '');

        $job_application_id = (new JobApplicationManager())->create([
            'post_title'       => "{$first_name} {$last_name}",
            'create_account'   => sanitize_text_field($_POST['create_account'] ?? ''),
            'password'         => $_POST['password'] ?? '',
            'password_confirm' => $_POST['password_confirm'] ?? '',
            'meta'             => [
                'user_id'          => get_current_user_id(),
                'first_name'       => $first_name,
                'last_name'        => $last_name,
                'email'            => sanitize_email($_POST['email'] ?? ''),
                'phone'            => sanitize_text_field($_POST['phone'] ?? ''),
                'cover_message'    => sanitize_textarea_field($_POST['cover_message'] ?? ''),
                'linkedin_url'     => sanitize_url($_POST['linkedin_url'] ?? ''),
            ],
            'files'                => [
                'cv' => $_FILES['cv'] ?? [],
            ],
        ]);

        Notice::set(
            new NoticeContent(__("Your application has been successfully submitted."), 'success')
        );

        Mail::setSender(new Mailer())->sendJobApplicationEmail(
            $job,
            new JobApplication($job_application_id)
        );

        wp_redirect(bto_current_url() . "#apply-form-{$job->id()}");
        exit;
    } catch (\Exception $e) {
        Notice::set(
            new NoticeContent($e->getMessage(), 'error')
        );
    }
}

add_action('template_redirect', 'bto_redirects_apply_for_job');
