<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\Concerns\FormatsApiValidationErrors;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    use FormatsApiValidationErrors;

    public function authorize(): bool
    {
        return true;
    }

    /**
     * قواعد التحقق من بيانات إنشاء حساب جديد.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            'password' => ['required', 'confirmed', Password::defaults()],
            'password_confirmation' => ['required', 'same:password'],
        ];
    }
}
