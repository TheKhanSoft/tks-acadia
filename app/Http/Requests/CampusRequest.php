<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CampusRequest extends FormRequest
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
    public function rules(): array
    {
        $campusId = $this->route('campus')?->id;
        
        return [
            'name' => [
                'required', 
                'string',
                'min:3', 
                'max:255',
                Rule::unique('campuses', 'email')->ignore($campusId)
            ],
            'code' => [
                'required', 
                'string', 
                'max:20', 
                'alpha_dash',
                Rule::unique('campuses', 'code')->ignore($campusId)
            ],
            'location' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'address' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => [
                'nullable', 
                'email', 
                Rule::unique('campuses', 'email')->ignore($campusId)
            ],
            'website' => ['nullable', 'url', 'max:255'],
            'founded_year' => ['nullable', 'integer', 'min:1800', 'max:' . date('Y')],
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
            'name.required' => "A campus without a name? That's like a book without a title! 📚",
            'name.min' => "Your campus name needs a bit more character — at least 3, to be exact!",
            'name.max' => "Whoa there! Your campus name is too long. Keep it under 255 characters please.",
            'name.unique' => "Oops! Looks like 'Starlight Academy' already exists. Please choose another unique name.",
            
            'code.required' => "Every great campus needs a unique code. Don't forget yours! 🏫",
            'code.string' => "The campus code must be a text string.",
            'code.max' => "Your code is too long. Keep it under 20 characters.",
            'code.unique' => "That code is already taken. Your campus deserves its own unique identity!",
            'code.alpha_dash' => "Campus codes can only contain letters, numbers, dashes, and underscores — no spaces or special characters allowed!",
            
            'location.max' => "Your location description is too detailed. Keep it under 255 characters.",
            
            'address.max' => "Your address is too long. Please shorten it to under 255 characters.",
            
            'phone.max' => "That phone number has too many digits. Keep it under 20 characters.",
            
            'email.email' => "That doesn't look like a valid email. We need a proper way to reach your campus! ✉️",
            'email.unique' => "This email is already registered. Does another campus have the same contact?",
            
            'website.url' => "Your website address seems a bit off. Don't forget the 'http://' or 'https://'!",
            'website.max' => "Your website URL is too long. Keep it under 255 characters.",
            
            'founded_year.integer' => "Founded year should be a number. No roman numerals here!",
            'founded_year.min' => "Unless your campus is older than Harvard (founded 1636), that year seems a bit too early!",
            'founded_year.max' => "Unless you're planning for the future, the founding year can't be after this year! 🔮",
            
            'is_active.boolean' => "Active status must be true or false. No maybes allowed here!"
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
            'name' => 'campus name',
            'code' => 'campus code',
            'is_active' => 'active status',
            'founded_year' => 'founding year',
            'location' => 'campus location',
            'description' => 'campus description',
            'address' => 'campus address',
            'phone' => 'contact phone',
            'email' => 'contact email',
            'website' => 'campus website',
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
            'is_active' => $this->is_active ?? true,
        ]);
    }

}