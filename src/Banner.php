<?php

/**
 * Output HTML for banner elements
 *
 * @package Bluetown
 *
 * @author Fhoke <hello@fhoke.com>
 */

namespace Fhoke\Bluetown;

use Fhoke\Bluetown\PostTypes\Job;
use Fhoke\Slate\ACF\ACF;
use Fhoke\Slate\ACF\Button;
use Fhoke\Slate\ACF\Link;
use Fhoke\Slate\Templating\Views\View;
use Fhoke\Slate\Utilities;

class Banner
{
    public string $type               = 'option_1';
    public bool $shapesEnabled        = false;
    public string $shapesPosition     = 'option_1';
    public string $subtitle           = '';
    public string $icon               = '';
    public string $title              = '';
    public string $description        = '';
    public string $image              = '';
    public array $imageCards          = [];
    public string $backgroundImageUrl = '';
    public string $backgroundColor    = 'option_1';
    protected string $pageType        = '';

    public Button $button;
    public Link $link;
    protected $postID;
    protected string $settingsID;

    public function __construct()
    {
        $this->postID     = ACF::postID();
        $this->settingsID = ACF::$settingsPageID;
        $this->pageType   = $this->setPageType();

        $this->type = ACF::field('banner_type', $this->postID) ?: $this->type;

        $this->shapesEnabled  = ACF::field('banner_shapes', $this->postID) ?: $this->shapesEnabled;
        $this->shapesPosition = ACF::field('banner_shapes_position', $this->postID) ?: $this->shapesPosition;
    }

    public function isStandardType()
    {
        return $this->type == 'option_1';
    }

    public function isBackgroundImageType()
    {
        return $this->pageType == 'account' || $this->pageType == 'wpbb_job' || ($this->type == 'option_3' && $this->backgroundImageUrl);
    }

    public function hasBackgroundColor()
    {
        return $this->backgroundColor && $this->backgroundColor != 'option_1';
    }

    /**
     * Set the page type
     *
     * @return string
     */
    public function setPageType()
    {
        if (Utilities::wcIsActive()) {
            if (\is_tax('product_cat')) {
                return 'wc_product_category';
            }

            if (\is_singular(['product'])) {
                return 'wc_product';
            }

            if (\is_account_page()) {
                return 'wc_account';
            }

            if (\is_checkout()) {
                return 'wc_checkout';
            }

            if (\is_cart()) {
                return 'wc_cart';
            }
        }

        if (\is_page_template('templates/login.php')) {
            return 'login';
        }

        if (\is_page_template('templates/register.php')) {
            return 'register';
        }

        if (\is_page_template('templates/account.php') || \is_page_template('templates/applied-jobs.php') || \is_page_template('templates/saved-jobs.php')) {
            return 'account';
        }

        if (\is_post_type_archive(['wpbb_job'])) {
            return 'wpbb_job_archive';
        }

        if (\is_singular(['wpbb_job'])) {
            return 'wpbb_job';
        }

        if (\is_tax() || \is_category() || \is_tag()) {
            return 'taxonomy';
        }

        if (\is_404()) {
            return '404';
        }

        if (\is_search()) {
            return 'search';
        }

        if (\is_author()) {
            return 'author';
        }

        if (\is_single()) {
            return 'post';
        }

        return '';
    }

    /**
     * Whether or not to show the banner
     *
     * @return bool
     */
    public function show()
    {
        switch ($this->pageType) {
            case 'wc_product':
            case 'wc_account':
            case 'wc_checkout':
            case 'wc_cart':
            case 'login':
                return false;
        }

        return true;
    }

    /**
     * HTML for the subtitle text
     *
     * @return string
     */
    public function subtitleHTML()
    {
        if (! $this->subtitle) {
            return '';
        }

        return "<h2 class='site-banner__subtitle'>{$this->subtitle}</h2>";
    }

    /**
     * HTML for the icon
     *
     * @return string
     */
    public function iconHTML()
    {
        if (! $this->icon) {
            return '';
        }

        return "<div class='site-banner__icon'>{$this->icon}</div>";
    }

    /**
     * HTML for the title text
     *
     * @return string
     */
    public function titleHTML()
    {
        if (! $this->title) {
            return '';
        }

        return "<h1 class='site-banner__title-txt'>{$this->title}</h1>";
    }

    /**
     * HTML for the description text
     *
     * @return string
     */
    public function descriptionHTML()
    {
        if (! $this->description) {
            return '';
        }

        return "<div class='site-banner__txt'>" . \wpautop($this->description) . '</div>';
    }

