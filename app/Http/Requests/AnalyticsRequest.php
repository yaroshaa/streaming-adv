<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class AnalyticsRequest
 * @package App\Http\Requests
 */
class AnalyticsRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'key' => 'required'
        ];
    }
}
