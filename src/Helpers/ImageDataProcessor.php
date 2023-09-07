<?php

namespace AnisAronno\MediaHelper\Helpers;

use AnisAronno\MediaHelper\Facades\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ImageDataProcessor
{
    /**
     * Image Data Processor
     *
     * @param Request $request
     * @return array
     */
    public static function process(Request $request, $field = 'image'): array
    {
        $data = [];
        
        if ($request->$field) {
            $data['url'] = Media::upload($request, $field, Str::plural($field));
            $data['mimes'] = $request->$field->extension();
            $data['type'] = $request->$field->getClientMimeType();
            $data['size'] = number_format($request->$field->getSize() / (1024 * 1024), 2, '.', '')."MB";
        }

        return $data;
    }
}