    /**
     * HTML for the button
     *
     * @return string
     */
    public function buttonHTML()
    {
        if (! $this->button->isSetup() && ! $this->link->isSetup()) {
            return '';
        }

        return "<div class='site-banner__btns'>{$this->button->render()}{$this->link->render()}</div>";
    }

    /**
     * HTML for the image
     *
     * @return string
     */
    public function imageHTML()
    {
        if (! $this->image) {
            return '';
        }

        $size_attrs = \bto_img_size_attrs('Banner - Image');

        return <<< EOT
            <figure class="site-banner__img">
                <img loading="lazy" {$size_attrs} src="{$this->image}" alt="">
            </figre>
        EOT;
    }

    /**
     * HTML for the form
     *
     * @return string
     */
    public function formHTML()
    {
        if (! \is_front_page() && !\is_post_type_archive('wpbb_job') && !\bto_is_job_search_page()) {
            return '';
        }

        return \get_search_form();
    }

    /**
     * Menu HTML
     *
     * @return string
     */
    public function menuHTML()
    {
        if ($this->pageType != 'account') {
            return '';
        }

        $endpoints = [
            'account'      => ACF::settingsField('endpoint_account'),
            'saved_jobs'   => ACF::settingsField('endpoint_saved_jobs'),
            'applied_jobs' => ACF::settingsField('endpoint_applied_jobs'),
        ];

        return View::render('components/account-menu.twig', [
            'menu_items' => [
                [
                    'title'   => \__('Account', 'bluetown'),
                    'url'     => \get_permalink($endpoints['account']),
                    'current' => $this->postID == $endpoints['account'],
                ],
                [
                    'title'   => \__('Saved Jobs', 'bluetown'),
                    'url'     => \get_permalink($endpoints['saved_jobs']),
                    'current' => $this->postID == $endpoints['saved_jobs'],
                ],
                [
                    'title'   => \__('Applied Jobs', 'bluetown'),
                    'url'     => \get_permalink($endpoints['applied_jobs']),
                    'current' => $this->postID == $endpoints['applied_jobs'],
                ],
                [
                    'title' => \__('Log out', 'bluetown'),
                    'url'   => \wp_logout_url(\home_url()),
                ],
            ],
        ]);
    }

    /**
     * Extra HTML
     *
     * @return string
     */
    public function extraHTML()
    {
        if ($this->imageCards) {
            $html = '';

            $grid_svg = Utilities::svg('vertical-grid', 'shapes');

            $grid_img_1 = [
                'png' => [
                    '1x' => \BTO_IMG_URL . '/vertical-grid-1.png',
                    '2x' => \BTO_IMG_URL . '/vertical-grid-1-@2x.png',
                ],
                'webp' => [
                    '1x' => \BTO_IMG_URL . '/vertical-grid-1.webp',
                    '2x' => \BTO_IMG_URL . '/vertical-grid-1-@2x.webp',
                ],
            ];

            $grid_img_2 = [
                'png' => [
                    '1x' => \BTO_IMG_URL . '/vertical-grid-2.png',
                    '2x' => \BTO_IMG_URL . '/vertical-grid-2-@2x.png',
                ],
                'webp' => [
                    '1x' => \BTO_IMG_URL . '/vertical-grid-2.webp',
                    '2x' => \BTO_IMG_URL . '/vertical-grid-2-@2x.webp',
                ],
            ];

            $html .= <<< EOT
                <div class="vertical-grid-shape" data-parallax>
                    <div class="vertical-grid-shape__inner">
                        <picture>
                            <source srcset="{$grid_img_1['webp']['1x']} 1x, {$grid_img_1['webp']['2x']} 2x"" type="image/webp">
                            <source srcset="{$grid_img_1['png']['1x']} 1x, {$grid_img_1['png']['2x']} 2x"" type="image/png">
                            <img width="270" height="312" srcset="{$grid_img_1['png']['1x']} 1x, {$grid_img_1['png']['2x']} 2x" src="{$grid_img_1['png']['1x']}" alt="">
                        </picture>
                        <picture>
                            <source srcset="{$grid_img_2['webp']['1x']} 1x, {$grid_img_2['webp']['2x']} 2x"" type="image/webp">
                            <source srcset="{$grid_img_2['png']['1x']} 1x, {$grid_img_2['png']['2x']} 2x"" type="image/png">
                            <img width="260" height="323" srcset="{$grid_img_2['png']['1x']} 1x, {$grid_img_2['png']['2x']} 2x" src="{$grid_img_2['png']['1x']}" alt="">
                        </picture>
                        {$grid_svg}
                    </div>
                </div>
            EOT;

            return $html . View::render('sections/image-cards.twig', [
                'cards' => $this->imageCards,
            ]);
        }

        if ($this->pageType == 'wpbb_job') {
            $job = (new Job($this->postID))
                ->withIndustries()
                ->withLocations()
                ->withTypes()
                ->withSalary();

            return View::render('components/job-apply.twig', [
                'job' => $job,
            ]);
        }

        return '';
    }

