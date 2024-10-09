<?php


namespace Modules\Api\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;

class MarketingChannelUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => [
                'string',
                'required',
                'unique:App\Entities\MarketingChannel,name,' . $this->route('channel')->getId(),
                'max:255'
            ],
            'icon_link' => ['string', 'required', 'max:255'],
        ];
    }
}
