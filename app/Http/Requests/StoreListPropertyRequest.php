<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreListPropertyRequest extends FormRequest
{
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
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'alternate_phone' => ['required', 'string', 'max:20'],
            'property_title' => ['required', 'string', 'max:255'],
            'type' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'max:50'],
            'owner_type' => ['nullable', 'string', 'max:100'],
            'remark' => ['nullable', 'string', 'max:2000'],
            'property_address' => ['nullable', 'string', 'max:500'],
            'property_image' => ['nullable', 'array', 'max:10'],
            'property_image.*' => ['image', 'max:5120'],
            'verification' => ['accepted'],
            'source' => ['nullable', 'string', 'max:100'],
        ];
    }
}
