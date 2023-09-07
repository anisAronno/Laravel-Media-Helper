<?php

namespace AnisAronno\MediaHelper\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateImageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'title' => 'nullable|string|max:250|min:3',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,gif,svg,webp|max:2048',
        ];
    }
}
