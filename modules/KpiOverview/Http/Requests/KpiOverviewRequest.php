<?php


namespace Modules\KpiOverview\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;

class KpiOverviewRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'date.0' => ['required', 'date'],
            'date.1' => ['required', 'date'],
            'currency.id' => ['required', 'exists:App\Entities\Currency,id'],
            'market.*.id' => ['exists:App\Entities\Market,remoteId'],
            'date_granularity' => ['required', 'string']
        ];
    }

    public function messages(): array
    {
        return [
            'date.0.date' => 'Date from invalid',
            'date.1.date' => 'Date to invalid',
            'date.0.required' => 'Date from required',
            'date.1.required' => 'Date to required',
        ];
    }
}
