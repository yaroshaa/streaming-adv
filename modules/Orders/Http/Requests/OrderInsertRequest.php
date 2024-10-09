<?php

namespace Modules\Orders\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderInsertRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'data.*.order_id' => 'required|integer',
            'data.*.address' => 'required|string',
            'data.*.updated_at' => 'required|date',
            'data.*.status' => 'required|array',
            'data.*.status.remote_id' => 'required|integer',
            'data.*.status.name' => 'required|string',
            'data.*.customer' => 'required|array',
            'data.*.customer.remote_id' => 'required|integer',
            'data.*.customer.name' => 'required|string',
            'data.*.currency' => 'required|array',
            'data.*.currency.code' => 'required|string',
            'data.*.market' => 'required|array',
            'data.*.market.remote_id' => 'required|integer',
            'data.*.market.name' => 'required|string',
            'data.*.products' => 'required|array',
            'data.*.products.*.remote_id' => 'required|integer',
            'data.*.products.*.name' => 'required|string',
            'data.*.products.*.price' => 'required',
            'data.*.products.*.profit' => 'required',
            'data.*.products.*.qty' => 'required|integer',
        ];
    }
}
