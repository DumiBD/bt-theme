<?php

/**
 * Theme base setup
 *
 * @package Bluetown
 *
 * @author  Fhoke <hello@fhoke.com>
 */

use Fhoke\Bluetown\PostTypes\Job;
use Fhoke\Bluetown\PostTypes\TeamMember;
use Fhoke\Bluetown\PostTypes\Testimonial;

/**
 * Globals
 */
//---- Variables
$httpHost = $_SERVER['HTTP_HOST'] ?? '';
$isDevEnv = strpos($httpHost, '.local') !== false || $httpHost === 'localhost:8888';

//---- Constants
define('BTO_ENV', $isDevEnv ? 'development' : 'production');
define('BTO_THEME_PATH', get_template_directory());
define('BTO_THEME_URI', get_template_directory_uri());
define('BTO_ASSETS_PATH', BTO_THEME_PATH . '/dist');
define('BTO_ASSETS_URI', BTO_THEME_URI . '/dist');

define('BTO_INCLUDES', BTO_THEME_PATH . '/includes');
define('BTO_THEME_PARTS', BTO_INCLUDES . '/theme-parts');
define('BTO_THIRD_PARTY', BTO_INCLUDES . '/third-party');

define('SLATE_VIEWS_PATH', BTO_INCLUDES . '/views');

// Directory path (e.g. /Applications/MAMP/htdocs/...)
define('BTO_IMG_PATH', BTO_ASSETS_PATH . '/img');
define('BTO_CSS_PATH', BTO_ASSETS_PATH . '/css');
define('BTO_JS_PATH', BTO_ASSETS_PATH . '/js');

// Directory URL (e.g. http://localhost:8888/...)
define('BTO_IMG_URL', BTO_ASSETS_URI . '/img');
define('BTO_CSS_URL', BTO_ASSETS_URI . '/css');
define('BTO_JS_URL', BTO_ASSETS_URI . '/js');

/**
 * Autoloader (via Composer)
 */
require_once BTO_THEME_PATH . '/vendor/autoload.php';

/**
 * Include files
 */
//---- Theme
include BTO_INCLUDES . '/session.php';
include BTO_INCLUDES . '/setup.php';
include BTO_INCLUDES . '/post-types.php';
include BTO_INCLUDES . '/taxonomies.php';
include BTO_INCLUDES . '/gutenberg.php';
include BTO_INCLUDES . '/shortcodes.php';
include BTO_INCLUDES . '/extend.php';
include BTO_INCLUDES . '/filters.php';
include BTO_INCLUDES . '/menus.php';
include BTO_INCLUDES . '/meta-boxes.php';

//---- Ajax
include BTO_INCLUDES . '/ajax/ajax.php';

//---- Admin
include BTO_INCLUDES . '/admin/admin.php';
include BTO_INCLUDES . '/admin/tinyMCE.php';

//---- Other
include BTO_INCLUDES . '/helpers.php';
include BTO_INCLUDES . '/redirects.php';
include BTO_INCLUDES . '/job-expiry.php';
include BTO_INCLUDES . '/weekly-emailer.php';
include BTO_INCLUDES . '/DEV.php';

//---- Third Party
include BTO_THIRD_PARTY . '/third-party.php';

/**
 * Slate settings
 */
function slate_twig_globals()
{
    return [
        'home_url'      => home_url(),
        'all_posts_url' => get_permalink(get_option('page_for_posts')),
        'all_jobs_url'  => get_post_type_archive_link('wpbb_job'),
        'logged_in'     => is_user_logged_in(),
    ];
}

function slate_twig_functions()
{
    return [
        new \Twig\TwigFunction(
            'Testimonial',
            function (int $post_id = null) {
                if (!$post_id) {
                    return null;
                }

                return new Testimonial($post_id);
            },
            [
                'is_safe' => ['html'],
            ]
        ),

        new \Twig\TwigFunction(
            'TeamMember',
            function (int $post_id = null) {
                if (!$post_id) {
                    return null;
                }

                return new TeamMember($post_id);
            },
            [
                'is_safe' => ['html'],
            ]
        ),

        new \Twig\TwigFunction(
            'Job',
            function (int $post_id = null) {
                if (!$post_id) {
                    return null;
                }

                return new Job($post_id);
            },
            [
                'is_safe' => ['html'],
            ]
        ),

        new \Twig\TwigFunction(
            'split_string_into_spans',
            function (string $string = '') {
                if (!$string) {
                    return '';
                }

                $string = explode(' ', $string);

                $string = array_map(function (string $word) {
                    return "<span class='word'>{$word}</span>";
                }, $string);

                $string = implode(' ', $string);

                return $string;
            },
            [
                'is_safe' => ['html'],
            ]
        ),
    ];
}
