<?php

/**
 * Default page template
 *
 * @package Bluetown
 *
 * @author Fhoke <hello@fhoke.com>
 */

get_header();
?>

<section class="section section--medium pv-medium">
    <?php while (have_posts()) : ?>
        <?php the_post(); ?>
        <div class="txt-styles">
            <?php the_content(); ?>
        </div>
    <?php endwhile; ?>
</section>

<?php get_footer(); ?>
