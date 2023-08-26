<?php

namespace AnisAronno\MediaHelper\Facades;

use Illuminate\Support\Facades\Facade;

class Media extends Facade
{
    /**
      * @method static void \AnisAronno\MediaHelper\Media setStorageDisk(string $disk)
      * @method static string \AnisAronno\MediaHelper\Media getDefaultFiles(true)
      * @method static string \AnisAronno\MediaHelper\Media getPlaceholderImage()
      * @method static string \AnisAronno\MediaHelper\Media getURL(string $path)
      * @method static void \AnisAronno\MediaHelper\Media upload($request, $fieldName, string $upload_dir)
      * @method static void \AnisAronno\MediaHelper\Media public function delete($value)
      *
      * @see \AnisAronno\MediaHelper\Media
      */
    protected static function getFacadeAccessor()
    {
        return 'Media';
    }
}
