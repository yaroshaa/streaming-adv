<?php

namespace Modules\Analytic\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class EventsRequest
 * @package Modules\Analytic\Http\Requests
 */
class EventsRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
//            'date_from' => ['required', 'date'],
//            'date_to' => ['required', 'date'],
            'site_id' => ['required', 'site_id'],
            'event' => 'nullable|string'
        ];
    }
}
