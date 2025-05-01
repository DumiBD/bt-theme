<?php

/**
 * Default search form
 *
 * @package Bluetown
 *
 * @author Fhoke <hello@fhoke.com>
 */

use Fhoke\Slate\Utilities;

?>
<form method="get" class="search-form" action="<?php echo home_url('/'); ?>">
    <input type="text" class="search-form__field" placeholder="<?php echo esc_attr__('Search Job, Title, Keyword...', 'bluetown'); ?>" value="<?php echo esc_attr(get_search_query()); ?>" name="s">
    <button class="search-form__action" aria-label="<?php _e('Search', 'bluetown') ?>">
        <?php echo Utilities::svg('search', 'icons'); ?>
    </button>
    <input type="hidden" name="post_type" value="wpbb_job">
</form>
