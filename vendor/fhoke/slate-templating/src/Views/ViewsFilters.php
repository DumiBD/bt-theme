<?php

namespace Fhoke\Slate\Templating\Views;

trait ViewsFilters
{
    /**
     * @var \Twig\Environment
     */
    protected $twig;

    /**
     * Add custom filters
     */
    protected function addFilters()
    {
        //---- Format content from WYSIWYG
        $this->twig->addFilter(
            new \Twig\TwigFilter(
                'wysiwyg',
                function ($string) {
                    return \apply_filters('slate/format/content', $string);
                },
                [
                    'is_safe' => ['html'],
                ]
            )
        );
    }
}
