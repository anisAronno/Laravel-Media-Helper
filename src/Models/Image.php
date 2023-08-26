<?php

namespace AnisAronno\MediaHelper\Models;

use AnisAronno\MediaHelper\Facades\Media;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'url',
        'mimes',
        'type',
        'size',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function getUrlAttribute($value)
    {
        return  $this->attributes['url'] = Media::getUrl($value);
    }
}
