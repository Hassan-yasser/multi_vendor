<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\Concerns\FormatsApiValidationErrors;
use Illuminate\Foundation\Http\FormRequest;

class SendPasswordResetLinkRequest extends FormRequest
{
    use FormatsApiValidationErrors;

    public function authorize(): bool
    {
        return true;
    }

    /**
     * التحقق من البريد المرسل لطلب رابط إعادة التعيين.
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
        ];
    }
}
