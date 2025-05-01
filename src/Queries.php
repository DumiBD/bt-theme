<?php

/**
 * Assorted methods for using WP_Query
 *
 * @package Bluetown
 *
 * @author Fhoke <hello@fhoke.com>
 */

namespace Fhoke\Bluetown;

class Queries
{
    /**
     * Get related posts from post ID
     *
     * @return \WP_Query
     */
    public static function related(int $post_id, int $count = 3, $post_type = 'post', string $taxonomy = 'category')
    {
        $current_terms = \wp_get_post_terms($post_id, $taxonomy, [
            'fields' => 'ids',
        ]);

        return new \WP_Query([
            'post_type'      => $post_type,
            'posts_per_page' => $count,
            'orderby'        => 'rand',
            'post__not_in'   => [$post_id],
            'tax_query'      => [
                [
                    'taxonomy' => $taxonomy,
                    'field'    => 'term_id',
                    'terms'    => $current_terms,
                    'operator' => 'IN',
                ],
            ],
        ]);
    }
}
