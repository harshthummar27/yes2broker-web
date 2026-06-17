<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreHomeLoanRequest extends FormRequest
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
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:255'],
            'amount' => ['nullable', 'string', 'max:50'],
            'finalized' => ['nullable', 'in:yes,no'],
            'bank' => ['nullable', 'string', 'max:255'],
            'terms' => ['accepted'],
            'source' => ['nullable', 'string', 'max:100'],
        ];
    }
}
