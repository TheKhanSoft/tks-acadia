<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Office;
use App\Models\OfficeType;
use App\Models\Employee;

class OfficeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        // Allow all authenticated users for now
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @param int|null $officeId The ID of the office being updated, if any.
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(?int $officeId = null)
    {
        $officeId = $this->route('office') ? $this->route('office')->id : $officeId;

        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:255',
                Rule::unique('offices', 'name')->ignore($officeId)
            ],
            'code' => [
                'required',
                'string',
                'max:50',
                'alpha_dash',
                Rule::unique('offices', 'code')->ignore($officeId)
            ],
            'office_type_id' => [
                'required',
                'integer',
                Rule::exists(OfficeType::class, 'id') // Ensure the office type exists
            ],
            'description' => ['nullable', 'string', 'max:1000'],
            'head_id' => [
                'nullable',
                'integer',
                Rule::exists(Employee::class, 'id') // Ensure the employee exists
            ],
            'head_appointment_date' => [
                'nullable',
                'date',
                // Ensure appointment date is not before establishment year if both are provided
                Rule::when($this->filled('established_year'), [
                    'after_or_equal:' . $this->input('established_year') . '-01-01'
                ])
            ],
            'office_location' => ['nullable', 'string', 'max:255'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:50'], // Basic phone validation
            'established_year' => ['nullable', 'integer', 'digits:4', 'before_or_equal:' . date('Y')],
            'parent_office_id' => [
                'nullable',
                'integer',
                Rule::exists(Office::class, 'id'), // Ensure parent office exists
                // Prevent setting an office as its own parent
                Rule::when($officeId !== null, ['different:id']) // Check against the actual ID from the route/model
            ],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * Get custom validation messages.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => 'Every office needs a name (e.g., "Registrar Office", "Physics Department").',
            'name.min' => 'The office name needs at least 3 characters.',
            'name.max' => 'The office name is too long. Please keep it under 255 characters.',
            'name.unique' => 'Oops! An office with this name already exists in the system.',

            'code.required' => 'Please provide a unique code for this office (e.g., "REG", "PHY").',
            'code.max' => 'The office code is too long. Keep it under 50 characters.',
            'code.alpha_dash' => 'Office codes should only contain letters, numbers, dashes (-), and underscores (_).',
            'code.unique' => 'This office code is already taken. Please choose another one.',

            'office_type_id.required' => 'You must select the type of office (e.g., Department, Section).',
            'office_type_id.exists' => 'The selected office type doesn\'t seem to exist. Please choose a valid one.',

            'head_id.exists' => 'The selected employee for the head of office doesn\'t seem to exist.',

            'head_appointment_date.date' => 'Please enter a valid date for the head\'s appointment.',
            'head_appointment_date.after_or_equal' => 'The head\'s appointment date cannot be before the office was established.',

            'contact_email.email' => 'Please enter a valid email address for the office contact.',
            'contact_email.max' => 'The contact email is too long. Keep it under 255 characters.',

            'contact_phone.max' => 'The contact phone number is too long. Keep it under 50 characters.',

            'established_year.integer' => 'The established year must be a number.',
            'established_year.digits' => 'Please enter the established year as a 4-digit number (e.g., 2023).',
            'established_year.before_or_equal' => 'The established year cannot be in the future.',

            'parent_office_id.integer' => 'The parent office selection is invalid.',
            'parent_office_id.exists' => 'The selected parent office doesn\'t seem to exist.',
            'parent_office_id.different' => 'An office cannot be its own parent. Please select a different parent office.',

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
            'name' => 'office name',
            'code' => 'office code',
            'office_type_id' => 'office type',
            'head_id' => 'head of office',
            'head_appointment_date' => 'head appointment date',
            'office_location' => 'office location',
            'contact_email' => 'contact email',
            'contact_phone' => 'contact phone',
            'established_year' => 'established year',
            'parent_office_id' => 'parent office',
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
        $this->merge([
            'code' => strtoupper($this->code),
            // Set default for is_active only if it's not an update and not already present
            'is_active' => $this->route('office') ? $this->is_active : ($this->is_active ?? true),
        ]);
    }
}
