<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class DurationFitsInSlot implements ValidationRule
{
    protected string $startTime;
    protected string $endTime;

    public function __construct(string $startTime, string $endTime)
    {
        $this->startTime = $startTime;
        $this->endTime = $endTime;
    }

    /**
     * Validate the rule.
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @param  \Closure(string): void  $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $slotLength = (strtotime($this->endTime) - strtotime($this->startTime)) / 60; // in minutes

        if ($value > $slotLength) {
            $fail('The duration must not exceed the available time slot.');
        }
    }
}
