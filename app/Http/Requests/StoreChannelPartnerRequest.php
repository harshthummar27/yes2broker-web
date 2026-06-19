<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Http\Requests\Concerns\SanitizesPhoneFields;
use App\Support\ValidationRules;
use Illuminate\Foundation\Http\FormRequest;

class StoreChannelPartnerRequest extends FormRequest
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
            'email' => ValidationRules::email(),
            'mobile' => ValidationRules::mobile(required: false),
            'city' => ['nullable', 'string', 'max:100'],
            'full_address' => ['nullable', 'string', 'max:500'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'gst_number' => ['nullable', 'string', 'max:50'],
            'remark' => ['nullable', 'string', 'max:2000'],
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
