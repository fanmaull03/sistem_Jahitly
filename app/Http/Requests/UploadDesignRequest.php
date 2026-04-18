<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadDesignRequest extends FormRequest
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
            'design_file' => ['required', 'file', 'mimes:jpg,jpeg,png', 'max:5120'],
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
            'design_file.required' => 'File desain harus diunggah.',
            'design_file.file' => 'File desain harus berupa file yang valid.',
            'design_file.mimes' => 'File desain harus berformat JPG atau PNG.',
            'design_file.max' => 'Ukuran file desain maksimal 5MB.',
        ];
    }
}
