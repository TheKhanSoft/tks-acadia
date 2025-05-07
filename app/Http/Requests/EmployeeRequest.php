<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Employee;
use App\Models\EmployeeType;
use App\Models\EmployeeWorkStatus;
use App\Models\JobNature; // Added JobNature model

class EmployeeRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules($employee_id)
    {
        $employeeId = $this->route('employee') ? $this->route('employee')->id : $employee_id ?? null;

        return [
            'employee_id' => [
                'required',
                'string',
                'max:50', // Consistent max length like Office code
                'alpha_dash', // Similar constraint as Office code
                Rule::unique('employees', 'employee_id')->ignore($employeeId),
            ],
            'first_name' => ['required', 'string', 'min:2', 'max:255'],
            'last_name' => ['nullable', 'string', 'min:1', 'max:255'],
            'email' => [
                'required',
                'email:rfc,dns', // Stricter email validation like Office contact_email
                'max:255',
                Rule::unique('employees', 'email')->ignore($employeeId),
            ],
            'phone' => ['nullable', 'string', 'max:50'], // Consistent max length like Office contact_phone
            'gender' => ['required', Rule::in(['Male', 'Female', 'Other'])],
            'nic_no' => [
                'nullable',
                'string',
                'max:15', // Max length from schema
                // Example for PK NIC: 12345-1234567-1 - Keep regex or adjust as needed
                'regex:/^\d{5}-\d{7}-\d{1}$/',
                Rule::unique('employees', 'nic_no')->ignore($employeeId),
            ],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'employee_type_id' => [
                'required',
                'integer',
                Rule::exists(EmployeeType::class, 'id'), // Ensure the type exists
            ],
            'appointment_date' => ['nullable', 'date'],
            'termination_date' => [
                'nullable',
                'date',
                // Ensure termination date is not before appointment date if both are provided
                Rule::when($this->filled('appointment_date'), [
                    'after_or_equal:appointment_date'
                ])
            ],
            'postal_address' => ['nullable', 'string', 'max:500'], // Increased max length
            'permanent_address' => ['nullable', 'string', 'max:500'], // Increased max length
            'qualification' => ['nullable', 'string', 'max:255'],
            'specialization' => ['nullable', 'string', 'max:255'],
            'photo_path' => ['nullable', 'string', 'max:255'], // Consider adding image validation rules if handling uploads
            'bio' => ['nullable', 'string', 'max:2000'], // Increased max length
            'employee_work_status_id' => [
                'required',
                'integer',
                 Rule::exists(EmployeeWorkStatus::class, 'id'), // Ensure the status exists
             ],
             'job_nature_id' => [ // Added job_nature_id validation
                 'required',
                 'integer',
                 Rule::exists(JobNature::class, 'id'), // Ensure the job nature exists
             ],
             // 'is_active' => ['sometimes', 'boolean'], // Removed is_active validation
         ];
    }

    /**
     * Get custom validation messages.
     *
     * @return array
     */
    public function messages()
    {
        // Messages styled similarly to OfficeRequest
        return [
            'employee_id.required' => 'Every employee needs a unique ID (e.g., "EMP-001").',
            'employee_id.max' => 'The employee ID is too long. Keep it under 50 characters.',
            'employee_id.alpha_dash' => 'Employee IDs should only contain letters, numbers, dashes (-), and underscores (_).',
            'employee_id.unique' => 'Oops! An employee with this ID already exists.',

            'first_name.required' => 'Please provide the employee\'s first name.',
            'first_name.min' => 'The first name needs at least 2 characters.',
            'first_name.max' => 'The first name is too long. Please keep it under 255 characters.',

            'last_name.min' => 'The last name needs at least 1 character.',
            'last_name.max' => 'The last name is too long. Please keep it under 255 characters.',

            'email.required' => 'An email address is required for the employee.',
            'email.email' => 'Please enter a valid email address (e.g., user@example.com).',
            'email.max' => 'The email address is too long. Keep it under 255 characters.',
            'email.unique' => 'This email address is already registered to another employee.',

            'phone.max' => 'The phone number is too long. Keep it under 50 characters.',

            'gender.required' => 'You must select the employee\'s gender.',
            'gender.in' => 'The selected gender is not valid. Please choose Male, Female, or Other.',

            'nic_no.max' => 'The NIC number is too long. Keep it under 15 characters.',
            'nic_no.regex' => 'Please enter a valid NIC number format (e.g., 12345-1234567-1).',
            'nic_no.unique' => 'This NIC number is already registered to another employee.',

            'date_of_birth.date' => 'Please enter a valid date for the date of birth.',
            'date_of_birth.before' => 'The date of birth cannot be today or a future date.',

            'employee_type_id.required' => 'You must select the type of employee (e.g., Faculty, Staff).',
            'employee_type_id.exists' => 'The selected employee type doesn\'t seem to exist. Please choose a valid one.',

            'appointment_date.date' => 'Please enter a valid date for the appointment.',

            'termination_date.date' => 'Please enter a valid date for termination.',
            'termination_date.after_or_equal' => 'The termination date cannot be before the employee\'s appointment date.',

            'postal_address.max' => 'The postal address is too long. Keep it under 500 characters.',
            'permanent_address.max' => 'The permanent address is too long. Keep it under 500 characters.',

            'qualification.max' => 'The qualification description is too long. Keep it under 255 characters.',
            'specialization.max' => 'The specialization description is too long. Keep it under 255 characters.',
            'photo_path.max' => 'The photo path is too long. Keep it under 255 characters.',
            'bio.max' => 'The bio is too long. Keep it under 2000 characters.',

            'employee_work_status_id.required' => 'You must select the employee\'s current work status.',
            'employee_work_status_id.exists' => 'The selected work status doesn\'t seem to exist.',

            'job_nature_id.required' => 'You must select the nature of the job.', // Added message
            'job_nature_id.exists' => 'The selected job nature doesn\'t seem to exist.', // Added message

            // 'is_active.boolean' => 'The active status must be either true or false (on or off).', // Removed is_active message
         ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        // Attributes styled similarly to OfficeRequest
        return [
            'employee_id' => 'employee ID',
            'first_name' => 'first name',
            'last_name' => 'last name',
            'nic_no' => 'NIC number',
            'date_of_birth' => 'date of birth',
            'employee_type_id' => 'employee type',
            'appointment_date' => 'appointment date',
            'termination_date' => 'termination date',
            'postal_address' => 'postal address',
            'permanent_address' => 'permanent address',
            'qualification' => 'qualification',
            'specialization' => 'specialization',
            'photo_path' => 'photo path',
            'employee_work_status_id' => 'work status',
            'job_nature_id' => 'job nature', // Added attribute
            // 'is_active' => 'active status', // Removed is_active attribute
         ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        // Consistent with OfficeRequest
        $this->merge([
            // Example: Convert employee_id to uppercase if needed, similar to Office code
            // 'employee_id' => strtoupper($this->employee_id),

            // Removed default setting for is_active
        ]);
    }
}
