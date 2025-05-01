<?php

namespace Fhoke\Slate\ACF;

use Fhoke\Slate\Utilities;

class ACF
{
    public static $settingsPageID = 'theme_settings';

    /**
     * Get the current post ID
     *
     * @return int|string
     */
    public static function postID()
    {
        $current_id = Utilities::currentPostID();

        if (\is_tax() || \is_category() || \is_tag()) {
            return \get_queried_object()->taxonomy . '_' . $current_id;
        } elseif (\is_author()) {
            return 'user_' . $current_id;
        }

        return $current_id;
    }

    /**
     * Check if a row has data to loop over
     *
     * @param string $field_name
     * @param int|string $field_name
     * @return bool
     */
    public static function haveRows(string $field_name, $post_id = null)
    {
        if (!\function_exists('have_rows')) {
            return false;
        }

        return \have_rows($field_name, $post_id ?: self::postID());
    }

    /**
     * Set the data for the current loop
     * * Must be used inside a self::haveRows loop
     *
     * @param bool $format
     * @return mixed
     */
    public static function theRow($format = false)
    {
        if (!\function_exists('the_row')) {
            return;
        }

        return \the_row($format);
    }

    /**
     * Get a page field
     *
     * @param string $field_name
     * @param int|string $post_id
     * @param bool $format
     * @return mixed
     */
    public static function field(string $field_name, $post_id = null, bool $format = true)
    {
        if (!\function_exists('get_field')) {
            return;
        }

        return \get_field($field_name, $post_id ?: self::postID(), $format);
    }

    /**
     * Get a theme settings field
     *
     * @param string $field_name
     * @param bool $format
     * @param string|int $settingsPageID
     * @return mixed
     */
    public static function settingsField(string $field_name, bool $format = true, $settingsPageID = null)
    {
        $settingsPageID = $settingsPageID ?: self::$settingsPageID;

        return self::field($field_name, $settingsPageID, $format);
    }

    /**
     * Get a sub field
     * * Must be used inside an ACF loop
     *
     * @param string $field_name
     * @param bool $format
     * @return mixed
     */
    public static function subField(string $field_name, bool $format = true)
    {
        if (!\function_exists('get_sub_field')) {
            return;
        }

        return \get_sub_field($field_name, $format);
    }
}
