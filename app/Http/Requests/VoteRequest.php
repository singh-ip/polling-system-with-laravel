<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class VoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'option_id' => 'required|exists:poll_options,id',
        ];
    }
}
