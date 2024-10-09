<?php

namespace Modules\MarketingOverview\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HolidayEventStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'title' => ['string', 'required', 'unique:App\Entities\HolidayEvent,title', 'max:255'],
            'date' => ['required', 'date'],
        ];
    }
}
