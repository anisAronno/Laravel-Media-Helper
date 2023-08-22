<?php

namespace AnisAronno\Media;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaHelpers
{
    private $fileTypeFolders;
    private $storageDisk;
    private $storageURL;

    private static $instance;

    private $result;

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
        $this->storageDisk =  Storage::getDefaultDriver();
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

    public function url($value)
    {
        $this->result = $this->getUrl($value);
        return $this;
    }

    public function get()
    {
        return $this->result;
    }

    public function getUrl($value): string
    {
        if (!empty($value)) {
            $fileExtension = pathinfo(parse_url($value, PHP_URL_PATH), PATHINFO_EXTENSION);
            $fileTypeFolder = $this->getFileTypeFolder($fileExtension);
            $path = stristr($value, $fileTypeFolder);

            if ($this->isDefaultFile($value) && Storage::disk($this->storageDisk)->exists($this->fullPath($value))) {
                return Storage::url($value);
            } elseif (!empty($path) && Storage::disk($this->storageDisk)->exists($this->fullPath($path))) {
                return Storage::url($path);
            } else {
                return $value;
            }
        } else {
            return Storage::disk($this->storageDisk)->url($this->findDefaultsFolderPath().'/placeholder.png');
        }
    }
    public function upload($request, $file_name, string $upload_dir)
    {
        try {
            if ($request->hasFile($file_name)) {
                $file = $request->$file_name;
                $extension = $file->extension();
                $filename = time().'.'.$extension;
                $fileTypeFolder = $this->getFileTypeFolder($extension);
                $up_path = $fileTypeFolder.'/'.$upload_dir.'/'.date('Y-m');
                $filePath = $up_path.'/'.$filename;

                if (!$this->isAllowFileType($filePath)) {
                    return false;
                }

                return $this->store($file, $filePath);
            } else {
                return false;
            }
        } catch (\Throwable $th) {
            return false;
        }
    }

    private function store($file, $file_path)
    {
        $storageDisk = Storage::disk($this->storageDisk);
        $storageDisk->put('public/'.$this->fullPath($file_path), file_get_contents($file));

        return $file_path;
    }

    private function fullPath($value): string
    {
        return  $this->storageURL.$value;
    }

    public function deleteFile($value): bool
    {
        $path = stristr($value, 'images');

        if (!$this->isAllowFileType($path) || $this->isDefaultFile($path)) {
            return false;
        }

        try {
            if (Storage::disk($this->storageDisk)->exists($this->fullPath($path))) {
                Storage::disk($this->storageDisk)->delete($this->fullPath($path));
                return true;
            }

            return false;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function isAllowFileType($path = ''): bool
    {
        $extension = pathinfo(
            parse_url($path, PHP_URL_PATH),
            PATHINFO_EXTENSION
        );

        $allowedExtensions = array_merge(...array_values($this->fileTypeFolders));

        return in_array($extension, $allowedExtensions);
    }


    private function isDefaultFile($path): bool
    {
        $disk = Storage::disk($this->storageDisk);
        $defaultFolderPath = $this->findDefaultsFolderPath();

        $defaultFiles = $disk->files($defaultFolderPath);
        $trimPath = stristr($path, $defaultFolderPath);

        return in_array($trimPath, $defaultFiles);
    }

    private function findDefaultsFolderPath(): ?string
    {
        $directories = Storage::disk('public')->allDirectories();
        $defaultsFolder = array_filter($directories, fn ($directory) => Str::contains($directory, 'defaults'));

        return count($defaultsFolder) > 0 ? reset($defaultsFolder) : null;
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
}
