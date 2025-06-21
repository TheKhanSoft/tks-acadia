<?php

namespace App\Http\Requests;

use App\Models\AcademicSession;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Student;
use App\Models\DepartmentProgram;
use App\Models\Session;
use App\Models\EnrollmentStatus;
use App\Models\StudentProgramEnrollment;

class StudentProgramEnrollmentRequest extends FormRequest
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
        $enrollmentId = $this->route('student_program_enrollment') ? $this->route('student_program_enrollment')->id : null;

        return [
            'student_id' => [
                'required',
                'integer',
                Rule::exists(Student::class, 'id'),
            ],
            'department_program_id' => [
                'required',
                'integer',
                Rule::exists(DepartmentProgram::class, 'id'),
            ],
            'academic_session_id' => [
                'required',
                'integer',
                Rule::exists(AcademicSession::class, 'id'),
            ],
            'enrollment_date' => ['required', 'date'],
            'expected_completion_date' => [
                'nullable',
                'date',
                Rule::when($this->filled('enrollment_date'), [
                    'after_or_equal:enrollment_date'
                ])
            ],
            'actual_completion_date' => [
                'nullable',
                'date',
                Rule::when($this->filled('enrollment_date'), [
                    'after_or_equal:enrollment_date'
                ]),
                 Rule::when($this->filled('expected_completion_date'), [
                    'after_or_equal:expected_completion_date'
                ])
            ],
            'grades' => ['nullable', 'numeric', 'between:0,100'],
            'remarks' => ['nullable', 'string', 'max:1000'],
            'enrollment_status_id' => [
                'required',
                'integer',
                Rule::exists(EnrollmentStatus::class, 'id'),
            ],
            // Unique constraint across student, department_program, and session
            Rule::unique('student_program_enrollments')
                ->where('student_id', $this->input('student_id'))
                ->where('department_program_id', $this->input('department_program_id'))
                ->where('academic_session_id', $this->input('academic_session_id'))
                ->ignore($enrollmentId),
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
            'student_id.required' => 'A student must be selected for the enrollment.',
            'student_id.exists' => 'The selected student does not exist.',

            'department_program_id.required' => 'A program must be selected for the enrollment.',
            'department_program_id.exists' => 'The selected program does not exist.',

            'academic_session_id.required' => ' a session must be selected for the enrollment.',
            'academic_session_id.exists' => 'The selected session does not exist.',

            'enrollment_date.required' => 'Enrollment date is required.',
            'enrollment_date.date' => 'Enrollment date must be a valid date.',

            'expected_completion_date.after_or_equal' => 'Expected completion date cannot be before the enrollment date.',
            'actual_completion_date.after_or_equal' => 'Actual completion date cannot be before the enrollment date or expected completion date.',

            'grades.numeric' => 'Grades must be a number.',
            'grades.between' => 'Grades must be between 0 and 100.',

            'remarks.max' => 'Remarks cannot exceed 1000 characters.',

            'enrollment_status_id.required' => 'An enrollment status must be selected.',
            'enrollment_status_id.exists' => 'The selected enrollment status does not exist.',

            'unique' => 'This student is already enrolled in this program for this session.',
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
            'student_id' => 'student',
            'department_program_id' => 'program',
            'academic_session_id' => 'session',
            'enrollment_date' => 'enrollment date',
            'expected_completion_date' => 'expected completion date',
            'actual_completion_date' => 'actual completion date',
            'grades' => 'grades',
            'remarks' => 'remarks',
            'enrollment_status_id' => 'enrollment status',
        ];
    }
}
