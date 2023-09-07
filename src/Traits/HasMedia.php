<?php

namespace AnisAronno\MediaHelper\Traits;

use AnisAronno\MediaHelper\Models\Image;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait HasMedia
{
    public function images(): MorphToMany
    {
        return $this->morphToMany(Image::class, 'imageable')
            ->wherePivot('is_featured', '!=', true)
            ->withPivot('is_featured')
            ->withTimestamps();
    }

    public function image()
    {
        return $this->morphToMany(Image::class, 'imageable')
            ->wherePivot('is_featured', true)
            ->withPivot('is_featured')
            ->withTimestamps()
            ->latest()
            ->limit(1);
    }
}
