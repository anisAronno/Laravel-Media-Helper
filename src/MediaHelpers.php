<?php

namespace AnisAronno\MediaHelper;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaHelpers
{
    private array $fileTypeFolders = [];
    private string $storageDisk;
    private $result;
    private static $instance;

    private function __construct($fileTypeArray = [])
    {
        $defaultFileTypeFolders = [
            'images' => ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg'],
            'videos' => ['mp4', 'webm', 'ogg', 'mpeg', 'wav', 'mkv'],
            'csv' => ['csv'],
            'excel' => ['xls', 'xlsx'],
            'pdf' => ['pdf'],
            'text' => ['txt'],
            'documents' => ['doc', 'docx'],
            'files' => ['zip', 'rar', 'gz', 'tar'],
        ];

        $this->fileTypeFolders = array_merge($defaultFileTypeFolders, $fileTypeArray);
        $this->storageDisk = Storage::getDefaultDriver() != 'local' ? Storage::getDefaultDriver() : 'public' ;

    }

    /**
     * @param  array  $fileTypeFolders
     * @return MediaHelpers|self
     */
    public static function getInstance(array $fileTypeFolders = []): MediaHelpers
    {
        if (!self::$instance) {
            self::$instance = new self($fileTypeFolders);
        }
        return self::$instance;
    }

    /**
     * @param $disk
     * @return $this
     */
    public function setStorageDisk($disk): MediaHelpers
    {
        $this->storageDisk = $disk;
        return $this;
    }

    /**
     * @param $assoc
     * @param $key
     * @return array|mixed
     */
    public function getAllDefaultFiles($assoc = false, $key = null)
    {
        $defaultFiles = $this->defaultFiles();

        if(!$assoc) {
            return $defaultFiles;
        } else {
            $defaultFilesArr = $this->mappingDefaultFiles($defaultFiles);

            if(is_null($key)) {
                return $defaultFilesArr;
            } else {
                return array_key_exists($key, $defaultFilesArr) ? $defaultFilesArr[$key] : [];
            }
        }
    }


    /**
     * @param $path
     * @return bool
     */
    public function isAllowedFileType($path = ''): bool
    {
        $extension = pathinfo(parse_url($path, PHP_URL_PATH), PATHINFO_EXTENSION);
        $allowedExtensions = array_merge(...array_values($this->fileTypeFolders));

        return in_array($extension, $allowedExtensions);
    }

    /**
     * @param $fullPath
     * @return string
     */
    public function getURL($fullPath): string
    {
        if (!empty($fullPath)) {
            $this->result = $this->processImageURL($fullPath);
        } else {
            $this->result = Storage::disk($this->storageDisk)->url($this->getAllDefaultFiles(true, 'placeholder'));
        }
        return $this->result;
    }

    /**
     * @param $request
     * @param $fieldName
     * @param  string  $uploadDir
     * @return false|mixed|string
     */
    public function upload($request, $fieldName, string $uploadDir = 'common')
    {
        try {
            if ($request->hasFile($fieldName)) {
                $file = $request->$fieldName;
                $extension = $file->extension();
                $filename = $this->generateUniqueFileName($file);
                $path = $this->getFilePath($filename, $uploadDir, $extension);

                if (!$this->isAllowedFileType($path)) {
                    return false;
                }
                $this->result = $this->storeFile($file, $path);
            } else {
                $this->result = false;
            }
        } catch (\Throwable $th) {
            $this->result = $th->getMessage();
        }

        return $this->result;
    }

    /**
     * @param $fullPath
     * @return bool|string
     */
    public function delete($fullPath)
    {
        $path = $this->getPathFromValue($fullPath);

        if (!$path || !$this->isAllowedFileType($path) || $this->isDefaultFile($path)) {
            $this->result = false;
        }

        try {
            if ($this->existsInStorage($path)) {
                $this->result = Storage::disk($this->storageDisk)->delete($path);
            }

            $this->result = false;
        } catch (\Throwable $th) {
            $this->result = $th->getMessage();
        }
       return $this->result;
    }

