<?php

namespace AnisAronno\MediaHelper\Traits;

trait CacheKey
{
    public static function getImageCacheKey(): string
    {
        return '_image_';
    }

}