    /**
     * CSS classes
     *
     * @param array $new_classes
     *
     * @return string
     */
    public function cssClasses(array $new_classes = [])
    {
        $classes = ['site-banner'];

        //---- Add classes
        if (!$this->show()) {
            $classes[] = 'site-banner--diabled';
        }

        if (!$this->isBackgroundImageType() && $this->hasBackgroundColor()) {
            $classes[] = 'site-banner--txt-light';
        }

        if ($this->isBackgroundImageType()) {
            $classes[] = 'site-banner--bg-img';
        }

        if ($this->hasBackgroundColor()) {
            if ($this->backgroundColor == 'option_2') {
                $classes[] = 'site-banner--bg-color-1';
            } elseif ($this->backgroundColor == 'option_3') {
                $classes[] = 'site-banner--bg-color-2';
            } elseif ($this->backgroundColor == 'option_4') {
                $classes[] = 'site-banner--bg-color-3';
            }
        }

        if ($this->image) {
            $classes[] = 'site-banner--img';
        }

        if ($this->shapesEnabled) {
            if ($this->shapesPosition == 'option_1') {
                $classes[] = 'site-banner--shapes-1';
            } elseif ($this->shapesPosition == 'option_2') {
                $classes[] = 'site-banner--shapes-2';
            }
        }

        //---- Merge arrays into escaped string
        $classes = Utilities::arraysMergeToString($classes, $new_classes);

        //---- Return
        if ($classes) {
            return "class='${classes}'";
        }
    }

    /**
     * Inline styles
     *
     * @param array $new_classes
     *
     * @return string
     */
    public function inlineStyles(array $new_styles = [])
    {
        $styles = [];

        //---- Add styles
        if ($this->isBackgroundImageType()) {
            $styles[] = "background-image: url({$this->backgroundImageUrl});";
        }

        //---- Merge arrays into escaped string
        $styles = Utilities::arraysMergeToString($styles, $new_styles, '');

        //---- Return
        if ($styles) {
            return "style='${styles}'";
        }
    }

    /**
     * Get the subtitle text
     *
     * @return $this
     */
    public function withSubtitle()
    {
        switch ($this->pageType) {
            case 'search':
                global $wp_query;

                if (! $wp_query) {
                    return $this;
                }

                $this->subtitle = \sprintf(
                    \_n(
                        '%s Result For',
                        '%s Results For',
                        $wp_query->found_posts,
                        'bluetown'
                    ),
                    \number_format_i18n($wp_query->found_posts)
                );
                break;
            case 'author':
                $this->subtitle = \__('Posts from', 'bluetown');
                break;
            case 'wpbb_job':
                $this->subtitle = Utilities::term(['post_id' => $this->postID, 'tax' => 'wpbb_job_industry']) ?: '';
                break;
            case 'post':
                $this->subtitle = Utilities::term(['post_id' => $this->postID]) ?: '';
                break;
            case '404':
                $this->subtitle = ACF::field('404_banner_subtitle', $this->settingsID) ?: '';
                break;
            default:
                $this->subtitle = ACF::field('banner_subtitle', $this->postID) ?: '';
                break;
        }

        return $this;
    }

    /**
     * Get the icon html
     *
     * @return $this
     */
    public function withIcon()
    {
        switch ($this->pageType) {
            default:
                $field = ACF::field('banner_icon', $this->postID) ?: '';
                $url = $field['url'] ?? '';

                if (! $field || ! $url) {
                    return $this;
                }

                $this->icon = "<img src='{$url}' alt=''>";
        }

        return $this;
    }

    /**
     * Get the title text
     *
     * @return $this
     */
    public function withTitle()
    {
        switch ($this->pageType) {
            case 'wc_product_category':
            case 'wpbb_job_archive':
                $this->title = ACF::field('jobs_banner_title', $this->settingsID) ?: (\get_post_type_labels(\get_post_type_object(\get_post_type()))->name ?? '');
                break;
            case 'taxonomy':
                $this->title = \single_cat_title(false, false);
                break;
            case 'author':
                $this->title = \get_the_author_meta('display_name', \get_queried_object()->ID);
                break;
            case '404':
                $this->title = ACF::field('404_banner_title', $this->settingsID) ?: \__('404 Page Not Found', 'bluetown');
                break;
            case 'search':
                $this->title = '&lsquo;' . \get_search_query() . '&rsquo;';
                break;
            case 'post':
                $this->title = \get_the_title($this->postID);
                break;
            default:
                $field = ACF::field('banner_title', $this->postID) ?: '';

                if (! $field) {
                    $this->title = \get_the_title($this->postID);

                    return $this;
                }

                $this->title = $field;
        }

        return $this;
    }

