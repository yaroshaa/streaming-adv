<?php

namespace Modules\Api\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WarehouseStatisticsStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'warehouse_id' => ['required', 'exists:App\Entities\Warehouse,id'],
            'in_packing' => ['integer', 'required'],
            'open' => ['integer', 'required'],
            'awaiting_stock' => ['integer', 'required'],
            'station' => ['integer', 'required'],
            'market_id' => ['required', 'exists:App\Entities\Market,remoteId'],
        ];
    }
}
