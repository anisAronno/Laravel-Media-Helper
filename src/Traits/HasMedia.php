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

    /**
     * Media Upload with DB
     *
     * @param array $ids
     * @param boolean $isFeatured
     * @return void
     */
    public function upload(array $ids, $isFeatured): void
    {
        if ($isFeatured) {
            $this->images()->attach($ids, ['is_featured' => 1]);
        } else {
            $this->images()->attach($ids);
        }

    }
}
