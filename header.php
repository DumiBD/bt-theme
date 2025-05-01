<?php

/**
 * Theme header
 *
 * @package Bluetown
 *
 * @author Fhoke <hello@fhoke.com>
 */

use Fhoke\Slate\ACF\ACF;
use Fhoke\Slate\ACF\Button;
use Fhoke\Slate\Templating\Views\View;
use Fhoke\Bluetown\Banner;

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
    <head>
        <meta charset="<?php bloginfo('charset'); ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=0, viewport-fit=cover">
        <meta http-equiv="X-UA-Compatible" content="IE=Edge">

        <?php wp_head(); ?>

        <?php echo ACF::settingsField('global_code_head'); ?>
        <?php echo ACF::field('page_code_head'); ?>
    </head>
    <body <?php body_class(); ?>>
        <?php echo ACF::settingsField('global_code_body'); ?>
        <?php echo ACF::field('page_code_body'); ?>

        <div class="page-wrap">
            <?php do_action('slate_before_header'); ?>

           <?php
            View::present('blocks/header.twig', [
                'menu' => wp_nav_menu([
                    'theme_location'  => 'nav-1',
                    'container'       => 'nav',
                    'container_class' => 'site-menu site-menu--fancy',
                    'fallback_cb'     => false,
                    'depth'           => 2,
                    'echo'            => false,
                ]),
                'menu_2' => wp_nav_menu([
                    'theme_location'  => is_user_logged_in() ? 'nav-4' : 'nav-2',
                    'container'       => 'nav',
                    'container_class' => 'site-menu',
                    'fallback_cb'     => false,
                    'depth'           => 2,
                    'echo'            => false,
                ]),
                'hide_menu'        => ACF::field('hide_page_menu'),
                'hide_menu_button' => new Button(ACF::field('menu_replacement_button') ?: []),
            ]);
            ?>

            <?php
            View::present('blocks/mobile-menu.twig', [
                'menu' => wp_nav_menu([
                    'theme_location'  => 'nav-1',
                    'container'       => 'nav',
                    'container_class' => 'mobile-menu__items',
                    'fallback_cb'     => false,
                    'depth'           => 1,
                    'echo'            => false,
                ]),
                'menu_2'      => wp_nav_menu([
                    'theme_location'  => 'nav-6',
                    'container'       => 'nav',
                    'container_class' => 'mobile-menu__btns',
                    'fallback_cb'     => false,
                    'depth'           => 1,
                    'echo'            => false,
                ]),
                'socialLinks' => bto_social_links(),
                'menu_3'      => wp_nav_menu([
                    'theme_location'  => 'nav-5',
                    'container'       => 'nav',
                    'container_class' => 'mobile-menu__items-alt',
                    'fallback_cb'     => false,
                    'depth'           => 1,
                    'echo'            => false,
                ]),
            ]);
            ?>

            <?php do_action('slate_after_header'); ?>

            <?php
            View::present('blocks/banner.twig', [
                'banner' => (new Banner())
                    ->withIcon()
                    ->withTitle()
                    ->withSubtitle()
                    ->withDescription()
                    ->withButton()
                    ->withLink()
                    ->withImage()
                    ->witImageCards()
                    ->withBackgroundColor()
                    ->withBackgroundImage(),
            ]);
            ?>

            <?php do_action('slate_after_banner'); ?>
