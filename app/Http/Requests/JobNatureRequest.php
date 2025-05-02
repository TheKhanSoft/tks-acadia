<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class JobNatureRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Implement your authorization logic here
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules($jobNatureId)
    {
        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:255',
                Rule::unique('job_natures', 'name')->ignore($jobNatureId)
            ],

            'code' => [
                'required', 
                'string', 
                'min:2',
                'max:6', 
                'alpha_dash', 
                Rule::unique('office_types', 'code')->ignore($jobNatureId) 
            ],

            'description' => ['nullable', 'string', 'max:1000'], 
            
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
            'name.required' => "Hold On! Each job nature must have a unique name!",
            'name.min' => "The name needs at least 3 characters.",
            'name.max' => "Whoa there! The name is too long. Keep it under 255 characters.",
            'name.unique' => "Oops! Looks like the name already exists. Please choose another unique name.",

            'code.required' => "Every job nature needs a unique code. Write a code of 2 to 6 characters.",
            'code.string' => "The code must be a text string and at least 2 and at most 6 characters.",
            'code.min' => "Oops, The code is too short. Keep it at least 2 characters.",
            'code.max' => "The code is too long. Keep it under 6 characters.",
            'code.unique' => "That is not good! This code is already in use. You should choose another.",
            'code.alpha_dash' => "Codes can only contain letters, numbers, dashes, and underscores.",
            
            'description.max' => "Hey! The description is too long. Keep it under 1000 characters.",
            
            'is_active.boolean' => "Active status must be either true or false."
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
            'name' => 'job nature name',
            'code' => 'job nature code',
            'is_active' => 'active status',
            'description' => 'description',
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        // Automatically set 'is_active' to true if not provided during creation
        // Convert code to uppercase for consistency
        $this->merge([
            'code' => strtoupper($this->code),
            // Set default for is_active only if it's not an update (i.e., job_nature route param is not set)
            // and if is_active is not already present in the request
            'is_active' => $this->route('job_nature') ? $this->is_active : ($this->is_active ?? true),
        ]);
    }
}
