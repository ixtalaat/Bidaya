<?php

namespace App\Http\Requests;

use App\Enums\Gender;
use App\Enums\RelationshipType;
use App\Enums\StudentStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStudentRequest extends FormRequest
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
            'date_of_birth' => ['required', 'date', 'before:today'],
            'gender' => ['required', Rule::enum(Gender::class)],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'address' => ['nullable', 'string', 'max:500'],
            'school' => ['nullable', 'string', 'max:255'],
            'grade_level' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::enum(StudentStatus::class)],
            'notes' => ['nullable', 'string'],
            'parents' => ['nullable', 'array'],
            'parents.*.id' => ['required', 'integer', 'distinct', 'exists:parents,id'],
            'parents.*.relationship' => ['required', Rule::enum(RelationshipType::class)],
        ];
    }
}
