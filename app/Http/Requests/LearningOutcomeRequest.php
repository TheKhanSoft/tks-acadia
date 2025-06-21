<?php

namespace App\Http\Requests;

use App\Models\Subject;
use App\Models\Program;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class LearningOutcomeRequest extends FormRequest
{
    // Define the allowed outcomeable types here
    // Using fully qualified class names for robustness in validation
    protected $allowedOutcomeableTypes = [
        Subject::class,
        Program::class,
    ];

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
        $outcomeableType = $this->input('outcomeable_type');
        $tableName = null;

        // Resolve table name from outcomeable_type if it's a valid FQCN
        if (in_array($outcomeableType, $this->allowedOutcomeableTypes) && class_exists($outcomeableType)) {
            $tableName = (new $outcomeableType())->getTable();
        }

        return [
            'outcomeable_type' => [
                'required',
                'string',
                Rule::in($this->allowedOutcomeableTypes)
            ],
            'outcomeable_id' => [
                'required',
                'integer',
                // Ensure the ID exists in the table corresponding to outcomeable_type
                // This rule only applies if $tableName could be resolved
                $tableName ? Rule::exists($tableName, 'id')->where(function ($query) {
                    // Add any additional constraints if needed, e.g., for soft deletes
                    // if (in_array(\Illuminate\Database\Eloquent\SoftDeletes::class, class_uses($this->input('outcomeable_type')))) {
                    //     $query->whereNull('deleted_at');
                    // }
                }) : 'bail', // if tableName is null, bail on this rule to avoid error, type validation will fail first
            ],
            // Assuming 'description' from the old request maps to the 'outcomes' text field in the model
            'outcomes' => ['required', 'string', 'min:10', 'max:65535'],
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
            'outcomeable_type.required' => 'The type of item this learning outcome belongs to is required.',
            'outcomeable_type.in' => 'The selected item type is invalid.',
            'outcomeable_id.required' => 'The item ID this learning outcome belongs to is required.',
            'outcomeable_id.integer' => 'The item ID must be an integer.',
            'outcomeable_id.exists' => 'The selected parent item does not exist or is invalid.',

            'outcomes.required' => 'A description for the learning outcome is required.',
            'outcomes.min' => 'The learning outcome description should be at least 10 characters long.',
            'outcomes.max' => 'The learning outcome description is too long.',
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
            'outcomeable_type' => 'item type',
            'outcomeable_id' => 'item ID',
            'outcomes' => 'learning outcome description',
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        // If outcomeable_type is sent as a short name (e.g., "Subject"),
        // try to resolve it to its FQCN.
        if ($this->has('outcomeable_type') && !Str::contains($this->input('outcomeable_type'), '\\')) {
            $shortType = Str::studly(Str::singular($this->input('outcomeable_type')));
            $fqcn = 'App\\Models\\' . $shortType;
            if (class_exists($fqcn) && in_array($fqcn, $this->allowedOutcomeableTypes)) {
                $this->merge(['outcomeable_type' => $fqcn]);
            }
        }

        // If the form sends 'description' but the model/service expects 'outcomes'
        if ($this->has('description') && !$this->has('outcomes')) {
            $this->merge(['outcomes' => $this->input('description')]);
        }
    }
}
