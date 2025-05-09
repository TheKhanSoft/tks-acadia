<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\StudentStatus;

class StudentStatusRequest extends FormRequest
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
        $studentStatusId = $this->route('student_status') ? $this->route('student_status')->id : null;

        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:255',
                Rule::unique('student_statuses', 'name')->ignore($studentStatusId),
            ],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active_status' => ['sometimes', 'boolean'], // Matches the field in StudentStatus model
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
            'name.required' => 'Every student status needs a name (e.g., "Active", "Graduated").',
            'name.min' => 'The student status name needs at least 3 characters.',
            'name.max' => 'The student status name is too long. Please keep it under 255 characters.',
            'name.unique' => 'Oops! A student status with this name already exists.',

            'description.max' => 'The description is too long. Keep it under 1000 characters.',

            'is_active_status.boolean' => 'The active status must be either true or false (on or off).',
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
            'name' => 'student status name',
            'description' => 'description',
            'is_active_status' => 'active status',
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('is_active_status')) {
            $this->merge(['is_active_status' => filter_var($this->is_active_status, FILTER_VALIDATE_BOOLEAN)]);
        } elseif (!$this->route('student_status')) { // Only default for creation
            $this->merge(['is_active_status' => true]);
        }
    }
}
