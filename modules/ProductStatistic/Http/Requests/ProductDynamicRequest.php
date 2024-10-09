<?php

namespace Modules\ProductStatistic\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductDynamicRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'filter.remote_id' => ['required', 'string'],
            'filter.date.0' => ['required', 'date'],
            'filter.date.1' => ['required', 'date'],
            'filter.currency.id' => ['required', 'exists:App\Entities\Currency,id'],
            'filter.market.*.id' => ['exists:App\Entities\Market,remoteId'],
        ];
    }

    public function messages(): array
    {
        return [
            'filter.date.0.date' => 'Date from invalid',
            'filter.date.1.date' => 'Date to invalid',
            'filter.date.0.required' => 'Date from required',
            'filter.date.1.required' => 'Date to required',
        ];
    }
}
