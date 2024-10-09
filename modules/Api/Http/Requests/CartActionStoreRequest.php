<?php

namespace Modules\Api\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CartActionStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'remote_id' => ['required', 'exists:App\Entities\Market,remoteId'],
            'status' => ['boolean', 'required']
        ];
    }
}
