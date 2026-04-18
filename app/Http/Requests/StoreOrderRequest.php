<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isCustomer();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'service_id' => ['required', 'exists:services,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'material_source' => ['nullable', 'in:customer,jasa'],
            'material_status' => ['nullable', 'in:ready,po'],
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
            'service_id.required' => 'Layanan harus dipilih.',
            'service_id.exists' => 'Layanan yang dipilih tidak valid.',
            'quantity.required' => 'Jumlah harus diisi.',
            'quantity.integer' => 'Jumlah harus berupa angka.',
            'quantity.min' => 'Jumlah minimal adalah 1.',
            'notes.max' => 'Catatan maksimal 2000 karakter.',
            'material_source.in' => 'Sumber bahan harus "customer" atau "jasa".',
            'material_status.in' => 'Status bahan harus "ready" atau "po".',
        ];
    }
}
