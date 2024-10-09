<?php


namespace Modules\Api\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;

class WarehouseUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => [
                'string',
                'required',
                'unique:App\Entities\Warehouse,name,' . $this->route('warehouse')->getId(),
                'max:255'
            ],
        ];
    }
}
