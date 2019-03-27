<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BeaconContainerRequest extends FormRequest
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
        return [
            'container' => 'required',
            'container.id' => 'required|exists:content_containers,id,deleted_at,NULL'
        ];
    }
}
