<?php

/**
 * Load in Ajax files
 *
 * @package Bluetown
 *
 * @author Fhoke <hello@fhoke.com>
 */

use Fhoke\Slate\Utilities;

/**
 * Create global JS ajax variable
 */
function bto_ajax_js_variables()
{
    ?>
    <script type="text/javascript">
        /* <![CDATA[ */
            const SlateAjax = {
                "home_url": "<?php echo esc_url(home_url()); ?>",
                "login_url": "<?php echo esc_url(add_query_arg('redirect_to', bto_current_url(), home_url('login'))); ?>",
                "ajax_admin_url": "<?php echo esc_url(admin_url('admin-ajax.php')); ?>",
            };
        /* ]]> */
    </script>
    <?php
}

add_action('wp_head', 'bto_ajax_js_variables');

/**
 * Load Ajax scripts
 */
function bto_ajax_enqueue_scripts()
{
    wp_enqueue_script(
        'bluetown-ajax',
        BTO_JS_URL . '/ajax.min.js',
        ['bluetown'],
        Utilities::fileLastUpdateTimestamp(BTO_JS_PATH . '/ajax.min.js'),
        true
    );
}

add_action('wp_enqueue_scripts', 'bto_ajax_enqueue_scripts');

/**
 * Load Ajax files
 */
include BTO_INCLUDES . '/ajax/save-job.php';
