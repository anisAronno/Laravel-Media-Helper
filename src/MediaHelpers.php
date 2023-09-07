<?php

namespace AnisAronno\MediaHelper;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaHelpers
{
    private $fileTypeFolders = [];
    private $storageDisk;
    private $storageURL;
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
        $this->storageDisk = Storage::getDefaultDriver();
        $this->storageURL = $this->storageDisk == 'local' ? 'public/' : '';
    }

    public static function getInstance($fileTypeFolders = [])
    {
        if (!self::$instance) {
            self::$instance = new self($fileTypeFolders);
        }
        return self::$instance;
    }

    public function setStorageDisk($disk)
    {
        $this->storageDisk = $disk;
        $this->storageURL = $this->storageDisk == 'local' ? 'public/' : '';

        return $this;
    }
    public function getAllDefaultFiles($assoc = false, $key = null)
    {
        $defaultFiles = $this->defaultFiles();

        if($assoc == false) {
            return $defaultFiles;
        } else {
            $defaultFilesArr = $this->mappingDefaultFiles($defaultFiles);

            if(is_null($key)) {
                return $defaultFilesArr;
            } else {
                return is_array($defaultFilesArr) && array_key_exists($key, $defaultFilesArr) ? $defaultFilesArr[$key] : [];
            }
        }
    }


    public function isAllowedFileType($path = ''): bool
    {
        $extension = pathinfo(parse_url($path, PHP_URL_PATH), PATHINFO_EXTENSION);
        $allowedExtensions = array_merge(...array_values($this->fileTypeFolders));

        return in_array($extension, $allowedExtensions);
    }

    public function getURL($fullPath)
    {
        if (!empty($fullPath)) {

            $this->result = $this->processImageURL($fullPath);
        } else {
            $this->result = Storage::disk($this->storageDisk)->url($this->getPlaceholderImage());
        }
        return $this->result;
    }

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
                logger($path);
                $this->result = $this->storeFile($file, $path);
            } else {
                $this->result = false;
            }
        } catch (\Throwable $th) {
            $this->result = $th->getMessage();
        }

        return $this->result;
    }

    public function delete($fullPath): bool|string
    {
        $path = $this->getPathFromValue($fullPath);

        if (!$path || !$this->isAllowedFileType($path) || $this->isDefaultFile($path)) {
            $this->result = false;
        }

        try {
            if ($this->existsInStorage($path)) {
                $this->result = Storage::disk($this->storageDisk)->delete($this->fullPath($path));
            }

            $this->result = false;
        } catch (\Throwable $th) {
            $this->result = $th->getMessage();
        }
       return $this->result;
    }

    private function processImageURL($fullPath): string
    {
        $path = $this->getPathFromValue($fullPath);

        if ($this->isDefaultFile($fullPath) && $this->existsInStorage($fullPath)) {
            return Storage::url($fullPath);
        } elseif ($path && $this->existsInStorage($path)) {
            return Storage::url($path);
        } else {
            return $fullPath;
        }

    }

    private function storeFile($file, $file_path)
    {

        $storageDisk = Storage::disk($this->storageDisk);
        $storageDisk->put($this->fullPath($file_path), file_get_contents($file));

        return $file_path;
    }

    private function getPathFromValue($value)
    {
        $fileExtension = pathinfo(parse_url($value, PHP_URL_PATH), PATHINFO_EXTENSION);
        $fileTypeFolder = $this->getFileTypeFolder($fileExtension);

        return stristr($value, $fileTypeFolder);
    }

    private function existsInStorage($path)
    {
        return Storage::disk($this->storageDisk)->exists($this->fullPath($path));
    }

    private function generateUniqueFileName($file)
    {
        $name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $slug = Str::slug($name);
        $extension = $file->extension();

        return substr($slug, 0, 150) . '-' . time() . '.' . $extension;
    }

    private function getFilePath($filename, $uploadDir, $extension)
    {
        $fileTypeFolder = $this->getFileTypeFolder($extension);
        return "{$fileTypeFolder}/{$uploadDir}/" . date('Y-m') . "/{$filename}";
    }

    private function isDefaultFile($path): bool
    {
        $defaultFiles = $this->defaultFiles();

        if (empty($defaultFiles)) {
            return false;
        }

        $defaultFolderPath = $this->findDefaultsFolderPath();

        $trimPath = stristr($path, $defaultFolderPath);

        return in_array($trimPath, $defaultFiles);
    }

    private function defaultFiles()
    {
        $defaultFolderPath = $this->findDefaultsFolderPath();
        return $defaultFolderPath ? Storage::disk($this->storageDisk)->files($defaultFolderPath) : [];
    }

    private function findDefaultsFolderPath($defaultFolderName = 'defaults')
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

    private function getFileTypeFolder($extension)
    {
        foreach ($this->fileTypeFolders as $folder => $extensions) {
            if (in_array($extension, $extensions)) {
                return $folder;
            }
        }

        return 'others';
    }


    public function mappingDefaultFiles($defaultFiles): array
    {
        $fileMapping = [];

        foreach ($defaultFiles as $file) {
            $filename = pathinfo($file, PATHINFO_FILENAME);
            $fileMapping[$filename] = $file;
        }

        return $fileMapping;
    }

    private function fullPath($value): string
    {
        return  $this->storageURL . $value;
    }


}
