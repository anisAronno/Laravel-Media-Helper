# Laravel Media Helper

## Table of Contents
- [Laravel Media Helper](#laravel-media-helper)
  - [Table of Contents](#table-of-contents)
  - [Introduction](#introduction)
  - [Installation](#installation)
  - [Usage](#usage)
    - [Store Image/File](#store-imagefile)
    - [Store Image/File with Storage Disk](#store-imagefile-with-storage-disk)
    - [Get Image/File](#get-imagefile)
    - [Get Image/File with Storage Disk](#get-imagefile-with-storage-disk)
    - [Delete Image/File](#delete-imagefile)
    - [Delete Image/File with Storage Disk](#delete-imagefile-with-storage-disk)
    - [Get Default Image/File](#get-default-imagefile)
    - [Get as an Associative Array](#get-as-an-associative-array)
    - [Get by Specific Value](#get-by-specific-value)
  - [Contribution Guide](#contribution-guide)
  - [License](#license)

## Introduction
The Laravel Media Helper simplifies the management of media and image files in your Laravel project. This README provides installation instructions, usage examples, and additional information.

## Installation
To get started, install the package using Composer:

```shell
composer require anisaronno/laravel-media-helper
```

## Usage

### Store Image/File
Upload an image or file:

```php
use AnisAronno\MediaHelper\Facades\Media;
Media::upload($request, $fieldName, string $upload_dir)
```

### Store Image/File with Storage Disk
Upload an image or file with a specific storage disk:

```php
use AnisAronno\MediaHelper\Facades\Media;
Media::setStorageDisk('public')->upload($request, $fieldName, string $upload_dir)
```

### Get Image/File
Retrieve an image or file by its path:

```php
use AnisAronno\MediaHelper\Facades\Media;
Media::getURL($path)
```

### Get Image/File with Storage Disk
Retrieve an image or file with a specific storage disk:

```php
use AnisAronno\MediaHelper\Facades\Media;
Media::setStorageDisk('public')->getURL($path)
```

### Delete Image/File
Delete an image or file by its path:

```php
use AnisAronno\MediaHelper\Facades\Media;
Media::delete($path)
```

### Delete Image/File with Storage Disk
Delete an image or file with a specific storage disk:

```php
use AnisAronno\MediaHelper\Facades\Media;
Media::setStorageDisk('public')->delete($path)
```

### Get Default Image/File
Retrieve the default image or file:

```php
use AnisAronno\MediaHelper\Facades\Media;
Media::getDefaultFilesURL();
```

### Get as an Associative Array
Retrieve default files as an associative array:

```php
Media::getDefaultFilesURL(true);
```

### Get by Specific Value
Retrieve default files by specific value or method:

```php
Media::getDefaultFilesURL(true, 'placeholder');
Media::getDefaultLogo();
Media::getDefaultFavIcon();
Media::getDefaultBanner();
Media::getDefaultAvatar();
Media::getDefaultPlaceholder();
```

## Contribution Guide
Please follow our [Contribution Guide](https://github.com/anisAronno/multipurpose-admin-panel-boilerplate/blob/develop/CONTRIBUTING.md) if you'd like to contribute to this package.

## License
This package is open-source software licensed under the [MIT License](https://opensource.org/licenses/MIT).