<?php
namespace App\Http\Requests;

use App\Rules\EndTimeAfterStartTime;
use App\Rules\DurationFitsInSlot;
use Illuminate\Foundation\Http\FormRequest;

class ManageAvailabilityRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'availabilities' => ['required', 'array', 'min:1'],
        ];

        // Only build sub-rules if availabilities is a valid array
        if (is_array($this->input('availabilities'))) {
            foreach ($this->input('availabilities') as $index => $availability) {
                $start = $availability['start_time'] ?? null;
                $end = $availability['end_time'] ?? null;

                $rules["availabilities.$index.date"] = [
                    'required',
                    'date_format:Y-m-d',
                    'after_or_equal:today',
                    'before_or_equal:' . now()->addDays(6)->format('Y-m-d'),
                ];

                $rules["availabilities.$index.start_time"] = ['required', 'date_format:H:i:s'];
                $rules["availabilities.$index.end_time"] = ['required', 'date_format:H:i:s'];

                if ($start) {
                    $rules["availabilities.$index.end_time"][] = new \App\Rules\EndTimeAfterStartTime($start);
                }

                $rules["availabilities.$index.slot_duration"] = ['required', 'integer', 'min:10'];

                if ($start && $end) {
                    $rules["availabilities.$index.slot_duration"][] = new \App\Rules\DurationFitsInSlot($start, $end);
                }
            }
        }

        return $rules;
    }


    public function messages(): array
    {
        return [
            'availabilities.*.date.before_or_equal' =>
                'You can only set availability for today and up to 7 days in the future.',
        ];
    }

}
