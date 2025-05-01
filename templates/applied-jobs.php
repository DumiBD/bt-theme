<?php

/**
 * Template Name: Account – Applied Jobs
 *
 * @package Bluetown
 *
 * @author Fhoke <hello@fhoke.com>
 */

use Fhoke\Bluetown\PostTypes\Job;
use Fhoke\Bluetown\User;
use Fhoke\Slate\Pagination;
use Fhoke\Slate\Templating\Views\View;

get_header();

$user_obj = new User(get_current_user_id());

$jobs = new WP_Query([
    'post_type' => 'wpbb_job',
    'post__in'  => $user_obj->appliedJobsIds,
]);
?>

<?php if ($user_obj->appliedJobsIds && $jobs->have_posts()) : ?>
    <section class="section section--large pv-large">
        <div class="mb-small">
            <p class="txt-small txt-center">
                <?php echo sprintf(_n('Showing %s job', 'Showing %s jobs', $jobs->found_posts, 'bluetown'), $jobs->found_posts); ?>
            </p>
        </div>

        <div class="job-grid">
            <?php while ($jobs->have_posts()) : ?>
                <?php
                    $jobs->the_post();
                ?>
                <div class="job-grid__item">
                    <?php
                       $job = (new Job(get_the_ID()))
                           ->withIndustries()
                           ->withLocations()
                           ->withTypes()
                           ->withSalary();

                        View::present('components/job-preview.twig', [
                            'job' => $job,
                        ]);
                    ?>
                </div>
            <?php endwhile; ?>
        </div>

        <?php
            View::present('components/pagination.twig', [
                'query'    => $wp_query,
                'prev_url' => Pagination::url($wp_query, 'prev'),
                'next_url' => Pagination::url($wp_query, 'next'),
            ]);
        ?>
    </section>
<?php else : ?>
    <section class="section section--medium pv-large">
        <div class="txt-styles txt-center">
            <div class="boxed">
                <h2 class="h3">
                    <?php _e('No Jobs Found', 'bluetown'); ?>
                </h2>
                <p class="txt-large">
                    <?php echo sprintf(__("Try viewing <a href='%s'>all jobs</a> or going to the <a href='%s'>homepage</a>.", 'bluetown'), get_post_type_archive_link('wpbb_job'), home_url()); ?>
                </p>
            </div>
        </div>
    </section>
<?php endif; ?>

<?php get_footer(); ?>
