<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\Concerns\FormatsApiValidationErrors;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class ResetPasswordRequest extends FormRequest
{
    use FormatsApiValidationErrors;

    public function authorize(): bool
    {
        return true;
    }

    /**
     * التحقق من بيانات نموذج كلمة المرور الجديدة بعد فتح الرابط من البريد.
     *
     * ملاحظة: حقل password_confirmation مُعرَّف صراحةً ليظهر في validated()
     * (قاعدة confirmed وحدها لا تُضيفه أحياناً لمصفوفة البيانات المُصدَّقة).
     */
    public function rules(): array
    {
        return [
            'token' => ['required', 'string'],
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'password_confirmation' => ['required', 'same:password'],
        ];
    }
}
