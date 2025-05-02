<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PaymentMethodRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Implement your authorization logic here
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $paymentMethodId = $this->route('payment_method')?->id; // Assuming route model binding name is 'payment_method'

        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:255',
                Rule::unique('payment_methods', 'name')->ignore($paymentMethodId)
            ],
        ];
    }

    /**
     * Get custom validation messages.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'name.required' => "A payment method needs a name!",
            'name.min' => "The name needs at least 3 characters.",
            'name.max' => "The name is too long (max 255 characters).",
            'name.unique' => "This payment method name already exists.",
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes(): array
    {
        return [
            'name' => 'payment method name',
        ];
    }
}
