<?php
/**
 * Remove ACF filters from WYSIWYG fields
 */
function slate_remove_acf_wysiwyg_filters()
{
    remove_all_filters('acf_the_content');
}

add_action('acf/init', 'slate_remove_acf_wysiwyg_filters');
