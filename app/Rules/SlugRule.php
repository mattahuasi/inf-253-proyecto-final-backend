<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SlugRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $msg = 'El :attribute debe ser un slug válido, sin guiones al inicio o al final y solo contener letras y números.';
        if (empty($value)) {
            $fail($msg);
            return;
        }
        if (!preg_match('/^(?!-)[a-z0-9]+(?:-[a-z0-9]+)*(?<!-)$/', $value)) {
            $fail($msg);
        }
    }
}
