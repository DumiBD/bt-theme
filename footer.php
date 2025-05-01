<?php

/**
 * Theme footer
 *
 * @package Bluetown
 *
 * @author Fhoke <hello@fhoke.com>
 */

use Fhoke\Slate\ACF\ACF;
use Fhoke\Slate\Templating\Views\View;

?>
            <?php
            View::present('sections/call-to-action.twig', [
                'html_id' => 'cta',
                'txt'     => ACF::field('cta_override') ? ACF::field('cta_txt', null, false) : ACF::settingsField('cta_txt', false),
            ]);
            ?>

            <?php
            View::present('blocks/footer.twig', [
                'locations'   => ACF::settingsField('footer_locations'),
                'socialLinks' => bto_social_links(),
                'menu'        => wp_nav_menu([
                    'theme_location'  => 'nav-3',
                    'container'       => 'nav',
                    'container_class' => 'site-footer__menu',
                    'fallback_cb'     => false,
                    'depth'           => 1,
                    'echo'            => false,
                ]),
            ]);
            ?>
        </div><!--end .page-wrap-->

        <?php wp_footer(); ?>

        <?php echo ACF::settingsField('global_code_footer'); ?>
        <?php echo ACF::field('page_code_footer'); ?>
    </body>
</html>
