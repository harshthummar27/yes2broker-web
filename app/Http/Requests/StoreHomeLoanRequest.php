<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Http\Requests\Concerns\SanitizesPhoneFields;
use App\Support\ValidationRules;
use Illuminate\Foundation\Http\FormRequest;

class StoreHomeLoanRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'phone' => ValidationRules::mobile(),
            'email' => ValidationRules::email(),
            'amount' => ['nullable', 'string', 'max:50'],
            'finalized' => ['nullable', 'in:yes,no'],
            'bank' => ['nullable', 'string', 'max:255'],
            'terms' => ['accepted'],
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
