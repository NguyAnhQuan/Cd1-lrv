<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingCheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'tour_id' => ['required', 'integer', 'exists:tours,id'],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:20'],
            'customer_email' => ['required', 'string', 'email', 'max:255'],
            'customer_address' => ['nullable', 'string', 'max:500'],
            'payment_method' => ['required', 'string', 'in:bank_transfer,card,e_wallet'],
            'number_of_people' => ['nullable', 'integer', 'min:1', 'max:99'],
            'travel_date' => ['nullable', 'date', 'after_or_equal:today'],
            'coupon_code' => ['nullable', 'string', 'max:100'],
        ];
    }
}
