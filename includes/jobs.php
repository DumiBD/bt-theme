<?php

use Fhoke\Bluetown\PostTypes\Job;
use Fhoke\Slate\Pagination;
use Fhoke\Slate\Templating\Views\View;

?>

<?php if (have_posts()) : ?>
    <section class="section section--large pb-large">
        <div class="mb-small">
            <p class="txt-small txt-center">
                <?php echo sprintf(_n('Showing %s job', 'Showing %s jobs', $wp_query->found_posts, 'bluetown'), $wp_query->found_posts); ?>
            </p>
        </div>

        <div class="job-grid">
            <?php while (have_posts()) : ?>
                <?php
                    the_post();
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
    <section class="section section--medium pb-large">
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