    /**
     * Get the description text
     *
     * @return $this
     */
    public function withDescription()
    {
        switch ($this->pageType) {
            case 'wc_product_category':
            case 'taxonomy':
                $this->description = \get_queried_object()->description ?? '';
                break;
            case 'wpbb_job_archive':
                $this->description = ACF::field('jobs_banner_txt', $this->settingsID) ?: (\get_post_type_labels(\get_post_type_object(\get_post_type()))->name ?? '');
                break;
            case 'post':
                $this->description = Utilities::datetime('date');
                break;
            case '404':
                $this->description = ACF::field('404_banner_txt', $this->settingsID) ?: '';
                break;
            default:
                $this->description = ACF::field('banner_txt', $this->postID) ?: '';
                break;
        }

        return $this;
    }

    /**
     * Get the button
     *
     * @return $this
     */
    public function withButton()
    {
        switch ($this->pageType) {
            case '404':
                $this->button = new Button(ACF::field('404_banner_btn', $this->settingsID));
                break;
            default:
                $this->button = new Button(ACF::field('banner_btn', $this->postID));
                break;
        }

        return $this;
    }

    /**
     * Get the link
     *
     * @return $this
     */
    public function withLink()
    {
        $arrow_right_icon = Utilities::svg('arrow-right', 'icons');
        $classes          = [
            'site-banner__link',
        ];

        switch ($this->pageType) {
            case '404':
                $this->link = (new Link(ACF::field('404_banner_link', $this->settingsID), ['classes' => $classes]))->addBeforeText("<span class='fancy-icon'>{$arrow_right_icon}</span>");
                break;
            default:
                $this->link = (new Link(ACF::field('banner_link', $this->postID), ['classes' => $classes]))->addBeforeText("<span class='fancy-icon'>{$arrow_right_icon}</span><span>")->addAfterText('</span>');
                break;
        }

        return $this;
    }

    /**
     * Get the image
     *
     * @return $this
     */
    public function withImage()
    {
        $img_size = 'Banner - Image';

        switch ($this->pageType) {
            case 'post':
                $this->image = \get_the_post_thumbnail_url(null, $img_size);
                break;
            case '404':
                $this->image = ACF::field('404_banner_img', $this->settingsID)['sizes'][$img_size] ?? '';
                break;
            default:
                $this->image = ACF::field('banner_img', $this->postID)['sizes'][$img_size] ?? '';
                break;
        }

        return $this;
    }

    /**
     * Get the background color hex code
     *
     * @return $this
     */
    public function withBackgroundColor()
    {
        $this->backgroundColor = ACF::field('banner_bg_color', $this->postID) ?: $this->backgroundColor;

        return $this;
    }

    /**
     * Get the background image URL
     *
     * @return $this
     */
    public function withBackgroundImage()
    {
        if ($this->pageType != 'account' && $this->pageType != 'wpbb_job' && $this->type != 'option_3') {
            return $this;
        }

        switch ($this->pageType) {
            case 'account':
            case 'wpbb_job':
                $this->backgroundImageUrl = \BTO_IMG_URL . '/bg-night-sky.png';
                break;
            case 'wc_product_category':
                $thumbnail_id = \get_term_meta(\get_queried_object_id(), 'thumbnail_id', true);

                if (! $thumbnail_id) {
                    return $this;
                }

                $this->backgroundImageUrl = \wp_get_attachment_image_src($thumbnail_id, 'Banner')[0] ?? '';
                break;
            case '404':
                $this->backgroundImageUrl = ACF::field('404_banner_bg_img', $this->settingsID)['sizes']['Banner'] ?? '';
                break;
            default:
                $this->backgroundImageUrl = ACF::field('banner_bg_img', $this->postID)['sizes']['Banner'] ?? '';
                break;
        }

        return $this;
    }

    /**
     * Get the image cards
     *
     * @return $this
     */
    public function witImageCards()
    {
        if (! \is_front_page()) {
            return $this;
        }

        $this->imageCards = ACF::field('banner_cards', $this->postID) ?: [];

        return $this;
    }
}
