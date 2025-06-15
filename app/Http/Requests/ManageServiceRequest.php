<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ManageServiceRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'subscribe' => ['nullable', 'array'],
            'subscribe.*.service_id' => ['required', 'exists:services,id'],
            'subscribe.*.price' => ['required', 'numeric', 'gt:0'],

            'unsubscribe' => ['nullable', 'array'],
            'unsubscribe.*' => ['required', 'exists:services,id'],
            'force_unsubscribe' => 'boolean'
        ];
    }
}
