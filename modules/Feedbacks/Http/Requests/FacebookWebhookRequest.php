<?php

namespace Modules\Feedbacks\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FacebookWebhookRequest extends FormRequest
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
            'object' => 'required|string',
            'entry.*.changes' => 'required',
        ];
    }
}
