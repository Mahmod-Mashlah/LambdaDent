<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Carbon\Carbon;

class DateAfterOneMonthAtLeast implements Rule
{
    public function passes($attribute, $value)
    {
        $dateFrom = request('date_from');
        $dateTo = request('date_to');

        if (!$dateFrom || !$dateTo) {
            return false;
        }

        $dateFrom = Carbon::parse($dateFrom);
        $dateTo = Carbon::parse($dateTo);

        return $dateFrom->diffInMonths($dateTo) >= 1;
    }

    public function message()
    {
        return 'The difference between the two dates must be at least one month.';
    }
}
