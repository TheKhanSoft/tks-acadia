<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Session;

class SessionRequest extends FormRequest
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
        $sessionId = $this->route('session') ? $this->route('session')->id : null;

        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:255',
                Rule::unique('sessions', 'name')->ignore($sessionId),
            ],
            'start_date' => ['nullable', 'date'],
            'end_date' => [
                'nullable',
                'date',
                Rule::when($this->filled('start_date'), [
                    'after_or_equal:start_date'
                ])
            ],
            'type' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
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
            'name.required' => 'Every session needs a name (e.g., "Fall 2023").',
            'name.min' => 'The session name needs at least 3 characters.',
            'name.max' => 'The session name is too long. Please keep it under 255 characters.',
            'name.unique' => 'Oops! A session with this name already exists.',

            'end_date.after_or_equal' => 'The end date cannot be before the start date.',

            'description.max' => 'The description is too long. Keep it under 1000 characters.',
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
            'name' => 'session name',
            'start_date' => 'start date',
            'end_date' => 'end date',
            'type' => 'type',
            'description' => 'description',
        ];
    }
}
