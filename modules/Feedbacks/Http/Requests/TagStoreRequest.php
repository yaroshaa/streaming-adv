<?php

namespace Modules\Feedbacks\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TagStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['string', 'required', 'unique:App\Entities\Tag,name', 'max:255'],
            'color' => ['string', 'required'],
            'keywords' => ['required'],
            'keywords.*' => ['string', 'required'],
        ];
    }
}
