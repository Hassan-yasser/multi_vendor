<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\Concerns\FormatsApiValidationErrors;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    use FormatsApiValidationErrors;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    public function remember(): bool
    {
        return $this->boolean('remember');
    }
}
