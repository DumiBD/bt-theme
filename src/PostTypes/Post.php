<?php

namespace Fhoke\Bluetown\PostTypes;

use OOPWP\PostTypes\Post as PostTypesPost;

class Post extends PostTypesPost
{
    public function getMeta($key, $single = true)
    {
        return \get_post_meta($this->id, $key, $single);
    }
}
