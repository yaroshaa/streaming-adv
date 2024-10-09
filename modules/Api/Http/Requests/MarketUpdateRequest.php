<?php

namespace Modules\Api\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MarketUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'remote_id' => [
                'integer',
                'required',
                'unique:App\Entities\Market,remoteId,' . $this->route('market')->getid(),
            ],
            'name' => [
                'string',
                'required',
                'unique:App\Entities\Market,name,' . $this->route('market')->getid(),
                'max:255'
            ],
            'icon_link' => ['string', 'required', 'max:255'],
            'color' => ['string', 'required', 'max:255'],
        ];
    }
}
