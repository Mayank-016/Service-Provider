<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\ValidationRule;

class EndTimeAfterStartTime implements ValidationRule
{
    protected string $startTime;

    public function __construct(string $startTime)
    {
        $this->startTime = $startTime;
    }

    /**
     * Validate the rule.
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @param  \Closure(string): void  $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, \Closure $fail): void
    {
        if (strtotime($value) <= strtotime($this->startTime)) {
            $fail('The end time must be after the start time.');
        }
    }
}
