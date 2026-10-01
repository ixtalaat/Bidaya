<?php

namespace App\Http\Requests;

use App\Enums\DayOfWeek;
use App\Models\GroupSchedule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateGroupScheduleRequest extends FormRequest
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
            'day_of_week' => ['required', Rule::enum(DayOfWeek::class)],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'classroom' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<int, callable(Validator): void>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->any()) {
                    return;
                }

                $duplicate = GroupSchedule::where('group_id', $this->route('group')->id)
                    ->where('day_of_week', $this->enum('day_of_week', DayOfWeek::class))
                    ->where('start_time', $this->input('start_time'))
                    ->where('id', '!=', $this->route('schedule')->id)
                    ->exists();

                if ($duplicate) {
                    $validator->errors()->add('day_of_week', __('schedules.duplicate'));
                }
            },
        ];
    }
}
