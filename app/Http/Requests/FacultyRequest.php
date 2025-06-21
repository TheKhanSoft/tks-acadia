<?php

namespace App\Http\Requests;

use App\Models\Campus;
use App\Models\Employee;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Faculty;

class FacultyRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @param int|null $facultyId The ID of the faculty being updated, if any.
     * @return array&lt;string, \Illuminate\Contracts\Validation\ValidationRule|array&lt;mixed&gt;|string&gt;
     */
    public function rules(?int $facultyId = null): array
    {
        $facultyId = $this->route('faculty') ? $this->route('faculty')->id : $facultyId;

        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:255',
                Rule::unique('faculties', 'name')->ignore($facultyId),
            ],
            'code' => [
                'required',
                'string',
                'max:50',
                'alpha_dash', // Allows letters, numbers, dashes, and underscores
                Rule::unique('faculties', 'code')->ignore($facultyId),
            ],
            'description' => ['nullable', 'string', 'max:1000'],
            'campus_id' => [
                'nullable', // A faculty might not be directly under a campus in some structures
                'integer',
                Rule::exists(Campus::class, 'id'),
            ],
            'head_id' => [
                'nullable',
                'integer',
                Rule::exists(Employee::class, 'id'),
            ],
            'established_year' => [
                'nullable',
                'integer',
                'digits:4',
                'before_or_equal:' . date('Y'),
            ],
            'head_appointment_date' => [
                'nullable',
                'date',
                Rule::when($this->filled('established_year') && $this->input('established_year') !== null, [
                    'after_or_equal:' . $this->input('established_year') . '-01-01'
                ])
            ],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * Get custom validation messages for validator errors.
     *
     * @return array;
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Every Faculty/College needs a name (e.g., "Faculty of Engineering", "College of Sciences").',
            'name.min' => 'The Faculty/College name should be at least 3 characters long.',
            'name.max' => 'The Faculty/College name is a bit too long. Please keep it under 255 characters.',
            'name.unique' => 'Looks like a Faculty/College with this name already exists. Try a different one!',

            'code.required' => 'Please assign a unique code to this Faculty/College (e.g., "FOE", "COS").',
            'code.max' => 'The Faculty/College code is too long. Max 50 characters, please.',
            'code.alpha_dash' => 'Faculty/College codes can only have letters, numbers, dashes (-), and underscores (_).',
            'code.unique' => 'This Faculty/College code is already in use. Please pick another!',

            'description.max' => 'The description is a bit lengthy. Try to keep it under 1000 characters.',

            'campus_id.exists' => 'The selected campus doesn\'t seem to be in our records. Please choose a valid one.',
            'campus_id.integer' => 'The campus selection is invalid.',

            'head_id.exists' => 'The employee selected as Head doesn\'t seem to exist. Please pick a valid employee.',
            'head_id.integer' => 'The Head selection is invalid.',

            'established_year.integer' => 'The established year should be a number (e.g., 2005).',
            'established_year.digits' => 'Please use a 4-digit format for the established year (e.g., 2005).',
            'established_year.before_or_equal' => 'The established year can\'t be in the future. Let\'s stick to the past or present!',

            'head_appointment_date.date' => 'Please enter a valid date for the Head\'s appointment.',
            'head_appointment_date.after_or_equal' => 'The Head\'s appointment date cannot be before the Faculty/College was established.',

            'contact_phone.max' => 'The contact phone number is too long. Please keep it under 50 characters.',
            'contact_email.email' => 'Please provide a valid email address for contact.',
            'contact_email.max' => 'The contact email is too long. Max 255 characters, please.',

            'is_active.boolean' => 'The active status should be either on or off (true or false).',
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
            'name' => 'Faculty/College name',
            'code' => 'Faculty/College code',
            'description' => 'description',
            'campus_id' => 'campus',
            'head_id' => 'Head of Faculty/College',
            'established_year' => 'established year',
            'head_appointment_date' => 'Head\'s appointment date',
            'contact_phone' => 'contact phone',
            'contact_email' => 'contact email',
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
        if ($this->has('code')) {
            $this->merge([
                'code' => strtoupper($this->code),
            ]);
        }

        // Set default for is_active only if it's not an update and not already present
        // For a new faculty, if 'is_active' is not sent, default it to true.
        // If it's an update (this->route('faculty') is not null), then 'is_active' will be what's submitted or its current value.
        if (!$this->route('faculty') && !$this->has('is_active')) {
            $this->merge(['is_active' => true]);
        } elseif ($this->has('is_active')) {
            $this->merge(['is_active' => filter_var($this->is_active, FILTER_VALIDATE_BOOLEAN)]);
        }
    }
}
