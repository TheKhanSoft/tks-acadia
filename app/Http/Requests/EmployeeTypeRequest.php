<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\EmployeeType; // Use EmployeeType model

class EmployeeTypeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Allow all authenticated users for now
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Get the ID from the route parameter if available (for updates)
        $employeeTypeId = $this->route('employee_type') ? $this->route('employee_type')->id : null;

        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:255',
                // Ensure name is unique, ignoring the current record during updates
                Rule::unique('employee_types', 'name')->ignore($employeeTypeId),
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
            'name.required' => 'Every employee type needs a name (e.g., "Faculty", "Administrative Staff").',
            'name.min' => 'The employee type name needs at least 3 characters.',
            'name.max' => 'The employee type name is too long. Please keep it under 255 characters.',
            'name.unique' => 'Oops! An employee type with this name already exists.',

            'description.max' => 'The description is too long. Keep it under 1000 characters.',

            'is_active.boolean' => 'The active status must be either true or false (on or off).',
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
            'name' => 'employee type name',
            'description' => 'description',
            'is_active' => 'active status',
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        // Set default for is_active only if it's not an update and not already present
        if (!$this->route('employee_type') && !$this->has('is_active')) {
            $this->merge(['is_active' => true]);
        } elseif ($this->has('is_active')) {
             // Ensure boolean value if provided
             $this->merge(['is_active' => filter_var($this->is_active, FILTER_VALIDATE_BOOLEAN)]);
        }
    }
}
