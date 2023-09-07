<?php

namespace AnisAronno\MediaHelper\Facades;

use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Str;

class Media extends Facade
{
    /**
      * @method static void setStorageDisk(string $disk)
      * @method static string getDefaultFiles(bool $includePath = false)
      * @method static string getDefaultLogo()
      * @method static string getDefaultFavIcon()
      * @method static string getDefaultBanner()
      * @method static string getDefaultAvatar()
      * @method static string getDefaultPlaceholder()
      * @method static string getURL(string $path)
      * @method static void upload($request, $fieldName, string $upload_dir)
      * @method static void delete($value)
      *
      * @see \AnisAronno\MediaHelper\Media
      */
    protected static function getFacadeAccessor()
    {
        return 'Media';
    }

    public static function __callStatic($method, $arguments)
    {
        $instance = static::resolveFacadeInstance(static::getFacadeAccessor());
        $files = $instance->getAllDefaultFiles(true);

        if(Str::startsWith($method, 'getDefault')) {
            $key = strtolower(trim(str_replace('getDefault', '', $method)));

            if (array_key_exists($key, $files)) {
                return $files[$key];
            } else {
                throw new \BadMethodCallException("Method {$method} does not exist.");
            }
        } else {
            try {
                return call_user_func_array([$instance, $method], $arguments);
            } catch (\Throwable $th) {
                throw new \BadMethodCallException($th->getMessage());
            }
        }
    }

}
