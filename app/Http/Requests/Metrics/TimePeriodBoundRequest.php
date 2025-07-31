<?php

namespace App\Http\Requests\Metrics;

use Illuminate\Foundation\Http\FormRequest;

class TimePeriodBoundRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'from' => ['required', 'date', 'before_or_equal:to'],
            'to' => ['required', 'date'],
            'group_by' => ['nullable', 'in:day,week,month'],
        ];
    }

    public function dateFormatToGroupBy(): string
    {
        $grouping = match ($this->input('group_by', 'day')) {
            'month' => 'month',
            'week' => 'week',
            default => 'day',
        };

        return match ($grouping) {
            'month' => '%Y-%m',     // e.g. 2025-07
            'week' => '%x-W%v',     // e.g. 2025-W30 (ISO week)
            default => '%Y-%m-%d',  // e.g. 2025-07-29
        };
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'group_by.in' => 'The group_by value must be one of: day, week, or month.',
        ];
    }
}
