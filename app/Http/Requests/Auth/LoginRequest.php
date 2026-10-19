<?php

namespace App\Http\Requests\Auth;

use App\Rules\PasswordInput;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->input('email'))) {
            $this->merge(['email' => Str::lower(trim($this->input('email')))]);
        }
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'max:255'],
            'password' => ['bail', 'required', 'string', new PasswordInput],
        ];
    }

    public function throttleKey(): string
    {
        return 'auth-login:'.hash('sha256', $this->ip().'|'.$this->string('email'));
    }

    public function authenticate(): void
    {
        $key = $this->throttleKey();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            abort(429, 'Bạn đã đăng nhập sai quá nhiều lần. Vui lòng thử lại sau một phút.');
        }

        if (! Auth::attempt($this->only('email', 'password'))) {
            RateLimiter::hit($key, 60);
            throw ValidationException::withMessages([
                'email' => 'Email hoặc mật khẩu chưa đúng.',
            ]);
        }

        if (! Auth::user()->is_active) {
            Auth::logout();
            $this->session()->invalidate();
            $this->session()->regenerateToken();
            RateLimiter::hit($key, 60);
            throw ValidationException::withMessages([
                'email' => 'Tài khoản đã bị khóa. Vui lòng liên hệ showroom.',
            ]);
        }

        RateLimiter::clear($key);
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không hợp lệ.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
        ];
    }
}
