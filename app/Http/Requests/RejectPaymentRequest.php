<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RejectPaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'rejection_note' => ['required', 'string', 'min:10', 'max:2000'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'rejection_note.required' => 'Alasan penolakan harus diisi.',
            'rejection_note.min' => 'Alasan penolakan minimal 10 karakter.',
            'rejection_note.max' => 'Alasan penolakan maksimal 2000 karakter.',
        ];
    }
}
