<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\Rule;
use Carbon\Carbon;


class DateAfterOneDayAtLeast implements Rule
{
    protected $dateFrom;

    public function __construct($dateFrom)
    {
        $this->dateFrom = $dateFrom;
    }

    public function passes($attribute, $value)
    {
        $dateFrom = Carbon::parse($this->dateFrom);
        $dateTo = Carbon::parse($value);

        return $dateTo->gt($dateFrom->addDay());
    }

    public function message()
    {
        return 'The :attribute must be at least one day after the date from.';
    }
}
