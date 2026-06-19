<?php

declare(strict_types=1);

namespace App\Support;

class ValidationRules
{
    /**
     * @return list<string>
     */
    public static function email(bool $required = true): array
    {
        return $required
            ? ['required', 'string', 'email', 'max:255']
            : ['nullable', 'string', 'email', 'max:255'];
    }

    /**
     * @return list<string>
     */
    public static function mobile(bool $required = true): array
    {
        return $required
            ? ['required', 'string', 'digits:10']
            : ['nullable', 'string', 'digits:10'];
    }

    /**
     * @return array<string, string>
     */
    public static function emailMessages(): array
    {
        return [
            'email.required' => 'Email is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.max' => 'Email must not exceed 255 characters.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function mobileMessages(): array
    {
        return [
            'phone.required' => 'Mobile number is required.',
            'phone.digits' => 'Mobile number must be exactly 10 digits.',
            'mobile.digits' => 'Mobile number must be exactly 10 digits.',
            'alternate_phone.required' => 'Alternate mobile number is required.',
            'alternate_phone.digits' => 'Alternate mobile number must be exactly 10 digits.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function contactMessages(): array
    {
        return array_merge(self::emailMessages(), self::mobileMessages());
    }
}
