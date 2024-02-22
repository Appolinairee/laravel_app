<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class OrderStoreRequest extends FormRequest
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
            'shipping_address' => 'required|string',
            'shipping_price' => 'required|numeric|min:0',
            'shipping_preview' => 'required|string',
            'shipping_service' => 'required|string',
            'shipping_date' => 'required|date', 
        ];
    }
}
