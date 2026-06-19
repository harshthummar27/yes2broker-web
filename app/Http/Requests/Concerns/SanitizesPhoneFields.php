<?php

declare(strict_types=1);

namespace App\Http\Requests\Concerns;

trait SanitizesPhoneFields
{
    protected function prepareForValidation(): void
    {
        foreach (['phone', 'mobile', 'alternate_phone'] as $field) {
            if (! $this->has($field)) {
                continue;
            }

            $value = preg_replace('/\D/', '', (string) $this->input($field));

            if (strlen($value) === 12 && str_starts_with($value, '91')) {
                $value = substr($value, 2);
            }

            $this->merge([$field => $value]);
        }
    }
}
