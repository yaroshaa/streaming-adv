<?php

namespace Modules\Api\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SourceStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'remote_id' => ['integer', 'required', 'unique:App\Entities\Source,remoteId'],
            'name' => ['string', 'required', 'unique:App\Entities\Source,name', 'max:255'],
            'icon_link' => ['string', 'required', 'max:255']
        ];
    }
}
