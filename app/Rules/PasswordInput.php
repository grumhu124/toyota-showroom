<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PasswordInput implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // PHP bcrypt cannot safely represent NUL or more than 72 bytes.
        if (is_string($value) && (strlen($value) > 72 || str_contains($value, "\0"))) {
            $fail('Mật khẩu tối đa 72 byte và không được chứa ký tự rỗng.');
        }
    }
}
