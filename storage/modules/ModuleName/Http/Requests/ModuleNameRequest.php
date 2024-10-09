<?php

namespace Modules\ModuleName\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ModuleNameRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'number' => ['required', 'integer']
        ];
    }
}
