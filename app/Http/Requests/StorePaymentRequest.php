<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
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
     * Logika kondisional:
     * - File bukti wajib untuk metode transfer dan qris
     * - File bukti tidak wajib untuk metode cash
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $proofRule = ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'];

        // File bukti wajib untuk transfer dan qris
        if (in_array($this->input('payment_method'), ['transfer', 'qris'], true)) {
            $proofRule = ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'];
        }

        return [
            'payment_method' => ['required', 'in:transfer,qris,cash'],
            'amount' => ['required', 'numeric', 'min:1000'],
            'proof_file' => $proofRule,
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
            'payment_method.required' => 'Metode pembayaran harus dipilih.',
            'payment_method.in' => 'Metode pembayaran harus transfer, qris, atau cash.',
            'amount.required' => 'Nominal pembayaran harus diisi.',
            'amount.numeric' => 'Nominal pembayaran harus berupa angka.',
            'amount.min' => 'Nominal pembayaran minimal Rp 1.000.',
            'proof_file.required' => 'Bukti pembayaran wajib diunggah untuk metode transfer/QRIS.',
            'proof_file.file' => 'Bukti pembayaran harus berupa file yang valid.',
            'proof_file.mimes' => 'Bukti pembayaran harus berformat JPG, PNG, atau PDF.',
            'proof_file.max' => 'Ukuran file bukti pembayaran maksimal 5MB.',
        ];
    }
}
