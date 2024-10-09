<?php

namespace Modules\Orders\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderListRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'from' => ['required', 'date'],
            'currency.id' => ['required', 'exists:App\Entities\Currency,id'],
            'percentile' => ['numeric', 'nullable'],
            'product.remote_id' => ['numeric', 'nullable'],
            'orderStatus.id' => ['exists:App\Entities\OrderStatus,remoteId'],
            'market.*.id' => ['exists:App\Entities\Market,remoteId'],
            'weight.greater_than' => ['numeric', 'nullable'],
            'weight.lower_than' => ['numeric', 'nullable'],
        ];
    }

    public function messages(): array
    {
        return [
            'from.date' => 'Date from invalid',
            'from.required' => 'Date from required',
        ];
    }
}
