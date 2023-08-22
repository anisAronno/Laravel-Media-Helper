## Laravel Media helper

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

Get Image/File

```
    use AnisAronno\Media\Facades\Media;
    Media::getUrl($path)
```

Get Image/File with set storage disk

```
    use AnisAronno\Media\Facades\Media;
    Media::setStorageDisk('public')->url($path)->get();
```

Delete Image/File

```
    use AnisAronno\Media\Facades\Media;
    Media::delete($path)
```

## Contribution Guide

Follow the [Link](https://github.com/anisAronno/multipurpose-admin-panel-boilerplate/blob/develop/CONTRIBUTING.md).

## License

The application is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
