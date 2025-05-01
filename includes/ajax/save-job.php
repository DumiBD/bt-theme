<?php

use Fhoke\Bluetown\User;

/**
 * Save job to user account
 */
function bto_ajax_save_job()
{
    $job_id   = $_POST['job_id'] ?? null;
    $user_obj = new User(get_current_user_id());

    try {
        $requires_login = false;
        $added          = false;
        $removed        = false;

        if (!is_user_logged_in()) {
            $requires_login = true;
        } elseif (in_array($job_id, $user_obj->savedJobsIds)) {
            $user_obj->unsaveJob([$job_id]);

            $removed = true;
        } else {
            $user_obj->saveJob([$job_id]);

            $added = true;
        }

        wp_send_json_success([
            'message'        => __('Job successfully saved.', 'bluetown'),
            'requires_login' => $requires_login,
            'added'          => $added,
            'removed'        => $removed,
        ]);

        wp_die();
    } catch (\Exception $e) {
        wp_send_json_error([
            'message' => __('Sorry, there was an error saving the job.', 'bluetown'),
        ]);

        wp_die();
    }
}

add_action('wp_ajax_nopriv_bto_save_job', 'bto_ajax_save_job');
add_action('wp_ajax_bto_save_job', 'bto_ajax_save_job');
