<?php

namespace AnisAronno\MediaHelper\Facades;

use Illuminate\Support\Facades\Facade;

class Media extends Facade
{
    /**
      * @method static void \AnisAronno\MediaHelper\MediaHelpers setStorageDisk(string $disk)
      * @method static string \AnisAronno\MediaHelper\MediaHelpers getDefaultFiles(true)
       * @method static string \AnisAronno\MediaHelper\MediaHelpers getURL(string $path)
      * @method static void \AnisAronno\MediaHelper\MediaHelpers upload($request, $fieldName, string $upload_dir)
      * @method static void \AnisAronno\MediaHelper\MediaHelpers public function delete($value)
      *
      * @see \AnisAronno\MediaHelper\MediaHelpers
      */
    protected static function getFacadeAccessor()
    {
        return 'Media';
    }
}
