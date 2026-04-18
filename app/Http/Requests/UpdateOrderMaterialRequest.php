<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderMaterialRequest extends FormRequest
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
            'material_source' => ['required', 'in:customer,jasa'],
            'material_status' => ['required', 'in:ready,po'],
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
            'material_source.required' => 'Sumber bahan harus diisi.',
            'material_source.in' => 'Sumber bahan harus "customer" atau "jasa".',
            'material_status.required' => 'Status bahan harus diisi.',
            'material_status.in' => 'Status bahan harus "ready" atau "po".',
        ];
    }
}
