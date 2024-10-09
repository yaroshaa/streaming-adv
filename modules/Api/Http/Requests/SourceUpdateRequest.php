<?php

namespace Modules\Api\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SourceUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'remote_id' => [
                'integer',
                'required',
                'unique:App\Entities\Source,remoteId,' . $this->route('source')->getid(),
            ],
            'name' => [
                'string',
                'required',
                'unique:App\Entities\Source,name,' . $this->route('source')->getid(),
                'max:255'
            ],
            'icon_link' => ['string', 'required', 'max:255']
        ];
    }
}
