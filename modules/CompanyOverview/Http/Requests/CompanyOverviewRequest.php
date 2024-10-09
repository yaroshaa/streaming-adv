<?php

namespace Modules\CompanyOverview\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompanyOverviewRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'date.0' => ['required', 'date'],
            'date.1' => ['required', 'date'],
            'currency.id' => ['required', 'exists:App\Entities\Currency,id'],
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
