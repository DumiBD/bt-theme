<?php

namespace Fhoke\Slate\Templating\Views;

use Fhoke\Slate\ACF\Button;
use Fhoke\Slate\ACF\Link;
use Fhoke\Slate\Utilities;

trait ViewsFunctions
{
    /**
     * @var \Twig\Environment $twig
     */
    protected $twig;

    protected $allowed = [
        'is_safe' => ['html'],
    ];

    /**
     * Add custom functions
     */
    protected function addFunctions()
    {
        //---- Run a shortcode
        $this->twig->addFunction(
            new \Twig\TwigFunction(
                'do_shortcode',
                function (string $content = null) {
                    if (!$content) {
                        return '';
                    }

                    return \do_shortcode($content);
                },
                $this->allowed
            )
        );

        //---- Get SVG code
        $this->twig->addFunction(
            new \Twig\TwigFunction(
                'svg',
                function (string $filename = null, $directory = null) {
                    if (!$filename) {
                        return '';
                    }

                    return Utilities::svg($filename, $directory);
                },
                $this->allowed
            )
        );

        //---- Apply a WordPress filter
        $this->twig->addFunction(
            new \Twig\TwigFunction(
                'apply_filters',
                function (string $filter = null, $value = null) {
                    if (!$filter || !$value) {
                        return;
                    }

                    return \apply_filters($filter, $value);
                },
                $this->allowed
            )
        );

        //---- Format WYSIWYG content
        $this->twig->addFunction(
            new \Twig\TwigFunction(
                'wysiwyg',
                function (string $value = null) {
                    return $value;
                },
                $this->allowed
            )
        );

        //---- Call a WordPress action
        $this->twig->addFunction(
            new \Twig\TwigFunction(
                'do_action',
                function (string $hook = null, ...$args) {
                    if (!$hook) {
                        return;
                    }

                    return \do_action($hook, ...$args);
                },
                $this->allowed
            )
        );

        //---- Output a Gravity Form
        $this->twig->addFunction(
            new \Twig\TwigFunction(
                'gravity_form',
                function (int $form_id = null, bool $display_title = false, bool $display_description = false, bool $ajax = true) {
                    return \gravity_form(
                        $form_id,
                        $display_title,
                        $display_description,
                        false,
                        null,
                        $ajax,
                        1,
                        false
                    );
                },
                $this->allowed
            )
        );

        //---- Check if array contains item
        $this->twig->addFunction(
            new \Twig\TwigFunction(
                'contains',
                function (array $arr = [], $item = null) {
                    if (!$arr) {
                        return false;
                    }

                    return \in_array($item, $arr);
                },
                $this->allowed
            )
        );

        //---- Get HTML tag for CMS image
        $this->twig->addFunction(
            new \Twig\TwigFunction(
                'cms_image',
                function (array $img = [], string $size = null) {
                    if (!$img) {
                        return '';
                    }

                    return Utilities::cmsImage($img, $size);
                },
                $this->allowed
            )
        );

        //---- Get SVG code from CMS image
        $this->twig->addFunction(
            new \Twig\TwigFunction(
                'cms_svg',
                function (array $img = [], string $size = null) {
                    if (!$img) {
                        return '';
                    }

                    return Utilities::svgFromCms($img, $size);
                },
                $this->allowed
            )
        );

        //---- Create new Link object
        $this->twig->addFunction(
            new \Twig\TwigFunction(
                'Link',
                function (array $field = [], array $args = []) {
                    if (!$field) {
                        return;
                    }

                    return new Link($field, $args);
                },
                $this->allowed
            )
        );

        //---- CMS button
        $this->twig->addFunction(
            new \Twig\TwigFunction(
                'cms_button',
                function (array $field = [], array $args = []) {
                    if (!$field) {
                        return;
                    }

                    return (new Button($field, $args))->render();
                },
                $this->allowed
            )
        );

        //---- Get post meta field
        $this->twig->addFunction(
            new \Twig\TwigFunction(
                'get_post_meta',
                function (int $post_id = null, string $key = null, bool $single = true) {
                    if (!$post_id || !$key) {
                        return;
                    }

                    return \get_post_meta($post_id, $key, $single);
                },
                $this->allowed
            )
        );

        //---- Get post thumbnail HTML
        $this->twig->addFunction(
            new \Twig\TwigFunction(
                'get_the_post_thumbnail',
                function (int $post_id = null, string $size = null, array $attrs = []) {
                    if (!$post_id || !$size) {
                        return '';
                    }

                    return \get_the_post_thumbnail($post_id, $size, $attrs);
                },
                $this->allowed
            )
        );

        //---- Get post URL
        $this->twig->addFunction(
            new \Twig\TwigFunction(
                'get_permalink',
                function (int $post_id = null, bool $leavename = false) {
                    if (!$post_id) {
                        return '';
                    }

                    return \get_permalink($post_id, $leavename);
                },
                $this->allowed
            )
        );

        //---- Get pagination number link
        $this->twig->addFunction(
            new \Twig\TwigFunction(
                'get_pagenum_link',
                function (int $page_number = null, bool $escape = true) {
                    if (!$page_number) {
                        return '';
                    }

                    return \get_pagenum_link($page_number, $escape);
                },
                $this->allowed
            )
        );

        $this->twig->addFunction(
            new \Twig\TwigFunction(
                '__',
                function ($string, $domain) {
                    return \__($string, $domain);
                },
                $this->allowed
            )
        );
    }
}
