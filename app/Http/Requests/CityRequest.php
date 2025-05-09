<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CityRequest extends FormRequest
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
                Rule::unique('cities')->where('state_id', $this->input('state_id'))->ignore($id),
            ],
            'state_id' => [
                'required',
                'exists:states,id',
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
            'name.required' => 'Please give this city a name!',
            'name.unique' => 'A city with this name already exists in the selected state. Try a different name.',
            'name.max' => 'The city name is a bit long. Please keep it under :max characters.',
            'state_id.required' => 'Which state is this city in? Please select one.',
            'state_id.exists' => 'The selected state is not valid.',
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
            'name' => 'City Name',
            'state_id' => 'State',
        ];
    }
}
