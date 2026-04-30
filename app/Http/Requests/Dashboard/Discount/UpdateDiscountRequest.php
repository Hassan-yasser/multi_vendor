<?php

namespace App\Http\Requests\Dashboard\Discount;

use App\Models\Discount;
use Illuminate\Foundation\Http\FormRequest;

class UpdateDiscountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->store_id !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'discount_price' => ['required', 'numeric', 'min:0'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            /** @var Discount $discount */
            $discount = $this->route('discount');
            $discountPrice = (float) $this->input('discount_price', 0);
            $originalPrice = (float) $discount->product->price;

            if ($originalPrice > 0) {
                $discountPercentage = (($originalPrice - $discountPrice) / $originalPrice) * 100;

                if ($discountPercentage > 60) {
                    $validator->errors()->add('discount_price', 'Discount cannot exceed 60% of the original price.');
                }
            }
        });
    }
}
