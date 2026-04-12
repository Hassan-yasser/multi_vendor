<?php

namespace App\Http\Requests\Concerns;

use App\Helper\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;

trait FormatsApiValidationErrors
{
    /**
     * على مسارات api/* نُرجع نفس شكل ApiResponse بدل شكل Laravel الافتراضي.
     */
    protected function failedValidation(Validator $validator): void
    {
        if ($this->is('api/*') || $this->wantsJson()) {
            throw new HttpResponseException(
                ApiResponse::error(
                    message: __('The given data was invalid.'),
                    errors: ApiResponse::flattenValidationMessages($validator->errors()->toArray()),
                    status: 422,
                )
            );
        }

        throw (new ValidationException($validator))
            ->errorBag($this->errorBag)
            ->redirectTo($this->getRedirectUrl());
    }
}
