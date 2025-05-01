<?php

use Fhoke\Bluetown\Mail\Mail;
use Fhoke\Bluetown\Mail\MailgunSender;
use Fhoke\Bluetown\PostTypes\Job;
use Fhoke\Bluetown\User;

/**
 * Send weekly emailer
 */
function bto_send_weekly_emailer()
{
    $users_query = new WP_User_Query([
        'fields'     => ['id'],
        'meta_query' => [
            'relation' => 'AND',
            [
                'relation' => 'OR',
                [
                    'key'     => 'email_alerts_opt_out',
                    'compare' => '!=',
                    'value'   => 'on',
                ],
                [
                    'key'     => 'disable_email_updates',
                    'compare' => '!=',
                    'value'   => true,
                ],
            ],
            [
                'relation' => 'OR',
                [
                    'key' => 'interested_in_terms',
                ],
                [
                    'key' => 'saved_job_sector',
                ],
                [
                    'key' => 'saved_job_location',
                ],
                [
                    'key' => 'saved_job_type',
                ],
            ],
        ],
    ]);

    $users = $users_query->results;

    if (!$users) {
        return;
    }

    $user_ids = array_map(function (object $user) {
        return $user->id;
    }, $users);

    array_map(function (int $user_id) {
        $user_obj = new User($user_id);

        if (!$user_obj->interestedInTerms) {
            return;
        }

        //---- "Interested in" text
        $interested_job_taxonomy_labels = '';

        $interested_terms = get_terms([
            'taxonomy'   => ['wpbb_job_industry', 'wpbb_job_location', 'wpbb_job_type'],
            'hide_empty' => false,
            'include'    => $user_obj->interestedInTerms,
        ]);

        $interested_terms_industry = array_filter($interested_terms, function (\WP_Term $term) {
            return $term->taxonomy == 'wpbb_job_industry';
        });

        $interested_terms_locations = array_filter($interested_terms, function (\WP_Term $term) {
            return $term->taxonomy == 'wpbb_job_location';
        });

        $interested_terms_type = array_filter($interested_terms, function (\WP_Term $term) {
            return $term->taxonomy == 'wpbb_job_type';
        });

        if ($interested_terms_industry || $interested_terms_type) {
            $interested_job_taxonomy_labels = "You're interested in jobs which are " . implode(' and ', array_map(function (WP_Term $term) {
                return "\"$term->name\"";
            }, array_merge($interested_terms_industry, $interested_terms_type)));

            if ($interested_terms_locations) {
                $interested_job_taxonomy_labels .= " in " . implode(' and ', array_map(function (WP_Term $term) {
                    return "\"$term->name\"";
                }, $interested_terms_locations));
            }
        }

        //---- Jobs content
        $users_jobs = new WP_Query(
            [
                'fields'         => 'ids',
                'post_type'      => 'wpbb_job',
                'post_status'    => ['publish'],
                'posts_per_page' => 20,
                'tax_query'      => [
                    'relation' => 'OR',
                    [
                        'taxonomy' => 'wpbb_job_industry',
                        'terms'    => array_map(function (WP_Term $term) {
                            return $term->term_id;
                        }, $interested_terms_industry),
                    ],
                    [
                        'taxonomy' => 'wpbb_job_location',
                        'terms'    => array_map(function (WP_Term $term) {
                            return $term->term_id;
                        }, $interested_terms_locations),
                    ],
                    [
                        'taxonomy' => 'wpbb_job_type',
                        'terms'    => array_map(function (WP_Term $term) {
                            return $term->term_id;
                        }, $interested_terms_type),
                    ],
                ],
                'date_query'     => [
                    [
                        'after' => '1 week ago',
                    ],
                ],
            ]
        );

        $jobs = '';

        if (!$users_jobs->posts) {
            return;
        }

        foreach ($users_jobs->posts as $job_id) {
            $job = (new Job($job_id))
                ->withSalary();

            $jobs .= <<< EOT
                <h3><a style='color: #000;' href='{$job->url()}' target='_blank'>{$job->title()}</a></h3>
                <p>
                    <strong>Salary:</strong> {$job->salary}<br>
                </p>
                <p><strong><a href='{$job->url()}' target='_blank'>Apply &rarr;</a></strong></p>
                <hr>
            EOT;
        }

        //---- Email content
        $content = <<< EOT
            {$interested_job_taxonomy_labels}
            <hr>
            {$jobs}
        EOT;

        //---- Send email
        Mail::setSender(new MailgunSender())->sendWeeklyJobs(
            $user_obj->fullName(),
            $user_obj->email(),
            $content
        );
    }, $user_ids);
}

add_action('bto_weekly_emailer_action', 'bto_send_weekly_emailer');

/**
 * Create wp-cron for sending weekly emailer
 */
function bto_add_weekly_emailer_schedule()
{
    if (!wp_next_scheduled('bto_weekly_emailer_action')) {
        wp_schedule_event(strtotime((new DateTime('next monday 8am'))->format('Y-m-d H:i:s')), 'weekly', 'bto_weekly_emailer_action');
    }
}

add_action('init', 'bto_add_weekly_emailer_schedule');
