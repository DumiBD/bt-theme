<?php

namespace Fhoke\Bluetown;

class Auth
{
    public static function login(string $email, string $password, bool $remember = false)
    {
        $user = \wp_signon([
            'user_login'    => $email,
            'user_password' => $password,
            'remember'      => $remember,
        ]);

        if (\is_wp_error($user)) {
            if (isset($user->errors['incorrect_password'])) {
                throw new \InvalidArgumentException("<strong>Error</strong>: those details weren't recognised.");
            }

            throw new \InvalidArgumentException($user->get_error_message());
        }

        return \wp_set_current_user($user->ID, $user->display_name);
    }

    public static function generatePasswordResetKey(string $email)
    {
        $user = \get_user_by('email', $email);

        if (!$user) {
            throw new \InvalidArgumentException("No user was found with email address.");
        }

        \do_action('lostpassword_post');

        \do_action('retrieve_password', $user->user_login);

        if (!\apply_filters('allow_password_reset', true, $user->ID)) {
            throw new \Exception("You're not currently allowed to reset your password.");
        }

        $key = \wp_generate_password(20, false);

        \do_action('retrieve_password_key', $user->user_login, $key);

        global $wpdb;

        require_once ABSPATH . 'wp-includes/class-phpass.php';
        $wp_hasher = new \PasswordHash(8, true);
        $hashed    = $wp_hasher->HashPassword($key);

        $wpdb->update(
            $wpdb->users,
            [
                'user_activation_key' => $hashed,
            ],
            [
                'user_login' => $user->user_login,
            ]
        );

        return $key;
    }

    public static function checkPasswordResetKey(string $username, string $key)
    {
        $user = \get_user_by('login', $username);

        if (!$user) {
            return;
        }

        $user_key = \get_user_option('user_activation_key', $user->ID);

        require_once ABSPATH . 'wp-includes/class-phpass.php';
        $wp_hasher = new \PasswordHash(8, true);

        if ($wp_hasher->CheckPassword($key, $user_key)) {
            return true;
        }
    }

    public static function setPassword(int $user_id, string $password)
    {
        \wp_set_password($password, $user_id);
    }
}
