<?php

/**
 * Default archive template
 *
 * @package Bluetown
 *
 * @author Fhoke <hello@fhoke.com>
 */

use Fhoke\Bluetown\PostTypes\Job;
use Fhoke\Slate\Pagination;
use Fhoke\Slate\Templating\Views\View;
use Fhoke\Slate\Utilities;

get_header();
?>

<?php if (have_posts()) : ?>
    <section class="section section--large pb-large">
        <?php if (bto_is_job_search_page()) : ?>
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
        <?php else : ?>
            <div class="blog-grid">
                <?php while (have_posts()) : ?>
                    <?php
                        the_post();
                    ?>
                    <div class="blog-grid__item">
                        <?php
                            View::present('components/post-preview.twig', [
                                'image_html' => bto_retina_thumb(['size' => 'Headshot']),
                                'term_html'  => Utilities::term(),
                                'title'      => get_the_title(),
                                'url'        => get_permalink(),
                                'date'       => Utilities::datetime('date'),
                            ]);
                        ?>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>

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
        <?php if (bto_is_job_search_page()) : ?>
            <?php View::present('components/no-jobs-found.twig');?>
        <?php else : ?>
            <?php View::present('components/no-posts-found.twig');?>
        <?php endif; ?>
    </section>
<?php endif; ?>

<?php get_footer(); ?>
