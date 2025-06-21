<?php

namespace App\Http\Requests;

use App\Models\Office;
use App\Models\Subject;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class DepartmentSubjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $departmentSubjectId = $this->route('department_subject') ? $this->route('department_subject')->id : null;

        return [
            'department_id' => [
                'required',
                'integer',
                Rule::exists(Office::class, 'id')->where(function ($query) {
                    // Optionally, ensure the office is a department type
                    // $query->whereIn('office_type_id', [config('constants.office_types.DEPARTMENT_ID')]);
                }),
                Rule::unique('department_subjects')->ignore($departmentSubjectId)
                    ->where('subject_id', $this->input('subject_id')),
            ],
            'subject_id' => [
                'required',
                'integer',
                Rule::exists(Subject::class, 'id'),
                Rule::unique('department_subjects')->ignore($departmentSubjectId)
                    ->where('department_id', $this->input('department_id')),
            ],
            'is_active' => ['sometimes', 'boolean'],
            // Add other pivot-specific fields here if your department_subjects table has more columns
            // e.g., 'offered_since' => ['nullable', 'date'],
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
            'department_id.required' => 'A department must be selected.',
            'department_id.exists' => 'The selected department does not exist.',
            'department_id.unique' => 'This subject is already associated with the selected department.',

            'subject_id.required' => 'A subject must be selected.',
            'subject_id.exists' => 'The selected subject does not exist.',
            'subject_id.unique' => 'This subject is already associated with the selected department.',

            'is_active.boolean' => 'The active status for this association must be true or false.',
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
            'department_id' => 'department',
            'subject_id' => 'subject',
            'is_active' => 'active status for association',
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        // For a new association, if is_active is not sent, default to true.
        if (!$this->route('department_subject') && !$this->has('is_active')) {
            $this->merge(['is_active' => true]);
        } elseif ($this->has('is_active')) {
            $this->merge([
                'is_active' => filter_var($this->is_active, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
            ]);
        }
    }
}
