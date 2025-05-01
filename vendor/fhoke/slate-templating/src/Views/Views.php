<?php

namespace Fhoke\Slate\Templating\Views;

use Fhoke\Slate\ACF\ACF;
use Fhoke\Slate\Utilities;

class Views
{
    use ViewsFunctions;
    use ViewsFilters;

    /**
     * @var \Twig\Environment
     */
    protected $twig;

    /**
     * @var bool
     */
    protected $debugEnabled = true;

    public function __construct()
    {
        if (!\defined('SLATE_VIEWS_PATH')) {
            throw new \DomainException('SLATE_VIEWS_PATH constant is missing. Please define it before calling ' .  self::class);
        }

        $this->twig = new \Twig\Environment(new \Twig\Loader\FilesystemLoader(\SLATE_VIEWS_PATH), [
            'debug' => $this->debugEnabled,
            // 'cache' => CAC_THEME_PATH . '/.cache',
        ]);

        $this->addExtensions();
        $this->addFunctions();
        $this->addFilters();
        $this->addGlobals();

        $this->addThemeGlobals();
        $this->addThemeFunctions();
    }

    /**
     * Add any required extensions
     */
    protected function addExtensions()
    {
        if (!$this->debugEnabled) {
            return;
        }

        $this->twig->addExtension(new \Twig\Extension\DebugExtension());
    }

    /**
     * Add global variables
     */
    protected function addGlobals()
    {
        $this->twig->addGlobal('urls', [
            'theme'  => \get_template_directory_uri(),
            'home'   => \home_url(),
            'blog'   => \get_permalink(\get_option('page_for_posts')),
        ]);

        $this->twig->addGlobal('ACF', new ACF());
        $this->twig->addGlobal('Utilities', new Utilities());
    }

    /**
     * Add custom globals defined in theme
     */
    protected function addThemeGlobals()
    {
        if (!\function_exists('slate_twig_globals')) {
            return;
        }

        foreach (\slate_twig_globals() as $key => $value) {
            $this->twig->addGlobal($key, $value);
        }
    }

    /**
     * Add custom functions defined in theme
     */
    protected function addThemeFunctions()
    {
        if (!\function_exists('slate_twig_functions')) {
            return;
        }

        foreach (\slate_twig_functions() as $function) {
            if (!\is_a($function, \Twig\TwigFunction::class)) {
                continue;
            }

            $this->twig->addFunction($function);
        }
    }

    /**
     * Return a Twig template
     *
     * @param string $filename
     * @param array $args
     * @return string
     */
    public function render(string $file, array $args = [])
    {
        $filepath = \SLATE_VIEWS_PATH . "/$file";

        if (!\file_exists($filepath) && !$this->debugEnabled) {
            return;
        }

        return $this->twig->render($file, $args);
    }

    /**
     * Echo a Twig template
     *
     * @param string $filename
     * @param array $args
     * @return string
     */
    public function present(string $file, array $args = [])
    {
        echo $this->render($file, $args);
    }
}
