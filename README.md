## Laravel Media Helper

# Getting started

## Installation

Install package with composer command

```
composer require anisaronno/laravel-media-helper
```

Store Image/File

```
    use AnisAronno\Media\Facades\Media;
    Media::upload($request, $fieldName, string $upload_dir)
```

Store Image/File with storage disk

```
    use AnisAronno\Media\Facades\Media;
    Media::setStorageDisk('public')->upload($request, $fieldName, string $upload_dir)
```

Get Image/File

```
    use AnisAronno\Media\Facades\Media;
    Media::getURL($path)
```

Get Image/File with storage disk

```
    use AnisAronno\Media\Facades\Media;
    Media::setStorageDisk('public')->getURL($path);
```

Delete Image/File

```
    use AnisAronno\Media\Facades\Media;
    Media::delete($path)
```

Delete Image/File with storage disk

```
    use AnisAronno\Media\Facades\Media;
    Media::setStorageDisk('public')->delete($path)
```

Get Default Image/File

```
    use AnisAronno\Media\Facades\Media;
    Media::getgetDefaultFilesURL();
```

```
Media::getgetDefaultFilesURL(true); // get associative array
```

```
    Media::getgetDefaultFilesURL(true, 'placeholder'); // get specific value
```

## Contribution Guide

Follow the [Link](https://github.com/anisAronno/multipurpose-admin-panel-boilerplate/blob/develop/CONTRIBUTING.md).

## License

The application is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
