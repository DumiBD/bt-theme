<?php

namespace Fhoke\Bluetown;

use Fhoke\Bluetown\Mail\Mail;

class UserManager
{
    public function create(array $args)
    {
        Validator::requiredFields([
            'first_name',
            'last_name',
            'email',
            'password',
            'password_confirm',
        ], $args);

        Validator::passwordsMatch($args['password'], $args['password_confirm']);

        $data = [
            'first_name' => $args['first_name'],
            'last_name'  => $args['last_name'],
            'user_email' => $args['email'],
            'user_pass'  => $args['password'],
        ];

        $data['user_login'] = \bto_create_username_from_name($data['first_name'], $data['last_name']);

        $meta = $args['meta'] ?? [];

        $uploaded_cv_url = \bto_upload_cv_file($args['files']['cv'] ?? [])['url'] ?? '';

        if ($uploaded_cv_url) {
            $meta['cv_url'] = $uploaded_cv_url;
        }

        $user_id = \wp_insert_user(
            \array_merge(
                \array_filter($data),
                [
                    'meta_input' => $meta,
                ],
            )
        );

        if (\is_wp_error($user_id)) {
            throw new \Exception($user_id->get_error_message());
        }

        Mail::setUp()->sendWelcomeEmail("{$args['first_name']} {$args['last_name']}", $args['email']);

        return $user_id;
    }

    public function update(array $args)
    {
        Validator::requiredFields([
            'id',
            'first_name',
            'last_name',
            'email',
        ], $args);

        Validator::passwordsMatch($args['password'], $args['password_confirm']);

        $data = [
            'ID'         => $args['id'],
            'first_name' => $args['first_name'],
            'last_name'  => $args['last_name'],
            'user_email' => $args['email'],
            'user_pass'  => $args['password'] ?? '',
        ];

        $meta = $args['meta'] ?? [];

        $uploaded_cv_url = \bto_upload_cv_file($args['files']['cv'] ?? [])['url'] ?? '';

        if ($uploaded_cv_url) {
            $meta['cv_url'] = $uploaded_cv_url;
        }

        $user_id = \wp_update_user(
            \array_merge(
                \array_filter($data),
                [
                    'meta_input' => $meta,
                ],
            )
        );

        if (\is_wp_error($user_id)) {
            throw new \Exception($user_id->get_error_message());
        }

        return $user_id;
    }
}
