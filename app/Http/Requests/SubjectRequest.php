<?php

namespace App\Http\Requests;

use App\Models\Office;
use App\Models\Subject;
use App\Models\SubjectType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

/**
 * @method bool has(string|array $key)
 * @method mixed input(string $key = null, $default = null)
 * @method void merge(array $input)
 * @method \Illuminate\Routing\Route|object|string|null route(string $param = null, $default = null)
 */
class SubjectRequest extends FormRequest
{
    public ?int $subjectId = null;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // return Auth::check();
        return true; // Assuming all authenticated users can create or update subjects
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        if ($this->has('code')) {
            $this->merge([
                'code' => strtoupper($this->input('code')),
            ]);
        }
    }

    public function rules(): array
    {
        $subjectId = $this->subjectId ?? ($this->route('subject') ? $this->route('subject')->id : null);

        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:255',
                Rule::unique('subjects', 'name')
                    ->ignore($subjectId)
                    ->where('parent_department_id', $this->input('parent_department_id'))
                    ->where('code', $this->input('code')),
            ],
            'code' => [
                'required',
                'string',
                'max:50',
                'alpha_dash', // Allows alphanumeric characters, dashes, and underscores
                Rule::unique('subjects', 'code')
                    ->ignore($subjectId)
                    ->where('parent_department_id', $this->input('parent_department_id'))
                    ->where('name', $this->input('name')),
            ],
            'credit_hours' => [
                'required',
                'string',
                'max:10',
                // Allows single numbers or numbers separated by '+'
                'regex:/^(\d+(\s*\+\s*\d+)*)$/',
            ],
            'parent_department_id' => [
                'required',
                'integer',
                Rule::exists(Office::class, 'id'),
            ],
            'subject_type_id' => ['required', 'integer', Rule::exists(SubjectType::class, 'id')],
            'description' => ['nullable', 'string', 'max:65535'],
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
            'name.required' => 'The subject name is essential (e.g., "Introduction to Programming", "Calculus I").',
            'name.min' => 'The subject name should be at least 3 characters long.',
            'name.max' => 'The subject name is too long; please keep it under 255 characters.',
            'name.unique' => 'A subject with this name and code already exists within the selected department.',

            'code.required' => 'Please provide a unique code for this subject (e.g., "CS101", "MATH202").',
            'code.max' => 'The subject code is too long; keep it under 50 characters.',
            'code.alpha_dash' => 'Subject codes should only contain letters, numbers, dashes (-), and underscores (_).',
            'code.unique' => 'A subject with this code and name already exists within the selected department.',

            'credit_hours.required' => 'Credit hours are required for the subject.',
            'credit_hours.string' => 'Credit hours must be a valid string.',
            'credit_hours.max' => 'Credit hours input is too long (max 10 characters).',
            'credit_hours.regex' => 'Credit hours must be a number or in "3+1" format.',

            'parent_department_id.required' => 'You must select the department offering this subject.',
            'parent_department_id.exists' => 'The selected department doesn\'t seem to exist. Please choose a valid one.',

            'subject_type_id.required' => 'You must select a subject type.',
            'subject_type_id.exists' => 'The selected subject type is invalid.',
            'is_active.boolean' => 'The active status must be either true or false.',
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
            'name' => 'subject name',
            'code' => 'subject code',
            'credit_hours' => 'credit hours',
            'parent_department_id' => 'department',
            'description' => 'description',
            'subject_type_id' => 'subject type',
            'is_active' => 'active status',
        ];
    }
}
