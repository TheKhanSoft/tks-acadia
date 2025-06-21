<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubjectTypeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(?int $subjectTypeId = null): array
    {
        // If no ID is passed, try to resolve it from the request (for controller/DI usage)
        if ($subjectTypeId === null) {
            $subjectTypeId = $this->subject_type?->id ?? $this->route('subject_type') ?? $this->route('id') ?? $this->input('editing_subject_type_id');
        }

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('subject_types', 'name')->ignore($subjectTypeId),
            ],
            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'is_active' => [
                'present',
                'boolean',
            ],
        ];
    }

    /**
     * Get custom friendly messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Please provide a name for the subject type. It helps in identifying it.',
            'name.unique' => 'Oops! It looks like this subject type name is already in use. Please choose another.',
            'name.max' => 'The subject type name is a bit long. Could you keep it under :max characters?',
            
            'description.max' => 'The description is quite extensive. Please try to keep it concise, under :max characters.',
            
            'is_active.boolean' => 'Please specify if this subject type is active or inactive using a true/false value.',
            'is_active.present' => 'We need to know the active status for this subject type. Please ensure it is provided.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if (!$this->has('is_active')) {
            $this->merge([
                'is_active' => false,
            ]);
        } else {
            $this->merge([
                'is_active' => filter_var($this->is_active, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false,
            ]);
        }
    }
}
