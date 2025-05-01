<?php

/**
 * Default archive template
 *
 * @package Bluetown
 *
 * @author Fhoke <hello@fhoke.com>
 */

use Fhoke\Slate\Pagination;
use Fhoke\Slate\Templating\Views\View;
use Fhoke\Slate\Utilities;

get_header();
?>

<?php if (have_posts()) : ?>
    <section class="section section--large pb-large">
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
        <?php View::present('components/no-posts-found.twig');?>
    </section>
<?php endif; ?>

<?php get_footer(); ?>
