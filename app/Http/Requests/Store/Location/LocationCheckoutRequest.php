<?php

namespace App\Http\Requests\Store\Location;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Store\Location\Location;
use App\Models\Shopper\Shopper;

class LocationCheckoutRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $location = Location::where('uuid', $this->locationUuid)->first();

        if ($this->user()->cannot('view', $location)) {
            return false;
        }
        
        $shopper = Shopper::find($this->shopper_id);
        if (!$shopper || $shopper->location_id != $location->id) {
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
            //
        ];
    }
}
