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
    Media::getDefaultFilesURL();
```

Get as a associative array

```
    Media::getDefaultFilesURL(true);
```

Get by specific value

```
     Media::getDefaultFilesURL(true, 'placeholder');
```
Or Get by specific method

```
    Media::getDefaultLogo();
    Media::getDefaultFavIcon();
    Media::getDefaultBanner();
    Media::getDefaultAvatar();
    Media::getDefaultPlaceholder();
```

# Use as a Media Library with Storing Media in DB

publish migration file, factory, config, seeder

```
    php artisan vendor:publish
```

## Run Migration

```
    php artisan migrate
```

## Run Seeder

```
    php artisan db:seed --class=ImageSeeder
```

## Define this relation in User Model

```
    public function images(): HasMany
    {
        return $this->hasMany(Image::class, 'user_id', 'id');
    }
```

## Now Use in another Model for storing image/media (Like: Blog, Product Model etc.)

Use HasMedia Trait in your targeted Model.

```
    use AnisAronno\MediaHelper\Traits\HasMedia;
    
    use HasMedia;

```

## Use seeder with relation mapping

Like: User have Blog and Blog use HasMedia Trait.
now follow this code for creating seeder

```
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

Follow this slug after your domain path

Get all image: `api/image` &nbsp; - &nbsp; (`@method('GET')`)<br>
Get single image: `api/image/{id}` &nbsp; - &nbsp; (`@method('GET')`)<br>
Store image: `api/image` &nbsp; - &nbsp; (`@method('POST')`)<br>
Delete image: `api/image/{id}` &nbsp; - &nbsp; (`@method('DELETE')`)<br>
Update image: `api/image/update` &nbsp; - &nbsp; (`@method('POST')`)<br>
Delete All Image: `image/delete-all` &nbsp; - &nbsp; (`@method('POST')`) <br>

## use Media with relational Model

Like: You want to store image for a blog model.

```
    $blog = Blog::query(); or new Blog(); //Blog Model Instance
    $blog->images()->attach(array $id);
```

If you want to sync or update image for a blog model.

```
    $blog = Blog::query(); or new Blog();
    $blog->images()->sync(array $id);
```

If you want to delete image/media image for a blog model.

```
    $blog = Blog::query(); or new Blog();
    $blog->images()->detach(array $id);
```

## Working with single or feature image
Just use `image` instead of `images` method and use isFeatured is `true` in 2nd parameter
```
    $blog = Blog::query(); or new Blog(); //Blog Model Instance
    $blog->image()->attach(array $id, ['is_featured' => 1]);
```
Note: Sync and detach are same. just use `image` instead of `images``.


## Or You can use helper method
Note: `isFeatured` options for make feature image for this blog post

For Attach:

```
    $blog = Blog::query(); or new Blog(); //Blog Model Instance
    $blog->attachImages(array $ids, $isFeatured = false);
```


For Sync:

```
    $blog = Blog::query(); or new Blog(); //Blog Model Instance
    $blog->syncImages(array $ids, $isFeatured = false);
```

For Delete:

```
    $blog = Blog::query(); or new Blog(); //Blog Model Instance
    $blog->detachImages(array $ids, $isFeatured = false);
```

## Fetch Media/Image from relational model
- Fetch all images as a array
```
    $blog = Blog::query(); or new Blog(); //Blog Model Instance
    $image = $blog->images;
```
- Fetch feature image only
```
    $blog = Blog::query(); or new Blog(); //Blog Model Instance
    $image = $blog->image;
```

Then you can show data with this way
```
    $image->url;
    $image->title;
    $image->mimes;
    $image->size;
    $image->type;
```

# Contribution Guide

Follow the [Link](https://github.com/anisAronno/multipurpose-admin-panel-boilerplate/blob/develop/CONTRIBUTING.md).

## License

The application is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
