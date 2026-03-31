<?php

namespace App\Http\Requests;

use App\Models\Order;
use App\Models\ServiceZip;
use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'in:adhoc,subscription'],
            'pickup_location_id' => ['required', 'exists:pickup_locations,id'],
            'pickup_link' => ['required', 'url', 'max:2048'],
            'pickup_time' => ['required', 'date', 'after:now'],
            'delivery_address' => ['required', 'string', 'max:255'],
            'delivery_city' => ['required', 'string', 'max:100'],
            'delivery_state' => ['required', 'string', 'size:2'],
            'delivery_zip' => ['required', 'string', 'max:10', function ($attr, $value, $fail) {
                if (! ServiceZip::allows($value)) {
                    $fail('Sorry, we don\'t currently deliver to that ZIP code. Please check our service area or contact us.');
                }
            }],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
