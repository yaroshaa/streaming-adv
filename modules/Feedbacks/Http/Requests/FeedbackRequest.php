<?php

namespace Modules\Feedbacks\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FeedbackRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'data.*.name' => 'required|string',
            'data.*.message' => 'required|string',
            'data.*.created_at' => 'required|date',
            'data.*.market_id' => 'required|integer',
            'data.*.source_id' => 'required|integer',
            'data.*.url' => 'string',
        ];
    }
}
