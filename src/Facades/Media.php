<?php

namespace AnisAronno\MediaHelper\Facades;

use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Str;

/**
 * @method static this setStorageDisk(string $disk)
 * @method static string getDefaultFiles(bool $includePath = false)
 * @method static string getDefaultLogo()
 * @method static string getDefaultFavIcon()
 * @method static string getDefaultBanner()
 * @method static string getDefaultAvatar()
 * @method static string getDefaultPlaceholder()
 * @method static string getURL(string $path)
 * @method static false|mixed|string upload($request, $fieldName, string $upload_dir)
 * @method static bool|string delete($value)
 * @method static string getFileTypeFolder($extension)
 *
 * @see \AnisAronno\MediaHelper\MediaHelpers
 */
class Media extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'Media';
    }

    /**
     * @param $method
     * @param $args
     * @return mixed
     */
    public static function __callStatic($method, $args)
    {
        $instance = static::resolveFacadeInstance(static::getFacadeAccessor());
        $files = $instance->getAllDefaultFiles(true);

        if ($method === 'getDefaultFiles') {
            return array_map(function ($file) use ($instance) {
                return $instance->getURL($file);
            }, $files);
        }

        if(Str::startsWith($method, 'getDefault')) {
            $key = strtolower(trim(str_replace('getDefault', '', $method)));

            if (array_key_exists($key, $files)) {
                return $instance->getURL($files[$key]);
            } else {
                throw new \BadMethodCallException("Method {$method} does not exist.");
            }
        } else {
            try {
                return call_user_func_array([$instance, $method], $args);
            } catch (\Throwable $th) {
                throw new \BadMethodCallException($th->getMessage());
            }
        }
    }

}
