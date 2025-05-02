<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\EmployeeWorkStatus;

class EmployeeWorkStatusRequest extends FormRequest
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
    public function rules($employeeWorkStatusId)
    {
        // Assuming the route parameter will be updated to 'employee_work_status'
        $empWorkStatusId = $this->route('employee_work_status') ? $this->route('employee_work_status')->id : $employeeWorkStatusId;

        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:255',
                Rule::unique('employee_work_statuses', 'name')->ignore($empWorkStatusId),
            ],
            
            'code' => [
                'required', 
                'string', 
                'min:2',
                'max:6', 
                'alpha_dash', 
                Rule::unique('office_types', 'code')->ignore($empWorkStatusId) 
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
            'name.required' => 'Every employee work status needs a name (e.g., "Permanent", "Contractual").',
            'name.min' => 'The employee work status name needs at least 3 characters.',
            'name.max' => 'The employee work status name is too long. Please keep it under 255 characters.',
            'name.unique' => 'Oops! An employee work status with this name already exists.',

            'code.required' => "A employee work status must have a unique code. Provide a code of 2 to 6 characters.",
            'code.string' => "The code must be a text string and at least 2 and at most 6 characters.",
            'code.min' => "Oops, The code is too short. Keep it at least 2 characters.",
            'code.max' => "The code is too long. Keep it under 6 characters.",
            'code.unique' => "That is not good! This code is already in use. You should choose another.",
            'code.alpha_dash' => "A code can only contain letters, numbers, dashes, and underscores.",

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
            'name' => 'employee work status name',
            'code' => 'employee work status code',
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
        // Assuming the route parameter will be updated to 'employee_work_status'
        if (!$this->route('employee_work_status') && !$this->has('is_active')) {
            $this->merge(['is_active' => true]);
        } elseif ($this->has('is_active')) {
             $this->merge(['is_active' => filter_var($this->is_active, FILTER_VALIDATE_BOOLEAN)]);
        }

        $this->merge([
            'code' => strtoupper($this->code),
            // Set default for is_active only if it's not an update (i.e., employee_work_status route param is not set)
            // and if is_active is not already present in the request
            'is_active' => $this->route('employee_work_status') ? $this->is_active : ($this->is_active ?? true),
        ]);
    }
}
