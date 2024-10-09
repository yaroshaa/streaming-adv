<?php

namespace Modules\Api\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WarehouseStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['string', 'required', 'unique:App\Entities\Warehouse,name', 'max:255'],
        ];
    }
}
