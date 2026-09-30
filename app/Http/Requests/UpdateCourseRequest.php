<?php

namespace App\Http\Requests;

use App\Enums\CourseStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateCourseRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category' => ['nullable', 'string', 'max:255'],
            'min_age' => ['nullable', 'integer', 'min:0', 'max:100'],
            'max_age' => ['nullable', 'integer', 'min:0', 'max:100'],
            'level' => ['nullable', 'string', 'max:255'],
            'duration_weeks' => ['nullable', 'integer', 'min:1', 'max:520'],
            'sessions_count' => ['nullable', 'integer', 'min:1'],
            'monthly_fee' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'status' => ['required', Rule::enum(CourseStatus::class)],
            'prerequisites' => ['nullable', 'string'],
        ];
    }

    /**
     * @return array<int, callable(Validator): void>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->hasAny(['min_age', 'max_age'])) {
                    return;
                }

                $minAge = $this->integer('min_age');
                $maxAge = $this->integer('max_age');

                if ($this->filled('min_age') && $this->filled('max_age') && $maxAge < $minAge) {
                    $validator->errors()->add('max_age', __('courses.max_age_gte_min_age'));
                }
            },
        ];
    }
}
