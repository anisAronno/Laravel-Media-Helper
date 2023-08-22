<?php

namespace AnisAronno\Media\Facades;

use Illuminate\Support\Facades\Facade;

class Media extends Facade
{
    /**
     * @method static void \AnisAronno\Media\MediaHelpers setStorageDisk(string $disk)
     * @method static string \AnisAronno\Media\MediaHelpers url(string $path)
     * @method static string \AnisAronno\Media\MediaHelpers get()
     * @method static string \AnisAronno\Media\MediaHelpers getUrl(string $path)
     * @method static void \AnisAronno\Media\MediaHelpers upload($request, $file_name, string $upload_dir)
     * @method static void \AnisAronno\Media\MediaHelpers public function deleteFile($value)
     *
     * @see \AnisAronno\Media\MediaHelpers
     */
    protected static function getFacadeAccessor()
    {
        return 'Media';
    }
}
