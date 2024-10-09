<?php

namespace Modules\Feedbacks\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class HelpScoutWebhookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        Log::info($this->header('X-HELPSCOUT-SIGNATURE'));
        Log::info(base64_encode(
            hash_hmac('sha1', $this->getContent(), config('helpscout.key'), true)
        ));
        Log::info($this->getContent());
        return $this->header('X-HELPSCOUT-SIGNATURE') === base64_encode(
                hash_hmac('sha1', $this->getContent(), config('helpscout.key'), true)
            );
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'mailboxId' => ['required', 'integer'],
            'createdAt' => ['date'],
        ];
    }
}
