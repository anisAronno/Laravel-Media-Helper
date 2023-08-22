<?php

namespace AnisAronno\MediaHelper\Facades;

use Illuminate\Support\Facades\Facade;

class Media extends Facade
{
    /**
     * @method static void \AnisAronno\MediaHelper\MediaHelpers setStorageDisk(string $disk)
     * @method static string \AnisAronno\MediaHelper\MediaHelpers url(string $path)
     * @method static string \AnisAronno\MediaHelper\MediaHelpers get()
     * @method static string \AnisAronno\MediaHelper\MediaHelpers getUrl(string $path)
     * @method static void \AnisAronno\MediaHelper\MediaHelpers upload($request, $file_name, string $upload_dir)
     * @method static void \AnisAronno\MediaHelper\MediaHelpers public function delete($value)
     *
     * @see \AnisAronno\MediaHelper\MediaHelpers
     */
    protected static function getFacadeAccessor()
    {
        return 'Media';
    }
}
