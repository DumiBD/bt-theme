<?php

namespace Fhoke\Slate;

class Pagination
{
    /**
     * Get pagination URL
     *
     * @param object $query
     * @param string $type
     * @return string
     */
    public static function url($query = null, $type = 'next')
    {
        if (!$query) {
            global $wp_query;

            $query = $wp_query;
        }

        $page_num = ($query->query['paged'] ?? 1);

        if ($type == 'next' && $page_num < $query->max_num_pages) {
            return \get_pagenum_link($page_num + 1);
        }

        if ($type == 'prev' && $page_num > 1) {
            return \get_pagenum_link($page_num - 1);
        }
    }

    /**
     * HTML for pagination
     *
     * @param object $query
     * @param bool $show_disabled
     * @return string
     */
    public static function render($query = null, $show_disabled = false)
    {
        if (!$query) {
            global $wp_query;

            $query = $wp_query;
        }

        $prev_url = self::url($query, 'prev');
        $next_url = self::url($query, 'next');

        if ($prev_url || $next_url) {
            $output = '<nav class="pagination">';

            if (!$prev_url && $show_disabled) {
                $output .= '<span class="page-numbers prev disabled">' . __('Previous', 'slate') . '</span>';
            }

            $output .= \paginate_links([
                'prev_text' => __('Previous', 'slate'),
                'next_text' => __('Next', 'slate'),
                'end_size'  => 1,
                'mid_size'  => 2,
                'total'     => $query->max_num_pages
            ]);

            if (!$next_url && $show_disabled) {
                $output .= '<span class="page-numbers next disabled">' . __('Next', 'slate') . '</span>';
            }

            $output .= '</nav>';

            return $output;
        }
    }
}
