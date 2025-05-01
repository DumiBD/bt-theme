<?php

namespace Fhoke\Bluetown;

class JobApplicationManager
{
    protected function createAccount(array $args)
    {
        return (new UserManager())
            ->create([
                'email'            => sanitize_email($args['meta']['email'] ?? ''),
                'password'         => $args['password'],
                'password_confirm' => $args['password_confirm'],
                'first_name'       => sanitize_text_field($args['meta']['first_name'] ?? ''),
                'last_name'        => sanitize_text_field($args['meta']['last_name'] ?? ''),
                'meta'             => [
                    'phone' => sanitize_text_field($args['meta']['phone'] ?? ''),
                ],
            ]);
    }

    public function create(array $args)
    {
        Validator::requiredFields([
            'first_name',
            'last_name',
            'phone',
            'email',
            'cover_message',
        ], $args['meta'] ?? []);

        $user_id = \get_current_user_id();

        if (($args['create_account'] ?? 'off') == 'on') {
            $user_id = $this->createAccount($args);
        }

        $user_obj = new User($user_id);

        $data = [
            'post_type'   => 'job_application',
            'post_title'  => $args['post_title'] ?? '',
            'post_status' => 'publish',
        ];

        $meta = $args['meta'] ?? [];

        $meta['user_id'] = $user_obj->id();

        $uploaded_cv_url = \bto_upload_cv_file($args['files']['cv'] ?? [])['url'] ?? '';

        if ($uploaded_cv_url) {
            $meta['cv_url'] = $uploaded_cv_url;
        }

        if (\is_user_logged_in()) {
            if (!$uploaded_cv_url && $user_obj->cvUrl) {
                $meta['cv_url'] = $user_obj->cvUrl;
            }
        }

        $meta['job_id'] = \get_the_ID();

        $post_id = \wp_insert_post(
            \array_merge(
                \array_filter($data),
                [
                    'meta_input' => $meta,
                ],
            )
        );

        if (\is_wp_error($post_id)) {
            throw new \Exception($post_id->get_error_message());
        }

        $user_obj->addJobToApplied(\get_the_ID());

        return $post_id;
    }
}
