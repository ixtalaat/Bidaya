<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreAssignmentGradesRequest extends FormRequest
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
            'records' => ['required', 'array', 'min:1'],
            'records.*.enrollment_id' => ['required', 'integer', 'distinct', 'exists:enrollments,id'],
            'records.*.score' => ['required', 'numeric', 'min:0'],
            'records.*.feedback' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
