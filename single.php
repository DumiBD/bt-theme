<?php

/**
 * Default post template
 *
 * @package Bluetown
 *
 * @author Fhoke <hello@fhoke.com>
 */

use Fhoke\Bluetown\Queries;
use Fhoke\Slate\Templating\Views\View;
use Fhoke\Slate\Utilities;

get_header();

$related_query = Queries::related(get_the_id());
?>

<section class="pb-large core-section--shapes-bottom-right">
    <div class="content">
        <div class="content__side">
            <div class="sticky">
                <h6 class="txt-small mb-mini txt-uppercase m2-txt-center">
                    <?php _e('Share', 'bluetown'); ?>
                </h6>
                <?php echo bto_share_links(); ?>
            </div>
        </div>
        <div class="content__main">
            <?php while (have_posts()) : ?>
                <?php the_post(); ?>
                <article class="txt-styles">
                    <?php the_content(); ?>
                </article>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<?php if ($related_query->have_posts()) : ?>
    <section class="pv-large bg-dark core-section--shapes-top-right">
        <div class="section section--large">
            <div class="mb-medium  txt-center txt-light">
                <h2>
                    <?php _e("Related reads", 'bluetown'); ?>
                </h2>
            </div>

            <div class="blog-grid">
                <?php while ($related_query->have_posts()) : ?>
                    <?php
                        $related_query->the_post();
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

            <div class="mt-medium txt-center">
                <a class="btn" href="<?php echo get_permalink(get_option('page_for_posts')); ?>">
                    <span>
                        <?php _e('All Posts', 'bluetown'); ?>
                    </span>
                </a>
            </div>
        </div>
    </section>
<?php endif; ?>

<?php get_footer(); ?>
