<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'       => ['required', 'string', 'max:255'],
            'content'     => ['required', 'string'],
            'channels'    => ['required', 'array', 'min:1'],
            'channels.*'  => ['required', 'string', 'in:email,slack,sms'],
        ];
    }
}
