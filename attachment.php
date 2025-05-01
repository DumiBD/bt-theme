<?php

/**
 * File attachment template
 * * This is used when an image is loaded as a post
 *
 * @package Bluetown
 *
 * @author Fhoke <hello@fhoke.com>
 */

get_header();
?>

<?php while (have_posts()) : ?>
    <?php the_post(); ?>

    <div class="attachment-img">
        <?php echo wp_get_attachment_image(get_the_id(), 'full'); ?>
    </div>
<?php endwhile; ?>

<?php get_footer(); ?>
