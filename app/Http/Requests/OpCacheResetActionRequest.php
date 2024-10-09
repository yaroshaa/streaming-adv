<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OpCacheResetActionRequest extends FormRequest
{
    const SECRET = '95b3f901151d2810a02da40a1b6c4e6da628ed6c3f392ed82963e4ccc62f931a';

    public function authorize()
    {
        return self::SECRET === $this->route('secret', '');
    }

    public function rules(): array
    {
        return [];
    }
}
