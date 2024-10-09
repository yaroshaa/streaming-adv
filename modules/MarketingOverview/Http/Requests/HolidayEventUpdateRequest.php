<?php

namespace Modules\MarketingOverview\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HolidayEventUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'id' => ['integer', 'required'],
            'title' => ['string', 'required', 'unique:App\Entities\HolidayEvent,title,' . $this->route('holiday_event')->getId(), 'max:255'],
            'date' => ['required', 'date'],
        ];
    }
}