    /**
     * @param $fullPath
     * @return string
     */
    private function processImageURL($fullPath): string
    {
        $path = $this->getPathFromValue($fullPath);

        if ($this->isDefaultFile($fullPath) && $this->existsInStorage($fullPath)) {
            return Storage::disk($this->storageDisk)->url($fullPath);
        } elseif ($path && $this->existsInStorage($path)) {
            return Storage::disk($this->storageDisk)->url($path);
        } else {
            return $fullPath;
        }

    }

    /**
     * @param $file
     * @param $file_path
     * @return mixed
     */
    private function storeFile($file, $file_path)
    {
        $storageDisk = Storage::disk($this->storageDisk);
        $storageDisk->put($file_path, file_get_contents($file));

        return $file_path;
    }

    /**
     * @param $value
     * @return false|string
     */
    private function getPathFromValue($value)
    {
        $fileExtension = pathinfo(parse_url($value, PHP_URL_PATH), PATHINFO_EXTENSION);
        $fileTypeFolder = $this->getFileTypeFolder($fileExtension);

        return stristr($value, $fileTypeFolder);
    }

    /**
     * @param $path
     * @return bool
     */
    private function existsInStorage($path): bool
    {
        return Storage::disk($this->storageDisk)->exists($path);
    }

    /**
     * @param $file
     * @return string
     */
    private function generateUniqueFileName($file): string
    {
        $name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $slug = Str::slug($name);
        $extension = $file->extension();

        return substr($slug, 0, 150) . '-' . time() . '.' . $extension;
    }

    /**
     * @param $filename
     * @param $uploadDir
     * @param $extension
     * @return string
     */
    private function getFilePath($filename, $uploadDir, $extension): string
    {
        $fileTypeFolder = $this->getFileTypeFolder($extension);
        return "{$fileTypeFolder}/{$uploadDir}/" . date('Y-m') . "/{$filename}";
    }

    /**
     * @param $path
     * @return bool
     */
    private function isDefaultFile($path): bool
    {
        $defaultFiles = $this->defaultFiles();

        if (empty($defaultFiles)) {
            return false;
        }

        $defaultFolderPath = $this->findDefaultsFolderPath();

        return collect($defaultFiles)->some(function ($item) use ($defaultFolderPath) {
            $parts = explode('/', $item);
            array_pop($parts);
            return implode('/', $parts) === $defaultFolderPath;
        });
    }

    /**
     * @return array
     */
    private function defaultFiles(): array
    {
        $defaultFolderPath = $this->findDefaultsFolderPath();
        return $defaultFolderPath ? Storage::disk($this->storageDisk)->files($defaultFolderPath) : [];
    }

    /**
     * @param  string  $defaultFolderName
     * @return mixed
     */
    private function findDefaultsFolderPath(string $defaultFolderName = 'defaults')
    {
        $directories = Storage::disk($this->storageDisk)->allDirectories();
        $defaultsFolder = array_filter($directories, fn ($directory) => Str::contains($directory, $defaultFolderName));

        if (count($defaultsFolder) > 0) {
            return reset($defaultsFolder);
        } else {
            if (!Storage::exists($defaultFolderName)) {
                $folderPath = __DIR__.'/assets/images/';
                $files = File::allFiles($folderPath);

                foreach ($files as $file) {
                    $file = $file->getFilename();
                    $this->storeFile($folderPath.$file, $defaultFolderName.'/'.$file);
                }

                return $defaultFolderName;
            } else {
                return null;
            }
        }
    }

    /**
     * @param $extension
     * @return int|string
     */
    public function getFileTypeFolder($extension)
    {
        foreach ($this->fileTypeFolders as $folder => $extensions) {
            if (in_array($extension, $extensions)) {
                return $folder;
            }
        }

        return 'others';
    }


    /**
     * @param $defaultFiles
     * @return array
     */
    public function mappingDefaultFiles($defaultFiles): array
    {
        $fileMapping = [];

        foreach ($defaultFiles as $file) {
            $filename = pathinfo($file, PATHINFO_FILENAME);
            $fileMapping[$filename] = $file;
        }

        return $fileMapping;
    }
}
