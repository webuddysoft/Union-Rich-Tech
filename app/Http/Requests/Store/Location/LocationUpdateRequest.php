<?php

namespace App\Http\Requests\Store\Location;

use App\Models\Store\Location\Location;
use Illuminate\Foundation\Http\FormRequest;

class LocationUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $location = Location::where('uuid', $this->locationUuid)->first();

        if (!$location || $this->user()->cannot('update', $location)) {
            return false;
        }

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
            'location_name' => 'required|string',
            'shopper_limit' => 'required|integer|max:100'
        ];
    }
}
