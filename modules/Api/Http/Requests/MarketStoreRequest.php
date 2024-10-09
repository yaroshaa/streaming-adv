<?php

namespace Modules\Api\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MarketStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'remote_id' => ['integer', 'required', 'unique:App\Entities\Market,remoteId'],
            'name' => ['string', 'required', 'unique:App\Entities\Market,name', 'max:255'],
            'icon_link' => ['string', 'required', 'max:255'],
            'color' => ['string', 'required', 'max:255'],
        ];
    }
}
