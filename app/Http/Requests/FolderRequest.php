<?php

namespace App\Http\Requests;

use App\Models\Folder;
use App\Rules\RequiredIdRule;
use Illuminate\Foundation\Http\FormRequest;

class FolderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if ($this->method() === 'POST') {
            return $this->user()->can('create', Folder::class);
        }

        return $this->user()->can('update', Folder::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'name' => ['required', 'max:255'],
            'order' => 'nullable|integer|max:4294967295',
            'category' => ['nullable', new RequiredIdRule],
            'category.id' => ['exists:categories,id'],
            'tags' => ['array'],
            'tags.*.id' => ['required', 'exists:tags,id']
        ];

        if ($this->method() === 'POST') {
            $rules['container'] = 'required';
            $rules['container.id'] = 'required|exists:containers,id,deleted_at,NULL';
        } else {
            $rules['container'] = ['nullable', new RequiredIdRule];
            $rules['container.id'] = 'exists:containers,id,deleted_at,NULL';
        }

        return $rules;
    }
}
