<?php

use Fhoke\Slate\ACF\ACF;
use Fhoke\Slate\Templating\Views\View;
use Fhoke\Slate\Utilities;

function bto_alter_menu_items($items, $args)
{
    $icons = [
        'user'        => Utilities::svg('user', 'icons'),
        'arrow-right' => Utilities::svg('arrow-right', 'icons'),
        'caret-down'  => Utilities::svg('caret-down', 'icons'),
    ];

    if ($args->theme_location == 'nav-1') {
        foreach ($items as &$item) {
            $item->title = "<span class='menu-item-text'>{$item->title}</span>";
        }
    }

    if ($args->theme_location == 'nav-2' || $args->theme_location == 'nav-4') {
        foreach ($items as &$item) {
            $style       = ACF::field('style', $item);
            $button_type = ACF::field('button_type', $item);
            $icon        = ACF::field('icon', $item);
            $display     = ACF::field('display', $item);

            if ($style == 'option_2') {
                if ($button_type == 'standard') {
                    $item->classes[] = 'menu-item-btn';
                } elseif ($button_type == 'arrow') {
                    $item->classes[] = 'menu-item-block fancy-link';
                    $item->title     = "<span class='fancy-icon'>{$icons['arrow-right']}</span><span>{$item->title}</span>";
                }
            } elseif ($style == 'option_3' && array_key_exists($icon, $icons)) {
                $item->classes[] = 'menu-item-with-icon';
                $item->title     = $icons[$icon];
            }

            if (in_array('option_1', $display)) {
                $item->classes[] = 'menu-item-desktop-show';
            }
        }
    }

    if ($args->theme_location == 'nav-1' || $args->theme_location == 'nav-2' || $args->theme_location == 'nav-4') {
        foreach ($items as &$item) {
            if (in_array('menu-item-has-children', $item->classes)) {
                $item->title = "{$item->title}<span class='menu-item-action'>{$icons['caret-down']}</span>";
            }
        }
    }

    return $items;
}

add_filter('wp_nav_menu_objects', 'bto_alter_menu_items', 10, 2);

function bto_add_menu_items($items, $args)
{
    if ($args->theme_location == 'nav-2' || $args->theme_location == 'nav-4') {
        $menu_toggle = View::render('elements/toggle-menu.twig');

        $items .= "<li class='menu-item menu-item-tablet-show'>{$menu_toggle}</li>";
    }

    if ($args->theme_location == 'nav-3') {
        $devAttrLink = Utilities::devAttrLink();

        if ($devAttrLink) {
            $items .= "<li class='menu-item menu-item-dev-attr'>{$devAttrLink}</li>";
        }
    }

    return $items;
}

add_filter('wp_nav_menu_items', 'bto_add_menu_items', 10, 2);
