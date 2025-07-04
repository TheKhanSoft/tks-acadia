<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\OfficeType;

class OfficeTypeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {

        return true; 
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @param int|null $officeTypeId The ID of the office type being updated, if any.
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(?int $officeTypeId = null)
    {
        return [
            'name' => [
                'required', 
                'string',
                'min:3', 
                'max:255',
                Rule::unique('office_types', 'name')->ignore($officeTypeId) 
            ],
            'code' => [
                'required', 
                'string', 
                'max:50', 
                'alpha_dash', 
                Rule::unique('office_types', 'code')->ignore($officeTypeId) 
            ],
            'description' => ['nullable', 'string', 'max:1000'], 
            'is_active' => ['sometimes', 'boolean'], 
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
            'name.required' => "An office type needs a name, like 'Department' or 'Section'.",
            'name.min' => "The name needs at least 3 characters.",
            'name.max' => "The name is too long. Keep it under 255 characters.",
            'name.unique' => "Oops! An office type with this name already exists.",
            
            'code.required' => "Every office type needs a unique code.",
            'code.string' => "The code must be a text string.",
            'code.max' => "The code is too long. Keep it under 50 characters.",
            'code.unique' => "This code is already in use. Please choose another.",
            'code.alpha_dash' => "Codes can only contain letters, numbers, dashes, and underscores.",
            
            'description.max' => "The description is too long. Keep it under 1000 characters.",
            
            'is_active.boolean' => "Active status must be either true or false."
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes(): array
    {
        // Friendly names for attributes in error messages
        return [
            'name' => 'office type name',
            'code' => 'office type code',
            'is_active' => 'active status',
            'description' => 'description',
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        // Automatically set 'is_active' to true if not provided during creation
        // Convert code to uppercase for consistency
        $this->merge([
            'code' => strtoupper($this->code),
            // Set default for is_active only if it's not an update (i.e., office_type route param is not set)
            // and if is_active is not already present in the request
            'is_active' => $this->route('office_type') ? $this->is_active : ($this->is_active ?? true),
        ]);
    }
}
