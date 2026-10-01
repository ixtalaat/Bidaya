<?php

namespace App\Http\Requests;

use App\Enums\AssignmentStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateAssignmentRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'due_date' => ['required', 'date'],
            'max_score' => ['required', 'numeric', 'min:0.01', 'max:100000'],
            'attachment' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp,doc,docx', 'max:10240'],
            'status' => ['required', Rule::enum(AssignmentStatus::class)],
        ];
    }

    /**
     * @return array<int, callable(Validator): void>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->has('status')) {
                    return;
                }

                $assignment = $this->route('assignment');
                $newStatus = AssignmentStatus::from($this->input('status'));

                if ($assignment->status !== $newStatus && ! in_array($newStatus, $assignment->status->allowedTransitions(), true)) {
                    $validator->errors()->add('status', __('assignments.invalid_transition'));
                }
            },
        ];
    }
}
