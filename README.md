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
  - [Use as a Media Library with Storing Media in DB](#use-as-a-media-library-with-storing-media-in-db)
    - [Publish Migration, Factory, Config, Seeder](#publish-migration-factory-config-seeder)
    - [Run Migration](#run-migration)
    - [Run Seeder](#run-seeder)
    - [Define Relation in User Model](#define-relation-in-user-model)
    - [Use in Another Model for Storing Media](#use-in-another-model-for-storing-media)
    - [Use Seeder with Relation Mapping](#use-seeder-with-relation-mapping)
  - [Media/Image Retrieve, Store, Update and Delete](#mediaimage-retrieve-store-update-and-delete)
  - [Use Media with Relational Model](#use-media-with-relational-model)
  - [Working with Single or Featured Image](#working-with-single-or-featured-image)
  - [Helper Methods](#helper-methods)
  - [Fetch Media/Image from Relational Model](#fetch-mediaimage-from-relational-model)
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
use AnisAronno\Media\Facades\Media;
Media::upload($request, $fieldName, string $upload_dir)
```

### Store Image/File with Storage Disk
Upload an image or file with a specific storage disk:

```php
use AnisAronno\Media\Facades\Media;
Media::setStorageDisk('public')->upload($request, $fieldName, string $upload_dir)
```

### Get Image/File
Retrieve an image or file by its path:

```php
use AnisAronno\Media\Facades\Media;
Media::getURL($path)
```

### Get Image/File with Storage Disk
Retrieve an image or file with a specific storage disk:

```php
use AnisAronno\Media\Facades\Media;
Media::setStorageDisk('public')->getURL($path)
```

### Delete Image/File
Delete an image or file by its path:

```php
use AnisAronno\Media\Facades\Media;
Media::delete($path)
```

### Delete Image/File with Storage Disk
Delete an image or file with a specific storage disk:

```php
use AnisAronno\Media\Facades\Media;
Media::setStorageDisk('public')->delete($path)
```

### Get Default Image/File
Retrieve the default image or file:

```php
use AnisAronno\Media\Facades\Media;
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

## Use as a Media Library with Storing Media in DB
For media library features, follow these steps:

### Publish Migration, Factory, Config, Seeder
Publish the migration file, factory, configuration, and seeder:

```shell
php artisan vendor:publish
```

### Run Migration
Apply the migrations to set up the media storage table:

```shell
php artisan migrate
```

### Run Seeder
Seed the media storage table with initial data:

```shell
php artisan db:seed --class=ImageSeeder
```

### Define Relation in User Model
If you want to associate media with a user, add the following relation in your User model:

```php
public function images(): HasMany
{
    return $this->hasMany(Image::class, 'user_id', 'id');
}
```

### Use in Another Model for Storing Media
To use media storage in another model (e.g., Blog, Product), add the `HasMedia` trait:

```php
use AnisAronno\MediaHelper\Traits\HasMedia;
use HasMedia;
```

### Use Seeder with Relation Mapping
If you want to set up seed data with relation mapping (e.g., User has Blog, Blog uses HasMedia Trait), follow this code for creating a seeder:

```php
use App\Models\Blog;
use App\Models\User;
use Database\Factories\ImageFactory;

User::factory()->count(10)
    ->has(
        Blog::factory()->count(10)
        ->has(ImageFactory::new()->count(5), 'images')
        ->afterCreating(function ($blog) {
            $blog->images->first()->pivot->is_featured = 1;
            $blog->images->first()->pivot->save();
        })
    )
    ->create();
```

## Media/Image Retrieve, Store, Update and Delete
To manage your media storage, you can use the following routes:

- Get all images: `api/image` (GET)
- Get a single image: `api/image/{id}` (GET)
- Store an image: `api/image` (POST)
- Delete an image: `api/image/{id}` (DELETE)
- Update an image: `api/image/update` (POST)
- Delete all images: `image/delete-all` (POST)

## Use Media with Relational Model
If you want to store images for a relational model (e.g., Blog), you can use the following methods:

- Attach: `$blog->images()->attach(array $id)`
- Sync: `$blog->images()->sync(array $id)`
- Delete: `$blog->images()->detach(array $id)`

## Working with Single or Featured Image
To work with a single or featured image, use the `image` method and set `isFeatured` to `true` in the second parameter:

- Attach: `$blog->image()->attach(array $id, ['is_featured' => 1])`

Note: Sync and detach are the same; use `image` instead of `images`.

## Helper Methods
You can also use helper methods for media management:

-

 For Attach: `$blog->attachImages(array $ids, $isFeatured = false)`
- For Sync: `$blog->syncImages(array $ids, $isFeatured = false)`
- For Delete: `$blog->detachImages(array $ids, $isFeatured = false)`

## Fetch Media/Image from Relational Model
To retrieve media/images from a relational model:

- Fetch all images as an array: `$blog->images`
- Fetch the featured image only: `$blog->image`

You can access image properties like URL, title, mimes, size, and type:

- `$image->url`
- `$image->title`
- `$image->mimes`
- `$image->size`
- `$image->type`

## Contribution Guide
Please follow our [Contribution Guide](https://github.com/anisAronno/multipurpose-admin-panel-boilerplate/blob/develop/CONTRIBUTING.md) if you'd like to contribute to this package.

## License
This package is open-source software licensed under the [MIT License](https://opensource.org/licenses/MIT).