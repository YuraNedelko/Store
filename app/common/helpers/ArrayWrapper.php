<?php


namespace app\common\helpers;


class ArrayWrapper
{
    /**
     * @param mixed $value
     * @return array
     */
    static public function wrap($value)
    {
        if (is_null($value)) {
            return [];
        }

        return is_array($value) ? $value : [$value];
    }
}