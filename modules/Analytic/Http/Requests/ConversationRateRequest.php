<?php

namespace Modules\Analytic\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class ConversationRateRequest
 * @package Modules\Analytic\Http\Requests
 */
class ConversationRateRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'site_id' => 'required|integer',
            'event' => 'nullable|string'
        ];
    }
}
