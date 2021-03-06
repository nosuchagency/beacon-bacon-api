<?php

namespace App\Http\Requests;

use App\Models\BeaconProvider;
use Illuminate\Foundation\Http\FormRequest;

class BeaconProviderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if ($this->method() === 'POST') {
            return $this->user()->can('create', BeaconProvider::class);
        }

        return $this->user()->can('update', BeaconProvider::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if ($this->method() === 'POST') {
            return $this->rulesForCreating();
        }

        return $this->rulesForUpdating();
    }

    /**
     * @return array
     */
    public function rulesForCreating()
    {
        return [
            'name' => ['required', 'max:255'],
            'type' => ['required', 'in:kontakt,estimote'],
            'meta.api_key' => ['required_if:type,kontakt'],
            'meta.app_id' => ['required_if:type,estimote'],
            'meta.app_token' => ['required_if:type,estimote']
        ];
    }

    /**
     * @return array
     */
    public function rulesForUpdating()
    {
        return [
            'name' => ['filled', 'max:255'],
        ];
    }
}
