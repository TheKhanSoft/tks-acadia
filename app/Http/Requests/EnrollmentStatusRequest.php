<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\EnrollmentStatus;

class EnrollmentStatusRequest extends FormRequest
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
        $enrollmentStatusId = $this->route('enrollment_status') ? $this->route('enrollment_status')->id : null;

        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:255',
                Rule::unique('enrollment_statuses', 'name')->ignore($enrollmentStatusId),
            ],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['sometimes', 'boolean'], // Matches the field in EnrollmentStatus model
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
            'name.required' => 'Every enrollment status needs a name (e.g., "Enrolled", "Completed").',
            'name.min' => 'The enrollment status name needs at least 3 characters.',
            'name.max' => 'The enrollment status name is too long. Please keep it under 255 characters.',
            'name.unique' => 'Oops! An enrollment status with this name already exists.',

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
            'name' => 'enrollment status name',
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
        if ($this->has('is_active')) {
            $this->merge(['is_active' => filter_var($this->is_active, FILTER_VALIDATE_BOOLEAN)]);
        } elseif (!$this->route('enrollment_status')) { // Only default for creation
            $this->merge(['is_active' => false]); // Default to false for new enrollments
        }
    }
}
