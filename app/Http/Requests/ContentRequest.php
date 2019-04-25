<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ContentRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        $rules = [
            'name' => 'required',
            'type' => [
                'required',
                Rule::in(['image', 'video', 'text', 'gallery', 'file', 'web']),
            ]
        ];

        if ($this->method() === 'POST') {
            $rules['folder'] = 'required';
            $rules['folder.id'] = 'required|exists:folders,id,deleted_at,NULL';
        }

        return $rules;
    }
}
