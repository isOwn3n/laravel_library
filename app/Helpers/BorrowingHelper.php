<?php

use Carbon\Carbon;

if (!function_exists('calculateFine')) {
    /**
     * Calculate fine based on the difference in days.
     *
     * @param string $due_date
     * @return int
     */
    function calculateFine(string $due_date): int
    {
        $start = Carbon::createFromFormat('Y-m-d', $due_date);
        $today = Carbon::now();

        $diffInDays = $today->diffInDays($start);
        if ($diffInDays >= 0)
            return 0;
        $fine_per_day = env('FINE_PER_DAY', 1000);

        return abs($diffInDays * $fine_per_day);
    }
}
