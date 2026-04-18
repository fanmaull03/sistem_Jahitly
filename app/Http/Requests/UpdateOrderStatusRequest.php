<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderStatusRequest extends FormRequest
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
            'status' => [
                'required',
                'in:menunggu_appointment,menunggu_bahan,diproses,dijahit,finishing,selesai',
            ],
            'notes' => ['nullable', 'string', 'max:2000'],
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
            'status.required' => 'Status pesanan harus diisi.',
            'status.in' => 'Status pesanan tidak valid. Pilih salah satu: menunggu_appointment, menunggu_bahan, diproses, dijahit, finishing, selesai.',
            'notes.max' => 'Catatan maksimal 2000 karakter.',
        ];
    }
}
