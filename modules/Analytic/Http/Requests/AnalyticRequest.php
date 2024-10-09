<?php

namespace Modules\Analytic\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AnalyticRequest extends FormRequest
{
    public function rules(): array
    {
        return [
//            'number' => ['required', 'integer']
        ];
    }
}
