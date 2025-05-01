<?php

namespace Fhoke\Bluetown;

class Helpers
{
    public static function mergeArraysUnique(array $arr_1, array $arr_2)
    {
        return \array_unique(
            \array_filter(
                \array_merge(
                    $arr_1,
                    $arr_2
                )
            )
        );
    }
}
