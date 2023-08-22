<?php

namespace AnisAronno\MediaHelper;

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

    public function url($fullPath)
    {
        $this->result = $this->getUrl($fullPath);
        return $this;
    }

    public function get()
    {
        return $this->result;
    }

    public function getDefaultFiles()
    {
        return $this->defaultFiles();
    }
    public function getUrl($fullPath): string
    {
        if (!empty($fullPath)) {
            $path = $this->getPathFromValue($fullPath);

            if ($this->isDefaultFile($fullPath) && $this->existsInStorage($fullPath)) {
                return Storage::url($fullPath);
            } elseif ($path && $this->existsInStorage($path)) {
                return Storage::url($path);
            } else {
                return $fullPath;
            }
        } else {
            return Storage::disk($this->storageDisk)->url($this->getDefaultPlaceholder());
        }
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

                return $this->storeFile($file, $path);
            } else {
                return false;
            }
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    public function delete($fullPath): bool|string
    {
        $path = $this->getPathFromValue($fullPath);

        if (!$path || !$this->isAllowedFileType($path) || $this->isDefaultFile($path)) {
            return false;
        }

        try {
            if ($this->existsInStorage($path)) {
                return Storage::disk($this->storageDisk)->delete($this->fullPath($path));
            }

            return false;
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
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

    private function isAllowedFileType($path = ''): bool
    {
        $extension = pathinfo(parse_url($path, PHP_URL_PATH), PATHINFO_EXTENSION);
        $allowedExtensions = array_merge(...array_values($this->fileTypeFolders));

        return in_array($extension, $allowedExtensions);
    }

    private function getDefaultPlaceholder()
    {
        $defaultFolderPath = $this->findDefaultsFolderPath();
        return $defaultFolderPath ? "{$defaultFolderPath}/placeholder.png" : '';
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

    private function findDefaultsFolderPath($defaultFolderName = 'defaults'): ?string
    {
        $directories = Storage::disk($this->storageDisk)->allDirectories();
        $defaultsFolder = array_filter($directories, fn ($directory) => Str::contains($directory, $defaultFolderName));

        if (count($defaultsFolder) > 0) {
            return reset($defaultsFolder);
        } else {
            if (!Storage::exists($defaultFolderName)) {
                Storage::makeDirectory($defaultFolderName);
                $imageUrl = __DIR__ . '/assets/images/placeholder.png';
                $this->storeFile($imageUrl, "{$defaultFolderName}/placeholder.png");
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

    private function storeFile($file, $file_path)
    {
        $storageDisk = Storage::disk($this->storageDisk);
        $storageDisk->put('public/' . $this->fullPath($file_path), file_get_contents($file));

        return $file_path;
    }

    private function fullPath($value): string
    {
        return  $this->storageURL . $value;
    }
}
