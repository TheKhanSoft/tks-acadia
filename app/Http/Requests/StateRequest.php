<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Assuming authorization logic is handled elsewhere, or allow all for now
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(?int $id = null): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('states')->where('country_id', $this->input('country_id'))->ignore($id),
            ],
            'country_id' => [
                'required',
                'exists:countries,id',
            ],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Every state needs a name! Please provide one.',
            'name.unique' => 'This state name already exists in the selected country. Please use a different name.',
            'name.max' => 'The state name is a bit long. Please keep it under :max characters.',
            'country_id.required' => 'Please select the country this state belongs to.',
            'country_id.exists' => 'The selected country is not valid.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'State Name',
            'country_id' => 'Country',
        ];
    }
}
