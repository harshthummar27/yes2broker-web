<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Http\Requests\Concerns\SanitizesPhoneFields;
use App\Support\ValidationRules;
use Illuminate\Foundation\Http\FormRequest;

class StorePropertyInquiryRequest extends FormRequest
{
    use SanitizesPhoneFields;

    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'property' => ['nullable', 'string', 'max:255'],
            'name' => ['required_without_all:first_name,last_name', 'nullable', 'string', 'max:255'],
            'first_name' => ['required_without:name', 'nullable', 'string', 'max:100'],
            'last_name' => ['required_without:name', 'nullable', 'string', 'max:100'],
            'phone' => ValidationRules::mobile(),
            'email' => ValidationRules::email(),
            'message' => ['nullable', 'string', 'max:2000'],
            'source' => ['nullable', 'string', 'max:100'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return ValidationRules::contactMessages();
    }
}
