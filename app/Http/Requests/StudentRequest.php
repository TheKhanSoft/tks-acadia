<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Student;
use App\Models\StudentStatus;
use App\Models\City; // Import the City model
use App\Enums\Gender; // Import the Gender enum

class StudentRequest extends FormRequest
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
        $studentId = $this->route('student') ? $this->route('student')->id : null;

        return [
            'student_id' => [
                'required',
                'string',
                'max:50',
                'alpha_dash',
                Rule::unique('students', 'student_id')->ignore($studentId),
            ],
            'first_name' => ['required', 'string', 'min:2', 'max:255'],
            'last_name' => ['nullable', 'string', 'min:1', 'max:255'],
            'email' => [
                'required',
                'email:rfc,dns',
                'max:255',
                Rule::unique('students', 'email')->ignore($studentId),
            ],
            'phone' => ['nullable', 'string', 'max:50'],
            'phone_alternative' => ['nullable', 'string', 'max:50'], // Added validation for phone_alternative
            'gender' => ['required', Rule::in(array_column(Gender::cases(), 'value'))], // Use Gender enum values
            'nic_no' => [
                'nullable',
                'string',
                'max:15',
                'regex:/^\d{5}-\d{7}-\d{1}$/', // PK NIC format
                Rule::unique('students', 'nic_no')->ignore($studentId)->whereNull('deleted_at'), // Unique among non-deleted
            ],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'postal_address' => ['nullable', 'string', 'max:500'],
            'permanent_address' => ['nullable', 'string', 'max:500'],
            'city_id' => [ // Added validation for city_id
                'nullable',
                'integer',
                Rule::exists(City::class, 'id'), // Ensure the city exists
            ],
            'photo_path' => ['nullable', 'string', 'max:255'], // Consider image validation if handling uploads
            'bio' => ['nullable', 'string', 'max:2000'],
            'student_status_id' => [
                'required',
                'integer',
                Rule::exists(StudentStatus::class, 'id'), // Ensure the status exists
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
            'student_id.required' => 'Every student needs a unique ID (e.g., "S2023-001").',
            'student_id.max' => 'The student ID is too long. Keep it under 50 characters.',
            'student_id.alpha_dash' => 'Student IDs should only contain letters, numbers, dashes (-), and underscores (_).',
            'student_id.unique' => 'Oops! A student with this ID already exists.',

            'first_name.required' => 'Please provide the student\'s first name.',
            'first_name.min' => 'The first name needs at least 2 characters.',
            'first_name.max' => 'The first name is too long. Please keep it under 255 characters.',

            'email.required' => 'An email address is required for the student.',
            'email.email' => 'Please enter a valid email address (e.g., user@example.com).',
            'email.unique' => 'This email address is already registered to another student.',

            'gender.required' => 'You must select the student\'s gender.',
            'gender.in' => 'The selected gender is not valid. Please choose from the allowed options.', // Updated message

            'nic_no.regex' => 'Please enter a valid NIC number format (e.g., 12345-1234567-1).',
            'nic_no.unique' => 'This NIC number is already registered to another student.',

            'date_of_birth.before' => 'The date of birth cannot be today or a future date.',

            'city_id.exists' => 'The selected city doesn\'t seem to exist.', // Added message for city_id

            'student_status_id.required' => 'You must select the student\'s current status.',
            'student_status_id.exists' => 'The selected student status doesn\'t seem to exist.',
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
            'student_id' => 'student ID',
            'first_name' => 'first name',
            'last_name' => 'last name',
            'phone_alternative' => 'alternative phone', // Added attribute for phone_alternative
            'nic_no' => 'NIC number',
            'date_of_birth' => 'date of birth',
            'postal_address' => 'postal address',
            'permanent_address' => 'permanent address',
            'city_id' => 'city', // Added attribute for city_id
            'photo_path' => 'photo path',
            'student_status_id' => 'student status',
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        // Example: Convert student_id to uppercase if needed
        if ($this->student_id) {
            $this->merge([
                'student_id' => strtoupper($this->student_id),
            ]);
        }
    }
}
