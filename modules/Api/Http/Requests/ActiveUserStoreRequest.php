<?php

namespace Modules\Api\Http\Requests;

use App\ClickHouse\Repositories\BaseActiveUserRepository;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ActiveUserStoreRequest extends FormRequest
{
    public function rules(BaseActiveUserRepository $repository): array
    {
        return [
            'remote_id' => ['required', 'exists:App\Entities\Market,remoteId'],
            'status' => ['boolean', 'required']
        ];
    }
}
