<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateSessionRequest extends FormRequest
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
            'date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'classroom' => ['nullable', 'string', 'max:255'],
            'teacher_notes' => ['nullable', 'string'],
        ];
    }

    /**
     * @return array<int, callable(Validator): void>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->has('date')) {
                    return;
                }

                $group = $this->route('session')->group;
                $date = $this->date('date');

                if ($date < $group->start_date->startOfDay()) {
                    $validator->errors()->add('date', __('sessions.date_before_group_start'));
                }

                if ($group->end_date !== null && $date > $group->end_date->endOfDay()) {
                    $validator->errors()->add('date', __('sessions.date_after_group_end'));
                }
            },
        ];
    }
}
